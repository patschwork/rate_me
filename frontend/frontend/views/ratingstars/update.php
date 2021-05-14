<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RatingStars */

$this->title = Yii::t('app', 'Update Rating Stars: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rating Stars'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="rating-stars-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,

        'allModelRatingTypeConfigFields' => $allModelRatingTypeConfigFields,
        'dynamic_type_config_model' => $dynamic_type_config_model,
        'fieldValue' => $fieldValue,
        'fieldNamePrefix' => $fieldNamePrefix,
    ]) ?>

</div>
