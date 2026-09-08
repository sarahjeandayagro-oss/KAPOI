<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Library Entrance Portal — Panabo City Library</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --primary: #2F9E8F;
      --primary-dark: #26867A;
      --primary-light: rgba(47, 158, 143, 0.08);
      --bg: #F5F7FA;
      --panel: #FFFFFF;
      --card: #FFFFFF;
      --border: #E5E7EB;
      --text: #1F2937;
      --muted: #6B7280;
      --success: #22C55E;
      --warning: #F59E0B;
      --error: #EF4444;
      --shadow: 0 10px 30px rgba(15,23,42,.08);
      --shadow-lg: 0 20px 60px rgba(15,23,42,.12);
      --radius: 12px;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      font-size: 14px;
    }

    /* ===== HEADER ===== */
    .header {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      padding: 16px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
      box-shadow: 0 4px 20px rgba(47,158,143,.25);
    }

    .header-left {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .header-logo {
      width: 44px;
      height: 44px;
      border-radius: 10px;
      object-fit: cover;
      border: 2px solid rgba(255,255,255,.3);
    }

    .header-brand {
      display: flex;
      flex-direction: column;
    }

    .header-brand h1 {
      font-size: 17px;
      font-weight: 700;
      letter-spacing: -0.3px;
    }

    .header-brand span {
      font-size: 12px;
      opacity: 0.85;
    }

    .header-links {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .header-links a {
      color: white;
      text-decoration: none;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 500;
      border: 1px solid rgba(255,255,255,.3);
      transition: all .2s;
    }

    .header-links a:hover {
      background: rgba(255,255,255,.15);
      border-color: rgba(255,255,255,.5);
    }

    .header-links a.primary-link {
      background: white;
      color: var(--primary);
      border-color: white;
      font-weight: 600;
    }

    /* ===== MAIN CONTAINER ===== */
    .main-container {
      min-height: calc(100vh - 76px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    /* ===== PORTAL SECTION ===== */
    .portal-section {
      text-align: center;
      max-width: 600px;
      width: 100%;
    }

    .portal-icon {
      width: 120px;
      height: 120px;
      margin: 0 auto 32px;
      border-radius: 20px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 60px;
      box-shadow: 0 10px 30px rgba(47, 158, 143, 0.2);
    }

    .portal-title {
      font-size: 32px;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 12px;
      letter-spacing: -0.5px;
    }

    .portal-desc {
      color: var(--muted);
      font-size: 15px;
      line-height: 1.8;
      margin-bottom: 32px;
    }

    /* ===== OPAC BUTTON ===== */
    .opac-button {
      display: inline-block;
      width: 100%;
      max-width: 400px;
      padding: 24px;
      background: var(--panel);
      border: 2px solid var(--border);
      border-radius: var(--radius);
      text-decoration: none;
      color: var(--text);
      cursor: pointer;
      transition: all .3s;
      box-shadow: var(--shadow);
      font-family: 'Inter', sans-serif;
    }

    .opac-button:hover {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(47, 158, 143, 0.12), var(--shadow-lg);
      transform: translateY(-3px);
    }

    .opac-button-icon {
      font-size: 36px;
      margin-bottom: 12px;
      display: block;
    }

    .opac-button-label {
      display: block;
      font-size: 18px;
      font-weight: 600;
      color: var(--text);
      margin-bottom: 8px;
    }

    .opac-button-desc {
      display: block;
      font-size: 13px;
      color: var(--muted);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
      .header {
        padding: 12px 16px;
      }

      .header-logo {
        width: 40px;
        height: 40px;
      }

      .header-brand h1 {
        font-size: 15px;
      }

      .header-brand span {
        font-size: 11px;
      }

      .main-container {
        padding: 30px 16px;
      }

      .portal-title {
        font-size: 24px;
      }

      .portal-icon {
        width: 100px;
        height: 100px;
        font-size: 48px;
        margin-bottom: 24px;
      }
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="header">
    <div class="header-left">
      <img src="{{ asset('images/library logos.jpg') }}" alt="Library Logo" class="header-logo" />
      <div class="header-brand">
        <h1>Panabo City Library</h1>
        <span>Management System</span>
      </div>
    </div>
    <div class="header-links">
      <a href="{{ route('login') }}" class="primary-link">Login</a>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <div class="main-container">
    <div class="portal-section">
      <div class="portal-icon">📚</div>
      <h2 class="portal-title">Library Entrance Portal</h2>
      <p class="portal-desc">
        Welcome to Panabo City Library's Online Public Access Catalog. Search and browse our collection of books and resources.
      </p>

      <a href="{{ route('opac.index') }}" class="opac-button">
        <span class="opac-button-icon">🔍</span>
        <span class="opac-button-label">Browse Catalog</span>
        <span class="opac-button-desc">Access our Online Public Access Catalog</span>
      </a>
    </div>
  </div>
</body>
</html>
