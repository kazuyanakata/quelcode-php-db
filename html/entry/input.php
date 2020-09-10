<?php 
  session_start();
  require_once('dbconnect.php');

  if (!empty($_POST)) {
    // エラーチェック
    if ($_POST['name'] === '') {
      $error['name'] = 'blank';
    } elseif (strlen($_POST['name']) > 100) {
      $error['name'] = 'over';
    }
    if ($_POST['furigana'] === '') {
      $error['furigana'][] = 'blank';
    } elseif (strlen($_POST['furigana']) > 100) {
      $error['furigana'][] = 'over';
    } 
    if (!preg_match('/^[ぁ-ん]+[ぁ-ん 　]*$/', $_POST['furigana'])) {
      $error['furigana'][] = 'spell';
    }
    if ($_POST['email'] === '') {
      $error['email'][] = 'blank';
    } elseif (strlen($_POST['email']) > 100) {
      $error['email'][] = 'over';
    } 
    if (!preg_match('/[@]/', $_POST['email']) && strlen($_POST['email']) >= 1) {
      $error['email'][] = 'spell';
    }
    if ($_POST['phone-number'] === '') {
      $error['phone-number'] = 'blank';
    } elseif (!preg_match('/^[0][0-9]{9}/', $_POST['phone-number']) && !preg_match('/^[0][0-9]{10}/', $_POST['phone-number'])) {
      $error['phone-number'] = 'spell';
    }
    if ($_POST['birthday-month'] === '') {
      $error['birthday'] = 'blank';
    } elseif ($_POST['birthday-day'] === '') {
      $error['birthday'] = 'blank';
    }
    if ((int)$_POST['birthday-month'] === 4 && (int)$_POST['birthday-day'] === 31) {
      $error['birthday'] = 'notDay';
    } elseif ((int)$_POST['birthday-month'] === 6 && (int)$_POST['birthday-day'] === 31) {
      $error['birthday'] = 'notDay';
    } elseif ((int)$_POST['birthday-month'] === 9 && (int)$_POST['birthday-day'] === 31) {
      $error['birthday'] = 'notDay';
    } elseif ((int)$_POST['birthday-month'] === 11 && (int)$_POST['birthday-day'] === 31) {
      $error['birthday'] = 'notDay';
    } elseif ((int)$_POST['birthday-month'] === 2 && (int)$_POST['birthday-day'] === 31) {
      $error['birthday'] = 'notDay';
    } elseif ((int)$_POST['birthday-month'] === 2 && (int)$_POST['birthday-day'] === 30) {
      $error['birthday'] = 'notDay';
    } elseif ((int)$_POST['birthday-year'] % 4 !== 0 && (int)$_POST['birthday-month'] === 2 && (int)$_POST['birthday-day'] === 29) {
      $error['birthday'] = 'notDay';
    }
    if ($_POST['prefecture-id'] === '') {
      $error['prefecture-id'] = 'blank';
    }

    if (empty($error)) {
      $_SESSION['input'] = $_POST;
      header('Location: confirm.php');
      exit();
    }
  }
  
  // confirmページの戻るボタンを押した場合かつセッションおよびGETが空でない場合
  if (!empty($_GET) && !empty($_SESSION['input']) && $_GET['action'] === 'return') {
    $_POST = $_SESSION['input'];
    unset($_SESSION['input']);
  }

  // ユーザー定義関数
  function h($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  }
  // 県リスト作成
  $prefectures = $db->query('SELECT id, name FROM prefectures ORDER BY id');
  foreach ($prefectures as $pre) {
    $pre_list[(string)$pre['id']] = $pre['name'];
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

    <form action="input.php" method="post" novalidate>
      <div class="blank"></div>
<?php 
if (!empty($error)){
?>
      <p class="input-error-message">正しく入力されていない項目があります</p>
<?php }?>
      <ul class="form-list-items">
        <li class="form-list-item">
          <p class="list-item-title">お名前<span class="must">必須</span></p>
          <input type="text" placeholder="山田 太郎" name="name" maxlength="100" value="<?php if(!empty($_POST)){echo h($_POST['name']);}?>" class="name <?php if(!empty($error['name'])){ echo h('error-item');}?>">
          <p class="list-item-detail">漢字/フルネームでご記入ください</p>
<?php 
if(!empty($error['name']) && $error['name'] === 'blank') {
?>
          <p class="error">お名前は必須入力です</p>
<?php 
} elseif (!empty($error['name']) && $error['name'] === 'over') {
?>
          <p class="error">お名前は半角100文字以内で入力してください</p>
<?php }?>
        </li>
        <li class="form-list-item">
          <p class="list-item-title">ふりがな<span class="must">必須</span></p>
          <input type="text" placeholder="やまだ たろう" name="furigana" maxlength="100" value="<?php if(!empty($_POST)) {echo h($_POST['furigana']);}?>" class="furigana <?php if(!empty($error['furigana'])){ echo h('error-item');}?>">
<?php 
if(!empty($error['furigana']) && in_array('blank', $error['furigana'])) {
?>
          <p class="error">ふりがなは必須入力です</p>
<?php 
} elseif (!empty($error['furigana']) && in_array('over', $error['furigana'])) {
?>
          <p class="error">ふりがなは半角100文字以内で入力してください</p>
<?php 
} 
if (!empty($error['furigana']) && in_array('spell', $error['furigana'])) {
?>
          <p class="error">ふりがなはひらがなおよびスペースで入力してください</p>
<?php }?>
        </li>
        <li class="form-list-item">
          <p class="list-item-title">メールアドレス<span class="must">必須</span></p>
          <input type="text" placeholder="example@mail.com" name="email" maxlength="100" value="<?php if(!empty($_POST)) {echo h($_POST['email']);}?>" class="email <?php if(!empty($error['email'])){ echo h('error-item');}?>">
          <p class="list-item-detail">確認メールが届きます。入力の間違いがないようにご確認ください。</p>
<?php 
if(!empty($error['email']) && in_array('blank', $error['email'])) {
?>
          <p class="error">メールアドレスは必須入力です</p>
<?php 
} elseif (!empty($error['email']) && in_array('over', $error['email'])) {
?>
          <p class="error">メールアドレスは半角100文字以内で入力してください</p>
<?php 
} 
if (!empty($error['email']) && in_array('spell', $error['email'])) {
?>
          <p class="error">メールアドレスとして正しくありません</p>
<?php }?>
        </li>
        <li class="form-list-item">
          <p class="list-item-title">電話番号<span class="must">必須</span></p>
          <input type="text" placeholder="09012345678" name="phone-number" maxlength="11" value="<?php if(!empty($_POST)) {echo h($_POST['phone-number']);}?>" class="phone-number <?php if(!empty($error['phone-number'])){ echo h('error-item');}?>">
<?php 
if(!empty($error['phone-number']) && $error['phone-number'] === 'blank') {
?>
          <p class="error">電話番号は必須入力です</p>
<?php 
} elseif (!empty($error['phone-number']) && $error['phone-number'] === 'spell') {
?>
          <p class="error">電話番号として正しくありません</p>
<?php }?>
        </li>
        <li class="form-list-item">
          <p class="list-item-title">生年月日<span class="must">必須</span></p>
          <div class="birthday">
            <div class="year">
              <select name="birthday-year" class="birthday-year <?php if(!empty($error['birthday'])){echo h('error-item');}?>">
<?php 
if (!empty($_POST['birthday-year'])):
  for ($i = 1900; $i <= date('Y'); $i++):
    if($i === (int)$_POST['birthday-year']):
?>
                <option value="<?php echo $i;?>" selected><?php echo $i;?></option>
<?php 
    else:
?>
                <option value="<?php echo $i;?>"><?php echo $i;?></option>
<?php
    endif;
  endfor;
else:
  for ($i = 1900; $i <= date('Y') - 21; $i++):
?>
                <option value="<?php echo $i;?>"><?php echo $i;?></option>
<?php 
  endfor;
?>
                <option value="<?php echo date('Y') - 20;?>" selected><?php echo date('Y') - 20;?></option>
<?php   
  for ($i = date('Y') - 19; $i <= date('Y'); $i++):
?>
                <option value="<?php echo $i;?>"><?php echo $i;?></option>
<?php   
  endfor;
endif;
?>
              </select>
              <span>年</span>
            </div>
            <div class="month">
              <select name="birthday-month" class="birthday-month <?php if(!empty($error['birthday'])){echo h('error-item');}?>">
                <option value="" selected>-</option>
<?php 
for ($j = 1; $j <= 12; $j++):
  if(!empty($_POST['birthday-month']) && $j === (int)$_POST['birthday-month']):
?>
                  <option value="<?php echo sprintf('%02d', $j)?>" selected><?php echo sprintf('%02d', $j)?></option>
<?php   
  else:
?>
                  <option value="<?php echo sprintf('%02d', $j)?>"><?php echo sprintf('%02d', $j)?></option>
<?php   
  endif;
endfor;
?>
              </select>
              <span>月</span>
            </div>
            <div class="day">
              <select name="birthday-day" class="birthday-day <?php if(!empty($error['birthday'])){echo h('error-item');}?>">
                <option value="" selected>-</option>
<?php 
for ($k = 1; $k <= 31; $k++):
  if(!empty($_POST['birthday-day']) && $k === (int)$_POST['birthday-day']):
?>
                  <option value="<?php echo sprintf('%02d', $k)?>" selected><?php echo sprintf('%02d', $k)?></option>
<?php   
  else:
?>
                  <option value="<?php echo sprintf('%02d', $k)?>"><?php echo sprintf('%02d', $k)?></option>
<?php   
  endif;
endfor;
?>
              </select>
              <span>日</span>
            </div>
          </div>
          <p class="list-item-detail">満16歳以上の方を対象をしております。</p>
<?php 
  if(!empty($error['birthday']) && $error['birthday'] === 'blank') {
?>
          <p class="error">生年月日は必須入力です</p>
<?php 
} elseif (!empty($error['birthday']) && $error['birthday'] === 'notDay') {
?>
          <p class="error">日付として正しくありません</p>
<?php }?>
        </li>
        <li class="form-list-item">
          <p class="list-item-title">都道府県<span class="must">必須</span></p>
          <select name="prefecture-id" class="prefecture <?php if(!empty($error['prefecture'])){echo h('error-item');}?>">
            <option value="" selected>選択してください</option>
<?php 
  foreach ($pre_list as $pre_id => $pre_name):
    if (!empty($_POST['prefecture-id']) && (string)$pre_id === $_POST['prefecture-id']):
?>
            <option value="<?php echo (string)$pre_id;?>" selected><?php echo $pre_name;?></option>
<?php 
    else:
?>
            <option value="<?php echo (string)$pre_id;?>"><?php echo $pre_name;?></option>
<?php 
    endif;
  endforeach;
?>
          </select>
          <p class="list-item-detail">現在お住まいの都道府県を選択してください。</p>
<?php 
if(!empty($error['prefecture-id']) && $error['prefecture-id'] === 'blank') {
?>
          <p class="error">都道府県は必須入力です</p>
<?php }?>
        </li>
      </ul>
      <input type="submit" value="入力内容を確認" class="input-submit">
    </form>
  </main>
  
  <footer>
    <p>
      <a href="https://labot.inc/privacy-policy/">プライバシーポリシー</a>をお読みの上、同意して送信してください。
    </p>
  </footer>

</body>
</html>
