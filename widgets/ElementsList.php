<?php
namespace pistol88\cart\widgets;

use pistol88\cart\models\Cart;
use pistol88\cart\widgets\DeleteButton;
use yii\helpers\Html;
use yii\helpers\Url;

use Yii;

class ElementsList extends \yii\base\Widget
{
    public $template = NULL;
    public $offerUrl = NULL;
    public $textButton = NULL;
    public $type = 'full';
    public $model = NULL;
    
    public function init()
    {
        parent::init();
        
        $this->registerTranslations();
        
        if($this->offerUrl == NULL) {
            $this->offerUrl = Url::toRoute("/cart/default/index");
        }
        
        if($this->template) {
            \pistol88\cart\assets\WidgetAsset::register($this->template);
        }
    }

    public function run()
    {
        $cartModel = Cart::find()->my();
        $elements = $cartModel->getElements();
        
        if(empty($elements)) {
            return Html::tag('div', Yii::t('cart', 'Your cart empty'), ['class' => 'pistol88-cart pistol88-empty-cart']);
        }
        
        if($this->offerUrl) {
            $elements[] = Html::a(Yii::t('cart', 'Offer'), $this->offerUrl, ['class' => 'pistol88-cart-offer-button btn btn-success']);
        }
        $elements[] = Html::tag('div', Yii::t('cart', 'Total').': '.Html::tag('span', $cartModel->getPriceFormatted(), ['class' => 'pistol88-cart-price']), ['style' => 'text-align: right;']);
        
        if($this->type == 'dropdown') {
            $cart = Html::ul($elements, ['item' => function($item, $index) {
                return Html::tag('li', $this->_row($item), ['class' => 'pistol88-cart-row']);
            }, 'class' => 'dropdown-menu', 'aria-labelledby' => 'pistol88-cart-block']);
            
            if($this->textButton == NULL) {
                $this->textButton = Yii::t('cart', 'Cart (<span class="pistol88-cart-price">{p}</span>)', ['c' => $cartModel->getCount(), 'p' => $cartModel->getPriceFormatted()]);
            }
            $button = Html::a($this->textButton.'<span class="caret"></span>', $this->offerUrl,
                    [
                        'class' => 'pistol88-cart-open-button btn btn-default',
                        'data-target' => '#',
                        'id' => 'pistol88-cart-block',
                        'data-toggle' => 'dropdown',
                        'role' => 'button',
                        'aria-haspopup' => 'true',
                        'aria-expanded' => 'false',
                    ]);
            $cart = Html::tag('div', $button.$cart, ['class' => 'pistol88-cart dropdown']);
        }
        else {
            $cart = Html::ul($elements, ['item' => function($item, $index) {
                return Html::tag('li', $this->_row($item), ['class' => 'pistol88-cart-row']);
            }, 'class' => 'pistol88-cart-full']);
            $cart = Html::tag('div', $cart, ['class' => 'pistol88-cart']);
        }

        return $cart;
    }
    
    private function _row($item)
    {
        if(is_string($item)) {
            return $item;
        }
        
        $price = Html::encode($item->model->getCartPrice());
        $currency = Yii::$app->getModule('cart')->currency;
        if(Yii::$app->getModule('cart')->currencyPosition == 'after') {
            $price = "$price$currency";
        }
        else {
            $price = "$currency$price";
        }
        
        $columns = [];
        $columns[] = Html::tag('div', Html::encode($item->model->getCartName()), ['class' => 'col-lg-6']);
        $columns[] = Html::tag(
                        'div',
                        $price.'x'.Html::activeTextInput(
                            $item,
                            'count',
                            [
                                'class' => 'pistol88-cart-element-count',
                                'style' => 'width: 30px;',
                                'data-id' => $item->id,
                                'data-href' => Url::toRoute("/cart/element/update"),
                            ]
                        ),
                        ['class' => 'col-lg-4']
                    );
        $columns[] = Html::tag('div', DeleteButton::widget(['model' => $item, 'text' => 'X']), ['class' => 'col-lg-2']);
        
        return html::tag('div', implode('', $columns), ['class' => 'row']);
    }
    
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['cart*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__.'/../messages',
            'forceTranslation' => true,
        ];
    }
}