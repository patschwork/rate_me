<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RatingImages;

/**
 * RatingImagesSearch represents the model behind the search form of `app\models\RatingImages`.
 */
class RatingImagesSearch extends RatingImages
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_rating_main_id'], 'integer'],
            [['name', 'description', 'filename', 'file_blob', 'session_upload_key'], 'safe'],
            [['is_main_picture'], 'boolean'],
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
        $query = RatingImages::find();

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
            'is_main_picture' => $this->is_main_picture,
            'fk_rating_main_id' => $this->fk_rating_main_id,
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'description', $this->description])
            ->andFilterWhere(['ilike', 'filename', $this->filename])
            ->andFilterWhere(['ilike', 'file_blob', $this->file_blob])
            ->andFilterWhere(['ilike', 'session_upload_key', $this->session_upload_key]);

        return $dataProvider;
    }
}
