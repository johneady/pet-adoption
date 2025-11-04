<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationStatusHistory extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationStatusHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'adoption_application_id',
        'from_status',
        'to_status',
        'notes',
        'changed_by',
    ];

    public function adoptionApplication(): BelongsTo
    {
        return $this->belongsTo(AdoptionApplication::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
