<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilkProduction extends Model
{
    use HasFactory;

    protected $table = 'milk_production';

    protected $fillable = [
        'animal_id',
        'production_date',
        'morning_quantity',
        'evening_quantity',
        'fat_content',
        'protein_content',
        'quality_grade',
        'notes'
    ];

    protected $casts = [
        'production_date' => 'date',
        'morning_quantity' => 'decimal:2',
        'evening_quantity' => 'decimal:2',
        'fat_content' => 'decimal:2',
        'protein_content' => 'decimal:2'
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function getTotalQuantityAttribute()
    {
        return $this->morning_quantity + $this->evening_quantity;
    }
}