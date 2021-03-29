<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\rating\StarRating;

/* @var $this yii\web\View */
/* @var $model app\models\RatingStars */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rating-stars-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
        // echo $form->field($model, 'stars')->textInput()->label("Bewertung (1=Schlecht bis 5=Gut)");
        
        echo $form->field($model, 'stars')->widget(StarRating::classname(), [
            'pluginOptions' => ['step' => 1],
            'language' => 'de'
        ]);
    ?>
	
    <?= $form->field($model, 'user_comment')->widget(\bizley\quill\Quill::className(), ['options' => ['style' => 'height: 200px']]) ?>

    <?= $form->field($model, 'fk_rating_main_id')->textInput(['disabled' => $model->fk_rating_main_id === null ? false : true]) ?>

    <?php // echo $form->field($model, 'fk_user_id')->textInput(); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
