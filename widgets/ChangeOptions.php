<?php
namespace pistol88\cart\widgets; 

use yii\helpers\Url;
use yii\helpers\Html;
use yii;

class ChangeOptions extends \yii\base\Widget
{
    const TYPE_SELECT = 'select';
    const TYPE_RADIO = 'radio';
    
    public $model = NULL;
    public $type = NULL;
    public $cssClass = '';
    public $defaultValues = [];

    public function init()
    {
        if ($this->type == NULL) {
            $this->type = self::TYPE_SELECT;
        }
        
        parent::init();
        
        \pistol88\cart\assets\WidgetAsset::register($this->getView());
        
        return true;
    }

    public function run()
    {
        if($this->model instanceof \pistol88\cart\interfaces\CartElement) {
            $optionsList = $this->model->getCartOptions();
            $changerCssClass = 'pistol88-option-values-before';
            $id = $this->model->getCartId();
        } else {
            $optionsList = $this->model->getModel()->getCartOptions();
            $this->defaultValues = $this->model->getOptions();
            $id = $this->model->getId();
            $changerCssClass = 'pistol88-option-values';
        }

        if(!empty($optionsList)) {
            $i = 1;
            foreach($optionsList as $optionId => $optionData) {
                if(!is_array($values)) {
                    $values = [];
                }
                
                $cssClass = "{$changerCssClass} pistol88-cart-option{$id} ";
                
                $optionsArray = ['' => $optionData['name']];
                foreach($optionData['variants'] as $variantId => $value) {
                    $optionsArray[$variantId] = $value;
                }
                
                if($this->type == 'select') {

                    $list = Html::dropDownList('cart_options' . $id .'-' . $i,
                        $this->_defaultValue($option),
                        $optionsArray,
                        ['data-href' => Url::toRoute(["/cart/element/update"]), 'data-filter-id' => $optionId, 'data-name' => Html::encode($optionData['name']), 'data-id' => $id, 'class' => "form-control $cssClass"]
                    );
                } else {
                    $list = Html::tag('div', Html::tag('strong', $optionData['name']), ['class' => 'pistol88-option-heading']);
                    $list .= Html::radioList('cart_options' . $id . '-' . $i,
                        $this->_defaultValue($option),
                        $optionsArray,
                        ['itemOptions' => ['data-href' => Url::toRoute(["/cart/element/update"]), 'data-filter-id' => $optionId, 'data-name' => Html::encode($optionData['name']), 'data-id' => $id, 'class' => $cssClass]]
                    );
                }
                
                $options[] = Html::tag('div', $list, ['class' => "pistol88-option"]);
                $i++;
            }
        }
        else {
            return null;
        }
        
        return Html::tag('div', implode('', $options), ['class' => 'pistol88-change-options ' . $this->cssClass]);
    }
    
    private function _defaultValue($option)
    {
        if(isset($this->defaultValues[$option])) {
            return $this->defaultValues[$option];
        }
        
        return false;
    }
}
