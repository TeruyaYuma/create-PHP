<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 チャット遷移ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(!empty($_POST)){
  debug('POSTされました。');
  //bordのuser_idを取得
  $bord_id = getMsgBord($u_id);
  debug('取得成功しました。');
  debug('bord_id:'.print_r($bord_id,true));
  //user_idに値が入っているなら利用者とみなしパラメータをつけて遷移
  if(!empty($bord_id)){
    debug('チャットルームへ移動します。');
    header("Location:msg.php?b_id=".$bord_id['id']);
  //user_idが空なら新規bordを作成
  } else {
    debug('bordを作成します。');

    try{
      $dbh = dbConnect();

      $sql = 'INSERT INTO bord(user_id,create_date) VALUES (:u_id,:date)';
      $data = array('u_id' => $u_id, ':date' => date('Y-m-d H:i:s'));

      $stmt = queryPost($dbh,$sql,$data);
      //bordIDをパラメータに渡して遷移
      if($stmt){
        debug('新規チャットルームへ移動します。');
        header("Location:msg.php?b_id=".$dbh->lastInsertID());
      } 

    } catch (Exception $e) {
      error_log('エラー発生:'. $e->getMessage());
    }
  }
}
 ?>

<section id="js-msg-bord" class="bord-container">
      <div class="bord-nav">
        <p>質問はコチラから</p>
        <div class="btn-container">
          <form action="" method="post">
            <input type="submit" name="submit" value="チャットルームへ"
            style="margin:0 auto; padding:0; float:none; ">
          </form>
        </div>
      </div>
    </section>