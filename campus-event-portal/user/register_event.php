<?php
session_start();
require_once '../config/base.php';
if(!isset($_SESSION['user_id'])){header('Location: '.BASE_URL.'/login.php');exit;}
require_once '../config/db.php';
$uid=$_SESSION['user_id'];$msg='';$error='';
$eid=intval($_GET['id']??0);
if($eid&&($_GET['action']??'')==='register'){
  $c=$pdo->prepare("SELECT id FROM registrations WHERE user_id=? AND event_id=?");$c->execute([$uid,$eid]);
  if($c->fetch()){$error='Already registered for this event.';}
  else{
    $ev=$pdo->prepare("SELECT max_participants,(SELECT COUNT(*) FROM registrations WHERE event_id=e.id) AS rc FROM events e WHERE id=?");$ev->execute([$eid]);$evd=$ev->fetch();
    if($evd&&$evd['rc']>=$evd['max_participants']){$error='This event is fully booked.';}
    else{$pdo->prepare("INSERT INTO registrations(user_id,event_id)VALUES(?,?)")->execute([$uid,$eid]);$msg='Successfully registered!';}
  }
}
if($eid&&($_GET['action']??'')==='cancel'){$pdo->prepare("DELETE FROM registrations WHERE user_id=? AND event_id=?")->execute([$uid,$eid]);$msg='Registration cancelled.';}
$evs=$pdo->prepare("SELECT e.*,(SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id) AS rc,(SELECT id FROM registrations r2 WHERE r2.user_id=? AND r2.event_id=e.id LIMIT 1) AS mrid FROM events e WHERE e.event_date>=NOW() AND e.is_active=1 ORDER BY e.event_date ASC");$evs->execute([$uid]);$all=$evs->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/><title>Browse Events — CampusVerse</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet"/>
<style>
:root{--bg:#050810;--surface:#0d1117;--card:#111827;--border:rgba(255,255,255,0.08);--accent:#6EE7B7;--accent2:#818CF8;--text:#F1F5F9;--muted:#94A3B8;--error:#F87171;}
*{box-sizing:border-box;margin:0;padding:0;}body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh;}
.layout{display:flex;min-height:100vh;}
.sidebar{width:250px;background:var(--surface);border-right:1px solid var(--border);padding:2rem 1.5rem;position:fixed;height:100vh;display:flex;flex-direction:column;}
.main{margin-left:250px;padding:2.5rem;flex:1;}
.slogo{font-family:'Syne',sans-serif;font-weight:800;font-size:1.4rem;background:linear-gradient(135deg,var(--accent),var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:2rem;display:block;}
.ni{display:flex;align-items:center;gap:.75rem;padding:.7rem 1rem;border-radius:10px;text-decoration:none;color:var(--muted);font-size:.875rem;margin-bottom:2px;transition:all .2s;}
.ni:hover,.ni.active{background:rgba(110,231,183,.08);color:var(--accent);}
.sf{margin-top:auto;padding-top:1.5rem;border-top:1px solid var(--border);}
.btn-lo{display:block;padding:.6rem;text-align:center;border-radius:8px;border:1px solid var(--border);color:var(--muted);text-decoration:none;font-size:.82rem;transition:all .2s;}
.btn-lo:hover{border-color:var(--error);color:var(--error);}
.ph{margin-bottom:2rem;}
.ph h1{font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;}
.alert{padding:.875rem 1rem;border-radius:10px;margin-bottom:1.5rem;font-size:.875rem;}
.as{background:rgba(110,231,183,.08);border:1px solid rgba(110,231,183,.2);color:var(--accent);}
.ae{background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2);color:var(--error);}
.eg{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:1.25rem;}
.ec{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;transition:all .3s;}
.ec:hover{transform:translateY(-6px);border-color:rgba(110,231,183,.2);box-shadow:0 20px 50px rgba(0,0,0,.4);}
.eb{height:110px;display:flex;align-items:center;justify-content:center;font-size:3rem;position:relative;}
.b0{background:linear-gradient(135deg,#0a1628,#1a2a4a);}.b1{background:linear-gradient(135deg,#0a2010,#1a4020);}.b2{background:linear-gradient(135deg,#2a0a1a,#4a1a2a);}.b3{background:linear-gradient(135deg,#1a1a0a,#3a3a0a);}.b4{background:linear-gradient(135deg,#1a0a2a,#2a1a3a);}
.stag{position:absolute;top:.75rem;right:.75rem;padding:.2rem .6rem;border-radius:100px;font-size:.7rem;font-weight:600;}
.sok{background:rgba(110,231,183,.15);border:1px solid rgba(110,231,183,.3);color:var(--accent);}
.sfl{background:rgba(248,113,113,.15);border:1px solid rgba(248,113,113,.3);color:var(--error);}
.ed{padding:1.25rem;}
.et{font-family:'Syne',sans-serif;font-weight:700;margin-bottom:.5rem;font-size:.95rem;}
.em{color:var(--muted);font-size:.8rem;margin-bottom:1rem;display:flex;flex-direction:column;gap:.2rem;}
.ef{display:flex;justify-content:space-between;align-items:center;padding-top:.875rem;border-top:1px solid var(--border);}
.ep{font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;}.epf{color:var(--accent);}.epp{color:#FBBF24;}
.btn-r{padding:.45rem 1.1rem;border-radius:8px;font-size:.8rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;display:inline-block;transition:all .2s;font-family:'DM Sans',sans-serif;}
.breg{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#050810;}
.breg:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(110,231,183,.25);}
.bregd{background:rgba(110,231,183,.1);border:1px solid rgba(110,231,183,.3);color:var(--accent);cursor:default;}
.bful{background:rgba(255,255,255,.05);border:1px solid var(--border);color:var(--muted);cursor:not-allowed;}
.empty{text-align:center;padding:4rem;color:var(--muted);border:1px dashed var(--border);border-radius:16px;}
@media(max-width:768px){.sidebar{display:none;}.main{margin-left:0;}}
</style></head><body>
<div class="layout">
<aside class="sidebar">
  <span class="slogo">CampusVerse</span>
  <a href="<?=BASE_URL?>/user/dashboard.php" class="ni">🏠 Dashboard</a>
  <a href="<?=BASE_URL?>/user/my_events.php" class="ni">🎫 My Registrations</a>
  <a href="<?=BASE_URL?>/user/register_event.php" class="ni active">🔍 Browse Events</a>
  <div class="sf"><a href="<?=BASE_URL?>/logout.php" class="btn-lo">Sign Out</a></div>
</aside>
<main class="main">
  <div class="ph"><h1>Browse Events</h1></div>
  <?php if($msg): ?><div class="alert as">✅ <?=htmlspecialchars($msg)?></div><?php endif; ?>
  <?php if($error): ?><div class="alert ae">⚠️ <?=htmlspecialchars($error)?></div><?php endif; ?>
  <?php if(empty($all)): ?><div class="empty"><div style="font-size:3rem;margin-bottom:1rem">🎯</div><h3 style="font-family:'Syne',sans-serif;margin-bottom:.5rem">No Upcoming Events</h3><p>Check back soon!</p></div>
  <?php else: $em=['💻','⚽','🎨','📚','🎉','🛠️']; ?>
  <div class="eg"><?php foreach($all as $i=>$ev):$slots=$ev['max_participants']-$ev['rc'];$full=$slots<=0;$mine=!empty($ev['mrid']); ?>
    <div class="ec"><div class="eb b<?=$i%5?>"><?=$em[$i%6]?><span class="stag <?=$full?'sfl':'sok'?>"><?=$full?'Full':$slots.' slots'?></span></div>
    <div class="ed"><h3 class="et"><?=htmlspecialchars($ev['title'])?></h3><div class="em"><span>📅 <?=date('D, M j, Y',strtotime($ev['event_date']))?></span><span>🕐 <?=date('g:i A',strtotime($ev['event_date']))?></span><span>📍 <?=htmlspecialchars($ev['location']??'TBA')?></span><span>👥 <?=$ev['rc']?>/<?=$ev['max_participants']?> registered</span></div>
    <div class="ef"><span class="ep <?=($ev['price']==0)?'epf':'epp'?>"><?=(!$ev['price']||$ev['price']==0)?'Free':'₹'.number_format($ev['price'])?></span>
    <?php if($mine): ?><a href="<?=BASE_URL?>/user/register_event.php?id=<?=$ev['id']?>&action=cancel" class="btn-r bregd" onclick="return confirm('Cancel?')">✓ Registered</a>
    <?php elseif($full): ?><span class="btn-r bful">Full</span>
    <?php else: ?><a href="<?=BASE_URL?>/user/register_event.php?id=<?=$ev['id']?>&action=register" class="btn-r breg">Register →</a>
    <?php endif; ?></div></div></div>
  <?php endforeach; ?></div><?php endif; ?>
</main></div></body></html>