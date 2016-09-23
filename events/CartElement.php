<?php
namespace pistol88\cart\events;

use yii\base\Event;

class CartElement extends Event
{
    public $element;
    public $cost;
    public $stop;
}