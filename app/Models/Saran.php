<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saran extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_saran',
        'user_id',
        // 'departemen_id',
        'kondisi_awal',
        'usulan',
        'file_pendukung',
    ];

    protected $table = "saran";
    
    protected $guarded = [];

    public function user() {
       return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function departemen() {
        return $this->belongsTo(Departemen::class, 'user_id', 'id');
    }

}
