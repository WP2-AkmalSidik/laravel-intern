<?php

namespace App\Notifications;

use App\Models\ItemLoan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ItemLoan $loan
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $disetujui = $this->loan->status === 'disetujui';

        return (new MailMessage)
            ->subject(
                $disetujui
                ? 'Pengajuan Peminjaman Disetujui'
                : 'Pengajuan Peminjaman Ditolak'
            )
            ->greeting('Halo, ' . $notifiable->name)
            ->line(
                $disetujui
                ? 'Pengajuan peminjaman barang Anda telah disetujui.'
                : 'Mohon maaf, pengajuan peminjaman barang Anda ditolak.'
            )
            ->line('Kode Barang: ' . $this->loan->item->kode_barang)
            ->line('Nama Barang: ' . $this->loan->item->nama_barang)
            ->line('Jumlah Pinjam: ' . $this->loan->jumlah_pinjam)
            ->line('Tanggal Pinjam: ' . $this->loan->tanggal_pinjam->format('d-m-Y'))
            ->line('Tenggat Pengembalian: ' . $this->loan->tenggat_kembali->format('d-m-Y'))
            ->line('Status: ' . ucfirst($this->loan->status))
            ->line('Terima kasih telah menggunakan sistem inventaris.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'item_loan_id' => $this->loan->id,
            'status' => $this->loan->status,
        ];
    }
}
