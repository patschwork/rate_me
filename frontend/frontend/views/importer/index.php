<?php
/* @var $this yii\web\View */
use yii\helpers\Html
?>
<h1>importer/index</h1>

<p>
<?= Html::beginForm() ?>
<?= Html::textInput('import_nr_txt', $value = null); ?>
<?= Html::submitButton($content='Submit'); ?>
<?= Html::endForm() ?>

</p>
