<?php

use common\models\Course;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Participant $model */
/** @var $model2 */

$this->title = Yii::t('app', 'Изменить участника');
?>
<div class="participant-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?php if(Yii::$app->user->identity->ssn == 'admin'): ?>
        <?= $form->field($model, 'course_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Course::find()->all(), 'id', 'title'),
            'options' => [
                'placeholder' => 'Выберите цикл',
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ])->label(false) ?>
    <?php endif; ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Имя'])->label(false) ?>

    <?= $form->field($model, 'ssn')->textInput(['placeholder' => 'ИИН'])->label(false) ?>

    <?= $form->field($model, 'telephone')->textInput(['maxlength' => true, 'placeholder' => 'Телефон'])->label(false) ?>

    <?= $form->field($model, 'organization')->textInput(['maxlength' => true, 'placeholder' => 'Организация'])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Изменить'), ['class' => 'btn btn-outline-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>