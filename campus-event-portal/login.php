<?php
session_start();
require_once 'config/base.php';
if(isset($_SESSION['user_id'])){header('Location: '.BASE_URL.'/user/dashboard.php');exit;}
if(isset($_SESSION['admin_id'])){header('Location: '.BASE_URL.'/admin/admin_dashboard.php');exit;}
require_once 'config/db.php';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!hash_equals($_SESSION['csrf_token']??'',$_POST['csrf_token']??'')){$error='Invalid request.';}
  else{
    $email=trim(filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL));
    $password=$_POST['password']??'';
    $role=$_POST['role']??'user';
    if(empty($email)||empty($password)){$error='Please fill in all fields.';}
    else{
      if($role==='admin'){
        $s=$pdo->prepare("SELECT * FROM admins WHERE email=? LIMIT 1");$s->execute([$email]);$admin=$s->fetch();
        if($admin&&password_verify($password,$admin['password'])){session_regenerate_id(true);$_SESSION['admin_id']=$admin['id'];$_SESSION['admin_name']=$admin['name'];header('Location: '.BASE_URL.'/admin/admin_dashboard.php');exit;}
        else{$error='Invalid admin credentials.';}
      }else{
        $s=$pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");$s->execute([$email]);$user=$s->fetch();
        if($user&&password_verify($password,$user['password'])){session_regenerate_id(true);$_SESSION['user_id']=$user['id'];$_SESSION['user_name']=$user['name'];header('Location: '.BASE_URL.'/user/dashboard.php');exit;}
        else{$error='Invalid email or password.';}
      }
    }
  }
}
if(empty($_SESSION['csrf_token']))$_SESSION['csrf_token']=bin2hex(random_bytes(32));
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/><title>Sign In — CampusVerse</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet"/>
<style>
:root{--bg:#050810;--card:#111827;--border:rgba(255,255,255,0.08);--accent:#6EE7B7;--accent2:#818CF8;--accent3:#F472B6;--text:#F1F5F9;--muted:#94A3B8;--error:#F87171;}
*{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}
.bg1{position:fixed;inset:0;z-index:0;background:radial-gradient(ellipse 800px 600px at 20% 50%,rgba(110,231,183,0.06),transparent 70%),radial-gradient(ellipse 600px 500px at 80% 50%,rgba(129,140,248,0.06),transparent 70%);}
.grid{position:fixed;inset:0;z-index:0;background-image:linear-gradient(rgba(255,255,255,0.015) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.015) 1px,transparent 1px);background-size:60px 60px;}
.card{position:relative;z-index:1;width:100%;max-width:440px;background:rgba(17,24,39,0.85);backdrop-filter:blur(24px);border:1px solid var(--border);border-radius:24px;overflow:hidden;animation:cardIn 0.7s cubic-bezier(0.23,1,0.32,1) both;}
@keyframes cardIn{from{opacity:0;transform:translateY(40px) scale(0.97)}to{opacity:1;transform:translateY(0) scale(1)}}
.card-top{padding:2.5rem 2.5rem 2rem;border-bottom:1px solid var(--border);}
.back{display:inline-flex;align-items:center;color:var(--muted);text-decoration:none;font-size:0.82rem;margin-bottom:1.5rem;transition:color .2s;}
.back:hover{color:var(--accent);}
.logo{font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;background:linear-gradient(135deg,var(--accent),var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:block;margin-bottom:.5rem;}
.card-title{font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;letter-spacing:-0.02em;}
.card-sub{color:var(--muted);font-size:.9rem;margin-top:.3rem;}
.role-toggle{display:flex;margin-top:1.5rem;background:var(--bg);border-radius:12px;padding:4px;border:1px solid var(--border);}
.role-btn{flex:1;padding:.6rem 1rem;border:none;border-radius:9px;background:transparent;color:var(--muted);cursor:pointer;font-family:'DM Sans',sans-serif;font-size:.875rem;font-weight:500;transition:all .3s;}
.role-btn.active{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#050810;font-weight:700;box-shadow:0 4px 15px rgba(110,231,183,.2);}
.card-body{padding:2rem 2.5rem 2.5rem;}
.alert-error{padding:.875rem 1rem;border-radius:10px;margin-bottom:1.25rem;font-size:.875rem;background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2);color:var(--error);}
.fg{margin-bottom:1.25rem;}
label{display:block;font-size:.78rem;font-weight:600;color:var(--muted);margin-bottom:.45rem;letter-spacing:.04em;text-transform:uppercase;}
.iw{position:relative;}
.ii{position:absolute;left:1rem;top:50%;transform:translateY(-50%);pointer-events:none;}
input{width:100%;padding:.85rem 1rem .85rem 2.8rem;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:12px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:.9rem;transition:all .3s;outline:none;}
input:focus{border-color:var(--accent);background:rgba(110,231,183,.04);box-shadow:0 0 0 3px rgba(110,231,183,.1);}
input::placeholder{color:rgba(148,163,184,.5);}
.pwt{position:absolute;right:1rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);font-size:.9rem;}
.forgot{text-align:right;margin-bottom:1rem;font-size:.82rem;color:var(--accent2);text-decoration:none;}
.btn-submit{width:100%;padding:.95rem;background:linear-gradient(135deg,var(--accent),var(--accent2));border:none;border-radius:12px;color:#050810;font-weight:800;font-size:1rem;cursor:pointer;font-family:'Syne',sans-serif;transition:all .3s;}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(110,231,183,.3);}
.sec{display:flex;align-items:center;justify-content:center;gap:.4rem;margin-top:1.25rem;color:var(--muted);font-size:.75rem;}
.reg{text-align:center;font-size:.875rem;color:var(--muted);margin-top:1.25rem;}
.reg a{color:var(--accent);text-decoration:none;font-weight:600;}
</style></head><body>
<div class="bg1"></div><div class="grid"></div>
<div class="card">
  <div class="card-top">
    <a href="<?=BASE_URL?>/" class="back">← Back to home</a>
    <span class="logo">CampusVerse</span>
    <h1 class="card-title">Welcome back</h1>
    <p class="card-sub">Sign in to access your portal</p>
    <div class="role-toggle">
      <button type="button" class="role-btn active" id="rbS" onclick="setRole('user')">🎓 Student</button>
      <button type="button" class="role-btn" id="rbA" onclick="setRole('admin')">⚙️ Admin</button>
    </div>
  </div>
  <div class="card-body">
    <?php if($error): ?><div class="alert-error">⚠️ <?=htmlspecialchars($error)?></div><?php endif; ?>
    <form method="POST" id="lf">
      <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>"/>
      <input type="hidden" name="role" id="ri" value="user"/>
      <div class="fg"><label>Email Address</label><div class="iw"><span class="ii">✉️</span><input type="email" name="email" placeholder="you@college.edu" value="<?=htmlspecialchars($_POST['email']??'')?>" required/></div></div>
      <div class="fg"><label>Password</label><div class="iw"><span class="ii">🔒</span><input type="password" id="pw" name="password" placeholder="Your password" required/><button type="button" class="pwt" onclick="togglePw()" id="pt">👁</button></div></div>
      <div style="text-align:right;margin-bottom:1rem;"><a href="#" class="forgot" style="font-size:.82rem;color:var(--accent2);text-decoration:none;">Forgot password?</a></div>
      <button type="submit" class="btn-submit">Sign In</button>
      <div class="sec">🔐 Secured with 256-bit encryption</div>
    </form>
    <div class="reg">Don't have an account? <a href="<?=BASE_URL?>/register.php">Create one free →</a></div>
  </div>
</div>
<script>
function setRole(r){document.getElementById('ri').value=r;document.getElementById('rbS').classList.toggle('active',r==='user');document.getElementById('rbA').classList.toggle('active',r==='admin');}
function togglePw(){const p=document.getElementById('pw'),b=document.getElementById('pt');p.type=p.type==='password'?'text':'password';b.textContent=p.type==='password'?'👁':'🙈';}
</script></body></html>