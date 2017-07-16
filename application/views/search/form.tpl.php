<?php
$search_google = $this->model_options->getOption('search_google');
if($search_google === false) $search_google = 0;

if($search_google == 0)
{
    ?>
    <form action="http://odessit.in.ua/search/" id="cse-search-box">
        <div>
          <input type="hidden" name="cx" value="partner-pub-4634339770824757:6476019032" />
          <input type="hidden" name="cof" value="FORID:10" />
          <input type="hidden" name="ie" value="UTF-8" />
          <input type="text" name="q" size="25" style="background: #fdf2e3;" />
          <input type="submit" name="sa" value="" style="background: url(/img/search.png); width: 28px; height: 25px; border: 0; padding-top: 5px;" />
        </div>
    </form>
    <?php
}
else
{
    ?>
    <form action="http://odessit.in.ua/search/" id="cse-search-box">
        <div>
          <input type="hidden" name="cx" value="partner-pub-4634339770824757:6476019032" />
          <input type="hidden" name="cof" value="FORID:10" />
          <input type="hidden" name="ie" value="UTF-8" />
          <input type="text" name="q" size="25" style="background: #fdf2e3;" />
          <input type="submit" name="sa" value="" style="background: url(/img/search.png); width: 28px; height: 25px; border: 0; padding-top: 5px;" />
        </div>
      </form>
      
      <script type="text/javascript" src="http://www.google.com.ua/coop/cse/brand?form=cse-search-box&amp;lang=ru"></script>

    <?php
}
?>
