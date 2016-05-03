<?php
namespace pistol88\cart;
use Yii;
use yii\base\Component;
use pistol88\cart\models\Cart as CartModel;
use pistol88\cart\events\Cart as CartEvent;
use pistol88\cart\models\CartElement;
use pistol88\cart\events\CartElement as CartElementEvent;

class Cart extends Component {
    const EVENT_CART_INIT = 'cart_init';
    const EVENT_CART_TRUNCATE = 'cart_truncate';
    const EVENT_CART_COST = 'cart_cost';
    const EVENT_CART_COUNT = 'cart_count';
    const EVENT_CART_PUT = 'cart_put';
    
    protected $_cart = null;
    protected $_cost = 0;
    
    public $currency = NULL;
    public $behaviors = [];
    public $elementBehaviors = [];
    public $currencyPosition = 'after';
    public $priceFormat = [2, '.', ''];
    public $id = null;

    public function behaviors() {
        return $this->behaviors;
	}
    
    public function init() {
        if($this->_cart == NULL) {
            $this->_cart = CartModel::find()->my();
            $this->id = $this->_cart->id;
            
            $this->trigger(self::EVENT_CART_INIT, new CartEvent(['cart' => $this->_cart]));
        }
        return $this;
    }
    
    public function getCost() {
        $cost = $this->_cart->getElements()->sum('price*count');

        $cartEvent = new CartEvent(['cart' => $this->_cart, 'cost' => $cost]);

        $this->trigger(self::EVENT_CART_COST, $cartEvent);

        $cost = $cartEvent->cost;

        $this->setCost($cost);

        return $this->_cost;
    }

    public function setCost($cost) {
        $this->_cost = $cost;
    }

    public function getCart() {
        return $this->_cart;
    }

    public function getCostFormatted() {
        $price = number_format($this->getCost(), $this->priceFormat[0], $this->priceFormat[1], $this->priceFormat[2]);

        if ($this->currencyPosition == 'after') {
            return "$price{$this->currency}";
        } else {
            return "{$this->currency}$price";
        }
    }

    public function getCount() {
        $count = intval($this->_cart->getElements()->sum('count'));

        $cartEvent = new CartEvent(['cart' => $this->_cart, 'count' => $count]);

        $this->trigger(self::EVENT_CART_COUNT, $cartEvent);

        $count = $cartEvent->count;

        return $count;
    }

    public function getElements($withModel = true) {
        $returnModels = [];
        foreach ($this->_cart->elements as $element) {
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

    public function getElementByModel(\pistol88\cart\interfaces\CartElement $model) {
        return $this->_cart->getElements()->andWhere(['model' => get_class($model), 'item_id' => $model->id])->one();
    }

    public function getElementById($id) {
        return $this->_cart->getElements()->andWhere(['id' => $id])->one();
    }

    public function haveModelElements($modelName) {
        if ($this->_cart->getElements()->andWhere(['model' => $modelName, 'price' => 0])->one()) {
            return true;
        } else {
            return false;
        }
    }

    public function put(\pistol88\cart\interfaces\CartElement $model, $count = 1, $description = '') {
        if (!$elementModel = $this->hasElement($model, $description)) {
            $elementModel = new CartElement;
            $elementModel->count = (int)$count;
            $elementModel->description = htmlspecialchars($description);
            $elementModel->price = $model->getCartPrice();
            $elementModel->item_id = $model->id;
            $elementModel->model = get_class($model);
            $elementModel->link('cart', $this->_cart);
            
            $elementEvent = new CartElementEvent(['element' => $elementModel]);
            $this->trigger(self::EVENT_CART_PUT, $elementEvent);
            
            if(!$elementEvent->stop) {
                if ($elementModel->validate() && $elementModel->save()) {
                    return $elementModel;
                } else {
                    throw new \yii\base\Exception(current($elementModel->getFirstErrors()));
                }
            }
        } else {

            $elementModel->count += (int)$count;
            $elementModel->save();

            return $elementModel;
        }
    }
    
    public function truncate() {
        foreach($this->_cart->elements as $element) {
            $element->delete();
        }
        
        $this->trigger(self::EVENT_CART_TRUNCATE, new CartEvent(['cart' => $this->_cart]));
        
        return $this->_cart;
    }

    public function hasElement(\pistol88\cart\interfaces\CartElement $model, $description = '') {
        return $this->_cart->getElements()->where(['description' => $description, 'model' => get_class($model), 'item_id' => $model->id])->one();
    }
}