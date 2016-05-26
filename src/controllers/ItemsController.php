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
    public function behaviors()
    {
        return [
            [
                'class' => SortableDateController::className(),
                'model' => Item::className(),
            ],
            [
            'class' => StatusController::className(),
            'model' => Item::className()
            ]
        ];
    }

    public function actionIndex($id = null)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Item::find()
        ]);
        if ($id) {
            $dataProvider->query->joinWith(['categories categories'], false)->where(['categories.category_id'=>$id]);
        }
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
    public function actionPhotos($id)
    {
        if (!($model = Item::findOne($id))) {
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        return $this->render('photos', [
            'model' => $model,
        ]);
    }

    public function actionClearImage($id)
    {
        $model = Item::findOne($id);

        if ($model === null) {
            $this->flash('error', Yii::t('easyii', 'Not found'));
        } else if ($model->image) {
            $model->image = '';
            if ($model->update()) {
                $this->flash('success', Yii::t('easyii', 'Image cleared'));
            } else {
                $this->flash('error', Yii::t('easyii', 'Update error. {0}', $model->formatErrors()));
            }
        }
        return $this->back();
    }

    public function actionDelete($id)
    {
        if (($model = Item::findOne($id))) {
            $model->delete();
        } else {
            $this->error = Yii::t('easyii', 'Not found');
        }
        return $this->formatResponse(Yii::t('app', 'Article deleted'));
    }

    public function actionUp($id)
    {
        return $this->move($id, 'up');
    }

    public function actionDown($id)
    {
        return $this->move($id, 'down');
    }

    public function actionOn($id)
    {
        return $this->changeStatus($id, Item::STATUS_ON);
    }

    public function actionOff($id)
    {
        return $this->changeStatus($id, Item::STATUS_OFF);
    }
}
