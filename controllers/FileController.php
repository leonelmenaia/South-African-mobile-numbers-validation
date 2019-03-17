<?php

namespace app\controllers;


use app\common\controllers\BaseController;
use app\common\exceptions\ActiveRecordNotFoundException;
use app\models\File;
use Yii;
use yii\web\UploadedFile;

class FileController extends BaseController
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

    public function actionDetails()
    {
        $id = Yii::$app->getRequest()->get('id');

        if(empty($id)){
            return $this->response->falseMissingParams();
        }

        $file = File::findOne(['id' => $id]);

        if(empty($file)){
            throw new ActiveRecordNotFoundException(File::class, $id);
        }

        $file = $file->toArray();
        $file['stats'] = File::getStatsById($file['id']);


        return $this->response->success($file);
    }

    public function actionValidate()
    {
        $file = UploadedFile::getInstanceByName("file");

        if(empty($file)){
            return $this->response->falseMissingParams();
        }

        $file = File::validateFile($file);
        $file = $file->toArray();
        $file['stats'] = File::getStatsById($file['id']);

        return $this->response->success($file);
    }

}