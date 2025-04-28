<?php

/** @var yii\web\View $this */
/** @var $model */
/** @var $dataProvider */
/** @var $courseDP */

use common\models\User;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->name;
?>
<div class="site-index">
    <h1>
        <?= $this->title; ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover'],
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
                    return Html::a(Yii::t('app', 'Өзгерту'), ['user/update', 'id' => $model->id], ['class' => 'btn btn-outline-primary w-100']);
                },
                'headerOptions' => ['style' => 'width: 10%;'],
            ],
        ],
    ]); ?>

    <br>

    <h1><?= Yii::$app->language == 'kz' ? $model->category->title : $model->category->title_ru ?></h1>

    <?= GridView::widget([
        'dataProvider' => $courseDP,
        'summary' => false,
        'tableOptions' => ['class' => 'table table-hover'],
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width: 5%;'],
            ],
            [
                'attribute' => 'title',
                'label' => Yii::t('app', 'Атауы'),
            ],
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
            [
                'headerOptions' => ['style' => 'width: 10%;'],
                'format' => 'raw',
                'value' => function ($model){
                    return Html::a(Yii::t('app', 'Жазылу') , ['site/enroll', 'id' => $model->id, 'type' => '1'], ['class' => 'btn btn-outline-primary w-100']);
                }
            ]
        ],
    ]) ?>
</div>
