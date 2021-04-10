<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rating_main".
 *
 * @property int $id
 * @property string|null $name
 * @property float|null $price
 * @property string|null $description
 * @property string|null $vendor
 * @property int|null $fk_rating_type_id
 * @property string|null $session_upload_key
 * @property string|null $inserted_dt
 * @property string|null $packaging_unit
 *
 * @property RatingImages[] $ratingImages
 * @property RatingType $fkRatingType
 * @property RatingStars[] $ratingStars
 */
class RatingMain extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rating_main';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price'], 'number'],
            [['fk_rating_type_id'], 'default', 'value' => null],
            [['fk_rating_type_id'], 'integer'],
            [['inserted_dt'], 'safe'],
            [['name', 'session_upload_key'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 4000],
            [['vendor', 'packaging_unit'], 'string', 'max' => 255],
            [['fk_rating_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RatingType::className(), 'targetAttribute' => ['fk_rating_type_id' => 'id']],
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
            'price' => Yii::t('app', 'Price'),
            'description' => Yii::t('app', 'Description'),
            'vendor' => Yii::t('app', 'Vendor'),
            'fk_rating_type_id' => Yii::t('app', 'Fk Rating Type ID'),
            'session_upload_key' => Yii::t('app', 'Session Upload Key'),
            'inserted_dt' => Yii::t('app', 'Inserted Dt'),
            'packaging_unit' => Yii::t('app', 'Packaging Unit'),
        ];
    }

    /**
     * Gets query for [[RatingImages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRatingImages()
    {
        return $this->hasMany(RatingImages::className(), ['fk_rating_main_id' => 'id']);
    }

    /**
     * Gets query for [[FkRatingType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFkRatingType()
    {
        return $this->hasOne(RatingType::className(), ['id' => 'fk_rating_type_id']);
    }

    /**
     * Gets query for [[RatingStars]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRatingStars()
    {
        return $this->hasMany(RatingStars::className(), ['fk_rating_main_id' => 'id']);
    }

    public function getCntRatingStars()
    {
        return $this->hasOne(VCntRatingsPerRatingMain::className(), ['fk_rating_main_id' => 'id']);
    }
}
