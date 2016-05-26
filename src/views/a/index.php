<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */

$moduleId = $this->context->module->id;

?>
<?= Html::a('Edit Posts', ['/admin/'.$moduleId.'/items']); ?>

<?= Html::a('Edit Categories', ['/admin/'.$moduleId.'/category']);
