<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%file_type}}".
 *
 * @property int $id
 * @property string $title
 * @property string|null $title_ru
 */
class FileType extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%file_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title_ru'], 'default', 'value' => null],
            [['title'], 'required'],
            [['title', 'title_ru'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'title_ru' => Yii::t('app', 'Title Ru'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\FileTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\FileTypeQuery(get_called_class());
    }

}
