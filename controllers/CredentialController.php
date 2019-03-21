<?php

namespace app\controllers;


use app\common\controllers\BaseController;
use app\models\Credential;
use Exception;
use Yii;
use yii\web\UnauthorizedHttpException;

class CredentialController extends BaseController
{

    /**
     * Endpoint to authenticate API client. It receives a username and password and returns jwt token.
     * @return array
     * @throws UnauthorizedHttpException
     */
    public function actionAuth()
    {
        $username = $this->getBody('username');
        $password = $this->getBody('password');

        if(empty($username || $password)){
            return $this->getResponse()->falseMissingParams();
        }

        try{
            $result = Credential::getJWT($username, $password);
        } catch (Exception $e){
            return $this->getResponse()->falseAccessDenied();
        }

        return $this->getResponse()->success($result);
    }



}