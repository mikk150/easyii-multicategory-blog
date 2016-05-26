<?php
use yii\easyii\helpers\Image;
use yii\easyii\widgets\DateTimePicker;
use yii\easyii\widgets\TagsInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\easyii\widgets\Redactor;
use yii\easyii\widgets\SeoForm;
use dosamigos\selectize\SelectizeDropDownList;
use mikk150\blog\models\Category;
use yii\helpers\ArrayHelper;

$module = $this->context->module->id;

?>
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'options' => ['enctype' => 'multipart/form-data', 'class' => 'model-form']
    ]); ?>

    <?= $form->field($model, 'title') ?>

    <?php if ($this->context->module->settings['articleThumb']) : ?>
        <?php if ($model->image) : ?>
            <img src="<?= Image::thumb($model->image, 240) ?>">
            <a href="<?= Url::to(['/admin/'.$module.'/items/clear-image', 'id' => $model->primaryKey]) ?>" class="text-danger confirm-delete" title="<?= Yii::t('easyii', 'Clear image')?>"><?= Yii::t('easyii', 'Clear image')?></a>
        <?php endif; ?>
        <?= $form->field($model, 'image')->fileInput() ?>
    <?php endif; ?>

    <?php if ($this->context->module->settings['enableShort']) : ?>
        <?= $form->field($model, 'short')->textarea() ?>
    <?php endif; ?>

    <?= $form->field($model, 'text')->widget(Redactor::className(), [
        'options' => [
            'minHeight' => 400,
            'imageUpload' => Url::to(['/admin/redactor/upload', 'dir' => 'article'], true),
            'fileUpload' => Url::to(['/admin/redactor/upload', 'dir' => 'article'], true),
            'plugins' => ['fullscreen']
        ]
    ]) ?>

    <?= $form->field($model, 'time')->widget(DateTimePicker::className()); ?>

    <?= ''/*$form->field($model, 'categoryIds')->widget(Select2::className(), [
        'options' => [
            'multiple' => true,
        ],
        'clientOptions' => [
            'tags' => ArrayHelper::map(Category::find()->all(), 'category_id', 'title'),
            'insertTag' => 'function (data, tag) {data.push(tag);}'
        ]
    ])*/ ?>

    <?= $form->field($model, 'categoryIds')->widget(SelectizeDropDownList::className(),[
        'options' => ['multiple' => true],
        'items' => ArrayHelper::map(Category::find()->all(), 'category_id', 'title')
    ]) ?>

    <?php if ($this->context->module->settings['enableTags']) : ?>
        <?= $form->field($model, 'tagNames')->widget(TagsInput::className()) ?>
    <?php endif; ?>

    <?php if (IS_ROOT) : ?>
        <?= $form->field($model, 'slug') ?>
        <?= SeoForm::widget(['model' => $model]) ?>
    <?php endif; ?>

<?= Html::submitButton(Yii::t('easyii', 'Save'), ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>