<?php
$per_page = userdata('per_page');
if(!$per_page)
{
    $per_page = 9;
}
?>
<form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
Выводить по <select name="per_page" onchange="submit()">
    <option value="9"<?php if($per_page == 9) echo ' selected'; ?>>9</option>
    <option value="18"<?php if($per_page == 18) echo ' selected'; ?>>18</option>
    <option value="36"<?php if($per_page == 36) echo ' selected'; ?>>36</option>
    <option value="1000"<?php if($per_page == 1000) echo ' selected'; ?>>ВСЕ</option>
</select> <span style="padding-left: 60px;">брендов</span>
<input type="hidden" name="back" value="<?=$_SERVER['REQUEST_URI']?>" />
</form>