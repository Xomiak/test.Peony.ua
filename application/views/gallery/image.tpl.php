<?php include("application/views/header.php") ?>

<table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">
    <tr>
        <td valign="top">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="200px" align="left" valign="top" style="padding-left:10px;">
                        <?php
                        include('application/views/left.tpl.php');
                        ?>
                    </td>
                    <td valign="top">
                        <table width="97%" cellpadding="0" cellspacing="0" border="0" align="center">
                            <tr>
                                <td valign="top">
                                    <div class="kroshki">
                                        <div xmlns:v="http://rdf.data-vocabulary.org/#">
                                            <span typeof="v:Breadcrumb">
                                                <a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
                                            </span>
                                            &nbsp;»&nbsp;
                                            <span typeof="v:Breadcrumb">
                                                <a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/gallery/">Галерея</a>
                                            </span>
                                            &nbsp;»&nbsp;
                                            <?php
                                            if($parent)
                                            {
                                                ?>
                                                <span typeof="v:Breadcrumb">
                                                    <a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/gallery/<?=$parent['url']?>/"><?=$parent['name']?></a>
                                                </span>
                                                &nbsp;»&nbsp;
                                                <?php
                                            }
                                            ?>
                                            <span typeof="v:Breadcrumb">
                                                <a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/gallery/<?php if($parent) echo $parent['url'].'/'; ?><?=$category['url']?>/"><?=$category['name']?></a>
                                            </span>
                                            &nbsp;»&nbsp;
                                            <?=$image['name']?>
                                        </div>
                                    </div>
                                    <center><?php getBanners('top'); ?></center>
                                    <h1 class="long">                                        
                                        <?=$image['name']?>
                                        <?php
                                        if (isClientAdmin())
                                            echo '<a href="/admin/gallery/edit/'.$image['id'].'/" rel="nofollow"><img src="/img/edit.png" border="0" title="Перейти к редактированию" /></a>';
                                        ?>
                                    </h1>
                                    
                                    <div class="gallery_image_content">
                                    <?php
                                    $folder = "";
                                    if($category['folder'] != "") $folder = "categories/".$category['folder'].'/';
                                    ?>
                                        <a rel="lightbox" href="/upload/gallery/<?=$folder?><?=$image['image']?>" title="<?=$image['name']?>">
                                            <img class="g_big_image" src="/upload/gallery/<?=$folder?>normal/<?=$image['image']?>" border="0" alt="<?php if($parent) echo $parent['name'].' - '; ?><?=$category['name']?> - <?=$image['name']?>" title="<?php if($parent) echo $parent['name'].' - '; ?><?=$category['name']?> - <?=$image['name']?>" />
                                        </a>
                                        
                                        <p><strong>Просмотров:</strong> <?=$image['count']?></p>
                                        <?php
                                        $date = '';
                                        $d = explode('-',$image['date']);
                                        if(count($d) == 3)
                                        {
                                            $date = '<p><strong>Добавлена: </strong>'.$d[2].' '.getMonthName($d[1]).' '.$d[0].' года';
                                            if($image['time'] != '') $date .= ' в '.$image['time'];
                                            $date .= '</p>';
                                        }
                                        
                                        ?>
                                        <?=$date?>
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center" width="50%">
                                                    <?php
                                                    if($prev)
                                                    {
                                                        ?>
                                                        <a href="/gallery/<?php if($parent) echo $parent['url'].'/'; ?><?=$category['url']?>/image/<?=$prev['id']?>/">
                                                            <strong>Предыдущая</strong>
                                                        </a>
                                                        <br />
                                                        <a href="/gallery/<?php if($parent) echo $parent['url'].'/'; ?><?=$category['url']?>/image/<?=$prev['id']?>/">
                                                            <img class="g_next_prev" src="/upload/gallery/mini/<?=$prev['image']?>" border="0" alt="<?php if($parent) echo $parent['name'].' - '; ?><?=$category['name']?> - <?=$prev['name']?>" title="<?php if($parent) echo $parent['name'].' - '; ?><?=$category['name']?> - <?=$prev['name']?>" />
                                                        </a>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td align="center" width="50%">
                                                    <?php
                                                    if($next)
                                                    {
                                                        ?>
                                                        <a href="/gallery/<?php if($parent) echo $parent['url'].'/'; ?><?=$category['url']?>/image/<?=$next['id']?>/">
                                                            <strong>Следующая</strong>
                                                        </a>
                                                        <br />
                                                        <a href="/gallery/<?php if($parent) echo $parent['url'].'/'; ?><?=$category['url']?>/image/<?=$next['id']?>/">
                                                            <img class="g_next_prev" src="/upload/gallery/mini/<?=$next['image']?>" border="0" alt="<?php if($parent) echo $parent['name'].' - '; ?><?=$category['name']?> - <?=$next['name']?>" title="<?php if($parent) echo $parent['name'].' - '; ?><?=$category['name']?> - <?=$next['name']?>" />
                                                        </a>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <?php
                                    // КОММЕНТАРИИ //
                                    include('application/views/comments.tpl.php');
                                    ?>
                                </td>
                            </tr>
                        </table>                        
                    </td>
                </tr>
            </table>            
        </td>
       <td width="200px" valign="top" align="center">            
            <?php
            include('application/views/right.tpl.php');
            ?>
        </td>
    </tr>
</table>
<?php include("application/views/footer.php") ?>