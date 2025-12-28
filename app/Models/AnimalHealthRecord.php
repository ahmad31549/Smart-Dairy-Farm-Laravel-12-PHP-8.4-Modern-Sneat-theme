<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalHealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'animal_id',
        'health_status',
        'check_date',
        'next_check_date',
        'veterinarian',
        'temperature',
        'symptoms',
        'treatment',
        'notes'
    ];

    protected $casts = [
        'check_date' => 'date',
        'next_check_date' => 'date',
        'temperature' => 'decimal:1'
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}