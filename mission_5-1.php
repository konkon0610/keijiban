<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>

<body>
    <h1>掲示板</h1>
    <!--送信ホーム作成-->
    <form action="" method="post">
        <h2>投稿</h2>
        <p>投稿内容書いてね</p>
        <input type="text" name="name" placeholder="名前">
        <input type="text" name="coment" placeholder="コメント"><br>
        <input type="text" name="pass" placeholder="パスワード"><br>
        <input type="submit" name="submit" value="投稿">

        <h2>編集</h2>
        <p>編集する内容書いてね</p>
        <input type="text" name="editnumber" placeholder="編集番号"><br>
        <input type="text" name="editname" placeholder="名前">
        <input type="text" name="editcoment" placeholder="コメント"><br>
        <input type="text" name="editpass" placeholder="パスワード"><br>
        <input type="submit" name="edit" value="編集">

        <h2>削除</h2>
        <input type="text" name="deletenumber" placeholder="削除番号"><br>
        <input type="text" name="deletepass" placeholder="パスワード"><br>
        <input type="submit" name="delete" value="削除"><br>
        <hr size="5" align="left">
        <h2>内容↓</h2>
    </form>




    <?php
    // データベースに接続
    $dsn = 'mysql:dbname=tb***db;host=localhost';
    $user = 'tb-240***';
    $password = '****';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    // echo "接続完了<br>";

    // テーブル作成
    // 実行するにはquery()使う
    $sql = "CREATE TABLE IF NOT EXISTS tbtb "
        . " ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "coment TEXT,"
        . "pass char(32),"
        . "date TEXT"
        . ");";
    $stmt = $pdo->query($sql);
    // echo  "テーブル作成<br>"; 

    ?>

    <?php
    //投稿機能
    // ボタンが押された時
    if (!empty($_POST["submit"])) {
        // パスワードのデータ受信
        $pass = $_POST["pass"];
        //もしパスワードが記入されていたらデータを受信してデーアタベースに保存する 
        if (!empty($pass)) {
            $name = $_POST["name"];
            $coment = $_POST["coment"];
            // INSERT文を変数に格納
            // データを挿入するINSERT文を変数に格納する（値を格納するVALUESの部分は後から値を入れる）
            $sql = "INSERT INTO tbtb (name,coment,pass,date) 
            VALUES(:name,:coment,:pass,now())";
            // 挿入する値は空のまま、SQL実行の準備をする
            $stmt = $pdo->prepare($sql);
            // 値をセット
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':coment', $coment);
            $stmt->bindParam(':pass', $pass);
            // 実行
            $stmt->execute();
            echo "新規投稿完了" . "<br>";
        }
    }
    // 編集機能
    elseif (!empty($_POST["edit"])) {
        // 編集番号を受信
        $editnumber = $_POST["editnumber"];
        // パスワードが記入された時
        if (empty($_POST["editpass"]) == false) {
            // データベースから投稿番号とパスワード取得
            $editpass = $_POST["editpass"];
            $id = $editnumber; // idがこの値のデータだけを抽出したい、とする
            $sql = 'SELECT * FROM tbtb WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();
            $results = $stmt->fetchAll();
            //   行ごとにループ
            foreach ($results as $row) {
                // もしパスワードが一致していたら
                if ($row["pass"] === $editpass) {
                    // 編集する名前とコメントのデーたをフォームから受信
                    $name = $_POST["editname"];
                    $coment = $_POST["editcoment"];
                    // データを更新する→（UPDATE テーブル名 SET カラム名 = 値 WHERE 条件;）更新したいデータのみ設定
                    $sql = 'UPDATE tbtb SET name=:name,coment=:coment,date=now() WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    // $stmt->bindParam(':id', $id,PDO::PARAM_INT);
                    // $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    // $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

                    // 更新したいデータを設定
                    $params = array(':id' => $id, ':name' => $name, ':coment' => $coment);
                    $stmt->execute($params);
                    echo "更新完了<br>";
                } else {
                    echo "パスワードが違います！！！<br>";
                }
            }
        }
    }

    //削除機能 
    elseif (!empty($_POST["delete"])) {
        // 削除番号を受信
        $deletenumber = $_POST["deletenumber"];
        // パスワードが記入されている時のみ
        if (empty($_POST["deletepass"]) == false) {
            // フォームのパスワード受信
            $deletepass = $_POST["deletepass"];
            // データベースから投稿番号とパスワード取得
            $id = $deletenumber;
            $sql = 'SELECT * FROM tbtb WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();
            $results = $stmt->fetchAll();
            //   行ごとにループ
            foreach ($results as $row) {
                // もしパスワードが一致していたら
                if ($row["pass"] === $deletepass) {
                    // SQL文をセット
                    $stmt = $pdo->prepare('DELETE FROM tbtb WHERE id = :id');
                    // 削除するレコードのIDを配列に格納する
                    $params = array(':id' => $id);
                    // 削除するレコードのIDが入った変数をexecuteにセットしてSQLを実行
                    $stmt->execute($params);
                    // 削除完了のメッセージ
                    echo '削除完了<br>';
                } else {
                    echo "パスワードが違います！！！<br>";
                }
            }
        }
    }

    #テーブルデータの表示
    $sql = 'SELECT * FROM tbtb';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();

    foreach ($results as $row) {
        echo $row['id'] . ' '; //投稿番号
        echo $row['name'] . ' '; //名前
        echo $row['coment'] . ' '; //コメント
        echo $row['date'] . ' <br>'; //【追加】日時
        // echo $row['pass'].'<br>';
    }



    ?>



</body>

</html>