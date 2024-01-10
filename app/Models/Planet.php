<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPlanet
 */
class Planet extends Model
{
    protected $connection = 'sde';

    protected $table = 'mapDenormalize';

    protected $primaryKey = 'itemID';

    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope('planet', function (Builder $query) {
            $query->where('groupID', 7);
        });
    }
}
