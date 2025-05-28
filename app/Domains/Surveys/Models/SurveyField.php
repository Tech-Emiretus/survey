<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Models;

use App\Domains\Surveys\Factories\SurveyFieldFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyField extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'options' => 'array',
    ];

    /**
     * @return BelongsTo<Survey, SurveyField>
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

    /**
     * @return BelongsTo<User, Survey>
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    protected static function newFactory(): SurveyFieldFactory
    {
        return SurveyFieldFactory::new();
    }
}
