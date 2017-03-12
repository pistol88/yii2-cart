<?php
namespace pistol88\cart;

use yii\base\BootstrapInterface;
use yii;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        yii::$container->set('pistol88\cart\interfaces\Cart', 'pistol88\cart\models\Cart');
        yii::$container->set('pistol88\cart\interfaces\Element', 'pistol88\cart\models\CartElement');
        yii::$container->set('cartElement', 'pistol88\cart\models\CartElement');

        if (!isset($app->i18n->translations['cart']) && !isset($app->i18n->translations['cart*'])) {
            $app->i18n->translations['cart'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => __DIR__.'/messages',
                'forceTranslation' => true
            ];
        }
    }
}