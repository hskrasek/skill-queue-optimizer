<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperRace
 */
class Race extends Model
{
    protected $connection = 'sde';

    protected $table = 'chrRaces';

    protected $primaryKey = 'raceID';
}
