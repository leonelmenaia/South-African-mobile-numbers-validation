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
 * @property boolean $validated
 * @property string $created_at
 *
 * @property File $file
 * @property PhoneNumberFix[] $phoneNumberFixes
 */
class PhoneNumber extends ActiveRecord
{

    const SOUTH_AFRICA_COUNTRY_INDICATIVE = 27;

    /**
     * {@inheritdoc}
     */
    public static function tableName() : string
    {
        return 'db.phone_number';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            [['phone_identifier', 'validated'], 'required'],
            [['phone_identifier', 'file_id', 'number'], 'integer'],
            [['validated'], 'boolean'],
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

    public static function validateNumber(int $phone_identifier, string $phone_number) : array
    {
        return ['aaaaaaaa'];
    }
}