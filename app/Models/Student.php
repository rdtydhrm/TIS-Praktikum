<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'nim',
        'nama',
        'program_studi',
        'angkatan'
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }
}