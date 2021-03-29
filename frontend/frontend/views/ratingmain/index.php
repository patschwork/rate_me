<?php

use yii\helpers\Html;
use yii\grid\GridView;

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
            'description:html',
            'price',
            'packaging_unit',
            [
                'attribute' => 'fkRatingType',
                'label' => 'Type',
                'value' => 'fkRatingType.name'
            ],
            'id',
            //'fk_rating_type_id',
        ],
    ]); ?>


</div>
