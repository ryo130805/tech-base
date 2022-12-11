<?php
    //以下データベースの接続情報ですが、不正防止のため書き換えてあります。
    //データソースネーム　識別子である。
    $dsn = 'mysql:dbname="データベース名";host=localhost';
    //ユーザー名。
    $user = 'ユーザー名';
    //パスワード。
    $password = 'パスワード';
    //$pdoはPHPデータベースオブジェクト。これでphpを介してデータベースにアクセスすることが出来る。array以下は
    //エラー分を出してくれるようにする命令。
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //M4-2
    //データベース名や列名(カラム名)にはダブルクォーテーションを使う。
    //SQLでは基本シングルクォーテーションを使う。
    //最初にテーブルを作成。テーブル名に-は使えない。_アンダーバーとかならいい。
    $sql = "CREATE TABLE IF NOT EXISTS tbm5_1 "
    //ここから下は、カラム名　データ型　オプション(今はない)の順で並んでいる。
    //そして、このデータ型が重要。今までinput type = "text,number,password"と
    //してきた部分に該当すると思われる。つまり、ここでデータの形が変わる。
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date TIMESTAMP,"
    . "password char(32)"
    .");";
    //$stmtはステイトメントオブジェクト。$pdoを介して、query(命令)で$sqlを実行するので、その結果を格納するために用意してある。
    $stmt = $pdo->query($sql);
    
    // //M4-3
    // //今あるテーブルを確認できる。
    // $sql ='SHOW TABLES';
    // //$pdo大事。これで命令を出しているから。
    // $result = $pdo -> query($sql);
    // foreach ($result as $row){
    //     echo $row[0];
    //     echo '<br>';
    // }
    // echo "<hr>";
    
    //M4-4
    // $sql = "SHOW CREATE TABLE tbm5_1";
    // $result = $pdo -> query($sql);
    // foreach ($result as $row){
    //     echo $row[1];
    // }
    // echo "<hr>";
    
    //＊＊＊＊＊テーブル削除＊＊＊＊＊＊
    // $sql = 'DROP TABLE tbm5_1';
    // $stmt = $pdo->query($sql);
    
    //変数まとめ
    $name = filter_input(INPUT_POST,"name");
    $comment = filter_input(INPUT_POST,"comment");
    $password = filter_input(INPUT_POST,"password");
        
    $delete = filter_input(INPUT_POST,"delete");
    $delpass = filter_input(INPUT_POST,"delpass");
    
    $edit = filter_input(INPUT_POST,"edit");
    $editpass = filter_input(INPUT_POST,"editpass");
    
    $editnumber = filter_input(INPUT_POST,"editnumber");
    
    
    //新規投稿機能
    //if文で$nameと$commentと$passwordが空じゃないときに動作するようにする。これまでと同じ。
    if(!empty($name) && !empty($comment) && !empty($password) && empty($editnumber)){
        
        $sql = $pdo -> prepare("INSERT INTO tbm5_1 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':password', $password, PDO::PARAM_STR);
        //以下も、これまでと同じようにPOSTでフォームからの入力を受け取る。
        $name = filter_input(INPUT_POST,"name");
        $comment = filter_input(INPUT_POST,"comment");
        $date = date('Y-m-d H:i:s');
        $password = filter_input(INPUT_POST,"password");
        $sql -> execute();
    }
    
    //削除機能
    //if文で$deleteと$delpassが空じゃないときに動作するようにする。これまでと同じ。
    if(!empty($delete) && !empty($delpass)){
        
        //ここで、$deleteと$delpassを変数に代入。
        $id = $delete;
        $password = $delpass;
        //DELETE文でテーブルを指定し、WHEREを使う。SQLではWHEREはif文と同じ役割なので、
        //これは、idが$deleteかつpasswordが$delpassの時に削除するという命令になっている。
        $sql = 'DELETE FROM tbm5_1 WHERE id=:id AND password=:password';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();
        
    }
    
    //編集機能
    //if文で$editと$editpassが空じゃないときに動作するようにする。これまでと同じ。
    if(!empty($edit) && !empty($editpass)){
        
        //SELECT文でテーブルを取得し、$pdoで$sqlを命令。fetchAll()でテーブルを$resultに代入しておく。
        $sql = 'SELECT * FROM tbm5_1';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        
        //foreach文でテーブルのレコード(行)を一行ずつ取り出し、idとpasswordが$editと$editpassと同じだった場合に、
        //これまでと同じように、idやnameを変数に入れる。
        foreach ($results as $row){
            if($row["id"] == $edit && $row["password"] == $editpass){
            //$rowの中にはテーブルのカラム名が入る
            $newnumber = $row["id"];
            $newname = $row['name'];
            $newcomment = $row['comment'];
            $newpassword = $row['password'];
            }
        } 
    }
        
        //$editnumberが空じゃないときに動作するようにする。もし、上のif($row["id"] == $edit && $row["password"] == $editpass)
        //という部分が正しくて、$newnumberにidが入っていたら、フォームのname = "editnumber"のところに入っていることになる。
        if(!empty($editnumber)){
            
            //ここで$idに代入するのは、$editnumber。$editnumberというのはidと同義なので、これでも投稿番号の部分(id)と言える。
            //$editnumberは編集するときの固有の変数なので、これを使う。
            $id = $editnumber;
            //新たにフォームの値を取得するように書く。
            $name = filter_input(INPUT_POST,"name");
            $comment = filter_input(INPUT_POST,"comment");
            $date = date('Y-m-d H:i:s');
            $password = filter_input(INPUT_POST,"password");
            //ここでUPDATEを使って更新する。
            $sql = 'UPDATE tbm5_1 SET id=:id,name=:name,comment=:comment,date=:date,password=:password WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->execute();
        }
    
    //ここでブラウザにテーブルを表示。
    $sql = 'SELECT * FROM tbm5_1'; 
    $stmt = $pdo -> query($sql);
    $result = $stmt -> fetchAll();
    foreach($result as $row){
        echo $row['id'].'';
        echo $row['name'].'';
        echo $row['comment'].'';
        echo $row['date'].'';
        echo $row['password'].'<br>';
    echo "<hr>";
    }
    
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
    
    <!-- フォームの作成はいつも通り -->
    <form action = "" method = "post">
    <input type = "text" name = "name" placeholder = "名前を入力" value = <?php if(!empty($newname)){
                                                                                    $sql = 'SELECT * FROM tbm5_1';
                                                                                    $stmt = $pdo->query($sql);
                                                                                    $results = $stmt->fetchAll();
        
                                                                                    foreach ($results as $row){
                                                                                        if($row["id"] == $edit && $row["password"] == $editpass){
                                                                                            echo $newname;
                                                                                        }
                                                                                    }
                                                                                }
                                                                                ?>>
                                                                                
    <br>
    <input type = "text" name = "comment" placeholder = "コメントを入力" value = <?php if(!empty($newcomment)){
                                                                                            $sql = 'SELECT * FROM tbm5_1';
                                                                                            $stmt = $pdo->query($sql);
                                                                                            $results = $stmt->fetchAll();
        
                                                                                            foreach ($results as $row){
                                                                                                if($row["id"] == $edit && $row["password"] == $editpass){
                                                                                                    echo $newcomment;
                                                                                                }
                                                                                            }
                                                                                        }   
                                                                                        ?>>
    <br>
    <input type = "password" name = "password" placeholder = "パスワードを入力" value = <?php if(!empty($newpassword)){
                                                                                                    $sql = 'SELECT * FROM tbm5_1';
                                                                                                    $stmt = $pdo->query($sql);
                                                                                                    $results = $stmt->fetchAll();
        
                                                                                                    foreach ($results as $row){
                                                                                                    if($row["id"] == $edit && $row["password"] == $editpass){
                                                                                                        echo $newpassword;
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                            ?>>
    <input type = "hidden" name = "editnumber" value = <?php if(!empty($newnumber)){
                                                                    $sql = 'SELECT * FROM tbm5_1';
                                                                    $stmt = $pdo->query($sql);
                                                                    $results = $stmt->fetchAll();
        
                                                                    foreach ($results as $row){
                                                                        if($row["id"] == $edit){
                                                                            echo $newnumber;
                                                                        }
                                                                    }
                                                                }
                                                                ?>>
    <input type = "submit" name = "submit">
    </form>
    
    <br>
    <form action = "" method = "post">
        <input type = "number" name = "delete" placeholder = "削除対象番号">
        <input type = "password" name = "delpass" placeholder = "パスワードを入力">
        <input type = "submit" value = "削除"　><!--inputタグのnameは一緒のを使ってはいけない-->
    </form>
    
    <br>
    <form action = "" method = "post">
        <input type = "number" name = "edit" placeholder = "編集対象番号">
        <input type = "password" name = "editpass" placeholder = "パスワードを入力" >
        <input type = "submit" value = "編集">
    </form>

</body>
</html>