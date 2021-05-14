<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "v_additional_key_value_entries".
 *
 * @property int|null $id
 * @property int|null $fk_rating_main_id
 * @property int|null $fk_rating_stars_id
 * @property string|null $fieldname
 * @property string|null $value
 */
class VAdditionalKeyValueEntries extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_additional_key_value_entries';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_rating_main_id', 'fk_rating_stars_id'], 'default', 'value' => null],
            [['id', 'fk_rating_main_id', 'fk_rating_stars_id'], 'integer'],
            [['value'], 'string'],
            [['fieldname'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fk_rating_main_id' => Yii::t('app', 'Fk Rating Main ID'),
            'fk_rating_stars_id' => Yii::t('app', 'Fk Rating Stars ID'),
            'fieldname' => Yii::t('app', 'Fieldname'),
            'value' => Yii::t('app', 'Value'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return VAdditionalKeyValueEntriesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VAdditionalKeyValueEntriesQuery(get_called_class());
    }
}
