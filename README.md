Yii2-cart
==========
Это простой модуль корзины. В корзину можно положить любой элемент.

```php
namespace frontend\controllers;

use Yii;
use frontend\models\Product;
use yii\pistol88\cart\models\Cart;

class ProductController extends Controller
{
    public function actionAddToCart($id)
    {
        $model = $this->findModel($id);
        $cartElement = Cart::my()->add($model);
}
```


Installation:
---------------------------------

Getting started:
---------------------------------
