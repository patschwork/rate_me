<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rating_type_config_fields".
 *
 * @property int $id
 * @property int|null $fk_rating_type_config_id
 * @property string|null $fieldname
 * @property string|null $datatype
 * @property string|null $allowed_values_csv
 * @property string|null $description
 * @property bool $used_4_rating_main
 * @property bool $used_4_rating_stars
 * @property int|null $depends_on_id
 * @property bool $is_a_lookup_value
 * @property bool $input_is_mandadory
 * @property bool $user_can_make_new_suggestion
 *
 * @property RatingTypeConfig $fkRatingTypeConfig
 * @property RatingTypeConfigFields $dependsOn
 * @property RatingTypeConfigFields[] $ratingTypeConfigFields
 * @property RatingTypeConfigLookupValues[] $ratingTypeConfigLookupValues
 */
class RatingTypeConfigFields extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rating_type_config_fields';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_rating_type_config_id', 'depends_on_id'], 'default', 'value' => null],
            [['fk_rating_type_config_id', 'depends_on_id'], 'integer'],
            [['allowed_values_csv'], 'string'],
            [['used_4_rating_main', 'used_4_rating_stars', 'is_a_lookup_value', 'input_is_mandadory', 'user_can_make_new_suggestion'], 'boolean'],
            [['fieldname', 'datatype'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 4000],
            [['fk_rating_type_config_id'], 'exist', 'skipOnError' => true, 'targetClass' => RatingTypeConfig::className(), 'targetAttribute' => ['fk_rating_type_config_id' => 'id']],
            [['depends_on_id'], 'exist', 'skipOnError' => true, 'targetClass' => RatingTypeConfigFields::className(), 'targetAttribute' => ['depends_on_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fk_rating_type_config_id' => Yii::t('app', 'Fk Rating Type Config ID'),
            'fieldname' => Yii::t('app', 'Fieldname'),
            'datatype' => Yii::t('app', 'Datatype'),
            'allowed_values_csv' => Yii::t('app', 'Allowed Values Csv'),
            'description' => Yii::t('app', 'Description'),
            'used_4_rating_main' => Yii::t('app', 'Used 4 Rating Main'),
            'used_4_rating_stars' => Yii::t('app', 'Used 4 Rating Stars'),
            'depends_on_id' => Yii::t('app', 'Depends On ID'),
            'is_a_lookup_value' => Yii::t('app', 'Is A Lookup Value'),
            'input_is_mandadory' => Yii::t('app', 'Input Is Mandadory'),
            'user_can_make_new_suggestion' => Yii::t('app', 'User Can Make New Suggestion'),
        ];
    }

    /**
     * Gets query for [[FkRatingTypeConfig]].
     *
     * @return \yii\db\ActiveQuery|yii\db\ActiveQuery
     */
    public function getFkRatingTypeConfig()
    {
        return $this->hasOne(RatingTypeConfig::className(), ['id' => 'fk_rating_type_config_id']);
    }

    /**
     * Gets query for [[DependsOn]].
     *
     * @return \yii\db\ActiveQuery|RatingTypeConfigFieldsQuery
     */
    public function getDependsOn()
    {
        return $this->hasOne(RatingTypeConfigFields::className(), ['id' => 'depends_on_id']);
    }

    /**
     * Gets query for [[RatingTypeConfigFields]].
     *
     * @return \yii\db\ActiveQuery|RatingTypeConfigFieldsQuery
     */
    public function getRatingTypeConfigFields()
    {
        return $this->hasMany(RatingTypeConfigFields::className(), ['depends_on_id' => 'id']);
    }

    /**
     * Gets query for [[RatingTypeConfigLookupValues]].
     *
     * @return \yii\db\ActiveQuery|RatingTypeConfigLookupValuesQuery
     */
    public function getRatingTypeConfigLookupValues()
    {
        return $this->hasMany(RatingTypeConfigLookupValues::className(), ['fk_rating_type_config_fields_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return RatingTypeConfigFieldsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RatingTypeConfigFieldsQuery(get_called_class());
    }
}
