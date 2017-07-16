<?php include("application/views/header2.php"); ?>
<div class="clear"></div>
<div>
	<?php include("application/views/menu_pages.php"); ?>
	<div class="page_right_sidebar_news">
		<h1 class="art"><span>Поиск</span></h1>
		<div style="width:160px; height:30px;position:relative;margin-bottom: 20px;">
		<form style="border:1px solid #000;width: 260px;height: 26px;" method="post" action="/search/">
			<input style="background: transparent;width: 261px;padding-top: 3px;" type="search" placeholder="Поиск" name="search" required />
			<input class="search2" type="submit" style="top: 6px;right: -92px;position: absolute;height: 19px;" value="" >
		</form>
		</div>
		<h1 class="art">Поиск</h1>
                
		<div id="cse-search-results"></div>
                    <script type="text/javascript">
                      var googleSearchIframeName = "cse-search-results";
                      var googleSearchFormName = "cse-search-box";
                      var googleSearchFrameWidth = 965;
                      var googleSearchDomain = "www.google.com.ua";
                      var googleSearchPath = "/cse";
                    </script>
                    <script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>
		<div class="clear"></div>
		</div></div>

<?php include("application/views/footer.php") ?>