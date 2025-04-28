<?php

use common\models\Certificate;
use common\models\Course;
use common\models\Participant;
use common\models\Question;
use common\models\Test;
use common\models\User;
use common\models\UserTest;
use yii\bootstrap5\LinkPager;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var $user */
/** @var $userDP */
/** @var $course */
/** @var $courseDP*/
/** @var $testsDP */
/** @var $surveyDP */
/** @var $certificatesDP */

$this->title = $user->name;

\yii\web\YiiAsset::register($this);
?>
<div class="course-view">

    <h1>
        <?= $this->title; ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $userDP,
        'tableOptions' => ['class' => 'table table-hover'],
        'pager' => [
            'class' => LinkPager::class,
        ],
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width: 5%;'],
            ],
            [
                'attribute' => 'ssn',
                'label' => Yii::t('app', 'ЖСН'),
                'value' => function ($model) {
                    return $model->ssn ?: '';
                }
            ],
            [
                'attribute' => 'telephone',
                'label' => 'Телефон',
                'value' => function ($model) {
                    return $model->telephone ?: '';
                }
            ],
            [
                'attribute' => 'organization',
                'label' => Yii::t('app', 'Мекеме'),
                'value' => function ($model) {
                    return $model->organization ?: '';
                }
            ],
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Yii::t('app', 'Өзгерту'), ['site/update', 'id' => $model->id], ['class' => 'btn btn-outline-primary w-100']);
                },
                'headerOptions' => ['style' => 'width: 10%;'],
            ],
        ],
    ]); ?>

    <br>

    <h1><?= $course->title ?></h1>

    <?= GridView::widget([
        'dataProvider' => $courseDP,
        'summary' => false,
        'tableOptions' => ['class' => 'table table-hover'],
        'columns' => [
            [
                'attribute' => 'month',
                'label' => Yii::t('app', 'Ай'),
                'value' => function ($model){
                    return Yii::t('app', $model->month);
                }
            ],
            [
                'attribute' => 'duration',
                'label' => Yii::t('app', 'Ұзақтығы'),
                'value' => function ($model){
                    return Yii::t('app', $model->duration);
                }
            ],
        ],
    ]) ?>

    <br>

    <h1>Тесты</h1>

    <?= GridView::widget([
        'dataProvider' => $testsDP,
        'tableOptions' => ['class' => 'table table-hover'],
        'pager' => [
            'class' => LinkPager::class,
        ],
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'lang',
                'label' => Yii::t('app', 'Тіл')
            ],
            [
                'attribute' => 'duration',
                'label' => Yii::t('app', 'Длительность')
            ],
            [
                'format' => 'raw',
                'value' => function ($model) {
                    $userTest = UserTest::findOne(['user_id' => Yii::$app->user->id, 'test_id' => $model->id]);
                    $isActive = '';
                    if ($userTest && $userTest->end_time != null) {
                        $isActive = 'disabled';
                    }

                    $firstQuestion = Question::find()->andWhere(['test_id' => $model->id])->one();
                    $firstQuestionId = $firstQuestion ? $firstQuestion->id : null;

                    return Html::a(Yii::t('app', 'Бастау'),
                        ['/site/test', 'id' => $firstQuestionId],
                        [
                            'class' => 'btn btn-outline-primary w-100 ' . $isActive,
                            'data-confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                        ]);
                },
                'headerOptions' => ['style' => 'width: 10%;'],
            ]
        ],
    ]); ?>

    <br>

    <h1>Анкета</h1>

    <?= GridView::widget([
        'dataProvider' => $surveyDP,
        'tableOptions' => ['class' => 'table table-hover'],
        'pager' => [
            'class' => LinkPager::class,
        ],
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'lang',
                'label' => Yii::t('app', 'Тіл')
            ],
            [
                'format' => 'raw',
                'value' => function ($model) {
                    $userTest = UserTest::findOne(['user_id' => Yii::$app->user->id, 'test_id' => $model->id]);
                    $isActive = '';
                    if ($userTest && $userTest->end_time != null) {
                        $isActive = 'disabled';
                    }

                    $firstQuestion = Question::find()->andWhere(['test_id' => $model->id])->one();
                    $firstQuestionId = $firstQuestion ? $firstQuestion->id : null;

                    return Html::a(Yii::t('app', 'Бастау'),
                        ['/site/survey', 'id' => $firstQuestionId],
                        [
                            'class' => 'btn btn-outline-primary w-100 ' . $isActive,
                            'data-confirm' => Yii::t('app', 'Сенімдісіз бе?'),
                        ]);
                },
                'headerOptions' => ['style' => 'width: 10%;'],
            ]
        ],

    ]); ?>

    <br>

    <h1>Сертификаты</h1>

    <?= GridView::widget([
        'dataProvider' => $certificatesDP,
        'tableOptions' => ['class' => 'table table-hover'],
        'pager' => [
            'class' => LinkPager::class,
        ],
        'summary' => false,
        'showHeader' => false,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'width: 5%;'],
            ],
            [
                'attribute' => 'img_path',
                'format' => 'raw',
                'value' => function ($model){
                    return Html::a('certificate_id_' . $model->id,
                        [$model->img_path],
                        ['target' => '_blank']);
                }
            ],
        ],
    ]); ?>
</div>
