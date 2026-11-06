<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Exibe os logs de auditoria (apenas admin).
     */
    public function index(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Acesso restrito a administradores.');
        }

        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filtro por ação
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filtro por modelo
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filtro por usuário
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Opções para filtros
        $actions = AuditLog::distinct()->pluck('action');
        $modelTypes = AuditLog::distinct()->pluck('model_type');
        $users = User::orderBy('name')->get();

        return view('admin.logs.index', compact('logs', 'actions', 'modelTypes', 'users'));
    }

    /**
     * Exibe os logs de erro do sistema (apenas admin).
     */
    public function errors()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Acesso restrito a administradores.');
        }

        // Ler logs de erro do Laravel
        $logFile = storage_path('logs/laravel.log');
        $errorLogs = [];

        if (file_exists($logFile)) {
            $lines = file($logFile);
            $lines = array_reverse($lines); // Mais recentes primeiro

            foreach ($lines as $line) {
                if (strpos($line, 'ERROR') !== false || strpos($line, 'CRITICAL') !== false || strpos($line, 'EMERGENCY') !== false) {
                    $errorLogs[] = $line;
                }
            }

            $errorLogs = array_slice($errorLogs, 0, 50); // Limitar a 50 erros
        }

        return view('admin.logs.errors', compact('errorLogs'));
    }

    /**
     * Exporta logs de auditoria em CSV.
     */
    public function exportCsv()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Acesso restrito a administradores.');
        }

        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit_logs_'.now()->format('Ymd_His').'.csv"',
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Usuário', 'Ação', 'Modelo', 'ID Modelo', 'IP', 'Data']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->user?->name ?? 'Sistema',
                    $log->action,
                    $log->model_type,
                    $log->model_id ?? '-',
                    $log->ip_address ?? '-',
                    $log->created_at->format('d/m/Y H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
