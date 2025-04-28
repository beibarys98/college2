<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%test}}".
 *
 * @property int $id
 * @property int $course_id
 * @property string $lang
 * @property string $status
 * @property string $duration
 *
 * @property Course $course
 * @property Question[] $questions
 */
class Test extends \yii\db\ActiveRecord
{
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%test}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'doc, docx', 'skipOnEmpty' => false],

            [['status'], 'default', 'value' => 'new'],
            [['course_id', 'lang', 'duration'], 'required'],
            [['course_id'], 'integer'],
            [['duration'], 'safe'],
            [['lang'], 'string', 'max' => 10],
            [['status'], 'string', 'max' => 50],
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
            'lang' => Yii::t('app', 'Lang'),
            'status' => Yii::t('app', 'Status'),
            'duration' => Yii::t('app', 'Duration'),
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
     * Gets query for [[Questions]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\QuestionQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::class, ['test_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\TestQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\TestQuery(get_called_class());
    }

}
