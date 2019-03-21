<?php

namespace app\common\utils;

class Utils
{

    public static function getBaseUrl(){
        return "http://" . $_SERVER['HTTP_HOST'] . '/';
    }

}