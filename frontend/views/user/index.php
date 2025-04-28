<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var common\models\search\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Участники';
?>
<div class="user-index">

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
                'attribute' => 'ssn',
                'label' => 'ИИН',
                'value' => function ($model) {
                    return $model->ssn ?: '';
                }
            ],
            [
                'attribute' => 'name',
                'label' => 'Имя',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->name, ['user/view', 'id' => $model->id]);
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
                'template' => '{delete}',
                'urlCreator' => function ($action, User $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
