<?php
include("header.php");
?>
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="200px" valign="top"><?php include("menu.php"); ?></td>
        <td width="20px"></td>
        <td valign="top">
            <div class="title_border">
                <div class="content_title"><h1><?=$title?></h1></div>
                <div class="back_and_exit">
		    		    русский <a href="/en<?=$_SERVER['REQUEST_URI']?>">english</a>

                    <span class="back_to_site"><a href="/" target="_blank" title="Открыть сайт в новом окне">Вернуться на сайт ></a></span>
                    <span class="exit"><a href="/admin/login/logoff/">Выйти</a></span>
                </div>
            </div>
            
            <div class="content">
                <div class="top_menu">
                    <div class="top_menu_link"><a href="/admin/mailer/">Рассылка</a></div>
                    <div class="top_menu_link"><a href="/admin/mailer/add/">Создание рассылки</a></div>
                </div>
                
            <form enctype="multipart/form-data" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
                <table>
                    <tr>
                        <td valign="top">Шапка:</td>
                        <td>                            
                            <?php
                            if($header != '')
                            {
                                echo '<img style="max-width: 1000px" src="'.$header.'" /><input type="hidden" name="header" value="'.$header.'"/>';
                            }
                            ?>
                            <div>Выбрать другую шапку:</div>
                            <input type="file" name="header" />
                        </td>
                    </tr>
                    <tr>
                        <td>Заголовок:</td>
                        <td>
                            <input type="text" name="name" size="50" value="<?=$name?>" />
                        </td>
                    </tr>
                   
                    
                    <tr>
                        <td>Основной текст:</td>
                        <td>
                            <textarea class="ckeditor" name="content" cols="50" rows="5"><?=$content?></textarea>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>Примечание:</td>
                        <td>
                            <textarea class="ckeditor" name="adding" cols="50" rows="5"><?=$adding?></textarea>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>Подвал:</td>
                        <td>
                            <textarea class="ckeditor" name="footer" cols="50" rows="5"><?=$footer?></textarea>
                        </td>
                    </tr>
                    <tr>
                            <td colspan="2"><input type="checkbox" name="no_price" /> Без цены</td>
                    </tr> 
					<tr>
						<td></td>
							<td><input type="submit" value="Сохранить" /></td>
						</tr> 
       
                </table>
				
				<h3>Редактирование описаний товаров:</h3>
				<table>
                <?php
                if($articles)
                {
                    
                    $count = count($articles);
                    for($i = 0; $i < $count; $i++)
                    {
                        $article = $articles[$i];
						?>
						<tr>
							<td>
								<h2><?=$article['name']?></h2>
								<img width="267px" src="<?=$article['image']?>" />
							</td>
							<td>
								<textarea class="ckeditor" name="content_<?=$article['id']?>" cols="50" rows="5"><?=$article['content']?></textarea>
							</td>
						</tr>
						<?php                        
                    }
                }
                ?>
				<tr>
					<td></td>
							<td></td>
						</tr> 
				</table>
<a name="emails_list"></a>
                <h3>Частичная рассылка:</h3>
                <table>
                    <tr>
                        <td colspan="2">
                            <input id="emails_list_on" type="checkbox" name="emails_list_on"<?php if($emails_list != NULL && is_array($emails_list)) echo ' checked' ?> /> Использовать частичную рассылку
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="cbname3[]" value="14" id="example_maincb"<?php if(!$emails_list) echo " disabled"; ?> /> Все подписанные<br />
                            <?php
                            if($userTypes)
                            {
                                $count = count($userTypes);
                                for($i = 0; $i < $count; $i++)
                                {
                                    $ut = $userTypes[$i];
                                    $tr = translitRuToEn($ut['name']);
                                    ?>
                                    <input class="cb-user-types" type="checkbox" name="type_<?=$tr?>" value="14" id="type_<?=$tr?>"<?php if(!$emails_list) echo " disabled"; ?> /> <?=$ut['name']?><br />
                                    <?php
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="emails_list">
                                <h4>Отказавшиеся от рассылки:</h4>
                                <ul>
                                    <?php
                                    foreach ($noMailerUsers as $key => $user) {
                                        $email = $user['email'];
                                        if(valid_email($email))
                                        {
                                            echo '<li><input class="mailer_user no_mailer type_'.translitRuToEn($user['user_type']).'" type="checkbox" name="emails_list[]" value="'.$email.'"';
                                            if((is_array($emails_list)) && in_array($email, $emails_list)) echo ' checked';
                                            if(!$emails_list) echo " disabled";
                                            echo '> '.$email.' (<a href="/admin/users/edit/'.$user['id'].'/" title="Просмотреть анкету клиента" target="_blank">'.$user['name'].' '.$user['lastname'].'</a>)</li>';
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr /></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="emails_list">
                                <h4>Подписанные:</h4>
                                <ul>
                                    <?php
                                    foreach ($users as $key => $user) {
                                        $email = $user['email'];
                                        if(valid_email($email))
                                        {
                                            echo '<li><input class="mailer_user type_'.translitRuToEn($user['user_type']).'" type="checkbox" name="emails_list[]" value="'.$email.'"';
                                            if((is_array($emails_list)) && in_array($email, $emails_list)) echo ' checked';
                                            if(!$emails_list) echo " disabled";
                                            echo '> '.$email.' (<a href="/admin/users/edit/'.$user['id'].'/" title="Просмотреть анкету клиента" target="_blank">'.$user['name'].' '.$user['lastname'].'</a>)</li>';
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="flying-buttons">
                    <div class="title_border flying-buttons-paddings">
                        <input type="submit" value="Сохранить" name="save" style="width: 100%">
                        <br />
                        <a href="/admin/mailer/" style="text-decoration:none;" onclick="return confirm('Вы точно не хотите сохранить изминения?')"><input type="button" value="      Назад      "></a>
                    </div>
                </div>
            </form>
            <script type="text/javascript">
                $(document).ready( function() {

                    <?php
                    if($userTypes)
                    {
                        $count = count($userTypes);
                        for($i = 0; $i < $count; $i++)
                        {
                            $ut = $userTypes[$i];
                            $tr = translitRuToEn($ut['name']);
                            ?>
                            $("#type_<?=$tr?>").click( function() { // при клике по главному чекбоксу
                                if($("#type_<?=$tr?>").attr('checked')){ // проверяем его значение
                                    $('.type_<?=$tr?>:enabled').attr('checked', true); // если чекбокс отмечен, отмечаем все чекбоксы
                                    $('.no_mailer').attr('checked', false);
                                } else {
                                    $('.type_<?=$tr?>:enabled').attr('checked', false); // если чекбокс не отмечен, снимаем отметку со всех чекбоксов
                                }
                            });
                            <?php
                        }
                    }
                    ?>

                    // if($('#emails_list_on').attr('checked') == false){
                    //     $('.mailer_user').attr('disabled', true);
                    // }else $('.mailer_user').attr('disabled', false);

                    $("#emails_list_on").click( function() { // при клике по главному чекбоксу
                        if($('#emails_list_on').attr('checked')){ // проверяем его значение
                            $('.mailer_user').attr('disabled', false); // если чекбокс отмечен, отмечаем все чекбоксы
                            $('#example_maincb').attr('disabled', false);
                            $('.cb-user-types').attr('disabled', false);
                            
                        } else {
                            $('.mailer_user').attr('disabled', true); // если чекбокс не отмечен, снимаем отметку со всех чекбоксов
                            $('#example_maincb').attr('disabled', true);
                            $('.cb-user-types').attr('disabled', true);
                        }
                   });

                    $(".mailer_user").click( function() { // при клике по главному чекбоксу         
                    //alert("asd");               
                            $('.emails_list_on').attr('checked', true); // если чекбокс отмечен, отмечаем все чекбоксы                        
                   });

                   $("#example_maincb").click( function() { // при клике по главному чекбоксу
                        if($('#example_maincb').attr('checked')){ // проверяем его значение
                            $('.mailer_user:enabled').attr('checked', true); // если чекбокс отмечен, отмечаем все чекбоксы
                            $('.no_mailer').attr('checked', false);
                        } else {
                            $('.mailer_user:enabled').attr('checked', false); // если чекбокс не отмечен, снимаем отметку со всех чекбоксов
                            $('.cb-user-types').attr('checked', false);
                        }
                   });

                });
                 
            </script>
               </div>
        </td>
    </tr>
</table>


<?php
include("footer.php");
?>