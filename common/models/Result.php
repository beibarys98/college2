<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%result}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $result
 *
 * @property User $user
 */
class Result extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%result}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'result'], 'required'],
            [['user_id', 'result'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'result' => Yii::t('app', 'Result'),
        ];
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
     * @return \common\models\query\ResultQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ResultQuery(get_called_class());
    }

}
