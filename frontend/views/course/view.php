<?php

use common\models\Certificate;
use common\models\Course;
use common\models\Participant;
use common\models\Test;
use common\models\User;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var $model */
/** @var $dataProvider */
/** @var $participantsDP */
/** @var $participantsSM */
/** @var $certificatesDP */
/** @var $testsDP */
/** @var $surveyDP */

$this->title = $model->title;

\yii\web\YiiAsset::register($this);
?>
<div class="course-view">

    <h1><?= $this->title ?></h1>

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
                'headerOptions' => ['style' => 'width: 5%;'],
                'class' => ActionColumn::className(),
                'template' => '{update}',
                'urlCreator' => function ($action, Course $model, $key, $index, $column){
                    if ($action === 'update') {
                        return Url::toRoute(['update', 'id' => $model->id, 'category_id' => $model->category_id]); // Custom update URL
                    }
                    return Url::toRoute([$action, 'id' => $model->id]); // Default for other actions
                }
            ]
        ],
    ]) ?>

    <br>

    <h1>Участники</h1>
    <p>
        <?= Html::a(Yii::t('app', 'Добавить'), ['user/create2', 'course_id' => $model->id, 'category_id' => $model->category_id], ['class' => 'btn btn-outline-primary']) ?>
        <?= Html::a(Yii::t('app', 'Добавить из excel'), ['user/create', 'course_id' => $model->id, 'category_id' => $model->category_id], ['class' => 'btn btn-outline-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $participantsDP,
        'filterModel' => $participantsSM,
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
                'attribute' => 'name',
                'label' => 'Имя',
                'format' => 'raw',
                'value' => function ($model){
                    return Html::a($model->name, ['user/view', 'id' => $model->id, 'category_id' => $model->course->category_id]);
                }
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
                'template' => '{delete}',
                'urlCreator' => function ($action, User $model){
                    return Url::toRoute(['user/delete', 'id' => $model->id, 'course_id' => $model->course_id]);
                }
            ],
        ],
    ]); ?>

    <br>

    <?php if($model->category->type != 'sem'): ?>

    <h1>Тесты</h1>
    <p>
        <?= Html::a(Yii::t('app', 'Добавить'), ['test/create', 'course_id' => $model->id, 'type' => 'test', 'category_id' => $model->category_id], ['class' => 'btn btn-outline-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $testsDP,
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
                'attribute' => 'title',
                'label' => 'Название',
                'format' => 'raw',
                'value' => function ($model){
                    return Html::a('test_id_' . $model->id, ['test/view', 'id' => $model->id, 'category_id' => $model->course->category_id]);
                },
            ],
            [
                'attribute' => 'lang',
                'label' => 'Язык'
            ],
            [
                'attribute' => 'status',
                'label' => 'Статус'
            ],
            [
                'attribute' => 'duration',
                'label' => 'Длительность'
            ],
            [
                'headerOptions' => ['style' => 'width: 5%;'],
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action, Test $model){
                    return Url::toRoute(['test/delete', 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <br>

    <h1>Анкета</h1>
    <p>
        <?= Html::a(Yii::t('app', 'Добавить'),
            ['test/create', 'course_id' => $model->id, 'type' => 'survey', 'category_id' => $model->category_id],
            ['class' => 'btn btn-outline-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $surveyDP,
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
                'attribute' => 'title',
                'label' => 'Название',
                'format' => 'raw',
                'value' => function ($model){
                    return Html::a('survey_id_' . $model->id, ['test/view', 'id' => $model->id, 'category_id' => $model->course->category_id]);
                }
            ],
            [
                'attribute' => 'lang',
                'label' => 'Язык'
            ],
            [
                'attribute' => 'status',
                'label' => 'Статус'
            ],
            [
                'headerOptions' => ['style' => 'width: 5%;'],
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action, Test $model, $key, $index, $column) {
                    return Url::toRoute(['test/delete', 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <br>

    <?php endif; ?>

    <h1>Сертификаты</h1>
    <p>
        <?= Html::a(Yii::t('app', 'Добавить'), ['certificate/create', 'course_id' => $model->id, 'category_id' => $model->category_id], ['class' => 'btn btn-outline-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $certificatesDP,
        'tableOptions' => ['class' => 'table table-striped'],
        'pager' => [
            'class' => \yii\bootstrap5\LinkPager::class,
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
                    return Html::a('certificate_id_' . $model->id, [$model->img_path], ['target' => '_blank']);
                }
            ],
            [
                'headerOptions' => ['style' => 'width: 5%;'],
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action, Certificate $model, $key, $index, $column) {
                    return Url::toRoute(['certificate/delete', 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>
</div>
