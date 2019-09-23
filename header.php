<header>
<!-- hero img -->
    <?php
    if(basename($_SERVER['PHP_SELF']) === 'index.php'){
     ?> 
     <div class="header-container">
       <div class="header-img">
        <div class="header-logo">
          <!-- 管理人IDならmypageリンク付与 -->
          <?PHP
           if(empty($u_id) || $u_id !== '1'){
          ?>
              <div class="logo-wrapper">
                <h2>WELCOME</h2>
                <p>my<br>site</p>
              </div>
          <?php
          } else if($u_id === '1'){
          ?>
              <a href="mypage.php">
                <div class="logo-wrapper">
                  <h2>WELCOME</h2>
                  <p>my<br>site</p>
                </div>
              </a>
          <?php } ?>
          </div>
        </div>
      </div>
    <?php  
    }
     ?>
<!-- global menu -->
      <div class="navigation-container">
        <div class="navigation-wrapper site-width">
          <div class="navigation-left">
           <a href="index.php"><h1>LOGO</h1></a>
          </div>
          <div class="navigation-right">
            <ul>
              <li><a href="/msgbox/login.php">login</a></li>
              <li><a href="/msgbox/logout.php">logout</a></li>
              <li><a href="/msgbox/product.php">product</a></li>
              <li><a href="#about">about</a></li>
              <li><a href="/msgbox/contact.php">contact</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </header>