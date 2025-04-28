<?php

namespace frontend\controllers;

use common\models\Course;
use common\models\Participant;
use common\models\search\ParticipantSearch;
use common\models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class ParticipantController extends Controller
{
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

    public function actionIndex()
    {
        $searchModel = new ParticipantSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $dataProvider->pagination->pageSize = 100;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $searchModel = new ParticipantSearch();
        $searchModel->id = $id; // Pre-filter by participant ID
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate($course_id)
    {
        $model = new Participant();

        if ($this->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file) {
                $filePath = Yii::getAlias('@webroot/uploads/') . $model->file->name;
                if ($model->file->saveAs($filePath)) {
                    $spreadsheet = IOFactory::load($filePath);
                    $sheet = $spreadsheet->getActiveSheet();
                    $rows = $sheet->toArray();

                    foreach ($rows as $row) {
                        $participant = new Participant();
                        $participant->course_id = $course_id;
                        $participant->name = trim($row[0]);

                        $participant->telephone = isset($row[1]) ? trim($row[1]) : '';
                        $participant->organisation = isset($row[2]) ? trim($row[2]) : '';

                        $participant->save(false);

                        $user = new User();
                        $user->participant_id = $participant->id;
                        $user->ssn = null;
                        $user->password = Yii::$app->security->generatePasswordHash('password');
                        $user->generateAuthKey();
                        $user->save(false);
                    }

                    unlink($filePath);

                    $model = Course::findOne($course_id);
                    $category_id = $model->category_id;

                    return $this->redirect(['course/view', 'id' => $course_id, 'category_id' => $category_id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionCreate2($course_id){
        $model = new Participant();
        $model2 = new User();

        if ($this->request->isPost && $model->load($this->request->post()) && $model2->load($this->request->post())) {
            $model->course_id = $course_id;


            $model2->password = Yii::$app->security->generatePasswordHash('password');
            $model2->generateAuthKey();
            if ($model->validate() && $model2->validate()) {
                $model->save(false);
                $model2->participant_id = $model->id;
                $model2->save(false);
            }else{
                return $this->render('create2', [
                    'model' => $model,
                    'model2' => $model2,
                ]);
            }

            $model = Course::findOne($course_id);
            $category_id = $model->category_id;

            return $this->redirect(['course/view', 'id' => $course_id, 'category_id' => $category_id]);
        }

        return $this->render('create2', [
            'model' => $model,
            'model2' => $model2,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model2 = User::findOne(['participant_id' => $id]);

        if ($this->request->isPost && $model->load($this->request->post()) && $model2->load($this->request->post())) {
            if ($model->validate() && $model2->validate()) {
                $model->save(false);
                $model2->save(false);
            }else{
                return $this->render('update', [
                    'model' => $model,
                    'model2' => $model2,
                ]);
            }
            if(Yii::$app->user->identity->ssn == 'admin'){
                return $this->redirect(['view', 'id' => $model->id, 'category_id' => $model->course->category_id]);
            }else{
                return $this->redirect(['site/index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'model2' => $model2,
        ]);
    }

    public function actionDelete($id, $course_id = null)
    {
        $user = User::findOne(['participant_id' => $id]);
        $user->delete();
        $this->findModel($id)->delete();

        if (!empty($course_id)) {
            return $this->redirect(['course/view', 'id' => $course_id, 'category_id' => Course::findOne($course_id)->category_id]);
        }else{
            return $this->redirect(['index']);
        }
    }

    protected function findModel($id)
    {
        if (($model = Participant::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
