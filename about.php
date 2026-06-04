<?php
session_start();
if( !isset($_SESSION['username']) ) {
    header("Location: login.php");
    exit();
}

$dashboard_items = isset($_SESSION['dashboard_logs']) ? $_SESSION['dashboard_logs'] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About - Spirited Away</title>
  <link rel="icon" type="image/png" href="img/tabicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond&family=Lustria&family=Vollkorn&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="index.css">

  <style>
    body {
        background-color: #0c0c0c;
        color: #f5f5f5;
        font-family: 'Vollkorn', serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .about-container {
        max-width: 1000px;
        margin: 80px auto;
        padding: 0 40px;
        display: flex;
        gap: 60px;
        align-items: flex-start; 
    }

    /* Fixed width for both main and dynamic image columns */
    .about-left-col, 
    .styled-row-image-area {
        flex: 0 0 400px;
    }

    .main-about-img,
    .stream-thumb {
        width: 100%;
        height: auto;
        border-radius: 4px;
        display: block;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
    }

    .about-right-col,
    .styled-row-text-area {
        flex: 1;
        padding-top: 5px;
    }

    .about-title {
        font-family: 'Cormorant Garamond', serif;
        color: #c5a059;
        font-size: 32px;
        margin-bottom: 25px;
        letter-spacing: 0.5px;
        font-weight: normal;
    }

    .about-p {
        font-family: 'Lustria', sans-serif;
        font-size: 15px;
        line-height: 1.8;
        color: #cccccc; 
        margin-bottom: 25px;
        letter-spacing: 0.3px;
    }

    .dynamic-stream-section {
        max-width: 1000px;
        width: 100%;
        margin: 0 auto 60px auto;
        padding: 0 40px;
    }
    
    .stream-divider {
        border: 0;
        height: 1px;
        background: linear-gradient(to right, rgba(226, 177, 60, 0.25), transparent);
        margin-bottom: 45px;
    }

    .styled-stream-wrapper {
        display: flex;
        flex-direction: column;
        gap: 60px; 
    }

    .styled-row {
        display: flex;
        gap: 60px;
        align-items: flex-start;
    }

    @media (max-width: 900px) {
        .about-container, .styled-row { flex-direction: column; text-align: left; padding: 40px 20px; }
        .about-left-col, .styled-row-image-area { flex: 0 0 100%; }
        .dynamic-stream-section { padding: 0 20px; }
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

  <main class="about-container">
      <div class="about-left-col">
          <img src="img/about.jpg" alt="Hayao Miyazaki Illustration" class="main-about-img">
      </div>

      <div class="about-right-col">
          <h2 class="about-title">The Visionary: Hayao Miyazaki</h2>
          <p class="about-p">
              A co-founder of Studio Ghibli, Miyazaki is a master storyteller and animator. His films often explore the relationship between humanity, nature, and technology, woven with magical realism and deep emotional resonance.
          </p>
          <p class="about-p">
              Spirited Away (2001) remains his most celebrated work, inspired by the children of his friends and the traditional Japanese bathhouse culture.
          </p>
      </div>
  </main>

  <section class="dynamic-stream-section">
      <?php 
      $about_items = [];
      foreach ($dashboard_items as $item) {
          if (strtoupper($item['section']) === 'ABOUT') {
              $about_items[] = $item;
          }
      }

      if (!empty($about_items)): ?>
          <hr class="stream-divider">
          <div class="styled-stream-wrapper">
              <?php foreach ($about_items as $live_card): ?>
                  <div class="styled-row">
                      <div class="styled-row-image-area">
                          <img src="img/<?php echo htmlspecialchars($live_card['photo']); ?>" alt="Published Visual" class="stream-thumb" onerror="this.src='img/image_e6153a.jpg';">
                      </div>
                      
                      <div class="styled-row-text-area">
                          <h2 class="about-title"><?php echo htmlspecialchars($live_card['title']); ?></h2>
                          <p class="about-p">
                              <?php echo nl2br(htmlspecialchars($live_card['desc'])); ?>
                          </p>
                      </div>
                  </div>
              <?php endforeach; ?>
          </div>
      <?php endif; ?>
  </section>

  <div class="side-menu" id="sideMenu">
    <div class="close-btn" id="closeBtn">×</div>
    <nav class="menu-links">
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
                link.href.includes('#') || !link.href.startsWith(window.location.origin)) {
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