<?php
include("header.php");
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" valign="top"><?php include("menu.php"); ?></td>
        <td width="20px"></td>
        <td valign="top">
            <div class="title_border">
                <div class="content_title"><h1><?=$title?></h1></div>
                <div class="back_and_exit">
                    <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться на сайт ></a></span>
                    <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                </div>
            </div>
            
            <div class="content">
                <div class="top_menu">                   
                    <div class="top_menu_link"><a href="/admin/gallery/">Галерея</a></div>
                    <div class="top_menu_link"><a href="/admin/gallery/add/">Добавить фотку</a></div>
                    <div class="top_menu_link"><a href="/admin/gallery/zip_import/">Импорт zip архива</a></div>
                    <div class="top_menu_link"><a href="/admin/gallery/categories/">Разделы галереи</a></div>
                    <div class="top_menu_link"><a href="/admin/gallery/categories/add/">Добавить раздел галереи</a></div>                    
                    <div class="top_menu_link"><a href="/admin/options/set_module/gallery/">Настройки галереи</a></div>
                </div>
                <strong><font color="Red"><?=$err?></font></strong>
                <form enctype="multipart/form-data" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                    <table>
                        <tr>
                            <td>Название *:</td>
                            <td><input required type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" /></td>
                        </tr>                    
                        <tr>
                            <td>Раздел *:</td>
                            <td>
                                <SELECT required name="category_id">
                                    <option value=""></option>
                                    <?php
                                    if($categories)
                                    {
                                        $count = count($categories);
                                        for($i = 0; $i < $count; $i++)
                                        {
                                            $c = $categories[$i];
                                            echo '<option value="'.$c['id'].'"';
                                            if($this->session->userdata('gallery_category_id') == $c['id']) echo ' selected';
                                            echo '>'.$c['name'].'</option>';
                                            $subs = $this->gallery->getSubCategories($c['id']);
                                            if($subs)
                                            {
                                                $scount = count($subs);
                                                for($j = 0; $j < $scount; $j++)
                                                {
                                                    $s = $subs[$j];
                                                    echo '<option value="'.$s['id'].'"';
                                                    if($this->session->userdata('gallery_category_id') == $s['id']) echo ' selected';
                                                    echo '>&nbsp;└&nbsp;'.$s['name'].'</option>';
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </SELECT>
                            </td>
                        </tr>
                        <tr>
                            <td>ZIP архив *:</td>
                            <td><input required type="file" name="userfile" /></td>
                        </tr>
                        
                        <tr>
                            <td colspan="2"><input type="submit" value="Добавить" /></td>
                        </tr>
                    </table>
                </form>
            </div>
        </td>
    </tr>
</table>
<?php
include("footer.php");
?>