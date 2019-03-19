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
 * @property Credential $credential
 * @property array $body
 * @property array $headers
 * @property array $query
 */
class BaseController extends Controller
{
    /** @var array $body */
    private $body;

    /** @var array $body */
    private $headers;

    /** @var array $body */
    private $query;

    /** @var ResponseFactory $response */
    private $response;

    /** @var Credential $credential */
    private $credential;

    public function guestActions()
    {
        return ['credential/auth'];
    }

    public function init(){
        $this->response = new ResponseFactory();

        $this->body = json_decode(Yii::$app->getRequest()->getRawBody(), true);
        $this->headers = Yii::$app->getRequest()->getHeaders();
        $this->query = Yii::$app->getRequest()->get();

        if($this->requiresAuth()){
            $this->authenticate();
        }

    }

    public function behaviors() {
        return [
            'rateLimiter' => [
                'class' => 'yii\filters\RateLimiter',
                'enableRateLimitHeaders' => false
            ]
        ];
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

    /**
     * @return Credential
     */
    public function getCredential(): Credential
    {
        return $this->credential;
    }

    /**
     * @return ResponseFactory
     */
    public function getResponse(): ResponseFactory
    {
        return $this->response;
    }

    /**
     * @param string $arg
     * @return null|string
     */
    protected function getBody(string $arg): ?string{
        return $this->body[$arg] ?? null;
    }

    /**
     * @param string $arg
     * @return null|string
     */
    protected function getHeaders(string $arg): ?string{
        return $this->headers[$arg] ?? null;
    }

    /**
     * @param string $arg
     * @return null|string
     */
    protected function getQuery(string $arg): ?string{
        return $this->query[$arg] ?? null;
    }



}