<?php

use common\models\Answer;
use common\models\UserAnswer;
use common\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\YiiAsset;

/** @var $this */
/** @var $question */
/** @var $userTest */

YiiAsset::register($this);

$durationArray = explode(':', $userTest->test->duration);
$totalDurationInSeconds = ($durationArray[0] * 3600) + ($durationArray[1] * 60) + $durationArray[2];
$totalDurationInSeconds = max($totalDurationInSeconds, 0);

$startTime = new DateTime($userTest->start_time);
$currentTime = new DateTime('now');
$elapsedTimeInSeconds = $currentTime->getTimestamp() - $startTime->getTimestamp();
$remainingTimeInSeconds = $totalDurationInSeconds - $elapsedTimeInSeconds;
$remainingTimeInSeconds = max($remainingTimeInSeconds, 0);

$this->registerJs("
    function startTimer(duration, display) {
        var timer = duration, hours, minutes, seconds;
        
        var interval = setInterval(function () {
        
            hours = parseInt(timer / 3600, 10); // Calculate hours
            minutes = parseInt((timer % 3600) / 60, 10); // Calculate minutes
            seconds = parseInt(timer % 60, 10); // Calculate seconds
            
            hours = hours < 10 ? '0' + hours : hours;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            display.textContent = hours + ':' + minutes + ':' + seconds;
            
            if (--timer < 0) {
                timer = 0;
                clearInterval(interval);
                window.location = \"" . Url::to(['site/end', 'id' => $question->id]) . "\";
            }
        }, 1000);
    }

    window.onload = function () {
        var duration = $remainingTimeInSeconds; // Countdown duration in seconds
        var display = document.querySelector('#clock'); // Timer display element
        startTimer(duration, display);
    };
", View::POS_END);
?>

<?= Alert::widget() ?>

<div style="font-size: 24px;
    user-select: none; -webkit-user-select: none; -moz-user-select: none;
    -ms-user-select: none;">

    <?php if ($question->img_path): ?>
        <?= Html::img(Url::to('@web/' . $question->img_path), ['class' => 'w-100']) ?>
    <?php else: ?>
        <?= $question->question; ?>
    <?php endif; ?>

    <br>

    <?php
    $answers = Answer::find()
        ->andWhere(['question_id' => $question->id])
        ->orderBy('RAND()')
        ->all();
    $alphabet = range('A', 'Z');
    $index = 0; ?>

    <form class="mt-5" id="answerForm" action="<?= Url::to(['site/submit']) ?>" method="get">
        <?php
        $userAnswer = UserAnswer::find()
            ->andWhere(['user_id' => Yii::$app->user->id,
                'question_id' => $question->id])->one();
        $selectedAnswerId = $userAnswer ? $userAnswer->answer_id : null; ?>

        <?php foreach ($answers as $a): ?>
            <input type="radio" name="answer_id" value="<?= $a->id ?>"
                   class="form-check-input me-1" style="border: 1px solid black;"
                <?= $selectedAnswerId == $a->id ? 'checked' : '' ?>>
            <?php if ($a->img_path): ?>
                <?= Html::img(Url::to('@web/' . $a->img_path)) ?><br>
            <?php else: ?>
                <?= $a->answer; ?><br>
            <?php endif; ?>
        <?php endforeach; ?>

        <input type="hidden" name="question_id" value="<?= $question->id ?>">

        <button type="submit" class="btn btn-outline-primary mt-5" data-pjax="false">
            <?= Yii::t('app', 'Сақтау') ?>
        </button>
    </form>
</div>
