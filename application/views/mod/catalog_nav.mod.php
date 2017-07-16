<?php $this->lang->load('catalog_nav', $current_lang); ?>
<?php
$filter_min_price = get_price(getOption('filter_min_price'), false, -2);
$filter_max_price = get_price(getOption('filter_max_price'), false, -2);
//$filter_step = get_price(getOption('filter_step'), false, -2);
$filter_step = getOption('filter_step');


if (isset($_POST['min_price']))
    set_userdata('min_price', $_POST['min_price']);
if (isset($_POST['max_price']))
    set_userdata('max_price', $_POST['max_price']);

$min_price = userdata('min_price');
$max_price = userdata('max_price');


if (!$min_price) {
    $min_price = $filter_min_price;
}

if (!$max_price) {
    $max_price = $filter_max_price;
}


$in_warehouse = userdata('in_warehouse');
?>
<div class="sidebar">
    <div class="filter">
        <div class="filter_title title_border"><?= $this->lang->line('catalog_nav_filter') ?></div>
        <div class="filter2_title title_border"><?= $this->lang->line('catalog_nav_sort_cost') ?></div>
        <div id="options">
            <script>
                $(function() {
                    $('#min_price').change(function() {
                        var val = $(this).val();
                        $('#slider_price').slider("values", 0, val);
                    });

                    $('#max_price').change(function() {
                        var val2 = $(this).val();
                        $('#slider_price').slider("values", 1, val2);
                    });

                    $("#slider_price").slider({
                        range: true,
                        //orientation: "vertical",
                        min: <?= $filter_min_price ?>,
                        step:<?= $filter_step ?>,
                        max: <?= $filter_max_price ?>,
                        values: [<?= $min_price ?>, <?= $max_price ?>],
                        slide: function(event, ui) {
                            //$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
                            $('#min_price').val(ui.values[0]);
                            $('#max_price').val(ui.values[1]);
                        }
                    });
                    //$( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
                    //" - $" + $( "#slider-range" ).slider( "values", 1 ) );
                    $('#min_price').val($('#slider_price').slider("values", 0));
                    $('#max_price').val($('#slider_price').slider("values", 1));
                });
            </script>
            <div id="slider_price"></div>
            <form class="diapazon" method="POST" action="<?= $_SERVER['REQUEST_URI'] ?>">
                <label for="price"><?= $this->lang->line('catalog_nav_sort_cost_from') ?>:
                    <input type="text" name="min_price" id="min_price">
                </label>

                <label for="price2"><?= $this->lang->line('catalog_nav_sort_cost_to') ?>:
                    <input type="text" name="max_price" id="max_price">
                </label>&nbsp;<?= $currency ?>	<input type="submit" value="ОК"/>
           <!--  </form>-->
        </div>
        <div class="filter2_title title_border"><?= $this->lang->line('catalog_nav_sort_by_size') ?></div>
        <div class="size_filter">
           <!-- <form method="POST" action="<?= $_SERVER['REQUEST_URI'] ?>"> -->
                <?php
                $shop_razmery = getOption('shop_razmery');

                $size = false;
                if ($this->session->userdata('size') !== false)
                    $size = $this->session->userdata('size');

                if ($shop_razmery) {
                    $shop_razmery = explode('|', $shop_razmery);
                    $srcount = count($shop_razmery);
                    for ($sri = 0; $sri < $srcount; $sri++) {
                        $sr = $shop_razmery[$sri];
                        ?>
                        <input<?php if ($size != false && in_array($sr, $size)) echo ' checked'; ?> type="checkbox" name="size[]" value="<?= $sr ?>" />&nbsp;<span><?= $sr ?></span><br />
                        <?php
                    }
                }
                ?>
                <input type="submit" value="<?= $this->lang->line('catalog_nav_button_show') ?>"/>
            </form>
        </div>
        <!--div class="filter2_title title_border"><?= $this->lang->line('catalog_nav_availability') ?></div>
        <div class="size_filter">
            <form method="POST" action="<?= $_SERVER['REQUEST_URI'] ?>">
            <input<?php if ($in_warehouse && $in_warehouse == 1) echo ' checked'; ?> onchange="submit()" type="radio" name="in_warehouse" value="1" />&nbsp;<span><?= $this->lang->line('catalog_nav_availability_in_stock') ?></span><br />
            <input<?php if ($in_warehouse !== false && $in_warehouse == 0) echo ' checked'; ?> onchange="submit()" type="radio" name="in_warehouse" value="0" />&nbsp;<span><?= $this->lang->line('catalog_nav_availability_out_of_stock') ?></span>
            </form>
        </div-->
        <div class="reset_filters"><a rel="nofollow" href="?filters_reset"><?= $this->lang->line('catalog_nav_reset_filters') ?></a></div>
    </div>
    <div class="clr"></div>
    <div class="side_last_items">
        <div class="filter_title "><?= $this->lang->line('catalog_nav_latest') ?></div>
        <?php
        $count = count($new_prod);
        for ($i = 0; $i < $count; $i++) {
            $np = $new_prod[$i];
            $np['name'] = unserialize($np['name']);
            ?>
            <div class="side_last_item">
                <div class="side_last_item_image"><a href="/<?= $np['cat_url'] ?>/<?= $np['url'] ?>/"><img src="<?= CreateThumb(70, 73, (isset($np['image']) && !empty($np['image']))? $np['image'] : '/img/net_foto.png', 'new_prod') ?>"/></a></div>
                <div class="side_last_item_desc">
                    <div class="side_last_item_title"><a href="/<?= $np['cat_url'] ?>/<?= $np['url'] ?>/"><?= ( isset($np['name'][$current_lang]) && !empty($np['name'][$current_lang]) )? $np['name'][$current_lang] : $np['name'][0] ?></a></div>
                    <div class="side_last_item_price"><?= get_price($np['current_price']) ?></div>
                    <div class="side_last_item_incart"><a href="/<?= $np['cat_url'] ?>/<?= $np['url'] ?>/"><?= $this->lang->line('catalog_nav_button_to_product') ?></a></div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>