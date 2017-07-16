<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getReview($r, $incjquery = false)
{
    $CI = &get_instance();
    $CI->load->library('partialcache');
    $data = '';

    if (($data = $CI->partialcache->get('review_' . $r['id'], 600)) === false) {
        ob_start();

        $shop = $CI->shop->getArticleById($r['shop_id']);
        $cat = $CI->model_categories->getCategoryById($shop['category_id']);
        $user = $CI->users->getUserByLogin($r['login']);
        ?>
        <li class="itm-rev">
            <div>
                <a href="/<?= $cat['url'] ?>/<?= $shop['url'] ?>/">
                    <h2 style="color: black"><?= $shop['name'] ?> (<?= $shop['color'] ?>)</h2>
                </a>
            </div>
            <div style="float: left; width: 100px">
                <a href="/<?= $cat['url'] ?>/<?= $shop['url'] ?>/">
                    <img src="<?= $shop['image'] ?>" width="75px"/>
                </a>
            </div>

            <style>
                .rating_date {
                    color: #888;
                    font-size: 10px;
                    margin-bottom: -15px;
                    padding-left: 75px;
                }

                .review {
                    margin-top: 0px;
                }
            </style>

            <img src="<?= $user['photo'] ?>" alt="<?= $user['name'] ?>" width="52px"/>

            <div class="review">
                <h5><?= $user['name'] ?></h5><span>пишет</span>
                <div class="rating_mini">
                    <input type="hidden" class="val" value="<?= $r['rate'] ?>"/>
                </div>
                <div class="rating_date"><?= $r['date'] ?></div>
                <p><?= $r['comment'] ?></p>
            </div>
            <div style="clear: both"></div>
        </li>
        <?php
        $data = ob_get_clean();
        $CI->partialcache->save('review_' . $r['id'], $data);
    }

    if (isClientAdmin()) $data .= '<a href="/admin/comments/del/' . $r['id'] . '/" style="color: Black">Удалить</a>';

    if($incjquery) {
        ob_start();
        ?>
        <!-- РЕЙТИНГ ЗВЁЗДЫ -->
        <link href="/css/jquery.rating.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="/js/jquery.rating.js"></script>
        <script type="text/javascript">
            j(document).ready(function () {
                // настройки звёзд в карточке товара
                j('div.rating').rating({
                    readOnly: true,
                    image: '/images/stars.png',
                    loader: '/images/ajax-loader.gif',
                    width: 32
                });

                j('div.rating_mini').rating({
                    readOnly: true,
                    image: '/images/stars16.png',
                    loader: '/images/ajax-loader.gif',
                    width: 16
                });

                // вывод звёзд в попапе "Оставить отзыв"
                j(function () {

                    j('#rating_shop').rating({
                        fx: 'half',
                        image: '/images/stars.png',
                        loader: '/images/ajax-loader.gif',
                        width: 32,
                        url: '/ajax/rate/'
                    });
                })
            });

        </script>
        <?php
        $data .= ob_get_clean();
    }

    return $data;
}

function getRatingHtnl($art, $ratingClass = "")
{
    $voitings_text = "отзывов";
    if ($art['voitings'] == 1) $voitings_text = 'отзыв';
    elseif ($art['voitings'] < 5) $voitings_text = "отзыва";

    $url = getFullUrl($art);
    $rating = round(getRating($art), 2);
    if($rating == 0) $rating = 5;
    if($art['voitings'] == 0) $art['voitings'] = 1;

    $ret = '
    <link href="/css/jquery.rating.css" rel="stylesheet" type="text/css" />
    <div class="' . $ratingClass . '">
            <div class="rating">
                <input type="hidden" class="val" value="' . getRating($art) . '"/>
                <input type="hidden" class="votes" value="' . $art['voitings'] . '"/>
            </div>
            <div class="rating-words" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                Оценка: <strong itemprop="ratingValue">' . $rating . '</strong> / <span itemprop="bestRating">5</span> (<a href="' . $url . '#reviews"><span itemprop="reviewCount">' . $art['voitings'] . '</span> ' . $voitings_text . '</a>)
                <meta itemprop="itemReviewed" content = "' . $art['name'] . ' (' . $art['color'] . ')">
            </div>
    </div>
        <!-- РЕЙТИНГ ЗВЁЗДЫ -->

<script type="text/javascript" src="/js/jquery.rating.js"></script>
<script type="text/javascript">
	j(document).ready(function(){
		// настройки звёзд в карточке товара
		j(\'div.rating\').rating({
			readOnly: true,
			image: \'/images/stars.png\',
			loader: \'/images/ajax-loader.gif\',
			width: 32
		});

		j(\'div.rating_mini\').rating({
			readOnly: true,
			image: \'/images/stars16.png\',
			loader: \'/images/ajax-loader.gif\',
			width: 16
		});

		// вывод звёзд в попапе "Оставить отзыв"
		j(function(){

			j(\'#rating_shop\').rating({
				fx: \'half\',
				image: \'/images/stars.png\',
				loader: \'/images/ajax-loader.gif\',
				width: 32,
				url: \'/ajax/rate/\'
			});
		})
	});

</script>

<!-- /РЕЙТИНГ ЗВЁЗДЫ -->
        ';
    return $ret;
}

function addReviews($shop)
{
    if(isset($_FILES['userfile1'])){
        //vd("YES");
    }
    if(isDebug()){
        //vd($_POST);
    }
    ?>
    <h3 class="related-products">Оставить отзыв:</h3>
    <?php
    $CI = &get_instance();
    if (userdata('login') != false) {

        ?>
        <!-- РЕЙТИНГ -->
        <link href="/css/jquery.rating.css" rel="stylesheet" type="text/css"/>


        <!-- /РЕЙТИНГ -->
        <div id="add-review">
            <form method="post" action="/comments/add/" enctype="multipart/form-data">
                <input type="hidden" name="add_review" value="true" />
                <input type="hidden" name="shop_id" value="<?= $shop['id'] ?>"/>

                <input type="hidden" name="back" value="<?= $_SERVER['REQUEST_URI'] ?>"/>
                <input required id="rating" type="hidden" name="rating" value="-1"/>

                <textarea id="review-message" required class="review-ta" placeholder="Ваш отзыв..."
                          name="comment"></textarea><br/>
                <div class="error" id="review-message-error"></div>
                <h3 class="related-products">Прикрепить фото:</h3>
                <div id="adding_fotos">
                    <div class="review-add-files"><input onchange="addNewFileInput()" id="userfile1" class="userfiles" type="file" name="userfile1" /></div>
                </div>
                <h3 class="related-products">Оценить:</h3>
                <div id="rating_shop">
                    <input type="hidden" class="val" required value=""/>
                    <input type="hidden" id="review_rate" class="votes" value="0"/>

                </div>
                <div class="error" id="review-rate-error"></div>
                <input class="add-review" id="review-send" type="submit" name="add_review" value="Отправить"/>
            </form>
        </div>
        <script>
            var fotosCount = 1;
            var fotos = [];
            function addNewFileInput() {
                fotosCount++;
                $("#adding_fotos").append('<div class="review-add-files"><input onchange="addNewFileInput()" id="userfile'+fotosCount+'" class="userfiles" type="file" name="userfile'+fotosCount+'" /></div>');
            }
            // Документ готов

            $(document).ready(function () {
                // Назначение события

                $("#review-send").click(function () {

                    var message = $("#review-message").val();
                    var rating = $("#rating").val();

                    if (message.length < 10) {
                        $("#review-message-error").html("Хотелось бы, чтобы Ваш отзыв был чуточку больше!");
                        return false;
                    } else {
                        $("#review-message-error").html("");
                        hideBackdrops();
                    }
                    if (rating == -1) {
                        $("#review-rate-error").html("Поставьте, пожалуйста, оценку данному товару!");
                        return false;
                    } else {
                        $("#review-rate-error").html("");
                        hideBackdrops();
                    }

                });
            });
        </script>
        <hr/>
        <?php
    } else {
        echo "Чтобы оставить отзыв, необходимо авторизироваться: ";
        ?>
        <div id="uLogine89cfaf5"
             data-ulogin="display=panel;fields=first_name,last_name,country,email,city,bdate,photo;optional=phone,photo_big,sex,nickname;verify=1;providers=vkontakte,odnoklassniki,mailru,google,facebook;hidden=yandex,twitter,livejournal,openid,lastfm,linkedin,liveid,soundcloud,steam,flickr,uid,youtube,webmoney,foursquare,tumblr,googleplus,dudu,vimeo,instagram,wargaming;redirect_uri=//<?= $_SERVER['SERVER_NAME'] ?>/login/soc/"></div>

        <?php
    }
}

function showReviews($shop)
{
    $CI = &get_instance();
    $CI->load->model('Model_comments', 'comments');
    $CI->load->model('Model_users', 'users');

    $reviews = $CI->comments->getCommentsToShop($shop['id'], 1);
    if ($reviews) {

        $count = count($reviews);
        echo "<ul>";
        for ($i = 0; $i < $count; $i++) {
            $r = $reviews[$i];
            $user = $CI->users->getUserByLogin($r['login']);
            ?>
            <li class="itm-rev" itemprop="review" itemscope itemtype="http://schema.org/Review">
                <meta itemprop="itemReviewed" content="<?= $shop['name'] ?> (<?= $shop['color'] ?>)">
                <style>
                    .rating_date {
                        color: #888;
                        font-size: 10px;
                        margin-bottom: -15px;
                        padding-left: 75px;
                    }

                    .review {
                        margin-top: 0px;
                    }
                </style>

                <img src="<?= $user['photo'] ?>" alt="<?= $user['name'] ?>" width="52px"/>

                <div class="review-block">
                    <h5 itemprop="author"><?= $user['name'] ?></h5><span>пишет</span>
                    <div class="rating_mini">
                        <input type="hidden" class="val" value="<?= $r['rate'] ?>"/>
                    </div>
                    <div itemprop="datePublished" class="rating_date"><?= $r['date'] ?></div>
                    <p itemprop="description"><?= $r['comment'] ?></p>
                    <div class="review_fotos" style="padding-left: 100px">
                    <?php
                    if($r['images'] != '' && $r['images'] != NULL){
                        $images = json_decode($r['images']);
                        $c = 1;
                        foreach ($images as $image){
                            if($image){
                                echo '<a target="_blank" href="'.$image.'"><img src="'.CreateThumb2(100,100,$image,'reviews').'" alt="'.$user['name'].' (#'.$c.')" /></a>';
                            }
                            $c++;
                        }
                    }
                    ?>

                    </div>
                    <div style="clear: both"></div>
                    <?php
                    if (isClientAdmin()) echo '<a href="/admin/comments/del/' . $r['id'] . '/" style="color: Black">Удалить</a>';
                    ?>
                    <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
                        <meta itemprop="worstRating" content="1">
                        <meta itemprop="bestRating" content="5">
                        <meta itemprop="ratingValue" content="<?= $r['rate'] ?>">

                    </div>
                </div>

            </li>
            <?php
        }
        echo "</ul>";
    } else {
        echo "<div>Пока отзывов нет...</div>";
    }

}

function relatedProducts($category, $newicle = false)
{
    $CI = &get_instance();

//vd($category);
    //$CI->db->where('category_id', $category['id']);
    // if($newicle)
    //    $CI->db->where('id <>', $newicle['id']);
    //$CI->db->where('active', 1);
    //$CI->db->order_by('old_price', 'DESC');
    //$CI->db->limit(100);
    //$related = $CI->db->get('shop')->result_array();
    $related = $CI->model_shop->getArticlesByCategory($category['id'], -1, -1, 1);
    //vd($related);
    if ($related) {
        shuffle($related);
        $count = count($related);
        if ($count > 5) $count = 5;

        for ($i = 0; $i < $count; $i++) {
            $new = $related[$i];

            echo getProductHtml($new, $category, true);
        }
    }
}


function showLast5()
{

}

function galleryShowTop5()
{

}

function showTop5()
{

}

function getMonthNameByNo($no)
{
    $month = '';
    if ($no == '01') $month = 'января';
    elseif ($no == '02') $month = 'февраля';
    elseif ($no == '03') $month = 'марта';
    elseif ($no == '04') $month = 'апреля';
    elseif ($no == '05') $month = 'мая';
    elseif ($no == '06') $month = 'июня';
    elseif ($no == '07') $month = 'июля';
    elseif ($no == '08') $month = 'августа';
    elseif ($no == '09') $month = 'сентября';
    elseif ($no == '10') $month = 'октября';
    elseif ($no == '11') $month = 'ноября';
    elseif ($no == '12') $month = 'декабря';
    return $month;
}

function showStandartModule($category_name)
{

}

function showDateTimeModule($category_name)
{

}

function getArticleHomeCategory($category_id)
{
    $CI = &get_instance();

    $CI->db->where('id', $category_id);
    $cat = $CI->db->get('categories')->result_array();
    if ($cat) {
        $cat = $cat[0];
        if ($cat['parent'] != 0) {
            $CI->db->where('id', $cat['parent']);
            $cat = $CI->db->get('categories')->result_array();
            if ($cat) {
                $cat = $cat[0];
            }
        }
        return $cat;
    } else return false;
}

function articleCommentsCount($newicle_id)
{
    $CI = &get_instance();

    $CI->db->where('article_id', $newicle_id);
    $comments = $CI->db->get('comments')->result_array();
    if (!$comments) return 0;
    else return count($comments);
}


function showDelivery($country_id)
{

    $CI = &get_instance();
    $CI->load->model('Model_shop', 'shop');
    ?>
    <div id="test"></div>
    <h2>Способ доставки</h2>
    <!--script src="/js/jquery.min.js"></script-->
    <ul>
        <?php
        $no_deliveries = true;
        if ($country_id != 'other') {
            $deliveries = $CI->shop->getDeliveriesByCountryId($country_id);
            if ($deliveries) {
                $no_deliveries = false;
                $count = count($deliveries);
                for ($i = 0; $i < $count; $i++) {
                    $delivery = $deliveries[$i];
                    ?>
                    <li>
                        <input required class="delivery" delivery-fields="<?= $delivery['fields'] ?>"
                               id="delivery_<?= $delivery['id'] ?>" type="radio" value="<?= $delivery['name'] ?>"
                               name="delivery" placeholder="">
                        <label for="delivery_<?= $delivery['id'] ?>"><?= $delivery['name'] ?></label>
                    </li>
                    <?php
                }
            } else $no_deliveries = true;
        } else $no_deliveries = true;


        if ($no_deliveries) {
            ?>
            <li>
                <input class="delivery" type="radio" value="Почтой" delivery-fields="[adress][zip]" name="delivery"
                       required/>
                <label>Почтой</label>
            </li>
            <?php
        }

        ?>
    </ul>
    <div class="form-error" id="form_delivery_err" style="display: none">Вы не выбрали способ доставки!</div>
    <div class="delivery-addings">
        <script>

            var j = jQuery.noConflict();

            function hideAll() {
                j(".inw").hide();
                j(".inw input").removeAttr('required');


            }

            j(document).ready(function () {
                j(".delivery").change(function () {
                    j(".inw").hide();
                });

                j(".delivery").change(function () {

                    var fields = j('input[name="delivery"]:checked').attr("delivery-fields");
                    var sel = j('input[name="delivery"]:checked').val();

                    //j(".inw").hide();
                    hideAll();

                    if (fields.indexOf("np") != -1) {
                        j("#np").show();
                        j("#input_np").focus();
                        j("#input_np").attr('required', 'required');
                    }
                    if (fields.indexOf("[adress]") != -1) {
                        j("#adress").show();
                        j("#adress input").attr('required', 'required');
                    }
                    if (fields.indexOf("[zip]") != -1) {
                        j("#zip").show();
                        j("#zip input").attr('required', 'required');
                    }
                    if (fields.indexOf("[passport]") != -1) {
                        j("#passport").show();
                        j("#passport input").attr('required', 'required');

                        //j("#passport input").attr('passport_required', 'required');
                    }

                    j("#delivery-adding").show();
                });
            });

        </script>
    </div>
    <?php

    //return $html;
}

function showAdress($show, $user)
{
    ?>
    <div class="form-group">
        <label>Город:</label>
        <input type="text" name="city" required value="<?php if ($user)
            echo $user['city'] ?>"/>
    </div>


    <?php
    if ($show['passport']) {
        ?>
        <div class="form-group">
            <label>Паспорт:</label>
            <input type="text" name="passport" required value="<?php if ($user)
                echo $user['passport'] ?>"/>
        </div>
        <?php
    }
    ?>

    <?php
    if ($show['adress']) {
        ?>
        <div class="form-group">
            <label>Адрес:</label>
            <input type="text" name="adress" required value="<?php if ($user)
                echo $user['adress'] ?>"/>
        </div>
        <?php
    }
    ?>

    <?php
    if ($show['zip']) {
        ?>
        <div class="form-group">
            <label>Почтовый индекс:</label>
            <input type="text" name="zip" required value="<?php if ($user)
                echo $user['zip'] ?>"/>
        </div>
        <?php
    }
    ?>


    <?php
    if ($show['np']) {
        ?>
        <script>
            $("#inp_novaposhta").focus();
        </script>
        <div class="form-group" style="text-align: right">
            <label>Отделение №:</label>
            <input id="inp_novaposhta" type="text" name="np" required value="<?php if ($user)
                echo $user['np'] ?>"/>
        </div>
        <?php
    }
    ?>


    <div class="form-group">
        <label>Ваше сообщение</label>
        <textarea name="message" placeholder=""></textarea>
    </div>
    <?php
}

function getUser()
{
    $CI = &get_instance();
    $login = $CI->session->userdata('login');
    if ($login) {
        $CI->db->where('login', $login);
        $CI->db->limit(1);
        $user = $CI->db->get('users')->result_array();
        if (isset($user[0])) return $user[0];
    }
    return false;
}

