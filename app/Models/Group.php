<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperGroup
 */
class Group extends Model
{
    protected $connection = 'sde';

    protected $table = 'invGroups';
}
