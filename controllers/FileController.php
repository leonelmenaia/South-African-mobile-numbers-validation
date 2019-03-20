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
     * Endpoint to get file details by id. It returns the file_id, stats and download link for
     * the phone numbers validated.
     * @return array
     * @throws ActiveRecordNotFoundException
     */
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

    /**
     * Endpoint that receives a binary file (csv) and validates each phone number.
     * It returns the file_id, stats and download link for the phone numbers validated.
     *
     * @return array
     */
    public function actionValidate()
    {

        $file = file_get_contents('php://input');

        if(empty($file)){
            return $this->response->falseMissingParams();
        }

        $file = File::csvToArray($file);
        $file = File::validateFile($file)->toArray();
        $file['stats'] = File::getStats($file['id']);
        $file['download'] = File::getDownloadLink($file['id']);

        return $this->getResponse()->success($file);
    }

}