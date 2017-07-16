<?php include("application/views/header.php") ?>

<table width="1000px" cellpadding="0" cellspacing="0" align="center" border="0">
    <tr>
        <td valign="top">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="180px" align="left" valign="top">
                        <!-- LEFT MENU -->
                        <?php showLeftMenu(); ?>
                        <!-- /LEFT MENU -->
                        <br />
                        <center>
                        <?php getBanners('left'); ?>
                        </center>
                    </td>
                    <td valign="top">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td valign="top">
                                    <div class="kroshki">
                                        <div xmlns:v="http://rdf.data-vocabulary.org/#">
                                            <span typeof="v:Breadcrumb">
                                                <a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
                                            </span>
                                            &nbsp;»&nbsp;
                                            Поиск
                                        </div>
                                    </div>
                                    <h1 class="art">Поиск</h1>
                                    <p><strong>Поиск по дате:</strong> <i><?=$_POST['search']?></i></p>
                                    <?php
                                    if(!$articles)
                                    {
                                        echo '<p>По Вашему запросу ничего не найдено...</p>';
                                    }
                                    else
                                    {
                                        $count = count($articles);
                                        $oldCat = '';
                                        $oldDate = '';
                                        for($i = 0; $i < $count; $i++)
                                        {                                            
                                            $a = $articles[$i];
                                            if($a['active'] == 1)
                                            {
                                                $carr = explode('*',$a['category_id']);
                                                $fcat = $carr[0];
                                                $cat = $this->cat->getCategoryById($fcat);
                                                $date = '';
                                                if($a['date'] != '')
                                                {
                                                    $darr = explode('-',$a['date']);
                                                    $date = $darr[2].' '.getMonthName($darr[1]).' '.$darr[0];
                                                }
                                                ?>
                                                <?php
                                                if($date != $oldDate)
                                                {
                                                ?>
                                                    <p class="news_date2"><?=$date?></p>                                                    
                                                <?php
                                                }                                                
                                                $oldDate = $date;
                                                
                                                if($cat['name'] != $oldCat)
                                                {
                                                ?>
                                                    <div class="modile_hd">
                                                        <span class="sp2"><?=$cat['name']?></span>
                                                    </div>
                                                <?php
                                                }
                                                $oldCat = $cat['name'];
                                                
                                                
                                                $parent = '';
                                                if($cat['parent'] != 0)
                                                {
                                                    $parent = $this->cat->getCategoryById($cat['parent']);
                                                }
                                                ?>
                                                <table cellpadding="0" cellspacing="0" width="98%">
                                                    <tr>
                                                        <td width="50px"><?=$a['time']?></td>
                                                        <td>
                                                            <?php
                                                            echo '<a href="/';
                                                            if($parent != '') echo $parent['url'];
                                                            else echo $cat['url'];
                                                            echo '/'.$a['url'].'/">'.$a['name'].'</a>';
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>
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
        <td width="180px" valign="top" align="center">            
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
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php include("application/views/footer.php") ?>