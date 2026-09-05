<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Exception;

class BackupService
{
    /**
     * Perform a database backup and record metadata.
     */
    public function createDatabaseBackup(?int $userId = null, string $disk = 'local'): Backup
    {
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "backup_db_{$timestamp}.sqlite";

        $backupRecord = Backup::create([
            'filename' => $filename,
            'disk' => $disk,
            'size' => 0,
            'type' => 'database',
            'status' => 'pending',
            'user_id' => $userId,
        ]);

        try {
            $dbPath = database_path('database.sqlite');

            if (file_exists($dbPath)) {
                $content = file_get_contents($dbPath);
                Storage::disk($disk)->put('backups/' . $filename, $content);
                $size = strlen($content);
            } else {
                // If in-memory or sqlite file not present, create a fallback JSON export dump
                $dumpData = [
                    'timestamp' => now()->toIso8601String(),
                    'categories' => \App\Models\Category::all()->toArray(),
                    'equipment' => \App\Models\Equipment::all()->toArray(),
                    'clients' => \App\Models\Client::all()->toArray(),
                    'sites' => \App\Models\Site::all()->toArray(),
                    'assignments' => \App\Models\EquipmentAssignment::all()->toArray(),
                    'maintenances' => \App\Models\Maintenance::all()->toArray(),
                    'stock_movements' => \App\Models\StockMovement::all()->toArray(),
                ];
                $jsonContent = json_encode($dumpData, JSON_PRETTY_PRINT);
                $filename = "backup_export_{$timestamp}.json";
                Storage::disk($disk)->put('backups/' . $filename, $jsonContent);
                $size = strlen($jsonContent);
            }

            $backupRecord->update([
                'filename' => $filename,
                'size' => $size,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return $backupRecord;
        } catch (Exception $e) {
            $backupRecord->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Remove backups older than specified days.
     */
    public function purgeOldBackups(int $daysToKeep = 30): int
    {
        $cutoff = now()->subDays($daysToKeep);
        $oldBackups = Backup::where('created_at', '<', $cutoff)->get();
        $deletedCount = 0;

        foreach ($oldBackups as $backup) {
            if (Storage::disk($backup->disk)->exists('backups/' . $backup->filename)) {
                Storage::disk($backup->disk)->delete('backups/' . $backup->filename);
            }
            $backup->delete();
            $deletedCount++;
        }

        return $deletedCount;
    }
}
