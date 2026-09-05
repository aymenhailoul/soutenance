<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\Maintenance;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // IT Equipment Statistics
        $totalEquipment = Equipment::count();
        $availableEquipment = Equipment::where('status', 'Available')->count();
        $assignedEquipment = Equipment::where('status', 'Assigned')->count();
        $inMaintenanceEquipment = Equipment::where('status', 'In Maintenance')->count();

        $totalCategories = Category::count();
        $totalClients = Client::count();
        $totalSites = Site::count();

        // Low stock alerts for consumables
        $lowStockConsumables = Equipment::where('is_consumable', true)
            ->whereColumn('quantity', '<=', 'min_stock')
            ->get();

        // Warranty alerts (expiring within 30 days)
        $expiringWarranties = Equipment::whereNotNull('warranty_end_date')
            ->whereBetween('warranty_end_date', [now(), now()->addDays(30)])
            ->get();

        // Recent Activity
        $recentAssignments = EquipmentAssignment::with(['equipment', 'client', 'site'])
            ->orderBy('assigned_at', 'desc')
            ->take(5)
            ->get();

        $recentMaintenances = Maintenance::with('equipment')
            ->orderBy('scheduled_at', 'desc')
            ->take(5)
            ->get();

        return view('welcome', compact(
            'totalEquipment',
            'availableEquipment',
            'assignedEquipment',
            'inMaintenanceEquipment',
            'totalCategories',
            'totalClients',
            'totalSites',
            'lowStockConsumables',
            'expiringWarranties',
            'recentAssignments',
            'recentMaintenances'
        ));
    }
}
