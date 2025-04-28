<?php

use common\models\UserSurvey;
use common\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;

/** @var $this */
/** @var $question */

YiiAsset::register($this);

?>

<?= Alert::widget() ?>

<div style="font-size: 24px; user-select: none; -webkit-user-select: none;
    -moz-user-select: none; -ms-user-select: none;">

    <?php if ($question->img_path): ?>
        <?= Html::img(Url::to('@web/' . $question->img_path), ['class' => 'w-100']) ?>
    <?php else: ?>
        <?= $question->question; ?>
    <?php endif; ?>

    <br>

    <form class="mt-5" id="surveyForm" action="<?= Url::to(['site/survey-submit']) ?>" method="get">
        <?php
        $userSurveyAnswer = UserSurvey::find()
            ->andWhere(['user_id' => Yii::$app->user->id, 'question_id' => $question->id])
            ->one();
        $previousAnswer = $userSurveyAnswer ? $userSurveyAnswer->answer : '';
        ?>

        <textarea name="answer" rows="4" class="form-control" placeholder="<?= Yii::t('app', 'Жауабыңыз') ?>" required><?= Html::encode($previousAnswer) ?></textarea>

        <input type="hidden" name="question_id" value="<?= $question->id ?>">

        <button type="submit" class="btn btn-outline-primary mt-5" data-pjax="false">
            <?= Yii::t('app', 'Сақтау') ?>
        </button>
    </form>
</div>


