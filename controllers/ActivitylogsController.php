<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class ActivitylogsController extends Controller
{
    public function actionIndex()
    {
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $this->getActivityLogs();
        }

        // Get filter options
        $modules = Yii::$app->db->createCommand(
            "SELECT DISTINCT module FROM activitylogs WHERE module IS NOT NULL AND module != '' ORDER BY module"
        )->queryColumn();

        $activities = Yii::$app->db->createCommand(
            "SELECT DISTINCT activity FROM activitylogs WHERE activity IS NOT NULL AND activity != '' ORDER BY activity"
        )->queryColumn();

        return $this->renderPartial('index', [
            'modules' => $modules,
            'activities' => $activities
        ]);
    }

    private function getActivityLogs()
    {
        $page = max(1, (int)(Yii::$app->request->post('page') ?? 1));
        $perPage = max(10, (int)(Yii::$app->request->post('per_page') ?? 20));
        $offset = ($page - 1) * $perPage;

        $dateFrom = Yii::$app->request->post('date_from');
        $dateTo = Yii::$app->request->post('date_to');
        $module = Yii::$app->request->post('module');
        $activity = Yii::$app->request->post('activity');
        $search = Yii::$app->request->post('search');

        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($dateFrom)) {
            $where .= " AND DATE(datetime) >= :dateFrom ";
            $params[':dateFrom'] = $dateFrom;
        }

        if (!empty($dateTo)) {
            $where .= " AND DATE(datetime) <= :dateTo ";
            $params[':dateTo'] = $dateTo;
        }

        if (!empty($module)) {
            $where .= " AND module = :module ";
            $params[':module'] = $module;
        }

        if (!empty($activity)) {
            $where .= " AND activity = :activity ";
            $params[':activity'] = $activity;
        }

        if (!empty($search)) {
            $where .= " AND (activity LIKE :search OR user_agent LIKE :search OR ip_address LIKE :search) ";
            $params[':search'] = '%' . $search . '%';
        }

        $total = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM activitylogs {$where}",
            $params
        )->queryScalar();

        $logs = Yii::$app->db->createCommand(
            "SELECT * FROM activitylogs {$where} ORDER BY datetime DESC LIMIT {$offset}, {$perPage}",
            $params
        )->queryAll();

        // Format logs with user info
        foreach ($logs as &$log) {
            $log['formatted_date'] = date('M d, Y', strtotime($log['datetime']));
            $log['formatted_time'] = date('h:i A', strtotime($log['datetime']));

            // Get user info
            if (!empty($log['uid'])) {
                $user = Yii::$app->db->createCommand(
                    "SELECT username, first_name, last_name FROM system_users WHERE id = :id LIMIT 1",
                    [':id' => $log['uid']]
                )->queryOne();

                if ($user) {
                    $log['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $user['username'];
                } else {
                    $log['user_name'] = 'Unknown User';
                }
            } else {
                $log['user_name'] = 'System';
            }

            // Parse additional data
            if (!empty($log['additional_data'])) {
                $log['additional_data_decoded'] = json_decode($log['additional_data'], true);
            }
        }

        return [
            'success' => true,
            'logs' => $logs,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    /**
     * Log activity
     * @param string $activity Activity name
     * @param string $activityType Activity type (create, update, delete, view, etc)
     * @param int $refId Reference ID
     * @param string $module Module name
     * @param array $additionalData Additional data
     */
    public static function logActivity($activity, $activityType, $refId, $module, $additionalData = [])
    {
        try {
            $userArray = Yii::$app->session->get('user_array');
            $userId = $userArray['id'] ?? null;

            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            Yii::$app->db->createCommand()->insert('activitylogs', [
                'activity' => $activity,
                'activitytype' => $activityType,
                'refid' => $refId,
                'module' => $module,
                'uid' => $userId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'additional_data' => !empty($additionalData) ? json_encode($additionalData) : null,
                'date' => date('Y-m-d'),
                'datetime' => date('Y-m-d H:i:s')
            ])->execute();
        } catch (\Exception $e) {
            // Log error but don't stop execution
            Yii::info('Activity log error: ' . $e->getMessage(), 'activity-log');
        }
    }
}
