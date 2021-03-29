<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RatingImages */

$this->title = Yii::t('app', 'Create Rating Images');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rating Images'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rating-images-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
