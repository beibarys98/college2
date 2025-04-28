<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%certificate}}".
 *
 * @property int $id
 * @property int $course_id
 * @property string|null $img_path
 *
 * @property Course $course
 */
class Certificate extends \yii\db\ActiveRecord
{
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%certificate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'jpg, jpeg, png', 'skipOnEmpty' => false],

            [['img_path'], 'default', 'value' => null],
            [['course_id'], 'required'],
            [['course_id'], 'integer'],
            [['img_path'], 'string', 'max' => 255],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['course_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'course_id' => Yii::t('app', 'Course ID'),
            'img_path' => Yii::t('app', 'Img Path'),
        ];
    }

    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\CourseQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::class, ['id' => 'course_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\CertificateQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CertificateQuery(get_called_class());
    }

}
