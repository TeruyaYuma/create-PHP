<?php
require('function.php');

$u_id = (!empty($_SESSION['user_id']))? $_SESSION['user_id'] : '';
$newProduct = getNewProduct();
debug('最新の３件：'.print_r($newProduct,true));

?>

<!-- head -->
<?php
$title = 'トップページ';
require('head.php');
 ?>
<!-- body -->
<body>
<!-- header -->
  <?php
  require('header.php');
   ?>
<!-- main -->
  <main>
    <?php 
    if(!empty($u_id) && $u_id !== '1'){
        require('chat.php');
    } 
    ?>
<!-- work -->
    <section id="work">
     <div class="site-width">
      <div class="work title txt-center">
        <h2>Work</h2>
      </div>
      <div class="products">
<!-- logo -->
       <div class="product left title txt-center">
         <div class="container-sticky">
         <h3>Products</h3>
          <div class="product-img">
            <img src="img/light-bulb.png" alt="">
          </div>
          </div>
       </div>
<!-- portforio -->
       <div class="product right">
       <?php
          foreach($newProduct as $key => $val){
        ?>   
          <div class="product-contents">
            <a href="">
            <div class="img">
                <img src="<?php echo sanitize($val['pic1']); ?>" alt="">
            </div>
            </a>
            <div class="article">
              <p><?php echo sanitize($val['comment']); ?></p>
            </div>
          </div>
          <?php
            }
          ?>
       </div>
      </div>
     </div>
    </section>

<!-- abou -->
    <section id="about">
      <div class="site-width">
        <div class="about title txt-center">
          <h2>About</h2>
        </div>
        <div class="about-txt txt-center">
          <p>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          テストテウそっテストテストテスト<br>
          </p>
        </div>
      </div>
    </section>
  </main>
<!-- footer -->
  <?php require('footer.php'); ?>