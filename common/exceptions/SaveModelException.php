<?php

namespace app\common\exceptions;

use Exception;

class SaveModelException extends Exception
{

    public function __construct(array $model_errors, int $code = 0, Exception $previous = null)
    {

        $message = "";

        foreach ($model_errors as $index => $msg) {
            $message = $msg[0];
            break;
        }

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

}