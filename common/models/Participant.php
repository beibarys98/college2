<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%participant}}".
 *
 * @property int $id
 * @property string $name
 * @property string $telephone
 * @property string $organisation
 */
class Participant extends \yii\db\ActiveRecord
{
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%participant}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'xls, xlsx', 'skipOnEmpty' => true],

            ['name', 'trim'],
            ['name', 'required', 'message' => Yii::t('app', 'Толтырыңыз!')],
            ['name', 'match', 'pattern' => '/^[А-Яа-яЁё]+(?:\s+[А-Яа-яЁё]+)+$/u', 'message' => Yii::t('app', 'Кемінде 2 сөз және кириллица болуы тиіс!')],

            // Telephone rules
            ['telephone', 'trim'],
            ['telephone', 'match', 'pattern' => '/^\+?[0-9\-()\s]+$/', 'message' => Yii::t('app', 'Телефон номерін енгізіңіз!')],
            ['telephone', 'string', 'min' => 11, 'max' => 12,
                'tooShort' => Yii::t('app', 'Телефон номерін енгізіңіз!'),
                'tooLong' => Yii::t('app', 'Телефон номерін енгізіңіз!')
            ],

            // Organisation rules
            ['organisation', 'trim'],
            ['organisation', 'string', 'max' => 255],

            [['course_id'], 'integer'],
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
            'name' => Yii::t('app', 'Name'),
            'telephone' => Yii::t('app', 'Telephone'),
            'organisation' => Yii::t('app', 'Organisation'),
        ];
    }

    public function getCourse()
    {
        return $this->hasOne(Course::class, ['id' => 'course_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['participant_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\ParticipantQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ParticipantQuery(get_called_class());
    }

}
