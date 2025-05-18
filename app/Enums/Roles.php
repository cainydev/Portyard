<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum Roles: string
{
    use EnumToArray;

    case Owner = 'owner';
    case Maintainer = 'maintainer';
    case Developer = 'developer';
    case Viewer = 'viewer';
}
