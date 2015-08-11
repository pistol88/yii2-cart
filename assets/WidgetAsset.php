<?php

namespace pistol88\cart\assets; 

use yii\web\AssetBundle;

class WidgetAsset extends AssetBundle {

    public $depends = [
        'pistol88\cart\assets\Asset'
    ];
    public $js = [
        'js/pistol88Cart.js',
    ];
    public $css = [
        'css/pistol88Cart.css',
    ];

    public function init() {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
