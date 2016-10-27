<?php
namespace pistol88\cart\events;

use yii\base\Event;

class CartGroupModels extends Event
{
    public $cost;
    public $cart;
    public $model;
}