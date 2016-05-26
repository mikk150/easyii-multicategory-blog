<?php

namespace mikk150\blog;

use yii\easyii\components\Module;

/**
*
*/
class BlogModule extends Module
{
    public $settings = [
        'categoryThumb' => true,
        'articleThumb' => true,
        'enablePhotos' => true,

        'enableShort' => true,
        'shortMaxLength' => 255,
        'enableTags' => true,

        'itemsInFolder' => false,
    ];

    public static $installConfig = [
        'title' => [
            'en' => 'Blog',
        ],
        'icon' => 'pencil',
        'order_num' => 65,
    ];
}
