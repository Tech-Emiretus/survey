<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Models;

use App\Domains\Surveys\Enums\SurveySummaryStatusEnum;
use App\Domains\Surveys\Factories\SurveySummaryFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveySummary extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo<Survey, SurveySummary>
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * @return BelongsTo<User, Survey>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'status' => SurveySummaryStatusEnum::class,
        ];
    }

    protected static function newFactory(): SurveySummaryFactory
    {
        return SurveySummaryFactory::new();
    }
}
