<?php
session_start();
try {
  $db = new PDO('mysql:dbname=quelcode-php-db;host=mysql;charset=utf8', 'root', 'root');
} catch (PDOException $e) {
  header('Location: ../error.php');
  exit();
}

if (!empty($_POST)) {// 更新ボタンを押した場合
  $_SESSION['update'] = $_POST;
  unset($_POST);
  header('Location:update.php');
  exit();
}
function h($value) {// ユーザー定義関数
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
function t($value) {// 時間表示設定
  $date = new DateTime($value);
  return $date->format('Y/m/d G:i:s');
}
function a($value) {// 年齢計算
  $now = date('Ymd');
  $birthday = str_replace('/','',$value);
  return floor(($now - $birthday)/10000);
}
// 該当メンバーの詳細テーブル作成→memberのidから対象者抽出
if(!empty($_GET)){
  $member_details = $db ->prepare('SELECT m.id, m.created_at, m.name, m.furigana, m.mail, m.phone_number, m.birthday, p.name AS pref_name, s.name AS status_name, s.id AS status_id
                                     FROM members m
                                LEFT JOIN prefectures p
                                       ON p.id = m.prefecture_id
                                LEFT JOIN statuses s
                                       ON s.id = m.status_id
                                    WHERE m.id = ?');
  $member_details-> execute(array($_GET['id']));
  $member_detail = $member_details->fetch();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>

  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css">
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <header>
    <ul class="header-list-items">
      <li class="header-list"><a href="../index.php" class="header-link">トップ</a></li>
      <li class="header-list"><a href="list.php" class="header-link">申し込み</a></li>
    </ul>
  </header>

  <main>
    <h1 class="main-title">
      申し込み詳細
    </h1>
<?php
if(!empty($member_detail))://GETパラメーターでidを指定しており、そのidのメンバーが存在する場合
?>
    <div class="status-update">
      <h2 class="status-update-title">ステータス</h2>
<?php
  if((int)$member_detail['status_id'] !== 6)://ステータスが退会ではない場合
?>
      <form action="" class="update-form" method="post">
        <input type="hidden" value="<?php echo h($member_detail['id'])?>" name="member-id">
        <select name="member-status">
<?php
    if ((int)$member_detail['status_id'] === 1):
?>
          <option value="1" selected>受付済</option>
          <option value="2">プレワーク案内済</option>
<?php
    elseif((int)$member_detail['status_id'] === 2):
?>
          <option value="2" selected>プレワーク案内済</option>
          <option value="3">プレワーク中</option>
          <option value="6">退会</option>
<?php
    elseif((int)$member_detail['status_id'] === 3):
?>
          <option value="3" selected>プレワーク中</option>
          <option value="4">本エントリー済</option>
          <option value="5">休会中</option>
          <option value="6">退会</option>
<?php
    elseif((int)$member_detail['status_id'] === 4):
?>
          <option value="4" selected>本エントリー済</option>
          <option value="3">プレワーク中</option>
          <option value="5">休会中</option>
          <option value="6">退会</option>
<?php
    elseif((int)$member_detail['status_id'] === 5):
?>
          <option value="5" selected>休会中</option>
          <option value="3">プレワーク中</option>
          <option value="6">退会</option>
<?php
    endif;
?>
        </select>
        <input type="submit" value="更新" class="update-form-submit">
      </form>
<?php
  elseif((int)$member_detail['status_id'] === 6)://ステータスが退会の場合
?>
      <p>退会</p>
<?php
  endif;
if(!empty($_SESSION['update']['success']) && (int)$_SESSION['update']['success'] === 1):
?>
      <p style="color: red;">更新が完了しました</p>
<?php
endif;
unset($_SESSION['update']);//更新のセッション削除
?>
    </div>

    <div class="member-detail">
      <ul class="member-detail-items">
        <li class="member-detail-item">
          <p class="detail-title">No</p>
          <p class="detail-content"><?php echo h($member_detail['id'])?></p>
        </li>
        <li class="member-detail-item">
          <p class="detail-title">申込日時</p>
          <p class="detail-content"><?php echo h(t($member_detail['created_at']))?></p>
        </li>
        <li class="member-detail-item">
          <p class="detail-title">名前</p>
          <p class="detail-content"><?php echo h($member_detail['name'])?></p>
        </li>
        <li class="member-detail-item">
          <p class="detail-title">ふりがな</p>
          <p class="detail-content"><?php echo h($member_detail['furigana'])?></p>
        </li>
        <li class="member-detail-item">
          <p class="detail-title">メールアドレス</p>
          <p class="detail-content"><a href="mailto:<?php echo h($member_detail['mail'])?>"><?php echo h($member_detail['mail'])?></a></p>
        </li>
        <li class="member-detail-item">
          <p class="detail-title">電話番号</p>
          <p class="detail-content"><?php echo h($member_detail['phone_number'])?></p>
        </li>
        <li class="member-detail-item">
          <p class="detail-title">生年月日</p>
          <p class="detail-content"><?php echo h($member_detail['birthday'])?>（満 <?php echo h(a($member_detail['birthday']))?>歳）</p>
        </li>
        <li class="member-detail-item">
          <p class="detail-title">都道府県</p>
          <p class="detail-content"><?php echo h($member_detail['pref_name'])?></p>
        </li>
      </ul>
    </div>

    <div class="return-list">
      <a href="list.php?search-name=<?php if(!empty($_SESSION['list']['search_name'])){echo h($_SESSION['list']['search_name']);}?>&search-status=<?php if(!empty($_SESSION['list']['search_status'])){echo h($_SESSION['list']['search_status']);}?>">◀︎ 一覧戻る</a>
    </div>
<?php
else://GETパラメーターでidを指定していない、または存在しないidを指定した場合
?>
    <p>該当のNoの申し込みはありません</p>
<?php
endif;
?>
  </main>
</body>
</html>
