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


<?php
/**
* Truncates text.
*
* Cuts a string to the length of $length and replaces the last characters
* with the ending if the text is longer than length.
*
* ### Options:
*
* - `ending` Will be used as Ending and appended to the trimmed string
* - `exact` If false, $text will not be cut mid-word
* - `html` If true, HTML tags would be handled correctly
*
* @param string  $text String to truncate.
* @param integer $length Length of returned string, including ellipsis.
* @param array $options An array of html attributes and options.
* @return string Trimmed string.
* @access public
* @link http://book.cakephp.org/view/1469/Text#truncate-1625
*/
function truncate($text, $length = 100, $options = array()) {
    $default = array(
        'ending' => '...', 'exact' => true, 'html' => false
    );
    $options = array_merge($default, $options);
    extract($options);

    if ($html) {
        if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
            return $text;
        }
        $totalLength = mb_strlen(strip_tags($ending));
        $openTags = array();
        $truncate = '';

        preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
        foreach ($tags as $tag) {
            if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                    array_unshift($openTags, $tag[2]);
                } else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                    $pos = array_search($closeTag[1], $openTags);
                    if ($pos !== false) {
                        array_splice($openTags, $pos, 1);
                    }
                }
            }
            $truncate .= $tag[1];

            $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
            if ($contentLength + $totalLength > $length) {
                $left = $length - $totalLength;
                $entitiesLength = 0;
                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                    foreach ($entities[0] as $entity) {
                        if ($entity[1] + 1 - $entitiesLength <= $left) {
                            $left--;
                            $entitiesLength += mb_strlen($entity[0]);
                        } else {
                            break;
                        }
                    }
                }

                $truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
                break;
            } else {
                $truncate .= $tag[3];
                $totalLength += $contentLength;
            }
            if ($totalLength >= $length) {
                break;
            }
        }
    } else {
        if (mb_strlen($text) <= $length) {
            return $text;
        } else {
            $truncate = mb_substr($text, 0, $length - mb_strlen($ending));
        }
    }
    if (!$exact) {
        $spacepos = mb_strrpos($truncate, ' ');
        if (isset($spacepos)) {
            if ($html) {
                $bits = mb_substr($truncate, $spacepos);
                preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                if (!empty($droppedTags)) {
                    foreach ($droppedTags as $closingTag) {
                        if (!in_array($closingTag[1], $openTags)) {
                            array_unshift($openTags, $closingTag[1]);
                        }
                    }
                }
            }
            $truncate = mb_substr($truncate, 0, $spacepos);
        }
    }
    $truncate .= $ending;

    if ($html) {
        foreach ($openTags as $tag) {
            $truncate .= '</'.$tag.'>';
        }
    }

    return $truncate;
}


function replace_headlines($html_text)
{
    $arr = ["h1", "h2", "h3", "h4"];
    foreach ($arr as $html_tag)
    {
        $html_text = str_replace('<'.$html_tag.'>', ' <b>', $html_text);
        $html_text = str_replace('</'.$html_tag.'>', '</b>', $html_text);
        $html_text = str_replace('<'.$html_tag.'/>', ' <b>', $html_text);
    }
    return $html_text;
}
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
                    $count = 0;
                    if ($model->cntRatingStars !== null)
                    {
                        $value = $model->cntRatingStars->avg_ratings_per_rating_main;
                        $count = $value == 0 ? 0 : $model->cntRatingStars->cnt_ratings_per_rating_main;
                        
                    }
                    return StarRating::widget(['name' => 'rating_' . $key, 'value' =>  $value, 'language' => 'de',
                        'pluginOptions' => [
                            'displayOnly' => true,
                            'size' => 'm',
                        ]
                    ]) . "<br>"
                       .Yii::t('app', '{count,plural,=0{Keine Bewertung bisher} =1{1 Bewertung} other{# Bewertungen}}',['count' => $count,])
                    ;
                },
            ],
//            [
//                'attribute' => 'description',
//                'format' => 'html',
//                'label' => Yii::t('app', 'Description'),
//                'contentOptions' => ['style' => 'width:50px; white-space: normal;'],
//            ],
            [
                'attribute' => 'description',
                'format' => 'html',
                'label' => Yii::t('app', 'Description'),
                'contentOptions' => ['style' => 'width:50px; white-space: normal;'],
		'value' => function($model, $key)
		    {
			return truncate(
				    replace_headlines($model->description),
					150, 
					array('html' => true, 'ending' => ' [...]')); 
		    }
            ],
            // 'description:html',
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
