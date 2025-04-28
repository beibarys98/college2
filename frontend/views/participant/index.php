<?php

use common\models\Participant;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var common\models\search\ParticipantSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Участники');
?>
<div class="participant-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped'],
        'pager' => [
            'class' => \yii\bootstrap5\LinkPager::class,
        ],
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width: 5%;'],
            ],
            [
                'attribute' => 'course',
                'label' => 'Цикл',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->course ? Html::a($model->course->title, ['/course/view', 'id' => $model->course_id, 'category_id' => $model->course->category_id]) : '';
                },
            ],
            [
                'attribute' => 'name',
                'label' => 'Имя',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->name, ['participant/view', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'ssn',
                'label' => 'ИИН',
                'value' => function ($model) {
                    return $model->user->ssn ?: '';
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
                'attribute' => 'organisation',
                'label' => 'Организация',
                'value' => function ($model) {
                    return $model->organisation ?: '';
                }
            ],
            [
                'headerOptions' => ['style' => 'width: 5%;'],
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action, Participant $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
