<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Course $model */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var $type */

$this->title = 'Добавить цикл';
?>

<div class="course-form">

    <h1><?= $this->title ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => 'Название'])->label(false) ?>

    <?php if($type != 'sem'): ?>

    <?= $form->field($model, 'month')->dropDownList(
        [
            'Қаңтар' => Yii::t('app', 'Қаңтар'),
            'Ақпан' => Yii::t('app', 'Ақпан'),
            'Наурыз' => Yii::t('app', 'Наурыз'),
            'Сәуір' => Yii::t('app', 'Сәуір'),
            'Мамыр' => Yii::t('app', 'Мамыр'),
            'Маусым' => Yii::t('app', 'Маусым'),
            'Шілде' => Yii::t('app', 'Шілде'),
            'Тамыз' => Yii::t('app', 'Тамыз'),
            'Қыркүйек' => Yii::t('app', 'Қыркүйек'),
            'Қазан' => Yii::t('app', 'Қазан'),
            'Қараша' => Yii::t('app', 'Қараша'),
            'Желтоқсан' => Yii::t('app', 'Желтоқсан'),
        ],
        ['prompt' => Yii::t('app', 'Ай')]
    )->label(false) ?>

    <?php
    $options = ($type == 'pov')
        ? [
            '1 апта' => '1 неделя',
            '2 апта' => '2 недели',
            '3 апта' => '3 недели',
        ]
        : [
            '1 ай' => '1 месяц',
            '1.5 ай' => '1.5 месяца',
        ];
    ?>

    <?= $form->field($model, 'duration')->dropDownList(
        $options,
        ['prompt' => Yii::t('app', 'Ұзақтығы')]
    )->label(false) ?>

    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Добавить'), ['class' => 'btn btn-outline-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
