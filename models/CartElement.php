<?php
namespace pistol88\cart\models;

use pistol88\cart\models\Cart;
use pistol88\cart\events\CartElement as CartElementEvent;
use yii;

class CartElement extends \yii\db\ActiveRecord
{
    const EVENT_ELEMENT_UPDATE = 'element_count';
    const EVENT_ELEMENT_DELETE = 'element_delete';
    
    public function getCartId()
    {
        return $this->id;
    }

    public function getOptions()
    {
        return json_decode($this->options, true);
    }
    
    public function getCartElementModel()
    {
        $model = '\\'.$this->model;
        if(is_string($this->model) && class_exists($this->model)) {
            $productModel = new $model();
            if ($productModel = $productModel::findOne($this->item_id)) {
                $model = $productModel;
            }
            else {
                throw new \yii\base\Exception('Element model not found');
            }
        }
        else {
            throw new \yii\base\Exception('Unknow element model');
        }
        return $model;
    }
    
    public function behaviors()
    {
        return yii::$app->cart->elementBehaviors;
    }
    
    public static function tableName()
    {
        return 'cart_element';
    }

    public function getCost()
    {
        return $this->price*$this->count;
    }

    public function getCostFormatted()
    {
        $price = $this->getCost();
        $currency = yii::$app->cart->currency;
        if (Yii::$app->cart->currencyPosition == 'after') {
            $price = "$price$currency";
        } else {
            $price = "$currency$price";
        }
        return $price;
    }
    
    public function rules()
    {
        return [
            [['cart_id', 'model', 'item_id'], 'required'],
            [['model'], 'validateModel'],
            [['hash', 'options'], 'string'],
            [['price'], 'double'],
            [['item_id', 'count', 'parent_id'], 'integer'],
        ];
    }

    public function validateModel($attribute, $param)
    {
        $model = $this->model;
        if (class_exists($model)) {
            $elementModel = new $model();
            if (!$elementModel instanceof \pistol88\cart\interfaces\CartElement) {
                $this->addError($attribute, 'Model implement error');
            }
        } else {
            $this->addError($attribute, 'Model not exists');
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => yii::t('cart', 'ID'),
            'parent_id' => yii::t('cart', 'Parent element'),
            'price' => yii::t('cart', 'Price'),
            'hash' => yii::t('cart', 'Hash'),
            'model' => yii::t('cart', 'Model name'),
            'cart_id' => yii::t('cart', 'Cart ID'),
            'item_id' => yii::t('cart', 'Item ID'),
            'count' => yii::t('cart', 'Count'),
        ];
    }

    public function getCart()
    {
        return $this->hasOne(Cart::className(), ['id' => 'cart_id']);
    }
    
    public function setCart($cart)
    {
        return $this->populateRelation('cart', $cart);
    }
    
    public function beforeSave($insert)
    {
        $cart = yii::$app->cart;

        $cart->cart->updated_time = time();
        $cart->cart->save();

        $elementEvent = new CartElementEvent(['element' => $this]);
        
        $this->trigger(self::EVENT_ELEMENT_UPDATE, $elementEvent);

        if($elementEvent->stop) {
            return false;
        } else {
            return true;
        }
    }
    
    public function beforeDelete()
    {
        $elementEvent = new CartElementEvent(['element' => $this]);
        
        $this->trigger(self::EVENT_ELEMENT_DELETE, $elementEvent);
        
        if($elementEvent->stop) {
            return false;
        } else {
            return true;
        }
    }
}
