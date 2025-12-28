<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilkQualityTest extends Model
{
    use HasFactory;

    protected $table = 'milk_quality_tests';

    protected $fillable = [
        'animal_id',
        'test_date',
        'batch_number',
        'fat_content',
        'protein_content',
        'lactose_content',
        'ph_level',
        'temperature',
        'somatic_cell_count',
        'quality_grade',
        'test_result',
        'tested_by',
        'notes'
    ];

    protected $casts = [
        'test_date' => 'date',
        'fat_content' => 'decimal:2',
        'protein_content' => 'decimal:2',
        'lactose_content' => 'decimal:2',
        'ph_level' => 'decimal:2',
        'temperature' => 'decimal:2',
        'somatic_cell_count' => 'integer'
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}

