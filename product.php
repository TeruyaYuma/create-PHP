<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 プロダクトページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

//各パラメーター取得
$currentPageNum = (!empty($_GET['p']))? $_GET['p'] : 1;
$category = (!empty($_GET['c_id']))? $_GET['c_id'] : '';
$sort = (!empty($_GET['s_id']))? $_GET['s_id'] : '';

$listSpan = 20;
$currentMinNum = (($currentPageNum-1)*$listSpan); 
//プロダクト情報取得
$productList = getProductList($currentMinNum, $category,$sort);
$dbCategory = getCategory();
 ?>
<!-- head -->
<?php
$title = 'プロダクト';
require('head.php');
 ?>
<!-- body -->
    <body>
<!-- header -->
        <?php
        require('header.php');
         ?>
<!-- main_contents -->
         <div class="product-container site-width">
<!-- side-bar -->
            <section id="side-bar">
                <div class="search-container">
                    <form class="form" action="" method="get">
                        <h3 class="txt-center">カテゴリー</h3>
                        <div class="select-wrapper">
                            <select name="c_id" id="">
                                <option value="0">選択してください。</option>
                                <?php
                                    foreach($dbCategory as $key => $val){
                                ?>
                                    <option value="<?php echo $val['id']; ?>">
                                        <?php echo $val['name']; ?>
                                    </option>
                                <?php        
                                    }  
                                ?>
                            </select>
                        </div>
                        <h3 class="txt-center">表示順</h3>
                        <div class="select-wrapper">
                            <select name="sort" id="">
                                <option value="0">選択してください</option>
                                <option value="1">安い順</option>
                                <option value="2">高い順</option>    
                            </select>
                        </div>
                        <input  type="submit" value="検索">
                    </form>
                </div>
            </section>
<!-- main_product -->
            <section id="main">
                <div class="search-title">
                    <div class="search-left">
                        page<span><?php echo sanitize(!empty($productList['data']))? $currentMinNum+1 : 0; ?></span>
                    </div>
                    <div class="search-right">
                        <span><?php echo sanitize($currentMinNum+count($productList['data'])); ?></span>/<span><?php echo sanitize($productList['total']); ?></span>件中
                    </div>
                </div>
                <div class="panel-wrapper">
                <?php
                if(!empty($productList['data'])){
                    foreach($productList['data'] as $key => $val){
                ?>
                <a><!-- link付ける予定 -->
                    <div class="panel-list">
                            <div class="panel-img">
                                <img src="<?php echo sanitize($val['pic1']); ?>" alt="">
                            </div>
                            <div class="panel-body">
                                <p>
                                    <?php echo sanitize($val['name']); ?><br><?php echo sanitize($val['comment']); ?>
                                </p>
                            </div>
                    </div>
                </a>
                <?php
                    }//endforeach
                } else{
                ?>
                    <p>まだ投稿はありません。</p>
                <?php    
                }//endif
                ?>
                </div>
                <?php pagination($currentPageNum,$productList['total_page']); ?>
            </section>
        </div>
<!-- footer -->
        <?php
        require('footer.php');
         ?>