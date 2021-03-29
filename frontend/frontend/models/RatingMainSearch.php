<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RatingMain;

/**
 * RatingMainSearch represents the model behind the search form of `app\models\RatingMain`.
 */
class RatingMainSearch extends RatingMain
{
    /**
     * {@inheritdoc}
     */

    public $fkRatingType;

    public function rules()
    {
        return [
            [['id', 'fk_rating_type_id'], 'integer'],
            [['name', 'description', 'vendor', 'fkRatingType'], 'safe'],
            [['price'], 'number'],
            // [['fkRatingType'], 'safe'],
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
        $query = RatingMain::find();
        $query->joinWith(['fkRatingType rattype']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['fkRatingType'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['rattype.name' => SORT_ASC],
            'desc' => ['rattype.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'rating_main.id' => $this->id,
            'price' => $this->price,
            // 'fk_rating_type_id' => $this->fk_rating_type_id,
        ]);

        $query->andFilterWhere(['ilike', 'rating_main.name', $this->name])
            ->andFilterWhere(['ilike'  , 'rating_main.description', $this->description])
            ->andFilterWhere(['ilike'  , 'rating_main.vendor', $this->vendor])
            ->andFilterWhere(['ilike'  , 'rattype.name', $this->fkRatingType]);

        return $dataProvider;
    }
}
