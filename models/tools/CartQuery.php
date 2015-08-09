<?php
namespace pistol88\cart\models\tools;

use yii\web\Session;
use Yii;

class CartQuery extends \yii\db\ActiveQuery
{
    public function my()
    {
        if(!$userId = Yii::$app->user->id) {
            $session = new Session;
            $session->open();
			if(!$userId = $session['tmp_user_id']) {
                $userId = md5(time().'-'.Yii::$app->request->userIP.Yii::$app->request->absoluteUrl);
				$session->set('tmp_user_id', $userId);
			}
		}
        
        $one = $this->andWhere(['user_id' => $userId])->one();
        if(!$one) {
			$one = new \pistol88\cart\models\Cart;
			$one->created_time = time();
			$one->updated_time = time();
			$one->user_id = $userId;
			$one->save();
        }
        return $one;
    }
}