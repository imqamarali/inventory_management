<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class UserController extends Controller
{
    public function actionProfile()
    {
        $userArray = Yii::$app->session->get('user_array');

        if (!$userArray) {
            return $this->redirect(['site/login']);
        }

        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $flag = Yii::$app->request->post('flag');

            if ($flag === 'checkUsername') {
                return $this->checkUsernameAvailability();
            } elseif ($flag === 'updateProfile') {
                return $this->updateProfile();
            } elseif ($flag === 'changePassword') {
                return $this->changePassword();
            }

            return ['success' => false, 'message' => 'Invalid action'];
        }

        $user = Yii::$app->db->createCommand(
            "SELECT * FROM system_users WHERE id = :id LIMIT 1",
            [':id' => $userArray['id']]
        )->queryOne();

        return $this->render('profile', [
            'user' => $user,
            'userArray' => $userArray
        ]);
    }

    private function checkUsernameAvailability()
    {
        $username = Yii::$app->request->post('username');
        $userId = Yii::$app->request->post('userId');

        if (empty($username)) {
            return ['available' => false];
        }

        $exists = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM system_users WHERE username = :username AND id != :userId",
            [':username' => $username, ':userId' => $userId]
        )->queryScalar();

        return ['available' => $exists == 0];
    }

    private function updateProfile()
    {
        $userId = Yii::$app->request->post('userId');
        $username = Yii::$app->request->post('username');

        if (empty($username)) {
            return ['success' => false, 'message' => 'Username is required'];
        }

        // Check username availability
        $exists = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM system_users WHERE username = :username AND id != :userId",
            [':username' => $username, ':userId' => $userId]
        )->queryScalar();

        if ($exists > 0) {
            return ['success' => false, 'message' => 'Username already exists'];
        }

        $updateData = [
            'username' => $username,
            'email' => Yii::$app->request->post('email'),
            'first_name' => Yii::$app->request->post('first_name'),
            'last_name' => Yii::$app->request->post('last_name'),
            'phone' => Yii::$app->request->post('phone'),
            'whatsapp' => Yii::$app->request->post('whatsapp'),
            'date_of_birth' => Yii::$app->request->post('date_of_birth') ?: null,
            'gender' => Yii::$app->request->post('gender'),
            'address' => Yii::$app->request->post('address'),
            'city' => Yii::$app->request->post('city'),
            'country' => Yii::$app->request->post('country'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $result = Yii::$app->db->createCommand()
                ->update('system_users', $updateData, 'id = :id', [':id' => $userId])
                ->execute();

            if ($result) {
                // Update session
                $userArray = Yii::$app->session->get('user_array');
                $userArray['username'] = $username;
                Yii::$app->session->set('user_array', $userArray);

                return ['success' => true, 'message' => 'Profile updated successfully'];
            }

            return ['success' => false, 'message' => 'Failed to update profile'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function changePassword()
    {
        $userId = Yii::$app->request->post('userId');
        $currentPassword = Yii::$app->request->post('current_password');
        $newPassword = Yii::$app->request->post('new_password');

        if (empty($currentPassword) || empty($newPassword)) {
            return ['success' => false, 'message' => 'All password fields are required'];
        }

        // Get current user password
        $user = Yii::$app->db->createCommand(
            "SELECT password FROM system_users WHERE id = :id LIMIT 1",
            [':id' => $userId]
        )->queryOne();

        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'New password must be at least 6 characters'];
        }

        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $result = Yii::$app->db->createCommand()
                ->update('system_users', [
                    'password' => $hashedPassword,
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'id = :id', [':id' => $userId])
                ->execute();

            if ($result) {
                return ['success' => true, 'message' => 'Password changed successfully'];
            }

            return ['success' => false, 'message' => 'Failed to change password'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function actionSettings()
    {
        return $this->actionProfile();
    }
}
