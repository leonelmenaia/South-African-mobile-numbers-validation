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

        $id = $this->getQuery('id');

        if(empty($id)){
            return $this->response->falseMissingParams();
        }

        $file = File::findOne(['id' => $id]);

        if(empty($file)){
            throw new ActiveRecordNotFoundException(File::class, $id);
        }

        $file = $file->toArray();
        $file['stats'] = File::getStats($file['id']);
        $file['download'] = File::getDownloadLink($file['id']);

        return $this->getResponse()->success($file);
    }

    public function actionValidate()
    {

        $file = file_get_contents('php://input');

        if(empty($file)){
            return $this->response->falseMissingParams();
        }

        $file = File::binaryToArray($file);
        $file = File::validateFile($file)->toArray();
        $file['stats'] = File::getStats($file['id']);
        $file['download'] = File::getDownloadLink($file['id']);

        return $this->getResponse()->success($file);
    }

}