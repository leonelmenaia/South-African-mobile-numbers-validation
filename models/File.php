<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "File".
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 */
class File extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() : string
    {
        return 'File';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'integer'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_at' => 'Created At',
        ];
    }
}