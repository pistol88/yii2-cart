<?php

namespace pistol88\cart\models; 

use pistol88\cart\models\CartElement;
use Yii;

class Cart extends \yii\db\ActiveRecord {
    
    private static $_instance = null;
    
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

    public static function my() {
        if (self::$_instance === null) {
            self::$_instance = Cart::find()->my();
        }
        return self::$_instance;
    }
    
    public function getPrice() {
        return $this->hasMany(CartElement::className(), ['cart_id' => 'id'])->sum('price*count');
    }

    public function getPriceFormatted() {
        $priceFormat = Yii::$app->getModule('cart')->priceFormat;
        $price = number_format($this->getPrice(), $priceFormat[0], $priceFormat[1], $priceFormat[2]);
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
        $cartModel = Cart::my();

        if (!$elementModel = $this->getElementByModel($model)) {
            $elementModel = new CartElement;
            $data = [];
            $data['count'] = (int)$count;
            $data['price'] = $model->getCartPrice();
            $data['cart_id'] = $cartModel->id;
            $data['item_id'] = $model->id;
            $data['model'] = get_class($model);
            if ($elementModel->load(['CartElement' => $data]) && $elementModel->save()) {
                return $elementModel;
            } else {
                throw new \yii\base\Exception(current($elementModel->getFirstErrors()));
            }
        } else {
            $elementModel->count += (int)$count;
            $elementModel->save();
            return $element;
        }
    }
    
    public function getElements($withModel = true) {
        $returnModels = [];
        $elements = $this->hasMany(CartElement::className(), ['cart_id' => 'id'])->all();
        foreach ($elements as $element) {
            if ($withModel && class_exists($element->model)) {
                $model = '\\'.$element->model;
                $productModel = new $model();
                if ($productModel = $productModel::findOne($element->item_id)) {
                    $element->model = $productModel;
                }
            }
            $returnModels[$element->id] = $element;
        }
        return $returnModels;
    }

    public function getElementByModel(\pistol88\cart\models\tools\CartElementInterface $model) {
        return $this->hasMany(CartElement::className(), ['cart_id' => 'id'])->andWhere(['model' => get_class($model), 'item_id' => $model->id])->one();
    }
    
    public function getElementById($id) {
        return $this->hasMany(CartElement::className(), ['cart_id' => 'id'])->andWhere(['id' => $id])->one();
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