<?php

namespace app\controllers;

use app\common\controllers\BaseController;
use app\common\exceptions\ActiveRecordNotFoundException;
use app\models\File;
use app\models\PhoneNumber;
use Yii;
use yii\web\UploadedFile;

class PhoneNumberController extends BaseController
{

    public function actionValidate()
    {

        $number = $this->getBody('number');
        $identifier = $this->getBody('identifier');

        if(empty($number)){
            return $this->response->falseMissingParams();
        }

        $phone_number = PhoneNumber::validateNumber($number, $identifier);
        $phone_number_fix = $phone_number->getPhoneNumberFixes()->asArray()->all();

        $result = $phone_number->toArray();
        $result['fixes'] = $phone_number_fix;

        return $this->response->success($result);
    }
}
