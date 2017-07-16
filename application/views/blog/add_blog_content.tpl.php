<?php include("application/views/header.php") ?>

<table width="1040px" cellpadding="0" cellspacing="0" align="center" border="0">
    <tr>
        <td valign="top">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="200px" align="left" valign="top">
                        <!-- LEFT MENU -->
                        <?php userMenu(); ?>
                        <?php showLeftMenu(); ?>
                        <!-- /LEFT MENU -->
                        <br />
                        <center>
                        <?php getBanners('left'); ?>
                        </center>
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
                                                <a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/blog/">Блог</a>
                                            </span>
                                            &nbsp;»&nbsp;
                                            Добавление записи в блог
                                        </div>
                                    </div>
                                    <center><?php getBanners('top'); ?></center>
                                    <h1 class="long">
                                    <?=$h1?>
                                    </h1>
  
                                    <form enctype="multipart/form-data" action="/blog/add-blog-content/<?=$blog['id']?>/" method="post">
                                        <table>
                                            <tr>
                                                <td valign="top">
                                                    Название *:
                                                </td>
                                                <td>
                                                    <input type="text" name="name" value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>" />
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
                                                    <input type="file" name="userfile" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">
                                                    Краткое описание:
                                                </td>
                                                <td>
                                                    <textarea name="short_content" class="tinymce"><?php if(isset($_POST['short_content'])) echo $_POST['short_content']; ?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">
                                                    Контент *:
                                                </td>
                                                <td>
                                                    <textarea name="content" class="tinymce"><?php if(isset($_POST['content'])) echo $_POST['content']; ?></textarea>
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
                                                <td colspan="2">
                                                    <input type="checkbox" name="visible" /> Черновик
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <input type="submit" value="Добавить" />
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                </td>
                            </tr>
                        </table>                        
                    </td>
                </tr>
            </table>            
        </td>
        <td width="200px" valign="top" align="center">            
            <table cellpadding="0" cellspacing="0" bgcolor="#f1efe3" width="100%">
                <tr>
                    <td align="center">
                        <br />
                        <table align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td><img src="/img/adv-line.png" /></td>
                                <td><span style="color: #A4A8AB; font-size: 11px; padding: 0 4px; font-family: Tahoma,Verdana,Arial,Helvetica,Sans-serif;">Реклама</span></td>
                                <td><img src="/img/adv-line.png" /></td>
                            </tr>
                        </table>
                        <?php getBanners('right'); ?>
                        
                        <?php
                        $this->load->helper('modules_helper');
                        showTop5();
                        //showStandartModule("Скоро в пресс-центре");
                        showDateTimeModule("Анонсы");
                        showStandartModule("Пресс-центр");
                        ?>
                        <div class="modile_hd">
                            <span class="sp2">Подписаться</span>
                        </div>
                        <iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fodessamedia&width=200&amp;colorscheme=light&amp;show_faces=true&amp;stream=false&amp;header=false&amp;height=380" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:380px;" allowTransparency="true"></iframe>
                        <script type="text/javascript" src="http://userapi.com/js/api/openapi.js?22"></script>
                        <!-- VK Widget -->
                        <div id="vk_groups"></div>
                        <script type="text/javascript">
                        VK.Widgets.Group("vk_groups", {mode: 0, width: "200", height: "290"}, 32416938);
                        </script>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php include("application/views/footer.php") ?>