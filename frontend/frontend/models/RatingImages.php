<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rating_images".
 *
 * @property int $id
 * @property string|null $name
 * @property bool|null $is_main_picture
 * @property string|null $description
 * @property string|null $filename
 * @property resource|null $file_blob
 * @property int|null $fk_rating_main_id
 * @property string|null $session_upload_key
 * @property string|null $inserted_dt
 * @property int|null $image_height
 * @property int|null $image_type
 * @property string|null $image_htmlimg_width_heigt
 * @property int|null $image_bits
 * @property string|null $image_mime
 * @property int|null $image_width
 *
 * @property RatingMain $fkRatingMain
 */
class RatingImages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rating_images';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_main_picture'], 'boolean'],
            // [['file_blob'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png,gif,jpg,jpeg', 'maxFiles' => 15],
            [['file_blob'], 'file', 'extensions' => 'jpg, jpeg, gif, png', 'mimeTypes' => 'image/jpeg, image/gif, image/png', 'skipOnEmpty' => true, 'maxFiles' => 15],
            // [['file_blob'], 'file'],
            [['fk_rating_main_id', 'image_height', 'image_type', 'image_bits', 'image_width'], 'default', 'value' => null],
            [['fk_rating_main_id', 'image_height', 'image_type', 'image_bits', 'image_width'], 'integer'],
            [['inserted_dt'], 'safe'],
            [['name', 'session_upload_key', 'image_htmlimg_width_heigt', 'image_mime'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 4000],
            [['filename'], 'string', 'max' => 1000],
            [['fk_rating_main_id'], 'exist', 'skipOnError' => true, 'targetClass' => RatingMain::className(), 'targetAttribute' => ['fk_rating_main_id' => 'id']],
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
            'is_main_picture' => Yii::t('app', 'Is Main Picture'),
            'description' => Yii::t('app', 'Description'),
            'filename' => Yii::t('app', 'Filename'),
            'file_blob' => Yii::t('app', 'File Blob'),
            'fk_rating_main_id' => Yii::t('app', 'Fk Rating Main ID'),
            'session_upload_key' => Yii::t('app', 'Session Upload Key'),
            'inserted_dt' => Yii::t('app', 'Inserted Dt'),
            'image_height' => Yii::t('app', 'Image Height'),
            'image_type' => Yii::t('app', 'Image Type'),
            'image_htmlimg_width_heigt' => Yii::t('app', 'Image Htmlimg Width Heigt'),
            'image_bits' => Yii::t('app', 'Image Bits'),
            'image_mime' => Yii::t('app', 'Image Mime'),
            'image_width' => Yii::t('app', 'Image Width'),
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
}
