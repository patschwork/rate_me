<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RatingMain */

$this->title = Yii::t('app', 'Update Rating Main: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rating Mains'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="rating-main-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'model_images' => $model_images,
        'model_stars'  => $model_stars,
        'ratingTypes'  => $ratingTypes,
    ]) ?>

</div>
