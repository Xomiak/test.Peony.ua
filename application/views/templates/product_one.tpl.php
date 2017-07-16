<?php
addToCart();
//include("application/views/header_new.php");
// ЕСТЬ ЛИ АКЦИЯ
if (isDiscount($article)) {
	$article['discountNow'] = true;
	if($article['image_no_logo'] != '') $article['image'] = $article['image_no_logo'];
}

$currency = userdata('currency');
if(!$currency) $currency = 'uah';

// Округляем цену
//$article['price'] = round($article['price'],2);
// Подгружаем курс валют
$currensy_grn = getCurrencyValue('UAH');
$currensy_rub = getCurrencyValue('RUB');
?>
	<!-- main page - start-->



	<section class = "container cart" itemscope itemtype="http://schema.org/Product">
<!--		<div id="page-preloader"><span class="spinner"></span></div>-->
	<div class = "breadcrumbs">
		<div xmlns:v = "http://rdf.data-vocabulary.org/#">
			<span typeof = "v:Breadcrumb">
				<a property = "v:title" rel = "v:url" href = "http://<?= $_SERVER['SERVER_NAME'] ?>/">Главная</a>
			</span>&nbsp;-&nbsp;
			<span typeof = "v:Breadcrumb">
				<a property = "v:title" rel = "v:url" href = "http://<?= $_SERVER['SERVER_NAME'] ?>/<?= $category['url'] ?>/"><?= $category['name'] ?></a>
			</span>&nbsp;-&nbsp;
			<?= $article['name'] ?> (<?= $article['color'] ?>)
		</div>
	</div>



		<?php
		$image = array('image' => $article['image']);
		array_push($images,$image);
		$images = array_reverse($images);
		//vd($images);
		?>

		<div class = "ppoduct-left-sidebar">
			<meta itemprop="name" content="<?= $article['name'] ?> (<?= $article['color'] ?>)">
			<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<meta itemprop="price" content="<?= round($article['price'] * $currensy_grn,2) ?>">
				<meta itemprop="priceCurrency" content="UAH">
				<link itemprop="availability" href="http://schema.org/InStock">
			</div>
			<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<meta itemprop="price" content="<?= round($article['price'],2) ?>">
				<meta itemprop="priceCurrency" content="USD">
				<link itemprop="availability" href="http://schema.org/InStock">
			</div>
			<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<meta itemprop="price" content="<?= round($article['price'] * $currensy_rub, 2) ?>">
				<meta itemprop="priceCurrency" content="RUB">
				<link itemprop="availability" href="http://schema.org/InStock">
			</div>

			<ul id="bx-pager">

				<?php
		if ($images) {
			$i = 0;
			foreach($images as $img)
			{
				?>
						<li><img itemprop="image" class="" src="<?= $img['image'] ?>" alt = "<?= $article['image'] ?>  Фото №(<?= ($i + 1) ?>)" /></li>
						<?php
				$i++;
			}
		}
		?>

			</ul>

			<ul id="bxslider">
						<li><img itemprop="image" id="full" src="<?= $images[0]['image'] ?>" alt = "<?= $article['name'] ?>  Фото №(<?= ($i + 1) ?>)" class="zoom-desktop" /></li>
			</ul>
			<div style="clear: both;width: 100%">
				<div style="text-align: right">
					<script type="text/javascript">(function(w,doc) {
							if (!w.__utlWdgt ) {
								w.__utlWdgt = true;
								var d = doc, s = d.createElement('script'), g = 'getElementsByTagName';
								s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
								s.src = ('https:' == w.location.protocol ? 'https' : 'http')  + '://w.uptolike.com/widgets/v1/uptolike.js';
								var h=d[g]('body')[0];
								h.appendChild(s);
							}})(window,document);
					</script>
					<div data-background-alpha="0.0" data-buttons-color="#FFFFFF" data-counter-background-color="#ffffff" data-share-counter-size="12" data-top-button="false" data-share-counter-type="common" data-share-style="1" data-mode="share" data-follow-vk="peony_shop" data-like-text-enable="false" data-mobile-view="true" data-icon-color="#ffffff" data-orientation="horizontal" data-text-color="#000000" data-share-shape="round-rectangle" data-sn-ids="fb.vk.tw.ok.gp." data-share-size="30" data-background-color="#ffffff" data-preview-mobile="false" data-mobile-sn-ids="fb.vk.tw.wh.ok.gp." data-pid="1433737" data-counter-background-alpha="1.0" data-following-enable="true" data-exclude-show-more="false" data-selection-enable="true" data-follow-fb="Peony.Shop.Ukraine/" class="uptolike-buttons" ></div>
				</div>
			</div>
		</div>

		<!--------------------------------
        -------------END----------------
        ------------------------------->



	<div class = "itm-desc"<?php if(isset($_GET['iframe'])) echo ' style="max-width: 45%"';?>>
		<?php
		if($article['h1'] == $article['name']){
			$article['h1'] = '';
			if($category['name_one'] != '' && $category['name_one'] !== NULL) $article['h1'] .= $category['name_one'];
			else $article['h1'] .= $category['name'];
			$article['h1'] .= ' <span>'.$article['name'].'</span> ('.$article['color'].')';
		}
		?>
		<h1><?= $article['h1'] ?><?php //echo isAdminEdit($article['id']); ?></h1>


        <?php
        $currency = userdata('currency');
        $currencySymb = '$';
        if($currency == 'uah') $currencySymb = ' грн';
        elseif($currency == 'rub') $currencySymb = ' р';
        if(!$currency) $currency = 'uah';
        $currensy_grn = getCurrencyValue('UAH');
        $currensy_rub = getCurrencyValue('RUB');
        ?>
		<p class = "all-price<?php if (isset($article['discountNow']))
			echo " old-price"; ?>">

            <span class="product_old_price" product_id="<?=$article['id']?>" usd="<?=$article['price']?>$" uah="<?=round(($article['price'] * $currensy_grn),2)?> грн" rub="<?=round(($article['price'] * $currensy_rub),2)?> р">
                <?=getPriceInCurrency($article['price'],0,$currency)?> <?=$currencySymb?>
            </span>

		</p>
		<!--span class = "itm-code">Артикул: <?= $article['articul'] ?></span-->
		<?php
		if (isset($article['discountNow'])) {
			//$article['price'] = getNewPrice($article['price'], $article['discount']);
			?>
            <p class = "all-price new-price">
            <span class="product_price" product_id="<?=$article['id']?>" usd="<?=getNewPrice($article['price'], $article['discount'])?>$" uah="<?=getPriceInCurrency($article['price'],$article['discount'],'uah')?> грн" rub="<?=getPriceInCurrency($article['price'],$article['discount'],'rub')?> р">
                <?=getPriceInCurrency($article['price'],$article['discount'],$currency)?> <?=$currencySymb?>
            </span>
			</p>
			<?php
		}
		?>
		<a href="/usloviya-sotrudnichestva/" target="_blank"><div class="about-buing" style="color:black; font-size: 10px; margin-top: -10px; padding-bottom: 10px;" title="<?=strip_tags(getOption('about-buing'))?>"><?=getOption('about-buing')?></div></a>





<?php
$razmer = explode('*', $article['razmer']);
$warehouse = json_decode($article['warehouse'], true);

$razm = "";
if(is_array($razmer))
{
	sort($razmer);
	foreach($razmer as $r)
	{
		$available = 'true';
		if(($warehouse != NULL) && isset($warehouse[$r])) $available = $warehouse[$r];
		else $available = -1;

		$classes = 'harki-param';
		if(!$available) $classes .= ' not-available';
		else $classes .= ' available';

		$razm .= '<span class="'.$classes.'"';
		if((!$available) && $article['sale'] == 1)  $razm .= ' style="display:none;"';
		elseif($available == -1)  $razm .= ' style="display:none;"';

		$razm .= '>'.$r.';</span>&nbsp;';
	}
}
?>
		<div class="clr"></div>
		<span class="content" itemprop="description">
			<?= strip_tags($article['content'],'<p><i><strong><ul><li><br><em><a>') ?>

		</span>
		<div class="clr"></div>



		<p style="margin-top: -10px">
			<strong>Артикул</strong>: <?=$article['articul']?><br />
		<strong>Ткань</strong>: <?=$article['tkan']?><br />
			<?php
			if($article['sostav'])
			{
				echo '<strong>Состав</strong>: ';
				$sostav = str_replace("\n","<br>", $article['sostav']);
				echo $sostav;
				echo '<br />';
			}
			?>
			<?php
			if($article['height'] != NULL && $article['height'] != '')
				echo '<strong>Длина изделия</strong>: '.$article['height'].' см<br />';
			if($article['hand_height'] != NULL && $article['hand_height'] != '')
				echo '<strong>Длина рукава</strong>: '.$article['hand_height'].' см<br />';
			if($article['season'] != NULL && $article['season'] != '')
				echo '<strong>Сезон</strong>: '.$article['season'].'<br />';
			?>
		<strong>Размерный ряд</strong>: <?=$razm?>
		</p>



		<form id="myform" method = "post"  class = "cart-form">
			<input type = "hidden" name = "shop_id" value = "<?= $article['id'] ?>"/>
			<input type = "hidden" name = "back" value = "<?= $_SERVER['REQUEST_URI'] ?>"/>

			<span>Размер</span>

			<select id="razmer1" name = "razmer">
				<option></option>
				<?php
				if (is_array($razmer)) {

					$count = count($razmer);
					for ($i = 0; $i < $count; $i++) {
						$r = $razmer[$i];

						// Остаток по каждому размеру
						$available = 'true';
						if(isset($warehouse[$r])) $available = $warehouse[$r];
						else $available = -1;

						// Нет на складе

							$class = '';
							if(($article['warehouse'] != NULL) && $available == 0) $class = 'class="not-available"';
							elseif($available == -1)  $class = 'class="availible-unknoun"';
							echo '<option '.$class.' available="'.$available.'" value = "' . $razmer[$i] . '"';
							if((!$available) && $article['sale'] == 1) echo 'style="display:none;"';
							echo '>' . $razmer[$i] . '</option>';

					}
				}
				?>
			</select>
			<span class = "size" data-toggle = "modal" data-target = ".size-grid">Размерная сетка</span>
			<div id="razmererror" сlass="error" style="display: none; color: red;">Вы не указали размер!</div>
			<?php if($article['ended'] == 1) { ?>
				<div class="ended">
					Товар закончился и больше не выпускается
				</div>
			<?php } else { ?>

				<?php if($article['warehouse_sum'] > 0) { ?>
				<div id="b_submit" class = "cart-count">
					<span>Кол-во</span>
					<input pattern="^[ 0-9]+$" id="kolvo" type = "text" required placeholder = "1" value = "1" name = "kolvo" onkeyup="this.value=this.value.replace(/[^0-9]+/g,''); isright(this);">
					<button id="add_product_to_cart" onClick="yaCounter26267973.reachGoal('kupit'); ga('send', 'event', 'kupit', 'click');">В корзину<span class = "icon-cart"></button>


				</div>
				<?php } else { ?>
					<div class="not_in_warehouse">
						Товар закончился
					</div>
				<?php } ?>
			<?php } ?>

		</form>


        <?php
        if($article['warehouse_sum'] > 0) {
            if(isDebug()) {

               // echo get_one_click_form_content(true, $article['id']);
            }
        }
        ?>


	</div>
	<div class = "itm-info">

		<?php
		// ПОКАЗ ДРУГИХ ЦВЕТОВ, ЕСЛИ ОНИ ЕСТЬ
		$name = $article['name'];
		$pos = strpos($article['name'], '/');
		if ($pos !== false) {
			$name = substr($name, 0, $pos);
		}

		showAllColors($name, $article['id']);
		?>

		<span class="itm-info-title">Информация</span>
		<ul>
			<li data-toggle = "modal" data-target = ".conditions">
				<div class = "sprite-info-first"></div>
				<p>Условия
					сотрудничества</p>
			</li>
			<li data-toggle = "modal" data-target = ".order">
				<div class = "sprite-info-second"></div>
				<p>Как
					заказать?</p>
			</li>
			<li data-toggle = "modal" data-target = ".payment">
				<div class = "sprite-info-third"></div>
				<p>Оплата и
					доставка</p>
			</li>
		</ul>
	</div>
	</section>

    <a name="reviews"></a>
    [reviews]




	<section class = "container related">
		<span class="related-title">Также рекомендуем посмотреть</span>
		<?php
        loadHelper('modules');
		relatedProducts($category, $article);
		?>
	</section>

	<!-- POP_UP_Size_grid -->

	<div class = "modal fade size-grid bs-example-modal-lg" tabindex = "-1" role = "dialog" aria-labelledby = "myLargeModalLabel" aria-hidden = "true">
		<div class = "modal-dialog modal-md">
			<div class = "modal-content">
				<button class="close" type="button" data-dismiss="modal">&times;</button>
				<?= getOption('razmernaya_setka')?>
			</div>
		</div>
	</div>

	<!--END Size-grid modal-->
	<!--------
	----------
	---------->
	<!--POP_UP_Conditions-->
	<!--noindex-->
	<div class = "modal fade conditions bs-example-modal-lg" tabindex = "-1" role = "dialog" aria-labelledby = "mySmalModalLabel" aria-hidden = "true">
		<div class = "modal-dialog modal-lg">
			<div class = "modal-content razmernaya-setka">
				<button class="close" type="button" data-dismiss="modal">&times;</button>
				<?= shortCodes(getOption('usloviya')) ?>
			</div>
		</div>
	</div>
	<!--/noindex-->
	<!--END Conditions modal-->
	<!--------
	----------
	---------->
	<!--POP_UP_Order-->
	<!--noindex-->
	<div class = "modal fade order bs-example-modal-md" tabindex = "-1" role = "dialog" aria-labelledby = "mySmalModalLabel" aria-hidden = "true">
		<div class = "modal-dialog modal-md">
			<div class = "modal-content razmernaya-setka">
				<button class="close" type="button" data-dismiss="modal">&times;</button>
				<?= getOption('zakaz') ?>
			</div>
		</div>
	</div>
	<!--/noindex-->
	<!--END Order modal-->
	<!--------
	----------
	---------->
	<!--POP_UP_Payment-->
	<!--noindex-->
	<div class = "modal fade payment bs-example-modal-md" tabindex = "-1" role = "dialog" aria-labelledby = "mySmalModalLabel" aria-hidden = "true">
		<div class = "modal-dialog modal-md">
			<div class = "modal-content razmernaya-setka">
				<button class="close" type="button" data-dismiss="modal">&times;</button>
				<?= getOption('oplata') ?>
			</div>
		</div>
	</div>
	<!--/noindex-->
	<!--END Payment modal-->
	<!--------
	----------
	---------->
	<!--POP_UP_Reviews-->




	<!--END Review modal-->
	<!--------
	----------
	---------->


	<script src = "/js/jquery.min.js"></script>


    <script>
        $("#myform").submit(function () {
            return false;
        });
    </script>


	<script>
		var maxKolvo = <?=$article['warehouse_sum']?>

		var j = jQuery.noConflict();
		j( "#razmer1" )
			.change(function () {
				//alert('asd');
				$("#razmererror").hide();
				var available = j( "#razmer1 option:selected").attr("available");
				if(available == 0)
				{
					jQuery('#modal_not_available').modal('show');
					jQuery('#b_submit').hide();

				}
				if(available > 0)
				{
					jQuery('#b_submit').show();
					var kolvo = parseInt(j("#kolvo").val());
					if(kolvo > available)
					{
						j("#kolvo").val(available);
					}
				}
			})
			.change();

		j( "#kolvo" )
			.change(function () {
				//alert(available + "=" + j("#kolvo").val());
				var available = j( "#razmer1 option:selected").attr("available");
				var kolvo = parseInt(j("#kolvo").val());
				if(kolvo > available)
				{
					j("#kolvo").val(available);
					jQuery('#modal_max_available').modal('show');
				}
			})
			.change();
	</script>
<?php
$user = getCurrentUser();
if($user)
//vd($user);
$message = "";
$email = '';
if($user)
{
	$email = $user['email'];

	$message = '<section class="available-me-form"><p>К сожалению, на данный момент этого размера нет в наличии.</p>
<p>Сообщить, когда появится:</p>
<input class="itm-desc" type="email" required name="email" placeholder="e-mail" id="say_me_available_email" value="'.$email.'" /><button class="say-me" id="say_me_available">OK</button>
</section>';
}
else
{
	$message = '<section class="available-me-form"><p>К сожалению, на данный момент этого размера нет в наличии.</p>
<p>Сообщить, когда появится:</p>
            <p style="font-size: 12px;">Выберите любую, удобную для Вас, соц. сеть, либо почтовую службу:</p>
			<script src="//ulogin.ru/js/ulogin.js"></script>
            <div id="uLogin08292de1" data-ulogin="display=panel;fields=first_name,last_name,country,email,city,bdate,photo;optional=phone,photo_big,sex,nickname;verify=1;providers=vkontakte,odnoklassniki,mailru,facebook;hidden=google,yandex,twitter,livejournal,openid,lastfm,linkedin,liveid,soundcloud,steam,flickr,uid,youtube,webmoney,foursquare,tumblr,googleplus,dudu,vimeo,instagram,wargaming;redirect_uri=//'.$_SERVER['SERVER_NAME'].'/login/soc/"></div>';
}
?>


		<div class="modal fade bs-example-modal-sm" id="modal_not_available" tabindex="-1" role="dialog"
			 aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-sm registration-modal">
				<div class="modal-content">
					<button class="close" type="button" data-dismiss="modal">&times;</button>
					<div class="after-autorization">
						<?= $message ?>
					</div>
				</div>
			</div>
		</div>
		<script>
			j('#say_me_available').on('click', function () {
				var email = j('#say_me_available_email').val();
				if(j('#say_me_available_email').val() != "")
				{
					j.post(
						"/ajax/say_me_available/",
						{
							shop_id: <?=$article['id']?>,
							razmer: j("#razmer1").val(),
							email: j("#say_me_available_email").val(),
							url: "http://<?=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']?>"
						},
						onAjaxSuccess
					);
				}else alert('Укажите правильный e-mail!');
			});

			function onAjaxSuccess(data) {
				j("#modal_not_available").modal('hide');
			}
		</script>
		<?php


	$message = 'Это максимально допустимое количество данного размера';
	getModalDialog('modal_max_available', $message);
//}

$rating = getRating($article);
if($rating == 0) $rating = 4;
$voitings = $article['voitings'];
if($voitings == 0) $voitings = 1;
?>

	<script type="application/ld+json">
{
  "@context": "http://schema.org/",
  "@type": "Product",
  "name": "<?=$article['name']?> (<?=$article['color']?>)",
  "image": "http://<?=$_SERVER['SERVER_NAME']?><?=$article['image']?>",
  "description": "<?=strip_tags($article['content'])?>",
  "mpn": "<?=$article['articul']?>",
  "brand": {
    "@type": "Thing",
    "name": "PEONY"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "<?=$rating?>",
    "reviewCount": "<?=$voitings?>"
  },
  "offers": {
    "@type": "Offer",
    "priceCurrency": "USD",
    "price": "<?=getNewPrice($article['price'], $article['discount'])?>",
    "priceValidUntil": "2030-11-05",
    "itemCondition": "http://schema.org/NewCondition",
    "availability": "http://schema.org/InStock",
    "seller": {
      "@type": "Organization",
      "name": "PEONY"
    }
  }
}
</script>

<?php include("application/views/footer_new.php"); ?>