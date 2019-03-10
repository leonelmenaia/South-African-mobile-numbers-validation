<?php

namespace app\models;


use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_number".
 *
 * @property int $id
 * @property int $file_id
 * @property string $processed_number
 * @property string $country_iso
 * @property int $country_indicative
 * @property int $phone_number
 * @property string $fix_type
 * @property string $error_type
 * @property int $validated
 * @property string $created_at
 */
class PhoneNumber extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'phone_number';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file_id', 'processed_number', 'validated'], 'required'],
            [['file_id', 'country_indicative', 'phone_number', 'validated'], 'integer'],
            [['created_at'], 'safe'],
            [['processed_number'], 'string', 'max' => 100],
            [['country_iso'], 'string', 'max' => 10],
            [['fix_type', 'error_type'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_id' => 'File ID',
            'processed_number' => 'Processed Number',
            'country_iso' => 'Country Iso',
            'country_indicative' => 'Country Indicative',
            'phone_number' => 'Phone Number',
            'fix_type' => 'Fix Type',
            'error_type' => 'Error Type',
            'validated' => 'Validated',
            'created_at' => 'Created At',
        ];
    }
}