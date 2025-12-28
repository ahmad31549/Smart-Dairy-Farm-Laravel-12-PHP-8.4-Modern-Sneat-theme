<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'item_name',
        'category',
        'inventory_type',
        'batch_number',
        'quantity',
        'unit',
        'unit_price',
        'reorder_level',
        'supplier',
        'manufacturer',
        'last_restocked',
        'expiry_date',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'last_restocked' => 'date',
        'expiry_date' => 'date'
    ];

    public function getTotalValueAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    public function getNeedsReorderAttribute()
    {
        return $this->quantity <= $this->reorder_level;
    }

    public function getStatusAttribute()
    {
        if ($this->quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->quantity <= $this->reorder_level) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    public function getIsExpiringAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->expiry_date <= now()->addDays(30);
    }

    // Scopes for filtering
    public function scopeFeedSupplies($query)
    {
        return $query->where('inventory_type', 'feed_supplies');
    }

    public function scopeMedicalSupplies($query)
    {
        return $query->where('inventory_type', 'medical_supplies');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= reorder_level');
    }

    public function scopeExpiringSoon($query)
    {
        return $query->whereBetween('expiry_date', [now(), now()->addDays(30)]);
    }
}
