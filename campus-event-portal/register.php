<?php
session_start();
require_once 'config/base.php';
if(isset($_SESSION['user_id'])){header('Location: '.BASE_URL.'/user/dashboard.php');exit;}
require_once 'config/db.php';
$error='';$success='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!hash_equals($_SESSION['csrf_token']??'',$_POST['csrf_token']??'')){$error='Invalid request.';}
  else{
    $name=trim(strip_tags($_POST['name']??''));$email=trim(filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL));
    $dept=trim(strip_tags($_POST['department']??''));$year=intval($_POST['year']??0);
    $password=$_POST['password']??'';$confirm=$_POST['confirm_password']??'';
    if(!$name||!$email||!$dept||!$year||!$password||!$confirm){$error='All fields are required.';}
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){$error='Enter a valid email address.';}
    elseif(strlen($password)<8){$error='Password must be at least 8 characters.';}
    elseif($password!==$confirm){$error='Passwords do not match.';}
    else{
      $c=$pdo->prepare("SELECT id FROM users WHERE email=?");$c->execute([$email]);
      if($c->fetch()){$error='An account with this email already exists.';}
      else{
        $hash=password_hash($password,PASSWORD_BCRYPT,['cost'=>12]);
        $s=$pdo->prepare("INSERT INTO users(name,email,department,year,password,created_at)VALUES(?,?,?,?,?,NOW())");
        if($s->execute([$name,$email,$dept,$year,$hash])){$success='Account created! You can now sign in.';}
        else{$error='Something went wrong. Please try again.';}
      }
    }
  }
}
if(empty($_SESSION['csrf_token']))$_SESSION['csrf_token']=bin2hex(random_bytes(32));
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/><title>Register — CampusVerse</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet"/>
<style>
:root{--bg:#050810;--card:#111827;--border:rgba(255,255,255,0.08);--accent:#6EE7B7;--accent2:#818CF8;--accent3:#F472B6;--text:#F1F5F9;--muted:#94A3B8;--error:#F87171;}
*{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}
.bg1{position:fixed;inset:0;z-index:0;background:radial-gradient(ellipse 700px 500px at 80% 20%,rgba(129,140,248,.07),transparent 70%),radial-gradient(ellipse 600px 600px at 10% 80%,rgba(244,114,182,.06),transparent 70%);}
.card{position:relative;z-index:1;width:100%;max-width:500px;background:rgba(17,24,39,.88);backdrop-filter:blur(24px);border:1px solid var(--border);border-radius:24px;overflow:hidden;animation:ci .7s cubic-bezier(.23,1,.32,1) both;}
@keyframes ci{from{opacity:0;transform:translateY(40px)}to{opacity:1;transform:translateY(0)}}
.ct{padding:2.5rem 2.5rem 2rem;border-bottom:1px solid var(--border);}
.back{display:inline-flex;align-items:center;color:var(--muted);text-decoration:none;font-size:.82rem;margin-bottom:1.5rem;transition:color .2s;}
.back:hover{color:var(--accent);}
.logo{font-family:'Syne',sans-serif;font-weight:800;font-size:1.5rem;background:linear-gradient(135deg,var(--accent),var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:block;margin-bottom:.5rem;}
.ctitle{font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;letter-spacing:-.02em;}
.csub{color:var(--muted);font-size:.9rem;margin-top:.3rem;}
.cb{padding:2rem 2.5rem 2.5rem;}
.alert-e{padding:.875rem 1rem;border-radius:10px;margin-bottom:1.25rem;font-size:.875rem;background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2);color:var(--error);}
.alert-s{padding:.875rem 1rem;border-radius:10px;margin-bottom:1.25rem;font-size:.875rem;background:rgba(110,231,183,.08);border:1px solid rgba(110,231,183,.2);color:var(--accent);}
.fr{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
.fg{margin-bottom:1.1rem;}
label{display:block;font-size:.78rem;font-weight:600;color:var(--muted);margin-bottom:.45rem;letter-spacing:.04em;text-transform:uppercase;}
.iw{position:relative;}
.ii{position:absolute;left:1rem;top:50%;transform:translateY(-50%);pointer-events:none;font-size:.95rem;}
input,select{width:100%;padding:.8rem 1rem .8rem 2.8rem;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:10px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:.9rem;transition:all .3s;outline:none;appearance:none;}
input:focus,select:focus{border-color:var(--accent);background:rgba(110,231,183,.04);box-shadow:0 0 0 3px rgba(110,231,183,.08);}
input::placeholder{color:rgba(148,163,184,.5);}
select option{background:var(--card);}
.pwt{position:absolute;right:1rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);}
.psb{margin-top:.4rem;height:4px;border-radius:4px;background:var(--border);overflow:hidden;}
.psb-bar{height:100%;border-radius:4px;width:0;transition:all .4s;background:var(--error);}
.ph{font-size:.72rem;color:var(--muted);margin-top:.3rem;}
.btn-s{width:100%;padding:.95rem;background:linear-gradient(135deg,var(--accent),var(--accent2));border:none;border-radius:12px;color:#050810;font-weight:800;font-size:1rem;cursor:pointer;font-family:'Syne',sans-serif;transition:all .3s;margin-top:.5rem;}
.btn-s:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(110,231,183,.3);}
.li{text-align:center;font-size:.875rem;color:var(--muted);margin-top:1.25rem;}
.li a{color:var(--accent);text-decoration:none;font-weight:600;}
@media(max-width:480px){.fr{grid-template-columns:1fr;}}
</style></head><body>
<div class="bg1"></div>
<div class="card">
  <div class="ct">
    <a href="<?=BASE_URL?>/" class="back">← Back to home</a>
    <span class="logo">CampusVerse</span>
    <h1 class="ctitle">Create Account</h1>
    <p class="csub">Join your campus community today</p>
  </div>
  <div class="cb">
    <?php if($error): ?><div class="alert-e">⚠️ <?=htmlspecialchars($error)?></div><?php endif; ?>
    <?php if($success): ?><div class="alert-s">✅ <?=htmlspecialchars($success)?> <a href="<?=BASE_URL?>/login.php" style="color:var(--accent);margin-left:.5rem">Sign in →</a></div><?php endif; ?>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>"/>
      <div class="fr">
        <div class="fg"><label>Full Name</label><div class="iw"><span class="ii">👤</span><input type="text" name="name" placeholder="Your full name" value="<?=htmlspecialchars($_POST['name']??'')?>" required/></div></div>
        <div class="fg"><label>Year of Study</label><div class="iw"><span class="ii">📅</span><select name="year" required><option value="">Select Year</option><?php for($y=1;$y<=5;$y++){$sel=($_POST['year']??'')==$y?'selected':'';echo "<option value='$y' $sel>Year $y</option>";}?></select></div></div>
      </div>
      <div class="fg"><label>College Email</label><div class="iw"><span class="ii">✉️</span><input type="email" name="email" placeholder="you@college.edu" value="<?=htmlspecialchars($_POST['email']??'')?>" required/></div></div>
      <div class="fg"><label>Department</label><div class="iw"><span class="ii">🏛️</span><select name="department" required>
        <option value="">Select Department</option>
        <?php foreach(['Computer Science','Information Technology','Electronics & Communication','Mechanical Engineering','Civil Engineering','Electrical Engineering','Business Administration','Arts & Humanities','Science','Other'] as $d){$sel=($_POST['department']??'')===$d?'selected':'';echo "<option value='".htmlspecialchars($d)."' $sel>".htmlspecialchars($d)."</option>";}?>
      </select></div></div>
      <div class="fr">
        <div class="fg"><label>Password</label><div class="iw"><span class="ii">🔒</span><input type="password" id="pw1" name="password" placeholder="Min 8 chars" required/><button type="button" class="pwt" onclick="tp('pw1','t1')" id="t1">👁</button></div><div class="psb"><div class="psb-bar" id="sb"></div></div><div class="ph" id="ph">Use 8+ chars with uppercase & numbers</div></div>
        <div class="fg"><label>Confirm Password</label><div class="iw"><span class="ii">🔑</span><input type="password" id="pw2" name="confirm_password" placeholder="Repeat password" required/><button type="button" class="pwt" onclick="tp('pw2','t2')" id="t2">👁</button></div></div>
      </div>
      <label style="display:flex;align-items:flex-start;gap:.75rem;font-size:.82rem;color:var(--muted);text-transform:none;letter-spacing:0;margin-bottom:1rem;"><input type="checkbox" id="tc" name="terms" required style="width:auto;padding:0;accent-color:var(--accent);margin-top:3px;"/> I agree to the <a href="#" style="color:var(--accent);">Terms of Service</a></label>
      <button type="submit" class="btn-s">Create My Account →</button>
    </form>
    <div class="li">Already have an account? <a href="<?=BASE_URL?>/login.php">Sign in →</a></div>
  </div>
</div>
<script>
function tp(id,bid){const f=document.getElementById(id),b=document.getElementById(bid);f.type=f.type==='password'?'text':'password';b.textContent=f.type==='password'?'👁':'🙈';}
document.getElementById('pw1').addEventListener('input',function(){const v=this.value,sb=document.getElementById('sb'),ph=document.getElementById('ph');let s=0;if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;sb.style.width=(s/4*100)+'%';const c=['#F87171','#F87171','#FBBF24','#34D399','#6EE7B7'],l=['','Weak','Fair','Good','Strong ✓'];sb.style.background=c[s];ph.textContent=l[s]||'Use 8+ chars with uppercase & numbers';});
document.getElementById('pw2').addEventListener('input',function(){this.style.borderColor=this.value===document.getElementById('pw1').value?'var(--accent)':'var(--error)';});
</script></body></html>