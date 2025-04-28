<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_survey}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $question_id
 * @property string $answer
 *
 * @property Question $question
 * @property User $user
 */
class UserSurvey extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_survey}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'question_id', 'answer'], 'required'],
            [['user_id', 'question_id'], 'integer'],
            [['answer'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => Yii::t('app', 'User ID'),
            'question_id' => Yii::t('app', 'Question ID'),
            'answer' => Yii::t('app', 'Answer'),
        ];
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
     * @return \common\models\query\UserSurveyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\UserSurveyQuery(get_called_class());
    }

}
