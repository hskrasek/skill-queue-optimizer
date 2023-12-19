<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperBloodline
 */
class Bloodline extends Model
{
    protected $connection = 'sde';

    protected $table = 'chrBloodlines';

    protected $primaryKey = 'bloodlineID';
}
