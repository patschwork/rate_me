<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\rating\StarRating;

/* @var $this yii\web\View */
/* @var $model app\models\RatingStars */

$this->title = $model->id;
$showTheBreadcrumb = true;
if (isset($paramShowTheBreadcrumb))
{
    $showTheBreadcrumb = $paramShowTheBreadcrumb;
}
if ($showTheBreadcrumb)
{
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'List'), 'url' => ['/ratingmain/index', 'RatingMainSearch[id]' => $model->fk_rating_main_id]];
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Overview'), 'url' => ['/overview/view', 'id' => $model->fk_rating_main_id]];
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rating Stars'), 'url' => ['index', 'RatingStarsSearch[fk_rating_main_id]' => $model->fk_rating_main_id]];
    $this->params['breadcrumbs'][] = $this->title;
}

\yii\web\YiiAsset::register($this);
?>
<div class="rating-stars-view">

    <h1>
    <?php
    $showTheTitle = true;
    if (isset($paramShowTheTitle))
    {
        $showTheTitle = $paramShowTheTitle;
    }
    if ($showTheTitle)
    {
        echo Html::encode($this->title);
    }
    ?></h1>

    <p>
        <?php
        $showTheButtons = true;
        if (isset($paramShowTheButtons))
        {
            $showTheButtons = $paramShowTheButtons;
        }
        if ($showTheButtons)
        {
            echo Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
            echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]);
        }
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'format' => 'raw',
                'label' => Yii::t('app', 'Stars'),
                'value' => function($model) {
                    return StarRating::widget(['name' => 'rating_' . $model->id, 'value' => $model->stars, 'language' => 'de',
                    'pluginOptions' => [
                        'displayOnly' => true,
                        'size' => 'm',
                    ]
                ]);
                },
               ],
            'fk_rating_main_id',
            'fk_user_id',
            'user_comment:html',
            'session_upload_key',
            'inserted_dt',
        ],
        'template' => "<tr><th style='width: 25%;'>{label}</th><td>{value}</td></tr>"
    ]) ?>

</div>
