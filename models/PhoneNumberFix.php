<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_number_fix".
 *
 * @property int $id
 * @property int $phone_id
 * @property string $fix_type
 * @property int $number_before
 * @property int $number_after
 * @property string $created_at
 *
 * @property PhoneNumber $phone
 */
class PhoneNumberFix extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() : string
    {
        return 'db.phone_number_fix';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            [['phone_id', 'fix_type', 'number_before', 'number_after'], 'required'],
            [['phone_id'], 'integer'],
            [['created_at'], 'safe'],
            [['fix_type'], 'string', 'max' => 50],
            [['number_before', 'number_after'], 'string', 'max' => 100],
            [['phone_id'], 'exist', 'skipOnError' => true, 'targetClass' => PhoneNumber::className(), 'targetAttribute' => ['phone_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'id' => 'ID',
            'phone_id' => 'Phone ID',
            'fix_type' => 'Fix Type',
            'number_before' => 'Number Before',
            'number_after' => 'Number After',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhone() : ActiveQuery
    {
        return $this->hasOne(PhoneNumber::className(), ['id' => 'phone_id']);
    }

    public static function fixNumber($phone_number){

    }

    public static function removeNonDigits(string $phone_number): string
    {
        return preg_replace("/[^0-9]/", "", $phone_number );
    }

    public static function addCountryIndicative(string $phone_number) : string
    {
        if(strlen($phone_number) === 9){
            $phone_number = PhoneNumber::SOUTH_AFRICA_COUNTRY_INDICATIVE . $phone_number;
        }

        return $phone_number;
    }
}
