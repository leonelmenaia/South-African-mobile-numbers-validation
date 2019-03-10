<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_number".
 *
 * @property int $id
 * @property int $phone_identifier
 * @property int $file_id
 * @property int $number
 * @property int $validated
 * @property string $created_at
 *
 * @property File $file
 * @property PhoneNumberFix[] $phoneNumberFixes
 */
class PhoneNumber extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() : string
    {
        return 'phone_number';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            [['phone_identifier', 'validated'], 'required'],
            [['phone_identifier', 'file_id', 'number', 'validated'], 'integer'],
            [['number'], 'string', 'max' => 100],
            [['created_at'], 'safe'],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['file_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'id' => 'ID',
            'phone_identifier' => 'Phone Identifier',
            'file_id' => 'File ID',
            'number' => 'Number',
            'validated' => 'Validated',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFile() : ActiveQuery
    {
        return $this->hasOne(File::className(), ['id' => 'file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoneNumberFixes() : ActiveQuery
    {
        return $this->hasMany(PhoneNumberFix::className(), ['phone_id' => 'id']);
    }

    public static function validateNumber($phone_identifier, $phone_number) : array
    {
        return ['aaaaaaaa'];
    }
}