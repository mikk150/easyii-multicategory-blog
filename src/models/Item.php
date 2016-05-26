<?php

namespace mikk150\blog\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\easyii\behaviors\SeoBehavior;
use yii\easyii\behaviors\Taggable;
use yii\easyii\models\Photo;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use qwestern\easyii\article\comments\models\ArticleItem;

class Item extends ArticleItem
{
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    private $_categoryIds;

    public static function tableName()
    {
        return 'easyii_article_items';
    }

    public function rules()
    {
        return [
            [['text', 'title'], 'required'],
            [['title', 'short', 'text'], 'trim'],
            ['title', 'string', 'max' => 128],
            ['image', 'image'],
            [['views', 'time', 'status'], 'integer'],
            ['time', 'default', 'value' => time()],
            ['slug', 'match', 'pattern' => self::$SLUG_PATTERN, 'message' => Yii::t('easyii', 'Slug can contain only 0-9, a-z and "-" characters (max: 128).')],
            ['slug', 'default', 'value' => null],
            ['status', 'default', 'value' => self::STATUS_ON],
            ['tagNames', 'safe'],
            ['categoryIds', 'safe'],
        ];
    }

    public function setCategoryIds($value)
    {
        $this->_categoryIds=$value;
    }
    public function getCategoryIds()
    {
        if ($this->_categoryIds) {
            return $this->_categoryIds;
        }
        return ArrayHelper::getColumn($this->itemCategories, 'category_id');
    }

    public function getItemCategories()
    {
        return $this->hasMany(ItemCategory::className(), ['item_id' => 'item_id']);
    }
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['category_id' => 'category_id'])->via('itemCategories');
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['category_id' => 'category_id'])->viaTable('easyii_article_item_category', ['item_id' => 'item_id'], function ($query) {
            $query->where('`order`=0');
        });
        return $this->getItemCategories()->alias('main_category')->on('main_category.order=0');
    }
}
