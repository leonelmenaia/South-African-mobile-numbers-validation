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
 * @property string $name
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
            [['name'], 'required'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
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

    public static function validateFile(UploadedFile $file): File
    {

        $model = new File();
        $model->name = $file->name;

        if (!$model->save()) {
            throw new SaveModelException($model->getErrors());
        }

        ini_set('auto_detect_line_endings', TRUE); // Some sort of Excel hack, to not having the file messed up in Win Office 2007
        $handle = fopen($file->tempName, 'r');

        //ignore csv headers (id, sms_phone)
        fgetcsv($handle);

        while (($csv_row = fgetcsv($handle, 0, ',')) !== FALSE) {

            $data = [];

            $fields = [
                'identifier',
                'number'
            ];

            for ($i = 0; $i < count($csv_row); $i++) {
                if (!empty($fields[$i])) {
                    $field_name = $fields[$i];
                    $data[$field_name] = isset($csv_row[$i]) ? trim($csv_row[$i]) : null;
                }
            }

            PhoneNumber::validateNumber($data['number'], $data['identifier'], $model->id);
        }

        return $model;
    }

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