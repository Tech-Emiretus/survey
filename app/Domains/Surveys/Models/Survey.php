<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Models;

use App\Domains\Surveys\Enums\SurveyStatusEnum;
use App\Domains\Surveys\Factories\SurveyFactory;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Survey extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'status' => SurveyStatusEnum::class,
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        parent::booted();

        static::creating(function (Model $model) {
            $model->public_id ??= (string) Str::ulid();
        });
    }

    /**
     * @return BelongsTo<Company, Survey>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<User, Survey>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, Survey>
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
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

    public function isActive(): bool
    {
        return $this->status === SurveyStatusEnum::Published
            && $this->start_at <= now()
            && $this->end_at >= now();
    }

    protected static function newFactory(): SurveyFactory
    {
        return SurveyFactory::new();
    }
}
