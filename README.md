Yii2-cart
==========
Это модуль корзины для Yii2 фреймворка. Позволяет добавить в корзину любую модель, имплементирующую интерфейс pistol88\cart\interfaces\CartElement

Установка
---------------------------------
Выполнить команду

```
php composer require pistol88/yii2-cart "*"
```

Или добавьте в composer.json

```
"pistol88/yii2-cart": "*",
```

И выполните

```
php composer update
```

Далее, мигрируем базу:

```
php yii migrate --migrationPath=vendor/pistol88/yii2-cart/migrations
```

Подключение и настройка
---------------------------------
В конфигурационный файл приложения добавить компонент cart
```php
        'cart' => [
            'class' => 'pistol88\cart\Cart',
            'currency' => 'р.', //Валюта
            'currencyPosition' => 'after', //after или before (позиция значка валюты относительно цены)
            'priceFormat' => [0,'.', ''], //Форма цены
        ],
```

И модуль (если хотите использовать виджеты)

```php
    'modules' => [
        'cart' => [
            'class' => 'pistol88\cart\Module',
            'layoutPath' => 'frontend\views\layouts',
        ],
    ]
```

Использование
---------------------------------
Можно добавлять в корзину элементы самостоятельно через компонент, а можно использовать готовые виджеты.
Пример эктиона, добавляющего товар в корзину:

```php
//use...

class ProductController extends Controller
{
    public function actionAddToCart($id)
    {
        //Любая модель
        $model = $this->findModel($id);
        //Кладем ее в корзину
        $cartElement = yii::$app->cart->put($model);
}
```

Положить в корзину можно любую модель, имплемементирующую интерфейс CartElement. Пример модели:

```php
//...
class Product extends ActiveRecord implements \pistol88\cart\interfaces\CartElement {
    //..
    public function getCartName() {
        return $this->name;
    }
    public function getCartPrice() {
        return $this->price;
    }
    //..
}
```

Получить элементы корзины:
```php
//...
$elements = yii::$app->cart->elements;
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
use pistol88\cart\widgets\ChangeCount;
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

<?php /* Выведет корзину с выпадающими или обычными ('type' => 'full') элементами списка */ ?>
<?=ElementsList::widget(['type' => 'dropdown']);?>

<?php /* Выведет кнопку удаления элемента */ ?>
<?=DeleteButton::widget(['model' => $item, 'text' => 'X']);?>

<?php
/*
Выведет кнопку изменения кол-ва элемента.
Можно передать как модель элемента корзины, так и сам продукт,
когда он еще не стал элементом.
*/ ?>
<?=ChangeCount::widget(['model' => $item]);?>
```

Скидки
==========
Скидки реализуются через поведение и событие. Корзине можно присвоить любое поведение (в конфиге):
```
        'cart' => [
            'class' => 'pistol88\cart\Cart',
            //...
            'behaviors' => [
                'discount' => [
                    'class' => 'common\behaviors\Discount',
                    'persent' => 50,
                ],
            ],
        ],
```

Поведение цепляется к событию EVENT_CART_COST и задает скидку (например, ночную):

```php
<?php
namespace common\behaviors;

use yii\base\Behavior;
use pistol88\cart\Cart;
use yii;

class Discount extends Behavior {

    public $persent = 0;

    public function events() {
        return [
            Cart::EVENT_CART_COST => 'doDiscount'
        ];
    }

    public function doDiscount($event) {
        if($this->persent > 0 && $this->persent <= 100 && $event->cost > 0) {
            $hour = intval(date('H',time()));
            if(($hour >= 0 && $hour < 6)) {
                $nightPrice = ($event->cost*$this->persent)/100;
                //Устанавливаем ночную цену
                $event->cost = $nightPrice;
            }
        }
        
        return $this;
    }
}

```

Таким же макаром можно сделать и наценку.

Все события, к которым можно подцепиться поведением:

 * EVENT_CART_COST - изменение цены
 * EVENT_CART_COUNT - изменение количества
 * EVENT_CART_TRUNCATE - очищение корзины
 * EVENT_CART_PUT - добавление элемента
