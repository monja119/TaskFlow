<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    

    protected $fillable = [
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'user_id',
        'archived_at',
        'progress',
        'risk_score',
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'archived_at' => 'datetime',
        'progress' => 'integer',
        'risk_score' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at')->where('status', '!=', ProjectStatus::COMPLETED);
    }

    public function scopeAtRisk($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('risk_score')->where('risk_score', '>', 0)
                ->orWhere(function ($inner) {
                    $inner->whereNotNull('end_date')->whereDate('end_date', '<', now());
                });
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'status', 'progress', 'risk_score', 'start_date', 'end_date', 'user_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
