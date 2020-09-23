<?php
session_start();
require_once('../dbconnect.php');

if (empty($_SESSION['update'])){//直接アップデートに入った場合はエラー画面へ
  header('Location: ../error.php');
  exit();
}
// ステータスをアップデート
$status_update = $db->prepare('UPDATE members SET status_id = ? WHERE id = ?');
$status_update->execute(array(
  $_SESSION['update']['member-status'],
  $_SESSION['update']['member-id']
));
// ↓成功の証明追加
$_SESSION['update']['success'] = 1;
// ↓対象メンバーのidを変数に代入
$member_id = $_SESSION['update']['member-id'];
header("Location: detail.php?id=${member_id}");
exit();
