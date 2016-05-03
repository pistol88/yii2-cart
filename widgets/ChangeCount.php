<?php

namespace pistol88\cart\widgets; 

use yii\helpers\Url;
use yii\helpers\Html;

class ChangeCount extends \yii\base\Widget {

    public $model = NULL;
    public $lineSelector = 'li';
    public $downArr = '⟨';
    public $upArr = '⟩';
    public $cssClass = 'pistol88-change-count';
    public $defaultValue = 1;

    public function init() {
        parent::init();

        \pistol88\cart\assets\WidgetAsset::register($this->getView());
    }

    public function run() {
        if($this->model instanceof \pistol88\cart\models\CartElement) {
            $input = Html::activeTextInput($this->model, 'count', [
                'class' => 'pistol88-cart-element-count',
                'data-line-selector' => $this->lineSelector,
                'data-id' => $this->model->id,
                'data-href' => Url::toRoute("/cart/element/update"),
            ]);
        }
        else {
            $input = Html::input('number', 'count', $this->defaultValue, [
                'class' => 'pistol88-cart-element-before-count',
                'data-line-selector' => $this->lineSelector,
                'data-id' => $this->model->id,
            ]);
        }

        
        $downArr = Html::a($this->downArr, '#', ['class' => 'pistol88-arr pistol88-downArr']);
        $upArr = Html::a($this->upArr, '#', ['class' => 'pistol88-arr pistol88-upArr']);
        
        return Html::tag('div', $downArr.$input.$upArr, ['class' => $this->cssClass]);
    }

}
