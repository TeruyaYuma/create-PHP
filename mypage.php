<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

$u_id = $_SESSION['user_id'];
$myMsgAndBord = getMyMsgAndBord($u_id);
debug('mymsgandbord:'.print_r($myMsgAndBord,true));
$category = getCategory();
debug('category:'.print_r($category,true));

if(!empty($_POST)){
    debug('POSTされました。');
    debug('$_POST:'.print_r($_POST,true));
    debug('$_FILE:'.print_r($_FILES,true));

    $name = $_POST['name'];
    $category = $_POST['category'];
    debug('カテゴリーID:'.print_r($category,true));
    $comment = $_POST['comment'];
    $pic = (!empty($_FILES['pic']['name']))?  uploadImg($_FILES['pic']) : '';

    try{
        $dbh = dbConnect();

        $sql = 'INSERT INTO product (name, pic1, comment, category_id, user_id, create_date) VALUES (:name, :pic1, :comment, :category_id, :user_id, :create_date)';
        $data = array(':name' => $name, ':pic1' => $pic, ':comment' => $comment, ':category_id' => $category, ':user_id' => $u_id, ':create_date' => date('Y-m-d H:i:s'));

        $stmt = queryPost($dbh,$sql,$data);

        if(!$stmt){
            debug('mypage.クエリ失敗：');
        }
    } catch (Exception $e) {
        error_log('エラー発生：'. $e->getMessage());
    }
}
?>

<!-- head -->
<?php
$title = 'マイページ';
require('head.php');
 ?>
 <!-- body -->
    <body>
<!-- header -->
        <?php
        require('header.php');
         ?>
<!-- main -->
            <div class="myPage">
                <div class="myPage-container site-width">
                    <div class="myPage-txt txt-center">
                        <h2>Hello<br>Master!!</h2>
                    </div>
                </div>
                <div class="regist-area  form-container">
                    <form class="form" action="" method="post" enctype="multipart/form-data">
                        <div class="category-container">
                            <select name="category" id="">
                                <?php 
                                if(!empty($category)){
                                    foreach($category as $key => $val){
                                ?>
                                    <option value="<?php echo sanitize($val['id']) ?>" class="">
                                        <?php echo sanitize($val['name']); ?>
                                    </option>
                                <?php        
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="name-wrapper">
                        名前：
                            <input type="text" name="name">
                        </div>
                        <div class="imgDrop-container">
                            画像
                            <label class="area-drop">
                                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                <input type="file" name="pic" class="input-file">
                                <img src="" alt="" class="prev-img">
                                ドラッグ＆ドロップ
                            </label>
                        </div>
                        <div class="txt-container">
                            <textarea name="comment" id=""></textarea>
                        </div>
                        <input type="submit" value="登録">
                    </form>
                </div>
                <div class="displayarea-container" style="display: table; margin: 0 auto;">
                    <table>
                        <thead>
                            <tr>
                                <td>送信日時</td>
                                <td>取引相手</td>
                                <td>メッセージ</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(!empty($myMsgAndBord)){
                          foreach($myMsgAndBord as $key => $val){
                            debug('展開：'.print_r($myMsgAndBord,true));
                              if(!empty($val['msg'])){
                                $msg = array_shift($val['msg']);
                                  debug('表示展開２:'.print_r($msg,true));
                        ?>                        
                            <tr>
                                <td><?php echo sanitize(date('Y-m-d H:i:s',strtotime($msg['send_date']))); ?></td>
                                <td>0000</td>
                                <td><a href="msg.php?b_id=<?php echo sanitize($val['id']); ?>"><?php echo mb_substr(sanitize($msg['msg']),0,40); ?>...</a></td>
                            </tr>

                        <?php
                              } else {
                        ?>

                            <tr>
                                <td>---</td>
                                <td>0000</td>
                                <td><a href="msg.php?b_id=<?php echo sanitize($val['id']); ?>">メッセージはまだありません。</a></td>
                            </tr>
                        <?php
                              }
                          }
                        }
                        ?>
                    </tbody>
                    </table>
                </div>
            </div>
<!-- footer -->
         <?php
         require('footer.php');
          ?>