<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    /**
     * Process a stock movement atomically and synchronize equipment stock quantity.
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

            if ($movement === 'Sortie') {
                if ($equipment->quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => "Insufficient stock. Available: {$equipment->quantity}, Requested: {$quantity}",
                    ]);
                }
                $equipment->decrement('quantity', $quantity);
            } elseif ($movement === 'Entrée') {
                $equipment->increment('quantity', $quantity);
            } elseif ($movement === 'Transfert') {
                // Validate source & destination sites
                $sourceSiteId = $data['source_site_id'] ?? $equipment->site_id;
                $destinationSiteId = $data['destination_site_id'] ?? null;

                if (!$destinationSiteId) {
                    throw ValidationException::withMessages([
                        'destination_site_id' => 'Le site de destination est requis pour un transfert.',
                    ]);
                }

                if ($sourceSiteId && (int)$sourceSiteId === (int)$destinationSiteId) {
                    throw ValidationException::withMessages([
                        'destination_site_id' => 'Le site de destination doit être différent du site d\'origine.',
                    ]);
                }

                if ($equipment->quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => "Insufficient stock for transfer. Available: {$equipment->quantity}, Requested: {$quantity}",
                    ]);
                }

                // If non-consumable, update equipment location
                if (!$equipment->is_consumable) {
                    $equipment->update(['site_id' => $destinationSiteId]);
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
