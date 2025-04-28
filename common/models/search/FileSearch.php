<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\File;

/**
 * FileSearch represents the model behind the search form of `common\models\File`.
 */
class FileSearch extends File
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'participant_id', 'course_id'], 'integer'],
            [['file_path', 'type'], 'safe'],
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
        $query = File::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'participant_id' => $this->participant_id,
            'course_id' => $this->course_id,
        ]);

        $query->andFilterWhere(['like', 'file_path', $this->file_path])
            ->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
