<?php

namespace mikk150\blog\controllers;

use yii\easyii\behaviors\SortableDateController;
use yii\easyii\behaviors\StatusController;
use yii\easyii\helpers\Image;

use yii\easyii\components\Controller;
use yii\data\ActiveDataProvider;
use mikk150\blog\models\Item;
use mikk150\blog\models\Category;
use mikk150\blog\models\ItemCategory;
use yii\widgets\ActiveForm;
use yii\web\UploadedFile;
use Yii;

/**
*
*/
class ItemsController extends Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Item::find()
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate()
    {
        $model = new Item;

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {

                if (isset($_FILES) && $this->module->settings['articleThumb']) {
                    $model->image = UploadedFile::getInstance($model, 'image');
                    if ($model->image && $model->validate(['image'])) {
                        $model->image = Image::upload($model->image, 'article');
                    } else {
                        $model->image = '';
                    }
                }

                if ($model->save()) {

                    foreach ($model->categoryIds as $order => $category) {
                        $itemCategory=new ItemCategory([
                            'item_id' => $model->primaryKey,
                            'category_id' => $category,
                            'order' => $order
                        ]);
                        $itemCategory->save();
                    }

                    $this->flash('success', Yii::t('easyii', 'Article created'));
                    return $this->redirect(['update', 'id' => $model->primaryKey]);
                } else {
                    $this->flash('error', Yii::t('easyii', 'Create error. {0}', $model->formatErrors()));
                    return $this->refresh();
                }
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        if (!($model = Item::findOne($id))) {
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                if (isset($_FILES) && $this->module->settings['articleThumb']) {
                    $model->image = UploadedFile::getInstance($model, 'image');
                    if ($model->image && $model->validate(['image'])) {
                        $model->image = Image::upload($model->image, 'article');
                    } else {
                        $model->image = $model->oldAttributes['image'];
                    }
                }

                if ($model->save()) {
                    foreach ($model->itemCategories as $itemCategory) {
                        $itemCategory->delete();
                    }

                    foreach ($model->categoryIds as $order => $category) {
                        $itemCategory=new ItemCategory([
                            'item_id' => $model->primaryKey,
                            'category_id' => $category,
                            'order' => $order
                        ]);
                        $itemCategory->save();
                    }

                    $this->flash('success', Yii::t('easyii', 'Article updated'));
                    return $this->redirect(['update', 'id' => $model->primaryKey]);
                } else {
                    $this->flash('error', Yii::t('easyii', 'Update error. {0}', $model->formatErrors()));
                    return $this->refresh();
                }
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
}
