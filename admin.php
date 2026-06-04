<?php
session_start();
if( !isset($_SESSION['username']) ) {
	header("Location: login.php");
	exit();
} else {
    
}
$logged_in_user = 'System Administrator';
if (isset($_SESSION['username'])) {
    $logged_in_user = $_SESSION['username'];
} elseif (isset($_SESSION['user'])) {
    $logged_in_user = $_SESSION['user'];
}

$needs_reset = false;
if (isset($_SESSION['dashboard_logs']) && is_array($_SESSION['dashboard_logs'])) {
    foreach ($_SESSION['dashboard_logs'] as $log) {
        if (!is_array($log) || !isset($log['id'])) {
            $needs_reset = true;
            break;
        }
    }
} else {
    $needs_reset = true;
}

if ($needs_reset) {
    $_SESSION['dashboard_logs'] = [
        [
            'id' => 1, 
            'section' => 'CHARACTER', 
            'title' => 'No-Face (Kaonashi)', 
            'desc' => 'A lonely spirit who mirrors emotions.', 
            'photo' => 'noface.png',
            'created_by' => 'System Engine',
            'created_at' => '2026-06-03 14:22'
        ],
        [
            'id' => 2, 
            'section' => 'GALLERY', 
            'title' => 'The Bathhouse', 
            'desc' => 'Exterior shot of Aburaya glowing under the spirit moon.', 
            'photo' => 'bathhouse.jpg',
            'created_by' => 'System Engine',
            'created_at' => '2026-06-03 15:05'
        ]
    ];
}

$message = "";
$edit_item = null;

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $delete_id = (int)$_GET['id'];
    foreach ($_SESSION['dashboard_logs'] as $key => $log) {
        if (isset($log['id']) && (int)$log['id'] === $delete_id) {
            $deleted_title = htmlspecialchars($log['title'] ?? 'Record');
            unset($_SESSION['dashboard_logs'][$key]);
            $_SESSION['dashboard_logs'] = array_values($_SESSION['dashboard_logs']);
            $message = "Record <strong>'$deleted_title'</strong> successfully removed by <strong>" . htmlspecialchars($logged_in_user) . "</strong>!";
            break;
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $edit_id = (int)$_GET['id'];
    foreach ($_SESSION['dashboard_logs'] as $log) {
        if (isset($log['id']) && (int)$log['id'] === $edit_id) {
            $edit_item = $log;
            break;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'save_content') {
        $section = $_POST['target_section'] ?? '';
        $title = $_POST['item_title'] ?? '';
        $description = $_POST['item_desc'] ?? '';
        $is_edit = !empty($_POST['edit_id']);
        
        $photoName = $is_edit ? $_POST['existing_photo'] : 'placeholder.jpg';

        if (isset($_FILES['item_photo']) && $_FILES['item_photo']['error'] == 0) {
            $photoName = basename($_FILES['item_photo']['name']);
        }

        if ($is_edit) {
            $target_id = (int)$_POST['edit_id'];
            $updated = false;

            foreach ($_SESSION['dashboard_logs'] as &$log) {
                if (isset($log['id']) && (int)$log['id'] === $target_id) {
                    $log['section'] = $section;
                    $log['title'] = $title;
                    $log['desc'] = $description;
                    $log['photo'] = $photoName;
                    $log['created_by'] = $logged_in_user;
                    $log['created_at'] = date('Y-m-d H:i');
                    $updated = true;
                    break;
                }
            }
            unset($log);

            if ($updated) {
                $message = "Record <strong>'$title'</strong> successfully updated by <strong>" . htmlspecialchars($logged_in_user) . "</strong>!";
            } else {
                $message = "Error: Could not locate record ID $target_id to update.";
            }
        } else {
            $_SESSION['dashboard_logs'][] = [
                'id' => time(),
                'section' => $section,
                'title' => $title,
                'desc' => $description,
                'photo' => $photoName,
                'created_by' => $logged_in_user,
                'created_at' => date('Y-m-d H:i')
            ];
            $message = "Successfully published new record for <strong>$section</strong> via <strong>" . htmlspecialchars($logged_in_user) . "</strong>!";
        }
    }
}

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$display_logs = $_SESSION['dashboard_logs'];

if ($search_query !== '') {
    $display_logs = array_filter($_SESSION['dashboard_logs'], function($log) use ($search_query) {
        if (!isset($log['title']) || !isset($log['desc'])) return false;
        
        $title_match = stripos($log['title'], $search_query) !== false;
        $desc_match = stripos($log['desc'], $search_query) !== false;
        $section_match = stripos($log['section'], $search_query) !== false;
        
        return $title_match || $desc_match || $section_match;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="icon" type="image/png" href="img/tabicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond&family=Lustria&family=Vollkorn&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="index.css">

  <style>
    .admin-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        color: #fff;
        font-family: 'Vollkorn', serif;
    }

    .admin-title {
        font-family: 'Cormorant Garamond', serif;
        font-size: 32px;
        letter-spacing: 2px;
        color: #e2b13c;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .admin-subtitle {
        font-size: 12px;
        color: #888;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 25px;
        display: block;
    }

    .admin-nav {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .nav-links-wrapper {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .admin-nav a {
        color: #ccc;
        text-decoration: none;
        font-size: 13px;
        letter-spacing: 1px;
    }

    .admin-nav a.active {
        color: #e2b13c;
    }

    .nav-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .logged-user-badge {
        font-size: 11px;
        background: rgba(226, 177, 60, 0.12);
        border: 1px solid rgba(226, 177, 60, 0.3);
        padding: 4px 12px;
        border-radius: 20px;
        color: #e2b13c;
        letter-spacing: 0.5px;
    }

    .btn-logout {
        font-size: 11px;
        padding: 4px 14px;
        border-radius: 20px;
        border: 1px solid rgba(255, 82, 82, 0.4);
        background: rgba(255, 82, 82, 0.1);
        color: #ff5252;
        text-decoration: none;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .btn-logout:hover {
        background: #ff5252;
        color: #fff;
        border-color: #ff5252;
    }

    .search-wrapper {
        margin-bottom: 30px;
    }

    .search-form {
        display: flex;
        gap: 10px;
        width: 100%;
    }

    .search-input {
        flex: 1;
        padding: 12px 15px;
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 4px;
        color: #fff;
        font-family: 'Lustria', sans-serif;
        font-size: 14px;
        letter-spacing: 0.5px;
        transition: border 0.3s ease;
    }

    .search-input:focus {
        border-color: #e2b13c;
        outline: none;
    }

    .btn-search {
        background: transparent;
        color: #e2b13c;
        border: 1px solid #e2b13c;
        padding: 0 25px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-search:hover {
        background: #e2b13c;
        color: #111;
    }

    .btn-clear-search {
        background: rgba(255, 255, 255, 0.05);
        color: #aaa;
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 0 15px;
        font-size: 12px;
        text-transform: uppercase;
        border-radius: 4px;
        display: flex;
        align-items: center;
        text-decoration: none;
        letter-spacing: 0.5px;
    }

    .btn-clear-search:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.1);
    }

    .admin-grid {
        display: flex;
        gap: 30px;
    }

    .quick-actions-box {
        flex: 1;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 25px;
        border-radius: 6px;
        height: fit-content;
    }

    .logs-box {
        flex: 1.6;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 25px;
        border-radius: 6px;
    }

    .section-heading {
        font-family: 'Cormorant Garamond', serif;
        font-size: 18px;
        letter-spacing: 1.5px;
        margin-bottom: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 8px;
        color: #fff;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        color: #aaa;
        margin-bottom: 6px;
    }

    .form-group select,
    .form-group input[type="text"],
    .form-group textarea,
    .form-group input[type="file"] {
        width: 100%;
        padding: 12px;
        background: #111;
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #fff;
        border-radius: 4px;
        font-family: 'Lustria', sans-serif;
        font-size: 14px;
    }

    .form-group textarea {
        resize: none;
        height: 100px;
    }

    .btn-submit {
        background: #e2b13c;
        color: #111;
        border: none;
        padding: 14px;
        font-weight: bold;
        letter-spacing: 2px;
        text-transform: uppercase;
        width: 100%;
        cursor: pointer;
        border-radius: 4px;
    }

    .cancel-link {
        display: block;
        text-align: center;
        margin-top: 10px;
        color: #aaa;
        font-size: 12px;
        text-decoration: none;
    }

    .status-alert {
        background: rgba(40, 167, 69, 0.15);
        border: 1px solid #28a745;
        color: #2bed65;
        padding: 12px;
        font-size: 13px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .log-item {
        background: rgba(0, 0, 0, 0.2);
        border-left: 3px solid #e2b13c;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 4px;
        position: relative;
    }

    .log-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }

    .log-tag {
        background: #8b1e0f;
        color: #fff;
        font-size: 10px;
        padding: 2px 6px;
        font-weight: bold;
        border-radius: 2px;
        display: inline-block;
    }

    .log-meta-attribution {
        font-size: 11px;
        color: #888;
        font-family: 'Lustria', sans-serif;
        text-align: right;
        line-height: 1.4;
    }

    .log-meta-attribution .user-name {
        color: #e2b13c;
        font-weight: bold;
    }

    .log-meta-attribution .timestamp {
        font-size: 10px;
        opacity: 0.7;
    }

    .log-item h4 {
        margin-bottom: 5px;
        font-size: 16px;
    }

    .log-item p {
        font-size: 13px;
        color: #ccc;
        margin-bottom: 10px;
    }

    .log-file {
        font-size: 11px;
        color: #666;
    }

    .action-controls {
        display: flex;
        gap: 15px;
        margin-top: 10px;
        border-top: 1px solid rgba(255,255,255,0.05);
        padding-top: 10px;
    }

    .action-controls a {
        font-size: 12px;
        text-decoration: none;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .btn-edit {
        color: #e2b13c;
    }

    .btn-delete {
        color: #ff5252;
    }

    @media (max-width: 850px) {
        .admin-grid { flex-direction: column; }
        .log-item-header { flex-direction: column; gap: 5px; }
        .log-meta-attribution { text-align: left; }
        .search-form { flex-direction: column; }
        .btn-search, .btn-clear-search { padding: 12px; justify-content: center; }
        .nav-right { width: 100%; justify-content: flex-start; }
    }
  </style>
</head>
<body>

<div id="transitionOverlay" class="smooth-fade"></div>

<header class="header">
  <div class="logo-container">
    <a href="index.php">
      <img src="img/logo-removebg-preview.png" alt="Logo" class="main-logo">
    </a>
  </div>
  <div class="header-right">
    <button class="menu-btn" id="menuBtn">☰</button>
  </div>
</header>

<div class="admin-container">
    <h2 class="admin-title">ADMIN DASHBOARD</h2>
    <span class="admin-subtitle">Record Management System</span>

    <div class="admin-nav">
        <div class="nav-links-wrapper">
            <a href="admin.php" class="<?php echo ($search_query === '') ? 'active' : ''; ?>">ALL SECTIONS</a>
            <a href="about.php">ABOUT</a>
            <a href="gallery.php">GALLERY</a>
            <a href="folklore_origin.php">FOLKLORE ORIGIN</a>
            <a href="characters.php">CHARACTER</a>
            <a href="guidebook.php">SPIRIT WORLD GUIDEBOOK</a>
        </div>

        <!-- ✅ Logout button added here -->
        <div class="nav-right">
            <div class="logged-user-badge">
                Logged in As: <strong><?php echo htmlspecialchars($logged_in_user); ?></strong>
            </div>
            <a href="logout.php" class="btn-logout"
               onclick="return confirm('Are you sure you want to logout?');">
                ⏻ Logout
            </a>
        </div>
    </div>

    <div class="search-wrapper">
        <form action="admin.php" method="GET" class="search-form">
            <input type="text" name="search" class="search-input"
                   placeholder="Search entries by title, section, or description content..."
                   value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="btn-search">Search</button>
            <?php if ($search_query !== ''): ?>
                <a href="admin.php" class="btn-clear-search">✕ Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="admin-grid">
        <section class="quick-actions-box">
            <h3 class="section-heading">
                <?php echo $edit_item ? "EDIT SYSTEM RECORD" : "QUICK ACTIONS"; ?>
            </h3>

            <form action="admin.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_content">
                <input type="hidden" name="edit_id" value="<?php echo $edit_item && isset($edit_item['id']) ? $edit_item['id'] : ''; ?>">
                <input type="hidden" name="existing_photo" value="<?php echo $edit_item && isset($edit_item['photo']) ? $edit_item['photo'] : ''; ?>">

                <div class="form-group">
                    <label for="target_section">Select: </label>
                    <select id="target_section" name="target_section" required>
                        <?php
                        $sections = ["ABOUT", "GALLERY", "FOLKLORE ORIGIN", "CHARACTER", "SPIRIT WORLD GUIDEBOOK"];
                        foreach ($sections as $sec) {
                            $selected = ($edit_item && isset($edit_item['section']) && $edit_item['section'] == $sec) ? "selected" : "";
                            echo "<option value='$sec' $selected>$sec</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="item_title">Title</label>
                    <input type="text" id="item_title" name="item_title"
                           value="<?php echo $edit_item && isset($edit_item['title']) ? htmlspecialchars($edit_item['title']) : ''; ?>"
                           placeholder="Card object name..." required>
                </div>

                <div class="form-group">
                    <label for="item_photo">
                        Image File <?php echo $edit_item ? "(Leave blank to keep current)" : ""; ?>
                    </label>
                    <input type="file" id="item_photo" name="item_photo" accept="image/*" <?php echo $edit_item ? "" : "required"; ?>>
                </div>

                <div class="form-group">
                    <label for="item_desc">Description</label>
                    <textarea id="item_desc" name="item_desc" placeholder="Write description content metadata..." required><?php echo $edit_item && isset($edit_item['desc']) ? htmlspecialchars($edit_item['desc']) : ''; ?></textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <?php echo $edit_item ? "Save Modification" : "Publish Item"; ?>
                </button>

                <?php if ($edit_item): ?>
                    <a href="admin.php" class="cancel-link">Cancel Edit Mode</a>
                <?php endif; ?>
            </form>
        </section>

        <section class="logs-box">
            <h3 class="section-heading">
                <?php echo ($search_query !== '') ? "SEARCH RESULTS FOR \"" . htmlspecialchars($search_query) . "\"" : "LOGS / DATA ENTRIES"; ?>
            </h3>

            <?php if (!empty($message)): ?>
                <div class="status-alert"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="log-stream">
                <?php if (empty($display_logs)): ?>
                    <p style="color: #666; font-style: italic;">No records match your system search query parameters.</p>
                <?php else: ?>
                    <?php foreach ($display_logs as $log): ?>
                        <?php if (isset($log['id'])): ?>
                        <div class="log-item">
                            <div class="log-item-header">
                                <span class="log-tag"><?php echo htmlspecialchars($log['section'] ?? 'GENERAL'); ?></span>

                                <div class="log-meta-attribution">
                                    By: <span class="user-name"><?php echo htmlspecialchars($log['created_by'] ?? 'Unknown'); ?></span><br>
                                    <span class="timestamp">📅 <?php echo htmlspecialchars($log['created_at'] ?? 'N/A'); ?></span>
                                </div>
                            </div>

                            <h4><?php echo htmlspecialchars($log['title'] ?? 'Untitled'); ?></h4>
                            <p><?php echo htmlspecialchars($log['desc'] ?? ''); ?></p>
                            <div class="log-file">📁 File Asset: <?php echo htmlspecialchars($log['photo'] ?? 'placeholder.jpg'); ?></div>

                            <div class="action-controls">
                                <a href="admin.php?action=edit&id=<?php echo $log['id']; ?>" class="btn-edit">Edit Data</a>
                                <a href="admin.php?action=delete&id=<?php echo $log['id']; ?>"
                                   class="btn-delete"
                                   onclick="return confirm('Are you sure you want to remove this data node permanently?');">
                                    Delete
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<div class="side-menu" id="sideMenu">
  <div class="close-btn" id="closeBtn">×</div>
  <nav class="menu-links">
    <a href="about.php">ABOUT</a>
    <a href="gallery.php">GALLERY</a>
    <a href="folklore_origin.php">FOLKLORE ORIGIN</a>
    <a href="characters.php">CHARACTER</a>
    <a href="guidebook.php">SPIRIT WORLD GUIDEBOOK</a>
  </nav>
</div>

<div class="overlay" id="overlay"></div>

<footer class="footer">
    <p>"Once you've met someone, you never really forget them."</p>
    <p style="font-size: 10px; margin-top: 10px; opacity: 0.5;">© Spirited Away. All Rights Reserved.</p>
</footer>

<script>
    const menuBtn = document.getElementById("menuBtn");
    const sideMenu = document.getElementById("sideMenu");
    const closeBtn = document.getElementById("closeBtn");
    const overlay = document.getElementById("overlay");

    menuBtn.onclick = () => {
      sideMenu.classList.add("open");
      overlay.classList.add("show");
    };

    closeBtn.onclick = closeMenu;
    overlay.onclick = closeMenu;

    function closeMenu() {
      sideMenu.classList.remove("open");
      overlay.classList.remove("show");
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.body.classList.add('page-loaded');
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');

            if (!link || !link.href || link.target === '_blank' ||
                link.href.includes('#') || link.href.includes('?action=') ||
                link.href.includes('?search=') ||
                !link.href.startsWith(window.location.origin)) {
                return;
            }
            e.preventDefault();
            const targetUrl = link.href;
            document.body.classList.remove('page-loaded');
            document.body.classList.add('page-exiting');
            setTimeout(() => {
                window.location.href = targetUrl;
            }, 800);
        });
    });
</script>

<script src="script.js"></script>
</body>
</html>