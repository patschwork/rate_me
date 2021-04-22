<?php

/* @var $this yii\web\View */

$this->title = 'Rate me';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Rate me!</h1>

        <p class="lead">Tasted something good or bad? Give it a rate! :-)</p>

        <!-- <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p> -->
    </div>

    <div class="body-content">

        <?php 
        // echo "<a class=\"btn btn-default\" href=\"" . Yii::$app->urlManager->createUrl(["ratingmain"]) . "\">Main &raquo;</a><br>";
        // echo "<a class=\"btn btn-default\" href=\"" . Yii::$app->urlManager->createUrl(["ratingimages"]) . "\">Images</a><br>";
        // echo "<a class=\"btn btn-default\" href=\"" . Yii::$app->urlManager->createUrl(["ratingstars"]) . "\">Stars</a><br>";
        echo "<a class=\"btn btn-default\" href=\"" . Yii::$app->urlManager->createUrl(["gii"]) . "\">GII</a><br>";
        ?>
       

        <div class="row">
            <div class="col-lg-4">
                <h2>Add something you want to rate</h2>

                <p></p>

                <p><?="<a class=\"btn btn-default\" href=\"" . Yii::$app->urlManager->createUrl(["ratingmain"]) . "\">Main &raquo;</a><br>"?></p>
            </div>
            <div class="col-lg-4">
                <h2>Add images<br><br></h2>

                <p></p>

                <p><?= "<a class=\"btn btn-default\" href=\"" . Yii::$app->urlManager->createUrl(["ratingimages"]) . "\">Images</a><br>" ?></p>
            </div>
            <div class="col-lg-4">
                <h2>Comment and give stars<br><br></h2>

                <p></p>

                <p><?= "<a class=\"btn btn-default\" href=\"" . Yii::$app->urlManager->createUrl(["ratingstars"]) . "\">Stars</a><br>" ?></p>
            </div>            
            <div class="col-lg-4">
                <h2>Declare the type<br><br></h2>

                <p></p>

                <p><?= "<a class=\"btn btn-default\" href=\"" . Yii::$app->urlManager->createUrl(["ratingtype"]) . "\">Types</a><br>" ?></p>
            </div>
            <div class="col-lg-4">
                <h2>Import data from external source<br><br></h2>

                <p></p>

                <p><?= "<a class=\"btn btn-default\" href=\"" . Yii::$app->urlManager->createUrl(["importer"]) . "\">Importer</a><br>" ?></p>
            </div>
        </div>

    </div>
</div>
