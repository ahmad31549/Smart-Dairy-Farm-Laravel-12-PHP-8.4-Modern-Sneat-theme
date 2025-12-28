<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $table = 'income';

    protected $fillable = [
        'source',
        'description',
        'amount',
        'income_date',
        'customer',
        'quantity',
        'unit',
        'notes'
    ];

    protected $casts = [
        'income_date' => 'date',
        'amount' => 'decimal:2',
        'quantity' => 'decimal:2'
    ];
}