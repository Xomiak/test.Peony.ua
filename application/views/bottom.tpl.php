<div id="extras">
  <div id="recommended">
    <h2 class="heading">Рекомендуем!</h2>
    <?php
    $bottom1 = $this->model_articles->getBottom(1, 5);
    if($bottom1)
    {
      ?>
      <ul>
	<?php
	$count = count($bottom1);
	for($i = 0; $i < $count; $i++)
	{
	  $b = $bottom1[$i];
	  $category = $this->model_categories->getCategoryById($b['category_id']);
	  ?>
	  <li<?php if(($i + 1) == $count) echo ' class="last"'; ?>>
	    <a href="http://<?=$_SERVER['SERVER_NAME']?>/<?=$category['url']?>/<?=$b['url']?>/"><?=$b['name']?>  &raquo;</a>
	  </li>
	  <?php
	}
	?>
      </ul>
      <?php
    }
    ?>
    
      <!--li><a href="/est-problema/zachem-generalam-chujaya-zemlya/">Зачем генералам чужая земля? &raquo;</a></li>
      <li><a href="/na-dosuge/odesskii-bukovel-chut-ne-stal-lohotronom/">Одесский «Буковель» чуть не стал лохотроном &raquo;</a></li>
      <li><a href="/est-problema/u-amku-dlinnie-ruki/">У АМКУ – длинные руки &raquo;</a></li>
      <li><a href="/ustami-mladenca/deti-pravda/">Что такое правда? &raquo;</a></li>
      <li class="last"><a href="/debati/fashizm--/">Фашизм &raquo;</a></li-->
    
  </div>
  <div id="programs">
    <?php
    $bottom2 = $this->model_articles->getBottom(2, 1);
    
    if($bottom2)
    {
      $b = $bottom2[0];
      
      $category = $this->model_categories->getCategoryById($b['category_id']);
      ?>
      <a href="http://<?=$_SERVER['SERVER_NAME']?>/<?=$category['url']?>/<?=$b['url']?>/"><h2 class="heading"><?=$b['name']?></h2></a>
	
	<?php
	if($b['image'] != '')
	{
	  ?>
	  <a href="http://<?=$_SERVER['SERVER_NAME']?>/<?=$category['url']?>/<?=$b['url']?>/">
	    <img src="<?=CreateThumb(310, 195, $b['image'], 'bottom')?>" width="310px" alt="<?=$b['name']?>" title="<?=$b['name']?>"  />
	  </a>
	  <?php
	}

    }
    ?>
    </div>
  
  <div id="cartoon">
    <?php
    $bottom3 = $this->model_articles->getBottom(3, 1);
    
    if($bottom3)
    {
      $b = $bottom3[0];
      
      $category = $this->model_categories->getCategoryById($b['category_id']);
      ?>
      <a href="http://<?=$_SERVER['SERVER_NAME']?>/<?=$category['url']?>/<?=$b['url']?>/"><h2 class="heading"><?=$b['name']?></h2></a>
	
	<?php
	if($b['image'] != '')
	{
	  ?>
	  <a href="http://<?=$_SERVER['SERVER_NAME']?>/<?=$category['url']?>/<?=$b['url']?>/">
	    <img src="<?=CreateThumb(310, 195, $b['image'], 'bottom')?>" width="310px" alt="<?=$b['name']?>" title="<?=$b['name']?>"  />
	  </a>
	  <?php
	}

    }
    ?>
</div>