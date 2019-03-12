<?php

namespace app\models;

use app\common\exceptions\SaveModelException;
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

    const FIX_TYPE_REMOVE_NON_DIGITS = 'REMOVE_NON_DIGITS';
    const FIX_TYPE_ADD_COUNTRY_INDICATIVE = 'ADD_COUNTRY_INDICATIVE';

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

    public static function fixNumber(string $number, int $phone_id){

        if(PhoneNumber::isNumberValid($number)){
            return true;
        }

        $number = self::removeNonDigits($number, $phone_id);

        if(PhoneNumber::isNumberValid($number)){
            return true;
        }

        $number = self::addCountryIndicative($number, $phone_id);

        if(PhoneNumber::isNumberValid($number)){
            return true;
        }

        return false;

    }

    public static function removeNonDigits(string $phone_number, int $phone_id = null): string
    {
        $phone_number_fixed = preg_replace("/[^0-9]/", "", $phone_number );

        if($phone_id != null){
            $model = new PhoneNumberFix();
            $model->phone_id = $phone_id;
            $model->fix_type = self::FIX_TYPE_REMOVE_NON_DIGITS;
            $model->number_before = $phone_number;
            $model->number_after = $phone_number_fixed;

            if($model->save()){
                throw new SaveModelException($model->errors);
            }
        }

        return $phone_number_fixed;
    }

    public static function addCountryIndicative(string $phone_number, int $phone_id = null) : string
    {
        $phone_number_fixed = $phone_number;

        if(strlen($phone_number) === 9){
            $phone_number = PhoneNumber::SOUTH_AFRICA_COUNTRY_INDICATIVE . $phone_number;
        }

        if($phone_id != null){
            $model = new PhoneNumberFix();
            $model->phone_id = $phone_id;
            $model->fix_type = self::FIX_TYPE_REMOVE_NON_DIGITS;
            $model->number_before = $phone_number;
            $model->number_after = $phone_number_fixed;

            if($model->save()){
                throw new SaveModelException($model->errors);
            }
        }

        return $phone_number;
    }
}
