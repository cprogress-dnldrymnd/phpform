<?php
/**
 * Advanced Standalone Admin Panel.
 * Implements a tabbed interface, JSON-state-managed repeater fields, 
 * dynamic file scanning, dynamic token generation, CSV viewer, and API integrations.
 *
 * @author Digitally Disruptive - Donald Raymundo
 * @link https://digitallydisruptive.co.uk/
 */

session_start();

$config_file = __DIR__ . '/config.json';
$data_dir = __DIR__ . '/assets/data/';

function get_config($file) {
    if (!file_exists($file)) die("Configuration file not found.");
    return json_decode(file_get_contents($file), true);
}

function save_config($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function get_available_downloads() {
    $download_dir = __DIR__ . '/assets/downloads/';
    $available_files = [];
    if (is_dir($download_dir)) {
        $files = array_diff(scandir($download_dir), array('.', '..', '.htaccess'));
        foreach ($files as $file) {
            if (is_file($download_dir . $file)) {
                $available_files[] = 'assets/downloads/' . $file;
            }
        }
    }
    return $available_files;
}

$config = get_config($config_file);
$error = '';
$success = '';

// Authentication
if (isset($_POST['login'])) {
    if ($_POST['password'] === $config['admin_password']) {
        $_SESSION['logged_in'] = true;
    } else {
        $error = 'Invalid password.';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Data Processing
if (isset($_POST['update_settings']) && isset($_SESSION['logged_in'])) {
    $config['has_attachment']     = isset($_POST['has_attachment']) ? true : false;
    $config['enable_csv_logging'] = isset($_POST['enable_csv_logging']) ? true : false;
    
    // CSV Settings
    $csv_name = isset($_POST['csv_file_name']) ? basename(htmlspecialchars(strip_tags($_POST['csv_file_name']))) : 'leads.csv';
    if (!empty($csv_name) && substr($csv_name, -4) !== '.csv') {
        $csv_name .= '.csv';
    }
    $config['csv_file_name'] = $csv_name;
    
    if ($config['enable_csv_logging']) {
        if (!is_dir($data_dir)) {
            mkdir($data_dir, 0755, true);
        }
    }
    
    // Form & Attachment Settings
    $config['download_url']         = filter_var($_POST['download_url'], FILTER_SANITIZE_URL);
    $config['download_method']      = isset($_POST['download_method']) ? htmlspecialchars($_POST['download_method']) : 'both';
    $config['download_button_text'] = isset($_POST['download_button_text']) ? htmlspecialchars(strip_tags($_POST['download_button_text'])) : 'Download Payload Specs';
    $config['success_message']      = isset($_POST['success_message']) ? htmlspecialchars(strip_tags($_POST['success_message'])) : 'Success! Your request has been processed.';
    
    // API Integrations
    $config['recaptcha_enabled']    = isset($_POST['recaptcha_enabled']) ? true : false;
    $config['recaptcha_site_key']   = isset($_POST['recaptcha_site_key']) ? htmlspecialchars(strip_tags($_POST['recaptcha_site_key'])) : '';
    $config['recaptcha_secret_key'] = isset($_POST['recaptcha_secret_key']) ? htmlspecialchars(strip_tags($_POST['recaptcha_secret_key'])) : '';
    
    if (isset($_POST['email_templates_json'])) {
        $templates = json_decode($_POST['email_templates_json'], true);
        if (is_array($templates)) {
            $config['email_templates'] = $templates;
        }
    }

    if (!empty($_POST['new_password'])) {
        $config['admin_password'] = $_POST['new_password'];
    }

    save_config($config_file, $config);
    $success = 'Settings updated successfully.';
}

$initial_templates = isset($config['email_templates']) ? $config['email_templates'] : [];
$local_files = get_available_downloads();

$available_csvs = [];
if (is_dir($data_dir)) {
    foreach (glob($data_dir . '*.csv') as $file) {
        $available_csvs[] = basename($file);
    }
}

$active_csv_setting = isset($config['csv_file_name']) ? $config['csv_file_name'] : 'leads.csv';
$current_view_csv = isset($_GET['view_csv']) && in_array($_GET['view_csv'], $available_csvs) ? $_GET['view_csv'] : $active_csv_setting;
$view_csv_path = $data_dir . $current_view_csv;

$active_tab = isset($_GET['view_csv']) ? 'tab-leads' : 'tab-form';

$dynamic_form_tokens = [];
$active_csv_path = $data_dir . $active_csv_setting;
if (file_exists($active_csv_path) && filesize($active_csv_path) > 0 && ($handle = fopen($active_csv_path, "r")) !== FALSE) {
    $headers = fgetcsv($handle, 1000, ",");
    if ($headers) {
        foreach ($headers as $header) {
            if (strtolower($header) !== 'timestamp') {
                $dynamic_form_tokens[] = '{' . htmlspecialchars($header) . '}';
            }
        }
    }
    fclose($handle);
}
if (empty($dynamic_form_tokens)) {
    $dynamic_form_tokens = ['{full_name}', '{work_email}', '{org_type}', '{phone}'];
}
$system_tokens = ['{download_link}', '{download_button}'];
$all_tokens = array_merge($dynamic_form_tokens, $system_tokens);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tundra Lead Capture Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --accent: #2DA1FF; --bg: #f4f6f9; --border: #e2e8f0; --text: #000000; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: var(--bg); color: var(--text); padding: 2rem; margin: 0; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .btn-logout { color: #ef4444; text-decoration: none; font-weight: 600; font-size: 0.9rem; }
        
        /* Tab Interface */
        .tabs { display: flex; gap: 1rem; margin-bottom: 0; border-bottom: 1px solid var(--border); }
        .tab-btn { background: transparent; border: none; padding: 1rem 2rem; font-size: 1rem; font-weight: 600; color: #000; cursor: pointer; border-bottom: 3px solid transparent; opacity: 0.5; transition: opacity 0.2s; }
        .tab-btn:hover { opacity: 0.8; }
        .tab-btn.active { color: var(--accent); border-bottom-color: var(--accent); opacity: 1; }
        .tab-content { display: none; background: #fff; padding: 2rem; border-radius: 0 0 8px 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .tab-content.active { display: block; }
        
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; }
        input[type="text"], input[type="password"], textarea, select { width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px; box-sizing: border-box; font-family: inherit; color: #000; }
        small { color: #000; opacity: 0.7; }
        
        /* Token Badges */
        .token-container { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem; }
        .token-badge { background: #f1f5f9; border: 1px solid var(--border); padding: 0.4rem 0.8rem; border-radius: 6px; font-family: monospace; font-size: 0.85rem; font-weight: 600; color: var(--accent); cursor: pointer; transition: all 0.15s ease; user-select: none; }
        .token-badge:hover { background: #e2e8f0; transform: translateY(-1px); }
        .token-badge.copied { background: var(--accent); color: #fff; border-color: var(--accent); }

        /* Repeater Architecture */
        .repeater-item { border: 1px solid var(--border); border-radius: 8px; margin-bottom: 1rem; overflow: hidden; }
        .repeater-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #f8fafc; cursor: pointer; user-select: none; }
        .repeater-title { font-weight: 700; margin: 0; }
        .repeater-actions { display: flex; gap: 0.5rem; }
        .btn-icon { background: #fff; border: 1px solid var(--border); border-radius: 4px; padding: 0.4rem 0.8rem; cursor: pointer; font-size: 0.8rem; font-weight: 600; color: #000; }
        .btn-icon:hover { background: #f1f5f9; }
        .btn-delete { color: #ef4444; }
        
        .repeater-body { display: none; padding: 1.5rem; border-top: 1px solid var(--border); }
        .repeater-item.expanded .repeater-body { display: block; }
        
        /* Split Grid for Live Preview */
        .editor-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
        .live-preview-box { border: 1px solid var(--border); border-radius: 4px; height: 100%; min-height: 400px; background: #fff; overflow-y: auto; }
        .live-preview-header { background: #f1f5f9; padding: 0.75rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #000; border-bottom: 1px solid var(--border); }
        .live-preview-content { padding: 1rem; }

        /* Data Table */
        .data-table-wrapper { overflow-x: auto; border: 1px solid var(--border); border-radius: 6px; }
        .data-table { width: 100%; border-collapse: collapse; text-align: left; }
        .data-table th, .data-table td { padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--border); }
        .data-table th { background: #f8fafc; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap; }
        .data-table td { font-size: 0.9rem; color: #1e293b; white-space: nowrap; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover { background: #f1f5f9; }

        .btn-primary { background: var(--accent); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 1rem; margin-top: 1rem; }
        .btn-secondary { background: #e2e8f0; color: #000; border: none; padding: 0.75rem 1.5rem; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
        
        .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; font-weight: 600; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .alert-success { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
    <div class="container">
        
        <?php if (!isset($_SESSION['logged_in'])): ?>
            <div style="max-width: 400px; margin: 4rem auto; background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h2>Authentication Required</h2>
                <?php if ($error) echo "<div class='alert alert-error'>$error</div>"; ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="btn-primary" style="width: 100%;">Login</button>
                </form>
            </div>
        <?php else: ?>

            <div class="header">
                <h2>System Configuration</h2>
                <a href="?logout=1" class="btn-logout">Disconnect Session</a>
            </div>

            <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>

            <div class="tabs">
                <button class="tab-btn <?php echo $active_tab === 'tab-form' ? 'active' : ''; ?>" onclick="switchTab(event, 'tab-form')">Form Settings</button>
                <button class="tab-btn <?php echo $active_tab === 'tab-emails' ? 'active' : ''; ?>" onclick="switchTab(event, 'tab-emails')">Email Builder</button>
                <button class="tab-btn <?php echo $active_tab === 'tab-integrations' ? 'active' : ''; ?>" onclick="switchTab(event, 'tab-integrations')">Integrations</button>
                <button class="tab-btn <?php echo $active_tab === 'tab-leads' ? 'active' : ''; ?>" onclick="switchTab(event, 'tab-leads')">Lead Data</button>
            </div>

            <form method="POST" id="mainForm">
                <input type="hidden" name="email_templates_json" id="emailTemplatesJson">

                <div id="tab-form" class="tab-content <?php echo $active_tab === 'tab-form' ? 'active' : ''; ?>">
                    
                    <div class="form-group">
                        <label>Form Success Message</label>
                        <input type="text" name="success_message" value="<?php echo htmlspecialchars(isset($config['success_message']) ? $config['success_message'] : 'Success! Your request has been processed.'); ?>">
                        <small>The message displayed on the frontend after a successful submission. All tokens (including <code>{download_link}</code>) are supported.</small>
                    </div>

                    <div class="form-group" style="background: #fff; padding: 1rem; border: 1px solid var(--border); border-radius: 4px;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 0.75rem;">
                            <input type="checkbox" name="enable_csv_logging" id="enableCsvToggle" value="1" <?php echo (isset($config['enable_csv_logging']) && $config['enable_csv_logging']) ? 'checked' : ''; ?> onchange="toggleCsvFields()">
                            <span style="font-size: 1rem;">Enable CSV Lead Logging</span>
                        </label>
                        
                        <div id="csvFieldsWrapper" style="<?php echo (isset($config['enable_csv_logging']) && $config['enable_csv_logging']) ? 'display: block;' : 'display: none;'; ?>">
                            <label style="margin-top: 1rem;">CSV File Name</label>
                            <input type="text" name="csv_file_name" value="<?php echo htmlspecialchars($active_csv_setting); ?>" placeholder="leads.csv">
                            <small>Automatically saved into the <code>assets/data/</code> directory.</small>
                        </div>
                    </div>

                    <hr style="margin: 2rem 0; border: 0; border-top: 1px solid var(--border);">

                    <div class="form-group" style="background: #fff; padding: 1rem; border: 1px solid var(--border); border-radius: 4px;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin: 0;">
                            <input type="checkbox" name="has_attachment" id="hasAttachmentToggle" value="1" <?php echo (!isset($config['has_attachment']) || $config['has_attachment']) ? 'checked' : ''; ?> onchange="toggleAttachmentFields()">
                            <span style="font-size: 1rem;">Enable File Attachment & Downloads</span>
                        </label>
                    </div>

                    <div id="attachmentFieldsWrapper" style="<?php echo (isset($config['has_attachment']) && !$config['has_attachment']) ? 'display: none;' : 'display: block;'; ?>">
                        <div class="form-group">
                            <label>Delivery Method</label>
                            <select name="download_method" id="downloadMethodSelect" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px; font-family: inherit;">
                                <option value="both" <?php echo (!isset($config['download_method']) || $config['download_method'] === 'both') ? 'selected' : ''; ?>>Both: Auto-Download in Browser + Email Tokens</option>
                                <option value="auto" <?php echo (isset($config['download_method']) && $config['download_method'] === 'auto') ? 'selected' : ''; ?>>Auto-Download Only</option>
                                <option value="email" <?php echo (isset($config['download_method']) && $config['download_method'] === 'email') ? 'selected' : ''; ?>>Email Tokens Only</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>File Download URL</label>
                            <select name="download_url" id="globalDownloadUrl" onchange="triggerGlobalPreviewRefresh()">
                                <option value="">-- Select an available file --</option>
                                <?php foreach($local_files as $file): ?>
                                    <option value="<?php echo htmlspecialchars($file); ?>" <?php echo (isset($config['download_url']) && $config['download_url'] === $file) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(basename($file)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small>Select a file from the <code>assets/downloads/</code> directory. This populates the <code>{download_link}</code> and <code>{download_button}</code> tokens.</small>
                        </div>

                        <div class="form-group">
                            <label>Email Button Text</label>
                            <input type="text" name="download_button_text" id="globalButtonText" value="<?php echo htmlspecialchars(isset($config['download_button_text']) ? $config['download_button_text'] : 'Download Payload Specs'); ?>" oninput="triggerGlobalPreviewRefresh()">
                            <small>The text displayed inside the <code>{download_button}</code> token.</small>
                        </div>
                    </div>
                    
                    <hr style="margin: 2rem 0; border: 0; border-top: 1px solid var(--border);">
                    
                    <div class="form-group" style="max-width: 400px;">
                        <label>Change Admin Password (leave blank to keep current)</label>
                        <input type="text" name="decoy_username" style="display:none;" aria-hidden="true" autocomplete="username" tabindex="-1">
                        <input type="password" name="new_password" autocomplete="new-password">
                    </div>
                    
                    <div style="margin-top: 2rem;">
                        <button type="submit" name="update_settings" class="btn-primary" onclick="serializeState()">Deploy Settings</button>
                    </div>
                </div>

                <div id="tab-emails" class="tab-content <?php echo $active_tab === 'tab-emails' ? 'active' : ''; ?>">
                    <div style="margin-bottom: 1.5rem; color: #000; font-size: 0.9rem;">
                        <strong>Dynamic Tokens (Click to copy):</strong>
                        <p style="margin: 0.5rem 0; opacity: 0.7; font-size: 0.85rem;">
                            These tokens are actively synced with the schema of your currently configured CSV file (<code><?php echo htmlspecialchars($active_csv_setting); ?></code>).
                        </p>
                        <div class="token-container">
                            <?php foreach ($all_tokens as $token): ?>
                                <span class="token-badge" onclick="copyToken(this)"><?php echo $token; ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div id="repeaterContainer"></div>
                    
                    <button type="button" class="btn-secondary" onclick="addTemplate()">+ Add Email Template</button>

                    <div style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                        <button type="submit" name="update_settings" class="btn-primary" onclick="serializeState()">Deploy Settings</button>
                    </div>
                </div>

                <div id="tab-integrations" class="tab-content <?php echo $active_tab === 'tab-integrations' ? 'active' : ''; ?>">
                    
                    <div class="form-group" style="background: #fff; padding: 1rem; border: 1px solid var(--border); border-radius: 4px;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 0.75rem;">
                            <input type="checkbox" name="recaptcha_enabled" id="enableRecaptchaToggle" value="1" <?php echo (isset($config['recaptcha_enabled']) && $config['recaptcha_enabled']) ? 'checked' : ''; ?> onchange="toggleRecaptchaFields()">
                            <span style="font-size: 1rem;">Enable Google reCAPTCHA v3 Protection</span>
                        </label>
                        
                        <div id="recaptchaFieldsWrapper" style="<?php echo (isset($config['recaptcha_enabled']) && $config['recaptcha_enabled']) ? 'display: block;' : 'display: none;'; ?>">
                            <div class="form-group" style="margin-top: 1rem;">
                                <label>Site Key</label>
                                <input type="text" name="recaptcha_site_key" value="<?php echo htmlspecialchars(isset($config['recaptcha_site_key']) ? $config['recaptcha_site_key'] : ''); ?>" placeholder="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI">
                            </div>
                            <div class="form-group">
                                <label>Secret Key</label>
                                <input type="password" name="recaptcha_secret_key" value="<?php echo htmlspecialchars(isset($config['recaptcha_secret_key']) ? $config['recaptcha_secret_key'] : ''); ?>" placeholder="6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe">
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 2rem;">
                        <button type="submit" name="update_settings" class="btn-primary" onclick="serializeState()">Deploy Settings</button>
                    </div>
                </div>
            </form>

            <div id="tab-leads" class="tab-content <?php echo $active_tab === 'tab-leads' ? 'active' : ''; ?>">
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 style="margin: 0 0 0.5rem 0;">Captured Leads</h3>
                        
                        <form method="GET" style="margin:0; display:flex; align-items:center; gap: 0.5rem;">
                            <label style="margin:0; font-size:0.85rem; color:#64748b;">Viewing:</label>
                            <?php if (!empty($available_csvs)): ?>
                                <select name="view_csv" onchange="this.form.submit()" style="width:auto; padding:0.4rem 0.75rem; font-size: 0.85rem;">
                                    <?php foreach($available_csvs as $csv): ?>
                                        <option value="<?php echo htmlspecialchars($csv); ?>" <?php echo $csv === $current_view_csv ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($csv); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <span style="font-size:0.85rem; color:#ef4444;">No CSV files found in assets/data/</span>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <div>
                        <?php if (file_exists($view_csv_path) && filesize($view_csv_path) > 0): ?>
                            <a href="assets/data/<?php echo htmlspecialchars($current_view_csv); ?>" download class="btn-secondary">⬇ Download CSV</a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php
                if (file_exists($view_csv_path) && filesize($view_csv_path) > 0 && ($handle = fopen($view_csv_path, "r")) !== FALSE) {
                    $rows = [];
                    $max_cols = 0;
                    
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        if (count($data) === 1 && $data[0] === null) continue;
                        $rows[] = $data;
                        if (count($data) > $max_cols) {
                            $max_cols = count($data);
                        }
                    }
                    fclose($handle);

                    if (!empty($rows)) {
                        echo '<div class="data-table-wrapper"><table class="data-table">';
                        foreach ($rows as $index => $row) {
                            echo '<tr>';
                            for ($i = 0; $i < $max_cols; $i++) {
                                $cell = isset($row[$i]) ? htmlspecialchars($row[$i]) : '';
                                if ($index === 0) {
                                    echo '<th>' . $cell . '</th>';
                                } else {
                                    echo '<td>' . $cell . '</td>';
                                }
                            }
                            echo '</tr>';
                        }
                        echo '</table></div>';
                    }
                } else {
                    echo '<div style="background: #f8fafc; border: 1px dashed var(--border); padding: 3rem; text-align: center; border-radius: 6px;">';
                    echo '<p style="color: #64748b; margin: 0; font-weight: 500;">No lead data found in this file.</p>';
                    echo '</div>';
                }
                ?>
            </div>

        <?php endif; ?>

    </div>

    <?php if (isset($_SESSION['logged_in'])): ?>
    <script>
        let appState = <?php echo json_encode($initial_templates); ?>;

        function generateId() {
            return 'tpl_' + Math.random().toString(36).substr(2, 9);
        }

        function copyToken(element) {
            const token = element.innerText;
            navigator.clipboard.writeText(token).then(() => {
                element.classList.add('copied');
                element.innerText = 'Copied!';
                setTimeout(() => {
                    element.classList.remove('copied');
                    element.innerText = token;
                }, 1000);
            });
        }

        function toggleCsvFields() {
            const toggle = document.getElementById('enableCsvToggle');
            const wrapper = document.getElementById('csvFieldsWrapper');
            if (toggle && wrapper) {
                wrapper.style.display = toggle.checked ? 'block' : 'none';
            }
        }

        function toggleAttachmentFields() {
            const toggle = document.getElementById('hasAttachmentToggle');
            const wrapper = document.getElementById('attachmentFieldsWrapper');
            if (toggle && wrapper) {
                wrapper.style.display = toggle.checked ? 'block' : 'none';
                triggerGlobalPreviewRefresh();
            }
        }

        function toggleRecaptchaFields() {
            const toggle = document.getElementById('enableRecaptchaToggle');
            const wrapper = document.getElementById('recaptchaFieldsWrapper');
            if (toggle && wrapper) {
                wrapper.style.display = toggle.checked ? 'block' : 'none';
            }
        }

        function switchTab(evt, tabId) {
            evt.preventDefault();
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            evt.currentTarget.classList.add('active');
            
            if (window.history.replaceState && window.location.search) {
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            }
        }

        function renderRepeater() {
            const container = document.getElementById('repeaterContainer');
            container.innerHTML = '';

            appState.forEach((tpl) => {
                const item = document.createElement('div');
                item.className = 'repeater-item';
                item.dataset.id = tpl.id;
                
                item.innerHTML = `
                    <div class="repeater-header" onclick="toggleCollapse(this)">
                        <h4 class="repeater-title">${tpl.name || 'Untitled Template'}</h4>
                        <div class="repeater-actions">
                            <button type="button" class="btn-icon" onclick="duplicateTemplate(event, '${tpl.id}')">Duplicate</button>
                            <button type="button" class="btn-icon btn-delete" onclick="deleteTemplate(event, '${tpl.id}')">Delete</button>
                        </div>
                    </div>
                    <div class="repeater-body">
                        <div class="editor-grid">
                            <div class="editor-fields">
                                <div class="form-group">
                                    <label>Template Reference Name (Internal)</label>
                                    <input type="text" value="${escapeHtml(tpl.name)}" oninput="updateState('${tpl.id}', 'name', this.value); updateHeader(this, this.value)">
                                </div>
                                <div class="form-group">
                                    <label>Recipient Email(s)</label>
                                    <input type="text" value="${escapeHtml(tpl.to)}" oninput="updateState('${tpl.id}', 'to', this.value)">
                                </div>
                                <div class="form-group">
                                    <label>Subject Line</label>
                                    <input type="text" value="${escapeHtml(tpl.subject)}" oninput="updateState('${tpl.id}', 'subject', this.value)">
                                </div>
                                <div class="form-group">
                                    <label>HTML Body</label>
                                    <textarea rows="14" oninput="updateState('${tpl.id}', 'body', this.value); renderPreview('${tpl.id}', this.value)">${escapeHtml(tpl.body)}</textarea>
                                </div>
                            </div>
                            <div class="editor-preview">
                                <div class="live-preview-box">
                                    <div class="live-preview-header">Live HTML Render</div>
                                    <div class="live-preview-content" id="preview_${tpl.id}">
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(item);
                renderPreview(tpl.id, tpl.body);
            });
        }

        function updateState(id, key, value) {
            const index = appState.findIndex(t => t.id === id);
            if (index > -1) {
                appState[index][key] = value;
            }
        }

        function updateHeader(input, value) {
            const header = input.closest('.repeater-item').querySelector('.repeater-title');
            header.textContent = value || 'Untitled Template';
        }

        function addTemplate() {
            appState.push({
                id: generateId(),
                name: 'New Template',
                to: '',
                subject: '',
                body: ''
            });
            renderRepeater();
            const items = document.querySelectorAll('.repeater-item');
            if(items.length > 0) items[items.length - 1].classList.add('expanded');
        }

        function duplicateTemplate(evt, id) {
            evt.stopPropagation();
            const index = appState.findIndex(t => t.id === id);
            if (index > -1) {
                const clone = JSON.parse(JSON.stringify(appState[index]));
                clone.id = generateId();
                clone.name = clone.name + ' (Copy)';
                appState.splice(index + 1, 0, clone);
                renderRepeater();
            }
        }

        function deleteTemplate(evt, id) {
            evt.stopPropagation();
            if(confirm('Are you sure you want to delete this template?')) {
                appState = appState.filter(t => t.id !== id);
                renderRepeater();
            }
        }

        function toggleCollapse(headerEl) {
            const item = headerEl.closest('.repeater-item');
            item.classList.toggle('expanded');
        }

        function triggerGlobalPreviewRefresh() {
            appState.forEach(tpl => renderPreview(tpl.id, tpl.body));
        }

        function renderPreview(id, htmlString) {
            const previewFrame = document.getElementById('preview_' + id);
            if (!previewFrame) return;

            const isAttachmentEnabled = document.getElementById('hasAttachmentToggle').checked;
            const currentUrl = document.getElementById('globalDownloadUrl').value || '#';
            const btnInput = document.getElementById('globalButtonText');
            const currentBtnText = (btnInput && btnInput.value.trim() !== '') ? btnInput.value : 'Download Payload Specs';
            
            const mockButton = (isAttachmentEnabled && currentUrl !== '#') 
                ? `<a href="${currentUrl}" style="display: inline-block; background-color: #2DA1FF; color: #0a0a0a; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; font-family: 'Inter', -apple-system, BlinkMacSystemFont, Arial, sans-serif; font-size: 14px;">${escapeHtml(currentBtnText)}</a>` 
                : '';

            let processedHtml = htmlString
                .replace(/{full_name}/g, 'James Morton')
                .replace(/{work_email}/g, 'james@police.uk')
                .replace(/{org_type}/g, 'Police Aviation Unit')
                .replace(/{phone}/g, '+44 7700 900077')
                .replace(/{download_link}/g, isAttachmentEnabled ? currentUrl : '')
                .replace(/{download_button}/g, mockButton);

            previewFrame.innerHTML = processedHtml;
        }

        function serializeState() {
            document.getElementById('emailTemplatesJson').value = JSON.stringify(appState);
        }

        function escapeHtml(unsafe) {
            return (unsafe || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderRepeater();
            const first = document.querySelector('.repeater-item');
            if (first) first.classList.add('expanded');
        });
    </script>
    <?php endif; ?>
</body>
</html>