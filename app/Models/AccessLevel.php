<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccessLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'code',
        'name',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
        ];
    }

    public function userAccesses(): HasMany
    {
        return $this->hasMany(UserAccess::class);
    }
}
