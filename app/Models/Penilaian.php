<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'saran_id',
        'status',
        'poin'
    ];

    protected $table = "penilaian";

    protected $guarded = [];

    public function saran()
    {
        return $this->belongsTo(Saran::class, 'saran_id');
    }
}
