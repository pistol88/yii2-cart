<?php

namespace pistol88\cart\models;

use pistol88\cart\models\CartElement;
use Yii;

class Cart extends \yii\db\ActiveRecord {
    
    public static function tableName() {
        return 'cart';
    }

    public static function my() {
        return Cart::find()->my();
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

    public function getPrice() {
        return $this->hasMany(CartElement::className(), ['cart_id' => 'id'])->sum('price*count');
    }

    public function getPriceFormatted() {

        $price = $this->getPrice();
        $priceFormat = Yii::$app->getModule('cart')->priceFormat;
        $price = number_format($price, $priceFormat[0], $priceFormat[1], $priceFormat[2]);
        $currency = Yii::$app->getModule('cart')->currency;
        if (Yii::$app->getModule('cart')->currencyPosition == 'after') {
            return "$price $currency";
        } else {
            return "$currency $price";
        }
    }

    public function getCount() {
        return intval($this->hasMany(CartElement::className(), ['cart_id' => 'id'])->sum('count'));
    }

    public function add(\pistol88\cart\models\tools\CartElementInterface $model, $count = 1) {
        $cartModel = Cart::find()->my();

        if (!$elementModel = CartElement::find()->andWhere(['cart_id' => $cartModel->id, 'model' => get_class($model), 'item_id' => $model->id])->one()) {
            $elementModel = new CartElement;
            $elementModel->count = (int)$count;
            $elementModel->price = $model->getCartPrice();
            $elementModel->cart_id = $cartModel->id;
        } else {
            $elementModel->count += (int)$count;
        }

        if ($elementModel->save()) {
            return $elementModel;
        } else {
            return false;
        }
    }
    
    public function getElements($withModel = true) {
        $returnModels = [];
        $elements = $this->hasMany(CartElement::className(), ['cart_id' => 'id'])->all();
        foreach ($elements as $element) {
            if ($withModel && class_exists($element->model)) {
                $model = $element->model;
                $productModel = new $model();
                if ($productModel = $productModel::findOne($element->item_id)) {
                    $element->model = $productModel;
                }
            }
            $returnModels[$element->id] = $element;
        }
        return $returnModels;
    }

    public function haveModelElements($modelName) {
        if ($this->hasMany(CartElement::className(), ['cart_id' => 'id'])->andWhere(['model' => $modelName])->one()) {
            return true;
        } else {
            return false;
        }
    }

    public function beforeDelete() {
        CartElement::find()->where(['cart_id' => $this->id])->delete();
    }

}
