<?php

use common\models\Question;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Question $model */
/** @var $dataProvider */

$this->title = 'question_id_' . $model->id;
\yii\web\YiiAsset::register($this);
?>
<div class="question-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'tableOptions' => ['class' => 'table table-striped'],
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width: 5%;'],
            ],
            [
                'attribute' => 'test_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('test_id_' . $model->test_id, Url::to(['test/view', 'id' => $model->test_id, 'category_id' => $model]));
                }
            ],
            'question:ntext',
            'answer',
            'img_path',
            [
                'headerOptions' => ['style' => 'width: 5%;'],
                'class' => ActionColumn::className(),
                'template' => '{update}',
                'urlCreator' => function ($action, Question $model, $key, $index, $column){
                    return Url::toRoute([$action, 'id' => $model->id, 'category_id' => $model->test->course->category_id]); // Default for other actions
                }
            ]
        ],
    ]); ?>

</div>
