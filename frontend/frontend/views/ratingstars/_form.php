<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\rating\StarRating;


use kartik\select2\Select2;
use yii\widgets\Pjax;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\RatingStars */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$this->registerJs(
    'function switch_dyn_attr(param_rating_main_id) {
        $.pjax.reload({
            url: "'.yii\helpers\Url::to(['create']).'&fk_rating_main_id="+param_rating_main_id+"&dynPart=2",
            container: "#pjax-ratingstars-form",
            timeout: false,
            method: "POST",
            async: false,   // important to load multiple pjax-Container at the same time (https://stackoverflow.com/questions/31985286/how-to-reload-multiple-pjax)
        });
     }
    '
    ,yii\web\View::POS_HEAD
 );
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

    <?php
        /* ********************************************************************************* */
        // THIS PART IS REDUNDANT TO THE EXACT SAME CODE PART IN views/rating_main/_form.php
        /* ********************************************************************************* */
        Pjax::begin(['id'=>'pjax-ratingstars-form','enablePushState'=>false]);
        foreach ($allModelRatingTypeConfigFields as $key => $value) 
        {
            if ($value->is_a_lookup_value) // values a prepared in the database...
            {
                if (isset($fieldValue[$value->id])) // maybe there are no lookup values declared in the database. In this case, do not show...
                {
                    // echo $form->field($dynamic_type_config_model, $value->id)->dropDownList($fieldValue[$value->id])
                    //     ->label($value->fieldname, ['style'=>'color:blue;']); 

                    if ($value->depends_on_id !== null) 
                    {
                        // echo "Hängt ab von: " . $value->depends_on_id . "er Einträgen. Dann muss hier für <i>" . $value->fieldname . "</i> ein DepDrop hin...";
                        // Beispiel: http://yii2insanity.blogspot.com/2017/06/katik-dependent-dropdown.html

                        echo $form->field($dynamic_type_config_model, $value->id)->widget(DepDrop::classname(), [
                            'type'=>DepDrop::TYPE_SELECT2,
                            'data' => $fieldValue[$value->id],
                            'name' => $fieldNamePrefix . '_select2_'.$value->id,
                            'options' => ['id' => $value->id, 'prompt' => 'Select dropDown before...'],
                            'pluginOptions' => [
                                'depends' => [$value->depends_on_id],
                                'placeholder' => 'Select ...',
                                'url' => Url::to(['/ratingmain/typeconfigdependent']),
                                'initialize'=>true      // important in  update-view!! 
                            ]
                        ])
                        ->label($value->fieldname, ['style'=>'color:blue;']);
                    }
                    else
                    {
                        echo $form->field($dynamic_type_config_model, $value->id)->widget(Select2::classname(), [
                            'data' => $fieldValue[$value->id],
                            'name' => $fieldNamePrefix . '_select2_'.$value->id,
                            'options' => ['placeholder' => 'Select ...', 'id' => $value->id],
                            'pluginOptions' => [
                                // 'allowClear' => true
                            ]
                        ])->label($value->fieldname, ['style'=>'color:blue;']);
                    }
                }
            }
            else // "free" textinput field
            {
                if ($value->fieldname == "Säureempfinden") // A TEST
                {
                    // glyphicon glyphicon-fire
                    echo $form->field($dynamic_type_config_model, $value->id)->widget(StarRating::classname(), [
                        'pluginOptions' => [
                            'step' => 1,
                            'starCaptions' => [
                                0 => 'Säure nicht bemerkbar',
                                1 => 'Wenig Säure',
                                2 => 'Angenehme Säure',
                                3 => 'Säure deutlich erkennbar',
                                4 => 'Viel Säure',
                                5 => 'Sehr viel Säure - unangenehm',
                            ],
                            'starCaptionClasses' => [
                                5 => 'text-danger',
                                4 => 'text-warning',
                                3 => 'text-info',
                                2 => 'text-primary',
                                1 => 'text-success'
                            ],
                            'filledStar' => '<i class="glyphicon glyphicon-fire"></i>',
                            'emptyStar' => '<i class="glyphicon glyphicon-asterisk"></i>',                                
                        ],
                        'language' => 'de',
                    ])->label($value->fieldname, ['style'=>'color:blue;']);
                }
                else
                {
                    echo $form->field($dynamic_type_config_model, $value->id)->textInput()
                    ->label($value->fieldname, ['style'=>'color:blue;']); 
                }
            }
        } 

        Pjax::end();

        /* ********************************************************************************* */
        // THIS PART IS REDUNDANT TO THE EXACT SAME CODE PART IN views/rating_main/_form.php
        /* ********************************************************************************* */
    ?>





    <?= $form->field($model, 'fk_rating_main_id')->textInput(['disabled' => $model->fk_rating_main_id === null ? false : true, 'onchange' => 'switch_dyn_attr(this.value);']) ?>

    <?php // echo $form->field($model, 'fk_user_id')->textInput(); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
