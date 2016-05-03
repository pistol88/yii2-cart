<?php

use yii\db\Schema;
use yii\db\Migration;

class m150714_050431_pistol88Cart extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%cart}}',
            [
                'id' => Schema::TYPE_PK,
                'user_id' => Schema::TYPE_VARCHAR . '(55) NOT NULL',
                'created_time' => Schema::TYPE_INTEGER . ' (11) NOT NULL',
                'updated_time' => Schema::TYPE_INTEGER . ' (11) NOT NULL'
            ],
            $tableOptions
        );
        
        $this->createIndex('id', '{{%cart}}', 'id');
        $this->createIndex('user_id', '{{%cart}}', 'user_id');
        
        $this->createTable(
            '{{%cart_element}}',
            [
                'id' => Schema::TYPE_PK,
                'parent_id' => Schema::TYPE_INTEGER . '(55) NOT NULL',
                'model' => Schema::TYPE_VARCHAR . ' (110) NOT NULL',
                'cart_id' => Schema::TYPE_INTEGER . ' (11) NOT NULL',
                'item_id' => Schema::TYPE_INTEGER . ' (55) NOT NULL',
                'count' => Schema::TYPE_INTEGER . ' (11) NOT NULL',
                'price' => Schema::TYPE_DECIMAL . ' (11, 2) NOT NULL',
                'description' => Schema::TYPE_VARCHAR . ' (255) NOT NULL',
            ],
            $tableOptions
        );
        
        $this->createIndex('id', '{{%cart}}', 'id');
        
        $this->addForeignKey(
            'elem_to_cart', '{{%cart_element}}', 'cart_id', '{{%cart}}', 'id', 'CASCADE', 'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%cart}}');
        $this->dropTable('{{%cart_element}}');
    }
}
