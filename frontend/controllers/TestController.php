<?php

namespace frontend\controllers;

use common\models\Answer;
use common\models\Question;
use common\models\search\UserTestSearch;
use common\models\Test;
use common\models\search\TestSearch;
use common\models\UserTest;
use PhpOffice\PhpWord\IOFactory;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class TestController extends Controller
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
        $searchModel = new TestSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id, $mode = 'prt')
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Test::find()->andWhere(['id' => $id]),
        ]);

        $searchModel2 = new UserTestSearch();
        $queryParams = $this->request->queryParams;
        $queryParams['test_id'] = $id;
        $dataProvider2 = $searchModel2->search($queryParams);


        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
            'mode' => $mode,
            'dataProvider2' => $dataProvider2,
            'searchModel2' => $searchModel2,
        ]);
    }

    public function actionCreate($course_id, $type)
    {
        $model = new Test();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                $model->file  = UploadedFile::getInstance($model, 'file');
                if ($model->file) {
                    $filePath = Yii::getAlias('@webroot/uploads/')
                        . Yii::$app->security->generateRandomString(8)
                        . '.' . $model->file->extension;

                    $model->file->saveAs($filePath);

                    $model->course_id = $course_id;
                    $model->type = $type;
                    $model->status = 'new';
                    $model->save(false);

                    if($type == 'survey'){
                        $this->parseSurvey($filePath, $model->id);
                    }else{
                        $this->parse($filePath, $model->id);
                    }

                    unlink($filePath);

                    return $this->redirect(['view', 'id' => $model->id, 'category_id' => $model->course->category_id]);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'type' => $type
        ]);
    }

    private function parseSurvey($filePath, $survey_id)
    {
        $phpWord = IOFactory::load($filePath);

        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }

        $lines = preg_split('/\r\n|\r|\n/', $text);

        $currentQuestionText = '';

        foreach ($lines as $line) {
            $line = trim($line);

            // Detect start of a new question
            if (preg_match('/^\s*\d+\s*[.)]\s*(.*)/', $line, $match)) {
                if ($currentQuestionText !== '') {
                    $this->saveSurveyQuestion($currentQuestionText, $survey_id);
                }

                $currentQuestionText = $match[1];
            } else {
                // Continuation of question text
                $currentQuestionText .= "\n" . $line;
            }
        }

        // Save the last question
        if ($currentQuestionText !== '') {
            $this->saveSurveyQuestion($currentQuestionText, $survey_id);
        }
    }

    private function saveSurveyQuestion($questionText, $survey_id)
    {
        $question = new Question();
        $question->test_id = $survey_id;
        $question->question = trim($questionText);
        $question->save(false);
    }

    private function parse($filePath, $test_id)
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) === true) {
            $xmlContent = $zip->getFromName('word/document.xml');
            $zip->close();

            // Remove MathML (entire <m:oMath> blocks)
            $xmlContent = preg_replace('/<m:oMath[^>]*>.*?<\/m:oMath>/s', '', $xmlContent);

            // Load the corrected XML into DOMDocument
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true); // Prevent warnings
            $dom->loadXML($xmlContent);
            libxml_clear_errors();

            // Extract text (ignoring formulas and images)
            $paragraphs = $dom->getElementsByTagName('p');
            $text = '';
            foreach ($paragraphs as $p) {
                $text .= trim($p->textContent) . "\n";
            }

            // Call function to process extracted text
            $this->processText($text, $test_id);
        } else {
            throw new \Exception('Failed to open the .docx file.');
        }
    }

    private function processText($text, $test_id)
    {
        $lines = explode("\n", $text);
        $currentQuestionText = '';
        $answers = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Check if the line is an answer
            if (preg_match('/^[\p{Latin}\p{Cyrillic}]\s*[.)]\s*(.*)/u', $line, $match)) {
                $answers[] = trim($match[1]);
            }
            // Check if the line is a question number
            elseif (preg_match('/^\s*\d+\s*[.)]\s*(.*)/', $line, $match)) {
                if ($currentQuestionText !== '' && !empty($answers)) {
                    $this->saveQuestion($currentQuestionText, $answers, $test_id);
                }
                // Start a new question
                $currentQuestionText = $match[1];
                $answers = [];
            }
            // Otherwise, it's part of the question text
            else {
                $currentQuestionText .= "\n" . $line;
            }
        }

        // Save the last question
        if ($currentQuestionText !== '' && !empty($answers)) {
            $this->saveQuestion($currentQuestionText, $answers, $test_id);
        }
    }

    private function saveQuestion($questionText, $answers, $test_id)
    {
        $question = new Question();
        $question->test_id = $test_id;
        $question->question = trim($questionText);
        $question->save(false);

        $firstAnswerId = null; // Store the first answer's ID

        foreach ($answers as $index => $ansText) {
            $answer = new Answer();
            $answer->question_id = $question->id;
            $answer->answer = trim($ansText);
            $answer->save(false);

            if ($index === 0) {
                $firstAnswerId = $answer->id; // Save first answer's ID
            }
        }

        // Update question->answer with the first answer's ID
        if ($firstAnswerId !== null) {
            $question->answer = $firstAnswerId;
            $question->save(false, ['answer']); // Save only 'answer' field
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save(false)) {
            return $this->redirect(['view', 'id' => $model->id, 'category_id' => $model->course->category_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $course_id = $model->course_id;
        $category_id = $model->course->category_id;

        $model->delete();

        return $this->redirect(['course/view', 'id' => $course_id, 'category_id' => $category_id]);
    }

    public function actionNew($id){
        $model = $this->findModel($id);
        $model->status = 'new';
        $model->save(false);
        return $this->redirect(['test/view',
            'id' => $id,
            'category_id' => $model->course->category_id]);
    }

    public function actionReady($id){
        $model = $this->findModel($id);
        $model->status = 'ready';
        $model->save(false);
        return $this->redirect(['test/view',
            'id' => $id,
            'category_id' => $model->course->category_id]);
    }

    public function actionPublish($id){
        $model = $this->findModel($id);
        $model->status = 'public';
        $model->save(false);
        return $this->redirect(['test/view',
            'id' => $id,
            'category_id' => $model->course->category_id]);
    }

    public function actionEnd($id){
        $model = $this->findModel($id);
        $model->status = 'finished';
        $model->save(false);
        return $this->redirect(['test/view',
            'id' => $id,
            'category_id' => $model->course->category_id]);
    }

    public function actionCertificate($id){
        $model = $this->findModel($id);
        $model->status = 'certificated';
        $model->save(false);
        return $this->redirect(['test/view',
            'id' => $id,
            'category_id' => $model->course->category_id]);
    }

    protected function findModel($id)
    {
        if (($model = Test::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

}
