<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Models;

use App\Domains\Surveys\Factories\SurveyFieldResponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyFieldResponse extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo<SurveyResponse, SurveyFieldResponse>
     */
    public function surveyResponse(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class);
    }

    /**
     * @return BelongsTo<SurveyField, SurveyFieldResponse>
     */
    public function surveyField(): BelongsTo
    {
        return $this->belongsTo(SurveyField::class);
    }

    protected static function newFactory(): SurveyFieldResponseFactory
    {
        return SurveyFieldResponseFactory::new();
    }
}
