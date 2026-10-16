<?php
// Start tracking session parameters securely
if (session_status() === PHP_SESSION_NONE) {
    // Force alternative session settings to bypass local configuration blocks
    ini_set('session.use_cookies', 1);
    ini_set('session.use_only_cookies', 0);
    ini_set('session.use_trans_sid', 1);
    session_start();
}

// \u2060\ud83d\udd12 SET YOUR ADMIN PASSWORD HERE (Change 'Atelier2026!' to whatever private password you prefer)
define('ADMIN_PASSWORD', 'Atelier2026!');

// Process log out request
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['me_authenticated']);
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Process login form submission parameters
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_password'])) {
    if ($_POST['admin_password'] === ADMIN_PASSWORD) {
        $_SESSION['me_authenticated'] = true;
        header('Location: admin.php?auth=' . session_id()); // Fallback URL parameter validation tracking
        exit;
    } else {
        $login_error = "The access key entered is invalid.";
    }
}

// Intercept fallback request parameters if cookies are locked out on local storage setups
if (isset($_GET['auth']) && $_GET['auth'] === session_id()) {
    $_SESSION['me_authenticated'] = true;
}

// \u2060\ud83d\udea7 IF NOT LOGGED IN: Stop execution and show the clean luxury gate screen instead
if (!isset($_SESSION['me_authenticated']) || $_SESSION['me_authenticated'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Maison Éclat - Gatekeeper</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Helvetica Neue', Arial, sans-serif;
            }
            body {
                background-color: #fbf9f9;
                color: #2b1b17;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                padding: 20px;
            }
            .gate-card {
                background: #ffffff;
                border: 1px solid #ebdada;
                border-radius: 4px;
                max-width: 400px;
                width: 100%;
                padding: 40px 30px;
                text-align: center;
                box-shadow: 0 10px 30px rgba(74, 44, 58, 0.03);
            }
            .logo {
                font-size: 22px;
                letter-spacing: 4px;
                text-transform: uppercase;
                font-weight: 700;
                color: #4a2c3a;
                margin-bottom: 8px;
            }
            .subtitle {
                font-size: 11px;
                text-transform: uppercase;
                color: #a38b97;
                letter-spacing: 2px;
                margin-bottom: 30px;
            }
            .input-group {
                margin-bottom: 20px;
                text-align: left;
            }
            label {
                display: block;
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #4a2c3a;
                margin-bottom: 8px;
                font-weight: 600;
            }
            input[type="password"] {
                width: 100%;
                padding: 12px 16px;
                border: 1px solid #ebdada;
                border-radius: 2px;
                font-size: 14px;
                color: #2b1b17;
                outline: none;
                transition: border-color 0.2s;
            }
            input[type="password"]:focus {
                border-color: #4a2c3a;
            }
            .error-banner {
                font-size: 12px;
                color: #6b1d2f;
                margin-bottom: 20px;
                font-weight: 500;
            }
            .gate-btn {
                background-color: #4a2c3a;
                color: #ffffff;
                border: none;
                width: 100%;
                padding: 14px;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 2px;
                font-weight: bold;
                cursor: pointer;
                border-radius: 2px;
                transition: background-color 0.2s;
            }
            .gate-btn:hover {
                background-color: #6b1d2f;
            }
        </style>
    </head>
    <body>
        <div class="gate-card">
            <div class="logo">Maison Éclat</div>
            <div class="subtitle">Restricted Operational Vault</div>
            
            <?php if (isset($login_error)): ?>
                <div class="error-banner"><?php echo $login_error; ?></div>
            <?php endif; ?>

            <form action="admin.php" method="POST">
                <div class="input-group">
                    <label for="admin_password">Enter Access Key</label>
                    <input type="password" name="admin_password" id="admin_password" placeholder="password" required autofocus>
                </div>
                <button type="submit" class="gate-btn">Unlock Console</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit; // Halt rendering of data metrics until access parameters match perfectly
}

// Data parser logic block handles background files
$sub_file = 'subscribers.txt';
$msg_file = 'messages.txt';

$subscribers = [];
$messages = [];

$total_subs = 0;
$total_msgs = 0;

// Parse Subscribers List
if (file_exists($sub_file)) {
    $sub_lines = array_reverse(file($sub_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    $total_subs = count($sub_lines);
    foreach ($sub_lines as $line) {
        $parts = explode(' - ', $line, 2);
        if (count($parts) === 2) {
            $subscribers[] = ['date' => trim($parts[0]), 'email' => trim($parts[1])];
        }
    }
}

// Parse Contact Messages List
if (file_exists($msg_file)) {
    $msg_lines = array_reverse(file($msg_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    $total_msgs = count($msg_lines);
    foreach ($msg_lines as $line) {
        $parts = explode(' | ', $line, 4);
        if (count($parts) === 4) {
            $messages[] = [
                'date' => trim($parts[0]),
                'name' => trim($parts[1]),
                'email' => trim($parts[2]),
                'message' => trim($parts[3])
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maison Éclat - Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }
        body {
            background-color: #fbf9f9;
            color: #2b1b17;
            line-height: 1.6;
            padding: 40px 20px;
        }
        .dashboard-container {
            max-width: 1100px;
            margin: 0 auto;
        }
        header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 1px solid #ebdada;
            padding-bottom: 24px;
        }
        .logo {
            font-size: 24px;
            letter-spacing: 4px;
            text-transform: uppercase;
            font-weight: 700;
            color: #4a2c3a;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 8px;
        }
        .panel-tag {
            font-size: 12px;
            text-transform: uppercase;
            color: #a38b97;
            letter-spacing: 2px;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 50px;
        }
        .metric-card {
            background-color: #ffffff;
            border: 1px solid #ebdada;
            border-radius: 4px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(74, 44, 58, 0.02);
        }
        .metric-value {
            font-size: 36px;
            font-weight: bold;
            color: #6b1d2f;
            margin-bottom: 4px;
        }
        .metric-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #a38b97;
        }
        .section-layout {
            margin-bottom: 50px;
        }
        .table-section-title {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #4a2c3a;
            margin-bottom: 16px;
            font-weight: 600;
        }
        .table-wrapper {
            background-color: #ffffff;
            border: 1px solid #ebdada;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(74, 44, 58, 0.02);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th {
            background-color: #4a2c3a;
            color: #ffffff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 16px 20px;
            font-weight: 500;
        }
        td {
            padding: 16px 20px;
            font-size: 14px;
            border-bottom: 1px solid #f5ecee;
            color: #2b1b17;
            vertical-align: top;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover td {
            background-color: #fbf9f9;
        }
        .msg-text {
            color: #555555;
            font-style: italic;
            display: block;
            margin-top: 6px;
            background: #fdfcfc;
            padding: 10px;
            border-left: 2px solid #6b1d2f;
        }
        .empty-state {
            padding: 40px;
            text-align: center;
            color: #a38b97;
            font-size: 14px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <header>
            <div class="logo">Maison Éclat</div>
            <div class="panel-tag">Atelier Operations Dashboard</div>
            <div style="margin-top: 15px;">
                <a href="admin.php?action=logout" style="font-size: 11px; text-transform: uppercase; color: #6b1d2f; letter-spacing: 1px; text-decoration: none; font-weight: bold; border: 1px solid #ebdada; padding: 4px 12px; border-radius: 2px; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#6b1d2f'; this.style.color='#ffffff';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#6b1d2f';">Secure Exit</a>
            </div>
        </header>

        <section class="metrics-grid">
            <div class="metric-card">
                <div class="metric-value"><?php echo $total_subs; ?></div>
                <div class="metric-label">Newsletter Subscribers</div>
            </div>
            <div class="metric-card">
                <div class="metric-value"><?php echo $total_msgs; ?></div>
                <div class="metric-label">Customer Messages</div>
            </div>
        </section>

        <section class="section-layout">
            <h2 class="table-section-title">Received Customer Enquiries</h2>
            <div class="table-wrapper">
                <?php if ($total_msgs > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 25%;">Timestamp & Sender</th>
                                <th style="width: 75%;">Message Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                                <tr>
                                    <td>
                                        <div style="font-size: 11px; color: #a38b97; font-family: monospace;"><?php echo htmlspecialchars($msg['date']); ?></div>
                                        <div style="font-weight: bold; color: #4a2c3a; margin-top: 4px;"><?php echo htmlspecialchars($msg['name']); ?></div>
                                        <div style="font-size: 12px; color: #7d5a6b;"><?php echo htmlspecialchars($msg['email']); ?></div>
                                    </td>
                                    <td>
                                        <span class="msg-text">"<?php echo htmlspecialchars($msg['message']); ?>"</span>
                                        
                                        <!-- Interactive Response Text Field Module Inside Row -->
                                        <div style="margin-top: 15px; border-top: 1px dashed #ebdada; padding-top: 15px;">
                                            <form action="reply.php" method="POST" class="reply-form" style="display: flex; flex-direction: column; gap: 8px;">
                                                <input type="hidden" name="customer_email" value="<?php echo htmlspecialchars($msg['email']); ?>">
                                                <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($msg['name']); ?>">
                                                <textarea name="reply_message" placeholder="Type your elegant response here..." style="width: 100%; height: 70px; padding: 8px; border: 1px solid #ebdada; font-size: 13px; font-family: sans-serif; resize: none; border-radius: 2px;" required></textarea>
                                                <button type="submit" style="align-self: flex-start; padding: 6px 16px; background-color: #4a2c3a; color: #ffffff; border: none; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; font-weight: bold; border-radius: 2px; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#6b1d2f'" onmouseout="this.style.backgroundColor='#4a2c3a'">Send Live Reply</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">No inquiries received yet.</div>
                <?php endif; ?>
            </div>
        </section>

        <section class="section-layout">
            <h2 class="table-section-title">Newsletter Registry</h2>
            <div class="table-wrapper">
                <?php if ($total_subs > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 35%;">Registration Date</th>
                                <th style="width: 65%;">Email Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subscribers as $sub): ?>
                                <tr>
                                    <td style="color: #a38b97; font-family: monospace; font-size: 13px;"><?php echo htmlspecialchars($sub['date']); ?></td>
                                    <td style="font-weight: 500; color: #4a2c3a;"><?php echo htmlspecialchars($sub['email']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">No newsletter directory records found.</div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <script>
    document.querySelectorAll('.reply-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button');
            submitBtn.innerText = 'TRANSMITTING...';
            submitBtn.disabled = true;

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if(data.status === 'success') {
                    form.querySelector('textarea').value = '';
                }
            })
            .catch(err => alert('Network processing failure.'))
            .finally(() => {
                submitBtn.innerText = 'Send Live Reply';
                submitBtn.disabled = false;
            });
        });
    });
    </script>
</body>
</html>