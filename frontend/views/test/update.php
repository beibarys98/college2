<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Test $model */

$this->title = Yii::t('app', 'Изменить тест');
?>
<div class="test-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lang')->textInput(['maxlength' => true, 'placeholder' => 'Язык'])->label(false) ?>

    <?php if($model->type == 'test'): ?>
    <?= $form->field($model, 'duration')->input('time', ['step' => 1])->label(false) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Изменить'), ['class' => 'btn btn-outline-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
