Yii2-cart
==========
Это простой модуль корзины для Yii2 фреймворка.

```php
//...
use yii\pistol88\cart\models\Cart;

class ProductController extends Controller
{
    public function actionAddToCart($id)
    {
        $model = $this->findModel($id);
        $cartElement = Cart::my()->add($model);
}
```

Положить в корзину можно любую модель, имплемементирующую интерфейс CartElementInterface

```php
//...
class Product extends ActiveRecord implements \pistol88\cart\models\tools\CartElementInterface 
```

Получить элементы корзины:
```php
//...
$elements = \pistol88\cart\Models\Cart::my();
```

Виджеты
==========
В состав модуля входит несколько виджетов.

```php
<?php
use pistol88\cart\widgets\BuyButton;
use pistol88\cart\widgets\CartInformer;
use pistol88\cart\widgets\ElementsList;
use pistol88\cart\widgets\DeleteButton;
?>

<?php /* Выведет кнопку покупки */ ?>
<?= BuyButton::widget([
	'model' => $model,
	'text' => 'заказать',
	'htmlTag' => 'a',
	'cssClass' => 'custom_class'
]) ?>

<?php /* Выведет количество товаров и сумму заказа */ ?>
<?= CartInformer::widget(['htmlTag' => 'a', 'offerUrl' => '/?r=cart', 'text' => '{c} на {p}']); ?>

<?php /* Выведет корзину с выпадающими или обычными ('type' => 'full') элементами списком */ ?>
<?=ElementsList::widget(['type' => 'dropdown']);?>

<?php /* Выведет кнопку удаления элемента */ ?>
<?=DeleteButton::widget(['model' => $item, 'text' => 'X']);?>
```

Установка
---------------------------------
Выполнить команду

php composer require pistol88/yii2-cart "dev-master"

Настройка
---------------------------------
В конфигурационный файл добавить модуль cart

```php
    'modules' => [
        'cart' => [
            'class' => 'pistol88\cart\Module',
            'layoutPath' => 'frontend\views\layouts',
            'currency' => '₽',
            'currencyPosition' => 'after',
            'priceFormat' => [2, '.', ''],
        ],
    ]
```
