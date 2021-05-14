<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\rating\StarRating;
use yii\helpers\VarDumper;
use kartik\select2\Select2;

use yii\widgets\Pjax;

use kartik\depdrop\DepDrop;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\RatingMain */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rating-main-form">

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'data-pjax'=>true]]); ?>

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
        'pluginEvents' => [
            "select2:select" => 'function() { // function to make ajax call here 
                $.pjax.reload({
                    url: "'.yii\helpers\Url::to(['create']).'&fk_rating_type_id="+$(this).val()+"&dynPart=1",
                    container: "#pjax-ratingmain-form",
                    timeout: false,
                    method: "POST",
                    async: false,   // important to load multiple pjax-Container at the same time (https://stackoverflow.com/questions/31985286/how-to-reload-multiple-pjax)
                    });
                $.pjax.reload({
                    url: "'.yii\helpers\Url::to(['create']).'&fk_rating_type_id="+$(this).val()+"&dynPart=2",
                    container: "#pjax-ratingstars-form",
                    timeout: false,
                    method: "POST",
                    async: false,   // important to load multiple pjax-Container at the same time (https://stackoverflow.com/questions/31985286/how-to-reload-multiple-pjax)
                });
            }',
        ]
    ]);
    ?>

    <?php // $form = ActiveForm::begin(['id'=>'active-ratingmain-form']); ?>

    <?php Pjax::begin(['id'=>'pjax-ratingmain-form','enablePushState'=>false]); ?> 
    <?php
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
                        // Example: http://yii2insanity.blogspot.com/2017/06/katik-dependent-dropdown.html
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
                    echo $form->field($dynamic_type_config_model, $value->id)->widget(StarRating::classname(), [
                        'pluginOptions' => ['step' => 1],
                        'language' => 'de'
                    ])->label($value->fieldname, ['style'=>'color:blue;']);
                }
                else
                {
                    echo $form->field($dynamic_type_config_model, $value->id)->textInput()
                    ->label($value->fieldname, ['style'=>'color:blue;']); 
                }
            }
        } 
    ?>
    <?php Pjax::end(); ?> 
    <?php // ActiveForm::end(); ?>



    <?php

        if ($model->isNewRecord)
        {
            echo $form->field($model_stars, 'stars')->widget(StarRating::classname(), [
                'pluginOptions' => ['step' => 1],
                'language' => 'de'
            ]);

            echo $form->field($model_stars, 'user_comment')->widget(\bizley\quill\Quill::className(), ['options' => ['style' => 'height: 200px']])->label("Bewertungstext");
            
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
                                //'data' => $fieldValue[$value->id],
                                'name' => $fieldNamePrefix . '_select2_'.$value->id,
                                'options' => ['id' => $value->id, 'prompt' => 'Select dropDown before...'],
                                'pluginOptions' => [
                                    'depends' => [$value->depends_on_id],
                                    'placeholder' => 'Select ...',
                                    'url' => Url::to(['/ratingmain/typeconfigdependent'])
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
                                    'allowClear' => true
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

            echo $form->field($model_images, 'name')->textInput(['maxlength' => true])->label("Bildname");
            
            echo $form->field($model_images, 'file_blob[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->label("");
        }
        
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
