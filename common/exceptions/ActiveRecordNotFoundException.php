<?php

namespace app\common\exceptions;

use Exception;

class ActiveRecordNotFoundException extends Exception
{

    public function __construct(string $active_record, int $record_id, int $code = 0, Exception $previous = null)
    {

        $message = $active_record . ' not found for id: ' . $record_id;

        parent::__construct($message, $code, $previous);
    }

}