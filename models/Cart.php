<?php
namespace pistol88\cart\models;

use pistol88\cart\models\CartElement;
use yii;

class Cart extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'cart';
    }
    
    public function rules()
    {
        return [
            [['created_time', 'user_id'], 'required', 'on' => 'create'],
            [['updated_time', 'created_time'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => yii::t('cart', 'ID'),
            'user_id' => yii::t('cart', 'User ID'),
            'created_time' => yii::t('cart', 'Created Time'),
            'updated_time' => yii::t('cart', 'Updated Time'),
        ];
    }

    public static function find()
    {
        return new tools\CartQuery(get_called_class());
    }

    public function getElements()
    {
        return $this->hasMany(CartElement::className(), ['cart_id' => 'id']);
    }

    public function beforeDelete()
    {
        foreach ($this->elements as $elem) {
            $elem->delete();
        }
        
        return true;
    }
}
