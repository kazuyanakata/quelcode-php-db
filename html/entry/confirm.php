<?php
session_start();
require_once('dbconnect.php');

if (!isset($_SESSION['input'])) {
  header('Location: error.php');
  exit();
}
if (!empty($_POST)) {
  $_SESSION['confirm'] = $_SESSION['input'];
  unset($_SESSION['input']);
  header('Location: complete.php');
  exit();
}
function h($value) {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>プレエントリーお申し込み | QUELCODE ISA オンラインプログラミングスクール | 卒業まで学費不要、日本初のISAを採用</title>

  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <img src="../images/cropped-quelcode-2.png" alt="QUELCODEロゴ" class="header-logo">
  </header>

  <main>
    <div class="title">
      <p class="main-title">
        【ISA】QUELCODE<br>
        プレエントリーフォーム
      </p>
      <p class="sub-title">
        応募はこちらから。日本で初めてISA(学費後払い)を採用したプログラミングスクール<br>
        の募集です。全国からのご応募をおまちしています。
      </p>
    </div>

    <form action="" method="post" novalidate>
    <div class="blank"></div>
      <input type="hidden" name="action" value="submit" />
      <ul class="confirm-form-list-items">
        <li class="confirm-form-list-item">
          <p>お名前<span class="must">必須</span></p>
          <p><?php echo h($_SESSION['input']['name'])?></p>
        </li>
        <li class="form-list-item">
          <p>ふりがな<span class="must">必須</span></p>
          <p><?php echo h($_SESSION['input']['furigana'])?></p>
        </li>
        <li class="form-list-item">
          <p>メールアドレス<span class="must">必須</span></p>
          <p><?php echo h($_SESSION['input']['email'])?></p>
        </li>
        <li class="form-list-item">
          <p>電話番号<span class="must">必須</span></p>
          <p><?php echo h($_SESSION['input']['phone-number'])?></p>
        </li>
        <li class="form-list-item">
          <p>生年月日<span class="must">必須</span></p>
          <p><?php echo h($_SESSION['input']['birthday-year'])?>年<?php echo h($_SESSION['input']['birthday-month'])?>月<?php echo h($_SESSION['input']['birthday-day'])?>日</p>
        </li>
        <li class="form-list-item">
          <p>都道府県<span class="must">必須</span></p>
          <?php 
          $member_prefectures = $db->prepare('SELECT name FROM prefectures WHERE id=?');
          $member_prefectures->execute(array((string)$_SESSION['input']['prefecture-id']));
          $member_prefecture = $member_prefectures->fetch();
          ?>
          <p><?php echo h($member_prefecture['name'])?></p>
        </li>
      </ul>
      <div class="button">
        <a href="input.php?action=return" class="confirm-return">戻る</a>
        <input type="submit" value="送信" class="confirm-submit">
      </div>
    </form>
  </main>

</body>
</html>
