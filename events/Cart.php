<?php
namespace pistol88\cart\events;

use yii\base\Event;

class Cart extends Event
{
    public $cart;
    public $cost;
    public $count;
}