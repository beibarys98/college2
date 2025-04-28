<?php

namespace frontend\controllers;

use common\models\Category;
use common\models\Certificate;
use common\models\Course;
use common\models\File;
use common\models\FileType;
use common\models\Participant;
use common\models\search\CourseSearch;
use common\models\search\ParticipantSearch;
use common\models\search\UserSearch;
use common\models\Test;
use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class CourseController extends Controller
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

    public function actionIndex($category_id)
    {
        $searchModel = new CourseSearch();
        $queryParams = $this->request->queryParams;
        $queryParams['CourseSearch']['category_id'] = $category_id;
        $dataProvider = $searchModel->search($queryParams);

        $category = Category::findOne($category_id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'category' => $category,
        ]);
    }

    public function actionView($id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Course::find()->andWhere(['id' => $id]),
        ]);

        $participantsSM = new UserSearch();
        $queryParams = $this->request->queryParams;
        $queryParams['UserSearch']['course_id'] = $id;
        $participantsDP = $participantsSM->search($queryParams);

        $certificatesDP = new ActiveDataProvider([
            'query' => Certificate::find()->andWhere(['course_id' => $id]),
        ]);

        $testsDP = new ActiveDataProvider([
            'query' => Test::find()->andWhere(['course_id' => $id, 'type' => 'test']),
        ]);

        $surveyDP = new ActiveDataProvider([
            'query' => Test::find()->andWhere(['course_id' => $id, 'type' => 'survey']),
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
            'participantsDP' => $participantsDP,
            'participantsSM' => $participantsSM,
            'certificatesDP' => $certificatesDP,
            'testsDP' => $testsDP,
            'surveyDP' => $surveyDP,
        ]);
    }

    public function actionCreate($category_id)
    {
        $model = new Course();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->category_id = $category_id;
                $model->save();
                return $this->redirect(['course/view', 'id' => $model->id, 'category_id' => $category_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        $category = Category::findOne($category_id);

        return $this->render('create', [
            'model' => $model,
            'type' => $category->type,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $id, 'category_id' => $model->category_id]);
        }

        return $this->render('update', [
            'model' => $model,
            'type' => $model->category->type,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $category_id = $model->category_id;
        $model->delete();

        return $this->redirect(['index', 'category_id' => $category_id]);
    }

    protected function findModel($id)
    {
        if (($model = Course::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
