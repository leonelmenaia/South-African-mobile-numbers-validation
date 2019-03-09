<?php

namespace app\controllers;

use app\common\controllers\BaseController;

class PhoneController extends BaseController
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
        return $this->response->success();
    }

    public function actionDetails()
    {
        return $this->response->success();
    }

    public function actionValidateFile(){
        return $this->response->success();
    }
}
