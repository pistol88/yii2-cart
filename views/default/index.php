<?php
use pistol88\cart\widgets\ElementsList;
use yii;

$this->title = yii::t('cart', 'Cart');
?>

<div class="cart">
    <h1><?= yii::t('cart', 'Cart'); ?></h1>
    <?= ElementsList::widget(); ?>
</div>