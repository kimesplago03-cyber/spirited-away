<?php
session_start();
if( !isset($_SESSION['username']) ) {
	header("Location: login.php");
	exit();
} else {
    
}


$gallery_logs = [];
if (isset($_SESSION['dashboard_logs']) && is_array($_SESSION['dashboard_logs'])) {
    foreach ($_SESSION['dashboard_logs'] as $log) {
        if (isset($log['section']) && strtoupper(trim($log['section'])) === 'GALLERY') {
            $gallery_logs[] = $log;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="icon" type="image/png" href="img/tabicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Lustria&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    
    <style>
        body {
            background-color: #050505 !important;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            color: #fff;
        }

        .gallery-hero {
            height: 100vh;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('img/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed; 
            text-align: center;
        }

        .hero-content h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(40px, 10vw, 90px);
            letter-spacing: 15px;
            text-transform: uppercase;
            margin: 20px 0;
        }

        .sub-title {
            color: #E2C275;
            letter-spacing: 5px;
            font-size: 12px;
            display: block;
        }

        .scroll-line {
            width: 1px;
            height: 60px;
            background: linear-gradient(to bottom, #E2C275, transparent);
            margin: 40px auto 0;
            position: relative;
            overflow: hidden;
        }

        .scroll-line::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            animation: scrollMove 2s infinite;
        }

        @keyframes scrollMove {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(100%); }
        }

        .gallery-container {
            padding: 100px 5%;
            background-color: #050505;
        }

        .gallery-banner {
            width: 100%;
            height: 60vh;
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid rgba(226, 194, 117, 0.2);
        }

        .gallery-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 1.5s ease;
        }

        .gallery-banner:hover img {
            transform: scale(1.05);
        }

        .banner-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 50px;
            background: linear-gradient(transparent, rgba(0,0,0,0.9));
        }

        .banner-overlay h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 38px;
            font-weight: 300;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            grid-auto-rows: 300px;
            grid-auto-flow: dense;
            gap: 20px;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(226, 194, 117, 0.1);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(40%) brightness(0.7);
            transition: 0.8s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .gallery-item:hover img {
            filter: grayscale(0%) brightness(1);
            transform: scale(1.1);
        }

        
        .gallery-item.tall { grid-row: span 2; }
        .gallery-item.wide { grid-column: span 2; }

        
        .dynamic-text-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 40px 25px 25px 25px;
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 70%, transparent 100%);
            box-sizing: border-box;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            z-index: 2;
        }

        
        .dynamic-text-overlay .dynamic-subtitle-desc {
            font-family: 'Lustria', sans-serif;
            font-size: 10px;
            color: #E2C275;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
        }

        
        .dynamic-text-overlay .dynamic-title-heading {
            font-family: 'Cormorant Garamond', serif;
            font-size: 26px;
            color: #ffffff;
            margin: 0;
            font-weight: normal;
            line-height: 1.2;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.9);
        }

        .smooth-fade {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            z-index: 100000;
            pointer-events: none;
            opacity: 1;
            transition: opacity 0.8s ease;
        }

        .page-loaded .smooth-fade { opacity: 0; }
        .page-exiting .smooth-fade { opacity: 1; }
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

    <section class="gallery-hero">
        <div class="hero-content">
            <span class="sub-title">WELCOME TO THE</span>
            <h1>Spirit Realm</h1>
            <p>Scroll down to reveal the visual archives</p>
            <div class="scroll-line"></div>
        </div>
    </section>

    <main class="gallery-container">
        
        <div class="gallery-banner">
            <img src="img/dragon.gif" alt="Featured Masterpiece">
            <div class="banner-overlay">
                <span class="sub-title">FEATURED ARTWORK</span>
                <h2>Journey through a world beyond.</h2>
            </div>
        </div>

        <div class="gallery-grid">
            <div class="gallery-item tall"><img src="img/castle.jpg" alt="Art 1"></div> 
            <div class="gallery-item wide"><img src="img/34554-3250x1757-desktop-hd-spirited-away-wallpaper-image.jpg" alt="Art 2"></div>
            <div class="gallery-item"><img src="img/noface.jpg" alt="Art 3"></div>
            <div class="gallery-item tall"><img src="img/faceless.jpg" alt="Art 4"></div>
            <div class="gallery-item"><img src="img/yubaba.jpg" alt="Art 5"></div>
            <div class="gallery-item wide"><img src="img/chihirohaku.gif" alt="Art 6"></div>
            <div class="gallery-item"><img src="img/others.jpg" alt="Art 7"></div>

            <?php if (!empty($gallery_logs)): ?>
                <?php 
                
                $layout_pattern = ['', 'tall', 'wide', ''];
                $pattern_count = count($layout_pattern);
                
                $item_index = 0; 
                foreach ($gallery_logs as $item): 
                    
                    $size_class = $layout_pattern[$item_index % $pattern_count];
                    
                    $img_filename = !empty($item['photo']) ? htmlspecialchars($item['photo']) : 'dragon.gif';
                    $img_title = !empty($item['title']) ? htmlspecialchars($item['title']) : 'Archive Artifact';
                    $img_desc = !empty($item['desc']) ? htmlspecialchars($item['desc']) : 'SPIRIT WORLD LOG';
                ?>
                    <div class="gallery-item <?php echo $size_class; ?>">
                        <img src="img/<?php echo $img_filename; ?>" 
                             alt="<?php echo $img_title; ?>"
                             onerror="this.onerror=null; if(this.src.indexOf('img/') !== -1){ this.src='img/dragon.gif'; } else { this.src='img/castle.jpg'; }">
                        
                        <div class="dynamic-text-overlay">
                            <span class="dynamic-subtitle-desc"><?php echo $img_desc; ?></span>
                            
                            <h3 class="dynamic-title-heading"><?php echo $img_title; ?></h3>
                        </div>
                    </div>
                <?php 
                $item_index++;
                endforeach; 
                ?>
            <?php endif; ?>
        </div>
    </main>

    <div class="side-menu" id="sideMenu">
        <div class="close-btn" id="closeBtn">×</div>
        <nav class="menu-links">
            <a href="index.php">HOME</a>
            <a href="about.php">ABOUT</a>
            <a href="folklore_origin.php">FOLKLORE ORIGIN</a>
            <a href="characters.php">CHARACTER</a>
            <a href="guidebook.php">GUIDEBOOK</a>
            <a href="admin.php">ADMIN DASHBOARD</a>
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

        menuBtn.onclick = () => { sideMenu.classList.add("open"); overlay.classList.add("show"); };
        const closeMenu = () => { sideMenu.classList.remove("open"); overlay.classList.remove("show"); };
        closeBtn.onclick = closeMenu;
        overlay.onclick = closeMenu;

        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('page-loaded');
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link || !link.href || link.target === '_blank' || link.href.includes('#') || !link.href.startsWith(window.location.origin)) return;
                e.preventDefault();
                document.body.classList.remove('page-loaded');
                document.body.classList.add('page-exiting');
                setTimeout(() => { window.location.href = link.href; }, 800);
            });
        });
    </script>
</body>
</html>