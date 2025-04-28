<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $ssn
 * @property string $password_hash
 * @property string $auth_key
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'xls, xlsx', 'skipOnEmpty' => true],

            [['category_id'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],

            [['course_id'], 'integer'],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['course_id' => 'id']],

            ['ssn', 'trim'],
            ['ssn', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Этот ИИН уже используется.'],
            ['ssn', 'match', 'pattern' => '/^\d{12}$/', 'message' => Yii::t('app', 'ЖСН 12 сан болуы тиіс!')],

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
            ['organization', 'trim'],
            ['organization', 'string', 'max' => 255],
        ];
    }

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function getCourse()
    {
        return $this->hasOne(Course::class, ['id' => 'course_id']);
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findByUsername($ssn)
    {
        return static::findOne(['ssn' => $ssn]);
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token
        ]);
    }

    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
