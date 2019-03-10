<?php

namespace tests\unit\models;

use app\models\PhoneNumber;
use app\models\PhoneNumberFix;
use Codeception\Test\Unit;

class PhoneNumberFixTest extends Unit
{

    public function testRemoveNonDigits()
    {
        $phone_number = '\|+*27831qweadas234_!  §567';

        $result = PhoneNumberFix::removeNonDigits($phone_number);

        $this->assertEquals('27831234567', $result);
    }

    public function testAddCountryIndicative()
    {
        $phone_number = '831234567';

        $result = PhoneNumberFix::addCountryIndicative($phone_number);

        $this->assertEquals('27831234567', $result);
    }

}

