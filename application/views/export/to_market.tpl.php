<?php include("application/views/head_new.php"); ?>
<?php include("application/views/header_new.php"); ?>


<section class="container user-date">
    <div class="breadcrumbs">
        <div xmlns:v="https://rdf.data-vocabulary.org/#">
			<span typeof="v:Breadcrumb">
				<a property="v:title" rel="v:url" href="https://<?= $_SERVER['SERVER_NAME'] ?>/">Главная</a>
			</span>&nbsp;-&nbsp;
			<span typeof="v:Breadcrumb">
				<a property="v:title" rel="v:url" href="https://<?= $_SERVER['SERVER_NAME'] ?>/export/">Экспорт</a>
			</span>&nbsp;-&nbsp;
            <?= $h1 ?>
        </div>
    </div>
    <article class="article-content">
        <h1><?= $h1 ?></h1>
        <?php
        //unset_userdata('access_token');
        //    unset_userdata('adding_price_type');
        //    unset_userdata('adding_price');

        //unset_userdata('group_id');
        if (isset($_GET['group_id'])) {
            set_userdata('group_id', $_GET['group_id']);
        }
        if (isset($_GET['album_id'])) {
            set_userdata('vk_album_id', $_GET['album_id']);
        }

        //  vd(userdata('user_id'));

        if (userdata('owner_id') == false && userdata('group_id') !== false) {

            set_userdata('owner_id', userdata('group_id'));
        }


        if (isset($_GET['delall'])) {
            //vd(userdata('owner_id'));
            $all = $this->vkmarket->get_all_products();
            //vd($all);
            //vd($all[1]->id);
            $i = 1;
            while (isset($all[$i]->id)) {
                $this->vkmarket->delete_vk_product($all[$i]->id);
                $i++;
            }
        }

        if (userdata('access_token') == false) {
            $this->vkmarket->authorize();
            echo '<br /><br /><br /><br /><br /><br /><br /><br /><br />';
        } else {
            ?>
            <label for="category_id">Что будем экспортировать?</label><br>
            <select id="category_id" name="category_id">
                <option value="-1">Все товары</option>
                <?php
                $cats = $this->model_categories->getCategories(1, 'shop');
                foreach ($cats as $cat) {
                    //if ($cat['name'] != 'Выбор редакции' && $cat['name'] != 'Хит продаж' && $cat['name'] != 'Вся коллекция' && $cat['name'] != 'Новинки') {
                    echo '<option value="' . $cat['id'] . '">' . $cat['name'] . '</option>';
                    //}
                }
                ?>
            </select>
            <button style="display: none" id="button_create_new_album" category_id="-1">Создать подборку</button><BR/>

            <div id="create_new_album_div" style="display: none">
                <input id="create_new_album" name="create_new_album" type="checkbox" checked/> Дополнить в существующую
                подборку (ID<span id="my_album_id"></span>)
                <br/>
            </div>
            <!--        vk_user_id: --><?//=userdata('vk_user_id')?><!--<br>-->
            <?php
            $groups = $this->vkmarket->getGroups();
            ?>
            Выберите, в какую группу:<br/>
            <select id="group_id">
            <option></option>
            <?php
            $groups = $this->vkmarket->getGroups();
            if ($groups) {
                foreach ($groups as $group) {
                    if (isset($group->gid)) {
                        echo '<option value="' . $group->gid . '"';
                        if ($group->gid == userdata('group_id')) echo ' selected';
                        echo '>' . $group->name . '</option>';
                    }
                }
            }
            ?>
            </select><?php
            if ((!$groups) || count($groups) < 1) echo '<div class="error" id="no-groups-error">У Вас нет групп, в которые можно выгружать товары!</div>';
            echo ' <span style="font-size: 11px; display: none" id="group-no-market" class="question">Внимание! В настройках этой группы не включены товары!</span>';
            ?>
            <br/>
            <div class="error" id="group_id_error" style="display: none">Вам необходимо указать ID своей группы!</div>
            <div style="display: none">
                <label for="currency">Валюта:</label><br>
                <select id="currency" name="currency">
                    <option value="uah"<?php if (userdata('currency') == 'uah') echo ' selected'; ?>>Гривна</option>
                    <option value="usd"<?php if (userdata('currency') == 'usd') echo ' selected'; ?>>Долар</option>
                    <option value="rub"<?php if (userdata('currency') == 'rub') echo ' selected'; ?>>Рубль</option>
                </select><BR/>
            </div>
            <label for="adding-price-type">Добавить к цене товара (<span id="cur">в валюте группы</span>): </label><br>
            <input type="text" placeholder="Сумма в валюте группы" id="adding-price-value"
                   value="<?= userdata('adding_price') ?>"/>
            <span style="font-size: 11px; color: #888888" class="question">сумма, которую Вы хотите зарабатывать на каждом товаре</span>
            <br/>
            <label for="sort">Направление экспорта: </label><br>
            <select id="sort">
                <option value="ASC"<?php if (userdata('sort') == 'ASC') echo ' selected'; ?>>От старых к новым</option>
                <option value="DESC"<?php if (userdata('sort') == 'DESC') echo ' selected'; ?>>От новых к старым
                </option>
            </select>

            <br/><br/>

            <button class="export-button" id="export-start">Начать перенос!</button>
            <button style="display: none" class="export-button" id="export-stop">Остановить</button><br/>
            <button id="test">test</button><br>

            <div id="blink" style="display: none; color: red; font-weight: bolder">Внимание!!! Не закрывайте эту страницу до завершения переноса!</div>

            Процесс выполнения:<br/>
            <textarea id="result" style="width: 500px;height: 500px"></textarea>

            <?php
        }
        ?>
    </article>
</section>
<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.formstyler/jquery.formstyler.min.js"></script>
<script>
    (function($) {
        $(function() {

            $('select').styler();

        });
    })(jQuery);
</script>
<script>

    var currencyArr = Array();
    currencyArr['uah'] = 'Гривна';
    currencyArr['usd'] = 'Долар';
    currencyArr['rub'] = 'Рубль';

    var index = 0;
    var allDone = false;
    var currency = 'uah';
    var adding_price = false;
    var lastProductId = 0;
    var group_id = userdata('group_id');
    var currentCategoryId = false;
    var vk_album_id = false;
    var createNewAlbum = true;
    var stop = false;
    var sort = 'ASC';
    var disabled = false;
    var random_id = 0;
    var speed = 500;

    $(document).ready(function () {

        $("#export-stop").click(function () {
            stop = true;
            $("#export-start").show();
            $("#export-stop").hide();
            $("#blink").hide();
            enableForm();
            addLine("Экспорт остановлен");
        });

        $("#export-start").click(function () {

            if (check_inputs()) {
                return;
            }

            create_export();

            disableForm();
            $(".error").hide();
            $("#export-stop").show();
            $("#export-start").hide();
            $("#blink").show();
            // alert(currency);
            group_id = $("#group_id").val();
            //alert(group_id);
            adding_price = $("#adding-price-value").val();
            currentCategoryId = $("#category_id").val();
            //$("#result").val("");
            $("#result").show();
            userdata('set', 'lastProductId', 0);
            userdata('set', 'group_id', group_id);
            userdata('set', 'currentCategoryId', currentCategoryId);
            userdata('set', 'index', index);
            userdata('set', 'adding_price', adding_price);

            var msg = 'lastProductId=' + lastProductId + "\r\n"
                + 'index=' + index + "\r\n"
                + 'currency=' + currency + "\r\n"
                + 'adding_price=' + adding_price + "\r\n"
                + 'group_id=' + group_id + "\r\n"
                + 'currentCategoryId=' + currentCategoryId + "\r\n"
                + 'vk_album_id=' + vk_album_id + "\r\n"
                + 'createNewAlbum=' + createNewAlbum;
            console.log(msg);

            //alert(currentAlbum);
            setTimeout(function () {
                add_product(0);
            }, speed); // время в мс
        });

        // нажатие кнопки создания подборки
        $("#button_create_new_album").click(function () {
            var category_id = $("#button_create_new_album").attr('category_id');
            add_album(category_id);
        });


        // событие: изминение валюты
        $("#currency").change(function () {
            currency = $("#currency").val();
            userdata('set', 'currency', currency);
            if (currency == 'uah')
                $("#cur").html("Гривна");
            else if (currency == 'usd')
                $("#cur").html("Доллары");
            else if (currency == 'rub')
                $("#cur").html("Рубли");
            addLine("Валюта изменена на " + currency);
        });

        // событие: изминение паблика
        $("#group_id").change(function () {
            group_id = $("#group_id").val();
            userdata('set', 'group_id', group_id);
            addLine("ID группы изменён на " + group_id);
            check_category(currentCategoryId);
            setGroupCurrency(group_id);
        });

        // событие: изминение направления
        $("#sort").change(function () {
            sort = $("#sort").val();
            userdata('set', 'sort', sort);
            addLine("Направление экспорта изменено на " + sort);
        });

        // событие: изминение категории
        $("#category_id").change(function () {
            currentCategoryId = $("#category_id").val();
            userdata('set', 'category_id', currentCategoryId);
            addLine("Категория изменена на " + currentCategoryId);
            $("#button_create_new_album").attr('category_id', currentCategoryId);
            console.log('Изменяем категорию на ' + currentCategoryId);
            check_category(currentCategoryId);
        });

        // событие: изминение добавочной стоимости
        $("#adding-price-value").change(function () {
            adding_price = $("#adding-price-value").val();
            userdata('set', 'adding_price', adding_price);
            addLine("Добавочная стоимость изменена на " + adding_price + ' ' + currencyArr[currency]);
        });

    });

    function addLine(newLine) {
        var text = $("#result").val();
        text = text + "\r\n" + newLine;
        $("#result").val(text);

        $("#result").animate({
            scrollTop: $("#result")[0].scrollHeight - $("#result").height()
        }, speed, function () {

        })
    }


    // Создаём альбом
    function add_album(category_id) {
        addLine('Создаём новую подборку...');
        //if(!check_inputs()) return false;
        // return;
        $.ajax({
            url: '/ajax/to_market/add_album/?category_id=' + category_id,
            method: 'post',
            data: {
                "category_id": category_id
            },

        }).done(function (data) {
            var msg = '';
            if (data != 'error') {

                vk_album_id = data;

                msg = "Альбом создан. ID: " + vk_album_id;
                if (data == 'Ошибка загрузки обложки') {
                    msg = msg + ", но обложка не подгрузилась";
                }

                addLine(msg);
                createNewAlbum = false;
                userdata('set', 'vk_album_id', vk_album_id);
                return vk_album_id;
            }
            else
                addLine(data);
        });
    }


    // Отправляем товар
    function add_product(index) {
        if (stop == true) {
            stop = false;
            return;
        }
        if (index == 0)
            addLine("Приступаем к экспорту...");
        //if(!check_inputs()) return false;

        if (createNewAlbum == true) {
            console.log('Создание подборки...');
            var category_id = $("#category_id").val();

            if (category_id > 0) {
                console.log("category_id>0. Создаём подборку...");
                setTimeout(function () {
                    add_album(category_id);
                }, speed); // время в мс
            }
            createNewAlbum = false;
        }
        $.ajax({
            url: '/ajax/to_market/add/?lastproductid=' + lastProductId + '&index=' + index + '&category_id=' + currentCategoryId + "&vk_album_id=" + vk_album_id,
            method: 'post',
            data: {
                "lastProductId": lastProductId,
                "index": index,
                "category_id": currentCategoryId,
                "vk_album_id": vk_album_id,
                "sort": sort
            },

        }).done(function (data) {
            index++;
            //addLine(data);
            if (data == 'error') {
                addLine(index + "Ошибка добавления фото!");
            } else {
                if (data == 'finish') {
                    enableForm();
                    addLine("Экспорт успешно завершён!");
                    $("#export-start").show();
                    $("#export-stop").hide();
                } else if (data.indexOf('обновлён') != false) {
                    addLine(index + ": " + data);


                    setTimeout(function () {
                        add_product(index);
                    }, speed); // время в мс
                } else {
                    //alert(lastProductId);
                    //addLine("Товар ID:" + data + " успешно добавлен!");
                    addLine(index + ": " + data);
                    setTimeout(function () {
                        add_product(index);
                    }, speed); // время в мс
                }
            }
        });
    }

    // проверяем, импортировали ли этот альбом ранее...
    function check_category(category_id) {
        //return false;
        $.ajax({
            url: '/ajax/to_market/check_category/?category_id=' + category_id + '&group_id=' + group_id,
            method: 'post',
            data: {
                "category_id": category_id,
                "group_id": group_id
            },

        }).done(function (data) {
            if (data == 'no_category') {
                $("#create_new_album_div").hide();
                $("#create_new_album").attr("checked", false);
                createNewAlbum = true;
                userdata('set', 'create_new_album', true);
                console.log('create_new_album = true');
            } else {
                $("#create_new_album_div").show();
                $("#create_new_album").prop("checked", true);
                $("#my_album_id").html(data);
                userdata('set', 'create_new_album', false);
                vk_album_id = data;
                createNewAlbum = false;
                console.log('create_new_album = false');
            }
        });
    }

    // получение данных о валюте группы
    function setGroupCurrency(group_id) {
        group_id = $("#group_id").val();
        $.ajax({
            url: '/ajax/vk/get_group/?group_id=' + group_id,
            method: 'post',
            data: {
                "group_id": group_id
            },

        }).done(function (data) {
            if (data == 'no_market') { // не включены товары
                $("#group-no-market").show();
                disableForm("group_id");
            } else {
                $("#group-no-market").hide();
                enableForm();
                addLine("В выбранной группе установлена валюта: " + currencyArr[data]);
                currency = data;
                $("#currency option[value=" + data + "]").attr('selected', 'true').text(currencyArr[data]);
                //$("#currency").prop('disabled', true);
                $("#cur").html(currencyArr[data]);
            }
        });
    }

    // проверка введённых данных
    function check_inputs() {
        var err = false;
        if (!group_id) {
            group_id = $("#group_id").val();
            if (!group_id) {
                err = true;
                addLine("Ошибка! Вы не указали идентификатор группы!");
                $("#group_id_error").show();
            }
        }
        return err;
    }

    // редактируем кэш
    function userdata(action, name, value) {
        $.ajax({
            url: '/ajax/userdata/' + action + '/?name=' + name + '&value=' + value,
            method: 'post',
            data: {
                "name": name,
                "value": value
            },

        }).done(function (data) {

        });
    }

    // создаём новый сеанс экспорта
    function create_export() {
        group_id = $("#group_id").val();
        category_id = $("#category_id").val();
        $.ajax({
            url: '/ajax/vk/create_export/',
            method: 'post',
            data: {
                "type": "vkmarket",
                "vk_group_id": group_id,
                "category_id": category_id
            },

        }).done(function (data) {
            random_id = data;
        });
    }

    // Делаем все поля disabled
    function disableForm(exceptions = "") {
        if (exceptions.indexOf('category_id') == -1)
            $("#category_id").prop({disabled: true});
        if (exceptions.indexOf('currency') == -1)
            $("#currency").prop({disabled: true});
        if (exceptions.indexOf('adding-price-value') == -1)
            $("#adding-price-value").prop({disabled: true});
        if (exceptions.indexOf('sort') == -1)
            $("#sort").prop({disabled: true});
        if (exceptions.indexOf('export-start') == -1)
            $("#export-start").prop({disabled: true});
        if (exceptions.indexOf('group_id') == -1)
            $("#group_id").prop({disabled: true});

        disabled = true;
    }

    // Делаем все поля enabled
    function enableForm() {
        $("#category_id").prop({disabled: false});
        $("#currency").prop({disabled: false});
        $("#adding-price-value").prop({disabled: false});
        $("#sort").prop({disabled: false});
        $("#export-start").prop({disabled: false});
        $("#group_id").prop({disabled: false});

        disabled = false;
    }

    $("#test").click(function () {
        if (!disabled) disableForm();
        else enableForm();
    });

    $("#test").hide();


</script>

<?php include("application/views/footer_new.php"); ?>
