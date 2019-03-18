<?php

namespace app\controllers;


use app\common\controllers\BaseController;
use app\common\exceptions\ActiveRecordNotFoundException;
use app\models\File;
use Yii;
use yii\web\UploadedFile;

class FileController extends BaseController
{

    public function actionDetails()
    {

        $id = Yii::$app->getRequest()->get('id') ?? null;

        if(empty($id)){
            return $this->response->falseMissingParams();
        }

        $file = File::findOne(['id' => $id])->toArray();

        if(empty($file)){
            throw new ActiveRecordNotFoundException(File::class, $id);
        }

        $file['stats'] = File::getStats($file['id']);
        $file['download'] = File::getDownloadLink($file['id']);

        return $this->response->success($file);
    }

    public function actionValidate()
    {


        $file = UploadedFile::getInstanceByName("file") ?? null;

        if(empty($file)){
            return $this->response->falseMissingParams();
        }

        $file = File::validateFile($file)->toArray();
        $file['stats'] = File::getStats($file['id']);
        $file['download'] = File::getDownloadLink($file['id']);

        return $this->response->success($file);
    }

}