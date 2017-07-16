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

    var currentAlbum = false;

    var index = 0;
    var albumOrGroup = userdata('albumOrGroup');
    var allDone = false;
    var currency = 'uah';
    var adding_price = false;
    var lastProductId = 0;
    var group_id = false;
    var currentCategoryId = false;
    var vk_album_id = false;
    var createNewAlbum = true;
    var stop = false;
    var sort = 'ASC';
    var disabled = false;
    var random_id = 0;
    var speed = 500;

    var all = [
        <?php
        $i = 0;
        $count = count($cats);
        for($i=0; $i < $count; $i++){
            $cat = $cats[$i];
            echo  $cat['id'] ;
            if(($i+1) < $count) echo ',';
        }
        ?>];
    var albumsCount = <?=count($cats)?>;



    $(document).ready(function () {

        $("#export-stop").click(function () {
            stop = true;
            $("#export-start").show();
            $("#export-stop").hide();
            enableForm();
            addLine("Экспорт остановлен");
        });

        $("#export-start").click(function () {
            create_export();

            var newWin = window.open("about:blank", "Начнём экспорт...", "width=200,height=200");

            disableForm();
            $("#export-stop").show();
            $("#export-start").hide();

            // $("#result").val("");
            $("#result").show();
            lastProductId = 0;
            userdata('set','lastProductId',0);
            currentCategoryId = $("#category_id").val();
//            if (currentCategoryId == -1) {
//                addLine("Приступаем к экспорту всех разделов и товаров...");
//                for (var i = 0; i < albumsCount; i++) {
//                    setTimeout(function () {
//
//                        currentCategoryId = all[i];
//                        alert(currentCategoryId);
//                        addLine("Выбираем следующий раздел для экспорта: " + currentCategoryId);
//                        // Проверяем, надо ли создавать новый альбом
//                        if(createNewAlbum == true) {
//                            // Создаём альбом
//                            console.log("Создаём новый альбом");
//                            create_album(currentCategoryId);
//                        } else{
//                            currentAlbum = $("#my_album_id").html();
//                            console.log("Получаем существующий альбом ID:"+currentAlbum);
//                        }
//                        //alert(currentAlbum);
//                        setTimeout(function () {
//                            add_foto(0, currentAlbum, currentCategoryId);
//                        }, speed); // время в мс
//
//                    }, speed); // время в мс
//                }
//            } else {
            // Проверяем, надо ли создавать новый альбом
            if(createNewAlbum == true) {
                // Создаём альбом
                console.log("Создаём новый альбом");
                create_album(currentCategoryId);
            } else{
                currentAlbum = $("#my_album_id").html();
                console.log("Получаем существующий альбом ID:"+currentAlbum);
            }
            //alert(currentAlbum);
            setTimeout(function () {
                add_foto(0);
            }, speed); // время в мс

            // Грузим фотки
            //   }
        });

        $("#category_id").change(function () {
            currentCategoryId = $("#category_id").val();
            check_category(currentCategoryId);
        });

        $("#currency").change(function () {
            currency = $("#currency").val();
            userdata('set', 'currency', currency);
            if(currency == 'uah')
                $("#cur").html("Гривна");
            else if(currency == 'usd')
                $("#cur").html("Доллары");
            else if(currency == 'rub')
                $("#cur").html("Рубли");
        });
        $("#adding-price-value").change(function () {
            adding_price = $("#adding-price-value").val();
            userdata('set', 'adding_price', adding_price);
        });

        // событие: изминение паблика
        $("#group_id").change(function () {
            group_id = $("#group_id").val();
            userdata('set', 'group_id', group_id);
            addLine("ID группы изменён на " + group_id);
        });

        // событие: изминение направления
        $("#sort").change(function () {
            sort = $("#sort").val();
            userdata('set', 'sort', sort);
            addLine("Направление экспорта изменено на " + sort);
        });

        // событие: изминение в альбом или группу
        $("#albumOrGroup").change(function () {
            albumOrGroup = $("#albumOrGroup").val();
            userdata('set', 'albumOrGroup', albumOrGroup);
            if(albumOrGroup == 'group') {
                addLine("Экспортировать будем в фотоальбомы группы...");
                $("#group_div").show();
            } else{
                addLine("Экспортировать будем в мои фотоальбомы");
                $("#group_div").hide();
                group_id = false;
                userdata('set','group_id',group_id);
            }
        });


        $("#create_new_album").change(function () {
            if($("#create_new_album").is(':checked')){
                createNewAlbum = false;
                console.log('create_new_album = false');
            }else{
                createNewAlbum = true;
                console.log('create_new_album = true');
            }
            userdata('set','create_new_album',createNewAlbum);
        });
//        $("#adding-price-type").change(function () {
//            if($("#adding-price-type").is(':checked')){
//                adding_price_type = "%";
//            } else{
//                adding_price_type = false;
//            }
//            userdata('set', 'adding_price_type', adding_price_type);
//        });
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

    // создаём новый сеанс экспорта
    function create_export() {
        group_id = $("#group_id").val();
        category_id = $("#category_id").val();
        $.ajax({
            url: '/ajax/vk/create_export/',
            method: 'post',
            data: {
                "type": "vkalbums",
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

    // проверяем, импортировали ли этот альбом ранее...
    function check_category(category_id) {
        $.ajax({
            url: '/ajax/export/check_category/',
            method: 'post',
            data: {
                "category_id": category_id,
                "group_id": group_id
            },

        }).done(function (data) {
            if(data == 'no_category'){
                $("#create_new_album_div").hide();
                $("#create_new_album").attr("checked", false);
                createNewAlbum = true;
                userdata('set','create_new_album',true);
                console.log('create_new_album = true');
            }else{
                $("#create_new_album_div").show();
                $("#create_new_album").prop("checked",true);
                $("#my_album_id").html(data);
                userdata('set','create_new_album',false);
                createNewAlbum = false;
                console.log('create_new_album = false');
            }
        });
    }

    // создаём новый альбом
    function create_album(category_id) {
        return;
        $.ajax({
            url: '/ajax/export/create_album/?category_id=' + category_id,
            method: 'post',
            data: {
                "category_id": category_id
            },

        }).done(function (data) {
            if (data != 'error') {
                addLine(data);
                //currentAlbum = data;
                //alert(currentAlbum);
                //return currentAlbum;
            } else {
                addLine("Ошибка создания альбома!");
                return false;
            }
        });
    }

    // Отправляем фото
    function add_foto(index) {
        if (stop == true) {
            stop = false;
            return;
        }
        if (index == 0)
            addLine("Приступаем к экспорту...");

        sort = $("#sort").val();
        currentCategoryId = $("#category_id").val();
        albumOrGroup = $("#albumOrGroup").val();
        $.ajax({
            url: '/ajax/export/add_foto/',
            method: 'post',
            data: {
                "category_id": currentCategoryId,
                "vk_album_id": vk_album_id,
                "sort": sort,
                "index": index,
                "albumOrGroup": albumOrGroup,
                "group_id": group_id
            },

        }).done(function (data) {
            index++;
            if (data == 'error') {
                addLine("Ошибка добавления фото!");
            } else if (data == 'finish') {
                addLine("Все товары успешно перенесены!");
                enableForm();
                $("#export-start").show();
                $("#export-stop").hide();
                return;
            } else {
                addLine(data);
                index++;
                setTimeout(function () {
                    add_foto(index);
                }, speed); // время в мс
            }
        });
    }

    // редактируем кэш
    function userdata(action, name, value) {
        $.ajax({
            url: '/ajax/userdata/' + action + '/',
            method: 'post',
            data: {
                "name": name,
                "value": value
            },

        }).done(function (data) {

        });
    }
</script>