<?php

namespace app\models;

use app\common\exceptions\SaveModelException;
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
        return 'db.phone_number';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['identifier', 'validated'], 'required'],
            [['identifier', 'file_id', 'number'], 'integer'],
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

    public static function validateNumber(string $number,
                                          int $identifier = null,
                                          int $file_id = null): array
    {
        $validated = false;

        $model = new PhoneNumber();
        $model->identifier = $identifier;
        $model->file_id = $file_id;
        $model->number = $number;
        $model->validated = $validated;

        if (!$model->save()) {
            throw new SaveModelException($model->errors);
        }

        if (self::isNumberValid($number)) {
            $validated = true;
        }

        if (!$validated) {
            $number = PhoneNumberFix::fixNumber($number, $model->id);
        }

        $result = [
            'identifier' => $model->identifier,
            'file_id' => $model->file_id,
            'number' => $model->number,
            'validated' => $model->validated,
        ];


        return $result;
    }

    public static function isNumberValid($number)
    {
        return ctype_digit($number) &&
            strlen($number) === 11 &&
            substr($number, 0, 2) === self::SOUTH_AFRICA_COUNTRY_INDICATIVE;
    }
}