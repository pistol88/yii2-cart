<?php
namespace pistol88\cart\widgets;

use pistol88\cart\widgets\DeleteButton;
use pistol88\cart\widgets\TruncateButton;
use pistol88\cart\widgets\ChangeCount;
use pistol88\cart\widgets\CartInformer;
use yii\helpers\Html;
use yii\helpers\Url;
use yii;

class ElementsList extends \yii\base\Widget
{
    public $offerUrl = NULL;
    public $textButton = NULL;
    public $type = 'full';
    public $model = NULL;
    public $cart = NULL;
    public $showDescription = false;
    public $showTotal = false;
    public $showOffer = true;
    public $showTruncate = true;

    public function init()
    {
        parent::init();

        if ($this->offerUrl == NULL) {
            $this->offerUrl = Url::toRoute(['/cart/default/index']);
        }

        if ($this->cart == NULL) {
            $this->cart = yii::$app->cart;
        }

        if ($this->textButton == NULL) {
            $this->textButton = yii::t('cart', 'Cart (<span class="pistol88-cart-price">{p}</span>)', ['c' => $this->cart->getCount(), 'p' => $this->cart->getCostFormatted()]);
        }

        \pistol88\cart\assets\WidgetAsset::register($this->getView());
        
        return true;
    }

    public function run()
    {
        $elements = $this->cart->elements;

        if (empty($elements)) {
            $cart = Html::tag('div', yii::t('cart', 'Your cart empty'), ['class' => 'pistol88-cart pistol88-empty-cart']);
        } else {
        	$cart = Html::ul($elements, ['item' => function($item, $index) {
                return $this->_row($item);
            }, 'class' => 'pistol88-cart-list']);
		}
		
        if (!empty($elements)) {
            if($this->offerUrl && $this->showOffer) {
                $bottomPanel = Html::a(yii::t('cart', 'Offer'), $this->offerUrl, ['class' => 'pistol88-cart-offer-button btn btn-success']);
            }
            
            if($this->showTruncate) {
                $bottomPanel .= TruncateButton::widget();
            }
            
            $cart .= Html::tag('div', $bottomPanel, ['class' => 'pistol88-cart-bottom-panel']);
        }
        
        $cart = Html::tag('div', $cart, ['class' => 'pistol88-cart']);
        
        if (empty($elements) && $this->showTotal) {
            $cart .= Html::tag('div', Yii::t('cart', 'Total') . ': ' . CartInformer::widget(), ['class' => 'pistol88-cart-total-row']);
        }

        if ($this->type == 'dropdown') {
            $button = Html::button($this->textButton.Html::tag('span', '', ["class" => "caret"]), ['class' => 'btn dropdown-toggle', 'id' => 'pistol88-cart-drop', 'type' => "button", 'data-toggle' => "dropdown", 'aria-haspopup' => 'true', 'aria-expanded' => "false"]);
            $list = Html::tag('div', $cart, ['class' => 'dropdown-menu', 'aria-labelledby' => 'pistol88-cart-drop']);
            $cart = Html::tag('div', $button.$list, ['class' => 'pistol88-cart-dropdown dropdown']);
        }
        
        return Html::tag('div', $cart, ['class' => 'pistol88-cart-block']); 
    }

    private function _row($item)
    {
        if (is_string($item)) {
            return html::tag('li', $item);
        }
        
        $columns = [];

        $cartElName = $item->cartElementModel->getCartName();

        if($this->showDescription && $item->description) {
            $cartElName .= ' ('.$item->description.')';
        }

        $columns[] = Html::tag('div', $cartElName, ['class' => 'col-lg-5 col-xs-5']);

        $columns[] = Html::tag('div', ChangeCount::widget(['model' => $item, 'showArrows' => false]), ['class' => 'col-lg-3 col-xs-3']);

        $columns[] = Html::tag('div', $item->getCostFormatted(), ['class' => 'col-lg-2 col-xs-2']);
        
        $columns[] = Html::tag('div', DeleteButton::widget(['model' => $item, 'lineSelector' => 'pistol88-cart-row ', 'cssClass' => 'delete']), ['class' => 'shop-cart-delete col-lg-2 col-xs-2']);

        $return = html::tag('div', implode('', $columns), ['class' => ' row']);
        return Html::tag('li', $return, ['class' => 'pistol88-cart-row ']);
    }
}
