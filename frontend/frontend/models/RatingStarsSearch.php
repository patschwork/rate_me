<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RatingStars;

/**
 * RatingStarsSearch represents the model behind the search form of `app\models\RatingStars`.
 */
class RatingStarsSearch extends RatingStars
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'stars', 'fk_rating_main_id', 'fk_user_id'], 'integer'],
            [['user_comment', 'session_upload_key', 'inserted_dt'], 'safe'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = RatingStars::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'stars' => $this->stars,
            'fk_rating_main_id' => $this->fk_rating_main_id,
            'fk_user_id' => $this->fk_user_id,
            'inserted_dt' => $this->inserted_dt,
        ]);

        $query->andFilterWhere(['ilike', 'user_comment', $this->user_comment])
            ->andFilterWhere(['ilike', 'session_upload_key', $this->session_upload_key]);

        return $dataProvider;
    }
}
