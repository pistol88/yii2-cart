<?php

namespace pistol88\cart\assets; 

use yii\web\AssetBundle;

class Asset extends AssetBundle {

    public $depends = [
        'yii\web\JqueryAsset',
		'yii\bootstrap\BootstrapAsset'
    ];

    public function init() {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
