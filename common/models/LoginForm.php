<?php

namespace common\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;

    public function rules()
    {
        return [
            [['username'], 'required', 'message' => Yii::t('app', 'Толтырыңыз!')],
            [['password'], 'safe'],
            ['rememberMe', 'boolean'],
        ];
    }

    public function login()
    {
        // Get the user from the database
        $user = $this->getUser();

        // Check if user exists
        if ($user) {
            // If username is 'admin', validate the password
            if ($user->ssn === 'admin') {
                if (empty($this->password)) {
                    Yii::$app->session->setFlash('error', 'Неверный пароль!');
                    return false;  // Stop login if password is empty
                }

                if (!$user->validatePassword($this->password)) {
                    Yii::$app->session->setFlash('error', 'Неверный пароль!');
                    return false;  // Password validation failed for admin
                }
            }

            // Log in user (admin or non-admin)
            return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;  // User not found
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::find()->andWhere(['ssn' => $this->username])->one();

            if ($this->_user === null && is_numeric($this->username)) {
                $this->_user = User::find()->andWhere(['id' => $this->username])->one();
            }
        }

        return $this->_user;
    }
}
