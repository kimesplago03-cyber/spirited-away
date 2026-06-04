<?php
session_start();
if( !isset($_SESSION['username']) ) {
	header("Location: login.php");
	exit();
} else {
    
}


$folklore_logs = [];


if (isset($_SESSION['dashboard_logs']) && is_array($_SESSION['dashboard_logs'])) {
    foreach ($_SESSION['dashboard_logs'] as $log) {
        if (isset($log['section'])) {
            $clean_section = strtoupper(trim($log['section']));
            if ($clean_section === 'FOLKLORE' || $clean_section === 'FOLKLORE ORIGIN' || $clean_section === 'FOLKLORE_ORIGIN') {
                $folklore_logs[] = $log;
            }
        }
    }
} else {
    $db_file = 'database.json';
    if (file_exists($db_file)) {
        $file_content = file_get_contents($db_file);
        $all_entries = json_decode($file_content, true);
        
        if (is_array($all_entries)) {
            foreach ($all_entries as $log) {
                if (isset($log['section'])) {
                    $clean_section = strtoupper(trim($log['section']));
                    if ($clean_section === 'FOLKLORE' || $clean_section === 'FOLKLORE ORIGIN' || $clean_section === 'FOLKLORE_ORIGIN') {
                        $folklore_logs[] = $log;
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Folklore Origin - Spirited Away</title>
    <link rel="icon" type="image/png" href="img/tabicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;600&family=Lustria&display=swap" rel="stylesheet">
    <style>
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #050505;
            color: #ffffff;
            font-family: 'Lustria', serif;
            line-height: 1.8;
            overflow-x: hidden;
        }

        .header {
            position: fixed;
            top: 0;
            width: 100%;
            height: 90px;
            padding: 0 50px;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent);
            backdrop-filter: blur(5px);
        }

        .main-logo { height: 50px; }

        .menu-btn {
            margin-left: auto; 
            background: none;
            border: none;
            color: #E2C275;
            font-size: 28px;
            cursor: pointer;
            z-index: 1001; 
        }

        .side-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 350px;
            height: 100%;
            background: #000;
            z-index: 1001;
            padding: 80px 40px;
            transition: 0.5s ease;
            display: flex;
            flex-direction: column;
        }

        .side-menu.open {
            right: 0;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 40px;
            color: #A7373F;
            cursor: pointer;
        }

        .menu-links a {
            font-family: 'Lustria', serif;
            display: block;
            font-size: 24px;
            color: #E2C275;
            text-decoration: none;
            margin-bottom: 30px;
            transition: 0.3s;
        }

        .menu-links a:hover {
            color: #A7373F;
            padding-left: 10px;
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            opacity: 0;
            visibility: hidden;
            z-index: 1000;
            transition: 0.4s;
        }

        .overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .hero {
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('img/hero-folklore.jpg') center/cover no-repeat;
            position: relative;
            text-align: center;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.4), #050505);
        }

        .hero-content { z-index: 2; }

        .sub-title { 
            color: #E2C275; letter-spacing: 5px; 
            font-size: 12px; 
            display: block; 
            margin-bottom: 10px; 
        }

        h1, h2 { font-family: 'Cormorant Garamond', serif; font-weight: 300; }

        .hero-content h1 { 
            font-size: clamp(3rem, 8vw, 5rem); 
        }

        .content-wrapper { 
            padding: 80px 0; 
            max-width: 1200px; 
            margin: 0 auto; 
        }

        .folklore-section {
            display: flex;
            align-items: center;
            gap: 60px;
            padding: 60px 20px;
            opacity: 0;
            transform: translateY(30px);
            transition: 1.2s ease-out;
        }

        .folklore-section.visible { 
            opacity: 1; transform: translateY(0); 
        }

        .folklore-section.reverse { 
            flex-direction: row-reverse; 
        }

        .text-side { flex: 1; }
        .image-side { flex: 1; }

        .chapter-num { 
            color: #E2C275; 
            font-size: 12px; 
            letter-spacing: 3px; 
            display: block; 
            margin-bottom: 10px; 
        }

        .text-side h2 { 
            font-size: 3rem; 
            margin-bottom: 20px; 
            color: #E2C275; 
        }

        .image-frame {
            width: 100%;
            height: 500px;
            border: 1px solid rgba(226, 194, 117, 0.2);
            padding: 10px;
        }

        .image-frame img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
        }

        .footer {
            padding: 60px 20px;
            text-align: center;
            border-top: 1px solid rgba(226, 194, 117, 0.1);
            color: #999;
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

    <div class="side-menu" id="sideMenu">
        <div class="close-btn" id="closeBtn">×</div>
        <nav class="menu-links">
            <a href="index.php">HOME</a>
            <a href="about.php">ABOUT</a>
            <a href="gallery.php">GALLERY</a>
            <a href="characters.php">CHARACTER</a>
            <a href="guidebook.php">SPIRIT WORLD GUIDEBOOK</a>
            <a href="admin.php">ADMIN DASHBOARD</a>
        </nav>
    </div>
    <div class="overlay" id="overlay"></div>

    <div class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <span class="sub-title">MYTHOLOGY & TRADITION</span>
            <h1>The Sacred Realm</h1>
            <p>Explore the ancient Japanese beliefs that breathe life into the spirits.</p>
        </div>
    </div>

    <main class="content-wrapper">
        <section class="folklore-section">
            <div class="text-side">
                <span class="chapter-num">CHAPTER I</span>
                <h2>The Heart of Shintoism</h2>
                <p>At the core of Spirited Away lies <strong>Shinto</strong>, Japan's indigenous religion. It asserts that spiritual powers, or <em>Kami</em>, inhabit all aspects of nature.</p>
            </div>
            <div class="image-side">
                <div class="image-frame">
                    <img src="img/shintoo.jpg" alt="Shinto Shrine">
                </div>
            </div>
        </section>

        <section class="folklore-section reverse">
            <div class="text-side">
                <span class="chapter-num">CHAPTER II</span>
                <h2>Yaoyorozu no Kami</h2>
                <p>The phrase <em>"Yaoyorozu no Kami"</em>, literally meaning "Eight Million Gods," signifies the infinite number of deities in the world.</p>
            </div>
            <div class="image-side">
                <div class="image-frame">
                    <img src="img/spirit.jpg" alt="Spirits Gathering">
                </div>
            </div>
        </section>

        <section class="folklore-section">
            <div class="text-side">
                <span class="chapter-num">CHAPTER III</span>
                <h2>The Kamikakushi</h2>
                <p>Refers to people mysteriously taken away by spirits or gods. <strong>Chihiro’s sudden disappearance</strong> into the spirit world reflects this belief, where humans cross into a hidden realm beyond ordinary life.</p>
            </div>
            <div class="image-side">
                <div class="image-frame">
                    <img src="img/tunnel.jpg" alt="Tunnel Entrance">
                </div>
            </div>
        </section>   

        <?php if (!empty($folklore_logs)): ?>
            <?php 
            $chapter_counter = 4; 
            
            foreach ($folklore_logs as $index => $item): 
                $title = !empty($item['title']) ? htmlspecialchars($item['title']) : 'Unrecorded Mythos';
                
                $desc = 'No description archived.';
                if (!empty($item['desc'])) {
                    $desc = htmlspecialchars($item['desc']);
                } elseif (!empty($item['description'])) {
                    $desc = htmlspecialchars($item['description']);
                }
                
                $photo = !empty($item['photo']) ? htmlspecialchars($item['photo']) : 'tunnel.jpg';
                
                $reverse_class = ($index % 2 === 0) ? ' reverse' : '';
                
                $roman_numerals = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII", "XIII", "XIV", "XV"];
                $chapter_display = isset($roman_numerals[$chapter_counter - 1]) ? $roman_numerals[$chapter_counter - 1] : $chapter_counter;
            ?>
                <section class="folklore-section<?php echo $reverse_class; ?>">
                    <div class="text-side">
                        <span class="chapter-num">CHAPTER <?php echo $chapter_display; ?></span>
                        <h2><?php echo $title; ?></h2>
                        <p><?php echo nl2br($desc); ?></p>
                    </div>
                    <div class="image-side">
                        <div class="image-frame">
                            <img src="img/<?php echo $photo; ?>" 
                                 alt="<?php echo $title; ?>" 
                                 onerror="this.onerror=null; this.src='img/tunnel.jpg';">
                        </div>
                    </div>
                </section>   
            <?php 
                $chapter_counter++;
            endforeach; 
            ?>
        <?php endif; ?>
    </main>

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

        function closeMenu() {
            sideMenu.classList.remove("open");
            overlay.classList.remove("show");
        }
        closeBtn.onclick = closeMenu;
        overlay.onclick = closeMenu;

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.body.classList.add('page-loaded');
            }, 100);

            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link || !link.href || link.target === '_blank' || 
                    link.href.includes('#') || !link.href.startsWith(window.location.origin)) {
                    return;
                }
                e.preventDefault();
                document.body.classList.remove('page-loaded');
                document.body.classList.add('page-exiting');
                setTimeout(() => {
                    window.location.href = link.href;
                }, 800);
            });
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.folklore-section').forEach(section => {
            observer.observe(section);
        });
    </script>
</body>
</html>