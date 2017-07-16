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
					<div class="top_menu_link"><a href="/admin/articles/">Статьи</a></div>
					<div class="top_menu_link"><a href="/admin/articles/add/">Добавить статью</a></div>
				</div>

				<strong><font color="Red"><?=$err?></font></strong>            
				<form enctype="multipart/form-data" action="/admin/articles/add/" method="post">
					<input type="hidden" name="num" value="<?=$num?>" />
					<table>
						<tr>
							<td>Название *:</td>
							<td><input required type="text" name="name" size="50" value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" /></td>
							<td>«»</td>
						</tr>                        
						<tr>
							<td>Раздел *:</td>
							<td>
								<SELECT required name="category_id[]"<?php if($article_in_many_categories != '0') echo ' multiple=""'; ?>
									<option></option>
									<?php
									$count = count($categories);
									for($i = 0; $i < $count; $i++)
									{
										$cat = $categories[$i];
										if($cat['type'] == 'articles')
										{
											echo '<option value="'.$cat['id'].'"';
											echo '>'.$cat['name'].'</option>';
											$subs = $this->mcats->getSubCategories($cat['id']);
											if($subs)
											{
												$subcount = count($subs);
												for($j = 0; $j < $subcount; $j++)
												{
													$sub = $subs[$j];
													echo '<option value="'.$sub['id'].'"';
													echo '>&nbsp;└&nbsp;'.$sub['name'].'</option>';
												}
											}
										}
									}
									?>
								</SELECT>
								<!-- <input type="hidden" name="category_id[]" value="10" /> -->

							</td>
						</tr>

						<tr>
							<td>Фото:</td>
							<td><input type="file" name="userfile" /><br /><a target="_blank" href="/admin/images/">Загрузить доп. фотографии</a></td>
						</tr>
						<tr>
							<td>Youtube:</td>
							<td><input type="text" name="youtube" size="50" value="<?php if(isset($_POST['youtube'])) echo $_POST['youtube'];?>" /></td>
						</tr>
						<tr>
							<td>Краткое описание:</td>
							<td><textarea name="short_content"  class="ckeditor"><?php if(isset($_POST['short_content'])) echo $_POST['short_content'];?></textarea></td>
						</tr>
						<tr>
							<td>Контент:</td>
							<td><textarea name="content"  class="ckeditor" rows="30"><?php if(isset($_POST['content'])) echo $_POST['content'];?></textarea></td>
						</tr>


						<tr>
							<td>На главной:</td>
							<td><input type="checkbox" name="glavnoe" checked="checked" /></td>
						</tr>

						<tr>
							<td>Кнопки соц. сетей:</td>
							<td><input type="checkbox" name="social_buttons"  /></td>
						</tr>


						<tr>
							<td colspan="2"><input type="checkbox" name="active" checked /> Активный</td>
						</tr>
						<tr>
							<td colspan="2"><input type="submit" value="Добавить" /></td>
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