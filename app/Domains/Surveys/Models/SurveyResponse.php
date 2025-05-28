<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Models;

use App\Domains\Surveys\Factories\SurveyResponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyResponse extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @return BelongsTo<Survey, SurveyResponse>
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * @return HasMany<SurveyFieldResponse, SurveyResponse>
     */
    public function fieldResponses(): HasMany
    {
        return $this->hasMany(SurveyFieldResponse::class);
    }

    protected static function newFactory(): SurveyResponseFactory
    {
        return SurveyResponseFactory::new();
    }
}
