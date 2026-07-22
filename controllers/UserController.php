<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class UserController extends Controller
{
    public function actionProfile()
    {
        $userArray = Yii::$app->session->get('user_array');

        if (!$userArray) {
            return $this->redirect(['site/login']);
        }

        $user = Yii::$app->db->createCommand(
            "SELECT * FROM inventory_users WHERE id = :id LIMIT 1",
            [':id' => $userArray['id']]
        )->queryOne();

        return $this->renderPartial('profile', [
            'user' => $user,
            'userArray' => $userArray
        ]);
    }

    public function actionSettings()
    {
        return $this->actionProfile();
    }
}
