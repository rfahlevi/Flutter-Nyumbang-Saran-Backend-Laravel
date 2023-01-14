<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;

    protected $fillable = [
        'dept_name',
    ];

    protected $table = "departemen";
    
    protected $guarded = [];

    public function saran() {
       return $this->hasMany(Saran::class, 'id' , 'departemen_id');
    }
    
}
