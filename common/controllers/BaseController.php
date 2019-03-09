<?php
namespace app\common\controllers;

use app\common\components\Response\ResponseFactory;
use yii\web\Controller;

/**
 * Class BaseController
 *
 * @package common\controllers
 *
 * @property ResponseFactory $response
 */
class BaseController extends Controller
{

    public $response;

    public function init(){
        $this->response = new ResponseFactory();
    }

}