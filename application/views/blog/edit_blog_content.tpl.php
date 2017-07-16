<?php include('application/views/header.php'); ?>
<?php include('application/views/left.inc.php'); ?>
<?php include('application/views/right.inc.php'); ?>

<div id="content">
                                    <div class="kroshki">
                                        <div xmlns:v="http://rdf.data-vocabulary.org/#">
                                            <span typeof="v:Breadcrumb">
                                                <a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
                                            </span>
                                            &nbsp;»&nbsp;
                                            <span typeof="v:Breadcrumb">
                                                <a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/blog/">Блог</a>
                                            </span>
                                            &nbsp;»&nbsp;
                                            <?=$h1?>
                                        </div>
                                    </div>
                                    <center><?php getBanners('top'); ?></center>
                                    <h1 class="long">
                                    <?=$h1?>
                                    </h1>
  
                                    <form enctype="multipart/form-data" action="/blog/edit-blog-content/<?=$blog['id']?>/" method="post">
                                        <table>
                                            <tr>
                                                <td valign="top">
                                                    Название *:
                                                </td>
                                                <td>
                                                    <input type="text" name="name" value="<?php if(isset($_POST['name'])) echo $_POST['name']; else echo $blog['name'] ?>" />
                                                    <?php
                                                    if(isset($err['name']))
                                                    {
                                                        ?>
                                                        <div class="error"><?=$err['name']?></div>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">
                                                    Фото:
                                                </td>
                                                <td>
                                                    <?php
                                                    if($blog['image'] != '')
                                                    {
                                                        ?>
                                                        <img src="/upload/blogs/mini/<?=$blog['image']?>" />
                                                        <input type="hidden" name="old_image" value="<?=$blog['image']?>" />
                                                        <br />
                                                        <input type="checkbox" name="del_image" /> Удалить фото
                                                        <br />
                                                        <?php
                                                    }
                                                    ?>
                                                    <input type="file" name="userfile" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">
                                                    Краткое описание:
                                                </td>
                                                <td>
                                                    <textarea name="short_content" class="tinymce"><?php if(isset($_POST['short_content'])) echo $_POST['short_content']; else echo $blog['short_content'] ?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">
                                                    Контент *:
                                                </td>
                                                <td>
                                                    <textarea name="content" class="tinymce"><?php if(isset($_POST['content'])) echo $_POST['content']; else echo $blog['content'] ?></textarea>
                                                    <?php
                                                    if(isset($err['content']))
                                                    {
                                                        ?>
                                                        <div class="error"><?=$err['content']?></div>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <input type="submit" value="Сохранить" />
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                </div>

<?php include('application/views/footer.php'); ?>