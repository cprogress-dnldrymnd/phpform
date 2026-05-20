<?php
/**
 * AJAX Endpoint for processing dynamic lead form submissions.
 * Incorporates reCAPTCHA v3 cURL validation, dynamic CSV logging,
 * Zapier webhook routing, email template parsing, and dynamic success messaging.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$config_file = __DIR__ . '/config.json';
if (!file_exists($config_file)) {
    echo json_encode(['success' => false, 'message' => 'System configuration error.']);
    exit;
}

$config = json_decode(file_get_contents($config_file), true);

// 1. Google reCAPTCHA v3 Verification
if (isset($config['recaptcha_enabled']) && $config['recaptcha_enabled']) {
    $recaptcha_response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
    
    if (empty($recaptcha_response)) {
        echo json_encode(['success' => false, 'message' => 'Security check failed. Please refresh and try again.']);
        exit;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'secret'   => $config['recaptcha_secret_key'],
        'response' => $recaptcha_response
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $verify_response = curl_exec($ch);
    curl_close($ch);

    $response_data = json_decode($verify_response);
    
    if (!$response_data->success || $response_data->score < 0.5) {
        echo json_encode(['success' => false, 'message' => 'Automated bot activity detected. Request denied.']);
        exit;
    }
}

// 2. Dynamic Token & CSV Data Generation
$search  = [];
$replace = [];
$csv_headers = ['Timestamp'];
$csv_values  = [date('Y-m-d H:i:s')];
$reply_to    = '';

foreach ($_POST as $key => $value) {
    if ($key === 'g-recaptcha-response') continue; 

    $safe_key = htmlspecialchars(strip_tags($key));
    
    if (is_array($value)) {
        $safe_val = implode(', ', array_map('htmlspecialchars', array_map('strip_tags', $value)));
    } else {
        $safe_val = htmlspecialchars(strip_tags($value));
    }

    $search[]  = '{' . $safe_key . '}';
    $replace[] = $safe_val;
    $csv_headers[] = $safe_key;
    $csv_values[]  = $safe_val;

    if (empty($reply_to) && filter_var($safe_val, FILTER_VALIDATE_EMAIL)) {
        $reply_to = $safe_val;
    }
}

if (empty($reply_to)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

// 3. CSV Logging Execution
if (isset($config['enable_csv_logging']) && $config['enable_csv_logging']) {
    $filename = !empty($config['csv_file_name']) ? basename($config['csv_file_name']) : 'leads.csv';
    if (substr($filename, -4) !== '.csv') {
        $filename .= '.csv';
    }
    
    $csv_file = __DIR__ . '/assets/data/' . $filename;
    $csv_dir  = dirname($csv_file);
    
    if (!is_dir($csv_dir)) {
        mkdir($csv_dir, 0755, true);
    }

    $needs_headers = !file_exists($csv_file) || filesize($csv_file) === 0;
    $fp = fopen($csv_file, 'a');
    
    if ($fp) {
        if ($needs_headers) {
            fputcsv($fp, $csv_headers);
        }
        fputcsv($fp, $csv_values);
        fclose($fp);
    }
}

// 4. Attachment & Routing Logic
$has_attachment  = isset($config['has_attachment']) ? $config['has_attachment'] : true;
$download_url    = ($has_attachment && isset($config['download_url'])) ? trim($config['download_url']) : '';
$download_method = ($has_attachment && isset($config['download_method'])) ? $config['download_method'] : 'none';

if ($download_url !== '' && !preg_match('~^(?:f|ht)tps?://~i', $download_url)) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $base_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    $download_url = $protocol . $host . $base_dir . '/' . ltrim($download_url, '/');
}

$button_text = (isset($config['download_button_text']) && trim($config['download_button_text']) !== '') 
    ? trim($config['download_button_text']) 
    : 'Download Payload Specs';

$download_button = $download_url 
    ? '<a href="' . htmlspecialchars($download_url) . '" style="display: inline-block; background-color: #2DA1FF; color: #0a0a0a; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif; font-size: 14px;">' . htmlspecialchars($button_text) . '</a>' 
    : '';

$search[] = '{download_link}';
$search[] = '{download_button}';
$replace[] = $download_url;
$replace[] = $download_button;


// 5. Zapier Webhook Integration
if (isset($config['zapier_enabled']) && $config['zapier_enabled'] && !empty($config['zapier_webhook_url'])) {
    
    $zapier_url = $config['zapier_webhook_url'];
    $zapier_template = isset($config['zapier_payload']) && !empty(trim($config['zapier_payload'])) ? $config['zapier_payload'] : '{}';
    
    // Safely parse JSON template to prevent syntax breakage from user input quotes
    $zapier_data = json_decode($zapier_template, true);
    
    if (is_array($zapier_data)) {
        array_walk_recursive($zapier_data, function(&$item, $key) use ($search, $replace) {
            if (is_string($item)) {
                $item = str_replace($search, $replace, $item);
            }
        });
        $zapier_json_safe = json_encode($zapier_data);
    } else {
        // Fallback to raw string replacement if the admin provided an invalid JSON format
        $zapier_json_safe = str_replace($search, $replace, $zapier_template);
    }
    
    $ch_z = curl_init($zapier_url);
    curl_setopt($ch_z, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch_z, CURLOPT_POSTFIELDS, $zapier_json_safe);
    curl_setopt($ch_z, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_z, CURLOPT_TIMEOUT, 3); // 3-second timeout prevents Zapier outages from hanging the user UI
    curl_setopt($ch_z, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($zapier_json_safe)
    ]);
  $result = curl_exec($ch_z);
    $http_code = curl_getinfo($ch_z, CURLINFO_HTTP_CODE);
    
    // DEBUG LOG: Write the response to a file in your assets/data folder
    file_put_contents(__DIR__ . '/assets/data/zapier_debug.log', 
        "[" . date('Y-m-d H:i:s') . "] Status: $http_code | Payload: $zapier_json_safe | Response: $result" . PHP_EOL, 
        FILE_APPEND
    );
    
    curl_close($ch_z);
}


// 6. Dispatch Emails
$headers  = "From: no-reply@" . $_SERVER['SERVER_NAME'] . "\r\n";
$headers .= "Reply-To: " . $reply_to . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$templates = isset($config['email_templates']) ? $config['email_templates'] : [];

foreach ($templates as $tpl) {
    $to = str_replace($search, $replace, $tpl['to']);
    
    if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $subject = str_replace($search, $replace, $tpl['subject']);
        $body    = str_replace($search, $replace, $tpl['body']);
        
        mail($to, $subject, $body, $headers);
    }
}

// 7. Dynamic Success Messaging
$has_auto_download = ($has_attachment && $download_url !== '' && in_array($download_method, ['auto', 'both']));
$base_success_msg  = isset($config['success_message']) ? $config['success_message'] : 'Success! Your request has been processed.';

$parsed_success_msg = str_replace($search, $replace, $base_success_msg);

echo json_encode([
    'success'      => true,
    'message'      => $parsed_success_msg,
    'download_url' => $has_auto_download ? $download_url : null
]);