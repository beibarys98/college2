<?php

namespace frontend\controllers;

use common\models\Answer;
use common\models\Question;
use common\models\search\AnswerSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * AnswerController implements the CRUD actions for Answer model.
 */
class AnswerController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Answer models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AnswerSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Answer model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Answer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($id)
    {
        $model = new Answer();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                if($model->img_path){
                    unlink($model->img_path);
                    $model->img_path = '';
                    $model->save(false);
                }

                $model->file  = UploadedFile::getInstance($model, 'file');
                if ($model->file) {
                    $filePath = Yii::getAlias('uploads/')
                        . Yii::$app->security->generateRandomString(8)
                        . '.' . $model->file->extension;
                    $model->file->saveAs($filePath);
                    $model->img_path = $filePath;
                }
                $model->question_id = $id;
                $model->save(false);

                $question = $model->question;
                if($question->answer == null){
                    $question->answer = $model->id;
                    $question->save(false);
                }

                return $this->redirect(['test/view', 'id' => $model->question->test_id, 'category_id' => $model->question->test->course->category_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Answer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                if($model->img_path){
                    unlink($model->img_path);
                    $model->img_path = '';
                    $model->save(false);
                }

                $model->file  = UploadedFile::getInstance($model, 'file');
                if ($model->file) {
                    $filePath = Yii::getAlias('uploads/')
                        . Yii::$app->security->generateRandomString(8)
                        . '.' . $model->file->extension;
                    $model->file->saveAs($filePath);
                    $model->img_path = $filePath;
                }
                $model->save(false);

                return $this->redirect(['test/view', 'id' => $model->question->test_id, 'category_id' => $model->question->test->course->category_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Answer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $question = $model->question;
        $test_id = $model->question->test_id;
        $category_id = $model->question->test->course->category_id;
        $model->delete();
        if($question->answer == $id){
            $answer = Answer::findOne(['question_id' => $question->id]);
            $question->answer =  $answer ? $answer->id : null;
            $question->save(false);
        }

        return $this->redirect(['test/view', 'id' => $test_id, 'category_id' => $category_id]);
    }

    /**
     * Finds the Answer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Answer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Answer::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
