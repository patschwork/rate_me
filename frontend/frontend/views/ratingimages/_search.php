<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\RatingImagesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rating-images-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'is_main_picture')->checkbox() ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'filename') ?>

    <?php // echo $form->field($model, 'file_blob') ?>

    <?php // echo $form->field($model, 'fk_rating_main_id') ?>

    <?php // echo $form->field($model, 'session_upload_key') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
