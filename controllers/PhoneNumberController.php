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

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionValidate()
    {

        $phone_number = Yii::$app->getRequest()->post('phone_number');

        if(empty($phone_number)){
            return $this->response->falseMissingParams();
        }

        $phone_number = PhoneNumber::validateNumber($phone_number);
        $phone_number_fix = $phone_number->phoneNumberFixes;

        $result = $phone_number->toArray();
        $result['fixes'] = $phone_number_fix;

        return $this->response->success($result);
    }
}
