<?php include("application/views/head.php"); ?>
<?php include("application/views/header.php"); ?>
<div class="container">
<div class="contant">

    <h1>Карта сайта</h1>
    <p class="sitemap"><a href="/">Главная</a></p>
	<p class="sitemap"><a href="/o-kompanii/">О компании</a></p>
<?php
if($categories)
{
    $count = 0;
    while(isset($categories[$count]))
    {
        $category = $categories[$count];
        
        echo '<p class="sitemap" style="padding:0;">'.$category['name'].'</p>';
        
        $articles = $this->articles->getArticlesByCategory($category['id']);
        $acount = count($articles);
        for($i = 0; $i < $acount; $i++)
        {
            $article = $articles[$i];
            
            echo '<p class="article_sitemap"><a href="/'.$category['url'].'/'.$article['url'].'/">'.$article['name'].'</a></p>';
        }
        
        $count++;
    }
}
?>	<p class="sitemap"></p>
    <p class="sitemap" style="padding-bottom: 0px !important;"><a href="/gallery/">Фотогалерея</a></p>
    <?php
if($gallery)
{
    $count = 0;
    while(isset($gallery[$count]))
    {
        $category = $gallery[$count];
        
        echo '<p class="article_sitemap"><a href="/gallery/'.$category['url'].'/">'.$category['name'].'</a>';
        
        $count++;
    }
}
?>
    <p class="sitemap"></p>
	<p class="sitemap" style="padding:0;">Объекты</p>
		<p class="article_sitemap"><a href="/obekti/">Галерея Афина</a></p>
		<p class="article_sitemap" style="padding-bottom:20px;"><a href="/kompaniya-Pfizer/">Компания Pfizer</a></p>
	<p class="sitemap"><a href="/gallery/">Контакты</a></p>
    
</div>
</div>
<?php include("application/views/footer.php"); ?>