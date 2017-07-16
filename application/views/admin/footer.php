    <div class="logo"></div>
    <script type="text/javascript" src="/js/jquery-3.2.1.min.js"></script>

    <script src="/js/ckedit/ckeditor.js"></script>
    <script src="/js/jquery.maskedinput.min.js" type="text/javascript"></script>
    <script src="/js/admin.js"></script>
    <script>
        $(document).ready(function () {
            var lastScrollTop = 0;
            $(window).scroll(function(event){
                var st = $(this).scrollTop();
                if ($(this).scrollTop() > 100) {
                    $('.table_fix_el').addClass('fixed');
                    $('.message').addClass('fixed');
                } else {
                    $('.table_fix_el').removeClass('fixed');
                    $('.message').removeClass('fixed');
                }
                lastScrollTop = st;
            });
        })
    </script>

    <?php
    if(request_uri(true, true) == '/admin/orders/')
        include('application/views/admin/orders/orders_js.php');
    ?>

    </body>
</html>