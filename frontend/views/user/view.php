<?php

use common\models\Participant;
use common\models\User;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var $model */
/** @var $dataProvider */

$this->title = $model->name;
\yii\web\YiiAsset::register($this);
?>
<div class="participant-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped'],
        'pager' => [
            'class' => \yii\bootstrap5\LinkPager::class,
        ],
        'summary' => false,
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
                'attribute' => 'ssn',
                'label' => 'ИИН',
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
                'label' => 'Организация',
                'value' => function ($model) {
                    return $model->organization ?: '';
                }
            ],
            [
                'headerOptions' => ['style' => 'width: 5%;'],
                'class' => ActionColumn::className(),
                'template' => '{update}',
                'urlCreator' => function ($action, User $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id, 'category_id' => $model->course ? $model->course->category_id : '']);
                }
            ],
        ],
    ]); ?>

</div>
