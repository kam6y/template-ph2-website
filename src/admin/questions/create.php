<?php

require(dirname(__FILE__) . '/../../db/pdo.php');

session_start();

if (!isset($_SESSION['id'])) {
  header('Location: /admin/auth/signin.php');
  exit;
} else {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
      // ファイルアップロードのバリデーション
      if (!isset($_FILES['image']) || $_FILES['image']['error'] != UPLOAD_ERR_OK) {
        throw new Exception("ファイルがアップロードされていない、またはアップロードでエラーが発生しました。");
      }

      // ファイルサイズのバリデーション
      if ($_FILES['image']['size'] > 5000000) {
        throw new Exception("ファイルサイズが大きすぎます。");
      }

      // 許可された拡張子かチェック
      $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
      $file_parts = explode('.', $_FILES['image']['name']);
      $file_ext = strtolower(end($file_parts));
      if (!in_array($file_ext, $allowed_ext)) {
        throw new Exception("許可されていないファイル形式です。");
      }

      // ファイルの内容が画像であるかをチェック
      $allowed_mime = array('image/jpeg', 'image/png', 'image/gif');
      $file_mime = mime_content_type($_FILES['image']['tmp_name']);
      if (!in_array($file_mime, $allowed_mime)) {
        throw new Exception("許可されていないファイル形式です。");
      }

      $image_name = uniqid(mt_rand(), true) . '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
      $image_path = dirname(__FILE__) . '/../../assets/img/quiz/' . $image_name;
      move_uploaded_file($_FILES['image']['tmp_name'], $image_path);

      $stmt = $dbh->prepare("INSERT INTO questions(content, image, supplement) VALUES(:content, :image, :supplement)");
      $stmt->execute([
        "content" => $_POST["content"],
        "image" => $image_name,
        "supplement" => $_POST["supplement"]
      ]);
      $lastInsertId = $dbh->lastInsertId();

      $stmt = $dbh->prepare("INSERT INTO choices(name, valid, question_id) VALUES(:name, :valid, :question_id)");

      for ($i = 0; $i < count($_POST["choices"]); $i++) {
        $stmt->execute([
          "name" => $_POST["choices"][$i],
          "valid" => (int)$_POST['correctChoice'] === $i + 1 ? 1 : 0,
          "question_id" => $lastInsertId
        ]);
      }

      $_SESSION['message'] = "問題作成に成功しました。";
      header('Location: /admin/index.php');
      exit;
    } catch (PDOException $e) {
      $_SESSION['message'] = "問題作成に失敗しました。";
      error_log($e->getMessage());
      exit;
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
  <title>POSSE 管理画面ダッシュボード</title>
  <!-- スタイルシート読み込み -->
  <link rel="stylesheet" href="../assets/styles/common.css">
  <link rel="stylesheet" href="../admin.css">
  <!-- Google Fonts読み込み -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&family=Plus+Jakarta+Sans:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
  <?php include(dirname(__FILE__) . '/../../components/admin/header.php'); ?>
  <div class="wrapper">
    <?php include(dirname(__FILE__) . '/../../components/admin/sidebar.php'); ?>
    <main>
      <div class="container">
        <h1 class="mb-4">問題作成</h1>
        <form class="question-form" method="POST" enctype="multipart/form-data">
          <div class="mb-4">
            <label for="question" class="form-label">問題文:</label>
            <input type="text" name="content" id="question" class="form-control required" placeholder="問題文を入力してください" />
          </div>
          <div class="mb-4">
            <label class="form-label">選択肢:</label>
            <div class="form-choices-container">
              <input type="text" name="choices[]" class="required form-control mb-2" placeholder="選択肢1を入力してください">
              <input type="text" name="choices[]" class="required form-control mb-2" placeholder="選択肢2を入力してください">
              <input type="text" name="choices[]" class="required form-control mb-2" placeholder="選択肢3を入力してください">
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label">正解の選択肢</label>
            <div class="form-check-container">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="correctChoice" id="correctChoice1" checked value="1">
                <label class="form-check-label" for="correctChoice1">
                  選択肢1
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="correctChoice" id="correctChoice2" value="2">
                <label class="form-check-label" for="correctChoice2">
                  選択肢2
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="correctChoice" id="correctChoice2" value="3">
                <label class="form-check-label" for="correctChoice2">
                  選択肢3
                </label>
              </div>
            </div>
          </div>
          <div class="mb-4">
            <label for="question" class="form-label">問題の画像</label>
            <input type="file" name="image" id="image" class="form-control required" placeholder="問題文を入力してください" />
          </div>
          <div class="mb-4">
            <label for="question" class="form-label">補足:</label>
            <input type="text" name="supplement" id="supplement" class="form-control" placeholder="補足を入力してください" />
          </div>
          <button type="submit" disabled class="btn submit">作成</button>
        </form>
      </div>
    </main>
  </div>
  <script>
    const submitButton = document.querySelector('.btn.submit')
    const inputDoms = Array.from(document.querySelectorAll('.required'))
    inputDoms.forEach(inputDom => {
      inputDom.addEventListener('input', event => {
        const isFilled = inputDoms.filter(d => d.value).length === inputDoms.length
        submitButton.disabled = !isFilled
      })
    })
  </script>
</body>

</html>