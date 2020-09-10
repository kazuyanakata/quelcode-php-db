<?php
session_start();
require_once('dbconnect.php');

if (isset($_SESSION['confirm'])) {
  $register = $db-> prepare('INSERT INTO members SET name=?, furigana=?, mail=?, phone_number=?, birthday=?, prefecture_id=?');
  $register->execute(array(
    $_SESSION['confirm']['name'],
    $_SESSION['confirm']['furigana'],
    $_SESSION['confirm']['email'],
    $_SESSION['confirm']['phone-number'],
    $_SESSION['confirm']['birthday-year'].'/'.$_SESSION['confirm']['birthday-month'].'/'.$_SESSION['confirm']['birthday-day'],
    (string)$_SESSION['confirm']['prefecture-id']
  ));
  unset($_SESSION['confirm']);
} else {
  header('Location: error.php');
  exit();
}
?><!DOCTYPE html>
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

    <div class="thanks-message">
      <p>ご応募ありがとうございました。</p>
    </div>
  </main>

</body>
</html>
