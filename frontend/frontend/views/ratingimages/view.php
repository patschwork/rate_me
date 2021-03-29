<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\RatingImages */

$this->title = $model->name;
$showTheBreadcrumb = true;
if (isset($paramShowTheBreadcrumb))
{
    $showTheBreadcrumb = $paramShowTheBreadcrumb;
}
if ($showTheBreadcrumb)
{
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'List'), 'url' => ['/ratingmain/index', 'RatingMainSearch[id]' => $model->fk_rating_main_id]];
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Overview'), 'url' => ['/overview/view', 'id' => $model->fk_rating_main_id]];
     $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rating Images'), 'url' => ['index', 'RatingImagesSearch[fk_rating_main_id]' => $model->fk_rating_main_id]];
    $this->params['breadcrumbs'][] = $this->title;
}

\yii\web\YiiAsset::register($this);
?>
<div class="rating-images-view">

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
            'is_main_picture:boolean',
            'description',
            'filename',
            //'file_blob',
            // array(
            //     'format' => 'image',
            //    'attribute'=>'file_blob',
            // ),
            'fk_rating_main_id',
        ],
        'template' => "<tr><th style='width: 25%;'>{label}</th><td>{value}</td></tr>"
    ]) ?>

    <?php

        $temp = $model['file_blob'];
        $html_img_dimensions = " ".$model['image_htmlimg_width_heigt'] . " ";

        $factor = 0.25; // Dieser Faktor als Default...

        $img_width = $model->image_width;
        $img_heigth = $model->image_height;

        // Defaultwerte setzen
        $img_width_new = $img_width;
        $img_heigth_new = $img_heigth;

        // Aus Android wird das Cookie nicht gesetzt... Daher prüfen ob vorhanden
        if (isset($_COOKIE['window_width']))
        {
            if ($img_width>=$_COOKIE['window_width'])
            {
                // Faktor auf Basis der Bildschirmgröße berechnen. 
                // Toleranz auch abziehen (($_COOKIE['window_width']*0.5))
                $factor = ($_COOKIE['window_width'] - ($_COOKIE['window_width']*0.5)) / $img_width;
                // Neue Größe als Ganzzahl berechnen
                $img_width_new = abs($img_width * $factor);
                $img_heigth_new = abs($img_heigth * $factor);
            }   
        }

        $img_mime = $model->image_mime;

        echo "<img src=\"data:$img_mime;base64," . base64_encode(stream_get_contents($temp) ) . '"' . " height=\"$img_heigth_new\" width=\"$img_width_new\"" . '/>';
    ?>
</div>



<?php
// Hier wird die Auflösung des aktuellen Browserfensters ermittelt und in ein Cookie geschrieben
// Quelle: https://stackoverflow.com/questions/1504459/getting-the-screen-resolution-using-php
$this->registerJs(
    "document.cookie = 'window_width='+window.innerWidth+'; expires=Fri, 3 Aug 2901 20:47:11 UTC; path=/';",
    yii\web\View::POS_BEGIN
);
?>