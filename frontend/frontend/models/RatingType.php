<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rating_type".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $fk_rating_type_config_id
 *
 * @property RatingMain[] $ratingMains
 * @property RatingTypeConfig $fkRatingTypeConfig
 */
class RatingType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rating_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_rating_type_config_id'], 'default', 'value' => null],
            [['fk_rating_type_config_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['fk_rating_type_config_id'], 'exist', 'skipOnError' => true, 'targetClass' => RatingTypeConfig::className(), 'targetAttribute' => ['fk_rating_type_config_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'fk_rating_type_config_id' => Yii::t('app', 'Fk Rating Type Config ID'),
        ];
    }

    /**
     * Gets query for [[RatingMains]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRatingMains()
    {
        return $this->hasMany(RatingMain::className(), ['fk_rating_type_id' => 'id']);
    }

    /**
     * Gets query for [[FkRatingTypeConfig]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFkRatingTypeConfig()
    {
        return $this->hasOne(RatingTypeConfig::className(), ['id' => 'fk_rating_type_config_id']);
    }
}
