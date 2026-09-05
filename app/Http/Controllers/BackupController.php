<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Exception;

class BackupController extends Controller
{
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function index()
    {
        $backups = Backup::with('user')->orderBy('created_at', 'desc')->paginate(10);
        $totalSize = Backup::where('status', 'completed')->sum('size');
        $completedCount = Backup::where('status', 'completed')->count();

        return view('backups.index', compact('backups', 'totalSize', 'completedCount'));
    }

    public function store(Request $request)
    {
        try {
            $userId = auth()->user()?->id;
            $disk = $request->input('disk', 'local');

            $backup = $this->backupService->createDatabaseBackup($userId, $disk);

            return redirect()->route('backups.index')->with('success', "Sauvegarde '{$backup->filename}' créée avec succès.");
        } catch (Exception $e) {
            return redirect()->route('backups.index')->with('error', "Échec de la sauvegarde: " . $e->getMessage());
        }
    }

    public function download(Backup $backup)
    {
        $path = 'backups/' . $backup->filename;

        if (!Storage::disk($backup->disk)->exists($path)) {
            return redirect()->route('backups.index')->with('error', "Fichier de sauvegarde introuvable sur le stockage.");
        }

        return Storage::disk($backup->disk)->download($path);
    }

    public function destroy(Backup $backup)
    {
        $path = 'backups/' . $backup->filename;

        if (Storage::disk($backup->disk)->exists($path)) {
            Storage::disk($backup->disk)->delete($path);
        }

        $backup->delete();

        return redirect()->route('backups.index')->with('success', "Sauvegarde supprimée avec succès.");
    }
}
