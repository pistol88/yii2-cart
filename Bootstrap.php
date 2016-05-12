<?php
namespace pistol88\cart;

use yii\base\BootstrapInterface;
use pistol88\cart\interfaces\CartService;
use pistol88\cart\interfaces\ElementService;
use pistol88\cart\models\Cart;
use pistol88\cart\models\CartElement;
use yii;

class Bootstrap implements BootstrapInterface
{

    public function bootstrap($app)
    {
        yii::$container->set(CartService::class, Cart::class);
        yii::$container->set(ElementService::class, CartElement::class);

        yii::$container->set('cartElement', CartElement::class);

        if (!isset($app->i18n->translations['cart']) && !isset($app->i18n->translations['cart*'])) {
            $app->i18n->translations['cart'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => __DIR__.'/messages',
                'forceTranslation' => true
            ];
        }
    }

}
