<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationUnitClosure extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ancestor_id',
        'descendant_id',
        'depth',
    ];

    protected function casts(): array
    {
        return [
            'depth' => 'integer',
        ];
    }

    public function ancestor(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class, 'ancestor_id');
    }

    public function descendant(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class, 'descendant_id');
    }
}
