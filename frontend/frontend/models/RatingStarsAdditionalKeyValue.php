<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rating_stars_additional_key_value".
 *
 * @property int $id
 * @property int $fk_rating_stars_id
 * @property int $fk_rating_type_config_fields_id
 * @property int|null $fk_rating_type_config_lookup_values_id
 * @property string|null $value_string_1
 * @property int|null $value_integer_1
 * @property float|null $value_currency_1
 * @property float|null $value_numeric_1
 * @property bool|null $value_bool_1
 *
 * @property RatingStars $fkRatingStars
 * @property RatingTypeConfigFields $fkRatingTypeConfigFields
 * @property RatingTypeConfigLookupValues $fkRatingTypeConfigLookupValues
 */
class RatingStarsAdditionalKeyValue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rating_stars_additional_key_value';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_rating_stars_id', 'fk_rating_type_config_fields_id'], 'required'],
            [['fk_rating_stars_id', 'fk_rating_type_config_fields_id', 'fk_rating_type_config_lookup_values_id', 'value_integer_1'], 'default', 'value' => null],
            [['fk_rating_stars_id', 'fk_rating_type_config_fields_id', 'fk_rating_type_config_lookup_values_id', 'value_integer_1'], 'integer'],
            [['value_currency_1', 'value_numeric_1'], 'number'],
            [['value_bool_1'], 'boolean'],
            [['value_string_1'], 'string', 'max' => 4000],
            [['fk_rating_stars_id'], 'exist', 'skipOnError' => true, 'targetClass' => RatingStars::className(), 'targetAttribute' => ['fk_rating_stars_id' => 'id']],
            [['fk_rating_type_config_fields_id'], 'exist', 'skipOnError' => true, 'targetClass' => RatingTypeConfigFields::className(), 'targetAttribute' => ['fk_rating_type_config_fields_id' => 'id']],
            [['fk_rating_type_config_lookup_values_id'], 'exist', 'skipOnError' => true, 'targetClass' => RatingTypeConfigLookupValues::className(), 'targetAttribute' => ['fk_rating_type_config_lookup_values_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fk_rating_stars_id' => Yii::t('app', 'Fk Rating Stars ID'),
            'fk_rating_type_config_fields_id' => Yii::t('app', 'Fk Rating Type Config Fields ID'),
            'fk_rating_type_config_lookup_values_id' => Yii::t('app', 'Fk Rating Type Config Lookup Values ID'),
            'value_string_1' => Yii::t('app', 'Value String 1'),
            'value_integer_1' => Yii::t('app', 'Value Integer 1'),
            'value_currency_1' => Yii::t('app', 'Value Currency 1'),
            'value_numeric_1' => Yii::t('app', 'Value Numeric 1'),
            'value_bool_1' => Yii::t('app', 'Value Bool 1'),
        ];
    }

    /**
     * Gets query for [[FkRatingStars]].
     *
     * @return \yii\db\ActiveQuery|yii\db\ActiveQuery
     */
    public function getFkRatingStars()
    {
        return $this->hasOne(RatingStars::className(), ['id' => 'fk_rating_stars_id']);
    }

    /**
     * Gets query for [[FkRatingTypeConfigFields]].
     *
     * @return \yii\db\ActiveQuery|RatingTypeConfigFieldsQuery
     */
    public function getFkRatingTypeConfigFields()
    {
        return $this->hasOne(RatingTypeConfigFields::className(), ['id' => 'fk_rating_type_config_fields_id']);
    }

    /**
     * Gets query for [[FkRatingTypeConfigLookupValues]].
     *
     * @return \yii\db\ActiveQuery|RatingTypeConfigLookupValuesQuery
     */
    public function getFkRatingTypeConfigLookupValues()
    {
        return $this->hasOne(RatingTypeConfigLookupValues::className(), ['id' => 'fk_rating_type_config_lookup_values_id']);
    }

    /**
     * {@inheritdoc}
     * @return RatingStarsAdditionalKeyValueQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RatingStarsAdditionalKeyValueQuery(get_called_class());
    }
}
