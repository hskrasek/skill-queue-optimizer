<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperAttributeType
 */
class AttributeType extends Model
{
    protected $connection = 'sde';

    protected $table = 'dgmAttributeTypes';

    protected $primaryKey = 'attributeID';
}
