<?php

namespace App\Console\Commands;

use App\Services\BirthdayService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:send-birthday-messages')]
#[Description('Envia mensagens automáticas para staff sobre pacientes aniversariantes do dia.')]
class SendBirthdayMessages extends Command
{
    /**
     * Execute the console command.
     * 
     * Intenção: Verificar pacientes que fazem aniversário hoje e notificar
     * todos os usuários Staff (Admin e Operacional) via sistema de mensagens interno.
     */
    public function handle(BirthdayService $birthdayService)
    {
        $messagesSent = $birthdayService->sendBirthdayNotifications();

        if ($messagesSent > 0) {
            $this->info("Notificações de aniversário enviadas para {$messagesSent} staff members.");
        } else {
            $this->info('Nenhum aniversariante encontrado hoje.');
        }

        return Command::SUCCESS;
    }
}
