<?php
session_start();
require_once '../config/base.php';
if(!isset($_SESSION['admin_id'])){header('Location: '.BASE_URL.'/login.php');exit;}
require_once '../config/db.php';
error_reporting(0);

$aname=$_SESSION['admin_name']??'Admin';
$te=$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$tu=$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$tr=$pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
$ue=$pdo->query("SELECT COUNT(*) FROM events WHERE event_date>=NOW()")->fetchColumn();
$regs=$pdo->query("SELECT r.*,u.name AS uname,u.department,e.title AS etitle FROM registrations r JOIN users u ON r.user_id=u.id JOIN events e ON r.event_id=e.id ORDER BY r.registered_at DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
$evs=$pdo->query("SELECT e.*,(SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id) AS rc FROM events e ORDER BY event_date DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Admin Dashboard — CampusVerse</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<style>
:root{
  --bg:#060B18;
  --surface:#0C1425;
  --card:#111C33;
  --border:rgba(255,255,255,0.07);
  --accent:#5EEAD4;
  --accent2:#818CF8;
  --accent3:#F472B6;
  --warn:#FBBF24;
  --text:#F0F4FF;
  --text2:#B8C4E0;
  --muted:#64748B;
  --error:#F87171;
  --green:#34D399;
}
*{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{
  background:var(--bg);color:var(--text);
  font-family:'Plus Jakarta Sans',sans-serif;
  min-height:100vh;font-size:15px;line-height:1.6;
}

/* LAYOUT */
.layout{display:flex;min-height:100vh;}
.sidebar{
  width:240px;flex-shrink:0;
  background:var(--surface);
  border-right:1px solid var(--border);
  display:flex;flex-direction:column;
  padding:1.75rem 1.25rem;
  position:fixed;height:100vh;overflow-y:auto;
}
.main{margin-left:240px;flex:1;padding:2.25rem 2.5rem;}

/* SIDEBAR */
.slogo{
  font-size:1.35rem;font-weight:800;letter-spacing:-0.02em;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  margin-bottom:0.4rem;display:block;
}
.abadge{
  background:rgba(244,114,182,0.1);
  border:1px solid rgba(244,114,182,0.2);
  color:var(--accent3);font-size:0.65rem;font-weight:700;
  letter-spacing:0.1em;text-transform:uppercase;
  padding:0.22rem 0.65rem;border-radius:100px;
  display:inline-block;margin-bottom:2rem;
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
.ni .icon{width:20px;text-align:center;}

.sf{margin-top:auto;padding-top:1.25rem;border-top:1px solid var(--border);}
.arow{display:flex;align-items:center;gap:0.75rem;margin-bottom:0.875rem;}
.av{
  width:38px;height:38px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--accent3),var(--accent2));
  display:flex;align-items:center;justify-content:center;
  font-weight:800;color:white;font-size:0.95rem;
}
.aname{font-weight:700;font-size:0.875rem;color:var(--text);}
.arole{font-size:0.72rem;color:var(--muted);margin-top:1px;}
.btn-lo{
  display:block;padding:0.55rem;text-align:center;
  border-radius:8px;border:1px solid var(--border);
  color:var(--muted);text-decoration:none;
  font-size:0.8rem;font-weight:500;transition:all 0.2s;
}
.btn-lo:hover{border-color:var(--error);color:var(--error);}

/* PAGE HEADER */
.pg-head{
  display:flex;justify-content:space-between;
  align-items:flex-start;margin-bottom:2rem;
  flex-wrap:wrap;gap:1rem;
}
.pg-head h1{font-size:1.75rem;font-weight:800;letter-spacing:-0.02em;color:var(--text);}
.pg-head p{color:var(--text2);font-size:0.88rem;margin-top:0.3rem;}
.btn-add{
  padding:0.6rem 1.35rem;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  border:none;border-radius:10px;color:#060B18;
  font-weight:700;font-size:0.875rem;
  text-decoration:none;display:inline-block;
  transition:all 0.3s;font-family:'Plus Jakarta Sans',sans-serif;
  white-space:nowrap;
}
.btn-add:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(94,234,212,0.25);}

/* STATS */
.stats{
  display:grid;grid-template-columns:repeat(4,1fr);
  gap:1rem;margin-bottom:2rem;
}
.sc{
  background:var(--card);border:1px solid var(--border);
  border-radius:14px;padding:1.4rem 1.5rem;
  transition:all 0.3s;position:relative;overflow:hidden;
}
.sc::before{
  content:'';position:absolute;top:-20px;right:-20px;
  width:80px;height:80px;border-radius:50%;opacity:0.07;
}
.sc:nth-child(1)::before{background:var(--accent);}
.sc:nth-child(2)::before{background:var(--accent2);}
.sc:nth-child(3)::before{background:var(--accent3);}
.sc:nth-child(4)::before{background:var(--warn);}
.sc:hover{transform:translateY(-3px);border-color:rgba(94,234,212,0.18);}
.sc-icon{font-size:1.35rem;margin-bottom:0.875rem;}
.sc-val{font-size:2.2rem;font-weight:800;letter-spacing:-0.03em;line-height:1;margin-bottom:0.3rem;}
.sc:nth-child(1) .sc-val{color:var(--accent);}
.sc:nth-child(2) .sc-val{color:var(--accent2);}
.sc:nth-child(3) .sc-val{color:var(--accent3);}
.sc:nth-child(4) .sc-val{color:var(--warn);}
.sc-lbl{font-size:0.78rem;font-weight:600;color:var(--muted);letter-spacing:0.03em;}

/* CONTENT GRID */
.cg{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;}
.panel{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;}
.ph{
  display:flex;justify-content:space-between;align-items:center;
  padding:1.1rem 1.5rem;border-bottom:1px solid var(--border);
}
.pt{font-size:0.95rem;font-weight:700;color:var(--text);}
.pl{color:var(--accent);font-size:0.8rem;font-weight:600;text-decoration:none;opacity:0.85;transition:opacity 0.2s;}
.pl:hover{opacity:1;}

/* TABLE */
table{width:100%;border-collapse:collapse;}
th{
  padding:0.75rem 1.25rem;text-align:left;
  font-size:0.7rem;font-weight:700;color:var(--muted);
  text-transform:uppercase;letter-spacing:0.08em;
  border-bottom:1px solid var(--border);
  background:rgba(255,255,255,0.02);
}
td{
  padding:0.875rem 1.25rem;
  border-bottom:1px solid rgba(255,255,255,0.04);
  font-size:0.845rem;color:var(--text2);
  vertical-align:middle;
}
tr:last-child td{border:none;}
tr:hover td{background:rgba(255,255,255,0.02);}
.td-name{font-weight:600;color:var(--text);}
.td-title{
  font-weight:500;color:var(--text);
  max-width:160px;white-space:nowrap;
  overflow:hidden;text-overflow:ellipsis;
}

/* TAGS */
.tag{display:inline-block;padding:0.22rem 0.65rem;border-radius:6px;font-size:0.7rem;font-weight:700;letter-spacing:0.04em;}
.tg{background:rgba(94,234,212,0.1);color:var(--accent);}
.tb{background:rgba(129,140,248,0.1);color:var(--accent2);}

/* ACTION BUTTONS */
.axs{display:flex;gap:0.4rem;}
.bxs{
  padding:0.3rem 0.75rem;border-radius:7px;
  font-size:0.72rem;font-weight:700;
  text-decoration:none;border:none;cursor:pointer;
  transition:all 0.2s;display:inline-block;
  font-family:'Plus Jakarta Sans',sans-serif;
}
.be{background:rgba(129,140,248,0.1);color:var(--accent2);}
.be:hover{background:rgba(129,140,248,0.22);}
.bd{background:rgba(248,113,113,0.1);color:var(--error);}
.bd:hover{background:rgba(248,113,113,0.22);}

/* EMPTY */
.empty{text-align:center;padding:2.5rem;color:var(--muted);font-size:0.875rem;}

@media(max-width:1100px){.stats{grid-template-columns:repeat(2,1fr);}.cg{grid-template-columns:1fr;}}
@media(max-width:768px){.sidebar{display:none;}.main{margin-left:0;padding:1.5rem;}.stats{grid-template-columns:1fr 1fr;}}
</style>
</head>
<body>
<div class="layout">

<!-- SIDEBAR -->
<aside class="sidebar">
  <span class="slogo">CampusVerse</span>
  <span class="abadge">Admin Panel</span>

  <div class="nav-label">Management</div>
  <a href="<?=BASE_URL?>/admin/admin_dashboard.php" class="ni active"><span class="icon">📊</span> Dashboard</a>
  <a href="<?=BASE_URL?>/admin/manage_events.php" class="ni"><span class="icon">📅</span> Manage Events</a>
  <a href="<?=BASE_URL?>/admin/add_event.php" class="ni"><span class="icon">➕</span> Add Event</a>
  <a href="<?=BASE_URL?>/admin/view_registrations.php" class="ni"><span class="icon">👥</span> Registrations</a>

  <div class="sf">
    <div class="arow">
      <div class="av"><?=strtoupper(substr($aname,0,1))?></div>
      <div>
        <div class="aname"><?=htmlspecialchars($aname)?></div>
        <div class="arole">Administrator</div>
      </div>
    </div>
    <a href="<?=BASE_URL?>/logout.php" class="btn-lo">Sign Out</a>
  </div>
</aside>

<!-- MAIN -->
<main class="main">

  <!-- Header -->
  <div class="pg-head">
    <div>
      <h1>Admin Dashboard</h1>
      <p>Welcome back, <?=htmlspecialchars($aname)?> — <?=date('D, M j, Y')?></p>
    </div>
    <a href="<?=BASE_URL?>/admin/add_event.php" class="btn-add">+ Add New Event</a>
  </div>

  <!-- Stats -->
  <div class="stats">
    <div class="sc">
      <div class="sc-icon">📅</div>
      <div class="sc-val"><?=$te?></div>
      <div class="sc-lbl">Total Events</div>
    </div>
    <div class="sc">
      <div class="sc-icon">🎓</div>
      <div class="sc-val"><?=$tu?></div>
      <div class="sc-lbl">Registered Students</div>
    </div>
    <div class="sc">
      <div class="sc-icon">🎫</div>
      <div class="sc-val"><?=$tr?></div>
      <div class="sc-lbl">Total Registrations</div>
    </div>
    <div class="sc">
      <div class="sc-icon">⚡</div>
      <div class="sc-val"><?=$ue?></div>
      <div class="sc-lbl">Upcoming Events</div>
    </div>
  </div>

  <!-- Content Panels -->
  <div class="cg">

    <!-- Recent Registrations -->
    <div class="panel">
      <div class="ph">
        <span class="pt">Recent Registrations</span>
        <a href="<?=BASE_URL?>/admin/view_registrations.php" class="pl">View All →</a>
      </div>
      <table>
        <thead>
          <tr><th>Student</th><th>Event</th><th>Dept</th><th>Date</th></tr>
        </thead>
        <tbody>
          <?php if(empty($regs)): ?>
          <tr><td colspan="4" class="empty">No registrations yet</td></tr>
          <?php else: foreach($regs as $r): ?>
          <tr>
            <td class="td-name"><?=htmlspecialchars($r['uname'])?></td>
            <td class="td-title"><?=htmlspecialchars($r['etitle'])?></td>
            <td><span class="tag tb"><?=htmlspecialchars(substr($r['department'],0,4))?></span></td>
            <td><?=date('M j',strtotime($r['registered_at']))?></td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Events Overview -->
    <div class="panel">
      <div class="ph">
        <span class="pt">Events Overview</span>
        <a href="<?=BASE_URL?>/admin/manage_events.php" class="pl">Manage →</a>
      </div>
      <table>
        <thead>
          <tr><th>Event</th><th>Date</th><th>Reg.</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php if(empty($evs)): ?>
          <tr><td colspan="4" class="empty">No events yet</td></tr>
          <?php else: foreach($evs as $ev): ?>
          <tr>
            <td class="td-title"><?=htmlspecialchars($ev['title'])?></td>
            <td><?=date('M j',strtotime($ev['event_date']))?></td>
            <td><span class="tag tg"><?=$ev['rc']?></span></td>
            <td>
              <div class="axs">
                <a href="<?=BASE_URL?>/admin/edit_event.php?id=<?=$ev['id']?>" class="bxs be">Edit</a>
                <a href="<?=BASE_URL?>/admin/delete_event.php?id=<?=$ev['id']?>" class="bxs bd"
                   onclick="return confirm('Delete this event?')">Del</a>
              </div>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</main>
</div>
</body>
</html>