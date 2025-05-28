<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Models;

use App\Domains\Surveys\Factories\SurveyFactory;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @return BelongsTo<Company, Survey>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return HasMany<SurveyField, Survey>
     */
    public function fields(): HasMany
    {
        return $this->hasMany(SurveyField::class);
    }

    /**
     * @return HasMany<SurveyResponse, Survey>
     */
    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    /**
     * @return HasOne<SurveySummary, Survey>
     */
    public function summary(): HasOne
    {
        return $this->hasOne(SurveySummary::class);
    }

    protected static function newFactory(): SurveyFactory
    {
        return SurveyFactory::new();
    }
}
