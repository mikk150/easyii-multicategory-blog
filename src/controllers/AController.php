<?php

namespace mikk150\blog\controllers;

use yii\easyii\components\Controller;

/**
*
*/
class AController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
