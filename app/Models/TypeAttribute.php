<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin IdeHelperTypeAttribute
 */
class TypeAttribute extends Model
{
    protected $connection = 'sde';

    protected $table = 'dgmTypeAttributes';

    protected $primaryKey = 'typeID';

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'typeID', 'typeID');
    }

    public function attribute(): HasOne
    {
        return $this->hasOne(AttributeType::class, 'attributeID', 'attributeID');
    }

    public function value(): HasOne
    {
        return $this->hasOne(AttributeType::class, 'attributeID', 'valueFloat');
    }
}
