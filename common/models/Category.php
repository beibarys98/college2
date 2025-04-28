<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property int $id
 * @property string $title
 * @property string $title_ru
 * @property string $type
 *
 * @property Course[] $courses
 */
class Category extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'title_ru', 'type'], 'required'],
            [['title', 'title_ru'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 20],
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
            'type' => Yii::t('app', 'Type'),
        ];
    }

    /**
     * Gets query for [[Courses]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\CourseQuery
     */
    public function getCourses()
    {
        return $this->hasMany(Course::class, ['category_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\CategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CategoryQuery(get_called_class());
    }

}
