<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rating_stars".
 *
 * @property int $id
 * @property int|null $stars
 * @property int|null $fk_rating_main_id
 * @property int|null $fk_user_id
 * @property string|null $user_comment
 * @property string|null $session_upload_key
 * @property string|null $inserted_dt
 *
 * @property RatingMain $fkRatingMain
 * @property User $fkUser
 */
class RatingStars extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rating_stars';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stars', 'fk_rating_main_id', 'fk_user_id'], 'default', 'value' => null],
            [['stars', 'fk_rating_main_id', 'fk_user_id'], 'integer'],
            [['inserted_dt'], 'safe'],
            [['user_comment'], 'string', 'max' => 4000],
            [['session_upload_key'], 'string', 'max' => 100],
            [['fk_rating_main_id'], 'exist', 'skipOnError' => true, 'targetClass' => RatingMain::className(), 'targetAttribute' => ['fk_rating_main_id' => 'id']],
            [['fk_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['fk_user_id' => 'id']],
            ['stars', 'in', 'range' => [1, 2, 3, 4, 5]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'stars' => Yii::t('app', 'Stars'),
            'fk_rating_main_id' => Yii::t('app', 'Fk Rating Main ID'),
            'fk_user_id' => Yii::t('app', 'Fk User ID'),
            'user_comment' => Yii::t('app', 'User Comment'),
            'session_upload_key' => Yii::t('app', 'Session Upload Key'),
            'inserted_dt' => Yii::t('app', 'Inserted Dt'),
        ];
    }

    /**
     * Gets query for [[FkRatingMain]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFkRatingMain()
    {
        return $this->hasOne(RatingMain::className(), ['id' => 'fk_rating_main_id']);
    }

    /**
     * Gets query for [[FkUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFkUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'fk_user_id']);
    }
}
