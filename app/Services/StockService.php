<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\SiteStock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    /**
     * Process a stock movement atomically and synchronize equipment and site-specific stock quantities.
     *
     * @throws ValidationException
     */
    public function recordMovement(array $data, int|string|null $userId = null): StockMovement
    {
        $userId = ($userId && (int)$userId > 0) ? (int)$userId : null;
        return DB::transaction(function () use ($data, $userId) {
            /** @var Equipment $equipment */
            $equipment = Equipment::lockForUpdate()->findOrFail($data['equipment_id']);
            $quantity = (int) $data['quantity'];
            $movement = $data['movement']; // Entrée, Sortie, Transfert

            if ($quantity <= 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'La quantité doit être supérieure à zéro.',
                ]);
            }

            // Non-consumables represent single assets (Serial/Asset Tag)
            if (!$equipment->is_consumable) {
                if (in_array($movement, ['Entrée', 'Sortie'])) {
                    throw ValidationException::withMessages([
                        'movement' => 'Les mouvements de stock en masse (Entrée/Sortie) ne sont pas autorisés pour les équipements individuels non-consommables.',
                    ]);
                }

                if ($movement === 'Transfert') {
                    if ($quantity !== 1) {
                        throw ValidationException::withMessages([
                            'quantity' => 'La quantité pour le transfert d\'un équipement individuel doit être égale à 1.',
                        ]);
                    }

                    $destinationSiteId = $data['destination_site_id'] ?? null;
                    if (!$destinationSiteId) {
                        throw ValidationException::withMessages([
                            'destination_site_id' => 'Le site de destination est requis pour un transfert.',
                        ]);
                    }

                    $sourceSiteId = $data['source_site_id'] ?? $equipment->site_id;
                    if ($sourceSiteId && (int)$sourceSiteId === (int)$destinationSiteId) {
                        throw ValidationException::withMessages([
                            'destination_site_id' => 'Le site de destination doit être différent du site d\'origine.',
                        ]);
                    }

                    $equipment->update(['site_id' => $destinationSiteId]);
                }
            } else {
                // Consumable Stock Logic using SiteStock
                if ($movement === 'Entrée') {
                    $targetSiteId = $data['destination_site_id'] ?? $equipment->site_id;
                    if ($targetSiteId) {
                        $siteStock = SiteStock::firstOrCreate(
                            ['equipment_id' => $equipment->id, 'site_id' => $targetSiteId],
                            ['quantity' => 0]
                        );
                        $siteStock->increment('quantity', $quantity);
                    }
                    $equipment->increment('quantity', $quantity);
                } elseif ($movement === 'Sortie') {
                    $sourceSiteId = $data['source_site_id'] ?? $equipment->site_id;

                    if ($equipment->quantity < $quantity) {
                        throw ValidationException::withMessages([
                            'quantity' => "Stock total insuffisant. Disponible: {$equipment->quantity}, Demandé: {$quantity}",
                        ]);
                    }

                    if ($sourceSiteId) {
                        $siteStock = SiteStock::where('equipment_id', $equipment->id)
                            ->where('site_id', $sourceSiteId)
                            ->lockForUpdate()
                            ->first();

                        if (!$siteStock) {
                            $totalSiteStocks = SiteStock::where('equipment_id', $equipment->id)->sum('quantity');
                            if ($totalSiteStocks == 0 && $equipment->quantity >= $quantity) {
                                $siteStock = SiteStock::create([
                                    'equipment_id' => $equipment->id,
                                    'site_id' => $sourceSiteId,
                                    'quantity' => $equipment->quantity,
                                ]);
                            }
                        }

                        if (!$siteStock || $siteStock->quantity < $quantity) {
                            $available = $siteStock?->quantity ?? 0;
                            throw ValidationException::withMessages([
                                'quantity' => "Stock insuffisant sur le site d'origine. Disponible: {$available}, Demandé: {$quantity}",
                            ]);
                        }
                        $siteStock->decrement('quantity', $quantity);
                    }

                    $equipment->decrement('quantity', $quantity);
                } elseif ($movement === 'Transfert') {
                    $sourceSiteId = $data['source_site_id'] ?? $equipment->site_id;
                    $destinationSiteId = $data['destination_site_id'] ?? null;

                    if (!$sourceSiteId) {
                        throw ValidationException::withMessages([
                            'source_site_id' => 'Le site d\'origine est requis pour un transfert.',
                        ]);
                    }

                    if (!$destinationSiteId) {
                        throw ValidationException::withMessages([
                            'destination_site_id' => 'Le site de destination est requis pour un transfert.',
                        ]);
                    }

                    if ((int)$sourceSiteId === (int)$destinationSiteId) {
                        throw ValidationException::withMessages([
                            'destination_site_id' => 'Le site de destination doit être différent du site d\'origine.',
                        ]);
                    }

                    $sourceSiteStock = SiteStock::where('equipment_id', $equipment->id)
                        ->where('site_id', $sourceSiteId)
                        ->lockForUpdate()
                        ->first();

                    if (!$sourceSiteStock) {
                        $totalSiteStocks = SiteStock::where('equipment_id', $equipment->id)->sum('quantity');
                        if ($totalSiteStocks == 0 && $equipment->quantity >= $quantity) {
                            $sourceSiteStock = SiteStock::create([
                                'equipment_id' => $equipment->id,
                                'site_id' => $sourceSiteId,
                                'quantity' => $equipment->quantity,
                            ]);
                        }
                    }

                    if (!$sourceSiteStock || $sourceSiteStock->quantity < $quantity) {
                        $available = $sourceSiteStock?->quantity ?? 0;
                        throw ValidationException::withMessages([
                            'quantity' => "Stock insuffisant sur le site d'origine pour transfert. Disponible: {$available}, Demandé: {$quantity}",
                        ]);
                    }

                    // Perform atomic transfer between sites
                    $sourceSiteStock->decrement('quantity', $quantity);

                    $destSiteStock = SiteStock::firstOrCreate(
                        ['equipment_id' => $equipment->id, 'site_id' => $destinationSiteId],
                        ['quantity' => 0]
                    );
                    $destSiteStock->increment('quantity', $quantity);
                }
            }

            $prixAchat = isset($data['prix_achat']) && $data['prix_achat'] !== '' ? (float) $data['prix_achat'] : 0;
            $montant = $prixAchat * $quantity;

            return StockMovement::create([
                'equipment_id' => $equipment->id,
                'movement' => $movement,
                'quantity' => $quantity,
                'source_site_id' => $data['source_site_id'] ?? null,
                'destination_site_id' => $data['destination_site_id'] ?? null,
                'prix_achat' => $prixAchat,
                'montant' => $montant,
                'user_id' => $userId,
                'comment' => $data['comment'] ?? null,
            ]);
        });
    }
}
