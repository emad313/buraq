<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasUuid;
}
