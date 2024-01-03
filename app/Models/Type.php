<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperType
 */
class Type extends Model
{
    protected $connection = 'sde';

    protected $table = 'invTypes';

    protected $primaryKey = 'typeID';


    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'groupID', 'groupID');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(TypeAttribute::class, 'typeID', 'typeID');
    }
}
