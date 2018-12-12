<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m181211_070958_menu
 */
class m181211_070958_menu extends Migration
{
    const TBL_NAME = '{{%yunz_menu}}';
    
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => Schema::TYPE_PK,
            'label' => Schema::TYPE_STRING . '(128) NOT NULL',
            'parent_id' => Schema::TYPE_INTEGER. ' NULL',
            'url' => Schema::TYPE_STRING . ' NULL',
            'order_by' => Schema::TYPE_INTEGER . ' NULL',
            'icon' => Schema::TYPE_STRING . ' NULL',
            'data' => Schema::TYPE_TEXT . ' NULL',
            'sub_menu' => Schema::TYPE_BOOLEAN . ' NULL'
        ], $tableOptions);
        
        $this->insert(self::TBL_NAME, [
            'label' => 'Dashboard',
            'parent_id' => 0,
            'url' => 'site/index',
            'order_by' => '',
            'icon' => 'fa fa-dashboard'
        ]);
        
        $this->insert(self::TBL_NAME, [
            'label' => 'Menu',
            'parent_id' => 0,
            'url' => 'menu/index',
            'order_by' => '',
            'icon' => 'fa fa-bars'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
