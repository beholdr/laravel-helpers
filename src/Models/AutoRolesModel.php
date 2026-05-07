<?php

namespace Beholdr\LaravelHelpers\Models;

use Beholdr\LaravelHelpers\Traits\HasAutoRoles;
use Illuminate\Database\Eloquent\Model;

/**
 * @internal Used to analyze HasAutoRoles in an Eloquent model context.
 */
abstract class AutoRolesModel extends Model
{
    use HasAutoRoles;
}
