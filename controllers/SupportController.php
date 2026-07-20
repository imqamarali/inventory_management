<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\data\Pagination;


class SupportController extends Controller
{
    public $school_id;

    public function init()
    {
        parent::init();
        $school_id = Yii::$app->Component->School_id();
        $this->school_id = $school_id;
    }
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
    {

        $actions = 'support/' . $action->id;
        if (Yii::$app->session->has('user_array') == NULL)
            $this->redirect(['site/index']);
        else if (Yii::$app->Permissions->checkMethod($actions)) {
            $this->enableCsrfValidation = true;
            return parent::beforeAction($action);
        } else {
            Yii::$app->session->setFlash('toast', 'Unauthorized access... Please contact support team.');
            $this->redirect(['site/index']);
        }
    }
    private function check_permissions($id)
    {
        $permissions = Yii::$app->Component->CheckStudentPermissions($id);

        if ($permissions['can_view'] == '0') {
            Yii::$app->session->setFlash('toast', 'You do not have Permission for this action');
            return $this->redirect(['site/index']);
        }
        return $permissions;
    }

    public function actionIndex()
    {
        $role_id = Yii::$app->Component->CheckRole();
        $session_id = Yii::$app->Component->ActiveSession();

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            echo json_encode($data);
            exit;
        }

        // Hide footer for support views
        $this->view->params['hideFooter'] = true;

        return $this->render('index');
    }


    public function actionGetcounts()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $db = Yii::$app->db;
        $uid = Yii::$app->user->id ?? $_SESSION['user_array']['id'] ?? null;

        try {
            $counts = [
                'to_handle' => (int)$db->createCommand(
                    "SELECT COUNT(*) FROM tickets WHERE status IN ('Open', 'Pending') AND (assigned_to = :uid OR assigned_to IS NULL)",
                    [':uid' => $uid]
                )->queryScalar(),

                'my_open' => (int)$db->createCommand(
                    "SELECT COUNT(*) FROM tickets WHERE created_by = :uid AND status IN ('Open', 'Pending', 'On hold')",
                    [':uid' => $uid]
                )->queryScalar(),

                'last_7_days' => (int)$db->createCommand(
                    "SELECT COUNT(*) FROM tickets WHERE created_by = :uid AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
                    [':uid' => $uid]
                )->queryScalar(),

                'open' => (int)$db->createCommand("SELECT COUNT(*) FROM tickets WHERE status = 'Open'")->queryScalar(),
                'pending' => (int)$db->createCommand("SELECT COUNT(*) FROM tickets WHERE status = 'Pending'")->queryScalar(),
                'on_hold' => (int)$db->createCommand("SELECT COUNT(*) FROM tickets WHERE status = 'On hold'")->queryScalar(),
            ];

            return ['success' => true, 'counts' => $counts];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function actionLoadsection($section)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        $db = Yii::$app->db;
        $uid = Yii::$app->user->id ?? $_SESSION['user_array']['id'] ?? null;
        $lim = 50;

        $title = '';
        $tickets = [];

        switch ($section) {
            case 'recent':
                $title = 'All Recent Tickets';
                $tickets = $db->createCommand(
                    "SELECT t.*, 
                            CONCAT(su.first_name, ' ', su.last_name) as created_by_name,
                            CONCAT(au.first_name, ' ', au.last_name) as assigned_to_name
                     FROM tickets t
                     LEFT JOIN system_users su ON t.created_by = su.id
                     LEFT JOIN system_users au ON t.assigned_to = au.id
                     WHERE 
                      ( t.assigned_to = '$uid'  OR t.created_by = '$uid'  )
                     ORDER BY t.created_at DESC
                     LIMIT :l",
                    [':l' => $lim]
                )->queryAll();
                break;

            case 'to-handle':
                $title = 'Tickets to Handle';
                $tickets = $db->createCommand(
                    "SELECT t.*, 
                            CONCAT(su.first_name, ' ', su.last_name) as created_by_name,
                            CONCAT(au.first_name, ' ', au.last_name) as assigned_to_name
                     FROM tickets t
                     LEFT JOIN system_users su ON t.created_by = su.id
                     LEFT JOIN system_users au ON t.assigned_to = au.id
                     WHERE t.status IN ('Open', 'Pending')
                       AND (t.assigned_to = :uid OR t.assigned_to IS NULL)
                     ORDER BY t.priority = 'High' DESC, t.created_at ASC",
                    [':uid' => $uid]
                )->queryAll();
                break;

            case 'my-open':
                $title = 'My Open Tickets';
                $tickets = $db->createCommand(
                    "SELECT t.*, 
                            CONCAT(su.first_name, ' ', su.last_name) as created_by_name,
                            CONCAT(au.first_name, ' ', au.last_name) as assigned_to_name
                     FROM tickets t
                     LEFT JOIN system_users su ON t.created_by = su.id
                     LEFT JOIN system_users au ON t.assigned_to = au.id
                     WHERE t.created_by = :uid
                       AND t.status IN ('Open', 'Pending', 'On hold')
                     ORDER BY t.created_at DESC",
                    [':uid' => $uid]
                )->queryAll();
                break;

            case 'last-7-days':
                $title = 'My Tickets in Last 7 Days';
                $tickets = $db->createCommand(
                    "SELECT t.*, 
                            CONCAT(su.first_name, ' ', su.last_name) as created_by_name,
                            CONCAT(au.first_name, ' ', au.last_name) as assigned_to_name
                     FROM tickets t
                     LEFT JOIN system_users su ON t.created_by = su.id
                     LEFT JOIN system_users au ON t.assigned_to = au.id
                     WHERE t.created_by = :uid
                       AND t.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                     ORDER BY t.created_at DESC",
                    [':uid' => $uid]
                )->queryAll();
                break;

            case 'status-open':
            case 'status-pending':
            case 'status-hold':
            case 'status-solved':
            case 'status-closed':
                $statusMap = [
                    'status-open' => 'Open',
                    'status-pending' => 'Pending',
                    'status-hold' => 'On hold',
                    'status-solved' => 'Solved',
                    'status-closed' => 'Closed',
                ];
                $status = $statusMap[$section];
                $title = $status . ' Tickets';

                $tickets = $db->createCommand(
                    "SELECT t.*, 
                            CONCAT(su.first_name, ' ', su.last_name) as created_by_name,
                            CONCAT(au.first_name, ' ', au.last_name) as assigned_to_name
                     FROM tickets t
                     LEFT JOIN system_users su ON t.created_by = su.id
                     LEFT JOIN system_users au ON t.assigned_to = au.id
                     WHERE t.status = :st
                      AND ( t.assigned_to = '$uid'  OR t.created_by = '$uid'  )
                     ORDER BY t.updated_at DESC",
                    [':st' => $status]
                )->queryAll();
                break;

            case 'folder-archive':
            case 'folder-spam':
            case 'folder-trash':
                $folderTitle = [
                    'folder-archive' => 'Archived Tickets',
                    'folder-spam' => 'Spam Tickets',
                    'folder-trash' => 'Trash Tickets',
                ];
                return $this->renderPartial('_ticket_list', [
                    'title' => $folderTitle[$section],
                    'tickets' => []
                ]);

            case 'create-ticket':
                $ticket_no = $db->createCommand("SELECT MAX(id) as tk_no FROM tickets")->queryOne();
                $new_no = ((int)$ticket_no['tk_no']) + 1;
                $employees = $db->createCommand("
                    SELECT  
                        id,  CONCAT(first_name, ' ', last_name,' (', (SELECT roles.name FROM roles WHERE id = role_id),')')  as name
                    FROM `system_users` ORDER BY role_id;")->queryAll();
                return $this->renderPartial('_create_ticket_form', ['tk_no' => $new_no, 'employees' => $employees]);

            default:
                if (preg_match('/^ticket-(\d+)$/', $section, $m)) {
                    return $this->renderPartial('_ticket_view', ['ticket_id' => $m[1]]);
                }
                return $this->renderPartial('nothingfound');
        }

        return $this->renderPartial('_ticket_list', [
            'title' => $title,
            'tickets' => $tickets,
        ]);
    }


    public function actionCreate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $db = Yii::$app->db;

        $post = $request->post();
        $files = UploadedFile::getInstancesByName('attachments');

        $ticketId         = $post['ticket_id'] ?? null;
        $title            = trim($post['title'] ?? '');
        $assignedTo       = $post['assigned_to'] ?? null;
        $priority         = $post['priority'] ?? 'Medium';
        $requesterName    = $post['requester_name'] ?? null;
        $requesterEmail   = $post['requester_email'] ?? null;
        $status           = $post['status'] ?? 'Open';
        $category         = $post['category'] ?? 'General';
        $description      = trim($post['description'] ?? '');
        $createdBy        = Yii::$app->user->id ?? ($_SESSION['user_array']['id'] ?? null);
        $now              = date('Y-m-d H:i:s');

        if (!$title || !$description) {
            return ['success' => false, 'message' => 'Title and Description are required.'];
        }

        try {
            // Insert or Update ticket
            if ($ticketId) {
                $db->createCommand()->update('tickets', [
                    'title'          => $title,
                    'description'    => $description,
                    'assigned_to'    => $assignedTo,
                    'priority'       => $priority,
                    'requester_name' => $requesterName,
                    'requester_email' => $requesterEmail,
                    'status'         => $status,
                    'category'       => $category,
                    'updated_at'     => $now,
                ], ['id' => $ticketId])->execute();
            } else {
                $db->createCommand()->insert('tickets', [
                    'title'          => $title,
                    'description'    => $description,
                    'assigned_to'    => $assignedTo,
                    'priority'       => $priority,
                    'status'         => $status,
                    'category'       => $category,
                    'requester_name' => $requesterName,
                    'requester_email' => $requesterEmail,
                    'created_by'     => $createdBy,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ])->execute();
                $ticketId = $db->getLastInsertID();

                // Log ticket creation
                $db->createCommand()->insert('ticket_logs', [
                    'ticket_id' => $ticketId,
                    'user_id' => $createdBy,
                    'action' => 'created',
                    'field_name' => null,
                    'old_value' => null,
                    'new_value' => null,
                    'created_at' => $now
                ])->execute();
            }

            // Handle File Uploads
            if (!empty($files)) {
                $uploadPath = Yii::getAlias('@webroot/ticketing');

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                foreach ($files as $file) {
                    $safeFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->name);
                    $fullPath = $uploadPath . '/' . $safeFileName;

                    if ($file->saveAs($fullPath)) {
                        $db->createCommand()->insert('ticket_files', [
                            'ticket_id'   => $ticketId,
                            'file_name'   => $file->name,
                            'file_path'   => 'ticketing/' . $safeFileName,
                            'uploaded_at' => $now,
                        ])->execute();
                    }
                }
            }

            return ['success' => true, 'message' => 'Ticket saved successfully.', 'ticket_id' => $ticketId];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }


    public function actionUpdate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $db = Yii::$app->db;

        $ticketId = $request->post('ticket_id');
        $field = $request->post('field');
        $value = $request->post('value');
        $userId = Yii::$app->user->id ?? ($_SESSION['user_array']['id'] ?? null);

        if (!$ticketId || !$field) {
            return ['success' => false, 'message' => 'Invalid parameters.'];
        }

        $allowedFields = ['status', 'priority', 'assigned_to', 'category'];
        if (!in_array($field, $allowedFields)) {
            return ['success' => false, 'message' => 'Invalid field.'];
        }

        try {
            // Get old value before update
            $oldTicket = $db->createCommand("SELECT * FROM tickets WHERE id = :id", [':id' => $ticketId])->queryOne();
            $oldValue = $oldTicket[$field] ?? null;

            // Check if ticket is locked (Solved or Closed)
            if (in_array($oldTicket['status'], ['Solved', 'Closed']) && $field !== 'status') {
                return ['success' => false, 'message' => 'This ticket is ' . $oldTicket['status'] . ' and cannot be modified.'];
            }

            // Update ticket
            $db->createCommand()->update('tickets', [
                $field => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $ticketId])->execute();

            // Log the change
            if ($oldValue != $value) {
                // Get user names for assigned_to field
                $oldDisplayValue = $oldValue;
                $newDisplayValue = $value;

                if ($field === 'assigned_to') {
                    if ($oldValue) {
                        $oldUser = $db->createCommand("SELECT CONCAT(first_name, ' ', last_name) as name FROM system_users WHERE id = :id", [':id' => $oldValue])->queryScalar();
                        $oldDisplayValue = $oldUser ?: 'Unknown';
                    } else {
                        $oldDisplayValue = 'Unassigned';
                    }

                    if ($value) {
                        $newUser = $db->createCommand("SELECT CONCAT(first_name, ' ', last_name) as name FROM system_users WHERE id = :id", [':id' => $value])->queryScalar();
                        $newDisplayValue = $newUser ?: 'Unknown';
                    } else {
                        $newDisplayValue = 'Unassigned';
                    }
                }

                $db->createCommand()->insert('ticket_logs', [
                    'ticket_id' => $ticketId,
                    'user_id' => $userId,
                    'action' => 'updated',
                    'field_name' => $field,
                    'old_value' => $oldDisplayValue,
                    'new_value' => $newDisplayValue,
                    'created_at' => date('Y-m-d H:i:s')
                ])->execute();
            }

            // Get updated ticket with user info
            $updatedTicket = $db->createCommand(
                "SELECT t.*, 
                        CONCAT(au.first_name, ' ', au.last_name) as assigned_to_name
                 FROM tickets t
                 LEFT JOIN system_users au ON t.assigned_to = au.id
                 WHERE t.id = :id",
                [':id' => $ticketId]
            )->queryOne();

            // Get the latest activity log entry
            $latestLog = $db->createCommand(
                "SELECT tl.*, CONCAT(su.first_name, ' ', su.last_name) as user_name
                 FROM ticket_logs tl
                 LEFT JOIN system_users su ON tl.user_id = su.id
                 WHERE tl.ticket_id = :id
                 ORDER BY tl.created_at DESC
                 LIMIT 1",
                [':id' => $ticketId]
            )->queryOne();

            return [
                'success' => true,
                'message' => ucfirst(str_replace('_', ' ', $field)) . ' updated successfully.',
                'ticket' => $updatedTicket,
                'latestLog' => $latestLog,
                'shouldLock' => in_array($updatedTicket['status'], ['Solved', 'Closed'])
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }


    public function actionReply()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $db = Yii::$app->db;

        $ticketId = $request->post('ticket_id');
        $message = trim($request->post('message') ?? '');
        $repliedBy = Yii::$app->user->id ?? ($_SESSION['user_array']['id'] ?? null);
        $file = UploadedFile::getInstanceByName('file');

        if (!$ticketId || !$message) {
            return ['success' => false, 'message' => 'Ticket ID and message are required.'];
        }

        // Check if ticket is locked (Solved or Closed)
        $ticket = $db->createCommand("SELECT status FROM tickets WHERE id = :id", [':id' => $ticketId])->queryOne();
        if ($ticket && in_array($ticket['status'], ['Solved', 'Closed'])) {
            return ['success' => false, 'message' => 'Cannot reply to a ' . $ticket['status'] . ' ticket.'];
        }

        try {
            $filePath = null;

            // Handle file upload if present
            if ($file) {
                $uploadPath = Yii::getAlias('@webroot/ticketing');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $safeFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->name);
                $fullPath = $uploadPath . '/' . $safeFileName;

                if ($file->saveAs($fullPath)) {
                    $filePath = 'ticketing/' . $safeFileName;
                }
            }

            // Insert reply
            $db->createCommand()->insert('ticket_replies', [
                'ticket_id' => $ticketId,
                'message' => $message,
                'file' => $filePath,
                'replied_by' => $repliedBy,
                'replied_at' => date('Y-m-d H:i:s')
            ])->execute();

            // Update ticket timestamp
            $db->createCommand()->update('tickets', [
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $ticketId])->execute();

            // Log the reply action
            $db->createCommand()->insert('ticket_logs', [
                'ticket_id' => $ticketId,
                'user_id' => $repliedBy,
                'action' => 'replied',
                'field_name' => null,
                'old_value' => null,
                'new_value' => null,
                'created_at' => date('Y-m-d H:i:s')
            ])->execute();

            return ['success' => true, 'message' => 'Reply added successfully.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }


    public function actionDelete()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $db = Yii::$app->db;

        $ticketId = $request->post('ticket_id');

        if (!$ticketId) {
            return ['success' => false, 'message' => 'Ticket ID is required.'];
        }

        try {
            // Delete ticket (cascade will handle replies and files)
            $db->createCommand()->delete('tickets', ['id' => $ticketId])->execute();

            return ['success' => true, 'message' => 'Ticket deleted successfully.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }


    public function actionSearch()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        $db = Yii::$app->db;
        $request = Yii::$app->request;

        $searchTerm = trim($request->get('q') ?? '');

        if (empty($searchTerm)) {
            return $this->renderPartial('_ticket_list', [
                'title' => 'Search Results',
                'tickets' => []
            ]);
        }

        try {
            $tickets = $db->createCommand(
                "SELECT t.*, 
                        CONCAT(su.first_name, ' ', su.last_name) as created_by_name,
                        CONCAT(au.first_name, ' ', au.last_name) as assigned_to_name
                 FROM tickets t
                 LEFT JOIN system_users su ON t.created_by = su.id
                 LEFT JOIN system_users au ON t.assigned_to = au.id
                 WHERE t.title LIKE :search 
                    OR t.description LIKE :search
                    OR t.id = :id
                 ORDER BY t.created_at DESC
                 LIMIT 50",
                [
                    ':search' => '%' . $searchTerm . '%',
                    ':id' => is_numeric($searchTerm) ? $searchTerm : 0
                ]
            )->queryAll();

            return $this->renderPartial('_ticket_list', [
                'title' => 'Search Results for: ' . htmlspecialchars($searchTerm),
                'tickets' => $tickets
            ]);
        } catch (\Exception $e) {
            return $this->renderPartial('_ticket_list', [
                'title' => 'Search Results',
                'tickets' => []
            ]);
        }
    }
}
