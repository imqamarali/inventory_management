<?php

/**
 * Webhook Endpoint
 * 
 * Subscribe to changes and receive updates in real time without calling the API.
 * This endpoint handles incoming webhook requests from various services.
 * 
 * Usage:
 * - Configure webhook URL: https://yourdomain.com/web/webhooks/
 * - Supports POST requests with JSON payloads
 * - Optional signature verification for security
 */

// Set headers for JSON response
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST requests are accepted.'
    ]);
    exit;
}

// Retrieve the raw request body
$rawBody = file_get_contents('php://input');

// Check if body is empty
if (empty($rawBody)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Empty request body'
    ]);
    exit;
}

// Decode the JSON payload
$data = json_decode($rawBody, true);

// Check if JSON decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON: ' . json_last_error_msg()
    ]);
    exit;
}

// Get request headers
$headers = getallheaders();

// Optional: Verify webhook signature (uncomment and configure as needed)
/*
$secretKey = 'your-webhook-secret-key'; // Set this in your configuration
$signature = $headers['X-Hub-Signature-256'] ?? $headers['X-Hub-Signature'] ?? '';

if (!empty($signature) && !empty($secretKey)) {
    $expectedSignature = 'sha256=' . hash_hmac('sha256', $rawBody, $secretKey);
    
    if (!hash_equals($expectedSignature, $signature)) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid signature'
        ]);
        exit;
    }
}
*/

// Log the webhook data (optional - for debugging)
$logFile = __DIR__ . '/webhook_log.txt';
$logEntry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'headers' => $headers,
    'payload' => $data,
    'raw_body' => $rawBody,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
];

file_put_contents($logFile, json_encode($logEntry, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);

// Process the webhook data
try {
    // Extract event type (common webhook patterns)
    $eventType = $data['event'] ?? $data['type'] ?? $data['action'] ?? 'unknown';
    $eventId = $data['id'] ?? $data['webhook_id'] ?? null;

    // Initialize Yii2 application if needed (optional)
    // Uncomment if you need to use Yii2 components
    /*
    require __DIR__ . '/../../vendor/autoload.php';
    require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
    $config = require __DIR__ . '/../../config/web.php';
    new yii\web\Application($config);
    
    // Example: Save to database using Yii2
    // Yii::$app->db->createCommand()->insert('webhook_logs', [
    //     'event_type' => $eventType,
    //     'event_id' => $eventId,
    //     'payload' => json_encode($data),
    //     'created_at' => date('Y-m-d H:i:s')
    // ])->execute();
    */

    // Process different event types
    switch ($eventType) {
        case 'payment.completed':
        case 'payment.succeeded':
            // Handle payment webhooks
            processPaymentWebhook($data);
            break;

        case 'subscription.created':
        case 'subscription.updated':
        case 'subscription.cancelled':
            // Handle subscription webhooks
            processSubscriptionWebhook($data);
            break;

        case 'user.created':
        case 'user.updated':
        case 'user.deleted':
            // Handle user webhooks
            processUserWebhook($data);
            break;

        default:
            // Handle generic webhooks
            processGenericWebhook($data, $eventType);
            break;
    }

    // Respond with success
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Webhook received and processed successfully',
        'event_type' => $eventType,
        'event_id' => $eventId,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    // Log error
    error_log('Webhook processing error: ' . $e->getMessage());

    // Still return 200 to prevent webhook retries (or return 500 if you want retries)
    http_response_code(200);
    echo json_encode([
        'success' => false,
        'message' => 'Error processing webhook: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Process payment-related webhooks
 */
function processPaymentWebhook($data)
{
    // Implement your payment processing logic here
    // Example: Update payment status in database, send notifications, etc.

    error_log('Processing payment webhook: ' . json_encode($data));

    // Add your custom logic here
}

/**
 * Process subscription-related webhooks
 */
function processSubscriptionWebhook($data)
{
    // Implement your subscription processing logic here
    // Example: Update subscription status, handle renewals, etc.

    error_log('Processing subscription webhook: ' . json_encode($data));

    // Add your custom logic here
}

/**
 * Process user-related webhooks
 */
function processUserWebhook($data)
{
    // Implement your user processing logic here
    // Example: Sync user data, update profiles, etc.

    error_log('Processing user webhook: ' . json_encode($data));

    // Add your custom logic here
}

/**
 * Process generic webhooks
 */
function processGenericWebhook($data, $eventType)
{
    // Implement your generic webhook processing logic here

    error_log("Processing generic webhook [{$eventType}]: " . json_encode($data));

    // Add your custom logic here
}
