<link rel = "stylesheet" href = "/1/multizoom.css" type = "text/css"/>

<script type = "text/javascript" src = "http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>

<script type = "text/javascript" src = "/1/multizoom.js"></script>


<script type = "text/javascript">


	jQuery(document).ready(function ($) {

		$('#image1').addimagezoom({ // single image zoom
			zoomrange: [3, 3],
			magnifiersize: [300, 300],
			magnifierpos: 'right',
			cursorshade: true,
			largeimage: 'hayden.jpg' //<-- No comma after last option!
		})


		$('#image2').addimagezoom() // single image zoom with default options

		$('#multizoom1').addimagezoom({ // multi-zoom: options same as for previous Featured Image Zoomer's addimagezoom unless noted as '- new'
			descArea: '#description', // description selector (optional - but required if descriptions are used) - new
			speed: 1500, // duration of fade in for new zoomable images (in milliseconds, optional) - new
			descpos: true, // if set to true - description position follows image position at a set distance, defaults to false (optional) - new
			imagevertcenter: true, // zoomable image centers vertically in its container (optional) - new
			magvertcenter: true, // magnified area centers vertically in relation to the zoomable image (optional) - new
			zoomrange: [3, 3],
			magnifiersize: [400, 400],
			magnifierpos: 'right',
			cursorshadecolor: '#fdffd5',
			cursorshade: true //<-- No comma after last option!
		});

		$('#multizoom2').addimagezoom({ // multi-zoom: options same as for previous Featured Image Zoomer's addimagezoom unless noted as '- new'
			descArea: '#description2', // description selector (optional - but required if descriptions are used) - new
			disablewheel: true // even without variable zoom, mousewheel will not shift image position while mouse is over image (optional) - new
			//^-- No comma after last option!
		});

	})

</script>

<div class = "ppoduct_left_sidebar">
	<div class = "targetarea" style = "border:1px solid #eee">
		<img id = "multizoom1" alt = "zoomable" title = "" src = "<?= $article['image'] ?>"/></div>

	<?php
	$show = false;
	//if($article['akciya'] == 1)
	if ($show) {
		?>
		<img class = "akciya_one" src = "/img/action_big.png" alt = "Акция" title = "Акция"/>
	<?php
	}
	?>


	<div class = "multizoom1 thumbs">
		<link href = "/2/style.css" rel = "stylesheet" type = "text/css"/>
		<script type = "text/javascript" src = "/2/lib/jquery.jcarousel.min.js"></script>
		<link rel = "stylesheet" type = "text/css" href = "/2/skins/tango/skin.css"/>


		<script type = "text/javascript">

			jQuery(document).ready(function () {
				jQuery('#mycarousel').jcarousel({
					vertical: true,
					scroll: 1,
					visible: 3,
					wrap: "both"
				});
			});

		</script>
		<ul id = "mycarousel" class = "jcarousel-skin-tango">
			<?php
			$article['image'] = str_replace('articles', 'original', $article['image']);
			?>
			<li>
				<a href = "<?= $article['image'] ?>" data-large = "<?= $article['image'] ?>"><img src = "<?= CreateThumb(115, 165, $article['image'], 'shop') ?>" alt = "<?= $article['name'] ?>" title = ""/></a>
			</li>
			<?php
			if ($images) {
				$count = count($images);
				for ($i = 0; $i < $count; $i++) {
					$img = $images[$i];
					?>
					<li>
						<a href = "<?= $img['image'] ?>" data-large = "<?= $img['image'] ?>"><img src = "<?= CreateThumb(115, 165, $img['image'], 'shop') ?>" alt = "<?= $article['name'] ?>" title = ""/></a>
					</li>
				<?php
				}
			}
			?>
		</ul>

		<div class = 'clear'></div>
	</div>

	<div class = "share">
		<div class = "share2">
			<script type = "text/javascript">(function (w, doc) {
					if (!w.__utlWdgt) {
						w.__utlWdgt = true;
						var d = doc, s = d.createElement('script'), g = 'getElementsByTagName';
						s.type = 'text/javascript';
						s.charset = 'UTF-8';
						s.async = true;
						s.src = ('https:' == w.location.protocol ? 'https' : 'http') + '://w.uptolike.com/widgets/v1/uptolike.js';
						var h = d[g]('body')[0];
						h.appendChild(s);
					}
				})(window, document);
			</script>
			<div data-share-size = "30" data-like-text-enable = "false" data-background-alpha = "0.0" data-mobile-view = "true" data-pid = "1294091" data-mode = "share" data-background-color = "ededed" data-share-shape = "round-rectangle" data-icon-color = "ffffff" data-share-counter-size = "11" data-text-color = "000000" data-buttons-color = "ff9300" data-counter-background-color = "ffffff" data-follow-ok = "group/52128400343108" data-share-counter-type = "common" data-orientation = "horizontal" data-following-enable = "true" data-sn-ids = "fb.tw.ok.vk.gp." data-selection-enable = "true" data-share-style = "1" data-follow-vk = "peony_shop" data-counter-background-alpha = "1.0" data-top-button = "false" data-follow-fb = "Peony.Shop.Ukraine" class = "uptolike-buttons"></div>
		</div>
	</div>

</div>
<?php
if (isset($article['discountNow']))
	echo '<img class="discount-img-big" src="/img/sale/' . $article['discount'] . '.png">';
?>