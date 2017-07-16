<?php
include("header.php");
?>
    <table width="100%" height="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="200px" valign="top"><?php include("menu.php"); ?></td>
            <td width="20px" align="right" valign="top"><div class="border"> </div></td>
            <td valign="top">
                <div class="title_border">
                    <div class="content_title"><h1><?=$title?></h1></div>
                    <div class="back_and_exit">		    русский <a href="/en<?=$_SERVER['REQUEST_URI']?>">english</a>

                        <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться на сайт ></a></span>
                        <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                    </div>
                </div>

                <div class="content">
                    <div class="top_menu">
                        <div class="top_menu_link"><a href="/admin/main/edit/">Редактировать SEO</a></div>
                        <div class="top_menu_link"><a href="/admin/tkdz/">URLs TKDZ</a></div>
                    </div>


                    <form method="post" action="/admin/tkdz/">
                        Поиск по URL'у: <input type="text" name="url" placeholder="http://rostov.peony-dress.ru/pants/" value="<?php if(isset($_POST['url'])) echo $_POST['url']; ?>" />
                        <input type="submit" value="Искать" />
                    </form>

                    <?php
                    if(isset($tkdz['url']))
                    {
                        ?>
                        <table width="100%">
                            <tr>
                                <th>
                                    URL:
                                </th>
                                <td><a target="_blank" href="<?=$tkdz['url']?>"><?=$tkdz['url']?></a></td>
                                <td><a href="/admin/tkdz/?del=<?=$tkdz['id']?>">удалить данные урла</a></td>
                            </tr>
                            <tr>
                                <th>
                                    Title:
                                </th>
                                <td><?=$tkdz['title']?></td>
                            </tr>
                            <tr>
                                <th>
                                    Description:
                                </th>
                                <td><?=$tkdz['description']?></td>
                            </tr>
                            <tr>
                                <th>
                                    Keywords:
                                </th>
                                <td><?=$tkdz['keywords']?></td>
                            </tr>
                            <tr>
                                <th>
                                    H1:
                                </th>
                                <td><?=$tkdz['h1']?></td>
                            </tr>
                            <tr>
                                <th>
                                    robots:
                                </th>
                                <td><?=$tkdz['robots']?></td>
                            </tr>
                            <tr>
                                <th>
                                    canonical:
                                </th>
                                <td><?=$tkdz['canonical']?></td>
                            </tr>
                            <tr>
                                <th valign="top">
                                    SEO Text:
                                </th>
                                <td><?=$tkdz['seo']?></td>
                            </tr>
                        </table>
                        <?php
                    }
                    elseif(isset($_POST['url'])) echo 'Для этого урла нет записей!<br />Добавьте их ниже.';
                    ?>
                    <?php
                    if(isset($_GET['del']))
                    {
                        echo 'Данные были успешно удалены!';
                    }
                    ?>
                    <h2>Добавить или заменить</h2>
                    <form method="post" action="/admin/tkdz/">
                        <table>
                            <tr>
                                <th>
                                    URL:
                                </th>
                                <td><input size="100" type="text" name="url" required placeholder="http://rostov.peony-dress.ru/pants/" value="<?php if(isset($_POST['url']) && !isset($tkdz['url'])) echo $_POST['url']; ?>" /> </td>

                            </tr>
                            <tr>
                                <th>
                                    Title:
                                </th>
                                <td><input size="100" type="text" name="title" /></td>
                            </tr>
                            <tr>
                                <th>
                                    Description:
                                </th>
                                <td><input size="100" type="text" name="description" /></td>
                            </tr>
                            <tr>
                                <th>
                                    Keywords:
                                </th>
                                <td><input size="100" type="text" name="keywords" /></td>
                            </tr>
                            <tr>
                                <th>
                                    H1:
                                </th>
                                <td><input size="100" type="text" name="h1" /></td>
                            </tr>
                            <tr>
                                <th>
                                    robots:
                                </th>
                                <td><input size="30" type="text" name="robots" value="index, follow" /></td>
                            </tr>
                            <tr>
                                <th>
                                    canonical:
                                </th>
                                <td><input size="100" type="text" name="canonical" value="" /></td>
                            </tr>
                            <tr>
                                <th valign="top">
                                    SEO Text:
                                </th>
                                <td>
                                    <textarea name="seo" class="ckeditor"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><input type="submit" name="add" value="Добавить" /></td>
                            </tr>
                        </table>
                    </form>
                </div>


            </td>
        </tr>
    </table>

<?php
include("footer.php");
?>