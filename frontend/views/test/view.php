<?php

use common\models\Answer;
use common\models\Question;
use common\models\Test;
use common\models\UserTest;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Test $model */
/** @var $dataProvider */

$this->title = $model->type == 'test' ? 'test_id_' . $model->id : 'survey_id_' . $model->id;
\yii\web\YiiAsset::register($this);
?>
<div class="test-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    $columns = [];

    $columns[] = [
        'attribute' => 'id',
        'headerOptions' => ['style' => 'width: 5%;'],
    ];

    $columns[] = [
        'attribute' => 'course_id',
        'label' => 'Цикл',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::a(
                $model->course->title,
                ['course/view', 'id' => $model->course->id, 'category_id' => $model->course->category_id]
            );
        }
    ];

    $columns[] = [
        'attribute' => 'lang',
        'label' => 'Язык',
    ];

    $columns[] = [
        'attribute' => 'status',
        'label' => 'Статус',
    ];

    if($model->type == 'test'){
        $columns[] = [
            'attribute' => 'duration',
            'label' => 'Длительность',
        ];
    }

    $columns[] = [
        'headerOptions' => ['style' => 'width: 5%;'],
        'class' => ActionColumn::class,
        'template' => '{update}',
        'urlCreator' => function ($action, $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->id, 'category_id' => $model->course->category_id]);
        }
    ];
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'tableOptions' => ['class' => 'table table-hover'],
        'columns' => $columns,
    ]); ?>

    <hr>
    <div>
        <?= Html::a('новый',
            ['test/new', 'id' => $model->id, 'category_id' => $model->course->category_id],
            ['class' => $model->status == 'new' ? 'btn btn-primary' : 'btn btn-outline-primary']) ?>
        <?= Html::a('готово',
            ['test/ready', 'id' => $model->id, 'category_id' => $model->course->category_id],
            ['class' => $model->status == 'ready' ? 'btn btn-primary' : 'btn btn-outline-primary']) ?>
        <?= Html::a('опубликовать',
            ['test/publish', 'id' => $model->id, 'category_id' => $model->course->category_id],
            ['class' => $model->status == 'public' ? 'btn btn-primary' : 'btn btn-outline-primary']) ?>
        <?= Html::a('закончить',
            ['test/end', 'id' => $model->id, 'category_id' => $model->course->category_id],
            ['class' => $model->status == 'finished' ? 'btn btn-primary' : 'btn btn-outline-primary']) ?>
        <?= Html::a('наградить',
            ['test/certificate', 'id' => $model->id, 'category_id' => $model->course->category_id],
            ['class' => $model->status == 'certificated' ? 'btn btn-primary' : 'btn btn-outline-primary']) ?>
    </div>
    <hr>
    <div>
        <?= Html::a('тест',
            ['test/view', 'id' => $model->id, 'mode' => 'test', 'category_id' => $model->course->category_id],
            ['class' => $mode == 'test' ? 'btn btn-primary' : 'btn btn-outline-primary']) ?>
        <?= Html::a('участники',
            ['test/view', 'id' => $model->id, 'mode' => 'prt', 'category_id' => $model->course->category_id],
            ['class' => $mode == 'prt' ? 'btn btn-primary' : 'btn btn-outline-primary']) ?>
    </div>
    <hr>

    <?php if($mode == 'test'): ?>
    <div style="font-size: 20px;">
        <?php
        $questions = Question::find()->andWhere(['test_id' => $model->id])->all();

        foreach ($questions as $index => $question) {
            echo Html::a('_/',
                    ['question/update', 'id' => $question->id, 'category_id' => $model->course->category_id],
                    ['class' => 'btn btn-sm btn-outline-primary']) . ' ';
            echo Html::a('Х',
                    ['question/delete', 'id' => $question->id],
                    [
                        'class' => 'btn btn-sm btn-outline-danger',
                        'data' => [
                            'confirm' => Yii::t('app', 'Вы уверены?'),
                            'method' => 'post',
                        ],
                    ]) . ' ';
            echo $index + 1 . '. ';
            if($question->img_path){
                echo Html::img(Yii::getAlias('@web/') . $question->img_path, ['style' => 'max-width: 80%; padding: 10px;']) . '<br>';
            }else{
                echo $question->question . '<br>';
            }
            $answers = Answer::find()->andWhere(['question_id' => $question->id])->all();
            $alphabet = range('A', 'Z');
            foreach ($answers as $index2 => $answer) {
                if($index2 == 0){
                    echo '<span style="margin: 15px;"></span>'
                        . Html::a('_/',
                            ['answer/update', 'id' => $answer->id, 'category_id' => $model->course->category_id],
                            ['class' => 'btn btn-sm btn-outline-primary']) . ' ';
                    echo Html::a('Х',
                            ['answer/delete', 'id' => $answer->id],
                            [
                                'class' => 'btn btn-sm btn-outline-danger',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Вы уверены?'),
                                    'method' => 'post',
                                ],
                            ]) . ' ';
                    echo '<strong>' . $alphabet[$index2] . '. ' .'</strong>';
                    if($answer->img_path){
                        echo Html::img(Yii::getAlias('@web/') . $answer->img_path, ['style' => 'max-width: 80%; padding: 10px;']) . '<br>';
                    }else{
                        echo '<strong>' . $answer->answer . '<br>' .'</strong>';
                    }
                }else{
                    echo '<span style="margin: 15px;"></span>'
                        . Html::a('_/',
                            ['answer/update', 'id' => $answer->id, 'category_id' => $model->course->category_id],
                            ['class' => 'btn btn-sm btn-outline-primary']) . ' ';
                    echo Html::a('Х',
                            ['answer/delete', 'id' => $answer->id],
                            [
                                'class' => 'btn btn-sm btn-outline-danger',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ]) . ' ';
                    echo $alphabet[$index2] . '. ';
                    if($answer->img_path){
                        echo Html::img(Yii::getAlias('@web/') . $answer->img_path, ['style' => 'max-width: 80%; padding: 10px;']) . '<br>';
                    }else{
                        echo $answer->answer . '<br>';
                    }
                }
            }
            if($model->type == 'test'){
                echo '<span style="margin: 15px;"></span>'
                    . Html::a('+ ответ',
                        ['answer/create', 'id' => $question->id, 'category_id' => $model->course->category_id],
                        ['class' => 'btn btn-sm btn-outline-primary']) . '<br>';
            }

        }
        echo Html::a('+ вопрос',
                ['question/create', 'id' => $model->id, 'category_id' => $model->course->category_id],
                ['class' => 'btn btn-sm btn-outline-primary']) . '<br>';
        ?>
    </div>
    <?php else: ?>
    <div>
        <?= GridView::widget([
            'dataProvider' => $dataProvider2,
            'filterModel' => $searchModel2,
            'tableOptions' => ['class' => 'table table-hover'],
            'pager' => [
                'class' => \yii\bootstrap5\LinkPager::class,
            ],
            'columns' => [
                [
                    'attribute' => 'id',
                    'headerOptions' => ['style' => 'width: 5%;'],
                ],
                [
                    'attribute' => 'user',
                    'format' => 'raw',
                    'value' => function ($model){
                        return Html::a($model->user->name, ['user/view', 'id' => $model->user->id, 'category_id' => $model->user->course->category_id]);
                    }
                ],
                'start_time',
                'end_time',
                'result',
            ],
        ]); ?>
    </div>
    <?php endif; ?>


</div>
