<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\rating\StarRating;
use phpDocumentor\Reflection\Types\Null_;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\RatingMainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Rating Mains');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rating-main-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Rating Main'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [


        	['class' => 'yii\grid\ActionColumn', 'contentOptions'=>[ 'style'=>'white-space: nowrap;']
            ,
				'template' => '{overview} {view} {update} {delete}',
				
						'buttons' => [
							'overview' => function ($url, $model) {
		
								$html_btn = Html::a('<h4><span style="color: green;" class="glyphicon glyphicon-th-list"></span></h4>', $url, [
										'title' => Yii::t('app', 'Overview'),
								]);
								return $html_btn;
							}
						],
						'urlCreator' => function ($action, $model, $key, $index) {
							if ($action === 'overview') {
								$url = "?r=overview/view&id=".$model->id; // your own url generation logic
								return $url;
							}
							// general button actions
							$controller = Yii::$app->controller->id;
							return Yii::$app->urlManager->createUrl([$controller . '/' . $action ,'id'=>$key]);
						}
			],

            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'vendor',
            [
                'attribute' => 'cntRatingStars',
                'format' => 'raw',
                'label' => Yii::t('app', 'Stars'),
                'value' => function($model, $key) 
                {
                    $value = 0;
                    if ($model->cntRatingStars !== null)
                    {
                        $value = $model->cntRatingStars->avg_ratings_per_rating_main;
                    }
                    return StarRating::widget(['name' => 'rating_' . $key, 'value' =>  $value, 'language' => 'de',
                        'pluginOptions' => [
                            'displayOnly' => true,
                            'size' => 'm',
                        ]
                    ]);
                },
            ],
            'description:html',
            'price',
            'packaging_unit',
            [
                'attribute' => 'fkRatingType',
                'label' => 'Type',
                'value' => 'fkRatingType.name'
            ],
            // [
            //     'attribute' => 'cntRatingStars',
            //     'label' => 'Avg rating',
            //     'value' => 'cntRatingStars.avg_ratings_per_rating_main'
            // ],
            'id',
            //'fk_rating_type_id',
        ],
    ]); ?>


</div>
