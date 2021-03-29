<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RatingType */

$this->title = Yii::t('app', 'Create Rating Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rating Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rating-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
