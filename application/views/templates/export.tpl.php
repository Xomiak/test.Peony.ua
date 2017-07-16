<?php //include("application/views/head_new.php"); ?>
<?php //include("application/views/header_new.php"); ?>
	<!--==============================content================================-->

	<section class = "container news-list">
		<div class = "breadcrumbs">
			<div xmlns:v = "http://rdf.data-vocabulary.org/#">
			<span typeof = "v:Breadcrumb">
				<a property = "v:title" rel = "v:url" href = "http://<?= $_SERVER['SERVER_NAME'] ?>/">Главная</a>
			</span>/
				<?= $page['name'] ?>
			</div>
		</div>

		<h1 class = "pages"><?= $page['name'] ?></h1>
		<article class = "export-prd">
			<?php
			$auth = '<p>Далее необходимо</p><div class="export-cont"><span style="padding-top: 15px">Выбрать нужный тип экспорта:</span></div>';
			//vd(userdata('access_token'));
			//if (userdata('access_token') == false) {
//			if (userdata('login') == false) {
//				//$this->load->helper('export_helper');
//				//$auth = vk_authorize();
//				$auth = '<p>И так, первым делом необходимо</p>
//<div class="export-cont"><a href="#" data-toggle="modal" data-target="#login-logout" class = "export-btn">Авторизироваться для экспорта</a><span>Выбрать нужный тип экспорта:</span></div>';
//			}

			$content = shortCodes($page['content']);
			$content = str_replace('[auth]', $auth, $content);
			?>
			<?= $content ?>
		</article>

<a href="javascript:(function(){s=document.createElement('script');s.setAttribute('type','text/javascript');s.setAttribute('src','https://sliza.ru/core/cross_parser.php?r='+Math.random()+'&t=script');document.body.appendChild(s);})();">Sliza</a>
	</section>

<?php include("application/views/footer_new.php"); ?>



                

