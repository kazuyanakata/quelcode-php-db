<?php
session_start();
require_once('dbconnect.php');

function encryption($value) {// 暗号化処理
  $method = 'AES-256-CBC';
  $encryption_item = base64_encode(openssl_encrypt($value, $method, getenv('ENCRYPTKEY'), OPENSSL_RAW_DATA, getenv('ENCRYPTIV')));
  return $encryption_item;
}
if (isset($_SESSION['confirm'])) {//暗号化・ハッシュ化してmembersテーブルへ保存
  // 名前・ふりがなを1文字づつに分けて配列化→1文字ずつハッシュ化したものを結合
  $name_parts = str_split($_SESSION['confirm']['name']);
  $name_hash = '';
  foreach ($name_parts as $name_part) {//名前のハッシュ化
    $name_hash .= crypt($name_part, getenv('ENCRYPTKEY'));
  }
  $furigana_parts = str_split($_SESSION['confirm']['furigana']);
  $furigana_hash = '';
  foreach ($furigana_parts as $furigana_part) {//ふりがなのハッシュ化
    $furigana_hash .= crypt($furigana_part, getenv('ENCRYPTKEY'));
  }
  $register = $db-> prepare('INSERT INTO members SET name=?, name_hash=?, furigana=?, furigana_hash=?,mail=?, phone_number=?, birthday=?, prefecture_id=?');
  $register->execute(array(
    encryption($_SESSION['confirm']['name']),
    $name_hash,
    encryption($_SESSION['confirm']['furigana']),
    $furigana_hash,
    encryption($_SESSION['confirm']['email']),
    encryption($_SESSION['confirm']['phone-number']),
    encryption($_SESSION['confirm']['birthday-year'].'/'.$_SESSION['confirm']['birthday-month'].'/'.$_SESSION['confirm']['birthday-day']),
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
