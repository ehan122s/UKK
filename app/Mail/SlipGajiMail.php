<?php

namespace App\Mail;

use App\Models\Gaji;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SlipGajiMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Gaji $gaji)
    {
    }

    public function build(): self
    {
        $pdf = Pdf::loadView('slip-gaji.pdf', [
            'gaji'     => $this->gaji,
            'karyawan' => $this->gaji->karyawan,
        ]);

        return $this
            ->subject('Slip Gaji Periode ' . $this->gaji->periode_awal->translatedFormat('d M Y')
                . ' - ' . $this->gaji->periode_akhir->translatedFormat('d M Y'))
            ->view('emails.slip-gaji')
            ->attachData($pdf->output(), 'slip-gaji.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
