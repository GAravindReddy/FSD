<?php
session_start();
require_once '../config/base.php';
if(!isset($_SESSION['user_id'])){header('Location: '.BASE_URL.'/login.php');exit;}
require_once '../config/db.php';
$uid=$_SESSION['user_id'];
if(isset($_GET['cancel'])){$eid=intval($_GET['cancel']);$pdo->prepare("DELETE FROM registrations WHERE user_id=? AND event_id=?")->execute([$uid,$eid]);header('Location: '.BASE_URL.'/user/my_events.php?msg=cancelled');exit;}
$msg=$_GET['msg']??'';
$s=$pdo->prepare("SELECT e.*,r.registered_at,r.id AS rid FROM registrations r JOIN events e ON r.event_id=e.id WHERE r.user_id=? ORDER BY e.event_date ASC");$s->execute([$uid]);$all=$s->fetchAll();
$up=array_filter($all,fn($e)=>strtotime($e['event_date'])>=time());
$ps=array_filter($all,fn($e)=>strtotime($e['event_date'])<time());
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/><title>My Events — CampusVerse</title>
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
.ph{margin-bottom:2rem;}.ph h1{font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;}
.alert-s{padding:.875rem 1rem;border-radius:10px;margin-bottom:1.5rem;font-size:.875rem;background:rgba(110,231,183,.08);border:1px solid rgba(110,231,183,.2);color:var(--accent);}
.sl{font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;margin-bottom:1rem;}
.eg{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem;margin-bottom:2.5rem;}
.ec{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;transition:all .3s;}
.ec.past{opacity:.6;}.ec:hover{transform:translateY(-4px);}
.eb{height:100px;display:flex;align-items:center;justify-content:center;font-size:2.5rem;}
.b0{background:linear-gradient(135deg,#0a1628,#1a2a4a);}.b1{background:linear-gradient(135deg,#0a2010,#1a4020);}.b2{background:linear-gradient(135deg,#2a0a1a,#4a1a2a);}.b3{background:linear-gradient(135deg,#1a1a0a,#3a3a0a);}.b4{background:linear-gradient(135deg,#1a0a2a,#2a1a3a);}
.ed{padding:1.25rem;}.et{font-family:'Syne',sans-serif;font-weight:700;margin-bottom:.5rem;font-size:.95rem;}
.em{color:var(--muted);font-size:.8rem;margin-bottom:.875rem;display:flex;flex-direction:column;gap:.2rem;}
.ef{display:flex;justify-content:space-between;align-items:center;padding-top:.875rem;border-top:1px solid var(--border);}
.tc{background:rgba(110,231,183,.1);color:var(--accent);padding:.2rem .6rem;border-radius:100px;font-size:.7rem;font-weight:600;}
.tp{background:rgba(148,163,184,.1);color:var(--muted);padding:.2rem .6rem;border-radius:100px;font-size:.7rem;font-weight:600;}
.btn-cx{padding:.4rem 1rem;border-radius:8px;font-size:.78rem;font-weight:600;background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.2);color:var(--error);text-decoration:none;transition:all .2s;display:inline-block;}
.btn-cx:hover{background:rgba(248,113,113,.2);}
.btn-browse{display:inline-block;margin-top:.75rem;padding:.6rem 1.4rem;background:linear-gradient(135deg,var(--accent),var(--accent2));border-radius:10px;color:#050810;font-weight:700;text-decoration:none;font-size:.875rem;}
.empty{text-align:center;padding:3rem;color:var(--muted);border:1px dashed var(--border);border-radius:16px;margin-bottom:2rem;}
@media(max-width:768px){.sidebar{display:none;}.main{margin-left:0;}}
</style></head><body>
<div class="layout">
<aside class="sidebar">
  <span class="slogo">CampusVerse</span>
  <a href="<?=BASE_URL?>/user/dashboard.php" class="ni">🏠 Dashboard</a>
  <a href="<?=BASE_URL?>/user/my_events.php" class="ni active">🎫 My Registrations</a>
  <a href="<?=BASE_URL?>/user/register_event.php" class="ni">🔍 Browse Events</a>
  <div class="sf"><a href="<?=BASE_URL?>/logout.php" class="btn-lo">Sign Out</a></div>
</aside>
<main class="main">
  <div class="ph"><h1>My Registrations</h1></div>
  <?php if($msg==='cancelled'): ?><div class="alert-s">✅ Registration cancelled.</div><?php endif; ?>
  <div class="sl">⚡ Upcoming (<?=count($up)?>)</div>
  <?php if(empty($up)): ?><div class="empty"><p>No upcoming registrations.</p><a href="<?=BASE_URL?>/user/register_event.php" class="btn-browse">Browse Events →</a></div>
  <?php else: ?><div class="eg"><?php $em=['💻','⚽','🎨','📚','🎉'];$i=0;foreach($up as $ev): ?><div class="ec"><div class="eb b<?=$i%5?>"><?=$em[$i%5]?></div><div class="ed"><h3 class="et"><?=htmlspecialchars($ev['title'])?></h3><div class="em"><span>📅 <?=date('D, M j, Y',strtotime($ev['event_date']))?></span><span>📍 <?=htmlspecialchars($ev['location']??'TBA')?></span></div><div class="ef"><div><span class="tc">✓ Confirmed</span><div style="font-size:.72rem;color:var(--muted);margin-top:3px;">Reg. <?=date('M j',strtotime($ev['registered_at']))?></div></div><a href="<?=BASE_URL?>/user/my_events.php?cancel=<?=$ev['id']?>" class="btn-cx" onclick="return confirm('Cancel?')">Cancel</a></div></div></div><?php $i++;endforeach;?></div><?php endif; ?>
  <div class="sl" style="margin-top:1rem;">🕐 Past Events (<?=count($ps)?>)</div>
  <?php if(empty($ps)): ?><div class="empty"><p>No past events.</p></div>
  <?php else: ?><div class="eg"><?php foreach($ps as $ev): ?><div class="ec past"><div class="eb b<?=$i%5?>"><?=$em[$i%5]?></div><div class="ed"><h3 class="et"><?=htmlspecialchars($ev['title'])?></h3><div class="em"><span>📅 <?=date('D, M j, Y',strtotime($ev['event_date']))?></span></div><div class="ef"><span class="tp">Completed</span></div></div></div><?php $i++;endforeach;?></div><?php endif; ?>
</main></div></body></html>