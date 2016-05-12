<?php
namespace pistol88\cart;

use yii\base\Component;
use yii\di\ServiceLocator;
use pistol88\cart\events\Cart as CartEvent;
use pistol88\cart\events\CartElement as CartElementEvent;
use yii;

class Cart extends Component
{
    const EVENT_CART_INIT = 'cart_init';
    const EVENT_CART_TRUNCATE = 'cart_truncate';
    const EVENT_CART_COST = 'cart_cost';
    const EVENT_CART_COUNT = 'cart_count';
    const EVENT_CART_PUT = 'cart_put';
    
    private $_cost = 0;
    private $_element = null;
    private $_cart = null;

    public $currency = NULL;
    public $elementBehaviors = [];
    public $currencyPosition = 'after';
    public $priceFormat = [2, '.', ''];
    
    public function __construct(interfaces\CartService $cartService, interfaces\ElementService $elementService, $config = [])
    {
        $this->_cart = $cartService->my();
        $this->_element = $elementService;
        parent::__construct($config);
    }
    
    public function init()
    {
        $this->trigger(self::EVENT_CART_INIT, new CartEvent(['cart' => $this->_cart]));
        $this->_update();
        
        return $this;
    }

    public function put(\pistol88\cart\interfaces\CartElement $model, $count = 1, $options = [])
    {
        if (!$elementModel = $this->_cart->getElement($model, $options)) {
            $elementModel = $this->_element;
            $elementModel->setCount((int)$count);
            $elementModel->setPrice($model->getCartPrice());
            $elementModel->setItemId($model->getCartId());
            $elementModel->setModel(get_class($model));
            $elementModel->setOptions($options);

            $elementEvent = new CartElementEvent(['element' => $elementModel]);
            $this->trigger(self::EVENT_CART_PUT, $elementEvent);
            
            if(!$elementEvent->stop) {
                try {
                    $this->cart->put($elementModel);
                } catch (Exception $e) {
                    throw new \yii\base\Exception(current($e->getMessage()));
                }
            }
        } else {
            $elementModel->countIncrement($count);
        }
        return $elementModel;
    }

    public function getElements()
    {
        return $this->_cart->elements;
    }

    public function getCount()
    {
        $count = $this->_cart->getCount();

        $cartEvent = new CartEvent(['cart' => $this->_cart, 'count' => $count]);
        $this->trigger(self::EVENT_CART_COUNT, $cartEvent);
        $count = $cartEvent->count;

        return $count;
    }
    
    public function getCost()
    {
        $cost = $this->_cart->getElements()->sum('price*count');
        
        $cartEvent = new CartEvent(['cart' => $this->_cart, 'cost' => $cost]);
        $this->trigger(self::EVENT_CART_COST, $cartEvent);
        $cost = $cartEvent->cost;
        
        $this->_cost = $cost;

        return $this->_cost;
    }
    
    public function getCostFormatted()
    {
        $price = number_format($this->getCost(), $this->priceFormat[0], $this->priceFormat[1], $this->priceFormat[2]);

        if ($this->currencyPosition == 'after') {
            return "$price{$this->currency}";
        } else {
            return "{$this->currency}$price";
        }
    }
    
    public function getElementsByModel(\pistol88\cart\interfaces\CartElement $model)
    {
        return $this->_cart->getElementByModel($model);
    }

    public function getElementById($id)
    {
        return $this->_cart->getElementById($id);
    }
    
    public function getCart()
    {
        return $this->_cart;
    }
    
    public function truncate()
    {
        $this->trigger(self::EVENT_CART_TRUNCATE, new CartEvent(['cart' => $this->_cart]));
        $truncate = $this->_cart->truncate();
        $this->_update();
        
        return $truncate;
    }
    
    private function _update()
    {
        $this->_cost = $this->_cart->getCost();
    }
}
