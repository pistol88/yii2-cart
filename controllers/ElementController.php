<?php
namespace pistol88\cart\controllers;

use yii\helpers\Json;
use yii\filters\VerbFilter;
use yii;

class ElementController extends \yii\web\Controller
{
    public function behaviors()
    {
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
    
    public function actionDelete()
    {
        $json = ['result' => 'undefind', 'error' => false];
        $elementId = yii::$app->request->post('elementId');

        $cart = yii::$app->cart;
        
        $elementModel = $cart->getElementById($elementId);
        
        if($elementModel->delete()) {
            $json['result'] = 'success';
        }
        else {
            $json['result'] = 'fail';
        }

        return $this->_cartJson($json);
    }
	
    public function actionCreate()
    {
        $json = ['result' => 'undefind', 'error' => false];

        $cart = yii::$app->cart;

        $postData = yii::$app->request->post();

        $model = $postData['CartElement']['model'];
        if($model) {
            $productModel = new $model();
            $productModel = $productModel::findOne($postData['CartElement']['item_id']);

            $options = [];
            if(isset($postData['CartElement']['options'])) {
                $options = $postData['CartElement']['options'];
            }

            $elementModel = $cart->put($productModel, $postData['CartElement']['count'], $options);

            $json['elementId'] = $elementModel->getId();
            $json['result'] = 'success';
        } else {
            $json['result'] = 'fail';
            $json['error'] = 'empty model';
        }

        return $this->_cartJson($json);
    }

    public function actionUpdate()
    {
        $json = ['result' => 'undefind', 'error' => false];

        $cart = yii::$app->cart;
        
        $postData = yii::$app->request->post();

        $elementModel = $cart->getElementById($postData['CartElement']['id']);
        
        if(isset($postData['CartElement']['count'])) {
            $elementModel->setCount($postData['CartElement']['count'], true);
        }
        
        if(isset($postData['CartElement']['options'])) {
            $elementModel->setOptions($postData['CartElement']['options'], true);
        }
        
        $json['elementId'] = $elementModel->getId();
        $json['result'] = 'success';

        return $this->_cartJson($json);
    }

    private function _cartJson($json)
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
