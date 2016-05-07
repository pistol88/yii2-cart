<?php

namespace pistol88\cart\widgets;

use pistol88\cart\models\Cart;
use pistol88\cart\widgets\DeleteButton;
use pistol88\cart\widgets\ChangeCount;
use yii\helpers\Html;
use yii\helpers\Url;
use yii;

class ElementsList extends \yii\base\Widget {

    public $offerUrl = NULL;
    public $textButton = NULL;
    public $type = 'full';
    public $model = NULL;
    public $cart = NULL;
    public $showDescription = false;
    public $showTotal = false;
    public $showOffer = false;

    public function init() {
        parent::init();

        if ($this->offerUrl == NULL) {
            $this->offerUrl = '#offer';
        }

        if ($this->cart == NULL) {
            $this->cart = yii::$app->cart;
        }

        if ($this->textButton == NULL) {
            $this->textButton = Yii::t('cart', 'Cart (<span class="pistol88-cart-price">{p}</span>)', ['c' => $this->cart->getCount(), 'p' => $this->cart->getCostFormatted()]);
        }

        \pistol88\cart\assets\WidgetAsset::register($this->getView());
    }

    public function run() {
        $elements = $this->cart->getElements();

        if (empty($elements)) {
            return Html::tag('div', Yii::t('cart', 'Your cart empty'), ['class' => 'pistol88-cart pistol88-empty-cart']);
        }

        if ($this->offerUrl) {
            $elements[] = Html::a(Yii::t('cart', 'Offer'), $this->offerUrl, ['class' => 'pistol88-cart-offer-button btn btn-success']);
        }
        
        if ($this->showTotal) {
            $elements[] = Html::tag('div', Yii::t('cart', 'Total') . ': ' . Html::tag('span', $this->cart->getCostFormatted(), ['class' => 'pistol88-cart-price']), ['style' => 'text-align: right;']);
        }
        
        $cart = Html::ul($elements, ['item' => function($item, $index) {
                    return $this->_row($item);
                }, 'class' => 'pistol88-cart-list']);

        $cart = Html::tag('div', $cart, ['class' => 'pistol88-cart']);
        
        if ($this->type == 'dropdown') {
            $button = Html::button($this->textButton.Html::tag('span', '', ["class" => "caret"]), ['class' => 'btn dropdown-toggle', 'id' => 'pistol88-cart-drop', 'type' => "button", 'data-toggle' => "dropdown", 'aria-haspopup' => 'true', 'aria-expanded' => "false"]);
            $list = Html::tag('div', $cart, ['class' => 'dropdown-menu', 'aria-labelledby' => 'pistol88-cart-drop']);
            $cart = Html::tag('div', $button.$list, ['class' => 'pistol88-cart-dropdown dropdown']);
        }
        
        return $cart;
    }

    private function _price($item) {
        return Html::tag(
            'div',
            $item->getCostFormatted(),
            ['class' => 'col-lg-2 price']
        );
    }
    
    private function _row($item) {
        if (is_string($item)) {
            return $item;
        }
        
        $columns = [];

        $cartName = "<div class=\"title\">".$item->model->getCartName()."</div><div class=\"count\">".ChangeCount::widget(['model' => $item])."</div>";

        if($item->description) {
            $cartName .= ' ('.$item->description.')';
        }

        $columns[] = Html::tag('div', $cartName, ['class' => 'col-lg-8']);

        $columns[] = $this->_price($item);
        $columns[] = Html::tag('div', DeleteButton::widget(['model' => $item, 'cssClass' => 'delete']), ['class' => 'shop-cart-delete col-lg-2']);

        $return = html::tag('div', implode('', $columns), ['class' => ' row']);
        return Html::tag('li', $return, ['class' => 'pistol88-cart-row ']);
    }
}
