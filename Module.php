<?php

namespace pistol88\cart;

use Yii;

class Module extends \yii\base\Module {

    public $currency = NULL;
    public $currencyPosition = 'after';
    public $priceFormat = [2, '.', ''];

    public function init() {
        parent::init();
    }
}
