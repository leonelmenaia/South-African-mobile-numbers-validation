<?php
namespace app\common\controllers;

use app\common\components\Response\ResponseFactory;
use app\common\exceptions\ActiveRecordNotFoundException;
use app\models\Credential;
use app\models\User;
use Firebase\JWT\JWT;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

/**
 * Class BaseController
 *
 * @package common\controllers
 *
 * @property ResponseFactory $response
 */
class BaseController extends Controller
{

    /** @var User $identity */
    private $identity;

    /** @var ResponseFactory $response */
    protected $response;

    /** @var Credential $credential */
    protected $credential;

    public function guestActions()
    {
        return ['credential/auth'];
    }

    public function init(){
        $this->response = new ResponseFactory();

        if($this->requiresAuth()){
            $this->authenticate();
        }
    }

    private function requiresAuth()
    {
        $current_route = Yii::$app->requestedRoute ?? null;

        if (empty($current_route)) {
            return true;
        }

        if (in_array($current_route, $this->guestActions())) {
            return false;
        }

        return true;
    }

    private function authenticate()
    {
        $token = Yii::$app->getRequest()->getHeaders()->get('Authorization') ?? null;

        if(empty($token)){
            throw new UnauthorizedHttpException();
        }

        $this->credential = Credential::findIdentityByAccessToken($token);

        Yii::$app->user->setIdentity($this->credential);
    }

}