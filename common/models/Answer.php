<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%answer}}".
 *
 * @property int $id
 * @property int $question_id
 * @property string|null $answer
 * @property string|null $img_path
 *
 * @property ParticipantAnswer[] $participantAnswers
 * @property Question $question
 */
class Answer extends \yii\db\ActiveRecord
{
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%answer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'jpg, jpeg, png', 'skipOnEmpty' => true],

            [['answer', 'img_path'], 'default', 'value' => null],
            [['question_id'], 'required'],
            [['question_id'], 'integer'],
            [['answer'], 'string'],
            [['img_path'], 'string', 'max' => 255],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::class, 'targetAttribute' => ['question_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'question_id' => Yii::t('app', 'Question ID'),
            'answer' => Yii::t('app', 'Answer'),
            'img_path' => Yii::t('app', 'Img Path'),
        ];
    }

    /**
     * Gets query for [[ParticipantAnswers]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\ParticipantAnswerQuery
     */
    public function getParticipantAnswers()
    {
        return $this->hasMany(ParticipantAnswer::class, ['answer_id' => 'id']);
    }

    /**
     * Gets query for [[Question]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\QuestionQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::class, ['id' => 'question_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\AnswerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AnswerQuery(get_called_class());
    }

}
