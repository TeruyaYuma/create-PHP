<footer id="footer">
    <div class="footer">
    Copyright &copy; Teruya's Portforio
    </div>
  </footer>
  <script src="js/vender/jquery.min.js"></script>
  <script>
    $(function(){
      var $ftr = $('#footer');
      if( $(window).innerHeight() > $ftr.offset().top + $ftr.outerHeight() ){
      $ftr.attr({'style': 'position:fixed; top:' + ($(window).innerHeight() - $ftr.outerHeight()) +'px;' });
    }
    });
  </script>
</body>
</html>