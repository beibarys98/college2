<?php

namespace frontend\models;

use common\models\Category;
use common\models\Participant;
use Yii;
use yii\base\Model;
use common\models\User;

class SignupForm extends Model
{
    public $category_id;
    public $ssn;
    public $name;
    public $telephone;
    public $organization;

    public function rules()
    {
        return [

            [['category_id'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],

            ['ssn', 'trim'],
            ['ssn', 'required', 'message' => Yii::t('app', 'Толтырыңыз!')],
            ['ssn', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Этот ИИН уже используется.'],
            ['ssn', 'match', 'pattern' => '/^\d{12}$/', 'message' => Yii::t('app', 'ЖСН 12 сан болуы тиіс!')],

            ['name', 'trim'],
            ['name', 'required', 'message' => Yii::t('app', 'Толтырыңыз!')],
            ['name', 'match', 'pattern' => '/^[А-Яа-яЁё]+(?:\s+[А-Яа-яЁё]+)+$/u', 'message' => Yii::t('app', 'Кемінде 2 сөз және кириллица болуы тиіс!')],

            // Telephone rules
            ['telephone', 'trim'],
            ['telephone', 'required', 'message' => Yii::t('app', 'Толтырыңыз!')],
            ['telephone', 'match', 'pattern' => '/^\+?[0-9\-()\s]+$/', 'message' => Yii::t('app', 'Телефон номерін енгізіңіз!')],
            ['telephone', 'string', 'min' => 7, 'max' => 20],

            // Organisation rules
            ['organization', 'trim'],
            ['organization', 'required', 'message' => Yii::t('app', 'Толтырыңыз!')],
            ['organization', 'string', 'max' => 255],
        ];
    }

    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->category_id = $this->category_id;
        $user->course_id = null;
        $user->ssn = $this->ssn;
        $user->name = $this->name;
        $user->telephone = $this->telephone;
        $user->organization = $this->organization;
        $user->setPassword('password');
        $user->generateAuthKey();

        return $user->save(false);
    }
}
