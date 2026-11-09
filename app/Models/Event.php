<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    protected $fillable = [
        'venue_id',
        'title',
        'description',
        'event_date',
        'capacity',
        'details',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'details' => 'array',
        ];
    }

    // Local Scope 1: Get upcoming events
    public function scopeUpcoming(Builder $query): void
    {
        $query->where('event_date', '>=', now());
    }

    // Local Scope 2: Get events with minimum capacity
    public function scopeWithMinimumCapacity(Builder $query, int $capacity): void
    {
        $query->where('capacity', '>=', $capacity);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class)
            ->withPivot('status')
            ->withTimestamps();
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}