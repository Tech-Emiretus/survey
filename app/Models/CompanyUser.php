<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyUser extends Model
{
    use HasFactory;

    protected $table = 'company_user';

    /**
     * @return BelongsTo<Company, CompanyUser>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<User, CompanyUser>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
