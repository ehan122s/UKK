<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gaji extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'periode_awal',
        'periode_akhir',
        'gaji_pokok',
        'lembur',
        'pinjaman_karyawan',
        'email_sent_at',
        'whatsapp_sent_at',
    ];

    protected $casts = [
        'periode_awal'     => 'date',
        'periode_akhir'    => 'date',
        'email_sent_at'    => 'datetime',
        'whatsapp_sent_at' => 'datetime',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    // Total Penghasilan = Gaji Pokok + Lembur
    public function getTotalPenghasilanAttribute(): int
    {
        return $this->gaji_pokok + $this->lembur;
    }

    // Total Potongan = Pinjaman Karyawan
    public function getTotalPotonganAttribute(): int
    {
        return $this->pinjaman_karyawan;
    }

    // Gaji Bersih = Total Penghasilan - Total Potongan
    public function getGajiBersihAttribute(): int
    {
        return $this->total_penghasilan - $this->total_potongan;
    }
}
