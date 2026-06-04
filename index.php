<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home</title>
    <link rel="icon" type="image/png" href="img/tabicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond&family=Lustria&family=Vollkorn&display=swap" rel="stylesheet">
    
<?php
session_start();
if( !isset($_SESSION['username']) ) {
	header("Location: login.php");
	exit();
} else {
    
}?>
    
    <style>
        /* CSS Reset & Base */
        @font-face {
            font-family: 'YujiBoku';
            src: url('fonts/YujiBoku-Regular.ttf'); 
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: #010101; 
            color: #E2C275;
            font-family: 'Vollkorn', serif;
        }

        /* Header */
        .header {
            position: fixed; top: 0; left: 0; width: 100%; height: 100px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 50px; z-index: 1000;
            background: linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0) 100%);
        }

        .main-logo { height: 60px; width: auto; object-fit: contain; }

        .menu-btn {
            background: none; border: none; color: #E2C275;
            font-size: 30px; cursor: pointer;
            transition: transform 0.3s ease;
        }
        .menu-btn:hover { transform: scale(1.1); color: #fff; }

        /* Hero Section */
        .hero {
            position: relative; width: 100%; height: 100vh;
            display: flex; align-items: flex-end; justify-content: flex-start;
            padding: 80px 60px; overflow: hidden;
        }
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
             filter: brightness(0.9);
        }
        
        .meta-info {
            font-family: 'Lustria', serif; font-size: 14px;
            letter-spacing: 3px; color: #7BAF9E;
            margin-bottom: 20px; text-transform: uppercase;
        }
        .hero-p {
            font-family: 'Cormorant Garamond', serif; font-size: 20px;
            line-height: 1.6; color: #D1D1D1; max-width: 500px;
        }

        /* Side Menu */
        .side-menu {
            position: fixed; top: 0; right: -100%; width: 350px; height: 100%;
            background: #000; z-index: 1001; padding: 80px 40px;
            transition: 0.5s ease; display: flex; flex-direction: column;
        }
        .side-menu.open { right: 0; }
        .close-btn { position: absolute; top: 20px; right: 30px; font-size: 40px; color: #A7373F; cursor: pointer; }
        .menu-links a {
            font-family: 'Lustria', serif; display: block; font-size: 24px;
            color: #E2C275; text-decoration: none; margin-bottom: 30px; transition: 0.3s;
        }
        .menu-links a:hover { color: #A7373F; padding-left: 10px; }

        .overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.7);
            opacity: 0; visibility: hidden; z-index: 1000; transition: 0.4s;
        }
        .overlay.show { opacity: 1; visibility: visible; }

        /* Footer & Transitions */
        .footer { padding: 60px 20px; text-align: center; border-top: 1px solid rgba(226, 194, 117, 0.1); color: #999; }
        .smooth-fade {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: #000000; z-index: 99999; pointer-events: none;
            opacity: 1; visibility: visible; transition: opacity 0.8s ease, visibility 0.8s ease;
        }
        .page-loaded .smooth-fade { opacity: 0; visibility: hidden; }
        .page-exiting .smooth-fade { opacity: 1; visibility: visible; }
    </style>
</head>
<body class="page-loaded">
    <div id="transitionOverlay" class="smooth-fade"></div>
    
    <header class="header">
        <div class="logo-container">
            <a href="index.php"><img src="img/logo-removebg-preview.png" alt="Logo" class="main-logo"></a>
        </div>
        <div class="header-right">
            <button class="menu-btn" id="menuBtn">☰</button>
        </div>
    </header>

    <section class="hero">
        <img src="img/indexphoto.jpg" alt="Background" class="hero-bg">
        <div class="hero-content">
            <div class="meta-info">2001 &nbsp; ADVENTURE &nbsp; ANIME</div>
            <p class="hero-p">
                A hidden realm where spirits roam freely, stories are born, 
                and ancient folklore comes alive beyond the human world.
            </p>
        </div>
    </section>

    <div class="side-menu" id="sideMenu">
        <div class="close-btn" id="closeBtn">×</div>
        <nav class="menu-links">
            <a href="about.php">ABOUT</a>
            <a href="gallery.php">GALLERY</a>
            <a href="folklore_origin.php">FOLKLORE ORIGIN</a>
            <a href="characters.php">CHARACTER</a>
            <a href="guidebook.php">SPIRIT WORLD GUIDEBOOK</a>
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

        menuBtn.onclick = () => {
            sideMenu.classList.add("open");
            overlay.classList.add("show");
        };

        const closeMenu = () => {
            sideMenu.classList.remove("open");
            overlay.classList.remove("show");
        };

        closeBtn.onclick = closeMenu;
        overlay.onclick = closeMenu;

        window.addEventListener('load', () => document.body.classList.add('page-loaded'));

        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link || !link.href || link.target === '_blank' || link.href.includes('#') || !link.href.startsWith(window.location.origin)) return;
            
            e.preventDefault();
            document.body.classList.remove('page-loaded');
            document.body.classList.add('page-exiting');
            setTimeout(() => { window.location.href = link.href; }, 800);
        });
    </script>
</body>
</html>