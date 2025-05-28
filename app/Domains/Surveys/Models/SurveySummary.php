<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Models;

use App\Domains\Surveys\Factories\SurveySummaryFactory;
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

    protected static function newFactory(): SurveySummaryFactory
    {
        return SurveySummaryFactory::new();
    }
}
