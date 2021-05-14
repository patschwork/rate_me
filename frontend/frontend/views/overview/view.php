<?php

use app\models\VAdditionalKeyValueEntries;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\RatingMain */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'List'), 'url' => ['/ratingmain']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'List [filtered on "{title}"]', array('title'=>'*'.$model->vendor.'*')), 'url' => ['/ratingmain', 'RatingMainSearch[vendor]' => $model->vendor]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'List [filtered on "{title}"]', array('title'=>$this->title)), 'url' => ['/ratingmain', 'RatingMainSearch[id]' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'List [filtered on "{title}"]', array('title'=>'*'.$model->fkRatingType->name.'*')), 'url' => ['/ratingmain', 'RatingMainSearch[fkRatingType]' => $model->fkRatingType->name]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="rating-main-view">
    <?php
    $btnGotoMainViewView=Html::a(Yii::t('app', 'Goto'), ['/ratingmain/view', 'id' => $model->id], ['class' => 'btn btn-warning']);
    $btnUpdMainViewView=Html::a(Yii::t('app', 'Update'), ['/ratingmain/update', 'id' => $model->id], ['class' => 'btn btn-primary']);
    ?>
    <h1><?= $model->name . " ". $btnUpdMainViewView ?></h1>
    <?php
    echo $this->render('/ratingmain/view', [
        'model' => $model,
        'paramShowTheBreadcrumb' => false,
        'paramShowTheButtons' => false,
        'paramShowTheTitle' => false,
        'additionalFieldsMain' => $additionalFieldsMain,
        ]);
    ?>
    <hr>
    <?php
    $btnListRatingstarIndexView=Html::a(Yii::t('app', 'List'), ['/ratingstars/index', 'RatingStarsSearch[fk_rating_main_id]' => $model->id], ['class' => 'btn btn-info']);
    $btnAddRatingstar=Html::a(Yii::t('app', 'Add rating'), ['/ratingstars/create', 'fk_rating_main_id' => $model->id], ['class' => 'btn btn-success']);
    ?>
    <h2>User rating <?= $btnListRatingstarIndexView . " " . $btnAddRatingstar  ?></h2>
    <?php
    foreach($model_stars as $key=>$model_star)
    {
        $modelVAdditionalKeyValueEntries = VAdditionalKeyValueEntries::find()->select(['fieldname', 'value'])->where(["fk_rating_stars_id" => $model_star->id])->asArray()->all();
        $additionalFieldsStars = array();
        foreach($modelVAdditionalKeyValueEntries as $key=>$value) { $additionalFieldsStars[$value["fieldname"]] = $value["value"]; }

        echo $this->render('/ratingstars/view', [
            'model' => $model_star,
            'paramShowTheBreadcrumb' => false,
            'paramShowTheButtons' => false,
            'paramShowTheTitle' => false,
            'additionalFieldsStars' => $additionalFieldsStars,
            ]);
        }
        ?>
    <hr>
    <?php
    $btnListImagesIndexView=Html::a(Yii::t('app', 'List'), ['/ratingimages/index', 'RatingImagesSearch[fk_rating_main_id]' => $model->id], ['class' => 'btn btn-info']);
    $btnAddImage=Html::a(Yii::t('app', 'Add image'), ['/ratingimages/create', 'fk_rating_main_id' => $model->id], ['class' => 'btn btn-success']);
    ?>
    <h2>Pictures <?= $btnListImagesIndexView . " " . $btnAddImage?></h2>
    <?php
    foreach($model_images as $key=>$model_image)
    {
        echo $this->render('/ratingimages/view', [
            'model' => $model_image,
            'paramShowTheBreadcrumb' => false,
            'paramShowTheButtons' => false,
            'paramShowTheTitle' => false,
        ]);
    }
    ?>

</div>
<?php
$this->title = $model->name;
?>