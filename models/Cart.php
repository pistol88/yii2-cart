<?php

namespace pistol88\cart\models;

use pistol88\cart\models\CartElement;

class Cart extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'cart';
    }
    
    public function rules() {
        return [
            [['created_time', 'user_id'], 'required', 'on' => 'create'],
            [['updated_time', 'created_time'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('cart', 'ID'),
            'user_id' => Yii::t('cart', 'User ID'),
            'created_time' => Yii::t('cart', 'Created Time'),
            'updated_time' => Yii::t('cart', 'Updated Time'),
        ];
    }

    public static function find() {
        return new tools\CartQuery(get_called_class());
    }

    public function getElements() {
        return $this->hasMany(CartElement::className(), ['cart_id' => 'id']);
    }

    public function beforeDelete() {
        foreach ($this->elements as $elem) {
            $elem->delete();
        }
        
        return true;
    }
}