<?php
require __DIR__ . '/../../db/dbconnect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // バリデーション
  if (empty($_POST['email'])) {
    $message = 'メールアドレスは必須項目です。';
  } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $message = '正しいEメールアドレスを指定してください。';
  } elseif (empty($_POST['password'])) {
    $message = 'パスワードは必須項目です。';
  } else {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // データベースへの接続
    $stmt = $dbh->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    // ユーザーが存在し、パスワードが正しいか確認
    if ($user && password_verify($password, $user["password"])) {
      session_start();
      $_SESSION['id'] = $user["id"];
      header('Location: ../index.php');
      exit();
    } else {
      // 認証失敗: エラーメッセージをセット
      $message = 'メールアドレスまたはパスワードが間違っています。';
    }
  }
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>POSSE ログイン</title>
  <!-- スタイルシート読み込み -->
  <link rel="stylesheet" href="./../assets/styles/common.css">
  <link rel="stylesheet" href="./../admin.css">
  <!-- Google Fonts読み込み -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&family=Plus+Jakarta+Sans:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
  <?php include __DIR__ . '/../../components/admin/header.php'; ?>
  <div class="wrapper">
    <main>
      <div class="container">
        <h1 class="mb-4">ログイン</h1>
        <?php if ($message !== '') { ?>
          <p style="color: red;"><?= $message ?></p>
        <?php }; ?>
        <form method="POST">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="email form-control" id="email">
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" name="password" id="password" class="form-control">
          </div>
          <button type="submit" disabled class="btn submit">ログイン</button>
        </form>
      </div>
    </main>
  </div>
  <script>
    const EMAIL_REGEX = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
    const submitButton = document.querySelector('.btn.submit')
    const emailInput = document.querySelector('.email')
    inputDoms = Array.from(document.querySelectorAll('.form-control'))
    inputDoms.forEach(inpuDom => {
      inpuDom.addEventListener('input', event => {
        const isFilled = inputDoms.filter(d => d.value).length === inputDoms.length
        submitButton.disabled = !(isFilled && EMAIL_REGEX.test(emailInput.value))
      })
    })
  </script>
</body>

</html>