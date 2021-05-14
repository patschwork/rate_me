<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RatingMain */

$this->title = Yii::t('app', 'Create Rating Main');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rating Mains'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rating-main-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php 
        echo $this->render('_form', [
            'model'        => $model,
            'model_images' => $model_images,
            'model_stars'  => $model_stars,
            'ratingTypes'  => $ratingTypes,


            'allModelRatingTypeConfigFields' => $allModelRatingTypeConfigFields,
            'dynamic_type_config_model' => $dynamic_type_config_model,
            'fieldValue' => $fieldValue,
            'fieldNamePrefix' => $fieldNamePrefix,
        ]); 

    ?>

</div>
