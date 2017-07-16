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
    <h1><?=$h1?></h1>
    <a href="/sitemap/">Назад</a>
    <p><strong>Разделы:</strong></p>
<?php
if($categories)
{
    $count = 0;
    while(isset($categories[$count]))
    {
        $category = $categories[$count];
        
        echo '<p><a href="/gallery/'.$category['url'].'/">'.$category['name'].'</a>';
        
        $count++;
    }
}
?>
    <br /><br /><br />
<p><a href="/sitemap/">Назад</a></p>
</body>
</html>