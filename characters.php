<?php

session_start();
if( !isset($_SESSION['username']) ) {
	header("Location: login.php");
	exit();
} else {
    
}


if (!isset($_SESSION['characters'])) {
    $_SESSION['characters'] = [
        ["tag" => "I", "name" => "Chihiro Ogino", "desc" => "Young girl who enters the spirit world and slowly learns courage, kindness, and responsibility. Through her journey, she grows from a frightened child into someone strong enough to protect others and herself.", "img" => "img/chihiro.jpg"],
        ["tag" => "II", "name" => '"Haku" Nigihayami Kohakunushi', "desc" => "Mysterious boy who is actually a river spirit. He guides Chihiro in the spirit world, showing both kindness and strength while struggling to remember his true identity.", "img" => "img/haku.jpg"],
        ["tag" => "III", "name" => "No-Face", "desc" => "Lonely spirit who reflects the emotions and desires of those around him. He becomes dangerous when consumed by greed, but gentle and calm when shown kindness.", "img" => "img/face.jpg"],
        ["tag" => "IV", "name" => "Yubaba", "desc" => "Powerful witch who rules the bathhouse in the spirit world. She is strict and intimidating, but values hard work and keeps order through magic and control.", "img" => "img/yubabs.jpg"],
        ["tag" => "V", "name" => "Kamaji", "desc" => "Multi-armed spirit who operates the bathhouse’s boiler room. Though he appears intimidating at first, he is one of Chihiro's most loyal allies.", "img" => "img/Kamaji.jpg"]
    ];
}


$characters = [];

// Process the baseline array configurations
foreach ($_SESSION['characters'] as $char) {
    $characters[] = [
        "tag"  => $char['tag'] ?? 'NEW',
        "name" => $char['name'] ?? $char['title'] ?? 'Unnamed Character',
        "desc" => $char['desc'] ?? $char['description'] ?? '',
        "img"  => !empty($char['img']) ? $char['img'] : 'img/chihiro.jpg'
    ];
}


if (isset($_SESSION['dashboard_logs']) && is_array($_SESSION['dashboard_logs'])) {
    $roman_numerals = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII", "XIII", "XIV", "XV", "XVI", "XVII"];
    $running_index = count($characters) + 1;

    foreach ($_SESSION['dashboard_logs'] as $log) {
        if (isset($log['section'])) {
            $clean_section = strtoupper(trim($log['section']));
            if ($clean_section === 'CHARACTER' || $clean_section === 'CHARACTERS') {
                
                
                $assigned_tag = isset($roman_numerals[$running_index - 1]) ? $roman_numerals[$running_index - 1] : $running_index;
                
                
                $photo_filename = !empty($log['photo']) ? $log['photo'] : (!empty($log['img']) ? $log['img'] : '');
                $photo_path = (strpos($photo_filename, 'img/') === 0) ? $photo_filename : 'img/' . $photo_filename;
                if(empty($photo_filename)) { $photo_path = 'img/chihiro.jpg'; }

                $characters[] = [
                    "tag"  => $assigned_tag,
                    "name" => $log['title'] ?? $log['name'] ?? 'Unrecorded Entity',
                    "desc" => $log['desc'] ?? $log['description'] ?? '',
                    "img"  => $photo_path
                ];
                $running_index++;
            }
        }
    }
}

$characters = array_values($characters);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Characters | Spirited Away</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="character.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;600&family=Lustria&display=swap" rel="stylesheet">
</head>
<body>
    <div id="transitionOverlay" class="smooth-fade"></div>
    
    <header class="header">
        <div class="logo-container">
            <a href="index.php"><img src="img/logo-removebg-preview.png" alt="Logo" class="main-logo"></a>
        </div>
        <div class="header-right"><button class="menu-btn" id="menuBtn">☰</button></div>
    </header>

    <div class="split-layout">
        <div class="split-left">
            <div class="image-frame">
                <img src="<?php echo !empty($characters) ? htmlspecialchars($characters[0]['img']) : 'img/chihiro.jpg'; ?>" id="main-visual" alt="Character Portrait" onerror="this.src='img/chihiro.jpg';">
            </div>
        </div>

        <div class="split-right">
            <?php if (empty($characters)): ?>
                <section class="content-section" data-img="img/chihiro.jpg">
                    <span class="tag">00</span>
                    <h1>No Profiles Found</h1>
                    <p>Navigate to your Admin Dashboard to add new character nodes here.</p>
                </section>
            <?php else: ?>
                <?php foreach ($characters as $index => $char): ?>
                    <section class="content-section" data-img="<?php echo htmlspecialchars($char['img']); ?>">
                        <span class="tag"><?php echo htmlspecialchars($char['tag']); ?></span>
                        <?php if ($index === 0): ?>
                            <h1><?php echo htmlspecialchars($char['name']); ?></h1>
                        <?php else: ?>
                            <h2><?php echo htmlspecialchars($char['name']); ?></h2>
                        <?php endif; ?>
                        <p><?php echo nl2br(htmlspecialchars($char['desc'])); ?></p>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="side-menu" id="sideMenu">
      <div class="close-btn" id="closeBtn">×</div>
      <nav class="menu-links">
        <a href="index.php">HOME</a>
        <a href="about.php">ABOUT</a>
        <a href="gallery.php">GALLERY</a>
        <a href="folklore_origin.php">FOLKLORE ORIGIN</a>
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
        
        const sections = document.querySelectorAll('.content-section');
        const mainImg = document.getElementById('main-visual');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const newImg = entry.target.getAttribute('data-img');
                    if (newImg) {
                        // Create a temporary image object to verify path validity before swapping
                        const imgTester = new Image();
                        imgTester.onload = function() {
                            if (mainImg.getAttribute('src') !== newImg) {
                                mainImg.style.opacity = '0';
                                setTimeout(() => {
                                    mainImg.src = newImg;
                                    mainImg.style.opacity = '1';
                                }, 300);
                            }
                        };
                        imgTester.onerror = function() {
                            if (mainImg.getAttribute('src') !== 'img/chihiro.jpg') {
                                mainImg.style.opacity = '0';
                                setTimeout(() => {
                                    mainImg.src = 'img/chihiro.jpg';
                                    mainImg.style.opacity = '1';
                                }, 300);
                            }
                        };
                        imgTester.src = newImg;
                    }
                }
            });
        }, { threshold: 0.5 });

        sections.forEach(section => {
            if (section.getAttribute('data-img')) {
                observer.observe(section);
            }
        });

        
        const menuBtn = document.getElementById("menuBtn");
        const sideMenu = document.getElementById("sideMenu");
        const closeBtn = document.getElementById("closeBtn");
        const overlay = document.getElementById("overlay");

        if(menuBtn) {
            menuBtn.onclick = () => {
              sideMenu.classList.add("open");
              overlay.classList.add("show");
            };
        }

        if(closeBtn) closeBtn.onclick = closeMenu;
        if(overlay) overlay.onclick = closeMenu;

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
</body>
</html>