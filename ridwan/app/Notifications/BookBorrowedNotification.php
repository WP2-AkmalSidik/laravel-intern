<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookBorrowedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Loan $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Konfirmasi Peminjaman Buku')
                    ->greeting('Halo, ' . $notifiable->name . '!')
                    ->line('Buku "' . $this->loan->book->judul . '" berhasil dipinjam.')
                    ->line('Tenggat pengembalian: ' . $this->loan->tenggat_kembali->format('Y-m-d'))
                    ->action('Lihat Peminjaman', url('/'))
                    ->line('Terima kasih!');
    }
}
