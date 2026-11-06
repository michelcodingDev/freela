<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('escrow:auto-release')]
#[Description('Libera automaticamente pagamentos em custodia há mais de 5 dias')]
class EscrowAutoRelease extends Command
{
    public function handle(NotificationService $notifications): int
    {
        $cutoff = now()->subDays(5);

        $payments = Payment::where('status', 'custodia')
            ->whereHas('project', fn ($q) => $q->where('status', 'aguardando_liberacao'))
            ->where('paid_at', '<=', $cutoff)
            ->with(['project', 'client', 'freelancer'])
            ->get();

        $count = 0;
        foreach ($payments as $payment) {
            $payment->update(['status' => 'liberado', 'released_at' => now()]);
            $payment->project->update(['status' => 'concluido', 'completed_at' => now()]);
            $count++;

            $notifications->notify(
                $payment->freelancer,
                'liberacao_automatica',
                'Pagamento liberado automaticamente',
                "O pagamento do projeto \"{$payment->project->title}\" foi liberado após 5 dias em custódia.",
                ['project_id' => $payment->project_id]
            );

            $notifications->notify(
                $payment->client,
                'liberacao_automatica',
                'Pagamento liberado automaticamente',
                "O pagamento do projeto \"{$payment->project->title}\" foi liberado automaticamente após 5 dias sem contestação.",
                ['project_id' => $payment->project_id]
            );
        }

        $this->info("{$count} pagamento(s) liberado(s) automaticamente.");

        return self::SUCCESS;
    }
}
