<?php
namespace pistol88\cart\widgets;

use yii\helpers\Html;

class ElementCost extends \yii\base\Widget
{
    public $model = NULL;
    public $cssClass = NULL;
    public $htmlTag = 'span';

    public function init()
    {
        parent::init();

        return true;
    }

    public function run()
    {
        return Html::tag($this->htmlTag, $this->model->getCost(), [
            'class' => "pistol88-cart-element-cost{$this->model->getId()} {$this->cssClass}",
        ]);
    }
}
?>
