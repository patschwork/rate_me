<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rating_type_config_lookup_values".
 *
 * @property int $id
 * @property int $fk_rating_type_config_id
 * @property int $fk_rating_type_config_fields_id
 * @property string $value
 * @property bool|null $new_offered_value_from_a_user_accepted
 * @property int|null $fk_user_id_for_new_offered_value
 * @property int|null $parent_id
 *
 * @property RatingTypeConfig $fkRatingTypeConfig
 * @property RatingTypeConfigFields $fkRatingTypeConfigFields
 * @property RatingTypeConfigLookupValues $parent
 * @property RatingTypeConfigLookupValues[] $ratingTypeConfigLookupValues
 * @property User $fkUserIdForNewOfferedValue
 */
class RatingTypeConfigLookupValues extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rating_type_config_lookup_values';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_rating_type_config_id', 'fk_rating_type_config_fields_id', 'value'], 'required'],
            [['fk_rating_type_config_id', 'fk_rating_type_config_fields_id', 'fk_user_id_for_new_offered_value', 'parent_id'], 'default', 'value' => null],
            [['fk_rating_type_config_id', 'fk_rating_type_config_fields_id', 'fk_user_id_for_new_offered_value', 'parent_id'], 'integer'],
            [['new_offered_value_from_a_user_accepted'], 'boolean'],
            [['value'], 'string', 'max' => 500],
            [['fk_rating_type_config_id'], 'exist', 'skipOnError' => true, 'targetClass' => RatingTypeConfig::className(), 'targetAttribute' => ['fk_rating_type_config_id' => 'id']],
            [['fk_rating_type_config_fields_id'], 'exist', 'skipOnError' => true, 'targetClass' => RatingTypeConfigFields::className(), 'targetAttribute' => ['fk_rating_type_config_fields_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => RatingTypeConfigLookupValues::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['fk_user_id_for_new_offered_value'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['fk_user_id_for_new_offered_value' => 'id']],
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
            'fk_rating_type_config_fields_id' => Yii::t('app', 'Fk Rating Type Config Fields ID'),
            'value' => Yii::t('app', 'Value'),
            'new_offered_value_from_a_user_accepted' => Yii::t('app', 'New Offered Value From A User Accepted'),
            'fk_user_id_for_new_offered_value' => Yii::t('app', 'Fk User Id For New Offered Value'),
            'parent_id' => Yii::t('app', 'Parent ID'),
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
     * Gets query for [[FkRatingTypeConfigFields]].
     *
     * @return \yii\db\ActiveQuery|RatingTypeConfigFieldsQuery
     */
    public function getFkRatingTypeConfigFields()
    {
        return $this->hasOne(RatingTypeConfigFields::className(), ['id' => 'fk_rating_type_config_fields_id']);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery|RatingTypeConfigLookupValuesQuery
     */
    public function getParent()
    {
        return $this->hasOne(RatingTypeConfigLookupValues::className(), ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[RatingTypeConfigLookupValues]].
     *
     * @return \yii\db\ActiveQuery|RatingTypeConfigLookupValuesQuery
     */
    public function getRatingTypeConfigLookupValues()
    {
        return $this->hasMany(RatingTypeConfigLookupValues::className(), ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[FkUserIdForNewOfferedValue]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getFkUserIdForNewOfferedValue()
    {
        return $this->hasOne(User::className(), ['id' => 'fk_user_id_for_new_offered_value']);
    }

    /**
     * {@inheritdoc}
     * @return RatingTypeConfigLookupValuesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RatingTypeConfigLookupValuesQuery(get_called_class());
    }
}
