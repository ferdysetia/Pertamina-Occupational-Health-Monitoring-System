<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HealthAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'mcu_record_id',
        'employee_id',
        'severity',
        'category',
        'reason',
        'status',
    ];

    public function mcuRecord(): BelongsTo
    {
        return $this->belongsTo(McuRecord::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }
}
