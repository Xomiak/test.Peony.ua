<div class="filters">
    <?php
    $filter_max_price = getOption('filter_max_price');
    $filter_step = getOption('filter_step');
    
    $min_price = userdata('min_price');
    $max_price = userdata('max_price');
    
    if(!$min_price) $min_price = 0;
    if(!$max_price) $max_price = $filter_max_price;
    ?>
    <script>
        var j = jQuery.noConflict();
        j(function() {
		j('#price').change(function () {
		var val = j(this).val();
		j('#slider_price').slider("values",0,val);
		});	
		
		j('#price2').change( function() {
			var val2 = j(this).val();
			j('#slider_price').slider("values",1,val2);
		});
	
		j( "#slider_price" ).slider({
			range: true,
			//orientation: "vertical",
			min: 0,
			step:<?=$filter_step?>,
			max: <?=$filter_max_price?>,
			values: [ <?=$min_price?>, <?=$max_price?> ],
			slide: function( event, ui ) {
				//j( "#amount" ).val( "j" + ui.values[ 0 ] + " - j" + ui.values[ 1 ] );
				j('#price').val(ui.values[0]);
				j('#price2').val(ui.values[1]);
			}
		});
		//j( "#amount" ).val( "j" + j( "#slider-range" ).slider( "values", 0 ) +
			//" - j" + j( "#slider-range" ).slider( "values", 1 ) );
			j('#price').val(j('#slider_price').slider("values",0));
			j('#price2').val(j('#slider_price').slider("values",1));
	});
    </script>
    <div class="filterstitle">Фильтры</div>
    <?php
    $filter_brand = userdata('filter_brand');
    $filter_color = userdata('filter_color');
    if(($filter_brand || $filter_color) || $filter_color || $filter_brand)
    {
        ?>
        <a class="filters_reset" rel="nofollow" href="?filters_reset">Сбросить фильтр</a>
        <?php
    }
    ?>
    <div class="filter_title">По цвету</div>
    <ul>
        <?php
        $f_values = $this->filters->getFilterValues(1,1);
        if($f_values)
        {
            $count = count($f_values);
            for($i = 0; $i < $count;$i++)
            {
                $fv = $f_values[$i];
                
                ?>
                <li><a href="?filter_color=<?=$fv['id']?>&back=<?=urlencode($_SERVER['REQUEST_URI'])?>"><?=$fv['value']?></a></li>
                <?php
            }
        }
        ?>
    </ul>
    <div class="filter_title">По бренду</div>
    <ul>
        <?php
        $brands = $this->products->getBrands(1);
        if($brands)
        {
            $count = count($brands);
            for($i = 0; $i < $count; $i++)
            {
                $brand = $brands[$i];
                ?>
                <li><a rel="nofollow" href="?filter_brand=<?=$brand['id']?>&back=<?=urlencode($_SERVER['REQUEST_URI'])?>"><?=$brand['name']?></a></li>
                <?php
            }
        }
        ?>        
    </ul>
    <div class="filter_title">По цене</div>
    <div id="options">
                <div id="slider_price"></div>
        <form class="diapazon" method="post">
                        <label for="price">от:
                                <input type="text" name="min_price" id="price">
                        </label>
                        
                        <label for="price2">до:
                                <input type="text" name="max_price" id="price2">
                        </label>
            <input style="padding: 4px 13px !important;margin-top:15px;position:relative;left:-5px;" type="submit" value="ОК"/>
                </form>
        </div>
</div>