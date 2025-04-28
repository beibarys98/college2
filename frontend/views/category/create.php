<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Category $model */

$this->title = Yii::t('app', 'Добавить категорию');
?>
<div class="category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => 'Атауы (kz)'])->label(false) ?>

    <?= $form->field($model, 'title_ru')->textInput(['maxlength' => true, 'placeholder' => 'Название (ru)'])->label(false) ?>

    <?= $form->field($model, 'type')->dropDownList([
        'pov' => 'Повышение квалификации',
        'cert' => 'Сертификационный курс',
        'sem' => 'Семинары'
    ], ['prompt' => 'Тип'])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Добавить'), ['class' => 'btn btn-outline-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
