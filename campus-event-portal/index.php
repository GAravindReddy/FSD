<?php
session_start();
require_once 'config/db.php';
require_once 'config/base.php';
$stmt=$pdo->prepare("SELECT * FROM events WHERE event_date>=NOW() ORDER BY event_date ASC LIMIT 6");
$stmt->execute();
$events=$stmt->fetchAll(PDO::FETCH_ASSOC);
$totalEvents=$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalUsers=$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalRegs=$pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/><title>CampusVerse — Event Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
<style>
:root{--bg:#050810;--surface:#0d1117;--card:#111827;--border:rgba(255,255,255,0.06);--accent:#6EE7B7;--accent2:#818CF8;--accent3:#F472B6;--text:#F1F5F9;--muted:#94A3B8;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}html{scroll-behavior:smooth;}
body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;overflow-x:hidden;}
nav{position:fixed;top:0;left:0;right:0;z-index:100;display:flex;align-items:center;justify-content:space-between;padding:1.2rem 4rem;background:rgba(5,8,16,0.85);backdrop-filter:blur(20px);border-bottom:1px solid var(--border);}
.nav-logo{font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;background:linear-gradient(135deg,var(--accent),var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
.nav-logo span{-webkit-text-fill-color:var(--accent3);}
.nav-links{display:flex;gap:2rem;list-style:none;}
.nav-links a{color:var(--muted);text-decoration:none;font-size:0.9rem;font-weight:500;transition:color 0.2s;}
.nav-links a:hover{color:var(--text);}
.nav-cta{display:flex;gap:0.75rem;align-items:center;}
.btn-ghost{padding:0.5rem 1.2rem;border:1px solid var(--border);background:transparent;color:var(--text);border-radius:8px;font-family:'DM Sans',sans-serif;font-size:0.875rem;transition:all 0.2s;text-decoration:none;cursor:pointer;}
.btn-ghost:hover{border-color:var(--accent);color:var(--accent);}
.btn-primary{padding:0.5rem 1.4rem;background:linear-gradient(135deg,var(--accent),var(--accent2));border:none;border-radius:8px;color:#050810;font-weight:700;font-family:'DM Sans',sans-serif;font-size:0.875rem;transition:all 0.3s;text-decoration:none;display:inline-block;cursor:pointer;}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(110,231,183,0.3);}
.ticker-wrap{overflow:hidden;white-space:nowrap;background:rgba(110,231,183,0.05);border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:0.85rem 0;margin-top:68px;}
.ticker-content{display:inline-block;animation:ticker 30s linear infinite;}
.ticker-content span{display:inline-block;padding:0 3rem;color:var(--muted);font-size:0.82rem;letter-spacing:0.08em;}
.ticker-content .sep{color:var(--accent);}
@keyframes ticker{from{transform:translateX(0)}to{transform:translateX(-50%)}}
.hero{min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:8rem 2rem 4rem;position:relative;overflow:hidden;}
.orb{position:absolute;border-radius:50%;filter:blur(80px);pointer-events:none;animation:floatOrb 8s ease-in-out infinite;}
.orb-1{width:600px;height:600px;background:radial-gradient(circle,rgba(110,231,183,0.12),transparent 70%);top:-100px;left:-100px;}
.orb-2{width:500px;height:500px;background:radial-gradient(circle,rgba(129,140,248,0.1),transparent 70%);bottom:-50px;right:-50px;animation-delay:-3s;}
.orb-3{width:300px;height:300px;background:radial-gradient(circle,rgba(244,114,182,0.08),transparent 70%);top:50%;left:50%;transform:translate(-50%,-50%);animation-delay:-6s;}
@keyframes floatOrb{0%,100%{transform:translate(0,0) scale(1)}33%{transform:translate(30px,-30px) scale(1.05)}66%{transform:translate(-20px,20px) scale(0.95)}}
.grid-bg{position:absolute;inset:0;pointer-events:none;background-image:linear-gradient(rgba(255,255,255,0.02) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.02) 1px,transparent 1px);background-size:60px 60px;mask-image:radial-gradient(ellipse at center,black 30%,transparent 80%);}
.hero-badge{display:inline-flex;align-items:center;gap:0.5rem;background:rgba(110,231,183,0.1);border:1px solid rgba(110,231,183,0.2);border-radius:100px;padding:0.35rem 1rem;margin-bottom:2rem;font-size:0.8rem;color:var(--accent);letter-spacing:0.08em;text-transform:uppercase;font-weight:600;animation:fadeUp 0.8s 0.2s both;}
.badge-dot{width:6px;height:6px;border-radius:50%;background:var(--accent);animation:pulse 1.5s infinite;}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:0.5;transform:scale(1.5)}}
.hero h1{font-family:'Syne',sans-serif;font-size:clamp(3rem,8vw,7rem);font-weight:800;line-height:1.0;letter-spacing:-0.04em;animation:fadeUp 0.8s 0.4s both;}
.line-accent{background:linear-gradient(135deg,var(--accent) 0%,var(--accent2) 50%,var(--accent3) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-size:200% auto;animation:gradShift 4s linear infinite;}
@keyframes gradShift{to{background-position:200% center}}
.hero-sub{margin-top:1.5rem;max-width:560px;color:var(--muted);font-size:1.15rem;line-height:1.7;animation:fadeUp 0.8s 0.6s both;}
.hero-cta{margin-top:2.5rem;display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;animation:fadeUp 0.8s 0.8s both;}
.btn-large{padding:0.9rem 2.2rem;font-size:1rem;border-radius:12px;font-weight:600;}
.btn-outline-large{padding:0.9rem 2.2rem;font-size:1rem;border:1px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.04);border-radius:12px;color:var(--text);text-decoration:none;display:inline-block;transition:all 0.3s;font-weight:500;}
.btn-outline-large:hover{border-color:var(--accent2);color:var(--accent2);transform:translateY(-2px);}
@keyframes fadeUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
.stats-bar{display:flex;justify-content:center;margin-top:5rem;animation:fadeUp 0.8s 1s both;position:relative;z-index:1;}
.stat-item{text-align:center;padding:1.5rem 3rem;border:1px solid var(--border);background:rgba(255,255,255,0.02);}
.stat-item:first-child{border-radius:16px 0 0 16px;}
.stat-item:last-child{border-radius:0 16px 16px 0;}
.stat-item:not(:last-child){border-right:none;}
.stat-num{font-family:'Syne',sans-serif;font-size:2.2rem;font-weight:800;color:var(--accent);display:block;}
.stat-label{font-size:0.8rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.1em;}
.scroll-hint{position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:0.5rem;color:var(--muted);font-size:0.75rem;animation:fadeUp 1s 1.2s both;}
.scroll-line{width:1px;height:40px;background:linear-gradient(to bottom,var(--accent),transparent);animation:sp 1.5s ease-in-out infinite;}
@keyframes sp{0%,100%{opacity:0.3}50%{opacity:1}}
section{padding:6rem 2rem;max-width:1200px;margin:0 auto;}
.section-eyebrow{font-size:0.75rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--accent);font-weight:600;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.5rem;}
.section-eyebrow::before{content:'';display:block;width:24px;height:1px;background:var(--accent);}
.section-title{font-family:'Syne',sans-serif;font-size:clamp(2rem,4vw,3.5rem);font-weight:800;letter-spacing:-0.03em;line-height:1.1;}
.events-header{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2.5rem;flex-wrap:wrap;gap:1rem;}
.events-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1.5rem;}
.event-card{background:var(--card);border:1px solid var(--border);border-radius:20px;overflow:hidden;transition:all 0.4s cubic-bezier(0.23,1,0.32,1);}
.event-card:hover{transform:translateY(-8px);border-color:rgba(110,231,183,0.2);box-shadow:0 20px 60px rgba(0,0,0,0.4);}
.event-banner{height:140px;position:relative;display:flex;align-items:center;justify-content:center;}
.event-banner-emoji{font-size:3.5rem;opacity:0.25;}
.event-cat-tag{position:absolute;top:1rem;left:1rem;background:rgba(0,0,0,0.6);backdrop-filter:blur(8px);border:1px solid var(--border);border-radius:100px;padding:0.25rem 0.75rem;font-size:0.72rem;font-weight:600;text-transform:uppercase;}
.ev-slots{position:absolute;top:1rem;right:1rem;background:rgba(110,231,183,0.15);border:1px solid rgba(110,231,183,0.3);color:var(--accent);border-radius:100px;padding:0.25rem 0.75rem;font-size:0.72rem;font-weight:600;}
.banner-0{background:linear-gradient(135deg,#0a1628,#1a2a4a);}
.banner-1{background:linear-gradient(135deg,#0a2010,#1a4020);}
.banner-2{background:linear-gradient(135deg,#2a0a1a,#4a1a2a);}
.banner-3{background:linear-gradient(135deg,#1a1a0a,#3a3a0a);}
.banner-4{background:linear-gradient(135deg,#1a0a2a,#3a0a4a);}
.event-body{padding:1.5rem;}
.event-title{font-family:'Syne',sans-serif;font-size:1.05rem;font-weight:700;margin-bottom:0.75rem;line-height:1.3;}
.event-meta{display:flex;flex-wrap:wrap;gap:0.6rem;margin-bottom:1.2rem;}
.event-meta span{color:var(--muted);font-size:0.8rem;}
.event-footer{display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1px solid var(--border);}
.event-price{font-family:'Syne',sans-serif;font-weight:700;font-size:1.1rem;color:var(--accent);}
.btn-reg{padding:0.5rem 1.2rem;background:linear-gradient(135deg,var(--accent),var(--accent2));border:none;border-radius:8px;color:#050810;font-weight:700;font-size:0.8rem;text-decoration:none;transition:all 0.3s;display:inline-block;cursor:pointer;}
.btn-reg:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(110,231,183,0.25);}
.no-events{text-align:center;padding:4rem;color:var(--muted);border:1px dashed var(--border);border-radius:20px;}
.features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.5px;margin-top:3rem;background:var(--border);border-radius:20px;overflow:hidden;}
.feat-card{background:var(--card);padding:2.5rem;transition:all 0.3s;}
.feat-card:hover{background:#151f30;}
.feat-icon{font-size:1.8rem;margin-bottom:1.2rem;}
.feat-title{font-family:'Syne',sans-serif;font-size:1rem;font-weight:700;margin-bottom:0.5rem;}
.feat-desc{color:var(--muted);font-size:0.88rem;line-height:1.7;}
.cats-section{padding:4rem 2rem;background:rgba(255,255,255,0.01);}
.cats-wrap{max-width:1200px;margin:0 auto;}
.cats-grid{display:flex;flex-wrap:wrap;gap:1rem;margin-top:2.5rem;}
.cat-chip{display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1.5rem;background:var(--card);border:1px solid var(--border);border-radius:100px;text-decoration:none;color:var(--text);font-size:0.9rem;transition:all 0.3s;}
.cat-chip:hover{border-color:var(--accent);color:var(--accent);transform:translateY(-2px);}
.steps-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:2rem;margin-top:3rem;position:relative;}
.step-card{text-align:center;padding:2rem 1.5rem;}
.step-num{width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;color:#050810;margin:0 auto 1.5rem;box-shadow:0 0 30px rgba(110,231,183,0.2);}
.step-title{font-family:'Syne',sans-serif;font-weight:700;margin-bottom:0.5rem;}
.step-desc{color:var(--muted);font-size:0.875rem;line-height:1.7;}
.cta-banner{margin:2rem auto 6rem;max-width:1200px;padding:0 2rem;}
.cta-inner{background:linear-gradient(135deg,rgba(110,231,183,0.08),rgba(129,140,248,0.08));border:1px solid rgba(110,231,183,0.15);border-radius:24px;padding:4rem;display:flex;align-items:center;justify-content:space-between;gap:2rem;flex-wrap:wrap;}
.cta-text h2{font-family:'Syne',sans-serif;font-size:2.2rem;font-weight:800;}
.cta-text p{color:var(--muted);margin-top:0.5rem;}
footer{background:var(--surface);border-top:1px solid var(--border);padding:3rem 4rem;}
.footer-inner{max-width:1200px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1.5rem;}
.footer-links{display:flex;gap:2rem;list-style:none;}
.footer-links a{color:var(--muted);text-decoration:none;font-size:0.875rem;transition:color 0.2s;}
.footer-links a:hover{color:var(--text);}
.footer-copy{color:var(--muted);font-size:0.8rem;}
.reveal{opacity:0;transform:translateY(40px);transition:all 0.7s cubic-bezier(0.23,1,0.32,1);}
.reveal.visible{opacity:1;transform:translateY(0);}
@media(max-width:768px){nav{padding:1rem 1.5rem;}.nav-links{display:none;}.stats-bar{flex-direction:column;}.events-grid{grid-template-columns:1fr;}.cta-inner{padding:2rem;}footer{padding:2rem 1.5rem;}}
</style></head><body>

<nav>
  <div class="nav-logo">Campus<span>Verse</span></div>
  <ul class="nav-links"><li><a href="#events">Events</a></li><li><a href="#features">Features</a></li><li><a href="#how-it-works">How It Works</a></li><li><a href="#categories">Categories</a></li></ul>
  <div class="nav-cta">
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="<?=BASE_URL?>/user/dashboard.php" class="btn-ghost">Dashboard</a>
      <a href="<?=BASE_URL?>/logout.php" class="btn-primary">Log Out</a>
    <?php else: ?>
      <a href="<?=BASE_URL?>/login.php" class="btn-ghost">Sign In</a>
      <a href="<?=BASE_URL?>/register.php" class="btn-primary">Join Free →</a>
    <?php endif; ?>
  </div>
</nav>

<div class="ticker-wrap"><div class="ticker-content">
  <span>🎓 Annual Tech Symposium</span><span class="sep">✦</span><span>🎨 Cultural Fest Open</span><span class="sep">✦</span><span>⚽ Sports Meet</span><span class="sep">✦</span><span>🎵 Battle of Bands</span><span class="sep">✦</span><span>🔬 Research Competition</span><span class="sep">✦</span><span>💡 Startup Pitch Challenge</span><span class="sep">✦</span>
  <span>🎓 Annual Tech Symposium</span><span class="sep">✦</span><span>🎨 Cultural Fest Open</span><span class="sep">✦</span><span>⚽ Sports Meet</span><span class="sep">✦</span><span>🎵 Battle of Bands</span><span class="sep">✦</span><span>🔬 Research Competition</span><span class="sep">✦</span><span>💡 Startup Pitch Challenge</span><span class="sep">✦</span>
</div></div>

<div class="hero">
  <div class="orb orb-1"></div><div class="orb orb-2"></div><div class="orb orb-3"></div><div class="grid-bg"></div>
  <div class="hero-badge"><span class="badge-dot"></span>Live Campus Platform</div>
  <h1>Your Campus,<br><span class="line-accent">One Portal.</span></h1>
  <p class="hero-sub">Discover, register, and experience every event your campus has to offer. From tech fests to cultural shows — all in one place.</p>
  <div class="hero-cta">
    <a href="<?=BASE_URL?>/register.php" class="btn-primary btn-large">Get Started Free</a>
    <a href="#events" class="btn-outline-large">Browse Events ↓</a>
  </div>
  <div class="stats-bar">
    <div class="stat-item"><span class="stat-num counter" data-target="<?=$totalEvents?>">0</span><span class="stat-label">Total Events</span></div>
    <div class="stat-item"><span class="stat-num counter" data-target="<?=$totalUsers?>">0</span><span class="stat-label">Students</span></div>
    <div class="stat-item"><span class="stat-num counter" data-target="<?=$totalRegs?>">0</span><span class="stat-label">Registrations</span></div>
  </div>
  <div class="scroll-hint"><span>SCROLL</span><div class="scroll-line"></div></div>
</div>

<section id="events">
  <div class="events-header reveal">
    <div><div class="section-eyebrow">Happening Soon</div><h2 class="section-title">Upcoming Events</h2></div>
    <a href="<?=BASE_URL?>/<?=isset($_SESSION['user_id'])?'user/register_event.php':'login.php'?>" class="btn-ghost">View All →</a>
  </div>
  <?php if(empty($events)): ?>
  <div class="no-events reveal"><div style="font-size:3rem;margin-bottom:1rem">🎯</div><h3 style="font-family:'Syne',sans-serif;margin-bottom:.5rem">No Upcoming Events Yet</h3><p>Check back soon!</p></div>
  <?php else: ?>
  <div class="events-grid">
  <?php $emojis=['Technical'=>'💻','Sports'=>'⚽','Cultural'=>'🎨','Academic'=>'📚','Social'=>'🎉','Workshop'=>'🛠️'];
  foreach($events as $i=>$ev): $cat=$ev['category']??'General'; $emoji=$emojis[$cat]??'📅'; ?>
    <div class="event-card" style="animation:fadeUp 0.6s <?=($i*0.1)?>s both;">
      <div class="event-banner banner-<?=$i%5?>"><div class="event-banner-emoji"><?=$emoji?></div><span class="event-cat-tag"><?=htmlspecialchars($cat)?></span><span class="ev-slots">Open</span></div>
      <div class="event-body">
        <h3 class="event-title"><?=htmlspecialchars($ev['title'])?></h3>
        <div class="event-meta"><span>📅 <?=date('D, M j',strtotime($ev['event_date']))?></span><span>🕐 <?=date('g:i A',strtotime($ev['event_date']))?></span><?php if(!empty($ev['location'])): ?><span>📍 <?=htmlspecialchars($ev['location'])?></span><?php endif; ?></div>
        <div class="event-footer">
          <span class="event-price"><?=(!$ev['price']||$ev['price']==0)?'Free':'₹'.number_format($ev['price'])?></span>
          <a href="<?=isset($_SESSION['user_id'])?BASE_URL.'/user/register_event.php?id='.$ev['id'].'&action=register':BASE_URL.'/login.php'?>" class="btn-reg">Register Now</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<section id="features" style="padding:6rem 2rem;max-width:1200px;margin:0 auto;">
  <div class="reveal"><div class="section-eyebrow">Why CampusVerse</div><h2 class="section-title">Everything You Need</h2></div>
  <div class="features-grid reveal">
    <div class="feat-card"><div class="feat-icon">🔐</div><h3 class="feat-title">Secure Authentication</h3><p class="feat-desc">Bcrypt passwords, CSRF tokens, session guards and rate limiting on every form.</p></div>
    <div class="feat-card"><div class="feat-icon">⚡</div><h3 class="feat-title">Instant Registration</h3><p class="feat-desc">Register for events in seconds with real-time seat tracking.</p></div>
    <div class="feat-card"><div class="feat-icon">📊</div><h3 class="feat-title">Admin Analytics</h3><p class="feat-desc">Full dashboard — registrations, event management, live stats.</p></div>
    <div class="feat-card"><div class="feat-icon">🎯</div><h3 class="feat-title">Smart Discovery</h3><p class="feat-desc">Filter events by category, date, or availability instantly.</p></div>
    <div class="feat-card"><div class="feat-icon">📱</div><h3 class="feat-title">Mobile-First</h3><p class="feat-desc">Perfect on phones, tablets and desktop screens.</p></div>
    <div class="feat-card"><div class="feat-icon">🔔</div><h3 class="feat-title">Real-Time Slots</h3><p class="feat-desc">Live availability. No overbooking — ever.</p></div>
  </div>
</section>

<div class="cats-section" id="categories"><div class="cats-wrap">
  <div class="reveal"><div class="section-eyebrow">Browse By</div><h2 class="section-title">Event Categories</h2></div>
  <div class="cats-grid reveal">
    <a href="<?=BASE_URL?>/login.php" class="cat-chip"><span>💻</span><span>Technical</span></a>
    <a href="<?=BASE_URL?>/login.php" class="cat-chip"><span>⚽</span><span>Sports</span></a>
    <a href="<?=BASE_URL?>/login.php" class="cat-chip"><span>🎨</span><span>Cultural</span></a>
    <a href="<?=BASE_URL?>/login.php" class="cat-chip"><span>📚</span><span>Academic</span></a>
    <a href="<?=BASE_URL?>/login.php" class="cat-chip"><span>🎉</span><span>Social</span></a>
    <a href="<?=BASE_URL?>/login.php" class="cat-chip"><span>🛠️</span><span>Workshop</span></a>
  </div>
</div></div>

<section id="how-it-works">
  <div class="reveal" style="text-align:center"><div class="section-eyebrow" style="justify-content:center">Simple Process</div><h2 class="section-title">How It Works</h2></div>
  <div class="steps-row reveal">
    <div class="step-card"><div class="step-num">1</div><h3 class="step-title">Create Account</h3><p class="step-desc">Sign up with your college email in under a minute.</p></div>
    <div class="step-card"><div class="step-num">2</div><h3 class="step-title">Discover Events</h3><p class="step-desc">Browse all upcoming events, filter by interest.</p></div>
    <div class="step-card"><div class="step-num">3</div><h3 class="step-title">Register Instantly</h3><p class="step-desc">One click to register. Spot confirmed immediately.</p></div>
    <div class="step-card"><div class="step-num">4</div><h3 class="step-title">Attend & Enjoy</h3><p class="step-desc">Show up, participate, and make memories.</p></div>
  </div>
</section>

<div class="cta-banner"><div class="cta-inner reveal">
  <div class="cta-text"><h2>Ready to Join Your Campus?</h2><p>Register now — completely free for students.</p></div>
  <div style="display:flex;gap:1rem;flex-wrap:wrap">
    <a href="<?=BASE_URL?>/register.php" class="btn-primary btn-large">Create Free Account</a>
    <a href="<?=BASE_URL?>/login.php" class="btn-ghost" style="padding:.9rem 2rem">Sign In →</a>
  </div>
</div></div>

<footer><div class="footer-inner">
  <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.3rem;background:linear-gradient(135deg,var(--accent),var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent">CampusVerse</div>
  <ul class="footer-links"><li><a href="#">About</a></li><li><a href="#">Privacy</a></li><li><a href="#">Terms</a></li><li><a href="#">Contact</a></li></ul>
  <p class="footer-copy">© <?=date('Y')?> CampusVerse. All rights reserved.</p>
</div></footer>

<script>
const rev=new IntersectionObserver(e=>{e.forEach(x=>{if(x.isIntersecting){x.target.classList.add('visible');rev.unobserve(x.target);}});},{threshold:0.1});
document.querySelectorAll('.reveal').forEach(el=>rev.observe(el));
const cobs=new IntersectionObserver(e=>{e.forEach(x=>{if(!x.isIntersecting)return;const el=x.target,t=+el.dataset.target||0,d=1400,s=performance.now();const tick=n=>{const p=Math.min((n-s)/d,1),q=1-Math.pow(1-p,3);el.textContent=Math.floor(q*t);if(p<1)requestAnimationFrame(tick);else el.textContent=t;};requestAnimationFrame(tick);cobs.unobserve(el);});},{threshold:0.5});
document.querySelectorAll('.counter').forEach(el=>cobs.observe(el));
document.addEventListener('mousemove',e=>{const x=(e.clientX/window.innerWidth-.5)*30,y=(e.clientY/window.innerHeight-.5)*30;document.querySelectorAll('.orb').forEach((o,i)=>{const d=(i+1)*.4;o.style.transform=`translate(${x*d}px,${y*d}px)`;});});
const nav=document.querySelector('nav');
window.addEventListener('scroll',()=>{nav.style.background=window.scrollY>50?'rgba(5,8,16,0.97)':'rgba(5,8,16,0.85)';});
</script></body></html>