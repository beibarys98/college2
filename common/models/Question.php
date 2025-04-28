<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%question}}".
 *
 * @property int $id
 * @property int $test_id
 * @property string|null $question
 * @property int|null $answer
 * @property string|null $img_path
 *
 * @property Answer[] $answers
 * @property ParticipantAnswer[] $participantAnswers
 * @property Test $test
 */
class Question extends \yii\db\ActiveRecord
{
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%question}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'jpg, jpeg, png', 'skipOnEmpty' => true],

            [['question', 'answer', 'img_path'], 'default', 'value' => null],
            [['test_id'], 'required'],
            [['test_id', 'answer'], 'integer'],
            [['question'], 'string'],
            [['img_path'], 'string', 'max' => 255],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::class, 'targetAttribute' => ['test_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'test_id' => Yii::t('app', 'Test ID'),
            'question' => Yii::t('app', 'Question'),
            'answer' => Yii::t('app', 'Answer'),
            'img_path' => Yii::t('app', 'Img Path'),
        ];
    }

    /**
     * Gets query for [[Answers]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\AnswerQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(Answer::class, ['question_id' => 'id']);
    }

    /**
     * Gets query for [[ParticipantAnswers]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\ParticipantAnswerQuery
     */
    public function getParticipantAnswers()
    {
        return $this->hasMany(ParticipantAnswer::class, ['question_id' => 'id']);
    }

    /**
     * Gets query for [[Test]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\TestQuery
     */
    public function getTest()
    {
        return $this->hasOne(Test::class, ['id' => 'test_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\QuestionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\QuestionQuery(get_called_class());
    }

}
