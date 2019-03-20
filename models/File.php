<?php

namespace app\models;

use app\common\exceptions\ActiveRecordNotFoundException;
use app\common\exceptions\SaveModelException;
use app\common\utils\Utils;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "File".
 *
 * @property int $id
 * @property string $created_at
 *
 * @property PhoneNumber[] $phoneNumbers
 */
class File extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoneNumbers(): ActiveQuery
    {
        return $this->hasMany(PhoneNumber::className(), ['file_id' => 'id']);
    }

    /**
     * Receives the file content as a string and returns an bidimensional array.
     *
     * @param string $file
     * @return array
     */
    public static function csvToArray(string $file){
        $lines = explode(PHP_EOL, $file);
        $array = [];
        foreach ($lines as $line) {
            $array[] = str_getcsv($line);
        }
        return $array;
    }

    /**
     * Validates the file by iterating each line and validating each phone number.
     *
     * @param array $file array of content of the file
     * @return File object reference to the file
     * @throws SaveModelException in case it fails to save
     */
    public static function validateFile(array $file): File
    {

        $model = new File();

        if (!$model->save()) {
            throw new SaveModelException($model->getErrors());
        }

        //remove headers from csv
        unset($file[0]);

        foreach($file as $row){

            $data = [];

            $fields = [
                'identifier',
                'number'
            ];

            for ($i = 0; $i < count($row); $i++) {
                if (!empty($fields[$i])) {
                    $field_name = $fields[$i];
                    $data[$field_name] = isset($row[$i]) ? trim($row[$i]) : null;
                }
            }

            PhoneNumber::validateNumber($data['number'], $data['identifier'], $model->id);
        }

        return $model;
    }

    /**
     * Return stats array for the specific file.
     *
     * @param int $id
     * @return array
     * @throws ActiveRecordNotFoundException in case the file id is invalid
     */
    public static function getStats(int $id)
    {

        $file = File::findOne(['id' => $id]);

        if (empty($file)) {
            throw new ActiveRecordNotFoundException(File::class, $id);
        }

        $phone_numbers = $file->phoneNumbers;

        $stat_validated = 0;
        $stat_validated_with_fix = 0;
        $stat_validated_without_fix = 0;
        $stat_invalidated = 0;

        foreach ($phone_numbers as $phone_number) {

            $validated = $phone_number->validated;
            $phone_number_fixes = $phone_number->phoneNumberFixes;

            if ($validated) {
                $stat_validated++;

                if (!empty($phone_number_fixes)) {
                    $stat_validated_with_fix++;
                } else {
                    $stat_validated_without_fix++;
                }

            } else {
                $stat_invalidated++;
            }
        }

        $total = count($phone_numbers);

        //avoid division with 0
        $percentage_validated = $stat_invalidated != 0 ?
            round($stat_validated / $total * 100, 1) : 0;
        $percentage_validated_with_fix = $stat_validated_with_fix != 0 ?
            round($stat_validated_with_fix / $total * 100, 1) : 0;
        $percentage_validated_without_fix = $stat_validated_without_fix != 0 ?
            round($stat_validated_without_fix / $total * 100, 1) : 0;
        $percentage_invalidated = $stat_invalidated != 0 ?
            round($stat_invalidated / $total * 100,1 ) : 0;

        return [
            'validated' => $stat_validated,
            'validated_with_fix' => $stat_validated_with_fix,
            'validated_without_fix' => $stat_validated_without_fix,
            'invalidated' => $stat_invalidated,
            'total' => $total,
            'percentage' => [
                'validated' => $percentage_validated,
                'validated_with_fix' => $percentage_validated_with_fix,
                'percentage_validated_without_fix' => $percentage_validated_without_fix,
                'percentage_invalidated' => $percentage_invalidated
            ]
        ];

    }

    /**
     * Returns the download link for the phone numbers that belong to the file.
     *
     * @param int $id
     * @return string download link
     * @throws ActiveRecordNotFoundException in case the file id is invalid
     */
    public static function getDownloadLink(int $id){

        $file = File::findOne(['id' => $id]);

        if (empty($file)) {
            throw new ActiveRecordNotFoundException(File::class, $id);
        }

        $phone_numbers = $file->getPhoneNumbers()->all();

        /** @var PhoneNumber $phone_number */
        foreach($phone_numbers as &$phone_number){
            $phone_number_fixes = $phone_number->getPhoneNumberFixes()->asArray()->all();

            $phone_number = $phone_number->toArray();
            $phone_number['fixes'] = $phone_number_fixes;
        }

        $file_name = 'files/phone_numbers_' . md5($file->id) . '.json';

        if(file_exists($file_name)){
            return Utils::getBaseUrl() . '/' . $file_name;
        }

        $fp = fopen($file_name, 'w');

        fwrite($fp,json_encode($phone_numbers));

        fclose($fp);

        return Utils::getBaseUrl() . '/' . $file_name;

    }
}