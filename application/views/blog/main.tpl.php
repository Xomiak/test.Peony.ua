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
                                            <?=$h1?>
                                        </div>
                                    </div>
                                    <h1 class="blog_contents_h1">
                                    <?=$h1?>
                                    </h1>
                                    
                                    <?php
                                    if($content)
                                    {
                                        $count = count($content);
                                        for($i = 0; $i < $count; $i++)
                                        {
                                            $c = $content[$i];
                                            $blog = $this->blogs->getBlogById($c['blog_id'],1);                                            
                                            if($blog)
                                            {
                                                $user = $this->users->getUserByLogin($blog['login']);
                                                ?>
                                                <table width="100%">
                                                    <tr>
                                                        <td>
                                                            <h3 class="blog_name_link"><a href="/blog/user/<?=$blog['url']?>/"><?=$blog['name']?></a></h3>
                                                        </td>
                                                        <td align="right">
                                                            <table align="right">
                                                                <tr>
                                                                    <td>
                                                                        <div class="blog_user_link" align="center">
                                                                            <a href="/user/<?=$user['id']?>/"><?=$user['login']?></a>
                                                                            <br />
                                                                            <a href="/user/<?=$user['id']?>/">
                                                                            <?php
                                                                            if($user['avatar'] != '')
                                                                            {
                                                                                ?>
                                                                                    <img width="50px" src="<?=$user['avatar']?>" alt="Пользователь <?=$user['login']?>" title="Пользователь <?=$user['login']?>" />
                                                                                <?php
                                                                            }
                                                                            else
                                                                            {
                                                                                ?>
                                                                                <img width="50px" src="/img/no_ava.png" alt="Пользователь <?=$user['login']?>" title="Пользователь <?=$user['login']?>" />
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                                
                                                </div>
                                                
                                                <p class="content_blog_name_link"><a href="/blog/user/<?=$blog['url']?>/<?=$c['id']?>/"><?=$c['name']?></a></p>
                                                <div class="content_blog_short_content">
                                                <?php
                                                if($c['image'] != '')
                                                {
                                                    ?>
                                                    <a href="/blog/user/<?=$blog['url']?>/<?=$c['id']?>/">
                                                        <img class="blog_contents_img" src="/upload/blogs/mini/<?=$c['image']?>" border="0" alt="<?=$blog['name']?> - <?=$c['name']?>" title="<?=$blog['name']?> - <?=$c['name']?>" />
                                                    </a>
                                                    <?php
                                                }
                                                ?>
                                                <?=$c['short_content']?>
                                                <div class="cclear"></div>
                                                <p align="right">
                                                    <a title="Перейти к записи Первая запись" href="/blog/user/1/1/"> Подробнее... </a>
                                                </p>
                                                </div>
                                                <div class="cclear"></div>
                                                <hr class="blog_contents_hr">
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
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