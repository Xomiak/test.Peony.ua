<div class="title_hot">Лучшее предложение</div>
<?php
$shop_special = $this->model_options->getOption('shop_special');
$arr = explode('|', $shop_special);
if(($arr !== false) && is_array($arr))
{
    $count = count($arr);
    for($i = 0; $i < $count; $i++)
    {
        $this->db->where('active', 1);
        $this->db->where('id', $arr[$i]);
        $this->db->limit(1);
        $a = $this->db->get('shop')->result_array();
        if($a)
        {
            $a = $a[0];
            $cat = $this->model_categories->getCategoryById($a['category_id']);
            ?>
            <div class="cat_one">
                <div class="cat_image">
                    <img class="cat_image_main" src="<?=$a['image']?>" alt="<?=$a['name']?>" />
                    <a href="/catalog/<?=$cat['url']?>/<?=$a['url']?>/"><div class="cat_buy_button"><input type="submit" value="КУПИТЬ"/></div></a>
                </div>
                <div class="cat_bottom">
                    <span href="<?=$a['name']?>">Платье MNG</span>
                    <span href="#"><?=get_price($a['price'])?>&nbsp;<?=$currency?></span>
                </div>
            </div>
            <?php
        }
    }
}
?>
