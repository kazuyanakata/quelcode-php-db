<?php
try {
  $db = new PDO('mysql:dbname=quelcode-php-db;host=mysql;charset=utf8', 'root', 'root');
} catch (PDOException $e) {
  header('Location: error.php');
  exit();
}

function h($value) {// htmlspecialchars設定
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
// ステータスとカウントリスト作成→連想配列に代入
$status_counts = $db->query('SELECT s.id, s.name, m.cnt 
                               FROM statuses s 
                          LEFT JOIN (SELECT status_id, COUNT(*) AS cnt 
                                       FROM members 
                                   GROUP BY status_id 
                                   ORDER BY status_id) AS m 
                                 ON s.id = m.status_id');
foreach ($status_counts as $status_count) {
  if(!empty($status_count['cnt'])){//ステータスのカウント数が0でない場合
    $count[$status_count['id']] = [
      $status_count['name'],
      $status_count['cnt']
    ];
  } else {//ステータスのカウント数が0である場合
    $count[$status_count['id']] = [
      $status_count['name'],
      0
    ];
  }
}
// 合計値集計
$sum_count = array_sum(array_column($count,'1'));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>

  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <ul class="header-list-items">
      <li class="header-list"><a href="index.php" class="header-link">トップ</a></li>
      <li class="header-list"><a href="data/list.php" class="header-link">申し込み</a></li>
    </ul>
  </header>

  <main>
    <h1 class="main-title">
      トップページ
    </h1>
    <div class="status-count">
      <h2 class="status-count-title">申し込み件数</h2>
      <table class="status-count-table">
        <tr class="table-row">
          <th class="table-head-left">ステータス</th>
          <th class="table-head-right">件数</th>
        </tr>
<?php
foreach($count as $status_id => $status_info):
?>
        <tr class="table-row">
          <td class="table-data-left"><a href="data/list.php?search-name=&search-status=<?php echo h($status_id)?>"><?php echo h($status_info[0])?></a></td>
          <td class="table-data-right"><?php echo h(number_format($status_info[1]))?></td>
        </tr>
<?php
endforeach;
?>
        <tr class="table-row-last">
          <td colspan="2" class="table-data-right"><span class="table-span">合計</span><?php echo h(number_format($sum_count))?></td>
        </tr>
      </table>
    </div>
  </main>
</body>
</html>
