<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "v_cnt_ratings_per_rating_main".
 *
 * @property int|null $fk_rating_main_id
 * @property int|null $cnt_ratings_per_rating_main
 * @property float|null $avg_ratings_per_rating_main
 */
class VCntRatingsPerRatingMain extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_cnt_ratings_per_rating_main';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_rating_main_id', 'cnt_ratings_per_rating_main'], 'default', 'value' => null],
            [['fk_rating_main_id', 'cnt_ratings_per_rating_main'], 'integer'],
            [['avg_ratings_per_rating_main'], 'number'],
            [['fk_rating_main_id'], 'exist', 'skipOnError' => true, 'targetClass' => RatingMain::className(), 'targetAttribute' => ['fk_rating_main_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fk_rating_main_id' => Yii::t('app', 'Fk Rating Main ID'),
            'cnt_ratings_per_rating_main' => Yii::t('app', 'Cnt Ratings Per Rating Main'),
            'avg_ratings_per_rating_main' => Yii::t('app', 'Avg Ratings Per Rating Main'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return VCntRatingsPerRatingMainQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VCntRatingsPerRatingMainQuery(get_called_class());
    }

    public function getFkRatingMain()
    {
        return $this->hasOne(RatingMain::className(), ['id' => 'fk_rating_main_id']);
    }
}
