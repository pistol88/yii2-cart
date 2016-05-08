<?php
namespace pistol88\cart\controllers;

use pistol88\cart\models\Cart;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii;

class DefaultController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'truncate' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $cartModel = yii::$app->cart;

        if ($cartModel) {
            $elements = $cartModel->getElements();
            $count = $cartModel->getCount();
            $price = $cartModel->getCostFormatted();
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

    function actionTruncate()
    {
        $json = ['result' => 'undefind', 'error' => false];

        $cartModel = yii::$app->cart;
        
        if ($cartModel->truncate()) {
            $json['result'] = 'success';
        } else {
            $json['result'] = 'fail';
            $json['error'] = $cartModel->getCart()->getErrors();
        }

        return $this->_cartJson($json);
    }

    function _cartJson($json)
    {
        if ($cartModel = yii::$app->cart) {
            $json['elementsHTML'] = \pistol88\cart\widgets\ElementsList::widget();
            $json['count'] = $cartModel->getCount();
            $json['price'] = $cartModel->getCostFormatted();
        } else {
            $json['count'] = 0;
            $json['price'] = 0;
        }
        return Json::encode($json);
    }

}
