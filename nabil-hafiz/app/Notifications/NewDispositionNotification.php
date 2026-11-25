<?php

namespace App\Notifications;

use App\Models\Disposition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDispositionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Disposition $disposition
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $letter = $this->disposition->letter;
        $department = $this->disposition->department;

        return (new MailMessage)
            ->subject('Disposisi Surat Baru')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Anda menerima disposisi surat baru untuk departemen Anda.')
            ->line('Nomor Surat: ' . $letter->nomor_surat)
            ->line('Perihal: ' . $letter->perihal)
            ->line('Departemen Tujuan: ' . $department->nama_departemen)
            ->line('Instruksi: ' . $this->disposition->catatan_instruksi)
            ->line('Sifat: ' . $this->disposition->sifat)
            ->line('Batas Waktu: ' . $this->disposition->batas_waktu->format('d-m-Y'))
            ->line('Silakan login ke sistem untuk menindaklanjuti disposisi ini.');
    }
}
