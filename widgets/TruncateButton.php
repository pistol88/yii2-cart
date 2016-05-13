<?php
namespace pistol88\cart\widgets; 

use yii\helpers\Html;
use yii;

class TruncateButton extends \yii\base\Widget
{
    public $text = NULL;
    public $cssClass = 'btn btn-danger';
 
    public function init()
    {
        parent::init();

        \pistol88\cart\assets\WidgetAsset::register($this->getView());

        if ($this->text == NULL) {
            $this->text = yii::t('cart', 'Truncate');
        }
        
        return true;
    }

    public function run()
    {
        return Html::a(Html::encode($this->text), ['/cart/default/truncate'], ['class' => 'pistol88-cart-truncate-button ' . $this->cssClass]);
    }
}
