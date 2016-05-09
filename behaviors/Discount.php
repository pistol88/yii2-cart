<?php
namespace pistol88\cart\behaviors;

use yii\base\Behavior;
use pistol88\cart\Cart;
use yii;

class Discount extends Behavior
{

    public $persent = 0;

    public function events()
    {
        return [
            Cart::EVENT_CART_COST => 'doDiscount'
        ];
    }

    public function doDiscount($event)
    {
        if($this->persent > 0 && $this->persent <= 100 && $event->cost > 0) {
            $event->cost = $nightPrice;
        }

        return $this;
    }
}
