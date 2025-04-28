<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%file}}".
 *
 * @property int $id
 * @property int $participant_id
 * @property int $course_id
 * @property string $file_path
 * @property string $type
 *
 * @property Course $course
 * @property Participant $participant
 */
class File extends \yii\db\ActiveRecord
{
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['file', 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf'],

            [['participant_id', 'course_id', 'file_path', 'type'], 'required'],
            [['participant_id', 'course_id'], 'integer'],
            [['file_path', 'title', 'title_ru'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 50],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Participant::class, 'targetAttribute' => ['participant_id' => 'id']],
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
            'participant_id' => Yii::t('app', 'Participant ID'),
            'course_id' => Yii::t('app', 'Course ID'),
            'file_path' => Yii::t('app', 'File Path'),
            'type' => Yii::t('app', 'Type'),
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
     * Gets query for [[Participant]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\ParticipantQuery
     */
    public function getParticipant()
    {
        return $this->hasOne(Participant::class, ['id' => 'participant_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\FileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\FileQuery(get_called_class());
    }

}
