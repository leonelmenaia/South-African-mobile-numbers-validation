<?php

namespace app\controllers;


use app\common\controllers\BaseController;
use app\models\Credential;
use Yii;
use yii\web\UnauthorizedHttpException;

class CredentialController extends BaseController
{

    /**
     * Endpoint to authenticate API client. It receives Basic Auth token and returns jwt token.
     * @return array
     * @throws UnauthorizedHttpException
     */
    public function actionAuth()
    {
        $token = $this->getHeaders('Authorization');

        if(empty($token)){
            throw new UnauthorizedHttpException();
        }

        $basic_auth = Credential::getBasicAuth($token);

        $result = Credential::basicAuth($basic_auth['username'], $basic_auth['password']);

        return $this->getResponse()->success($result);
    }



}