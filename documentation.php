<?php
/**
 * Tundra Lead Capture System - Client Documentation
 * Updated for LeadForm ID & SMTP Mailer
 * @author Digitally Disruptive - Donald Raymundo
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
        pre { background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto; border: 1px solid #ddd; }
        h1, h2 { color: #2DA1FF; }
        .alert { background: #fff3cd; padding: 10px; border-left: 5px solid #ffc107; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>System Implementation Guide</h1>
    <div class="alert">
        <strong>Important:</strong> Changes in the Admin Panel are not saved until you click the <strong>"Deploy Settings"</strong> button.
    </div>

    <h2>1. Frontend Implementation</h2>
    <p>Ensure your form in <code>index.php</code> uses the ID <code>LeadForm</code>. If the ID does not match, the submission logic will fail.</p>
    <pre>&lt;form id="LeadForm" ... &gt;
    &lt;!-- Hidden UTM fields are recommended --&gt;
    &lt;input type="hidden" name="utm_source" value=""&gt;
&lt;/form&gt;</pre>

    <h2>2. Email Builder & Template Management</h2>
    <p>You can manage multiple email templates using the Repeater interface:</p>
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
        <li><strong>Viewer:</strong> Use the "Lead Data" tab to view recent submissions.</li>
        <li><strong>Management:</strong> You can select different CSV files if you have multiple logs running.</li>
        <li><strong>Export:</strong> Click the "Download CSV" button to export your data for external use (e.g., uploading to a CRM).</li>
    </ul>

    <h2>5. Advanced Integrations</h2>
    <ul>
        <li><strong>reCAPTCHA v3:</strong> Automatically injected. Ensure your Site and Secret keys are valid in the Integrations tab.</li>
        <li><strong>Zapier Webhook:</strong> Ensure your JSON mapping has valid syntax (commas separating items). If you add a field in the form, add the mapping in the "Payload Mapping" repeater section.</li>
    </ul>

    <hr>
    <p><small>Developed by <strong>Digitally Disruptive - Donald Raymundo</strong>. Support: <a href="https://digitallydisruptive.co.uk/">digitallydisruptive.co.uk</a></small></p>
</body>
</html>