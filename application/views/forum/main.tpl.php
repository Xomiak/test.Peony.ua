<?php include('application/views/forum/head.php'); ?>
<?php include('application/views/forum/header.php'); ?>


<ul class="linklist">
	
		<li><a href="/forum/new-topics/">Новые темы</a> • <a href="/active-topics/">Активные темы</a></li>
	
</ul>
<?php
if($sections)
{
    $count = count($sections);
    for($i = 0; $i < $count; $i++)
    {
        $sect = $sections[$i];        
        ?>
        <div class="forabg">
			<div class="inner"><span class="corners-top"><span></span></span>
			<ul class="topiclist">

				<li class="header">
					<dl class="icon">
						<dt><a href="/forum/<?=$sect['url']?>/"><?=$sect['name']?></a></dt>
						<dd class="topics">Темы</dd>
						<dd class="posts">Сообщения</dd>
						<dd class="lastpost"><span>Последнее сообщение</span></dd>
					</dl>

				</li>
			</ul>
			<ul class="topiclist forums">
	
                        <?php
                        $secs2 = $this->forum->getParentSections($sect['id'],1);
                        $tcount = count($secs2);
                        for($j = 0; $j < $tcount; $j++)
                        {
                            $sec2 = $secs2[$j];
                            ?>
                            <li class="row">
                                <dl class="icon" style="background-image: url(/img/forum/forum_read.gif); background-repeat: no-repeat;">
                                        <dt title="Нет непрочитанных сообщений">
                                        
                                                <a href="/forum/<?=$sect['url']?>/<?=$sec2['url']?>/" class="forumtitle"><?=$sec2['name']?></a><br>
                                                <?=$sec2['descr']?>
                                                
                                        </dt>
        
                                        
                                                <dd class="topics">1 <dfn>Темы</dfn></dd>
                                                <dd class="posts">1 <dfn>Сообщения</dfn></dd>
                                                <dd class="lastpost"><span>
                                                        <dfn>Последнее сообщение</dfn>  <a href="http://odessit.in.ua/forum/memberlist.php?mode=viewprofile&amp;u=2&amp;sid=5a092d76e355977bd56f39602217fb41" style="color: #AA0000;" class="username-coloured">admin</a>
                                                        <a href="http://odessit.in.ua/forum/viewtopic.php?f=2&amp;p=1&amp;sid=5a092d76e355977bd56f39602217fb41#p1"><img src="/img/forum/icon_top.gif" width="11" height="9" alt="Перейти к последнему сообщению" title="Перейти к последнему сообщению"></a> <br>15 сен 2011, 08:43</span>
        
                                                </dd>
                                        
                                </dl>
                            </li>
                            <?php
                        }
                        
                        ?>
	
			</ul>

			<span class="corners-bottom"><span></span></span></div>
		</div>
        <?php
    }
}
?>
		
	
	
<?php include('application/views/forum/footer.php'); ?>