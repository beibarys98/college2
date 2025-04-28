<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Certificate;

/**
 * CertificateSearch represents the model behind the search form of `common\models\Certificate`.
 */
class CertificateSearch extends Certificate
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'course_id'], 'integer'],
            [['img_path'], 'safe'],
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
        $query = Certificate::find();

        // add conditions that should always apply here
        if (isset($params['CertificateSearch']['course_id']) && $params['CertificateSearch']['course_id']) {
            $query->andWhere(['course_id' => $params['CertificateSearch']['course_id']]);
        }

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
            'course_id' => $this->course_id,
        ]);

        $query->andFilterWhere(['like', 'img_path', $this->img_path]);

        return $dataProvider;
    }
}
