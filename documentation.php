<?php
/**
 * Tundra Lead Capture System - Client Documentation
 * Updated for LeadForm ID, SMTP Mailer, and Frontend Integration
 * * @author Digitally Disruptive - Donald Raymundo
 * @link https://digitallydisruptive.co.uk/
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Documentation</title>
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; max-width: 800px; margin: 40px auto; padding: 20px; color: #333; }
        code { background: #f4f4f4; padding: 2px 5px; border-radius: 4px; font-family: monospace; color: #d63384; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto; border: 1px solid #ddd; font-size: 0.9em; }
        h1, h2, h3 { color: #2DA1FF; }
        h3 { margin-top: 1.5em; color: #334155; }
        .alert { background: #fff3cd; padding: 10px; border-left: 5px solid #ffc107; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>System Implementation Guide</h1>
    <div class="alert">
        <strong>Important:</strong> Changes in the Admin Panel are not saved until you click the <strong>"Deploy Settings"</strong> button.
    </div>

    <h2>1. Frontend Implementation (Modifying index.php)</h2>
    <p>To fully integrate the form and reCAPTCHA protection, you must modify your <code>index.php</code> file in three specific places.</p>

    <h3>Step 1.1: Configuration Extraction (Top of Page)</h3>
    <p>Add this PHP block at the very top of your <code>index.php</code> file (before any HTML output) to securely extract the reCAPTCHA Site Key from the system configuration.</p>
<pre>&lt;?php
/**
 * Securely extracts the public reCAPTCHA Site Key from the system configuration.
 */
$config_file = __DIR__ . '/config.json';
$recaptcha_site_key = '';

if (file_exists($config_file)) {
    $config = json_decode(file_get_contents($config_file), true);
    if (isset($config['recaptcha_enabled']) && $config['recaptcha_enabled'] && !empty($config['recaptcha_site_key'])) {
        $recaptcha_site_key = $config['recaptcha_site_key'];
    }
}
?&gt;</pre>

    <h3>Step 1.2: Form Configuration</h3>
    <p>Ensure your form uses the ID <code>LeadForm</code>. If the ID does not match, the submission logic will fail to capture the data.</p>
<pre>&lt;form id="LeadForm" ... &gt;
    &lt;!-- Hidden UTM fields are recommended --&gt;
    &lt;input type="hidden" name="utm_source" value=""&gt;
&lt;/form&gt;</pre>

    <h3>Step 1.3: Script Loading (Bottom of Page)</h3>
    <p>Add this block right before the closing <code>&lt;/body&gt;</code> tag to pass the configuration variables to the frontend and load the main processor script.</p>
<pre>&lt;script&gt;
    window.TUNDRA_CONFIG = {
        recaptchaSiteKey: '&lt;?php echo htmlspecialchars($recaptcha_site_key, ENT_QUOTES, \'UTF-8\'); ?&gt;'
    };
&lt;/script&gt;
&lt;script src="assets/js/main.js" defer&gt;&lt;/script&gt;</pre>

    <h2>2. Email Builder & Template Management</h2>
    <p>You can manage multiple email templates using the Repeater interface in the Admin Panel:</p>
    <ul>
        <li><strong>Tokens:</strong> Click any token badge (e.g., <code>{full_name}</code>) to copy it to your clipboard. These are dynamically generated based on your CSV headers.</li>
        <li><strong>Headers:</strong> You can define custom "From Name", "From Email", CC, and BCC headers per template.</li>
        <li><strong>Live Preview:</strong> As you type in the HTML Body field, the system will render a live preview on the right-hand side.</li>
    </ul>

    <h2>3. SMTP Configuration</h2>
    <p>For better deliverability (SendGrid, Amazon SES, etc.), enable SMTP in the Integrations tab:</p>
    <ul>
        <li><strong>Encryption:</strong> Match the setting to your port:
            <ul>
                <li>Use <strong>TLS</strong> for Port 587.</li>
                <li>Use <strong>SSL</strong> for Port 465.</li>
            </ul>
        </li>
        <li><strong>Authentication:</strong> Ensure your API key or Username/Password are correctly populated.</li>
    </ul>

    <h2>4. Lead Data & CSV Logging</h2>
    <p>The system automatically logs every submission to a CSV file in <code>assets/data/</code>.</p>
    <ul>
        <li><strong>Viewer:</strong> Use the "Lead Data" tab to view recent submissions directly in the browser.</li>
        <li><strong>Management:</strong> You can select different CSV files if you have multiple logs running.</li>
        <li><strong>Export:</strong> Click the "Download CSV" button to export your data for external use (e.g., uploading to a CRM).</li>
    </ul>

    <h2>5. Advanced Integrations</h2>
    <ul>
        <li><strong>reCAPTCHA v3:</strong> The backend verifies the score automatically. Ensure your Site and Secret keys are valid in the Integrations tab.</li>
        <li><strong>Zapier Webhook:</strong> Ensure your JSON mapping has valid syntax (commas separating items). If you add a field in the form, add the mapping in the "Payload Mapping" repeater section.</li>
    </ul>

    <hr>
    <p><small>Developed by <strong>Digitally Disruptive - Donald Raymundo</strong>. Support: <a href="https://digitallydisruptive.co.uk/">digitallydisruptive.co.uk</a></small></p>
</body>
</html>