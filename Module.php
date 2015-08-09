<?php
namespace pistol88\cart;

use Yii;

class Module extends \yii\base\Module
{
    public $currency = NULL;
    public $currencyPosition = 'after';
    public $priceFormat = [2, '.', ''];

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['cart*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__.'/messages',
            'forceTranslation' => true,
        ];
    }
}