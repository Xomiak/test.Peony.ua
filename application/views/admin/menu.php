<div class="menu_border">
    <div class="menu_title">Меню</div>    
</div>
<div class="menu">
<?php
$this->db->where('active','1');
if(userdata('type') != 'admin') $this->db->like('access', userdata('type'));
$menuss = $this->db->get('admin_menu')->result_array();
$count = count($menuss);
for($i = 0; $i < $count; $i++)
{
    $menuuuu = $menuss[$i];
    ?>
    <div class="menu_category">
        <a href="<?=$menuuuu['url']?>">
            <?=$menuuuu['name']?>
        </a>
        <?php
        if($menuuuu['url'] == '/admin/comments/')
        {           
            $com_count = getNewCommentsCount();
            if($com_count > 0) echo "(".$com_count.")";
        }
        elseif($menuuuu['url'] == '/admin/orders/')
        {
            $newOrders = getNewOrdersCount();
            if($newOrders > 0) echo "(".$newOrders.")";
        }
        ?>
        <img src="/img/admin/menu_<?php if($_SERVER['REQUEST_URI'] != $menuuuu['url']) echo 'not_'; ?>active.png" width="13px" height="11px" alt="<?=$menuuuu['name']?>" title="<?=$menuuuu['name']?>" />
    </div>
    <?php
    
    if($_SERVER['REQUEST_URI'] == $menuuuu['url'] && $menuuuu['view'] != '')
    {
        $showCats = false;
        $type = false;
        if($menuuuu['view'] == 'articles_categories'){
            $showCats = true;
            $type = 'articles';
        } elseif ($menuuuu['view'] == 'shop_categories'){
            $showCats = true;
            $type = 'shop';
        }
        if($showCats)
        {
            $this->db->where('parent','0');
            if($type) $this->db->where('type',$type);
            $mcats = $this->db->get('categories')->result_array();
            $mcatscount = count($mcats);
            for($j = 0; $j < $mcatscount; $j++)
            {
                $mcat = $mcats[$j];
                ?>
                <div class="menu_subcategory">
                    <a href="/admin/articles/category/<?=$mcat['id']?>/"><?=$mcat['name']?></a>
                    <img src="/img/admin/menu_sub_<?php if($_SERVER['REQUEST_URI'] != $menuuuu['url']) echo 'not_'; ?>active.png" width="13px" height="11px" alt="<?=$mcat['name']?>" title="<?=$mcat['name']?>" />
                </div>
                <?php
                
                
                $this->db->where('parent',$mcat['id']);
                $msubcats = $this->db->get('categories')->result_array();
                if($msubcats)
                {
                    $msubcatscount = count($msubcats);
                    for($j2 = 0; $j2 < $msubcatscount; $j2++)
                    {
                        $msubcat = $msubcats[$j2];
                        ?>
                        <div class="menu_subsubcategory">
                            <a href="/admin/articles/category/<?=$msubcat['id']?>/"><?=$msubcat['name']?></a>
                            <img src="/img/admin/menu_sub_<?php if($_SERVER['REQUEST_URI'] != $menuuuu['url']) echo 'not_'; ?>active.png" width="13px" height="11px" alt="<?=$msubcat['name']?>" title="<?=$msubcat['name']?>" />
                        </div>
                        <?php
                    }
                }
            }
        }
    }
}

?>
</div>

