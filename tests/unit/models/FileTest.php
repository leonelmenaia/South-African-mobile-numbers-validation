<?php

namespace tests\unit\models;

use app\models\File;
use Codeception\Test\Unit;

class FileTest extends Unit
{

    public function testValidateFile(){

        $data = [
            ['id','sms_phone'],
            ['123','27831234567']
        ];

        $file = File::validateFile($data)->toArray();
        $file['stats'] = File::getStats($file['id']);
        $file['download'] = File::getDownloadLink($file['id']);

        $this->assertEquals(null, $file);

    }

    public function testEmptyFile(){

    }

    public function testGetFileDetails(){

    }

    public function testGetUnknownFileDetails(){

    }

}