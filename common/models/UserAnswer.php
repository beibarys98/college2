<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_answer}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $question_id
 * @property int $answer_id
 *
 * @property Answer $answer
 * @property Question $question
 * @property User $user
 */
class UserAnswer extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_answer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'question_id', 'answer_id'], 'required'],
            [['user_id', 'question_id', 'answer_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => Yii::t('app', 'User ID'),
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
     * Gets query for [[Question]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\QuestionQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::class, ['id' => 'question_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\UserAnswerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\UserAnswerQuery(get_called_class());
    }

}
