<?php

namespace app\common\utils;

use DateTime;

class TimeUtils
{

    public static function now()
    {
        return (new DateTime())->format('Y-m-d H:i:s');
    }

}