<?php

namespace frontend\controllers;

use common\models\Answer;
use common\models\Certificate;
use common\models\Course;
use common\models\File;
use common\models\FileType;
use common\models\Question;
use common\models\Result;
use common\models\search\UserSearch;
use common\models\Test;
use common\models\User;
use common\models\UserAnswer;
use common\models\UserSurvey;
use common\models\UserTest;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\SignupForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if(Yii::$app->user->isGuest) {
            return $this->redirect('/site/login');
        }

        if(Yii::$app->user->identity->ssn == 'admin'){
            Yii::$app->session->set('language', 'ru');
            return $this->redirect('/user/index');
        }else{
            $model = User::findOne(Yii::$app->user->id);

            if($model->course_id != null){
                return $this->redirect(['site/course', 'id' => $model->course_id]);
            }

            $searchModel = new UserSearch();
            $searchModel->id = Yii::$app->user->id;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            $courseDP = new ActiveDataProvider([
                'query' => Course::find()->andWhere(['category_id' => $model->category_id]),
            ]);

            return $this->render('index', [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'courseDP' => $courseDP,
            ]);
        }
    }

    public function actionEnroll($id, $type)
    {
        $user = User::findOne(Yii::$app->user->id);

        $fileCount = File::find()
            ->andWhere(['user_id' => $user->id, 'course_id' => $id, 'type' => 'doc'])
            ->count();

        if ($fileCount == 0) {
            $fileTypes = FileType::find()->all();

            foreach ($fileTypes as $fileType) {
                $file = new File();
                $file->user_id = $user->id;
                $file->course_id = $id;
                $file->title = $fileType->title;
                $file->title_ru = $fileType->title_ru;
                $file->file_path = '';
                $file->type = 'doc';
                $file->save(false);
            }
        }

        $files = new ActiveDataProvider([
            'query' => File::find()->andWhere(['course_id' => $id, 'user_id' => $user->id, 'type' => 'doc']),
        ]);

        return $this->render('enroll', [
            'files' => $files,
            'type' => $type,
            'id' => $id,
        ]);
    }

    public function actionCheckEnroll($id, $type){
        $user = User::findOne(Yii::$app->user->id);

        $requiredFilesCount = 5;
        $uploadedFilesCount = File::find()
            ->andWhere(['user_id' => $user->id, 'course_id' => $id, 'type' => 'doc'])
            ->andWhere(['not', ['file_path' => '']]) // Check if file_path is not empty
            ->count();

        $checkboxChecked = Yii::$app->request->get('agreeCheckbox', false);

        if ($type == '2' && !$checkboxChecked) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Келісімшартқа келісіңіз!'));
            return $this->redirect(['site/enroll', 'id' => $id, 'type' => $type]);
        }

        if ($uploadedFilesCount != $requiredFilesCount) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Файлдарды жүктеңіз!'));
            return $this->redirect(['site/enroll', 'id' => $id, 'type' => $type]);
        }

        $user->course_id = $id;
        $user->save(false);

        Yii::$app->session->setFlash('success', 'Циклға тіркелдіңіз!');
        return $this->redirect(['site/course', 'id' => $id]);
    }

    public function actionCourse($id)
    {
        $searchModel = new UserSearch();
        $searchModel->id = Yii::$app->user->id;
        $userDP = $searchModel->search(Yii::$app->request->queryParams);

        $courseDP = new ActiveDataProvider([
            'query' => Course::find()->andWhere(['id' => $id]),
        ]);

        $testsDP = new ActiveDataProvider([
            'query' => Test::find()->andWhere(['course_id' => $id, 'type' => 'test', 'lang' => Yii::$app->language, 'status' => 'public']),
        ]);

        $surveyDP = new ActiveDataProvider([
            'query' => Test::find()->andWhere(['course_id' => $id, 'type' => 'survey', 'lang' => Yii::$app->language, 'status' => 'public']),
        ]);

        $certificatesDP = new ActiveDataProvider([
            'query' => File::find()->andWhere(['user_id' => Yii::$app->user->id, 'course_id' => $id, 'type' => 'cert']),
        ]);

        return $this->render('course', [
            'user' => User::findOne(Yii::$app->user->id),
            'userDP' => $userDP,
            'course' => Course::findOne($id),
            'courseDP' => $courseDP,
            'testsDP' => $testsDP,
            'surveyDP' => $surveyDP,
            'certificatesDP' => $certificatesDP,
        ]);
    }

    public function actionTest($id)
    {
        $question = Question::findOne($id);
        $test = Test::findOne($question->test_id);
        $userTest = UserTest::findOne(['user_id' => Yii::$app->user->id, 'test_id' => $test->id]) ?: new UserTest();

        if(!$userTest->start_time){
            $userTest->user_id = Yii::$app->user->id;
            $userTest->test_id = $test->id;
            $userTest->start_time = date('Y-m-d H:i:s');
            $userTest->save(false);
        }

        return $this->render('/site/test', [
            'question' => $question,
            'userTest' => $userTest,
        ]);
    }

    public function actionSubmit()
    {
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/site/login']);
        }

        $answerId = Yii::$app->request->get('answer_id');
        $questionId = Yii::$app->request->get('question_id');
        $userId = Yii::$app->user->id;

        if ($answerId === null) {
            Yii::$app->session->setFlash('error', 'Жауап таңдалмады!');
            return $this->redirect(['site/test', 'id' => $questionId]);
        }

        $userAnswer = UserAnswer::findOne([
            'user_id' => $userId,
            'question_id' => $questionId,
        ]);

        if (!$userAnswer) {
            $userAnswer = new UserAnswer();
            $userAnswer->user_id = $userId;
            $userAnswer->question_id = $questionId;
        }
        $userAnswer->answer_id = $answerId;
        $userAnswer->save(false);

        $nextQuestion = Question::find()
            ->andWhere(['test_id' => Question::findOne($questionId)->test_id])
            ->andWhere(['>', 'id', $questionId])
            ->orderBy(['id' => SORT_ASC])
            ->one();

        if (!$nextQuestion) {
            $nextQuestion = Question::findOne(['test_id' => Question::findOne($questionId)->test_id]);
        }

        return $this->redirect(['site/test', 'id' => $nextQuestion->id]);
    }

    public function actionEnd($id){
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/site/login']);
        }

        $test = Test::findOne(Question::findOne($id)->test_id);
        $userTest = UserTest::findOne(['user_id' => Yii::$app->user->id, 'test_id' => $test->id]);

        //unanswered questions? return to test
        $now = new DateTime();
        $startTime = new DateTime($userTest->start_time);
        $testDuration = new DateTime($test->duration);

        $h = (int)$testDuration->format('H') * 3600;
        $i = (int)$testDuration->format('i') * 60;
        $s = (int)$testDuration->format('s');

        $durationInSeconds = $h + $i + $s;
        $timeElapsed = $now->getTimestamp() - $startTime->getTimestamp();

        if ($timeElapsed < $durationInSeconds) {
            $totalQuestions = Question::find()
                ->andWhere(['test_id' => $test->id])
                ->count();

            $answeredQuestions = UserAnswer::find()
                ->joinWith('question')
                ->andWhere(['user_answer.user_id' => $userTest->user_id])
                ->andWhere(['question.test_id' => $test->id])
                ->andWhere(['IS NOT', 'user_answer.answer_id', null]) // Ensure it's answered
                ->count();

            if ($answeredQuestions != $totalQuestions) {
                Yii::$app->session->setFlash('warning', Yii::t('app', 'Барлық сұрақтарға жауап беріңіз!'));
                return $this->redirect(['site/test', 'id' => $id]);
            }
        }

        //save end time
        $userTest->end_time = (new \DateTime())->format('Y-m-d H:i:s');
        $userTest->save(false);

        //save results in db
        $questions = Question::find()->andWhere(['test_id' => $test->id])->all();

        $score = 0;
        foreach ($questions as $q) {
            $teacherAnswer = UserAnswer::findOne([
                'user_id' => $userTest->user_id,
                'question_id' => $q->id]);

            if ($teacherAnswer !== null) {;
                if ($teacherAnswer->answer_id == $q->answer) {
                    $score++;
                }
            }
        }

        $userTest->result = $score;
        $userTest->save(false);

        return $this->redirect(['/site/index']);
    }

    public function actionSurvey($id)
    {
        $question = Question::findOne($id);
        $test = Test::findOne($question->test_id);
        $userTest = UserTest::findOne(['user_id' => Yii::$app->user->id, 'test_id' => $test->id]) ?: new UserTest();

        if(!$userTest->start_time){
            $userTest->user_id = Yii::$app->user->id;
            $userTest->test_id = $test->id;
            $userTest->start_time = date('Y-m-d H:i:s');
            $userTest->save(false);
        }

        return $this->render('/site/survey', [
            'question' => $question,
            'userTest' => $userTest,
        ]);
    }

    public function actionSurveySubmit()
    {
        $questionId = Yii::$app->request->get('question_id');
        $answer = Yii::$app->request->get('answer');

        // Find or create a SurveyAnswer model
        $surveyAnswer = UserSurvey::find()
            ->andWhere(['user_id' => Yii::$app->user->id, 'question_id' => $questionId])
            ->one();

        if (!$surveyAnswer) {
            $surveyAnswer = new UserSurvey();
            $surveyAnswer->user_id = Yii::$app->user->id;
            $surveyAnswer->question_id = $questionId;
        }

        $surveyAnswer->answer = $answer;
        $surveyAnswer->save(false);

        $nextQuestion = Question::find()
            ->andWhere(['test_id' => Question::findOne($questionId)->test_id])
            ->andWhere(['>', 'id', $questionId])
            ->orderBy(['id' => SORT_ASC])
            ->one();

        if (!$nextQuestion) {
            $nextQuestion = Question::findOne(['test_id' => Question::findOne($questionId)->test_id]);
        }

        return $this->redirect(['site/survey', 'id' => $nextQuestion->id]); // Example redirect to the next question
    }

    public function actionEndSurvey($id){
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/site/login']);
        }

        $test = Test::findOne(Question::findOne($id)->test_id);
        $userTest = UserTest::findOne(['user_id' => Yii::$app->user->id, 'test_id' => $test->id]);

        $totalQuestions = Question::find()
            ->andWhere(['test_id' => $test->id])
            ->count();

        $answeredQuestions = UserSurvey::find()
            ->joinWith('question')
            ->andWhere(['user_survey.user_id' => $userTest->user_id])
            ->andWhere(['question.test_id' => $test->id])
            ->andWhere(['IS NOT', 'user_survey.answer', null]) // Ensure it's answered
            ->count();

        if ($answeredQuestions != $totalQuestions) {
            Yii::$app->session->setFlash('warning', Yii::t('app', 'Барлық сұрақтарға жауап беріңіз!'));
            return $this->redirect(['site/survey', 'id' => $id]);
        }

        //save end time
        $userTest->end_time = (new \DateTime())->format('Y-m-d H:i:s');
        $userTest->save(false);

        return $this->redirect(['/site/index']);
    }

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
            return $this->redirect(['site/course', 'id' => $model->course_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/site/index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/site/index']);
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(['/site/index']);
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Тіркелу сәтті өтті!'));
            return $this->redirect(['/site/index']);
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionLanguage($view)
    {
        if(Yii::$app->language == 'kz'){
            Yii::$app->session->set('language', 'ru');
        }else{
            Yii::$app->session->set('language', 'kz');
        }
        return $this->redirect([$view]);
    }
}
