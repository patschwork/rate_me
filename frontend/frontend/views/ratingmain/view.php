<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\RatingMain */

$this->title = $model->name;
$showTheBreadcrumb = true;
if (isset($paramShowTheBreadcrumb))
{
    $showTheBreadcrumb = $paramShowTheBreadcrumb;
}
if ($showTheBreadcrumb)
{
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'List'), 'url' => ['index', 'RatingMainSearch[id]' => $model->id]];
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Overview'), 'url' => ['/overview/view', 'id' => $model->id]];
    $this->params['breadcrumbs'][] = $this->title;
}

\yii\web\YiiAsset::register($this);
?>
<div class="rating-main-view">

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
            'name',
            'price',
            'packaging_unit',
            'description:html',
            'vendor',
            'fkRatingType.name',
        ],
        'template' => "<tr><th style='width: 25%;'>{label}</th><td>{value}</td></tr>"
    ]) ?>

</div>
