<?php

namespace pistol88\cart\widgets; 

use pistol88\cart\models\Cart;
use pistol88\cart\widgets\DeleteButton;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;

class ElementsList extends \yii\base\Widget {

    public $offerUrl = NULL;
    public $textButton = NULL;
    public $type = 'full';
    public $model = NULL;
    public $cartModel = NULL;

    public function init() {
        parent::init();

        if ($this->offerUrl == NULL) {
            $this->offerUrl = Url::toRoute("/cart/default/index");
        }

        if ($this->cartModel == NULL) {
            $this->cartModel = Cart::my();
        }

        if ($this->textButton == NULL) {
            $this->textButton = Yii::t('cart', 'Cart (<span class="pistol88-cart-price">{p}</span>)', ['c' => $this->cartModel->getCount(), 'p' => $this->cartModel->getPriceFormatted()]);
        }

        \pistol88\cart\assets\WidgetAsset::register($this->getView());
    }

    public function run() {
        $elements = $this->cartModel->getElements();

        if (empty($elements)) {
            return Html::tag('div', Yii::t('cart', 'Your cart empty'), ['class' => 'pistol88-cart pistol88-empty-cart']);
        }

        if ($this->offerUrl) {
            $elements[] = Html::a(Yii::t('cart', 'Offer'), $this->offerUrl, ['class' => 'pistol88-cart-offer-button btn btn-success']);
        }
        $elements[] = Html::tag('div', Yii::t('cart', 'Total') . ': ' . Html::tag('span', $this->cartModel->getPriceFormatted(), ['class' => 'pistol88-cart-price']), ['style' => 'text-align: right;']);

        if ($this->type == 'dropdown') {
            $elementsHtml = Html::ul($elements, ['item' => function($item) {
                    return $this->_row($item);
                },
                'class' => 'dropdown-menu',
                'aria-labelledby' => 'pistol88-cart-block']
            );
            $button = $this->_button();
            $cart = Html::tag('div', $button . $elementsHtml, ['class' => 'pistol88-cart dropdown']);
        } else {
            $cart = Html::ul($elements, ['item' => function($item, $index) {
                            return $this->_row($item);
                        }, 'class' => 'pistol88-cart-full']);
            $cart = Html::tag('div', $cart, ['class' => 'pistol88-cart']);
        }

        return $cart;
    }

    private function _button() {
        return Html::a($this->textButton . '<span class="caret"></span>', $this->offerUrl, [
            'class' => 'pistol88-cart-open-button btn btn-default',
            'data-target' => '#',
            'id' => 'pistol88-cart-block',
            'data-toggle' => 'dropdown',
            'role' => 'button',
            'aria-haspopup' => 'true',
            'aria-expanded' => 'false',
        ]);
    }

    private function _count($item) {
        return Html::tag(
            'div', $item->getPriceFormatted() . 'x' . Html::activeTextInput($item, 'count', [
                'class' => 'pistol88-cart-element-count',
                'data-id' => $item->id,
                'data-href' => Url::toRoute("/cart/element/update"),
            ]), ['class' => 'col-lg-4']
        );
    }

    private function _row($item) {
        if (is_string($item)) {
            return $item;
        }

        $columns = [];
        $columns[] = Html::tag('div', Html::encode($item->model->getCartName()), ['class' => 'col-lg-6']);
        $columns[] = $this->_count($item);
        $columns[] = Html::tag('div', DeleteButton::widget(['model' => $item, 'text' => 'X']), ['class' => 'col-lg-2']);

        $return = html::tag('div', implode('', $columns), ['class' => 'row']);
        return Html::tag('li', $return, ['class' => 'pistol88-cart-row']);
    }

}
