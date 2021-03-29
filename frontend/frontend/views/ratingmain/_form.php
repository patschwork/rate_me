<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\rating\StarRating;
use yii\helpers\VarDumper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\RatingMain */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rating-main-form">

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput()->label("Preis") ?>

    <?= $form->field($model, 'packaging_unit')->textInput()->label("Verpackungseinheit") ?>

    <?= $form->field($model, 'description')->widget(\bizley\quill\Quill::className(), ['options' => ['style' => 'height: 200px']])->label("Beschreibung") ?>

    <?= $form->field($model, 'vendor')->textInput(['maxlength' => true])->label("Hersteller") ?>

    <?php // $form->field($model, 'fk_rating_type_id')->textInput() ?>

    <?php

    echo $form->field($model, 'fk_rating_type_id')->widget(Select2::classname(), [
        'data' => $ratingTypes,
        'options' => ['placeholder' => 'Select a type ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?php

        if ($model->isNewRecord)
        {
            echo $form->field($model_stars, 'stars')->widget(StarRating::classname(), [
                'pluginOptions' => ['step' => 1],
                'language' => 'de'
            ]);

            echo $form->field($model_stars, 'user_comment')->widget(\bizley\quill\Quill::className(), ['options' => ['style' => 'height: 200px']])->label("Bewertungstext");
            
            echo $form->field($model_images, 'name')->textInput(['maxlength' => true])->label("Bildname");
            
            echo $form->field($model_images, 'file_blob[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->label("");
        }
        
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
