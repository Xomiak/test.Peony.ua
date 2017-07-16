<?php include('application/views/header.php'); ?>
<?php include('application/views/left.inc.php'); ?>
<?php include('application/views/right.inc.php'); ?>

<div id="content">
                                    <div class="kroshki">
                                        <div xmlns:v="http://rdf.data-vocabulary.org/#">
                                            <span typeof="v:Breadcrumb">
                                                <a property="v:title" rel="v:url" href="http://<?=$_SERVER['SERVER_NAME']?>/">Главная</a>
                                            </span>
                                            &nbsp;»&nbsp;
                                            Галерея
                                        </div>
                                    </div>
                                    <h1 class="long"><?=$h1?></h1>                                    
                                    <p style="font-size: 15px">Если Вы хотите загрузить ещё дополнительные фотографии, то загружайте их в альбом "<strong>Фото участников</strong>"</p>
                                    <form enctype="multipart/form-data" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                                    <table>
                                        <tr>
                                            <td>Название *:</td>
                                            <td>
                                                <input type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" />
                                                <?php
                                                if(isset($err['name']) && $err['name'] != '')
                                                    echo '<div class="error">'.$err['name'].'</div>';
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Раздел *:</td>
                                            <td>
                                                <SELECT name="category_id">
                                                    <option value=""></option>
                                                    <?php
                                                    if($categories)
                                                    {
                                                        $count = count($categories);
                                                        for($i = 0; $i < $count; $i++)
                                                        {
                                                            $c = $categories[$i];
                                                            echo '<option value="'.$c['id'].'">'.$c['name'].'</option>';
                                                            $subs = $this->gallery->getSubCategories($c['id']);
                                                            if($subs)
                                                            {
                                                                $scount = count($subs);
                                                                for($j = 0; $j < $scount; $j++)
                                                                {
                                                                    $s = $subs[$j];
                                                                    echo '<option value="'.$s['id'].'">&nbsp;└&nbsp;'.$s['name'].'</option>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </SELECT>
                                                <?php
                                                if(isset($err['category_id']) && $err['category_id'] != '')
                                                    echo '<div class="error">'.$err['category_id'].'</div>';
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Фото *:</td>
                                            <td><input type="file" name="userfile" /></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><input type="submit" value="Добавить" /></td>
                                        </tr>
                                    </table>
                                    </form>
                                </div>

<?php include('application/views/footer.php'); ?>