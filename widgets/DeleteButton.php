<?php
namespace pistol88\cart\widgets; 

use yii\helpers\Html;

class DeleteButton extends \yii\base\Widget
{
    public $text = NULL;
    public $model = NULL;
    public $cssClass = 'btn btn-danger';
    public $lineSelector = 'li';  //Селектор материнского элемента, где выводится элемент

    public function init()
    {
        parent::init();

        \pistol88\cart\assets\WidgetAsset::register($this->getView());

        if ($this->text == NULL) {
            $this->text = '╳';
        }
        
        return true;
    }

    public function run()
    {
        return Html::a(Html::encode($this->text), ['/cart/element/delete'], ['data-line-selector' => $this->lineSelector, 'class' => 'pistol88-cart-delete-button '.$this->cssClass, 'data-id' => $this->model->getId()]);
    }

}
