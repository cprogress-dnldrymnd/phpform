<?php

/**
 * Tundra Lead Capture System - Client Documentation
 * @author Digitally Disruptive - Donald Raymundo
 * @link https://digitallydisruptive.co.uk/
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>System Documentation</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            color: #333;
        }

        code {
            background: #f4f4f4;
            padding: 2px 5px;
            border-radius: 4px;
            font-family: monospace;
        }

        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }

        h1,
        h2 {
            color: #2DA1FF;
        }
    </style>
</head>

<body>
    <h1>System Implementation Guide</h1>

    <h2>1. reCAPTCHA v3 Integration</h2>
    <p>This system uses Google reCAPTCHA v3 (Score-based) to prevent spam.</p>
    <ol>
        <li><strong>Get Keys:</strong> Register your domain at the <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Console</a> and generate a Site Key and Secret Key.</li>
        <li><strong>Admin Configuration:</strong> Login to your Admin Panel, navigate to <strong>Integrations</strong>, and paste your keys. Ensure you check "Enable Google reCAPTCHA v3".</li>
        <li><strong>Frontend:</strong> The system automatically loads the security script via your <code>main.js</code> file. No manual script tags are required in your <code>index.php</code> footer.</li>
    </ol>

    <h2>2. Modifying index.php</h2>
    <p>To ensure the form processes correctly, your <code>index.php</code> must adhere to these requirements:</p>
    <ul>
        <li><strong>Form ID:</strong> Your form must have the ID <code>tundraLeadForm</code>.</li>
        <li><strong>Hidden Fields:</strong> Include the standard hidden inputs for UTM tracking so the system can capture marketing source data:</li>
    </ul>
    <pre>
&lt;form id="tundraLeadForm" ... &gt;
    &lt;input type="hidden" name="utm_source" value=""&gt;
    &lt;input type="hidden" name="utm_medium" value=""&gt;
    &lt;input type="hidden" name="utm_campaign" value=""&gt;
    &lt;!-- ... rest of form ... --&gt;
&lt;/form&gt;</pre>

    <h2>3. SMTP Mailer Setup</h2>
    <p>If you prefer not to use the default PHP <code>mail()</code> function, you can enable SMTP (e.g., SendGrid, Mailgun, Amazon SES) in the Integrations tab.</p>
    <ul>
        <li><strong>Host/Port:</strong> Set these according to your email provider.</li>
        <li><strong>Encryption:</strong> Always select the encryption type that matches your port (typically TLS for 587, SSL for 465).</li>
    </ul>

    <h2>4. Zapier Integration</h2>
    <p>Automate your leads by passing data directly to a webhook.</p>
    <ol>
        <li>Create a "Webhook by Zapier" trigger in your Zap.</li>
        <li>Copy the URL provided by Zapier and paste it into the <strong>Webhook URL</strong> field in the Admin Panel.</li>
        <li>Use the <strong>Payload Mapping</strong> repeater to define exactly which form fields (e.g., <code>first-name</code>) map to which Zapier keys.</li>
        <li>Click <strong>Deploy Settings</strong> and run a test submission on your live site.</li>
    </ol>

    <hr>
    <p><small>System developed by <strong>Digitally Disruptive - Donald Raymundo</strong>. For support, visit <a href="https://digitallydisruptive.co.uk/">digitallydisruptive.co.uk</a>.</small></p>
</body>

</html>