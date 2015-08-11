<?php

namespace pistol88\cart\widgets;

use yii\helpers\Html;

class DeleteButton extends \yii\base\Widget {

    public $text = NULL;
    public $template = NULL;
    public $model = NULL;

    public function init() {
        parent::init();

        \pistol88\cart\assets\WidgetAsset::register($this->getView());

        if ($this->text == NULL) {
            $this->text = Yii::t('cart', 'Delete');
        }
    }

    public function run() {
        return Html::a(Html::encode($this->text), ['/cart/element/delete'], ['class' => 'pistol88-cart-delete-button btn btn-danger', 'data-id' => $this->model->id]);
    }

}
