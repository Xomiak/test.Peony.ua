<!doctype html>
<html>
<head>
 <meta charset="utf-8">
  
 <title><?=$title?></title>
 <meta name="keywords" content="<?=$keywords?>" />
 <meta name="description" content="<?=$description?>" />
 <meta name="robots" content="<?=$robots?>" />
</head>
<body>
    <a href="/sitemap/">Назад</a>
    <a href="/<?=$category['url']?>/"><h1><?=$h1?></h1></a>
<?php
echo $pager;
if($articles)
{
    $count = 0;
    while(isset($articles[$count]))
    {
        $article = $articles[$count];
        
        echo '<p><a href="/'.$category['url'].'/'.$article['url'].'/">'.$article['name'].'</a>';
        
        $count++;
    }
}
echo $pager;
?>
<p><a href="/sitemap/">Назад</a></p>
</body>
</html>