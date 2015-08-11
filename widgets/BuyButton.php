<?php

namespace pistol88\cart\widgets;

use yii\helpers\Html;

class BuyButton extends \yii\base\Widget {

    public $text = NULL;
    public $model = NULL;

    public function init() {
        parent::init();

        \pistol88\cart\assets\WidgetAsset::register($this->getView());

        if ($this->text == NULL) {
            $this->text = Yii::t('cart', 'Buy');
        }
    }

    public function run() {
        if (!is_object($this->model) | !$this->model instanceof \pistol88\cart\models\tools\CartElementInterface) {
            return false;
        }

        $model = $this->model;
        return Html::a(Html::encode($this->text), ['/cart/element/create'], ['class' => 'pistol88-cart-buy-button btn btn-success', 'data-id' => $model->id, 'data-model' => '\\' . $model::className()]);
    }

}
