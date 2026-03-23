<?php
session_start();
require_once '../config/base.php';
if(!isset($_SESSION['user_id'])){header('Location: '.BASE_URL.'/login.php');exit;}
require_once '../config/db.php';

// Suppress warnings - use error_reporting for production
error_reporting(0);

$uid=$_SESSION['user_id'];
$uname=$_SESSION['user_name']??'Student';

$uq=$pdo->prepare("SELECT * FROM users WHERE id=?");
$uq->execute([$uid]);
$u=$uq->fetch(PDO::FETCH_ASSOC);

$rq=$pdo->prepare("
  SELECT e.id, e.title, e.event_date, e.location, e.price, e.category,
         r.registered_at, r.id AS rid
  FROM registrations r
  JOIN events e ON r.event_id = e.id
  WHERE r.user_id = ?
  ORDER BY e.event_date ASC
");
$rq->execute([$uid]);
$myEv=$rq->fetchAll(PDO::FETCH_ASSOC);

$aq=$pdo->prepare("
  SELECT e.id, e.title, e.event_date, e.location, e.price, e.category,
         e.max_participants,
         (SELECT COUNT(*) FROM registrations r2 WHERE r2.event_id = e.id) AS reg_count
  FROM events e
  WHERE e.event_date >= NOW()
  AND e.is_active = 1
  AND e.id NOT IN (SELECT event_id FROM registrations WHERE user_id = ?)
  ORDER BY e.event_date ASC
  LIMIT 6
");
$aq->execute([$uid]);
$avail=$aq->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Dashboard — CampusVerse</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<style>
:root{
  --bg:#060B18;
  --surface:#0C1425;
  --card:#111C33;
  --card2:#162040;
  --border:rgba(255,255,255,0.07);
  --accent:#5EEAD4;
  --accent2:#818CF8;
  --accent3:#F472B6;
  --text:#F0F4FF;
  --text2:#B8C4E0;
  --muted:#64748B;
  --error:#F87171;
  --green:#34D399;
  --yellow:#FBBF24;
}
*{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{
  background:var(--bg);
  color:var(--text);
  font-family:'Plus Jakarta Sans',sans-serif;
  min-height:100vh;
  font-size:15px;
  line-height:1.6;
}

/* ── SIDEBAR ── */
.layout{display:flex;min-height:100vh;}
.sidebar{
  width:240px;flex-shrink:0;
  background:var(--surface);
  border-right:1px solid var(--border);
  display:flex;flex-direction:column;
  padding:1.75rem 1.25rem;
  position:fixed;height:100vh;overflow-y:auto;
}
.slogo{
  font-size:1.35rem;font-weight:800;letter-spacing:-0.02em;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  margin-bottom:2rem;display:block;
}
.nav-label{
  font-size:0.68rem;font-weight:700;letter-spacing:0.1em;
  text-transform:uppercase;color:var(--muted);
  margin:0.5rem 0 0.5rem 0.75rem;
}
.ni{
  display:flex;align-items:center;gap:0.65rem;
  padding:0.65rem 0.9rem;border-radius:10px;
  text-decoration:none;color:var(--text2);
  font-size:0.875rem;font-weight:500;
  margin-bottom:2px;transition:all 0.2s;
}
.ni:hover{background:rgba(94,234,212,0.08);color:var(--accent);}
.ni.active{background:rgba(94,234,212,0.12);color:var(--accent);font-weight:600;}
.ni .icon{width:20px;text-align:center;font-size:1rem;}

.su{
  margin-top:auto;padding-top:1.25rem;
  border-top:1px solid var(--border);
}
.urow{display:flex;align-items:center;gap:0.75rem;margin-bottom:0.875rem;}
.av{
  width:38px;height:38px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  display:flex;align-items:center;justify-content:center;
  font-weight:800;color:#060B18;font-size:0.95rem;
}
.uname{font-weight:700;font-size:0.875rem;color:var(--text);}
.udept{font-size:0.72rem;color:var(--muted);margin-top:1px;}
.btn-lo{
  display:block;padding:0.55rem;text-align:center;
  border-radius:8px;border:1px solid var(--border);
  color:var(--muted);text-decoration:none;font-size:0.8rem;
  font-weight:500;transition:all 0.2s;
}
.btn-lo:hover{border-color:var(--error);color:var(--error);}

/* ── MAIN ── */
.main{
  margin-left:240px;flex:1;
  padding:2.25rem 2.5rem;
  max-width:1100px;
}

/* Page header */
.pg-head{margin-bottom:2rem;}
.pg-head h1{
  font-size:1.75rem;font-weight:800;letter-spacing:-0.02em;
  color:var(--text);
}
.pg-head p{color:var(--text2);font-size:0.9rem;margin-top:0.3rem;}

/* Stats */
.stats{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:1rem;margin-bottom:2.25rem;
}
.sc{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:14px;padding:1.4rem 1.5rem;
  transition:all 0.3s;position:relative;overflow:hidden;
}
.sc::before{
  content:'';position:absolute;top:-20px;right:-20px;
  width:80px;height:80px;border-radius:50%;opacity:0.06;
}
.sc:nth-child(1)::before{background:var(--accent);}
.sc:nth-child(2)::before{background:var(--accent2);}
.sc:nth-child(3)::before{background:var(--accent3);}
.sc:nth-child(4)::before{background:var(--yellow);}
.sc:hover{transform:translateY(-3px);border-color:rgba(94,234,212,0.2);}
.sc-icon{font-size:1.35rem;margin-bottom:0.875rem;}
.sc-val{
  font-size:2rem;font-weight:800;letter-spacing:-0.03em;
  line-height:1;margin-bottom:0.3rem;
}
.sc:nth-child(1) .sc-val{color:var(--accent);}
.sc:nth-child(2) .sc-val{color:var(--accent2);}
.sc:nth-child(3) .sc-val{color:var(--accent3);}
.sc:nth-child(4) .sc-val{color:var(--yellow);}
.sc-lbl{font-size:0.78rem;font-weight:600;color:var(--muted);letter-spacing:0.03em;}

/* Section headers */
.sec-head{
  display:flex;justify-content:space-between;align-items:center;
  margin-bottom:1.1rem;
}
.sec-head h2{font-size:1.05rem;font-weight:700;color:var(--text);}
.sec-head a{
  font-size:0.8rem;font-weight:600;color:var(--accent);
  text-decoration:none;opacity:0.85;transition:opacity 0.2s;
}
.sec-head a:hover{opacity:1;}

/* Event grid */
.eg{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
  gap:1rem;margin-bottom:2.25rem;
}

/* Event card */
.ec{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:16px;overflow:hidden;
  transition:all 0.3s;
  display:flex;flex-direction:column;
}
.ec:hover{
  transform:translateY(-5px);
  border-color:rgba(94,234,212,0.25);
  box-shadow:0 16px 40px rgba(0,0,0,0.35);
}

/* Banner */
.eb{
  height:90px;
  display:flex;align-items:center;justify-content:center;
  font-size:2.2rem;position:relative;
  flex-shrink:0;
}
.b0{background:linear-gradient(135deg,#0D1F3C,#162D55);}
.b1{background:linear-gradient(135deg,#0B2210,#123318);}
.b2{background:linear-gradient(135deg,#2D0A1A,#45102A);}
.b3{background:linear-gradient(135deg,#1C1A08,#302D0C);}
.b4{background:linear-gradient(135deg,#180B2D,#261244);}

.ec-tag{
  position:absolute;top:0.6rem;left:0.6rem;
  background:rgba(0,0,0,0.55);backdrop-filter:blur(6px);
  border:1px solid rgba(255,255,255,0.1);
  border-radius:6px;padding:0.18rem 0.55rem;
  font-size:0.65rem;font-weight:700;
  letter-spacing:0.06em;text-transform:uppercase;
  color:var(--text2);
}
.ec-badge{
  position:absolute;top:0.6rem;right:0.6rem;
  border-radius:6px;padding:0.18rem 0.55rem;
  font-size:0.65rem;font-weight:700;
}
.badge-reg{background:rgba(94,234,212,0.15);border:1px solid rgba(94,234,212,0.3);color:var(--accent);}
.badge-open{background:rgba(129,140,248,0.15);border:1px solid rgba(129,140,248,0.3);color:var(--accent2);}

/* Card body */
.ed{padding:1.1rem 1.25rem 1.25rem;flex:1;display:flex;flex-direction:column;}
.et{
  font-size:0.95rem;font-weight:700;
  color:var(--text);margin-bottom:0.6rem;
  line-height:1.35;
}
.em{
  display:flex;flex-direction:column;gap:0.28rem;
  margin-bottom:0.875rem;flex:1;
}
.em span{
  font-size:0.78rem;color:var(--text2);
  display:flex;align-items:center;gap:0.4rem;
}
.ef{
  display:flex;justify-content:space-between;align-items:center;
  padding-top:0.875rem;
  border-top:1px solid var(--border);
}
.ep{font-size:1rem;font-weight:800;letter-spacing:-0.01em;}
.epf{color:var(--green);}
.epp{color:var(--yellow);}

/* Buttons */
.btn-r{
  padding:0.42rem 1rem;border-radius:8px;
  font-size:0.78rem;font-weight:700;
  text-decoration:none;border:none;cursor:pointer;
  display:inline-block;transition:all 0.2s;
  font-family:'Plus Jakarta Sans',sans-serif;
  letter-spacing:0.01em;
}
.btn-reg{
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  color:#060B18;
}
.btn-reg:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(94,234,212,0.25);}
.btn-cx{
  background:rgba(248,113,113,0.1);
  border:1px solid rgba(248,113,113,0.2);
  color:var(--error);
}
.btn-cx:hover{background:rgba(248,113,113,0.2);}
.btn-confirmed{
  background:rgba(94,234,212,0.1);
  border:1px solid rgba(94,234,212,0.25);
  color:var(--accent);cursor:default;
}

/* Empty state */
.empty{
  text-align:center;padding:2.5rem 1.5rem;
  color:var(--muted);
  border:1px dashed rgba(255,255,255,0.08);
  border-radius:14px;margin-bottom:2.25rem;
  background:rgba(255,255,255,0.01);
}
.empty .eico{font-size:2.5rem;margin-bottom:0.75rem;}
.empty h3{font-size:1rem;font-weight:700;color:var(--text2);margin-bottom:0.35rem;}
.empty p{font-size:0.83rem;}

/* Alert */
.alert{
  padding:0.875rem 1rem;border-radius:10px;
  margin-bottom:1.5rem;font-size:0.875rem;font-weight:500;
}
.alert-s{background:rgba(94,234,212,0.08);border:1px solid rgba(94,234,212,0.2);color:var(--accent);}

@media(max-width:900px){
  .sidebar{display:none;}
  .main{margin-left:0;padding:1.5rem;}
  .stats{grid-template-columns:1fr 1fr;}
}
@media(max-width:520px){
  .stats{grid-template-columns:1fr 1fr;}
  .eg{grid-template-columns:1fr;}
}
</style>
</head>
<body>
<div class="layout">

<!-- SIDEBAR -->
<aside class="sidebar">
  <span class="slogo">CampusVerse</span>

  <div class="nav-label">Menu</div>
  <a href="<?=BASE_URL?>/user/dashboard.php" class="ni active">
    <span class="icon">🏠</span> Dashboard
  </a>
  <a href="<?=BASE_URL?>/user/my_events.php" class="ni">
    <span class="icon">🎫</span> My Registrations
  </a>
  <a href="<?=BASE_URL?>/user/register_event.php" class="ni">
    <span class="icon">🔍</span> Browse Events
  </a>

  <div class="su">
    <div class="urow">
      <div class="av"><?=strtoupper(substr($uname,0,1))?></div>
      <div>
        <div class="uname"><?=htmlspecialchars($uname)?></div>
        <div class="udept"><?=htmlspecialchars($u['department']??'Student')?></div>
      </div>
    </div>
    <a href="<?=BASE_URL?>/logout.php" class="btn-lo">Sign Out</a>
  </div>
</aside>

<!-- MAIN -->
<main class="main">

  <!-- Header -->
  <div class="pg-head">
    <h1>Good <?=date('H')<12?'Morning':(date('H')<17?'Afternoon':'Evening')?>, <?=htmlspecialchars(explode(' ',$uname)[0])?>! 👋</h1>
    <p>Here's what's happening on your campus today — <?=date('D, M j, Y')?></p>
  </div>

  <!-- Stats -->
  <div class="stats">
    <div class="sc">
      <div class="sc-icon">🎫</div>
      <div class="sc-val"><?=count($myEv)?></div>
      <div class="sc-lbl">Events Registered</div>
    </div>
    <div class="sc">
      <div class="sc-icon">📅</div>
      <div class="sc-val"><?=count($avail)?></div>
      <div class="sc-lbl">Events Available</div>
    </div>
    <div class="sc">
      <div class="sc-icon">🎓</div>
      <div class="sc-val">Year <?=intval($u['year']??1)?></div>
      <div class="sc-lbl">Year of Study</div>
    </div>
    <div class="sc">
      <div class="sc-icon">⭐</div>
      <div class="sc-val"><?=count($myEv)>5?'Pro':'New'?></div>
      <div class="sc-lbl">Member Status</div>
    </div>
  </div>

  <!-- My Registered Events -->
  <div class="sec-head">
    <h2>My Registered Events</h2>
    <a href="<?=BASE_URL?>/user/my_events.php">View All →</a>
  </div>

  <?php if(empty($myEv)): ?>
  <div class="empty">
    <div class="eico">🎯</div>
    <h3>No Registrations Yet</h3>
    <p>Browse the events below and grab your spot!</p>
  </div>
  <?php else: ?>
  <div class="eg">
    <?php
    $emojis=['💻','⚽','🎨','📚','🎉','🛠️'];
    foreach(array_slice($myEv,0,3) as $i=>$ev):
    ?>
    <div class="ec">
      <div class="eb b<?=$i%5?>">
        <?=$emojis[$i%6]?>
        <span class="ec-tag"><?=htmlspecialchars($ev['category']??'Event')?></span>
        <span class="ec-badge badge-reg">✓ Registered</span>
      </div>
      <div class="ed">
        <div class="et"><?=htmlspecialchars($ev['title'])?></div>
        <div class="em">
          <span>📅 <?=date('D, M j, Y',strtotime($ev['event_date']))?></span>
          <span>🕐 <?=date('g:i A',strtotime($ev['event_date']))?></span>
          <span>📍 <?=htmlspecialchars($ev['location']??'TBA')?></span>
        </div>
        <div class="ef">
          <span class="ep <?=($ev['price']==0)?'epf':'epp'?>"><?=(!$ev['price']||$ev['price']==0)?'Free':'₹'.number_format($ev['price'])?></span>
          <a href="<?=BASE_URL?>/user/my_events.php?cancel=<?=intval($ev['id'])?>"
             class="btn-r btn-cx"
             onclick="return confirm('Cancel your registration for this event?')">Cancel</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Available Events -->
  <div class="sec-head">
    <h2>Available Events</h2>
    <a href="<?=BASE_URL?>/user/register_event.php">Browse All →</a>
  </div>

  <?php if(empty($avail)): ?>
  <div class="empty">
    <div class="eico">🎊</div>
    <h3>All Caught Up!</h3>
    <p>No new events available right now. Check back soon!</p>
  </div>
  <?php else: ?>
  <div class="eg">
    <?php foreach($avail as $i=>$ev):
      $slots = intval($ev['max_participants']) - intval($ev['reg_count']);
    ?>
    <div class="ec">
      <div class="eb b<?=$i%5?>">
        <?=$emojis[$i%6]?>
        <span class="ec-tag"><?=htmlspecialchars($ev['category']??'Event')?></span>
        <span class="ec-badge badge-open"><?=$slots>0?$slots.' slots':'Full'?></span>
      </div>
      <div class="ed">
        <div class="et"><?=htmlspecialchars($ev['title'])?></div>
        <div class="em">
          <span>📅 <?=date('D, M j, Y',strtotime($ev['event_date']))?></span>
          <span>🕐 <?=date('g:i A',strtotime($ev['event_date']))?></span>
          <span>📍 <?=htmlspecialchars($ev['location']??'TBA')?></span>
        </div>
        <div class="ef">
          <span class="ep <?=($ev['price']==0)?'epf':'epp'?>"><?=(!$ev['price']||$ev['price']==0)?'Free':'₹'.number_format($ev['price'])?></span>
          <?php if($slots>0): ?>
          <a href="<?=BASE_URL?>/user/register_event.php?id=<?=intval($ev['id'])?>&action=register"
             class="btn-r btn-reg">Register →</a>
          <?php else: ?>
          <span class="btn-r" style="background:rgba(255,255,255,0.05);color:var(--muted);cursor:not-allowed;">Full</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</main>
</div>
</body>
</html>