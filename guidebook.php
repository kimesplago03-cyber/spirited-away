<?php
session_start();
if( !isset($_SESSION['username']) ) {
	header("Location: login.php");
	exit();
} else {
    
}

if (!isset($_SESSION['guidebook_chapters'])) {
    $_SESSION['guidebook_chapters'] = [
        [
            "chapter" => "CHAPTER I",
            "title"   => "Remember your Name",
            "desc"    => "Names hold power. Losing your name means losing your identity and freedom.",
            "symbol"  => "✦"
        ],
        [
            "chapter" => "CHAPTER II",
            "title"   => "Obtain Work",
            "desc"    => "Those who do not work have no right to exist. You must find Yubaba, the bathhouse owner, and demand a job; once a contract is signed, she is bound by law to employ you.",
            "symbol"  => "◈"
        ],
        [
            "chapter" => "CHAPTER III",
            "title"   => "Breath Rule",
            "desc"    => "Humans must hold their breath while crossing the bridge into the bathhouse. A human’s breath has a \"worldly smell\" that allows spirits and bouncers to immediately identify them as trespassers.",
            "symbol"  => "✦"
        ],
        [
            "chapter" => "CHAPTER IV",
            "title"   => "Bathhouse Hierarchy",
            "desc"    => "The bathhouse serves as a place for spirits to wash away the \"impurities\" and pollution they pick up in the human world.",
            "symbol"  => "◈"
        ],
        [
            "chapter" => "CHAPTER V",
            "title"   => "Reclaim Your True Name",
            "desc"    => "You cannot leave if the spirit world owns your name. Remembering who you are is the only way to break your contract.",
            "symbol"  => "✦"
        ],
        [
            "chapter" => "CHAPTER VI",
            "title"   => "Final Trial",
            "desc"    => "You must prove your growth by passing a test of perception—usually seeing through a master's illusion.",
            "symbol"  => "◈"
        ],
        [
            "chapter" => "CHAPTER VII",
            "title"   => "Break the Contract",
            "desc"    => "Once your name is returned and the trial is passed, your binding ties to the spirit realm are severed.",
            "symbol"  => "✦"
        ],
        [
            "chapter" => "CHAPTER VIII",
            "title"   => "Do Not Look Back",
            "desc"    => "As you cross the tunnel or bridge back to reality, you must never look back. To look back is to risk being trapped forever or losing your memories of the journey.",
            "symbol"  => "◈"
        ]
    ];
}

$chapters = [];
if (isset($_SESSION['guidebook_chapters']) && is_array($_SESSION['guidebook_chapters'])) {
    foreach ($_SESSION['guidebook_chapters'] as $item) {
        $chapters[] = [
            "chapter" => $item['chapter'] ?? 'CHAPTER UNKNOWN',
            "title"   => $item['title'] ?? 'Untitled Manuscript',
            "desc"    => $item['desc'] ?? $item['description'] ?? '',
            "symbol"  => $item['symbol'] ?? '✦'
        ];
    }
}

$raw_logs = [];
if (isset($_SESSION['dashboard_logs']) && is_array($_SESSION['dashboard_logs'])) {
    $raw_logs = $_SESSION['dashboard_logs'];
}

$db_file = 'database.json';
if (file_exists($db_file)) {
    $file_content = file_get_contents($db_file);
    $all_entries = json_decode($file_content, true);
    if (is_array($all_entries)) {
        $raw_logs = array_merge($raw_logs, $all_entries);
    }
}

$roman_numerals = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII", "XIII", "XIV", "XV", "XVI", "XVII", "XVIII", "XIX", "XX"];
$running_index = count($chapters) + 1;
$seen_hashes = [];

foreach ($raw_logs as $log) {
    if (isset($log['section'])) {
        $clean_section = strtoupper(trim($log['section']));
        if (strpos($clean_section, 'GUIDE') !== false) {
            
            $log_hash = md5(($log['title'] ?? $log['name'] ?? '') . ($log['desc'] ?? $log['description'] ?? $log['text'] ?? ''));
            if (in_array($log_hash, $seen_hashes)) continue;
            $seen_hashes[] = $log_hash;

            $assigned_symbol = ($running_index % 2 !== 0) ? '✦' : '◈';
            $assigned_numeral = isset($roman_numerals[$running_index - 1]) ? $roman_numerals[$running_index - 1] : $running_index;

            $chapters[] = [
                "chapter" => "CHAPTER " . $assigned_numeral,
                "title"   => $log['title'] ?? $log['name'] ?? 'Unrecorded Law',
                "desc"    => $log['desc'] ?? $log['description'] ?? $log['text'] ?? 'No manuscript details captured.',
                "symbol"  => $assigned_symbol
            ];
            $running_index++;
        }
    }
}
$chapters = array_values($chapters);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guidebook | Spirited Away</title>
    <link rel="icon" type="image/png" href="img/tabicon.ico">
    <link rel="stylesheet" href="guidebook.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,600;1,400&family=Lustria&display=swap" rel="stylesheet">
    <style>
        /* LAYOUT ARCHITECTURE FIXES FOR THE GLOW BOX AND REVERSE SYSTEM */
        .guide-content {
            padding: 80px 0;
            background-color: #050505;
            color: #fff;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            gap: 100px; 
        }

        .guide-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 60px;
            width: 100%;
        }

        .guide-item.reverse {
            flex-direction: row-reverse;
        }

        .guide-info {
            flex: 1;
        }

        .guide-info .chapter {
            color: #E2C275;
            font-size: 12px;
            letter-spacing: 3px;
            display: block;
            margin-bottom: 10px;
        }

        .guide-info h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem;
            font-weight: 300;
            color: #ffffff;
            margin-bottom: 20px;
        }

        .guide-info p {
            font-family: 'Lustria', serif;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.7);
        }

        .guide-visual {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .glow-box {
            width: 100%;
            max-width: 350px;
            height: 450px;
            border: 1px solid rgba(226, 194, 117, 0.2);
            padding: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2.5rem;
            color: #E2C275;
            background-color: rgba(0, 0, 0, 0.3);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            transition: all 0.5s ease;
        }

        .guide-item:hover .glow-box {
            border-color: rgba(226, 194, 117, 0.6);
            color: #fff;
            text-shadow: 0 0 10px rgba(226, 194, 117, 0.8);
        }

        
        .smooth-fade {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #000000;
            z-index: 99999;
            pointer-events: none;
            opacity: 1;
            visibility: visible;
            will-change: opacity;
            transition: opacity 0.8s cubic-bezier(0.22, 1, 0.36, 1), 
                        visibility 0.8s cubic-bezier(0.22, 1, 0.36, 1);
        }

        body.page-loaded .smooth-fade {
            opacity: 0;
            visibility: hidden;
        }

        body.page-exiting .smooth-fade {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body>
    <div id="transitionOverlay" class="smooth-fade"></div>
    
    <header class="main-header">
        <div class="logo">
            <a href="index.php"><img src="img/logo-removebg-preview.png" alt="Spirited Away" class="main-logo"></a>
        </div>
        <button class="menu-btn" id="menuBtn">☰</button>
    </header>

    <div class="side-menu" id="sideMenu">
        <div class="close-btn" id="closeBtn">&times;</div>
        <nav class="menu-links">
            <a href="index.php">HOME</a>
            <a href="about.php">ABOUT</a>
            <a href="gallery.php">GALLERY</a>
            <a href="characters.php">CHARACTER</a>
            <a href="folklore_origin.php">FOLKLORE ORIGIN</a>
            <a href="admin.php">ADMIN DASHBOARD</a>
        </nav>
    </div>
    <div class="overlay" id="overlay"></div>

    <section class="guide-hero">
        <div class="hero-overlay"></div>
        <div class="hero-text">
            <span class="category">ANCIENT MANUSCRIPT</span>
            <h1>The Spirit Roots</h1>
            <p>The Ancient Laws of the Unseen.</p>
        </div>
    </section>

    <main class="guide-content">
        <div class="container">
            <?php if (empty($chapters)): ?>
                <p style="text-align: center; color: #666; font-family: 'Lustria', serif; padding: 40px 0;">No manuscript data compiled.</p>
            <?php else: ?>
                <?php foreach ($chapters as $index => $item): ?>
                    <div class="guide-item <?php echo ($index % 2 !== 0) ? 'reverse' : ''; ?>">
                        <div class="guide-info">
                            <span class="chapter"><?php echo htmlspecialchars($item['chapter']); ?></span>
                            <h2><?php echo htmlspecialchars($item['title']); ?></h2>
                            <p><?php echo nl2br(htmlspecialchars($item['desc'])); ?></p>
                        </div>
                        <div class="guide-visual">
                            <div class="glow-box"><?php echo htmlspecialchars($item['symbol'] ?? '✦'); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <p>"Once you've met someone, you never really forget them."</p>
        <p style="font-size: 10px; margin-top: 10px; opacity: 0.5;">© Spirited Away. All Rights Reserved.</p>
    </footer>

    <script>
        const menuBtn = document.getElementById('menuBtn');
        const sideMenu = document.getElementById('sideMenu');
        const closeBtn = document.getElementById('closeBtn');
        const overlay = document.getElementById('overlay');
        
        if (menuBtn) { 
            menuBtn.onclick = () => { 
                sideMenu.classList.add('open'); 
                overlay.classList.add('show'); 
            }; 
        }
        function closeMenu() { 
            sideMenu.classList.remove('open'); 
            overlay.classList.remove('show'); 
        }
        if (closeBtn) closeBtn.onclick = closeMenu; 
        if (overlay) overlay.onclick = closeMenu;

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.body.classList.remove('page-exiting');
                document.body.classList.add('page-loaded');
            }, 100);

            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link || !link.href || link.target === '_blank' || 
                    link.href.includes('#') || !link.href.startsWith(window.location.origin)) {
                    return;
                }
                e.preventDefault();
                closeMenu();
                document.body.classList.remove('page-loaded');
                document.body.classList.add('page-exiting');
                setTimeout(() => {
                    window.location.href = link.href;
                }, 800);
            });
        });
    </script>
</body>
</html>