<?php

namespace app\controllers;


use app\common\controllers\BaseController;
use app\models\Credential;
use Yii;
use yii\web\UnauthorizedHttpException;

class CredentialController extends BaseController
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

    public function actionAuth()
    {
        $token = Yii::$app->getRequest()->getHeaders()->get('Authorization');

        if(empty($token)){
            throw new UnauthorizedHttpException();
        }

        $basic_auth = Credential::getBasicAuth($token);

        $result = Credential::basicAuth($basic_auth['username'], $basic_auth['password']);

        return $this->response->success($result);
    }



}