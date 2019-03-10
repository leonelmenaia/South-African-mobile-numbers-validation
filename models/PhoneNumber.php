<?php

namespace app\models;


use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_number".
 *
 * @property int $id
 * @property int $phone_id
 * @property int $file_id
 * @property string $processed_number
 * @property int $validated_number
 * @property string $fix_type
 * @property string $error_type
 * @property int $validated
 * @property string $created_at
 */
class PhoneNumber extends ActiveRecord
{

    //ErrorType
    const ERROR_INVALID_COUNTRY_INDICATIVE = 'INVALID_COUNTRY_INDICATIVE';
    const ERROR_LOWER_LENGTH = 'LOWER_LENGTH';
    const ERROR_HIGHER_LENGTH = 'HIGHER_LENGTH';

    //FixType
    const FIX_ADD_COUNTRY_INDICATIVE = 'ADD_COUNTRY_INDICATIVE';
    const FIX_REMOVE_NON_DIGITS = 'REMOVE_NON_DIGITS';

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
            [['phone_id', 'processed_number', 'validated'], 'required'],
            [['phone_id','file_id', 'validated_number', 'validated'], 'integer'],
            [['created_at'], 'safe'],
            [['processed_number'], 'string', 'max' => 100],
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
            'phone_id' => 'Phone ID',
            'file_id' => 'File ID',
            'processed_number' => 'Processed Number',
            'validated_number' => 'Phone Number',
            'fix_type' => 'Fix Type',
            'error_type' => 'Error Type',
            'validated' => 'Validated',
            'created_at' => 'Created At',
        ];
    }

    public static function validateNumber($phone_id, $phone_number)
    {

    }
}