<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'email';
    public $incrementing = false;
    protected $keyType = 'string';
}
