<?php

namespace app\models;

use app\common\exceptions\SaveModelException;
use app\common\utils\TimeUtils;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

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
    public static function tableName(): string
    {
        return 'db.phone_number_fix';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
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
    public function attributeLabels(): array
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

    public function beforeSave($insert) : bool
    {

        if ($insert) {
            $this->created_at = TimeUtils::now();
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhone(): ActiveQuery
    {
        return $this->hasOne(PhoneNumber::className(), ['id' => 'phone_id']);
    }

    /**
     * @param string $number
     * @param int|null $phone_id
     * @return string
     * @throws SaveModelException
     */
    public static function fixNumber(string $number, int $phone_id = null): ?string
    {

        if (PhoneNumber::isNumberValid($number)) {
            return null;
        }

        $transaction = Yii::$app->getDb()->beginTransaction();

        $new_number = self::removeNonDigits($number);

        //if there was changes from the fix
        if (!empty($phone_id) && $number !== $new_number) {
            $model = new PhoneNumberFix();
            $model->phone_id = $phone_id;
            $model->number_before = $number;
            $model->number_after = $number = $new_number;
            $model->fix_type = self::FIX_TYPE_REMOVE_NON_DIGITS;

            //TODO nao ta a gravar este for some reason
            if (!$model->save()) {
                throw new SaveModelException($model->getErrors());
            }
        }

        if (PhoneNumber::isNumberValid($number)) {
            $transaction->commit();
            return $number;
        }

        $new_number = self::addCountryIndicative($number);

        //if there was changes from the fix
        if (!empty($phone_id) && $number !== $new_number) {
            $model = new PhoneNumberFix();
            $model->phone_id = $phone_id;
            $model->number_before = $number;
            $model->number_after = $number = $new_number;
            $model->fix_type = self::FIX_TYPE_ADD_COUNTRY_INDICATIVE;

            if (!$model->save()) {
                throw new SaveModelException($model->getErrors());
            }
        }

        if (PhoneNumber::isNumberValid($number)) {
            $transaction->commit();
            return $number;

        }

        //no need to save fixes if the number was incorrect
        $transaction->rollBack();

        return null;

    }

    public static function removeNonDigits(string $phone_number): string
    {

        if(ctype_digit($phone_number)){
            return $phone_number;
        }

        return preg_replace("/[^0-9]/", "", $phone_number);

    }

    public static function addCountryIndicative(string $phone_number): string
    {

        if (strlen($phone_number) === 9) {
            $phone_number = PhoneNumber::SOUTH_AFRICA_COUNTRY_INDICATIVE . $phone_number;
        }

        return $phone_number;
    }
}
