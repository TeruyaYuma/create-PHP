<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 コンタクトページ 」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(!empty($_POST)){
   $subject = (!empty($_POST['subject']))? $_POST['subject'] : '';
   $name = (!empty($_POST['name']))? $_POST['name'] : '';
   $comment = (!empty($_POST['comment']))? $_POST['comment'] : '';
   $from = $_POST['email'];
   debug('email'.$from);

   $msg = '';

   sendMail($from,$subject,$name,$comment);
}
 ?>
<!-- head -->
<?php
$title = 'お問い合わせ';
require('head.php');
 ?>
    <body>
        <!-- header -->
        <?php 
        require('header.php');
         ?>
            <div id="contacts">
                <div class="form-container site-width">
                    <form class="form" action="" method="post">
                    <div>
                     <p><?php if(!empty($msg)) echo $msg ?></p>
                    </div>
                    <h2 class="title txt-center">お問い合わせ</h2>
                        <label>
                            件名：
                            <input type="text" name="subject">
                        </label>
                        <label>
                            お名前：
                            <input type="text" name="name">
                        </label>
                        <label>
                            メールアドレス：
                            <input type="text" name="email">
                        </label>
                        <label>
                            お問い合わせ内容：
                            <textarea name="" id="" cols="30" rows="10" name="comment"></textarea>
                        </label>
                        <input type="submit">
                    </form>
                </div>
            </div>
        <?php
        require('footer.php');
         ?>