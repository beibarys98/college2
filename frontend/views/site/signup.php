<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var SignupForm $model */

use common\models\Course;
use frontend\models\SignupForm;
use kartik\select2\Select2;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = 'Signup';
?>
<div style="margin: 0 auto; width: 500px;">
    <?= Html::img('@web/images/adort2.png', [
        'alt' => 'logo',
        'class' => 'mb-3',
        'style' => 'width: 50%; display: block; margin: 0 auto;'
    ]) ?>

    <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

    <?php
    $titleField = Yii::$app->language === 'ru' ? 'title_ru' : 'title';

    echo $form->field($model, 'category_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(\common\models\Category::find()->all(), 'id', $titleField),
        'options' => [
            'placeholder' => Yii::t('app', 'Мамандық'),
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label(false);
    ?>

    <?= $form->field($model, 'ssn')->textInput(['autofocus' => true, 'placeholder' => Yii::t('app', 'ЖСН')])->label(false) ?>

    <?= $form->field($model, 'name')->textInput(['placeholder' => Yii::t('app', 'ТАЖ')])->label(false) ?>

    <?= $form->field($model, 'telephone')->textInput(['placeholder' => 'Телефон'])->label(false) ?>

    <?= $form->field($model, 'organization')->textInput(['placeholder' => Yii::t('app', 'Мекеме')])->label(false) ?>

    <div class="form-group text-center">
        <?= Html::submitButton(Yii::t('app', 'Тіркелу'), ['class' => 'btn btn-outline-success', 'name' => 'signup-button']) ?>
    </div>

    <div class="text-end mt-2">
        <?= Html::a(Yii::t('app', 'Артқа'), ['site/login'], ['class' => 'btn btn-outline-primary']) ?>
    </div>

    <hr>
    <div>
        <?php
        echo Html::tag('div', Html::a( Html::img(
            Yii::$app->language == 'kz' ? '/images/kz.png' : '/images/ru.png',
            ['style' => 'width: 40px; height: 40px; border: 1px solid black;', 'class' => 'rounded']
        ), ['/site/language', 'view' => '/site/index']));
        ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
