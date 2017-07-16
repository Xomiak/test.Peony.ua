<?php
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>http://<?=$_SERVER['SERVER_NAME']?>/</loc>
        <lastmod><?=date("Y-m-d")?></lastmod>
        <changefreq>always</changefreq>
        <priority>1</priority>
    </url>

    <?php
    if($categories)
    {
        $count = 0;
        while(isset($categories[$count]))
        {
            $category = $categories[$count];
            ?>
            <url>
                <loc>http://<?=$_SERVER['SERVER_NAME']?>/<?=$category['url']?>/</loc>
                <lastmod><?=date("Y-m-d")?></lastmod>
                <changefreq>always</changefreq>
                <priority>0.8</priority>
            </url>
            <?php
            $count++;
        }        
    }
    
    if($pages)
    {
        $count = 0;
        while(isset($pages[$count]))
        {
            $page = $pages[$count];
            if($page['url'] !='err404')
            {
            ?>
            <url>
                <loc>http://<?=$_SERVER['SERVER_NAME']?>/<?=$page['url']?>/</loc>
                <lastmod><?=date("Y-m-d")?></lastmod>
                <changefreq>always</changefreq>
                <priority>0.8</priority>
            </url>
            <?php
            }
            $count++;
        }        
    }
    
    if($articles)
    {
        $count = 0;
        while(isset($articles[$count]))
        {
            $article = $articles[$count];
            $category = $this->categories->getCategoryById($article['category_id']);
            ?>
            <url>
                <loc>http://<?=$_SERVER['SERVER_NAME']?>/<?=$category['url']?>/<?=$article['url']?>/</loc>
                <lastmod><?=$article['date']?></lastmod>
                <changefreq>always</changefreq>
                <priority>0.7</priority>
            </url>
            <?php
            $count++;
        }        
    }
    
    if($shop)
    {
        $count = 0;
        while(isset($shop[$count]))
        {
            $article = $shop[$count];
            $category = $this->categories->getCategoryById($article['category_id']);
            ?>
            <url>
                <loc>http://<?=$_SERVER['SERVER_NAME']?>/<?=$category['url']?>/<?=$article['url']?>/</loc>
                <lastmod><?=$article['date']?></lastmod>
                <changefreq>always</changefreq>
                <priority>0.7</priority>
            </url>
            <?php
            $count++;
        }        
    }
    
    if($g_categories)
    {
        $count = 0;
        while(isset($g_categories[$count]))
        {
            $category = $g_categories[$count];
            ?>
            <url>
                <loc>http://<?=$_SERVER['SERVER_NAME']?>/gallery/<?=$category['url']?>/</loc>
                <lastmod><?=$category['date']?></lastmod>
                <changefreq>always</changefreq>
                <priority>0.7</priority>
            </url>
            <?php
            $count++;
        }        
    }
    
    ?>
</urlset>