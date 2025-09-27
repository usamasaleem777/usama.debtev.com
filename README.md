<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>DebtDev — README</title>
  <style>
    :root{
      --bg:#0f1724;
      --card:#0b1220;
      --muted:#9aa4b2;
      --accent:#6ee7b7;
      --glass: rgba(255,255,255,0.03);
      --glass-2: rgba(255,255,255,0.02);
      --glass-border: rgba(255,255,255,0.04);
      --mono: 'SFMono-Regular', Consolas, "Liberation Mono", Menlo, monospace;
    }
    *{box-sizing:border-box}
    html,body{height:100%;margin:0;font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; background: linear-gradient(180deg,#071029 0%, #071a2a 60%), var(--bg); color:#e6eef6}
    .wrap{max-width:980px;margin:40px auto;padding:28px;border-radius:14px;background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));box-shadow:0 6px 30px rgba(2,6,23,0.7);border:1px solid var(--glass-border)}
    header{display:flex;align-items:center;gap:16px}
    .logo{width:64px;height:64px;border-radius:12px;background:linear-gradient(135deg,#0ea5a6,#06b6d4);display:grid;place-items:center;font-weight:700;color:#04263a}
    h1{margin:0;font-size:26px}
    .tagline{color:var(--muted);margin-top:6px}

    .grid{display:grid;grid-template-columns: 1fr 340px;gap:22px;margin-top:20px}
    .card{background:var(--card);padding:18px;border-radius:12px;border:1px solid var(--glass);}

    .features{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
    .feature{display:flex;gap:10px;align-items:flex-start;padding:10px;border-radius:10px;background:linear-gradient(180deg,var(--glass),transparent);border:1px solid var(--glass-2)}
    .feature svg{width:28px;height:28px;flex-shrink:0}
    .feature h4{margin:0;font-size:14px}
    .feature p{margin:4px 0 0;color:var(--muted);font-size:13px}

    .tech-list{display:flex;flex-wrap:wrap;gap:8px;margin-top:8px}
    .pill{background:transparent;border:1px solid var(--glass-border);padding:6px 10px;border-radius:999px;color:var(--muted);font-size:13px}

    pre{background:linear-gradient(180deg, rgba(2,6,23,0.8), rgba(2,6,23,0.6));padding:14px;border-radius:10px;overflow:auto;border:1px solid var(--glass-border);font-family:var(--mono);font-size:13px}

    .meta{font-size:13px;color:var(--muted);display:flex;gap:10px;flex-wrap:wrap}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;border:1px solid var(--glass-border);background:linear-gradient(180deg, rgba(255,255,255,0.02), transparent);cursor:pointer}

    footer{margin-top:18px;color:var(--muted);font-size:13px}

    @media (max-width:920px){.grid{grid-template-columns:1fr}.features{grid-template-columns:1fr}}
  </style>
</head>
<body>
  <main class="wrap">
    <header>
      <div class="logo">DD</div>
      <div>
        <h1>DebtDev</h1>
        <div class="tagline">🚀 DebtDev helps individuals and organizations manage debt, credit, and financial operations with clarity and security.</div>
      </div>
    </header>

    <div class="grid">
      <section class="card">
        <h2 style="margin-top:0">🔥 Overview</h2>
        <p style="color:var(--muted);margin-top:8px;line-height:1.6">DebtDev is a modern platform designed to help individuals, businesses, and organizations manage debt, credit, and financial operations more efficiently. Our goal is to simplify financial tracking, improve transparency, and provide powerful tools for debt management and recovery.</p>

        <h3 style="margin-top:16px">🔥 Features</h3>
        <div class="features">
          <div class="feature">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/></svg>
            <div>
              <h4>📊 Debt & Credit Tracking</h4>
              <p>Manage loans, credits, and repayments with ease.</p>
            </div>
          </div>

          <div class="feature">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"/></svg>
            <div>
              <h4>🏦 Multi-User & Multi-Company Support</h4>
              <p>Built for businesses managing multiple accounts and teams.</p>
            </div>
          </div>

          <div class="feature">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/></svg>
            <div>
              <h4>🔗 Online & Offline Sync</h4>
              <p>Work offline; sync automatically when online.</p>
            </div>
          </div>

          <div class="feature">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            <div>
              <h4>📅 Payment Scheduling & Reminders</h4>
              <p>Schedule payments and never miss due dates.</p>
            </div>
          </div>

          <div class="feature">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
            <div>
              <h4>📈 Analytics & Reports</h4>
              <p>Real-time charts and detailed financial reports.</p>
            </div>
          </div>

          <div class="feature">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z"/><path d="M7 21v-2a4 4 0 0 1 4-4h2a4 4 0 0 1 4 4v2"/></svg>
            <div>
              <h4>🔒 Secure Authentication</h4>
              <p>Role-based access control and strong encryption.</p>
            </div>
          </div>
        </div>

        <h3 style="margin-top:16px">🛠️ Tech Stack</h3>
        <div class="tech-list">
          <span class="pill">PHP (Laravel)</span>
          <span class="pill">MySQL</span>
          <span class="pill">TailwindCSS</span>
          <span class="pill">Bootstrap</span>
          <span class="pill">Vue.js</span>
          <span class="pill">Laravel Breeze</span>
          <span class="pill">LAMP / Cloud</span>
          <span class="pill">REST API</span>
          <span class="pill">Offline-first</span>
        </div>

        <h3 style="margin-top:16px">⚡ Installation</h3>
        <p class="meta">Clone, configure, and run the project locally.</p>
        <pre><code id="install-cmd"># Clone repository
git clone https://github.com/yourusername/debtdev.git

# Navigate into project
cd debtdev

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate --seed

# Start development server
php artisan serve
</code></pre>
        <div style="margin-top:10px;display:flex;gap:8px">
          <button class="btn" onclick="copyInstall()">Copy install commands</button>
          <a class="btn" href="https://github.com/yourusername/debtdev" target="_blank" rel="noopener">View on GitHub</a>
        </div>

        <h3 style="margin-top:16px">🤝 Contributing</h3>
        <p style="color:var(--muted)">We welcome contributions! Fork the repo, create a branch, and submit a pull request. Please follow the contribution guidelines and code of conduct in the repository.</p>

        <h3 style="margin-top:12px">📄 License</h3>
        <p style="color:var(--muted)">This project is licensed under the <strong>MIT License</strong>.</p>

        <footer>
          <p>👉 With DebtDev, take control of your financial future with clarity, security, and efficiency.</p>
        </footer>
      </section>

      <aside class="card">
        <h3 style="margin-top:0">📬 Contact</h3>
        <p class="meta">Website: <a href="https://debtev.com" target="_blank" style="color:var(--accent)">debtev.com</a></p>
        <p class="meta">Email: <a href="mailto:support@debtev.com" style="color:var(--accent)">support@debtev.com</a></p>
        <p class="meta">GitHub: <a href="https://github.com/yourusername/debtdev" target="_blank" style="color:var(--accent)">github.com/yourusername/debtdev</a></p>

        <hr style="border:none;border-top:1px solid var(--glass-border);margin:12px 0">

        <h4>Quick Links</h4>
        <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px">
          <a class="btn" href="#" onclick="alert('Replace this link with your docs URL')">Docs & API</a>
          <a class="btn" href="#" onclick="alert('Replace with privacy policy link')">Privacy Policy</a>
          <a class="btn" href="#" onclick="alert('Replace with support portal')">Support</a>
        </div>

        <hr style="border:none;border-top:1px solid var(--glass-border);margin:12px 0">
        <small style="color:var(--muted);">Last updated: Sep 28, 2025</small>
      </aside>
    </div>
  </main>

  <script>
    function copyInstall(){
      const txt = document.getElementById('install-cmd').innerText;
      navigator.clipboard.writeText(txt).then(()=>{alert('Installation commands copied to clipboard')},()=>{alert('Copy failed — select and copy manually')});
    }
  </script>
</body>
</html>
