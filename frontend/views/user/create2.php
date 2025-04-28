<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Participant $model */
/** @var yii\bootstrap5\ActiveForm $form */

$this->title = 'Добавить участников';
?>

<div class="participant-form">

    <h1><?= $this->title ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Имя'])->label(false) ?>

    <?= $form->field($model, 'ssn')->textInput(['placeholder' => 'ИИН'])->label(false) ?>

    <?= $form->field($model, 'telephone')->textInput(['maxlength' => true, 'placeholder' => 'Телефон'])->label(false) ?>

    <?= $form->field($model, 'organization')->textInput(['maxlength' => true, 'placeholder' => 'Организация'])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Добавить'), ['class' => 'btn btn-outline-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
