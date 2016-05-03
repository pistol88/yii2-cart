<?php

namespace pistol88\cart\controllers;

use pistol88\cart\models\Cart;
use pistol88\cart\models\CartElement;
use yii\helpers\Json;
use yii\filters\VerbFilter;
use Yii;

class ElementController extends \yii\web\Controller {
    function init() {
		$this->enableCsrfValidation = false;
	}
	
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

        if(CartElement::findOne($elementId)->delete()) {
            $json['result'] = 'success';
        }
        else {
            $json['result'] = 'fail';
        }

        return $this->_cartJson($json);
    }
	
    function actionCreate() {
        $json = ['result' => 'undefind', 'error' => false];

        $cartModel = yii::$app->cart;

        $json['cartId'] = $cartModel->id;

        if ($cartModel->id) {
            $postData = Yii::$app->request->post();

            $model = $postData['CartElement']['model'];
            if($model) {
                $productModel = new $model();
                $productModel = $productModel::findOne($postData['CartElement']['item_id']);

                $elementModel = $cartModel->put($productModel, $postData['CartElement']['count'], $postData['CartElement']['description']);

                $json['elementId'] = $elementModel->id;
                $json['result'] = 'success';
            }
            else {
                $json['result'] = 'fail';
                $json['error'] = 'empty model';
            }
        }

        return $this->_cartJson($json);
    }

    function actionUpdate() {
        $json = ['result' => 'undefind', 'error' => false];

        $cartModel = yii::$app->cart;
        
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
