<?php

namespace frontend\controllers;

use common\models\Course;
use common\models\Participant;
use common\models\search\ParticipantSearch;
use common\models\User;
use common\models\search\UserSearch;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $dataProvider->pagination->pageSize = 100;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchModel = new UserSearch();
        $searchModel->id = $id; // Pre-filter by participant ID
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($course_id)
    {
        $model = new User();


        if ($this->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file) {
                $filePath = Yii::getAlias('@webroot/uploads/') . $model->file->name;

                if ($model->file->saveAs($filePath)) {
                    $spreadsheet = IOFactory::load($filePath);
                    $sheet = $spreadsheet->getActiveSheet();
                    $rows = $sheet->toArray();

                    foreach ($rows as $row) {
                        $user = new User();
                        $user->category_id = Course::findOne($course_id)->category_id;
                        $user->course_id = $course_id;
                        $user->ssn = null;
                        $user->name = trim($row[0]);
                        $user->telephone = isset($row[1]) ? trim($row[1]) : '';
                        $user->organization = isset($row[2]) ? trim($row[2]) : '';

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
        $model = new User();

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->category_id = Course::findOne($course_id)->category_id;
            $model->course_id = $course_id;

            $model->password = Yii::$app->security->generatePasswordHash('password');
            $model->generateAuthKey();

            if ($model->validate()) {
                $model->save(false);
            }else{
                return $this->render('create2', [
                    'model' => $model,
                ]);
            }

            return $this->redirect(['course/view', 'id' => $course_id, 'category_id' => $model->course->category_id]);
        }

        return $this->render('create2', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = User::findOne($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            if ($model->validate()) {
                $model->save(false);
            }else{
                return $this->render('update', [
                    'model' => $model,
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
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $course_id = null)
    {
        $this->findModel($id)->delete();

        if (!empty($course_id)) {
            return $this->redirect(['course/view', 'id' => $course_id, 'category_id' => Course::findOne($course_id)->category_id]);
        }else{
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
