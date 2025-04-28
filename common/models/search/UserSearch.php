<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{
    public $category;
    public $course;
    public $time;
    public $result;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'course_id'], 'integer'],
            [['ssn', 'name', 'telephone', 'organization',
                'auth_key', 'password_hash', 'course', 'category', 'time', 'result'], 'safe'],
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
        $query = User::find()
            ->joinWith('course')
            ->joinWith('category');

        // add conditions that should always apply here
        if (!empty($params['UserSearch']['course_id'])) {
            $query->andWhere(['course_id' => $params['UserSearch']['course_id']]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'category' => [
                        'asc' => ['category.title' => SORT_ASC],
                        'desc' => ['category.title' => SORT_DESC],
                    ],
                    'course' => [
                        'asc' => ['course.title' => SORT_ASC],
                        'desc' => ['course.title' => SORT_DESC],
                    ],
                    'ssn',
                    'name',
                    'telephone',
                    'organization',

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
            'user.id' => $this->id,
            'course_id' => $this->course_id,
        ]);

        $query->andFilterWhere(['like', 'category.title', $this->category])
            ->andFilterWhere(['like', 'course.title', $this->course])
            ->andFilterWhere(['like', 'ssn', $this->ssn])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'telephone', $this->telephone])
            ->andFilterWhere(['like', 'organization', $this->organization])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash]);

        return $dataProvider;
    }
}
