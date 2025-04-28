<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Participant;

/**
 * ParticipantSearch represents the model behind the search form of `common\models\Participant`.
 */
class ParticipantSearch extends Participant
{
    public $course;
    public $ssn;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'course_id'], 'integer'],
            [['name', 'telephone', 'organisation', 'course', 'ssn'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Participant::find()
            ->joinWith('course')
            ->joinWith('user');

        // add conditions that should always apply here
        if (!empty($params['ParticipantSearch']['course_id'])) {
            $query->andWhere(['course_id' => $params['ParticipantSearch']['course_id']]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                    'telephone',
                    'organisation',
                    'course' => [
                        'asc' => ['course.title' => SORT_ASC],
                        'desc' => ['course.title' => SORT_DESC],
                    ],
                    'ssn' => [
                        'asc' => ['user.ssn' => SORT_ASC],
                        'desc' => ['user.ssn' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'participant.id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'telephone', $this->telephone])
            ->andFilterWhere(['like', 'organisation', $this->organisation])
            ->andFilterWhere(['like', 'course.title', $this->course])
            ->andFilterWhere(['like', 'user.ssn', $this->ssn]);

        return $dataProvider;
    }
}
