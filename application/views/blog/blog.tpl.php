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
                                            <?=$h1?>
                                        </div>
                                    </div>
                                    <center><?php getBanners('top'); ?></center>
                                    <h1 class="blog_contents_h1">
                                    <?=$h1?>
                                    </h1>
                                    
                                    <p class="blog_contents_user">Блог пользователя <a href="/users/<?=$user['id']?>/"><i><?=$user['login']?></i></a></p>
                                    <?php
                                    if($user['login'] == $this->session->userdata('login'))
                                    {
                                        ?>
                                        <p class="blog_user_options"><a rel="nofollow" href="/blog/add-blog-content/<?=$blog['id']?>/">Добавить запись</a></p>
                                        <p class="blog_user_options"><a rel="nofollow" href="/blog/edit/<?=$blog['id']?>/">Редактировать блог</a></p>
                                        <br />
                                        <?php
                                    }
                                    ?>
                                    
                                    <?php
                                    if($blog['image'] != '')
                                    {
                                        ?>
                                        <img class="blog_img" border="0" src="/upload/blogs/normal/<?=$blog['image']?>" alt="<?=$blog['name']?>" title="<?=$blog['name']?>" border="0" />
                                        <?php
                                    }
                                    ?>
                                    <?=$blog['content']?>
                                    <hr />
                                    <?php
                                    // Показываем список записей в блоге
                                    if($blog_content)
                                    {
                                        $count = count($blog_content);
                                        for($i = 0; $i < $count; $i++)
                                        {                                            
                                            $bc = $blog_content[$i];
                                            
                                            if($bc['visible'] == 1 || $bc['login'] == $this->session->userdata('login'))
                                            {
                                                ?>
                                                <div class="blog_content_list<?php if($bc['visible'] == 0) echo '_not_visible'; ?>">
                                                    <?php
                                                    if($bc['login'] == $this->session->userdata('login'))
                                                    {
                                                        ?>
                                                        <p class="blog_contents_user_buttons" align="right">
                                                            <a rel="nofollow" href="/blog/edit-blog-content/<?=$bc['id']?>/">
                                                                Редактировать
                                                            </a>
                                                            &nbsp;|&nbsp;
                                                            <a rel="nofollow" href="/blog/del-blog-content/<?=$bc['id']?>/" onclick="return confirm('Вы действительно хотите удалить запись в блоге?')">
                                                                Удалить
                                                            </a>
                                                        </p>
                                                        <?php
                                                    }
                                                    
                                                    if($bc['login'] == $this->session->userdata('login') && $bc['visible'] == 0)
                                                    {
                                                        ?>
                                                        <p class="blog_content_not_visible_p">[Черновик]</p>
                                                        <?php
                                                    }
                                                    ?>
                                                    <a href="/blog/user/<?=$blog['url']?>/<?=$bc['id']?>/" title="Перейти к записи <?=$bc['name']?>">
                                                        <h2 class="blog_contents_h2"><?=$bc['name']?></h2>
                                                    </a>
                                                    <p class="blog_contents_date"><?=$bc['date']?> <?=$bc['time']?></p>
                                                    <?php
                                                    if($bc['image'] != '')
                                                    {
                                                        ?>
                                                        <a href="/blog/user/<?=$blog['url']?>/<?=$bc['id']?>/">
                                                            <img class="blog_contents_img" src="/upload/blogs/mini/<?=$bc['image']?>" alt="<?=$blog['name']?> - <?=$bc['name']?>" />
                                                        </a>
                                                        <?php
                                                    }
                                                    
                                                    if(trim($bc['short_content']) == '')
                                                    {
                                                        echo substr($bc['content'], 0, 200).' ...';
                                                    }
                                                    else echo $bc['short_content'];
                                                    ?>
                                                    <div class="cclear"></div>
                                                    <p align="right">
                                                        <a href="/blog/user/<?=$blog['url']?>/<?=$bc['id']?>/" title="Перейти к записи <?=$bc['name']?>">
                                                            Подробнее...
                                                        </a>
                                                    </p>
                                                    <hr class="blog_contents_hr" />
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    <center><?=$pager?></center>
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