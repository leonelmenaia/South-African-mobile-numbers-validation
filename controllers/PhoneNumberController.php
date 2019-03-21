<?php

namespace app\controllers;

use app\common\controllers\BaseController;
use app\common\exceptions\ActiveRecordNotFoundException;
use app\models\File;
use app\models\PhoneNumber;
use Exception;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\UploadedFile;

class PhoneNumberController extends BaseController
{

    /**
     * Validates a single number and returns the possible fixes.
     * @return array
     */
    public function actionValidate()
    {

        $number = $this->getBody('number');
        $identifier = $this->getBody('identifier');

        if(empty($number)){
            return $this->response->falseMissingParams();
        }

        try {
            $phone_number = PhoneNumber::validateNumber($number, $identifier);
            $phone_number_fix = $phone_number->getPhoneNumberFixes()->asArray()->all();

            $result = $phone_number->toArray();
            $result['fixes'] = $phone_number_fix;
        } catch(InvalidArgumentException $e){
            return $this->getResponse()->false(null, 'INVALID_NUMBER');
        } catch(Exception $e){
            return $this->getResponse()->falseServerError();
        }

        return $this->response->success($result);
    }
}
