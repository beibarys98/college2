<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Question $model */

$this->title = Yii::t('app', 'Изменить вопрос');
?>
<div class="question-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'question')->textarea(['rows' => 6])->label(false) ?>

    <?= $form->field($model, 'answer')->textInput(['placeholder' => 'Ответ'])->label(false) ?>

    <?= $form->field($model, 'file')->fileInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Изменить'), ['class' => 'btn btn-outline-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
