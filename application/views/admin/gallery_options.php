<?php
include("header.php");
?>

<table width="100%" height="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" bgcolor="#DDDDDD" valign="top"><?php include("menu.php"); ?></td>
        <td valign="top">
            <h1><?=$title?></h1>
            <table>
                <tr>
                    <td>title:</td>
                    <td><strong><?=$main['title']?></strong></td>
                </tr>
                <tr>
                    <td>keywords:</td>
                    <td><strong><?=$main['keywords']?></strong></td>
                </tr>
                <tr>
                    <td>description:</td>
                    <td><strong><?=$main['description']?></strong></td>
                </tr>
                <tr>
                    <td>h1:</td>
                    <td><strong><?=$main['h1']?></strong></td>
                </tr>
                <tr>
                    <td>robots:</td>
                    <td><strong><?=$main['robots']?></strong></td>
                </tr>
                <tr>
                    <td>Кол-во ячеек:</td>
                    <td><strong><?=$main['cols']?></strong></td>
                </tr>
                <tr>
                    <td>Кол-во фото на странице:</td>
                    <td><strong><?=$main['pagination']?></strong></td>
                </tr>
                <tr>
                    <td>SEO текст:</td>
                    <td><strong><?=$main['seo']?></strong></td>
                </tr>
            </table>
            <a href="/admin/gallery/options/edit/">Редактировать</a>
        </td>
    </tr>
</table>

<?php
include("footer.php");
?>