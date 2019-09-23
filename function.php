<?php 
//===========================
// ログ
//===========================
ini_set('log_errors','On');
ini_set('error_log','php.log');

//===========================
// セッション
//===========================
//セッションファイルの置き場を変更する(/var/tmp/以下に)
session_save_path("/var/tmp/");
//ガーベージコレクションが削除するセッションの有効期限を設定
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を伸ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える
session_regenerate_id();

//===========================
//  デバッグ関数
//===========================
//debug_flgがtrueの時 debug関数でログをとる
$debug_flg = 'true';
function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}
//画面表示開始ログ吐き出し関数
function debugLogStart(){
    debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
    debug('セッションID:'.session_id());
    debug('セッション変数の中身:'.print_r($_SESSION,true));
    debug('現在日時のスタンプ:'.time());
    if(!empty($_SESSION['login_time']) && !empty($_SESSION['login_limit'])){
        debug( 'ログイン期限日時タイムスタンプ：'.( $_SESSION['login_time'] + $_SESSION['login_limit'] ) );
    }
}

//===========================
// 定数
//===========================
//エラーメッセージ
define('MSG01','未入力です。');
define('MSG02','emailの形式で入力してください。');
define('MSG03','半角英数字で入力してください。');
define('MSG04','最大文字数を超えています。');
define('MSG05','6文字以上で入力してください。');
define('MSG06','パスワードとパスワード再入力が違います。');
define('MSG07','そのemailは既に登録されています。');
define('MSG08','エラーが出ました。時間を置いてからやり直してください。');
define('MSG09','Emailかパスワードが間違っています。');

//===========================
// グローバル変数
//===========================
$err_msg = array();
//===========================
// フォームデータ関数
//===========================
//フォーム入力保持
function saveFormStr($str){
    if(!empty($_POST[$str])){
        echo $_POST[$str];
    }
}
//クラス err
function getErrMsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}
//===========================
// バリデーション関数
//===========================
//未入力チェック
function validRequire($str,$key){
    if($str === ''){
        global $err_msg;
        $err_msg[$key] = MSG01;
    }
}
//email形式チェック
function validEmail($str,$key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG02;
    }
}
//email重複チェック
function validEmailDup($email){
    global $err_msg;
    try{
        $dbh = dbConnect();

        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);

        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty(array_shift($result))){
            $err_msg['email'] = MSG07;
        }
    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG08;
    }
}
//半角英数字チェック
function validHalf($str,$key){
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}
//最大文字数チェック
function validMaxLen($str,$key,$max = 256){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}
//最小文字数チェック
function validMinLen($str,$key,$min = 6){
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}
function validMatch($str1,$str2,$key){
    if(!$str1 === $str2){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}
//===========================
// データベース
//===========================
//DB接続関数
function dbConnect(){
    $dsn = 'mysql:dbname=portoforio_r;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );

    $dbh = new PDO($dsn,$user,$password,$options);
    return $dbh;
}
//SQL実行関数
function queryPost($dbh, $sql, $data){
    $stmt = $dbh->prepare($sql);
    
    if(!$stmt->execute($data)){
        debug('失敗しました。');
        debug('SQL:'.print_r($stmt->errorInfo(),true));
        return 0;
    }
    debug('クエリ成功');
    return $stmt;
}
//bord取得関数
function getMsgBord($u_id){
    debug('bord情報を取得します。');
    debug('ユーザーID:'.$u_id);

    try{
        $dbh = dbConnect();
        debug('DB接続成功');
        $sql = 'SELECT id FROM bord WHERE user_id = :u_id';
        $data = array(':u_id' => $u_id);
        debug('SQL成功');
        $stmt = queryPost($dbh, $sql, $data);
        debug('$stmt:'.print_r($stmt,true));
        if($stmt){
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            debug('$result:'.print_r($result,true));
            return $result;
        } else {
            return false;
            debug('何も入っていません。');
        }
    } catch (Exception $e) {
        error_log('エラー発生:'. $e->getMessage());
    }
}
//msg表示用データ取得関数
function getMsgAndBord($b_id){
    debug('msg情報を取得します。');
    debug('bord_id:'.$b_id);

    try{
        $dbh = dbConnect();

        $sql = 'SELECT m.id AS m_id, bord_id, send_date, to_user, from_user, msg, b.master_id, b.user_id, b.create_date FROM message AS m RIGHT JOIN bord AS b ON b.id = m.bord_id WHERE b.id = :id ORDER BY send_date ASC';
        $data = array(':id' => $b_id);

        $stmt = queryPost($dbh,$sql,$data);
        debug('msgAndBord:'.print_r($stmt,true));
        if($stmt){
            return $stmt->fetchAll();
        } else {
            return false;
        }
        
    } catch (Exception $e) {
        error_log('エラー発生:'. $e->getMessage());
    }
}
//自分のmsgとbord情報を取得
function getMyMsgAndBord($u_id){
    debug('自分のMsgAndBord情報を取り出します。');
    debug('$u_id:'.$u_id);

    try{
        $dbh = dbConnect();

        $sql = 'SELECT * FROM bord WHERE master_id = :u_id OR user_id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);

        $stmt = queryPost($dbh,$sql,$data);

        $rst = $stmt->fetchAll();

        if(!empty($rst)){
            foreach($rst as $key => $val){
                $sql = 'SELECT * FROM message WHERE bord_id = :id AND delete_flg = 0 ORDER BY send_date DESC';
                $data = array(':id' => $val['id']);

                $stmt = queryPost($dbh, $sql, $data);

                $rst[$key]['msg'] = $stmt->fetchAll();
            }
        }

        if($stmt){
            return $rst;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生:'. $e->getMessage());
    }
}
//ユーザー情報取り出し関数
function getUser($u_id){
    debug('ユーザー情報を取り出します。');
    debug('ユーザーID:'.$u_id);

    try{
        $dbh = dbConnect();

        $sql = 'SELECT * FROM users WHERE id = :id AND delete_flg = 0';
        $data = array(':id' => $u_id);

        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }

    } catch (Exception $e) {
        error_log('エラー発生:'. $e->getMessage());
    }
}
//product取得関数
function getMyProduct(){
    debug('プロダクトを取得します。');

    try{
        $dbh = dbConnect();

        $sql = 'SELECT * FROM product';
        $data = array();

        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
            return $stmt->fetchAll();
        } else {
            return false;
        }

    } catch (Exception $e){
        error_log('エラー発生：'. $e->getMessage());
    }
}
//最新のproductを３件取得
function getNewProduct(){
    debug('最新の３件を取得します。');
    try{
        $dbh = dbConnect();

        $sql = 'SELECT * FROM product ORDER BY id DESC LIMIT 3 OFFSET 0 ';
        $data = array();
        
        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
            return $rst = $stmt->fetchAll();
        } else {
            return false;
            debug('取得できませんでした。');
        }

    } catch (Exception $e) {
        error_log('エラー発生：'. $e->getMessage());
    }
}
//件数用、ページング用プロダクトリスト取得
function getProductList($currentMinNum = 1, $category, $sort, $span = 20){
    debug('商品情報を取得します。');
    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // 件数用のSQL文作成
      $sql = 'SELECT id FROM product';
      if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
      if(!empty($sort)){
        switch($sort){
          case 1:
            $sql .= ' ORDER BY price ASC';
            break;
          case 2:
            $sql .= ' ORDER BY price DESC';
            break;
        }
      } 
      $data = array();
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      $rst['total'] = $stmt->rowCount(); //総レコード数
      debug('total:'.print_r($rst['total'],true));
      $rst['total_page'] = ceil($rst['total']/$span); //総ページ数
      if(!$stmt){
        return false;
      }
      
      // ページング用のSQL文作成
      $sql = 'SELECT * FROM product';
      if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
      if(!empty($sort)){
        switch($sort){
          case 1:
            $sql .= ' ORDER BY price ASC';
            break;
          case 2:
            $sql .= ' ORDER BY price DESC';
            break;
        }
      } 
      $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
      $data = array();
      debug('SQL：'.$sql);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
  
      if($stmt){
        // クエリ結果のデータを全レコードを格納
        $rst['data'] = $stmt->fetchAll();
        return $rst;
      }else{
        return false;
      }
  
    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
    }
  }
//カテゴリーデータを取得します。
function getCategory(){
    debug('カテゴリーを取得します。');

    try{
        $dbh = dbConnect();

        $sql ='SELECT * FROM category';
        $data = array();
        
        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
            return $stmt->fetchAll();
        } else {
            return false;
        }

    } catch (Exception $e) {
        error_log('エラー発生：'. $e->getMessage());
    }
}
//=================================
// メール送信
//=================================
function sendMail($from,$subject,$name,$comment){
    if(!empty($from) && !empty($comment)){
        debug('メール送信開始。');
        mb_language("Japanese");
        mb_internal("UTF-8");

        $to = 'ky19860120@gmail.com';

        $result = mb_send_mail($to,$subject,$comment,"From:".$from);

        if($result){
            unset($_POST);
            $msg = 'メール送信されました。';
        } else{
            $msg = '失敗しました。';
        }
    }
}
//=================================
// その他
//=================================
//サニタイズ
function sanitize($str){
    return htmlspecialchars($str,ENT_QUOTES);
}
//画像アップロード
function uploadImg($file){
    debug('画像アップロードを開始します。');
    debug('$pic'.print_r($file,true));

    if(isset($file['error']) && is_int($file['error'])){
        try{
            switch($file['error']){
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('ファイルが選択されていません');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('画像のサイズが大きすぎます');
                default:
                    throw new RuntimeExeption('その他のエラーが発生しました');
            }

            $type = @exif_imagetype($file['tmp_name']);
            if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true)){
                throw new RuntimeException('画像形式が未対応');
            }

            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            if(!move_uploaded_file($file['tmp_name'],$path)){
                throw new RuntimeException('ファイル保存時にエラーが発生しました。');
            }

            chmod($path,0644);

            debug('ファイルは正常にアップロードされました。');
            debug('ファイルパス：'.$path);
            return $path;

        } catch (RuntimeException $e) {
            error_log('ファイルエラー発生：'. $e->getMessage());
        }
    }
}
//ページネーション
//ページング
// $currentPageNum : 現在のページ数
// $totalPageNum : 総ページ数
// $link : 検索用GETパラメータリンク
// $pageColNum : ページネーション表示数
function pagination( $currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){
    // 現在のページが、総ページ数と同じ　かつ　総ページ数が表示項目数以上なら、左にリンク４個出す
    if( $currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
      $minPageNum = $currentPageNum - 4;
      $maxPageNum = $currentPageNum;
    // 現在のページが、総ページ数の１ページ前なら、左にリンク３個、右に１個出す
    }elseif( $currentPageNum == ($totalPageNum-1) && $totalPageNum > $pageColNum){
      $minPageNum = $currentPageNum - 3;
      $maxPageNum = $currentPageNum + 1;
    // 現ページが2の場合は左にリンク１個、右にリンク３個だす。
    }elseif( $currentPageNum == 2 && $totalPageNum > $pageColNum){
      $minPageNum = $currentPageNum - 1;
      $maxPageNum = $currentPageNum + 3;
    // 現ページが1の場合は左に何も出さない。右に５個出す。
    }elseif( $currentPageNum == 1 && $totalPageNum > $pageColNum){
      $minPageNum = $currentPageNum;
      $maxPageNum = 5;
    // 総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを１に設定
    }elseif($totalPageNum < $pageColNum){
      $minPageNum = 1;
      $maxPageNum = $totalPageNum;
    // それ以外は左に２個出す。
    }else{
      $minPageNum = $currentPageNum - 2;
      $maxPageNum = $currentPageNum + 2;
    }
    
    echo '<div class="pagination">';
      echo '<ul class="pagination-list">';
        if($currentPageNum != 1){
          echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
        }
        for($i = $minPageNum; $i <= $maxPageNum; $i++){
          echo '<li class="list-item ';
          if($currentPageNum == $i ){ echo 'active'; }
          echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
        }
        if($currentPageNum != $maxPageNum && $maxPageNum > 1){
          echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
        }
      echo '</ul>';
    echo '</div>';
  }