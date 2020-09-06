<!DOCTYPE html>
<html lang="ja">
<html>
<head>
    <meta charset="UTF-8">
    <title>mission5-1</title>
</head>
<body>
<?php #ここから編集番号指定フォームに記入された番号で指定された投稿を、投稿フォームに呼び出す
if(isset($_POST["edit"])){
    #編集許可パスワード
    $okpass3="tech-base3";
    //==========
    $pass3=$_POST["pass3"];
    $edit_req=$_POST["edit_req"];
    if(empty($edit_req)){
        $message3= " *編集対象番号が未記入です。";
    }else{
        if(empty($pass3)){
            $message3= " *パスワードが未記入です。";
        }elseif($pass3!=$okpass3){
            $message3=" *パスワードが誤っています。";
        }else{
            $message3=" *認証成功！";
            $dsn='データベース名';
            $user='ユーザー名';
            $password='パスワード';
            $pdo=new PDO($dsn, $user, $password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $id=intval($edit_req);
            $sql='SELECT*FROM mission_5_1_b WHERE id=:id';
            $stmt=$pdo->prepare($sql);
            $stmt ->bindParam(':id',$id,PDO::PARAM_INT);//その「差し替えるパラメータの値を指定してから、」
            $stmt ->execute();//SQLを実行する
            $results=$stmt ->fetchALl();
                foreach($results as $row){
                    //$rowの中にはカラム名を入れる。
                    $edit_num=$row['id'];
                    $edit_name=$row['name'];
                    $edit_com=$row['comment'];
                }
            $edit_message=" ＊編集中";
        }
    }
}
?>
<form action="" method="post">
    <!投稿>
    <input type="text" name="name" placeholder="名前"
        value="<?php echo $edit_name;?>"><?php echo $edit_message;?><br>
    <input type="text" name="comment" placeholder="コメント"
        value="<?php echo $edit_com;?>"><br>
    <input type="password" name="pass1" placeholder="パスワード">
    <input type="submit" name="name_com" value="送信"><br>
    <input type="hidden" name="edit_num" 
        value=<?php echo $edit_num;?>>
     <!削除><br>
     <input type="text" name="delete_num" placeholder="削除番号"><br>
     <input type="password" name="pass2" placeholder="パスワード">
     <input type="submit" name="delete" value="削除"><br>
     <!編集><br>
     <input type="text" name="edit_req" placeholder="編集対象番号"><br>
     <input type="password" name="pass3" placeholder="パスワード">
     <input type="submit" name="edit" value="編集"><br>
    <br>
<?php echo $message3;?><! ここに改行は必要か否か>
</form>
<?php
    #まずは接続
    $dsn='データベース名';
    $user='ユーザー名';
    $password='パスワード';
    $pdo=new PDO($dsn, $user, $password,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    #次に、テーブルを作成
    $sql="CREATE TABLE IF NOT EXISTS mission_5_1_b
    (id INT AUTO_INCREMENT PRIMARY KEY,name char(32),".
    "comment TEXT,created DATETIME);";
        //SQLステートメントを$sqlに格納した。
        //このSQLステートメント、一つでも記法を間違えるとうまく動かないので注意
    $stmt =$pdo ->query($sql);

#ここから投稿プロセスに入る
#まずは条件分岐から
#編集プロセスも、投稿プロセスと同じphp内に作る。
if(isset($_POST["name_com"])){
    #投稿フォームのパスワード
    $okpass1="tech-base1";
    //==========
    $pass1=$_POST["pass1"];
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $edit_num=$_POST["edit_num"];
    
    if((empty($name)||empty($comment))&&empty($edit_num)){
        echo " 名前またはコメントが未記入です。<br>";
    }elseif(empty($edit_num)){
        if(empty($pass1)){
            echo " パスワードが未記入です。<br>";
        }elseif($pass1!=$okpass1){
            echo " パスワードが誤っています。<br>";
        }else{
            echo " 認証成功！/ ";
            $date=date("Y/m/d H:i:s");
            #以下、DBを用いた投稿プロセスの始まり
            #まずはテーブルレコードの挿入から
            $sql=$pdo ->prepare("INSERT INTO mission_5_1_b (name,comment,created)VALUES
                (:name, :comment,:created)");
            $sql ->bindParam(':name',$name, PDO::PARAM_STR);
            $sql ->bindParam(':comment',$comment,PDO::PARAM_STR);
            $sql ->bindParam(':created',$date,PDO::PARAM_STR);
            $sql ->execute();
            echo "投稿完了<br>";
        }
    }else{
        #これ以降が編集プロセス
        if(empty($pass1)){
            echo " パスワードが未記入です。<br>"; 
        }elseif($pass1!=$okpass1){
            echo " パスワードが誤っています。<br>";
        }else{
            echo " 認証成功！/ ";
            $revised_n=$name;
            $revised_c=$comment;
            $e_date=date("Y/m/d H:i:s");
            $id=intval($edit_num);//編集する投稿の投稿番号
            $sql='UPDATE mission_5_1_b SET name=:revised_n,comment=:revised_c,created=:e_date
                WHERE id=:id';
            $stmt= $pdo ->prepare($sql);
            $stmt ->bindParam(':revised_n',$revised_n,PDO::PARAM_STR);
            $stmt ->bindParam(':revised_c',$revised_c,PDO::PARAM_STR);
            $stmt ->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt ->bindParam(':e_date',$e_date,PDO::PARAM_STR);
            $stmt ->execute();
            echo "編集完了<br>";
        }
    }
}    
    
#ここから削除プロセスに入る
if(isset($_POST["delete"])){
    #削除フォームのパスワード
    $okpass2="tech-base2";
    //==========
    $pass2=$_POST["pass2"];
    $delete_num=$_POST["delete_num"];
    if(empty($delete_num)){
        echo " 削除対象番号を記入してください。<br>";
    }else{
        if(empty($pass2)){
            echo " パスワードが未記入です。<br>";
        }elseif($pass2!=$okpass2){
            echo " パスワードが誤っています。<br>";
        }else{
            echo " 認証成功！/ ";
            $id = intval($delete_num);//レコード番号に、削除指定番号を代入
            $sql = 'delete from mission_5_1_b where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            echo "削除完了<br>";
        }
    }
}

    
#入力されたデータレコードを抽出し、表示する    
    #この部分は、特にどの条件分岐にも入っていないので、ボタン操作をしなくても、
    //初めから表示されている。
echo"<br>";

$sql='SELECT*FROM mission_5_1_b';
$stmt=$pdo ->query($sql);
$results=$stmt ->fetchALL();
    foreach($results as $row){
        echo '('.$row['id'].')'.',';
        echo '{'.$row['name'].'}'.',';
        echo '「'.$row['comment'].'」'.',';
        echo '['.$row['created'].']'.'<br>';
    echo "<hr>";
    }
    
?>