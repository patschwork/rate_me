<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\RatingStarsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rating-stars-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'stars') ?>

    <?= $form->field($model, 'fk_rating_main_id') ?>

    <?= $form->field($model, 'fk_user_id') ?>

    <?= $form->field($model, 'user_comment') ?>

    <?php // echo $form->field($model, 'session_upload_key') ?>

    <?php // echo $form->field($model, 'inserted_dt') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
