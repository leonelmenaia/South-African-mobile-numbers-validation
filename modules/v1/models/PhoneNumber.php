<?php

namespace app\modules\v1\models;

use app\common\exceptions\ActiveRecordNotFoundException;
use app\common\exceptions\SaveModelException;
use app\common\utils\TimeUtils;
use Exception;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_number".
 *
 * @property int $id
 * @property int $identifier
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

    const SOUTH_AFRICA_COUNTRY_INDICATIVE = '27';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'phone_number';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['number', 'validated'], 'required'],
            [['identifier', 'file_id'], 'integer'],
            [['validated'], 'boolean'],
            [['number'], 'string', 'max' => 100],
            [['created_at'], 'safe'],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['file_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'identifier' => 'Phone Identifier',
            'file_id' => 'File ID',
            'number' => 'Number',
            'validated' => 'Validated',
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
    public function getFile(): ActiveQuery
    {
        return $this->hasOne(File::className(), ['id' => 'file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoneNumberFixes(): ActiveQuery
    {
        return $this->hasMany(PhoneNumberFix::className(), ['phone_id' => 'id']);
    }

    /**
     * Receives a number and validates it. If it's incorrect it tries to fix it.
     *
     * @param string $number
     * @param int|null $identifier phone number identifier presented in the csv
     * @param int|null $file_id file associated with the number
     * @return PhoneNumber
     * @throws ActiveRecordNotFoundException
     * @throws Exception
     */
    public static function validateNumber(string $number,
                                          int $identifier = null,
                                          int $file_id = null): PhoneNumber
    {

        if(empty($number)){
            throw new InvalidArgumentException();
        }

        if(!empty($file_id)){
            $file = File::findOne(['id' => $file_id]);

            if(empty($file)){
                throw new ActiveRecordNotFoundException(File::class, $file_id);
            }
        }

        $transaction = Yii::$app->getDb()->beginTransaction();

        try {

            $model = new PhoneNumber();
            $model->identifier = $identifier;
            $model->file_id = $file_id;
            $model->number = $number;
            $model->validated = false;

            if (!$model->save()) {
                throw new SaveModelException($model->getErrors());
            }

            //if number is already validated update model and return
            if (self::isNumberValid($number)) {

                $model->validated = true;

                if(!$model->save()){
                    throw new SaveModelException($model->getErrors());
                }

                $transaction->commit();

                return $model;
            }

            $new_number = PhoneNumberFix::fixNumber($number, $model->id);

            //if it was not possible to fix the number return the number as not validated
            if (empty($new_number)) {
                $transaction->commit();
                return $model;
            }

            //save new number as validated
            $model->number = $new_number;
            $model->validated = true;

            if(!$model->save()){
                throw new SaveModelException($model->getErrors());
            }

        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        $transaction->commit();
        return $model;
    }

    /**
     * Check if the number is valid. It should only have digits, it should have 11 digits
     * and start with the country indicative.
     *
     * @param string $number
     * @return bool
     */
    public static function isNumberValid(string $number)
    {
        return ctype_digit($number) &&
            strlen($number) === 11 &&
            substr($number, 0, 2) === self::SOUTH_AFRICA_COUNTRY_INDICATIVE;
    }
}