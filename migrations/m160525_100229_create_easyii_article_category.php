<?php

use yii\db\Migration;

/**
 * Handles the creation for table `easyii_article_category`.
 */
class m160525_100229_create_easyii_article_category extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('easyii_article_item_category', [
            'item_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
            'order' => $this->integer()->notNull(),
            'PRIMARY KEY(item_id, category_id)'
        ]);

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('easyii_article_category');
    }
}
