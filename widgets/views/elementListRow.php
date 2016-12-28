<?php

use yii\helpers\Html;
use pistol88\cart\widgets\ChangeCount;
use pistol88\cart\widgets\DeleteButton;

?>
<li class="pistol88-cart-row ">
    <div class=" row">
        <div class="col-xs-8">
            <?= $name ?>

            <?php if ($options) {
                    $productOptions = '';
                    foreach ($options as $optionId => $valueId) {
                        if($optionData = $allOptions[$optionId]) {
                            $option = $optionData['name'];
                            $value = $optionData['variants'][$valueId];
                            $productOptions .= Html::tag('div', Html::tag('strong', $option) . ':' . $value);
                        }
                    }
                    echo Html::tag('div', $productOptions, ['class' => 'pistol88-cart-show-options']);
                } ?>

            <?php if(!empty($otherFields)) {
                foreach($otherFields as $fieldName => $field) {
                    echo Html::tag('p', Html::tag('small', $fieldName.': '.$product->$field));
                }
            } ?>
        </div>
        <div class="col-xs-3">
            <span class="cart-item-cost">
                <?= $cost ?>
            </span>
            <?= ChangeCount::widget(['model' => $model, 'showArrows' => $showCountArrows]); ?>
        </div>

        <?= Html::tag('div', DeleteButton::widget([
                'model' => $model,
                'lineSelector' => 'pistol88-cart-row ',
                'cssClass' => 'delete']),
                ['class' => 'shop-cart-delete col-xs-1']);
            ?>
    </div>
</li>
