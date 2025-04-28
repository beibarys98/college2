<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%course}}".
 *
 * @property int $id
 * @property string $title
 * @property int|null $category_id
 * @property string|null $month
 * @property string|null $duration
 *
 * @property Category $category
 * @property Certificate[] $certificates
 * @property Participant[] $participants
 * @property Test[] $tests
 */
class Course extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%course}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'month', 'duration'], 'default', 'value' => null],
            [['title'], 'required'],
            [['category_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['month', 'duration'], 'string', 'max' => 20],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'category_id' => Yii::t('app', 'Category ID'),
            'month' => Yii::t('app', 'Month'),
            'duration' => Yii::t('app', 'Duration'),
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\CategoryQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Certificates]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\CertificateQuery
     */
    public function getCertificates()
    {
        return $this->hasMany(Certificate::class, ['course_id' => 'id']);
    }

    /**
     * Gets query for [[Participants]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\ParticipantQuery
     */
    public function getParticipants()
    {
        return $this->hasMany(Participant::class, ['course_id' => 'id']);
    }

    /**
     * Gets query for [[Tests]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\TestQuery
     */
    public function getTests()
    {
        return $this->hasMany(Test::class, ['course_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\CourseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CourseQuery(get_called_class());
    }

}
