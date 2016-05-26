<?php
$this->title = Yii::t('easyii', 'Create article');
?>
<?= $this->render('_menu') ?>
<?= $this->render('_form', ['model' => $model]);