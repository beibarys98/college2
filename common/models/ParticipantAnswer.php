<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%participant_answer}}".
 *
 * @property int $id
 * @property int $participant_id
 * @property int $question_id
 * @property int|null $answer_id
 *
 * @property Answer $answer
 * @property Participant $participant
 * @property Question $question
 */
class ParticipantAnswer extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%participant_answer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['answer_id'], 'default', 'value' => null],
            [['participant_id', 'question_id'], 'required'],
            [['participant_id', 'question_id', 'answer_id'], 'integer'],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Participant::class, 'targetAttribute' => ['participant_id' => 'id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::class, 'targetAttribute' => ['question_id' => 'id']],
            [['answer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Answer::class, 'targetAttribute' => ['answer_id' => 'id']],
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
            'question_id' => Yii::t('app', 'Question ID'),
            'answer_id' => Yii::t('app', 'Answer ID'),
        ];
    }

    /**
     * Gets query for [[Answer]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\AnswerQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(Answer::class, ['id' => 'answer_id']);
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
     * @return \common\models\query\ParticipantAnswerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ParticipantAnswerQuery(get_called_class());
    }

}
