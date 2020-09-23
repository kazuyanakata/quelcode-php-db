<?php
session_start();
require_once('../dbconnect.php');

function h($value) {// htmlspecialchars設定
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
function t($value) {// 時間表示設定
  $date = new DateTime($value);
  return $date->format('Y/m/d');
}
function a($value) {// 満年齢計算
  $now = date('Ymd');
  $birthday = str_replace('/','',$value);
  return floor(($now - $birthday)/10000);
}
function decryption($value){// 復号処理
  $method = 'AES-256-CBC';
  $decryption_item = openssl_decrypt(base64_decode($value), $method, getenv('ENCRYPTKEY'), OPENSSL_RAW_DATA, getenv('ENCRYPTIV'));
  return $decryption_item;
}
//↓検索状況別に閲覧するメンバーを選択する
if (!empty($_GET) && !empty($_GET['search-status'])){//名前とステータス検索、またはステータスのみ検索している場合(ステータスが「すべて」の場合を除く)
  if (!empty($_GET['search-name'])) {//名前が入力されていた場合
    $name_parts = str_split($_GET['search-name']);
    $search_name = '';
    foreach ($name_parts as $name_part) {
      $search_name .= crypt($name_part, getenv('ENCRYPTKEY'));
    }
  } elseif (empty($_GET['search-name'])) {//名前が入力されていない場合
    $search_name = $_GET['search-name'];
  }
  $search_status = $_GET['search-status'];
  $_SESSION['list']['search_name'] = $_GET['search-name'];
  $_SESSION['list']['search_status'] = $search_status;
  // ↓メンバー詳細一覧を作成→連想配列に代入
  $member_lists = $db->prepare('SELECT m.id, m.name, m.name_hash, m.furigana, m.furigana_hash, m.birthday, m.created_at, p.name AS pref_name, s.name AS status_name, s.id AS status_id
                                  FROM members m
                             LEFT JOIN prefectures p
                                    ON p.id = m.prefecture_id
                             LEFT JOIN statuses s
                                    ON s.id = m.status_id
                                 WHERE (m.name_hash LIKE ?
                                    OR m.furigana_hash LIKE ?)
                                   AND status_id IN (?)
                              ORDER BY m.id');
  $member_lists->execute(array("%$search_name%",
                               "%$search_name%",
                               $search_status,
                              ));
  $member_info = [];
  foreach($member_lists as $member_list) {
    $member_info[$member_list['id']] = [
      $member_list['created_at'],
      $member_list['furigana'],
      $member_list['name'],
      $member_list['pref_name'],
      $member_list['birthday'],
      $member_list['status_name'],
      $member_list['status_id']
    ];
  }
} elseif (!empty($_GET) && empty($_GET['search-status'])) {//名前の検索のみ行い、ステータスは「すべて」である場合
  if (!empty($_GET['search-name'])) {//名前が入力されていた場合
    $name_parts = str_split($_GET['search-name']);
    $search_name = '';
    foreach ($name_parts as $name_part) {
      $search_name .= crypt($name_part, getenv('ENCRYPTKEY'));
    }
  } elseif (empty($_GET['search-name'])) {//名前が入力されていない場合
    $search_name = $_GET['search-name'];
  }
  $search_status = $_GET['search-status'];
  $_SESSION['list']['search_name'] = $_GET['search-name'];
  $_SESSION['list']['search_status'] = $search_status;
  $member_lists = $db->prepare('SELECT m.id, m.name, m.name_hash, m.furigana, m.furigana_hash, m.birthday, m.created_at, p.name AS pref_name, s.name AS status_name, s.id AS status_id
                                  FROM members m
                             LEFT JOIN prefectures p
                                    ON p.id = m.prefecture_id
                             LEFT JOIN statuses s
                                    ON s.id = m.status_id
                                 WHERE (m.name_hash LIKE ?
                                    OR m.furigana_hash LIKE ?)
                              ORDER BY m.id');
  $member_lists->execute(array("%$search_name%",
                               "%$search_name%",
                              ));
  $member_info = [];
  foreach($member_lists as $member_list) {
    $member_info[$member_list['id']] = [
      $member_list['created_at'],
      $member_list['furigana'],
      $member_list['name'],
      $member_list['pref_name'],
      $member_list['birthday'],
      $member_list['status_name'],
      $member_list['status_id']
    ];
  }
} else {//検索を実行していない場合
  $_SESSION['list']['search_name'] = '';
  $_SESSION['list']['search_status'] = '';
  $member_lists = $db->query('SELECT m.id, m.name, m.furigana, m.birthday, m.created_at, p.name AS pref_name, s.name AS status_name, s.id AS status_id
                                FROM members m
                           LEFT JOIN prefectures p
                                  ON p.id = m.prefecture_id
                           LEFT JOIN statuses s
                                  ON s.id = m.status_id
                            ORDER BY m.id');
  $member_info = [];
  foreach($member_lists as $member_list) {
    $member_info[$member_list['id']] = [
      $member_list['created_at'],
      $member_list['furigana'],
      $member_list['name'],
      $member_list['pref_name'],
      $member_list['birthday'],
      $member_list['status_name'],
      $member_list['status_id']
    ];
  }
}
// ステータス一覧を作成→連想配列に代入
$status_lists = $db -> query('SELECT id, name 
                                FROM statuses
                            ORDER BY id');
foreach($status_lists as $status_list){
  $status_name_list[$status_list['id']] = $status_list['name'];
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
      申込一覧
    </h1>
<?php
if (!empty($member_info) || !empty($_GET))://メンバーが0ではない、または検索ボタンを押されている場合
?>
    <div class="search">
      <h2 class="search-title">検索</h2>
      <form action="" method="GET" class="search-form">
        <ul class="search-form-items">
          <li class="search-form-name">
            <p>名前</p>
            <input type="text" name="search-name" value="<?php if (!empty($_GET)){ echo h($_GET['search-name']);}?>">
          </li>
          <li class="search-form-status">
            <p>ステータス</p>
            <select name="search-status">
              <option selected value="">すべて</option>
<?php 
  foreach($status_name_list as $status_id => $status_name):
    if (!empty($_GET) && $status_id === (int)$search_status):
?>
              <option value="<?php echo h($status_id)?>" selected><?php echo h($status_name)?></option>
<?php 
    else:
?>
              <option value="<?php echo h($status_id)?>"><?php echo h($status_name)?></option>
<?php
    endif;
  endforeach;
?>
            </select>
          </li>
        </ul>
        <input type="submit" value="検索" class="search-form-submit">
      </form>
<?php
  if (!empty($member_info))://メンバーが0でない場合
?>
    </div>
    <table class="list-table">
      <tr class="list-table-row-first">
        <th class="table-head">No</th>
        <th class="table-head">申込日</th>
        <th class="table-head">名前</th>
        <th class="table-head">都道府県</th>
        <th class="table-head">年齢</th>
        <th class="table-head">ステータス</th>
        <th class="table-head">詳細</th>
      </tr>
<?php 
    foreach($member_info as $member_id => $info):
      if((int)$info[6] === 1):
?>
      <tr class="list-table-row" style="background-color: lightyellow;">
<?php 
      elseif((int)$info[6] === 6):
?>
      <tr class="list-table-row" style="background-color: lightgray;">
<?php 
      else:
?>
      <tr class="list-table-row">
<?php
      endif;
?>
        <td class="table-data"><?php echo h($member_id)?></td>
        <td class="table-data"><?php echo h(t($info[0]))?></td>
        <td class="table-data">
          <p class="name-furigana"><?php echo h(decryption($info[1]))?></p>
          <p class="name-name"><?php echo h(decryption($info[2]))?></p>
        </td>
        <td class="table-data"><?php echo h($info[3])?></td>
        <td class="table-data"><?php echo h(a(decryption($info[4])))?></td>
        <td class="table-data"><?php echo h($info[5])?></td>
        <td class="table-data detail">
          <a href="detail.php?id=<?php echo h($member_id)?>">詳細</a>
        </td>
      </tr>
<?php
    endforeach;
?>
    </table>
<?php 
  elseif(empty($member_info))://検索ボタンを押した結果、メンバーが0であった場合
?>
    <p class="search-none">該当の検索条件では申込データがありません。</p>
<?php 
  endif;
elseif(empty($member_info) && empty($_GET))://検索ボタンを押す前に既にメンバーが0であった場合
?>
    <p>申込データがありません</p>
<?php
endif;
?>
  </main>
</body>
</html>
