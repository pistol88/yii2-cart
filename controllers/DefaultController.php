<?php

namespace pistol88\cart\controllers; 

use pistol88\cart\models\Cart;
use yii\filters\VerbFilter;
use Yii;

class DefaultController extends \yii\web\Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['add'],
                ],
            ],
        ];
    }

    function actionIndex() {
        $cartModel = Cart::my();

        if ($cartModel) {
            $elements = $cartModel->getElements();
            $count = $cartModel->getCount();
            $price = $cartModel->getPriceFormatted();
        } else {
            $elements = [];
            $count = 0;
            $price = 0;
        }

        return $this->render('index', [
                    'cartModel' => $cartModel,
                    'count' => $count,
                    'price' => $price,
                    'elements' => $elements,
        ]);
    }

}
