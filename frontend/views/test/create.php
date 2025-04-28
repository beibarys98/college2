<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Test $model */
/** @var $type */

if($type == 'test'){
    $title = 'тест';
}else{
    $title = 'анкету';
}

$this->title = Yii::t('app', 'Добавить ' . $title);
?>
<div class="test-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <!-- File input -->
    <?= $form->field($model, 'file')->fileInput()->label(false) ?>

    <!-- Lang input -->
    <?= $form->field($model, 'lang')->textInput(['maxlength' => true, 'placeholder' => 'Язык'])->label(false) ?>

    <?php if($type == 'test'): ?>

    <!-- Duration as time input -->
    <?= $form->field($model, 'duration')->input('time', ['step' => 1])->label(false) ?> <!-- step=1 enables HH:MM:SS -->

    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Добавить'), ['class' => 'btn btn-outline-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
