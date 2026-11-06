<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminReportController extends Controller
{
    /**
     * Exporta pagamentos em CSV.
     */
    public function payments(Request $request)
    {
        $this->authorizeAdmin();

        $query = Payment::with(['client', 'freelancer', 'project'])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        return $this->csv('pagamentos_'.now()->format('Ymd_His').'.csv', [
            'ID', 'Cliente', 'Freelancer', 'Projeto', 'Valor', 'Taxa plataforma',
            'Taxa gateway', 'Líquido freelancer', 'Método', 'Status', 'Criado em', 'Pago em', 'Liberado em',
        ], $query->lazy()->map(fn (Payment $payment) => [
            $payment->id,
            $payment->client?->name,
            $payment->freelancer?->name,
            $payment->project?->title,
            $this->money($payment->amount),
            $this->money($payment->platform_fee),
            $this->money($payment->gateway_fee),
            $this->money($payment->net_amount),
            $payment->method,
            $payment->status,
            $payment->created_at?->format('d/m/Y H:i'),
            $payment->paid_at?->format('d/m/Y H:i'),
            $payment->released_at?->format('d/m/Y H:i'),
        ]));
    }

    /**
     * Exporta usuários em CSV.
     */
    public function users(Request $request)
    {
        $this->authorizeAdmin();

        $query = User::with('profile')->withAvg('reviewsReceived as rating_avg', 'rating')->orderByDesc('created_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $this->csv('usuarios_'.now()->format('Ymd_His').'.csv', [
            'ID', 'Nome', 'E-mail', 'Tipo', 'Status', 'Título profissional', 'Avaliação média', 'Cadastro',
        ], $query->lazy()->map(fn (User $user) => [
            $user->id,
            $user->name,
            $user->email,
            $user->type,
            $user->status,
            $user->profile?->professional_title,
            $user->rating_avg !== null ? number_format((float) $user->rating_avg, 1, ',', '.') : '',
            $user->created_at?->format('d/m/Y H:i'),
        ]));
    }

    /**
     * Exporta projetos em CSV.
     */
    public function projects(Request $request)
    {
        $this->authorizeAdmin();

        $query = Project::with(['client', 'category'])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $this->csv('projetos_'.now()->format('Ymd_His').'.csv', [
            'ID', 'Título', 'Cliente', 'Categoria', 'Orçamento mín.', 'Orçamento máx.',
            'Status', 'Destaque', 'Criado em', 'Concluído em',
        ], $query->lazy()->map(fn (Project $project) => [
            $project->id,
            $project->title,
            $project->client?->name,
            $project->category?->name,
            $this->money($project->budget_min),
            $this->money($project->budget_max),
            $project->status,
            $project->is_featured ? 'Sim' : 'Não',
            $project->created_at?->format('d/m/Y H:i'),
            $project->completed_at?->format('d/m/Y H:i'),
        ]));
    }

    /**
     * @param  array<int, string>  $headers
     * @param  iterable<int, array<int, mixed>>  $rows
     */
    private function csv(string $filename, array $headers, iterable $rows)
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function money(mixed $value): string
    {
        return number_format((float) $value, 2, ',', '.');
    }

    private function authorizeAdmin(): void
    {
        if (! Auth::user()?->isAdmin()) {
            abort(403, 'Acesso restrito a administradores.');
        }
    }
}
