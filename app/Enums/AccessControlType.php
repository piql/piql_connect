<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class AccessControlType extends Enum
{
    const PermissionGroup  = 0;
    const Permission       = 1;
    const Role             = 2;
}
