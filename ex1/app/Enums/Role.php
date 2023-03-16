<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class Role extends Enum implements LocalizedEnum
{

    public const ADMIN = 'admin';

    public const USER = 'user';

}
