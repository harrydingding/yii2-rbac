<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m181211_071417_rbac
 */
class m181211_071417_rbac extends Migration
{
    const TABLE_RULE       = '{{%yunz_auth_rule}}';
    const TABLE_ITEM       = '{{%yunz_auth_item}}';
    const TABLE_ITEM_CHILD = '{{%yunz_auth_item_child}}';
    const TABLE_ASSIGNMENT = '{{%yunz_auth_assignment}}';
    
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable(self::TABLE_RULE, [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);

        $this->createTable(self::TABLE_ITEM, [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
            'FOREIGN KEY ([[rule_name]]) REFERENCES ' . self::TABLE_RULE . ' ([[name]])' .
                $this->buildFkClause('ON DELETE SET NULL', 'ON UPDATE CASCADE'),
        ], $tableOptions);
        $this->createIndex('idx-auth_item-type', self::TABLE_ITEM, 'type');

        $this->createTable(self::TABLE_ITEM_CHILD, [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[parent]], [[child]])',
            'FOREIGN KEY ([[parent]]) REFERENCES ' . self::TABLE_ITEM . ' ([[name]])' .
                $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
            'FOREIGN KEY ([[child]]) REFERENCES ' . self::TABLE_ITEM . ' ([[name]])' .
                $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
        ], $tableOptions);

        $this->createTable(self::TABLE_ASSIGNMENT, [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY ([[item_name]], [[user_id]])',
            'FOREIGN KEY ([[item_name]]) REFERENCES ' . self::TABLE_ITEM . ' ([[name]])' .
                $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable(self::TABLE_ITEM);
        $this->dropTable(self::TABLE_RULE);
        $this->dropTable(self::TABLE_ITEM_CHILD);
        $this->dropTable(self::TABLE_ASSIGNMENT);
    }
    
    protected function buildFkClause($delete = '', $update = '')
    {
        return implode(' ', ['', $delete, $update]);
    }
}
