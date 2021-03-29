<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rating_type_config".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property RatingType[] $ratingTypes
 */
class RatingTypeConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rating_type_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 100],
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
        ];
    }

    /**
     * Gets query for [[RatingTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRatingTypes()
    {
        return $this->hasMany(RatingType::className(), ['fk_rating_type_config_id' => 'id']);
    }
}
