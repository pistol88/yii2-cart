<?php

namespace pistol88\cart\controllers;

use pistol88\cart\models\Cart;
use pistol88\cart\models\CartElement;
use yii\helpers\Json;
use yii\filters\VerbFilter;
use Yii;

class ElementController extends \yii\web\Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    function actionDelete() {
        $json = ['result' => 'undefind', 'error' => false];
        $elementId = Yii::$app->request->post('elementId');

        CartElement::findOne($elementId)->delete();

        $json['result'] = 'success';

        return $this->_cartJson($json);
    }

    function actionCreate() {
        $json = ['result' => 'undefind', 'error' => false];

        $cartModel = Cart::my();

        $json['cartId'] = $cartModel->id;

        if ($cartModel->id) {
            $postData = Yii::$app->request->post();

            $model = $postData['CartElement']['model'];
            $productModel = new $model();
            $productModel = $productModel::find($postData['CartElement']['item_id'])->one();

            try {
                $elementModel = $cartModel->add($productModel);
            }
            catch(Exception $e) {
                
            }

            $json['elementId'] = $elementModel->id;
            $json['result'] = 'success';
        }

        return $this->_cartJson($json);
    }

    function actionUpdate() {
        $json = ['result' => 'undefind', 'error' => false];

        $cartModel = Cart::my();
        $json['cartId'] = $cartModel->id;

        $postData = Yii::$app->request->post();
        $elementModel = CartElement::find()->andWhere(['cart_id' => $cartModel->id, 'id' => $postData['CartElement']['id']])->one();

        if ($elementModel->load($postData) && $elementModel->save()) {
            $json['elementId'] = $elementModel->id;
            $json['result'] = 'success';
        } else {
            $json['result'] = 'fail';
            $json['error'] = $elementModel->getErrors();
        }

        return $this->_cartJson($json);
    }

    function _cartJson($json) {
        if ($cartModel = Cart::my()) {
            $json['elementsHTML'] = \pistol88\cart\widgets\ElementsList::widget(['type' => 'dropdown']);
            $json['count'] = $cartModel->getCount();
            $json['price'] = $cartModel->getPriceFormatted();
        } else {
            $json['count'] = 0;
            $json['price'] = 0;
        }
        return Json::encode($json);
    }

}
