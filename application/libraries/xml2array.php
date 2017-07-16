<?php
/*

http://webi.ru/webi_files/xmlwebi.html

 простой php класс для разбора xml на многомерный массив
 подходит для разбора не очень больших xml, так как весь разобранный xml помещается в массив, который будет находится в памяти


 пример использования
 //////////
$xml='<?xml version="1.0"?>
<recipe name="хлеб" preptime="5" cooktime="180">
  <title>Простой хлеб</title>
  <ingredient amount="3" unit="стакан">Мука</ingredient>
  <ingredient amount="0.25" unit="грамм">Дрожжи</ingredient>
  <ingredient amount="1.5" unit="стакан">Тёплая вода</ingredient>
  <ingredient amount="1" unit="чайная ложка">Соль</ingredient>
  <instructions>
   <step>Смешать все ингредиенты и тщательно замесить.</step>
   <step>Закрыть тканью и оставить на один час в тёплом помещении.</step>
   <step>Замесить ещё раз, положить на противень и поставить в духовку.</step>
   <step>Посетить сайт webi.ru</step>
  </instructions>
</recipe>';

$xmlwebi = new XML();
$arr = $xmlwebi->xmlwebi($xml);
print_r($arr);
////////////

разобранный массив будет выглядеть так

Array
(
    [recipe] => Array
        (
            [0] => Array
                (
                    [@] => Array
                        (
                            [name] => хлеб
                            [preptime] => 5
                            [cooktime] => 180
                        )

                    [#] => Array
                        (
                            [title] => Array
                                (
                                    [0] => Array
                                        (
                                            [#] => Простой хлеб
                                        )

                                )

                            [ingredient] => Array
                                (
                                    [0] => Array
                                        (
                                            [#] => Мука
                                            [@] => Array
                                                (
                                                    [amount] => 3
                                                    [unit] => стакан
                                                )

                                        )

                                    [1] => Array
                                        (
                                            [#] => Дрожжи
                                            [@] => Array
                                                (
                                                    [amount] => 0.25
                                                    [unit] => грамм
                                                )

                                        )

                                    [2] => Array
                                        (
                                            [#] => Тёплая вода
                                            [@] => Array
                                                (
                                                    [amount] => 1.5
                                                    [unit] => стакан
                                                )

                                        )

                                    [3] => Array
                                        (
                                            [#] => Соль
                                            [@] => Array
                                                (
                                                    [amount] => 1
                                                    [unit] => чайная ложка
                                                )

                                        )

                                )

                            [instructions] => Array
                                (
                                    [0] => Array
                                        (
                                            [#] => Array
                                                (
                                                    [step] => Array
                                                        (
                                                            [0] => Array
                                                                (
                                                                    [#] => Смешать все ингредиенты и тщательно замесить.
                                                                )

                                                            [1] => Array
                                                                (
                                                                    [#] => Закрыть тканью и оставить на один час в тёплом помещении.
                                                                )

                                                            [2] => Array
                                                                (
                                                                    [#] => Замесить ещё раз, положить на противень и поставить в духовку.
                                                                )

                                                            [3] => Array
                                                                (
                                                                    [#] => Посетить сайт webi.ru
                                                                )

                                                        )

                                                )

                                        )

                                )

                        )

                )

        )

)
*/


class xml2array {


    function xml2array() {
        $this->valid = FALSE;
    }


    function xmlwebi($data, $WHITE=1, $encoding='UTF-8') {
        $data = preg_replace ("'<\?xml.*\?>'si", "", $data); // сначала удалим начальный тег < xml > если он есть
        $data = "<webi_xml>".$data."</webi_xml>";   // окружение xml специфическим тегом, чтобы получилась обработка некоторых невалидных xml
        $data = trim($data);
        $vals = $index = $array = array();
        $parser = xml_parser_create($encoding);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, $WHITE);
        print $this->valid = xml_parse_into_struct($parser, $data, $vals, $index);
        xml_parser_free($parser);

        $i = 0;

        $tagname = $vals[$i]['tag'];
        if(isset($vals[$i]['attributes'])) {
            $array[$tagname]['@'] = $vals[$i]['attributes'];
        }
        else {
            $array[$tagname]['@'] = array();
        }
        $array[$tagname]['#'] = $this->xml_depth($vals, $i);

        return $array['webi_xml']['#']; // результат отдается без специфического тега, вставленного вначале
    }

    function xml_depth($vals,&$i) {
        $children = array();

        if (isset($vals[$i]['value'])) {
            array_push($children, $vals[$i]['value']);
        }

        while (++$i < count($vals)) {
            switch ($vals[$i]['type']) {
                case 'open':
                    if (isset($vals[$i]['tag'])) {
                        $tagname = $vals[$i]['tag'];
                    }
                    else {
                        $tagname = '';
                    }

                    if (isset($children[$tagname])) {
                        $size = sizeof($children[$tagname]);
                    }
                    else {
                        $size = 0;
                    }

                    if ( isset ( $vals[$i]['attributes'] ) ) {
                        $children[$tagname][$size]['@'] = $vals[$i]["attributes"];
                    }

                    $children[$tagname][$size]['#'] = $this->xml_depth($vals, $i);
                    break;

                case 'cdata':
                    array_push($children, $vals[$i]['value']);
                    break;

                case 'complete':
                    $tagname = $vals[$i]['tag'];
                    if(isset($children[$tagname])) {
                        $size = sizeof($children[$tagname]);
                    }
                    else {
                        $size = 0;
                    }

                    if(isset($vals[$i]['value'])) {
                        $children[$tagname][$size]['#'] = $vals[$i]['value'];
                    }
                    else {
                        $children[$tagname][$size]['#'] = '';
                    }

                    if(isset($vals[$i]['attributes'])) {
                        $children[$tagname][$size]['@'] = $vals[$i]['attributes'];
                    }
                    break;

                case 'close':
                    return $children;
                    break;
            }
        }
        return $children;
    }

}

?>