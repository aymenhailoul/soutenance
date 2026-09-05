<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\Maintenance;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $equipmentCount = Equipment::count();
        $assignmentCount = EquipmentAssignment::count();
        $maintenanceCount = Maintenance::count();
        $movementCount = StockMovement::count();

        return view('reports.index', compact('equipmentCount', 'assignmentCount', 'maintenanceCount', 'movementCount'));
    }

    public function exportEquipment(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="inventaire_equipements_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'ID',
                'Nom Equipement',
                'Categorie',
                'Marque',
                'Modele',
                'Numero de Serie',
                'Tag Asset',
                'Statut',
                'Etat',
                'Date Achat',
                'Prix Achat (MAD)',
                'Fin Garantie',
                'Consommable',
                'Quantite'
            ], ';');

            Equipment::with('category')->chunk(100, function ($equipments) use ($handle) {
                foreach ($equipments as $eq) {
                    fputcsv($handle, [
                        $eq->id,
                        $eq->name,
                        $eq->category->name ?? 'N/A',
                        $eq->brand ?? '',
                        $eq->model ?? '',
                        $eq->serial_number ?? '',
                        $eq->asset_tag ?? '',
                        $eq->status,
                        $eq->condition,
                        $eq->purchase_date ? $eq->purchase_date->format('Y-m-d') : '',
                        $eq->purchase_price ?? '0.00',
                        $eq->warranty_end_date ? $eq->warranty_end_date->format('Y-m-d') : '',
                        $eq->is_consumable ? 'Oui' : 'Non',
                        $eq->quantity
                    ], ';');
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    public function exportAssignments(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rapport_affectations_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'ID',
                'Equipement',
                'Client',
                'Site',
                'Employe / Beneficiaire',
                'Date Affectation',
                'Date Retour',
                'Statut',
                'Notes'
            ], ';');

            EquipmentAssignment::with(['equipment', 'client', 'site'])->chunk(100, function ($assignments) use ($handle) {
                foreach ($assignments as $asgn) {
                    fputcsv($handle, [
                        $asgn->id,
                        $asgn->equipment->name ?? 'N/A',
                        $asgn->client->name ?? 'Interne',
                        $asgn->site->name ?? 'N/A',
                        $asgn->employee_name ?? 'N/A',
                        $asgn->assigned_at ? $asgn->assigned_at->format('Y-m-d H:i') : '',
                        $asgn->returned_at ? $asgn->returned_at->format('Y-m-d H:i') : '',
                        $asgn->status,
                        $asgn->notes ?? ''
                    ], ';');
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
