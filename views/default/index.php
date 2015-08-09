<?php
use pistol88\cart\widgets\ElementsList;

$this->title = Yii::t('cart', 'Cart');
?>

<div class="cart">
	<h1><?=\Yii::t('cart', 'Cart');?></h1>
	<?=ElementsList::widget(['template' => $this]); ?>
</div>