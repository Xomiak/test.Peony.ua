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
                    <div class="top_menu_link"><a href="/admin/banners/">Баннеры</a></div>
                    <div class="top_menu_link"><a href="/admin/banners/add/">Добавить баннер</a></div>
                </div>
                <form action="/admin/banners/edit/<?=$banner['id']?>/" method="post" enctype="multipart/form-data">
                    <table>
                        <tr>
                            <td>Название *:</td>
                            <td><input required type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name']; else echo $banner['name'];?>" /></td>
                        </tr>
                        <tr>
                            <td>Формат *:</td>
                            <td><input type="text" name="format" value="<?php if(isset($_POST['format'])) echo $_POST['format']; else echo $banner['format'];?>" size="50" /></td>
                            <td>* Добавит к тэгу img указанные параметры</td>
                        </tr>
                        <tr>
                            <td>Загрузить баннер:</td>
                            <td><input type="file" name="userfile" /></td>
                        </tr>
                        <tr>
                            <td>Контент:</td>
                            <td>
                            	<!-- <textarea name="content" class="ckeditor"><?php if(isset($_POST['content'])) echo $_POST['content']; else echo $banner['content'];?></textarea> -->
								<?php
								if($banner['image'] != '')
								{
									if($banner['url'] != '') echo '<a target="_blank" href="'.$banner['url'].'">';
									?>
									<img src="<?=$banner['image']?>" style="max-width: 800px" />
									<?php
									if($banner['url'] != '') echo '</a>';
								}
								?>
                            </td>
                        </tr>
                        <tr>
                            <td>url:</td>
                            <td><input type="text" name="url" value="<?php if(isset($_POST['url'])) echo $_POST['url']; else echo $banner['url'] ?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td>NUM:</td>
                            <td><input type="text" name="num" value="<?php if(isset($_POST['num'])) echo $_POST['num']; else echo $banner['num'] ?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td>Позиция:</td>
                            <td><input type="text" name="position" value="<?php if(isset($_POST['position'])) echo $_POST['position']; else echo $banner['position'] ?>" size="50" /></td>
                        </tr>
                        <tr>
                            <td>Счётчик:</td>
                            <td><input type="text" name="count" value="<?php if(isset($_POST['count'])) echo $_POST['count']; else echo $banner['count']; ?>" size="7" /></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="checkbox" name="active"<?php if($banner['active'] == 1) echo ' checked' ?> /> Активный</td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" value="Редактировать" /></td>
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