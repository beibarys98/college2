<?php

use common\models\Course;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\search\CourseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var $category */

$this->title = (Yii::$app->language == 'kz' ? $category->title : $category->title_ru);
?>
<div class="course-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->identity->ssn === 'admin'): ?>
    <p>
        <?= Html::a(Yii::t('app', 'Добавить'),
            ['create', 'category_id' => $category->id],
            ['class' => 'btn btn-outline-primary']) ?>
    </p>
    <?php endif; ?>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $columns = [];

    $columns[] = [ 'attribute' => 'id', 'headerOptions' => ['style' => 'width: 5%;'], ];

    $columns[] = [
        'attribute' => 'title',
        'label' => Yii::t('app', 'Атауы'),
        'format' => 'raw',
        'value' => function ($model){
            return Yii::$app->user->identity->ssn == 'admin'
                ? Html::a($model->title, [
                    'view',
                    'id' => $model->id,
                    'category_id' => $model->category_id,
                ])
                : Html::a($model->title, [
                    'view2',
                    'id' => $model->id,
                    'category_id' => $model->category_id,
                ]);
        },
    ];

    $columns[] = [
        'attribute' => 'month',
        'label' => Yii::t('app', 'Ай'),
        'value' => function ($model){
            return Yii::t('app', $model->month);
        }
    ];
    $columns[] = [
        'attribute' => 'duration',
        'label' => Yii::t('app', 'Ұзақтығы'),
        'value' => function ($model){
            return Yii::t('app', $model->duration);
        }
    ];

    if (Yii::$app->user->identity->ssn === 'admin') {
        $columns[] = [
            'headerOptions' => ['style' => 'width: 5%;'],
            'class' => ActionColumn::class,
            'template' => '{delete}',
            'urlCreator' => function ($action, Course $model, $key, $index, $column){
                return Url::toRoute([$action, 'id' => $model->id]);
            },
        ];
    }
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped'],
        'pager' => [
            'class' => \yii\bootstrap5\LinkPager::class,
        ],
        'columns' => $columns,
    ]); ?>

    <?php Pjax::end(); ?>

</div>
