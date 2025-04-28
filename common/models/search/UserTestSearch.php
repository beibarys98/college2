<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserTest;

/**
 * UserTestSearch represents the model behind the search form of `common\models\UserTest`.
 */
class UserTestSearch extends UserTest
{
    public $user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'test_id', 'result'], 'integer'],
            [['start_time', 'end_time', 'user'], 'safe'],
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
        $query = UserTest::find()->joinWith(['user', 'test']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['user'] = [
            'asc' => ['user.name' => SORT_ASC],
            'desc' => ['user.name' => SORT_DESC],
        ];

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (isset($params['test_id'])) {
            $query->andWhere(['test_id' => $params['test_id']]);
        }

        $query->andWhere(['test.type' => 'test']);

        // grid filtering conditions
        $query->andFilterWhere([
            'user_test.id' => $this->id,
            'test_id' => $this->test_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'result' => $this->result,
        ]);

        $query->andFilterWhere(['like', 'user.name', $this->user]);

        return $dataProvider;
    }
}
