<section class = "container reviews">

    <?php
    $this->load->helper('modules_helper');
    echo getRatingHtnl($article,'shop-rating');
    ?>
    <span class="reviews-title">Отзывы</span>
    <?php
    showReviews($article);
    ?>

    <!--		<div id="block_rating" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">-->
    <!--			<meta itemprop="bestRating" content="10">-->
    <!--			<meta itemprop="ratingValue" content="8.8">-->
    <!--			<a href="/film/447301/votes/" class="continue rating_link rating_ball_green">-->
    <!--				<span class="rating_ball">8.789</span>-->
    <!--				<span class="ratingCount" itemprop="ratingCount">285 091</span>-->
    <!--		</div>-->

    <button data-toggle = "modal" data-target = ".review">оставить отзыв</button>

    <!-- Форма для нового отзыва -->
    <div style = "display: none" class = "add-review">

    </div>
</section>


<div class = "modal fade review bs-example-modal-md" tabindex = "-1" role = "dialog" aria-labelledby = "mySmalModalLabel" aria-hidden = "true">
    <div class = "modal-dialog modal-md">
        <div class = "modal-content revievs">
            <button class="close" type="button" data-dismiss="modal">&times;</button>

            <?php
            addReviews($article); // функция находится в helpers/modules_helper.php
            ?>
        </div>
    </div>
</div>