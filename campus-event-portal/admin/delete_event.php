<?php
session_start();require_once '../config/base.php';
if(!isset($_SESSION['admin_id'])){header('Location: '.BASE_URL.'/login.php');exit;}
require_once '../config/db.php';
$id=intval($_GET['id']??0);
if($id){$pdo->prepare("DELETE FROM registrations WHERE event_id=?")->execute([$id]);$pdo->prepare("DELETE FROM events WHERE id=?")->execute([$id]);}
header('Location: '.BASE_URL.'/admin/manage_events.php?msg=deleted');exit;