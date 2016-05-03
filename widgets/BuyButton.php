<?php

namespace pistol88\cart\widgets;

use yii\helpers\Html;
use yii\helpers\Url;

class BuyButton extends \yii\base\Widget {

    public $text = NULL;
    public $model = NULL;
    public $count = 1;
    public $description = '';
    public $cssClass = NULL;
    public $htmlTag = 'a';

    public function init() {
        parent::init();

        \pistol88\cart\assets\WidgetAsset::register($this->getView());

        if ($this->text === NULL) {
            $this->text = Yii::t('cart', 'Buy');
        }

        if ($this->cssClass === NULL) {
            $this->cssClass = 'btn btn-success';
        }
    }

    public function run() {
        if (!is_object($this->model) | !$this->model instanceof \pistol88\cart\interfaces\CartElement) {
            return false;
        }

        $model = $this->model;
        return Html::tag($this->htmlTag, $this->text, [
            'href' => Url::toRoute('/cart/element/create'),
            'class' => "pistol88-cart-buy-button pistol88-cart-buy-button{$this->model->id} {$this->cssClass}",
            'data-id' => $model->id,
            'data-count' => $this->count,
            'data-description' => $this->description,
            'data-model' => $model::className()
        ]);
    }

}
