<?php

use common\models\Category;
use common\models\Place;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var $categoryDP */
/** @var $categorySM */

$this->title = Yii::t('app', 'Места');
?>
<div class="place-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Добавить'),
            ['create'],
            ['class' => 'btn btn-outline-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped'],
        'pager' => [
            'class' => \yii\bootstrap5\LinkPager::class,
        ],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'width: 5%;'],
            ],
            [
                'attribute' => 'score',
                'label' => 'Баллы [>=]'
            ],
            [
                'headerOptions' => ['style' => 'width: 5%;'],
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action, Place $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <br>

    <h1>Категорий</h1>

    <p>
        <?= Html::a(Yii::t('app', 'Добавить'), ['category/create'], ['class' => 'btn btn-outline-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $categoryDP,
        'filterModel' => $categorySM,
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
                'attribute' => 'title_ru',
                'label' => 'Название',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->title_ru, ['category/update', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'title',
                'label' => 'Атауы'
            ],
            [
                'attribute' => 'type',
                'label' => 'Тип'
            ],
            [
                'headerOptions' => ['style' => 'width: 5%;'],
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action, Category $model, $key, $index, $column){
                    return Url::toRoute(['category/delete', 'id' => $model->id]); // Default for other actions
                }
            ]
        ],
    ]); ?>

</div>
