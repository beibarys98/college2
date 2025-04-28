<?php

use common\models\Course;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var $files */
/** @var $type */
/** @var $id */

$course = Course::findOne($id);
$category_id = $course->category_id;

$this->title = $course->title;
?>
<div class="course-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="text-center">
        <?= Html::a(Yii::t('app', 'Бюджет негізінде'), ['site/enroll', 'id' => $id, 'type' => '1'], ['class' => $type == '1' ? 'btn btn-primary' : 'btn btn-outline-primary']) ?>
        <?= Html::a(Yii::t('app', 'Келісім шарт негізінде'), ['site/enroll', 'id' => $id, 'type' => '2'], ['class' => $type == '2' ? 'btn btn-primary' : 'btn btn-outline-primary'])?>
    </div>

    <?php $form = yii\widgets\ActiveForm::begin([
        'action' => ['site/check-enroll', 'id' => $id, 'type' => $type],
        'method' => 'get', // Use GET or POST depending on your needs
    ]); ?>

    <?php if ($type == '2'): ?>
        <hr>
        <div class="text-center">
            <iframe
                    src="<?= Yii::getAlias('@web') ?>/uploads/example.pdf#toolbar=0&navpanes=0&scrollbar=0"
                    width="600px"
                    height="600px"
                    style="border: none;">
            </iframe>
        </div>
        <hr>
        <div class="d-flex justify-content-center">
            <div class="form-check" style="font-size: 1.5rem;">
                <?= Html::checkbox('agreeCheckbox', false, [
                    'id' => 'agreeCheckbox',
                    'style' => 'transform: scale(1.5); margin-right: 10px;',
                ]) ?>
                <label class="form-check-label" for="agreeCheckbox">
                    <?= Yii::t('app', 'Мен осы келісімшартпен таныстым және келісемін!') ?>
                </label>
            </div>
        </div>
        <hr>
    <?php endif; ?>

    <div class="mt-5">
        <?= GridView::widget([
            'dataProvider' => $files,
            'tableOptions' => ['class' => 'table table-hover'],
            'pager' => [
                'class' => \yii\bootstrap5\LinkPager::class,
            ],
            'showHeader' => false,
            'summary' => false,
            'rowOptions' => function ($model, $key, $index, $grid) use ($type) {
                if ($index < 5) {
                    return [];
                }

                if ($index === 5 && $type == '1') {
                    return ['class' => 'row-6'];
                } elseif ($index === 6 && $type == '2') {
                    return ['class' => 'row-7'];
                }

                return ['style' => 'display: none'];
            },
            'columns' => [
                [
                    'format' => 'raw',
                    'value' => function($model){
                        return Yii::$app->language == 'kz' ? $model->title : $model->title_ru;
                    }
                ],
                [
                    'headerOptions' => ['style' => 'width: 20%;'],
                    'format' => 'raw',
                    'value' => function($model) {
                        if (!$model->file_path) {
                            return '---';
                        }

                        $fileName = basename($model->file_path); // get the last part of the path
                        $url = Yii::getAlias('@web') . 'enroll.php/' . ltrim($model->file_path, '/'); // ensure proper URL

                        return Html::a($fileName, $url, ['target' => '_blank']);
                    }
                ],
                [
                    'format' => 'raw',
                    'value' => function($model) use ($type){
                        return Html::a(Yii::t('app', 'Жүктеу'), ['file/update', 'id' => $model->id, 'type' => $type], ['class' => 'btn btn-outline-primary']);
                    }
                ]
            ],

        ]); ?>
    </div>

    <div class="text-center">
        <?= Html::submitButton(Yii::t('app', 'Жазылу'), [
            'class' => 'btn btn-outline-primary'
        ]) ?>
    </div>

    <?php yii\widgets\ActiveForm::end(); ?>

</div>

