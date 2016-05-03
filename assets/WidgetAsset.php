<?php

namespace pistol88\cart\assets;

use yii\web\AssetBundle;

class WidgetAsset extends AssetBundle {

    public $depends = [
        'pistol88\cart\assets\Asset'
    ];

    public $js = [
        'js/scripts.js',
    ];
    public $css = [
        'css/styles.css',
    ];

    public function init() {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
