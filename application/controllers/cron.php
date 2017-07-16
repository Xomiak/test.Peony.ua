<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Cron extends CI_Controller
{
    var $noIncCatIds = array(12, 39, 35, 20, 19, 17);

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Model_shop', 'shop');
        $this->load->model('Model_users', 'users');
        $this->load->model('Model_images', 'images');
        $this->load->model('Model_main', 'main');
        $this->load->helper('file');
        $this->load->helper('translit_helper');
        $this->load->library('zip');
    }

    public function test()
    {
        if (!$this->input->is_cli_request()) {
            echo "This script can only be accessed via the command line" . PHP_EOL;
            return;
        }
        $this->load->helper('mail_helper');
        mail_send('xomiak@rap.org.ua', 'Cron test', 'Wery nice!!');
    }

    public function prom_orders_import()
    {
        $this->load->helper('prom_helper');
        prom_getOrders();
    }


    // Заливаем фотки во временную папку
    private function create_images_folder($path = "./upload/temp/zip_price/", $folder_name = "catalog")
    {
        $folder_name = iconv('utf-8', 'cp1251', $folder_name);

        // Очищаем временную папку
        delete_files($path, true);

        $path_img = $path . $folder_name . '/';
        if (!is_dir($path_img)) mkdir($path_img, 0777);


        $articles = $this->shop->getArticles(-1, -1, "ASC", 1);
        if ($articles) {
            $count = count($articles);
            for ($i = 0; $i < $count; $i++) {
                $a = $articles[$i];
                $image = '.' . $a['image'];
                if (file_exists($image)) {

                    $pos = strrpos($image, '.');
                    $extension = substr($image, $pos);

                    $file_name = "";
                    if (($i + 1) < 10) $file_name .= "0";
                    if (($i + 1) < 100) $file_name .= "0";
                    $file_name .= ($i + 1) . "-";
                    $file_name .= $a['articul'];
                    $file_name .= '-' . $a['name'];
                    $file_name .= '-' . $a['color'];


                    $file_name = translitRuToEn($file_name);
                    copy($image, $path_img . $file_name . $extension);

                }
                $images = $this->images->getByShopId($a['id'], 1, 1);
                if ($images) {
                    $icount = count($images);
                    for ($i2 = 0; $i2 < $icount; $i2++) {
                        $image = '.' . $images[$i2]['image'];

                        $pos = strrpos($image, '.');
                        $extension = substr($image, $pos);

                        $file_name = "";
                        if (($i + 1) < 10) $file_name .= "0";
                        if (($i + 1) < 100) $file_name .= "0";
                        $file_name .= ($i + 1) . "-";
                        $file_name .= $a['articul'];
                        $file_name .= '-' . $a['name'];
                        $file_name .= '-' . $a['color'];
                        $file_name .= '-' . ($i2 + 2);


                        $file_name = translitRuToEn($file_name);
                        copy($image, $path_img . $file_name . $extension);
                    }
                }
                //die();
            }
        }

        return $path;
    }

    private function create_price_xls($path = "./upload/temp/zip_price/", $filename = "price.xls", $save_to_file = true, $date_in_name = false)
    {
        $this->load->library('excel');
        //activate worksheet number 1
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Price');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', '№');
        $this->excel->getActiveSheet()->setCellValue('B1', 'Артикул');
        $this->excel->getActiveSheet()->setCellValue('C1', 'Название');
        $this->excel->getActiveSheet()->setCellValue('D1', 'Цвет');
        $this->excel->getActiveSheet()->setCellValue('E1', 'Размеры');
        $this->excel->getActiveSheet()->setCellValue('F1', 'Цена ($)');
        $this->excel->getActiveSheet()->setCellValue('G1', 'Цена (грн)');
        $this->excel->getActiveSheet()->setCellValue('H1', 'Цена (руб)');
        $this->excel->getActiveSheet()->setCellValue('I1', 'Ссылка на товар');
        $this->excel->getActiveSheet()->setCellValue('J1', 'Ссылка на фото');
        $this->excel->getActiveSheet()->setCellValue('K1', 'Раздел');
        $this->excel->getActiveSheet()->setCellValue('L1', 'Описание');

        //change the font size
        //$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A1:Z1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);

        //merge cell A1 until D1
        //$this->excel->getActiveSheet()->mergeCells('A1:D1');
        //set aligment to center for that merged cell (A1 to D1)
        $this->excel->getActiveSheet()->getStyle('A1:Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->excel->getActiveSheet()->getStyle('F:H')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        // ЗАПОЛНЯЕМ ЗНАЧЕНИЯМИ

        $articles = $this->shop->getArticles(-1, -1, $order_by = "DESC", 1);
        if ($articles) {
            $currensy_grn = getCurrencyValue('UAH');
            $currensy_rub = getCurrencyValue('RUB');

            $count = count($articles);
            for ($i = 0; $i < $count; $i++) {
                $a = $articles[$i];
                $cat = $this->model_categories->getCategoryById($a['category_id']);
                $url = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $cat['url'] . '/' . $a['url'] . '/';

                $a['razmer'] = str_replace('*', ', ', $a['razmer']);
                $n = $i + 2;
                $no = "";
                if (($i + 1) < 10) $no .= "0";
                if (($i + 1) < 100) $no .= "0";
                $no .= ($i + 1);
                $this->excel->getActiveSheet()->setCellValueExplicit('A' . $n, $no, PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('B' . $n, $a['articul'], PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('C' . $n, $a['name'] . ' (' . $a['color'] . ')', PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('D' . $n, $a['color'], PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('E' . $n, $a['razmer'], PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('F' . $n, $a['price'] . " $", PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('G' . $n, round(($a['price'] * $currensy_grn), 1) . " грн.", PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('H' . $n, round(($a['price'] * $currensy_rub)) . " р", PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValue('I' . $n, $url);
                $this->excel->getActiveSheet()->getCell('I' . $n)->getHyperlink()->setUrl($url);
                $this->excel->getActiveSheet()->setCellValue('J' . $n, 'http://' . $_SERVER['SERVER_NAME'] . $a['image']);
                $this->excel->getActiveSheet()->getCell('J' . $n)->getHyperlink()->setUrl($url);
                $this->excel->getActiveSheet()->setCellValueExplicit('K' . $n, $cat['name'], PHPExcel_Cell_DataType::TYPE_STRING);
                $content = '';
                $this->excel->getActiveSheet()->setCellValueExplicit('L' . $n, strip_tags(getAnons($a['content'])), PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }


        if ($date_in_name) $filename = date("Y-m-d") . '_' . $filename;


        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');


        if ($save_to_file) {
            $objWriter->save($path . $filename);
        } else {
            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');
        }

        return $filename;
    }


    public function create_zip_file($path = "./upload/temp/zip_price/", $xlsfile = "price.xls", $folder_name = "catalog")
    {
        $zip = new ZipArchive();
        $filename = "./upload/price.zip";
        if (is_file($filename))
            @unlink($filename);

        if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
            exit("Невозможно открыть <$filename>\n");
        }

        $zip->addFile($path . $xlsfile, $xlsfile);

        $files = get_filenames($path . $folder_name);

        $count = count($files);
        for ($i = 0; $i < $count; $i++) {
            $f = $files[$i];
            $file = $path . $folder_name . '/' . $f;
            $zip->addFile($file, $folder_name . '/' . $f);
        }

        echo "Добавлено файлов в архив: " . $zip->numFiles;
        $zip->close();
    }

    public function create_zip_price()
    {
        $path = "./upload/temp/zip_price/";
        $xlsfile = "price.xls";
        $folder_name = "catalog";

        $this->create_images_folder();

        $xlsfile = $this->create_price_xls();

        $this->create_zip_file();


        delete_files($path, true);
    }

    public function create_yml()
    {
        $this->load->helper('sstring_helper');


        $this->create_prom_yml();
        $this->create_yml_rozetka();
        $this->create_yml_sale();
        $this->create_yml_prom_sale();
        $this->create_yml_bigest_price();
        $this->create_dealer_yml();
        $this->create_yandex_market();
        $this->create_yandex_market('USD');
        $this->create_yandex_market_no_sale();


    }

    public function create_yandex_market($currency = 'UAH')
    {
        $currency = strtoupper($currency);
        $template = getOption('export_yml_template');
        $domain = $_SERVER['SERVER_NAME'];
        if ($template) {
            $template = str_replace('[DATE]', date("Y-m-d"), $template);
            $template = str_replace('[TIME]', date("H:i"), $template);
            $template = str_replace('[SERVER_NAME]', $domain, $template);
            $tkdzst = $this->main->getMain();
            $template = str_replace('[COMPANY]', $tkdzst['title'], $template);
            $usd_to_uah = getCurrencyValue('UAH');
            $usd_to_rur = getCurrencyValue('RUB');


            $currencies = '
<currency id="UAH" rate="1"/>
<currency id="USD" rate="' . (1 / $usd_to_uah) . '"/>
<currency id="RUR" rate="' . (1 / $usd_to_rur) . '"/>
';

            if ($currency == 'USD') {
                $currencies = '
<currency id="USD" rate="1"/>
<currency id="UAH" rate="' . $usd_to_uah . '"/>
<currency id="RUR" rate="' . $usd_to_rur . '"/>
';
            }
            $template = str_replace('[CURRENCIES]', $currencies, $template);

            $categories = $this->model_categories->getCategories(1, 'shop');
            $categories_yml = "";
            if ($categories) {
                foreach ($categories as $cat) {
                    if (!in_array($cat['id'], $this->noIncCatIds)) {
                        $categories_yml .= '
<category id="' . $cat['id'] . '">' . $cat['name'] . '</category>';
                    }
                }
            }
            $template = str_replace('[CATEGORIES]', $categories_yml, $template);

            $shop = $this->shop->getArticles(-1, -1, 'DESC', 1);
            $offers_yml = "";
            if ($shop) {
                $export_yml_currency = getOption('export_yml_currency');

                $export_yml_currency_coefficient = getCurrencyValue($currency);
                foreach ($shop as $a) {
                    $sizes = "";
                    $warehouse = json_decode($a['warehouse'], true);
                    $razmeri = explode('*', $a['razmer']);
                    if (is_array($razmeri)) {
                        foreach ($razmeri as $r) {
                            if (isset($warehouse[$r]) && $warehouse[$r] > 0)
                                $sizes .= $r . ';';
                        }
                        $sizes = substr($sizes, 0, -1);
                    } else $sizes = $razmeri;

                    $category = $this->model_categories->getCategoryById($a['category_id']);
                    //if($a['discount'] > 0) $category = $this->model_categories->getCategoryById(19);
                    $offers_yml .= '
<offer available="true" id="' . $a['id'] . '">
	<url>http://' . $domain . getFullUrl($a) . '</url>
	<price>' . ($a['price'] * $export_yml_currency_coefficient) . '</price>';

                    if ($a['discount'] > 0) $offers_yml .= '
    <discount>' . $a['discount'] . '%</discount>
                   ';

                    $offers_yml .= '<currencyId>' . $export_yml_currency . '</currencyId>
	<categoryId>' . $category['id'] . '</categoryId>
	<categoryName>' . $category['name'] . '</categoryName>';

                    if ($a['image_vk'] != '') {
                        $offers_yml .= '
	<picture>http://' . $domain . $a['image_vk'] . '</picture>
	';
                    }

                    if ($a['image'] != '') {
                        $offers_yml .= '
	<picture>http://' . $domain . $a['image'] . '</picture>
	';
                    }
                    // Выгружаем все картинки
                    $images = $this->images->getByShopId($a['id'], 1, 1);
                    if ($images) {
                        foreach ($images as $img) {
                            $offers_yml .= '
	<picture>http://' . $domain . $img['image'] . '</picture>
	';
                        }
                    }


                    $descr = getAnons($a['content'], 150);
                    //$words = string_words_count($descr);

                    $offers_yml .= '
	<delivery>true</delivery>';

                    if ($currency == 'USD') {
                        $offers_yml .= '<name>' . $a['name'] . '</name>';
                        $offers_yml .= '<tkan>' . $a['tkan'] . '</tkan>';
                        $offers_yml .= '<color>' . $a['color'] . '</color>';
                        $offers_yml .= '<season>' . $a['season'] . '</season>';
                        $offers_yml .= '<sostav>' . $a['sostav'] . '</sostav>';
                        $offers_yml .= '<height>' . $a['height'] . '</height>';
                        $offers_yml .= '<hand_height>' . $a['hand_height'] . '</hand_height>';
                        $offers_yml .= '<sizes>' . $sizes . '</sizes>';


                    }

                    $offers_yml .= '<vendor>PEONY</vendor>
	<vendorCode>' . $a['articul'] . '</vendorCode>
	<barcode>' . $a['barcode'] . '</barcode>
	<name>' . $a['name'] . ' (' . $a['color'] . ')' . '</name>
	<country_of_origin>Украина</country_of_origin>
	<description><![CDATA[' . $descr . ']]></description>
	<param name="Цвет">' . $a['color'] . '</param>
	<param name="Ткань">' . $a['tkan'] . '</param>
	';
                    if ($a['season'] != '') {
                        $offers_yml .= '<param name="Cезон">' . $a['season'] . '</param>
	';
                    }
                    if ($a['sostav'] != '') {
                        $offers_yml .= '<param name="Состав">' . $a['sostav'] . '</param>
	';
                    }
                    if ($a['height'] != '') {
                        $offers_yml .= '<param name="Длина">' . $a['height'] . '</param>
	';
                    }
                    if ($a['hand_height'] != '') {
                        $offers_yml .= '<param name="Длина рукава">' . $a['hand_height'] . '</param>
	';
                    }
                    $offers_yml .= '
	<param name="Размеры">' . $sizes . '</param>
</offer>
';
                }

                $template = str_replace('[OFFERS]', $offers_yml, $template);
            }
        }
        if ($currency != 'UAH')
            save_ftp_file('yandex_market_' . strtolower($currency) . '.xml', $template);
        else
            save_ftp_file('yandex_market.xml', $template);

//		header("Content-Type: text/xml");
//		header("Expires: Thu, 19 Feb 1998 13:24:18 GMT");
//		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
//		header("Cache-Control: no-cache, must-revalidate");
//		header("Cache-Control: post-check=0,pre-check=0");
//		header("Cache-Control: max-age=0");
//		header("Pragma: no-cache");
//		echo $template;
    }

    public function create_yandex_market_no_sale()
    {
        $template = getOption('export_yml_template');
        $domain = $_SERVER['SERVER_NAME'];
        if ($template) {
            $template = str_replace('[DATE]', date("Y-m-d"), $template);
            $template = str_replace('[TIME]', date("H:i"), $template);
            $template = str_replace('[SERVER_NAME]', $domain, $template);
            $tkdzst = $this->main->getMain();
            $template = str_replace('[COMPANY]', $tkdzst['title'], $template);
            $usd_to_uah = getCurrencyValue('UAH');
            $usd_to_rur = getCurrencyValue('RUB');

            $currencies = '
<currency id="UAH" rate="1"/>
<currency id="USD" rate="' . (1 / $usd_to_uah) . '"/>
<currency id="RUR" rate="' . (1 / $usd_to_rur) . '"/>
';
            $template = str_replace('[CURRENCIES]', $currencies, $template);

            $categories = $this->model_categories->getCategories(1, 'shop');
            $categories_yml = "";
            if ($categories) {
                foreach ($categories as $cat) {
                    if (!in_array($cat['id'], $this->noIncCatIds)) {
                        $categories_yml .= '
<category id="' . $cat['id'] . '">' . $cat['name'] . '</category>';
                    }
                }
            }
            $template = str_replace('[CATEGORIES]', $categories_yml, $template);

            $shop = $this->shop->getArticles(-1, -1, 'DESC', 1);
            $offers_yml = "";
            if ($shop) {
                $export_yml_currency = getOption('export_yml_currency');

                $export_yml_currency_coefficient = getCurrencyValue('UAH');
                foreach ($shop as $a) {
                    if ($a['discount'] > 0) {
                        $sizes = "";
                        $warehouse = json_decode($a['warehouse'], true);
                        $razmeri = explode('*', $a['razmer']);
                        if (is_array($razmeri)) {
                            foreach ($razmeri as $r) {
                                if (isset($warehouse[$r]) && $warehouse[$r] > 0)
                                    $sizes .= $r . ';';
                            }
                            $sizes = substr($sizes, 0, -1);
                        } else $sizes = $razmeri;

                        $category = $this->model_categories->getCategoryById($a['category_id']);
                        //if($a['discount'] > 0) $category = $this->model_categories->getCategoryById(19);
                        $offers_yml .= '
<offer available="true" id="' . $a['id'] . '">
	<url>http://' . $domain . getFullUrl($a) . '</url>
	<price>' . (getNewPrice($a['price'], $a['discount']) * $export_yml_currency_coefficient) . '</price>
	<currencyId>' . $export_yml_currency . '</currencyId>
	<categoryId>' . $category['id'] . '</categoryId>
	<categoryName>' . $category['name'] . '</categoryName>';

                        if ($a['image_vk'] != '') {
                            $offers_yml .= '
	<picture>http://' . $domain . $a['image_vk'] . '</picture>
	';
                        }

                        if ($a['image'] != '') {
                            $offers_yml .= '
	<picture>http://' . $domain . $a['image'] . '</picture>
	';
                        }
                        // Выгружаем все картинки
                        $images = $this->images->getByShopId($a['id'], 1, 1);
                        if ($images) {
                            foreach ($images as $img) {
                                $offers_yml .= '
	<picture>http://' . $domain . $img['image'] . '</picture>
	';
                            }
                        }


                        $descr = getAnons($a['content'], 120);
                        //$words = string_words_count($descr);

                        $offers_yml .= '
	<delivery>true</delivery>
	<name>' . $a['name'] . ' (' . $a['color'] . ')</name>
	<vendor>PEONY</vendor>
	<vendorCode>' . $a['articul'] . '</vendorCode>
	<country_of_origin>Украина</country_of_origin>
	<description><![CDATA[' . $descr . ']]></description>
	<param name="Цвет">' . $a['color'] . '</param>
	<param name="Ткань">' . $a['tkan'] . '</param>
	';
                        if ($a['season'] != '') {
                            $offers_yml .= '<param name="Cезон">' . $a['season'] . '</param>
	';
                        }
                        if ($a['sostav'] != '') {
                            $offers_yml .= '<param name="Состав">' . $a['sostav'] . '</param>
	';
                        }
                        if ($a['height'] != '') {
                            $offers_yml .= '<param name="Длина">' . $a['height'] . '</param>
	';
                        }
                        if ($a['hand_height'] != '') {
                            $offers_yml .= '<param name="Длина рукава">' . $a['hand_height'] . '</param>
	';
                        }
                        $offers_yml .= '
	<param name="Размеры">' . $sizes . '</param>
</offer>
';
                    }
                }

                $template = str_replace('[OFFERS]', $offers_yml, $template);
            }
        }

        save_ftp_file('yandex_market_no_sale.xml', $template);
//		header("Content-Type: text/xml");
//		header("Expires: Thu, 19 Feb 1998 13:24:18 GMT");
//		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
//		header("Cache-Control: no-cache, must-revalidate");
//		header("Cache-Control: post-check=0,pre-check=0");
//		header("Cache-Control: max-age=0");
//		header("Pragma: no-cache");
//		echo $template;
    }

    public function create_yandex_market_rub()
    {
        $template = getOption('export_yml_template');
        $domain = $_SERVER['SERVER_NAME'];
        if ($template) {
            $template = str_replace('[DATE]', date("Y-m-d"), $template);
            $template = str_replace('[TIME]', date("H:i"), $template);
            $template = str_replace('[SERVER_NAME]', $domain, $template);
            $tkdzst = $this->main->getMain();
            $template = str_replace('[COMPANY]', $tkdzst['title'], $template);
            $usd_to_uah = getOption('usd_to_uah');
            $usd_to_rur = getOption('usd_to_rur');

            $currencies = '
<currency id="USD" rate="1"/>
<currency id="UAH" rate="1"/>
<currency id="RUR" rate="' . (1 / $usd_to_rur) . '"/>
';
            $template = str_replace('[CURRENCIES]', $currencies, $template);

            $categories = $this->model_categories->getCategories(1, 'shop');
            $categories_yml = "";
            if ($categories) {
                foreach ($categories as $cat) {
                    $categories_yml .= '
<category id="' . $cat['id'] . '">' . $cat['name'] . '</category>';
                }
            }
            $template = str_replace('[CATEGORIES]', $categories_yml, $template);

            $shop = $this->shop->getArticles(-1, -1, 'DESC', 1);
            $offers_yml = "";
            if ($shop) {
                $export_yml_currency = getOption('export_yml_currency');

                $export_yml_currency_coefficient = getCurrencyValue('UAH');
                foreach ($shop as $a) {
                    $sizes = "";
                    $warehouse = json_decode($a['warehouse'], true);
                    $razmeri = explode('*', $a['razmer']);
                    if (is_array($razmeri)) {
                        foreach ($razmeri as $r) {
                            if (isset($warehouse[$r]) && $warehouse[$r] > 0)
                                $sizes .= $r . ';';
                        }
                        $sizes = substr($sizes, 0, -1);
                    } else $sizes = $razmeri;

                    $category = $this->model_categories->getCategoryById($a['category_id']);
                    //if($a['discount'] > 0) $category = $this->model_categories->getCategoryById(19);
                    $offers_yml .= '
<offer available="true" id="' . $a['id'] . '">
	<url>http://' . $domain . getFullUrl($a) . '</url>
	<price>' . (getNewPrice($a['price'], $a['discount']) * $export_yml_currency_coefficient) . '</price>
	<currencyId>' . $export_yml_currency . '</currencyId>
	<categoryId>' . $category['id'] . '</categoryId>
	<categoryName>' . $category['name'] . '</categoryName>';
                    if ($a['image'] != '') {
                        $offers_yml .= '
	<picture>http://' . $domain . $a['image'] . '</picture>
	';
                    }
                    // Выгружаем все картинки
                    $images = $this->images->getByShopId($a['id'], 1, 1);
                    if ($images) {
                        foreach ($images as $img) {
                            $offers_yml .= '
	<picture>http://' . $domain . $img['image'] . '</picture>
	';
                        }
                    }


                    $descr = getAnons($a['content'], 120);
                    //$words = string_words_count($descr);

                    $offers_yml .= '
	<delivery>true</delivery>
	<name>' . $a['name'] . ' (' . $a['color'] . ')</name>
	<vendor>PEONY</vendor>
	<vendorCode>' . $a['articul'] . '</vendorCode>
	<country_of_origin>Украина</country_of_origin>
	<description><![CDATA[' . $descr . ']]></description>
	<param name="Цвет">' . $a['color'] . '</param>
	<param name="Ткань">' . $a['tkan'] . '</param>
	';
                    if ($a['season'] != '') {
                        $offers_yml .= '<param name="Cезон">' . $a['season'] . '</param>
	';
                    }
                    if ($a['sostav'] != '') {
                        $offers_yml .= '<param name="Состав">' . $a['sostav'] . '</param>
	';
                    }
                    if ($a['height'] != '') {
                        $offers_yml .= '<param name="Длина">' . $a['height'] . '</param>
	';
                    }
                    if ($a['hand_height'] != '') {
                        $offers_yml .= '<param name="Длина рукава">' . $a['hand_height'] . '</param>
	';
                    }
                    $offers_yml .= '
	<param name="Размеры">' . $sizes . '</param>
</offer>
';
                }

                $template = str_replace('[OFFERS]', $offers_yml, $template);
            }
        }

        save_ftp_file('yandex_market_rub.xml', $template);
//		header("Content-Type: text/xml");
//		header("Expires: Thu, 19 Feb 1998 13:24:18 GMT");
//		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
//		header("Cache-Control: no-cache, must-revalidate");
//		header("Cache-Control: post-check=0,pre-check=0");
//		header("Cache-Control: max-age=0");
//		header("Pragma: no-cache");
//		echo $template;
    }

    public function create_prom_yml()
    {
        $this->load->helper('sstring_helper');
        $template = getOption('export_yml_template');
        $domain = $_SERVER['SERVER_NAME'];
        if ($template) {
            $template = str_replace('[DATE]', date("Y-m-d"), $template);
            $template = str_replace('[TIME]', date("H:i"), $template);
            $template = str_replace('[SERVER_NAME]', $domain, $template);
            $tkdzst = $this->main->getMain();
            $template = str_replace('[COMPANY]', $tkdzst['title'], $template);
            $usd_to_uah = getCurrencyValue("UAH");
            $usd_to_rur = getCurrencyValue("RUB");
            $prom_price_adding = getOption('prom_price_adding');

            $currencies = '
<currency id="UAH" rate="1"/>
<currency id="USD" rate="' . (1 / $usd_to_uah) . '"/>
<currency id="RUR" rate="' . (1 / $usd_to_rur) . '"/>
';
            $template = str_replace('[CURRENCIES]', $currencies, $template);


            if (!$prom_price_adding) $prom_price_adding = 2;
            $categories = $this->model_categories->getCategories(1, 'shop');
            $categories_yml = "";
            if ($categories) {
                foreach ($categories as $cat) {
                    if (!in_array($cat['id'], $this->noIncCatIds)) {
                        $categories_yml .= '
<category id="' . $cat['id'] . '">' . $cat['name'] . '</category>';
                    }
                }
            }
            $template = str_replace('[CATEGORIES]', $categories_yml, $template);

            $shop = $this->shop->getArticles(-1, -1, 'DESC', 1);
            $offers_yml = "";
            if ($shop) {
                $export_yml_currency = getOption('export_yml_currency');
                $export_yml_currency_coefficient = getCurrencyValue($export_yml_currency);

                $groupId = 0;

                foreach ($shop as $a) {
                    $groupId++;
                    $sizes = "";
                    $category = $this->model_categories->getCategoryById($a['category_id']);
                    $warehouse = json_decode($a['warehouse'], true);
                    $razmeri = explode('*', $a['razmer']);
                    if (is_array($razmeri)) {
                        foreach ($razmeri as $r) {
                            if (isset($warehouse[$r]) && $warehouse[$r] > 0) {
                                $sizes .= $r . '|';

                                $available = 'true';
                                if ($a['warehouse_sum'] == 0)
                                    $available = 'false';

                                // АКЦИЯ НА ВСЁ, КРОМЕ SALE
                                //if(isActionTime()) $a['discount'] = 10;

                                $offers_yml .= '
<offer available="' . $available . '" id="' . $a['id'] . '-' . $r . '" selling_type="u" group_id="' . $groupId . '">
	<url>http://' . $domain . getFullUrl($a) . '</url>

	<minimum_order_quantity>1</minimum_order_quantity>
	';


//                    if($a['discount'] > 0)
//                        $offers_yml .= '<oldprice>' . round($price * $export_yml_currency_coefficient, 2) . '</oldprice>';
                                // Оптовые цены:
                                // $price = (getNewPrice($a['price'], $a['discount']) * $export_yml_currency_coefficient);

                                $offers_yml .= '
	<currencyId>' . $export_yml_currency . '</currencyId>
	<categoryId>' . $category['id'] . '</categoryId>
	<categoryName>' . $category['name'] . '</categoryName>';
                                if ($a['image'] != '') {
                                    $offers_yml .= '
	<picture>http://' . $domain . $a['image'] . '</picture>
	';
                                }
                                // Выгружаем все картинки
                                $images = $this->images->getByShopId($a['id'], 1, 1);
                                if ($images) {
                                    foreach ($images as $img) {
                                        $offers_yml .= '
	<picture>http://' . $domain . $img['image'] . '</picture>
	';
                                    }
                                }

                                $descr = $category['prom_content'];
                                if($descr == '')
                                    $descr = getAnons($a['content'], 200);
                                //$words = string_words_count($descr);

                                $name = $a['name'] . ' (' . $r . ' размер, ' . $a['color'] . ')';
                                if (strpos($name, $category['name']) === false)
                                    $name = $category['name_one'] . ' ' . $name;
                                $name .= ' ТМ «PEONY»';

                                // TAGS
                                $tags = getTags($a, $category);

                                $price = getNewPrice($a['price'], $a['discount']) * $export_yml_currency_coefficient;

                                $oldPrice = false;
                                if ($a['discount'] > 0) $oldPrice = $a['price'] * $export_yml_currency_coefficient + ($prom_price_adding * $export_yml_currency_coefficient);

                                if ($prom_price_adding)                                                          // Надбавка для Прома
                                    $onePrice = ($price) + ($prom_price_adding * $export_yml_currency_coefficient);

                                $priceNow =
                                $offers_yml .= '
<price>' . ($price + ($prom_price_adding * $export_yml_currency_coefficient)) . '</price>
    <prices>
      <price>
        <value>' . getNewPrice($a['price'], $a['discount']) * $export_yml_currency_coefficient . '</value>
       <quantity>4</quantity>
      </price>
    </prices>

	<delivery>true</delivery>
	<name>' . $name . '</name>
	<keywords>' . $tags . '</keywords>
	<vendor>PEONY</vendor>
	<vendorCode>' . $a['articul'] . '</vendorCode>
	<country_of_origin>Украина</country_of_origin>
	<description><![CDATA[' . $descr . ']]></description>
	<param name="Цвет">' . $a['color'] . '</param>
	<param name="Ткань">' . $a['tkan'] . '</param>
	';
                                if ($a['discount'] > 0)
                                    $offers_yml .= '<oldprice>' . $oldPrice . '</oldprice>';
//                    if ($a['discount'] > 0) {
//                        $offers_yml .= '<discount>' . $a['discount'] . '%</discount>
//    ';
//                    }
                                if ($a['season'] != '') {
                                    $offers_yml .= '<param name="Cезон">' . $a['season'] . '</param>
	';
                                }
                                if ($a['sostav'] != '') {
                                    $offers_yml .= '<param name="Состав">' . $a['sostav'] . '</param>
	';
                                }

                                if ($a['height'] != '') {
                                    $offers_yml .= '<param name="Длина" unit="см">' . $a['height'] . '</param>
	';
                                }
                                if ($a['hand_height'] != '') {
                                    $offers_yml .= '<param name="Длина рукава" unit="см">' . $a['hand_height'] . '</param>
	';
                                }
                                $offers_yml .= '
	<param name="Размер">' . $r . '</param>
</offer>
';

                            }
                        }

                        // ДУБЛИРУЕМ ТОВАРЫ
                        foreach ($razmeri as $r) {
                            if (isset($warehouse[$r]) && $warehouse[$r] > 0) {
                                $sizes .= $r . '|';

                                $available = 'true';
                                if ($a['warehouse_sum'] == 0)
                                    $available = 'false';

                                // АКЦИЯ НА ВСЁ, КРОМЕ SALE
                                //if(isActionTime()) $a['discount'] = 10;

                                $offers_yml .= '
<offer available="' . $available . '" id="' . $a['id'] . '-' . $r . '-00" selling_type="u" group_id="100' . $groupId . '">
	<url>http://' . $domain . getFullUrl($a) . '</url>
 
	<minimum_order_quantity>1</minimum_order_quantity>
	';


//                    if($a['discount'] > 0)
//                        $offers_yml .= '<oldprice>' . round($price * $export_yml_currency_coefficient, 2) . '</oldprice>';
                                // Оптовые цены:
                                // $price = (getNewPrice($a['price'], $a['discount']) * $export_yml_currency_coefficient);

                                $offers_yml .= '
        <currencyId>' . $export_yml_currency . '</currencyId>
	<categoryId>0</categoryId>
	<categoryName>' . $category['name'] . '</categoryName>';
                                if ($a['image'] != '') {
                                    $offers_yml .= '
	<picture>http://' . $domain . $a['image'] . '</picture>
	';
                                }
                                // Выгружаем все картинки
                                $images = $this->images->getByShopId($a['id'], 1, 1);
                                if ($images) {
                                    foreach ($images as $img) {
                                        $offers_yml .= '
	<picture>http://' . $domain . $img['image'] . '</picture>
	';
                                    }
                                }


                                $descr = getAnons($a['content'], 200);
                                //$words = string_words_count($descr);

                                $name = $a['name'] . ' (' . $r . ' размер, ' . $a['color'] . ')';
                                if (strpos($name, $category['name']) === false)
                                    $name = $category['name_one'] . ' ' . $name;
                                $name .= ' ТМ «PEONY»';

                                // TAGS
                                $tags = getTags($a, $category);

                                $price = getNewPrice($a['price'], $a['discount']) * $export_yml_currency_coefficient;

                                $oldPrice = false;
                                if ($a['discount'] > 0) $oldPrice = $a['price'] * $export_yml_currency_coefficient + ($prom_price_adding * $export_yml_currency_coefficient);

                                if ($prom_price_adding)                                                          // Надбавка для Прома
                                    $onePrice = ($price) + ($prom_price_adding * $export_yml_currency_coefficient);

                                $priceNow =
                                $offers_yml .= '
<price>' . ($price + ($prom_price_adding * $export_yml_currency_coefficient)) . '</price>
    <prices>
      <price>
        <value>' . getNewPrice($a['price'], $a['discount']) * $export_yml_currency_coefficient . '</value>
       <quantity>4</quantity>
      </price>
    </prices>

	<delivery>true</delivery>
	<name>' . $name . '</name>
	<keywords>' . $tags . '</keywords>
	<vendor>PEONY</vendor>
	<vendorCode>' . $a['articul'] . '</vendorCode>
	<country_of_origin>Украина</country_of_origin>
	<description><![CDATA[' . $descr . ']]></description>
	<param name="Цвет">' . $a['color'] . '</param>
	<param name="Ткань">' . $a['tkan'] . '</param>
	';
                                if ($a['discount'] > 0)
                                    $offers_yml .= '<oldprice>' . $oldPrice . '</oldprice>';
//                    if ($a['discount'] > 0) {
//                        $offers_yml .= '<discount>' . $a['discount'] . '%</discount>
//    ';
//                    }
                                if ($a['season'] != '') {
                                    $offers_yml .= '<param name="Cезон">' . $a['season'] . '</param>
	';
                                }
                                if ($a['sostav'] != '') {
                                    $offers_yml .= '<param name="Состав">' . $a['sostav'] . '</param>
	';
                                }

                                if ($a['height'] != '') {
                                    $offers_yml .= '<param name="Длина" unit="см">' . $a['height'] . '</param>
	';
                                }
                                if ($a['hand_height'] != '') {
                                    $offers_yml .= '<param name="Длина рукава" unit="см">' . $a['hand_height'] . '</param>
	';
                                }
                                $offers_yml .= '
	<param name="Размер">' . $r . '</param>
</offer>
';

                            }
                        }
                        $sizes = substr($sizes, 0, -1);
                    } else $sizes = $razmeri;


                    //if($a['discount'] > 0) $category = $this->model_categories->getCategoryById(19);


                }

                $template = str_replace('[OFFERS]', $offers_yml, $template);
            }
        }

        save_ftp_file('prom.xml', $template);

        if (isset($_GET['show'])) {
            header("Content-Type: text/xml");
            header("Expires: Thu, 19 Feb 2998 13:24:18 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Cache-Control: post-check=0,pre-check=0");
            header("Cache-Control: max-age=0");
            header("Pragma: no-cache");
            echo $template;
        }
    }

    public function create_dealer_yml()
    {
        $this->load->helper('sstring_helper');
        $template = getOption('export_yml_template');
        $domain = $_SERVER['SERVER_NAME'];
        if ($template) {
            $template = str_replace('[DATE]', date("Y-m-d"), $template);
            $template = str_replace('[TIME]', date("H:i"), $template);
            $template = str_replace('[SERVER_NAME]', $domain, $template);
            $tkdzst = $this->main->getMain();
            $template = str_replace('[COMPANY]', $tkdzst['title'], $template);
            $usd_to_uah = getCurrencyValue("UAH");
            $usd_to_rur = getCurrencyValue("RUB");
            $dealer_price_adding = getOption('dealer_price_adding');

            $currencies = '
<currency id="UAH" rate="1"/>
<currency id="USD" rate="' . (1 / $usd_to_uah) . '"/>
<currency id="RUR" rate="' . (1 / $usd_to_rur) . '"/>
';
            $template = str_replace('[CURRENCIES]', $currencies, $template);


            if (!$dealer_price_adding) $dealer_price_adding = 2;
            $categories = $this->model_categories->getCategories(1, 'shop');
            $categories_yml = "";
            if ($categories) {
                foreach ($categories as $cat) {
                    if (!in_array($cat['id'], $this->noIncCatIds)) {
                        $categories_yml .= '
<category id="' . $cat['id'] . '">' . $cat['name'] . '</category>';
                    }
                }
            }
            $template = str_replace('[CATEGORIES]', $categories_yml, $template);

            $shop = $this->shop->getArticles(-1, -1, 'DESC', 1);
            $offers_yml = "";
            if ($shop) {
                $export_yml_currency = getOption('export_yml_currency');
                $export_yml_currency_coefficient = getCurrencyValue($export_yml_currency);

                foreach ($shop as $a) {
                    $sizes = "";
                    $warehouse = json_decode($a['warehouse'], true);
                    $razmeri = explode('*', $a['razmer']);
                    if (is_array($razmeri)) {
                        foreach ($razmeri as $r) {
                            if (isset($warehouse[$r]) && $warehouse[$r] > 0)
                                $sizes .= $r . '|';
                        }
                        $sizes = substr($sizes, 0, -1);
                    } else $sizes = $razmeri;

                    $category = $this->model_categories->getCategoryById($a['category_id']);
                    //if($a['discount'] > 0) $category = $this->model_categories->getCategoryById(19);

                    $price = $a['price'] * $export_yml_currency_coefficient;

                    if ($dealer_price_adding)                                                          // Надбавка для Прома
                        $onePrice = ($price) + ($dealer_price_adding * $export_yml_currency_coefficient);

                    $available = 'true';
                    if ($a['warehouse_sum'] == 0)
                        $available = 'false';

                    $offers_yml .= '
<offer available="' . $available . '" id="' . $a['id'] . '">
	<url>http://' . $domain . getFullUrl($a) . '</url>

	<minimum_order_quantity>1</minimum_order_quantity>
	';


//                    if($a['discount'] > 0)
//                        $offers_yml .= '<oldprice>' . round($price * $export_yml_currency_coefficient, 2) . '</oldprice>';
                    // Оптовые цены:
                    // $price = (getNewPrice($a['price'], $a['discount']) * $export_yml_currency_coefficient);

                    $offers_yml .= '
	<currencyId>' . $export_yml_currency . '</currencyId>
	<categoryId>' . $category['id'] . '</categoryId>
	<categoryName>' . $category['name'] . '</categoryName>';
                    if ($a['image'] != '') {
                        $offers_yml .= '
	<picture>http://' . $domain . $a['image'] . '</picture>
	';
                    }
                    // Выгружаем все картинки
                    $images = $this->images->getByShopId($a['id'], 1, 1);
                    if ($images) {
                        foreach ($images as $img) {
                            $offers_yml .= '
	<picture>http://' . $domain . $img['image'] . '</picture>
	';
                        }
                    }


                    $descr = getAnons($a['content'], 200);
                    //$words = string_words_count($descr);

                    $name = $a['name'] . ' (' . $a['color'] . ')';
                    if (strpos($name, $category['name']) === false)
                        $name = $category['name_one'] . ' ' . $name;
                    $name .= ' ТМ «PEONY»';

                    // TAGS
                    $tags = getTags($a, $category);

                    $offers_yml .= '
<price>' . $onePrice . '</price>
<price_sale>' . getNewPrice($onePrice, $a['discount']) . '</price_sale>

	<delivery>true</delivery>
	<name>' . $name . '</name>
	<keywords>' . $tags . '</keywords>
	<vendor>PEONY</vendor>
	<vendorCode>' . $a['articul'] . '</vendorCode>
	<country_of_origin>Украина</country_of_origin>
	<description><![CDATA[' . $descr . ']]></description>
	<param name="Цвет">' . $a['color'] . '</param>
	<param name="Ткань">' . $a['tkan'] . '</param>
	';
                    if ($a['discount'] > 0) {
                        $offers_yml .= '<discount>' . $a['discount'] . '%</discount>
    ';
                    }
                    if ($a['season'] != '') {
                        $offers_yml .= '<param name="Cезон">' . $a['season'] . '</param>
	';
                    }
                    if ($a['sostav'] != '') {
                        $offers_yml .= '<param name="Состав">' . $a['sostav'] . '</param>
	';
                    }

                    if ($a['height'] != '') {
                        $offers_yml .= '<param name="Длина" unit="см">' . $a['height'] . '</param>
	';
                    }
                    if ($a['hand_height'] != '') {
                        $offers_yml .= '<param name="Длина рукава" unit="см">' . $a['hand_height'] . '</param>
	';
                    }
                    $offers_yml .= '
	<param name="Размеры">' . $sizes . '</param>
</offer>
';
                }

                $template = str_replace('[OFFERS]', $offers_yml, $template);
            }
        }

        save_ftp_file('dealer.xml', $template);

        if (isset($_GET['show'])) {
            header("Content-Type: text/xml");
            header("Expires: Thu, 19 Feb 2998 13:24:18 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-cache, must-revalidate");
            header("Cache-Control: post-check=0,pre-check=0");
            header("Cache-Control: max-age=0");
            header("Pragma: no-cache");
            echo $template;
        }
    }

    public function create_yml_sale()
    {
        $this->load->helper('sstring_helper');
        $template = getOption('export_yml_template');
        $domain = $_SERVER['SERVER_NAME'];
        if ($template) {
            $template = str_replace('[DATE]', date("Y-m-d"), $template);
            $template = str_replace('[TIME]', date("H:i"), $template);
            $template = str_replace('[SERVER_NAME]', $domain, $template);
            $tkdzst = $this->main->getMain();
            $template = str_replace('[COMPANY]', $tkdzst['title'], $template);
            $usd_to_uah = getCurrencyValue('UAH');
            $usd_to_rur = getCurrencyValue('RUB');

            $currencies = '
<currency id="UAH" rate="1"/>
<currency id="USD" rate="' . (1 / $usd_to_uah) . '"/>
<currency id="RUR" rate="' . (1 / $usd_to_rur) . '"/>
';
            $template = str_replace('[CURRENCIES]', $currencies, $template);

            $categories = $this->model_categories->getCategories(1, 'shop');
            $categories_yml = "";
            if ($categories) {
                foreach ($categories as $cat) {
                    if ($cat['id'] == 19) {
                        $categories_yml .= '
<category id="' . $cat['id'] . '">' . $cat['name'] . '</category>';
                    }
                }
            }
            $template = str_replace('[CATEGORIES]', $categories_yml, $template);

            $shop = $this->shop->getArticles(-1, -1, 'DESC', 1);
            $offers_yml = "";
            if ($shop) {
                $export_yml_currency = getOption('export_yml_currency');
                $export_yml_currency_coefficient = getOption('usd_to_uah');
                foreach ($shop as $a) {
                    $sizes = "";
                    $warehouse = json_decode($a['warehouse'], true);
                    $razmeri = explode('*', $a['razmer']);
                    if (is_array($razmeri)) {
                        foreach ($razmeri as $r) {
                            if (isset($warehouse[$r]) && $warehouse[$r] > 0)
                                $sizes .= $r . ';';
                        }
                        $sizes = substr($sizes, 0, -1);
                    } else $sizes = $razmeri;

                    $category = false;
                    if ($a['discount'] > 0) {
                        $category = $this->model_categories->getCategoryById(19);
                        if ($a['image_no_logo'] != '')
                            $a['image'] = $a['image_no_logo'];

                        $offers_yml .= '
<offer available="true" id="' . $a['id'] . '">
	<url>http://' . $domain . getFullUrl($a) . '</url>
	<price>' . (getNewPrice($a['price'], $a['discount']) * $export_yml_currency_coefficient) . '</price>
	<currencyId>' . $export_yml_currency . '</currencyId>
	<categoryId>' . $category['id'] . '</categoryId>
	<categoryName>' . $category['name'] . '</categoryName>';
                        if ($a['image'] != '') {
                            $offers_yml .= '
	<picture>http://' . $domain . $a['image'] . '</picture>
	';
                        }
                        // Выгружаем все картинки
                        $images = $this->images->getByShopId($a['id'], 1, 0);
                        if (!$images) $images = $this->images->getByShopId($a['id'], 1, 1);
                        if ($images) {
                            foreach ($images as $img) {
                                $offers_yml .= '
	<picture>http://' . $domain . $img['image'] . '</picture>
	';
                            }
                        }


                        $descr = getAnons($a['content'], 120);
                        //$words = string_words_count($descr);

                        $offers_yml .= '
	<delivery>true</delivery>
	<name>' . $a['name'] . ' (' . $a['color'] . ')</name>
	<vendor>PEONY</vendor>
	<vendorCode>' . $a['articul'] . '</vendorCode>
	<country_of_origin>Украина</country_of_origin>
	<description><![CDATA[' . $descr . ']]></description>
	<param name="Цвет">' . $a['color'] . '</param>
	<param name="Ткань">' . $a['tkan'] . '</param>
	';
                        if ($a['season'] != '') {
                            $offers_yml .= '<param name="Cезон">' . $a['season'] . '</param>
	';
                        }
                        if ($a['sostav'] != '') {
                            $offers_yml .= '<param name="Состав">' . $a['sostav'] . '</param>
	';
                        }
                        if ($a['height'] != '') {
                            $offers_yml .= '<param name="Длина">' . $a['height'] . '</param>
	';
                        }
                        if ($a['hand_height'] != '') {
                            $offers_yml .= '<param name="Длина рукава">' . $a['hand_height'] . '</param>
	';
                        }
                        $offers_yml .= '
	<param name="Размеры">' . $sizes . '</param>
</offer>
';
                    }
                }

                $template = str_replace('[OFFERS]', $offers_yml, $template);
            }
        }

        save_ftp_file('sale.xml', $template);

    }

    public function create_yml_prom_sale()
    {
        $this->load->helper('sstring_helper');
        $template = getOption('export_yml_template');
        $domain = $_SERVER['SERVER_NAME'];
        if ($template) {
            $template = str_replace('[DATE]', date("Y-m-d"), $template);
            $template = str_replace('[TIME]', date("H:i"), $template);
            $template = str_replace('[SERVER_NAME]', $domain, $template);
            $tkdzst = $this->main->getMain();
            $template = str_replace('[COMPANY]', $tkdzst['title'], $template);
            $usd_to_uah = getCurrencyValue('UAH');
            $usd_to_rur = getCurrencyValue('RUB');
            $prom_price_adding = getOption('prom_price_adding');

            $currencies = '
<currency id="UAH" rate="1"/>
<currency id="USD" rate="' . (1 / $usd_to_uah) . '"/>
<currency id="RUR" rate="' . (1 / $usd_to_rur) . '"/>
';
            $template = str_replace('[CURRENCIES]', $currencies, $template);

            $categories = $this->model_categories->getCategories(1, 'shop');
            $categories_yml = "";
            if ($categories) {
                foreach ($categories as $cat) {
                    if ($cat['id'] == 19) {
                        $categories_yml .= '
<category id="' . $cat['id'] . '">' . $cat['name'] . '</category>';
                    }
                }
            }
            $template = str_replace('[CATEGORIES]', $categories_yml, $template);

            $shop = $this->shop->getArticles(-1, -1, 'DESC', 1);
            $offers_yml = "";
            if ($shop) {
                $export_yml_currency = getOption('export_yml_currency');
                $export_yml_currency_coefficient = getOption('usd_to_uah');
                foreach ($shop as $a) {
                    $sizes = "";
                    $warehouse = json_decode($a['warehouse'], true);
                    $razmeri = explode('*', $a['razmer']);
                    if (is_array($razmeri)) {
                        foreach ($razmeri as $r) {
                            if (isset($warehouse[$r]) && $warehouse[$r] > 0)
                                $sizes .= $r . ';';
                        }
                        $sizes = substr($sizes, 0, -1);
                    } else $sizes = $razmeri;

                    $category = false;
                    if ($a['discount'] > 0) {
                        $category = $this->model_categories->getCategoryById(19);
                        if ($a['image_no_logo'] != '')
                            $a['image'] = $a['image_no_logo'];

                        $offers_yml .= '
<offer available="true" id="' . $a['id'] . '">
	<url>http://' . $domain . getFullUrl($a) . '</url>
	<price>' . (getNewPrice(($a['price'] + $prom_price_adding), $a['discount']) * $export_yml_currency_coefficient) . '</price>
	<currencyId>' . $export_yml_currency . '</currencyId>
	<categoryId>' . $category['id'] . '</categoryId>
	<categoryName>' . $category['name'] . '</categoryName>';
                        if ($a['image'] != '') {
                            $offers_yml .= '
	<picture>http://' . $domain . $a['image'] . '</picture>
	';
                        }
                        // Выгружаем все картинки
                        $images = $this->images->getByShopId($a['id'], 1, 0);
                        if (!$images) $images = $this->images->getByShopId($a['id'], 1, 1);
                        if ($images) {
                            foreach ($images as $img) {
                                $offers_yml .= '
	<picture>http://' . $domain . $img['image'] . '</picture>
	';
                            }
                        }


                        $descr = getAnons($a['content'], 120);
                        //$words = string_words_count($descr);

                        $offers_yml .= '
	<delivery>true</delivery>
	<name>' . $a['name'] . ' (' . $a['color'] . ')</name>
	<vendor>PEONY</vendor>
	<vendorCode>' . $a['articul'] . '</vendorCode>
	<country_of_origin>Украина</country_of_origin>
	<description><![CDATA[' . $descr . ']]></description>
	<param name="Цвет">' . $a['color'] . '</param>
	<param name="Ткань">' . $a['tkan'] . '</param>
	';
                        if ($a['season'] != '') {
                            $offers_yml .= '<param name="Cезон">' . $a['season'] . '</param>
	';
                        }
                        if ($a['sostav'] != '') {
                            $offers_yml .= '<param name="Состав">' . $a['sostav'] . '</param>
	';
                        }
                        if ($a['height'] != '') {
                            $offers_yml .= '<param name="Длина">' . $a['height'] . '</param>
	';
                        }
                        if ($a['hand_height'] != '') {
                            $offers_yml .= '<param name="Длина рукава">' . $a['hand_height'] . '</param>
	';
                        }
                        $offers_yml .= '
	<param name="Размеры">' . $sizes . '</param>
</offer>
';
                    }
                }

                $template = str_replace('[OFFERS]', $offers_yml, $template);
            }
        }

        save_ftp_file('prom_sale.xml', $template);

    }

    public function create_yml_rozetka()
    {
        $template = getOption('export_yml_template');
        $domain = $_SERVER['SERVER_NAME'];
        //ob_start();
        if ($template) {
            $template = str_replace('[DATE]', date("Y-m-d"), $template);
            $template = str_replace('[TIME]', date("H:i"), $template);
            $template = str_replace('[SERVER_NAME]', $domain, $template);
            $tkdzst = $this->main->getMain();
            $template = str_replace('[COMPANY]', $tkdzst['title'], $template);

            $currencies = '<currency id="UAH" rate="1"/>';
            $template = str_replace('[CURRENCIES]', $currencies, $template);

            $categories = $this->model_categories->getCategories(1, 'shop');
            $categories_yml = "";
            if ($categories) {
                foreach ($categories as $cat) {
                    if ($cat['id'] != '19' && $cat['id'] != '20' && $cat['id'] != '35' && $cat['id'] != '12' && $cat['id'] != '17') {
                        $categories_yml .= '
<category id="' . $cat['id'] . '">' . $cat['name'] . '</category>';
                    }
                }
            }
            $template = str_replace('[CATEGORIES]', $categories_yml, $template);

            $shop = $this->shop->getArticlesWithDiscount();
            $offers_yml = "";
            if ($shop) {
                $export_yml_currency = 'UAH';
                $export_yml_currency_coefficient = getCurrencyValue('UAH');
                foreach ($shop as $a) {
                    $sizes = "";
                    if ($a['discount'] > 0 && $a['image_no_logo'] != '') $a['image'] = $a['image_no_logo'];  // выводим фото без лого
                    $category = $this->model_categories->getCategoryById($a['category_id']);
                    $name = $category['name_one'] . ' PEONY ' . $a['name'] . ' ' . $a['articul'] . ' ' . $a['color'];
                    $warehouse = json_decode($a['warehouse'], true);

                    $images = $this->images->getByShopId($a['id'], 1, 0);
                    //if(!$images) $images = $this->images->getByShopId($a['id'],1,1);

                    $razmeri = explode('*', $a['razmer']);

                    $color = $this->shop->getColorByName($a['color']);

                    $offer_id = $a['articul'] . '-[SIZE]';

                    $description = '';
                    if ($a['tkan'] != '') $description .= 'Ткань: ' . $a['tkan'] . "\r\n";
                    if ($a['sostav'] != '') $description .= 'Состав: ' . $a['sostav'] . "\r\n";
                    $description .= 'Размеры: ' . str_replace('*', ', ', $a['razmer']) . "\r\n";
                    $description .= htmlentities(strip_tags($a['content']));

                    if (isset($color['id'])) $offer_id .= ':' . $color['id'];
                    $offer = '
<offer available="true" id="' . $offer_id . '">
    <name>' . $name . '</name>
	<url>http://' . $domain . getFullUrl($a) . '</url>
	<price>' . (getNewPrice($a['price'], $a['discount']) * $export_yml_currency_coefficient) . '</price>
	<currencyId>' . $export_yml_currency . '</currencyId>
	<categoryId>' . $category['id'] . '</categoryId>
    <description><![CDATA[' . $description . ']]></description>
	<categoryName>' . $category['name'] . '</categoryName>';
                    if ($a['image'] != '') {
                        $offer .= '
	<picture>http://' . $domain . $a['image'] . '</picture>
	';
                    }
                    // Выгружаем все картинки

                    if ($images) {
                        foreach ($images as $img) {
                            $offer .= '
	<picture>http://' . $domain . $img['image'] . '</picture>
	';
                        }
                    }

                    $offer .= '
	<delivery>true</delivery>
	<vendor>PEONY</vendor>
	<vendorCode>' . $a['articul'] . '</vendorCode>
	<param name="country_of_origin">Украина</param>
	<param name="Цвет">' . $a['color'] . '</param>
	<param name="Ткань">' . $a['tkan'] . '</param>
	';
                    if ($a['season'] != '') {
                        $offers_yml .= '<param name="Cезон">' . $a['season'] . '</param>
	';
                    }
                    if ($a['sostav'] != '') {
                        $offer .= '<param name="Состав">' . $a['sostav'] . '</param>
	';
                    }
                    $offer .= '
	<param name="Размер">[SIZE]</param>
</offer>
';
                    if (is_array($razmeri)) {
                        foreach ($razmeri as $r) {
                            if (isset($warehouse[$r]) && $warehouse[$r] > 0) {
                                $offers_yml .= str_replace('[SIZE]', $r, $offer);
                            }
                        }
                    } else $offers_yml .= str_replace('[SIZE]', $razmeri, $offer);

                }

                $template = str_replace('[OFFERS]', $offers_yml, $template);
            }
        }

        //     $template = ob_get_clean();
        save_ftp_file('rozetka.xml', $template);
//		header("Content-Type: text/xml");
//		header("Expires: Thu, 19 Feb 1998 13:24:18 GMT");
//		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
//		header("Cache-Control: no-cache, must-revalidate");
//		header("Cache-Control: post-check=0,pre-check=0");
//		header("Cache-Control: max-age=0");
//		header("Pragma: no-cache");
//		echo $template;
    }

    public function export_to_vk()
    {
        $this->load->model('Model_export', 'export');
        $cron = $this->export->getNext();
        if (!$cron) die();

    }

    private function getCurrentCurrencyValue($cur, $type)
    {
        $pb = file_get_contents("https://api.privatbank.ua/p24api/pubinfo?exchange&json&coursid=11");
        if ($pb) {
            $pb = json_decode($pb);
            foreach ($pb as $item) {
                if ($item->ccy == $cur) {
                    if ($type == 'buy')
                        return $item->buy;
                    else
                        return $item->sale;
                }
            }
        }
        return false;
    }

    public function updateExchange()
    {
        $currencies = $this->shop->getCurrencies();
        $message = '';
        $message_set = '';
        $send = false;
        if ($currencies) {
            $pb = file_get_contents("https://api.privatbank.ua/p24api/pubinfo?exchange&json&coursid=11");
            if ($pb) {
                $pb = json_decode($pb);
                //vdd($pb[0]->ccy);
                foreach ($currencies as $currency) {
                    //vd($currency);
                    if ($currency['auto_update'] == 1) {

                        foreach ($pb as $item) {
                            //vd($currency);
                            if ($item->ccy == 'RUR') $item->ccy = 'RUB';
                            if ($item->ccy == 'UAH') {

                            }
                            //vdd($pb);
                            if ($currency['code'] == $item->ccy) {
                                //var_dump($item);
//echo $item->ccy.'<hr>';
                                $uahToUsd = $this->getCurrentCurrencyValue('USD', 'sale');
                                $uahToUsdV = round(($uahToUsd / $currency['auto_update_plus']), $currency['numbs_after']);
                                //$this->db->where('code', 'UAH')->limit(1)->update('currencies', array('value' => $uahToUsdV));
                                $uahToVal = $this->getCurrentCurrencyValue('RUR', 'buy');

                                $value = $uahToUsd / $uahToVal;
                                //$value = $uahToUsd / $uahToVal + $currency['auto_update_plus'];
                                //$value = $uahToUsd / $uahToVal * 0.95;

                                $value = round(($value / $currency['auto_update_plus']), $currency['numbs_after']);

                                if ($value != $currency['value']) {
                                    $send = true;
                                    $message_set .= 'Валюта ' . $currency['name'] . ' обновиласть и стала: ' . $value . ' (Предыдущее значение: ' . $currency['value'] . ')';
                                } else $message_set .= 'Валюта ' . $currency['name'] . ' не изменилась и осталась ' . $value;
                                //vd($value);
                                $this->db->where('id', $currency['id'])->limit(1)->update('currencies', array('value' => $value));
//                                echo 'UsdToRub = '.$value;
//                                $profit = round((1 - $value / $currency['value']) * 100, 1);    // Вычисляем разницу между старым и новым курсом
//                                if ($profit > 5) {        // Если разница между старым и новым курсом > 5%, сохраняем новый курс в базу
//                                    $message_set .= 'При автоматическом обновлении курса валюты <b>' . $currency['name'] . '</b>, между прежним и сегодняшним значениями, возникла разница в ' . $profit . '%!<br />
//                                    Значение валюты было изменено с <b>' . $currency['value'] . '</b> на <b>' . $value . '</b> за 1 ' . $this->shop->getMainCurrency();
//                                    $send = true;
//                                }
                            }
                        }
                    }
                }
            }
        }

        echo $message_set;

        if ($send) {
            $message .= $message_set;
            $this->load->helper('mail_helper');
            $to = getOption('admin_email');
            mail_send($to, 'Автоматическое обновление курса валют', $message);
        }
    }

    public function createNewOrdersFiles()
    {
        $unix = time() - 1500;
        $this->db->where('torgsoft_file', 0);
        $this->db->where('unix <', $unix);
        $this->db->where('status <>', 'new');
        $orders = $this->db->get('orders')->result_array();
        //vd($orders);
        if ($orders) {
            foreach ($orders as $order) {
                $writed = writeOrderFile($order);
                if ($writed)
                    $this->db->where('id', $order['id'])->limit(1)->update('orders', array('torgsoft_file' => 1));
            }
        }
    }

    public function createMailerSaleCrons()
    {
        $products = $this->shop->getForMailer('sale');
        if ($products) {

            $idsArr = array();
            foreach ($products as $product) {
                array_push($idsArr, $product['id']);
            }

            $shop_json = json_encode($idsArr);

            $mailer_sale_active = getOption('mailer_sale_active');
            if ($mailer_sale_active == 1) {
                $this->load->helper('mail_helper');

                $mailer_sale_header = getOption('mailer_sale_header');
                $mailer_sale_subject = getOption('mailer_sale_subject');
                $mailer_sale_template = getOption('mailer_sale_template');

                $users = $this->users->getMailerUsers();
                if ($users) {
                    foreach ($users as $user) {
                        $user_name = $user['name'] . ' ' . $user['lastname'];
                        $content = str_replace('[name]', $user_name, $mailer_sale_template);
                        $message = createEmail($mailer_sale_header, $mailer_sale_subject, $content, $products, false, false, 0, $user, true);

                        // Проверяем, чтобы не было дубля
                        $this->db->where('to_email', $user['email']);
                        $this->db->where('shop_json', $shop_json);
                        $this->db->limit(1);
                        $this->db->from('mailer_cron');
                        $exists = $this->db->count_all_results();
                        //                    vd($user['login']);
                        //                      echo $message;
//vdd($exists);
                        if ($exists == 0) {      // если такой заготовки письма нет, то добавляем...
                            $dbins = array(
                                'to_email' => $user['email'],
                                'user_id' => $user['id'],
                                'login' => $user['login'],
                                'subject' => getOption('mailer_sale_subject'),
                                'message' => $message,
                                'date' => date("Y-m-d H:i"),
                                'unix' => time(),
                                'shop_json' => $shop_json
                            );

                            $this->db->insert('mailer_cron', $dbins);
                        }
                    }
                }
            }

            // Убираем у товаров отметку sale
            foreach ($products as $product) {
                $dbins = array('mailer_sale' => 0);
                $this->db->where('id', $product['id']);
                $this->db->limit(1);
                $this->db->update('shop', $dbins);
                //$this->db->where('id',$product['id'])->limit(1)->update('shop',array('mailer_sale' => 0));
            }
        }

    }

    public function createMailerNewCrons()
    {
        $products = $this->shop->getForMailer('new');
        if ($products) {

            $idsArr = array();
            foreach ($products as $product) {
                array_push($idsArr, $product['id']);
            }

            $shop_json = json_encode($idsArr);

            $mailer_sale_active = getOption('mailer_new_active');
            if ($mailer_sale_active == 1) {
                $this->load->helper('mail_helper');

                $mailer_sale_header = getOption('mailer_new_header');
                $mailer_sale_subject = getOption('mailer_new_subject');
                $mailer_sale_template = getOption('mailer_new_template');

                $users = $this->users->getMailerUsers();
                if ($users) {
                    foreach ($users as $user) {
                        $user_name = $user['name'] . ' ' . $user['lastname'];
                        $content = str_replace('[name]', $user_name, $mailer_sale_template);
                        $message = createEmail($mailer_sale_header, $mailer_sale_subject, $content, $products);

                        // Проверяем, чтобы не было дубля
                        $this->db->where('to_email', $user['email']);
                        $this->db->where('shop_json', $shop_json);
                        $this->db->limit(1);
                        $this->db->from('mailer_cron');
                        $exists = $this->db->count_all_results();
                        //                    vd($user['login']);
                        //                      echo $message;
//vdd($exists);
                        if ($exists == 0) {      // если такой заготовки письма нет, то добавляем...
                            $dbins = array(
                                'to_email' => $user['email'],
                                'user_id' => $user['id'],
                                'login' => $user['login'],
                                'subject' => getOption('mailer_new_subject'),
                                'message' => $message,
                                'date' => date("Y-m-d H:i"),
                                'unix' => time(),
                                'shop_json' => $shop_json
                            );

                            $this->db->insert('mailer_cron', $dbins);
                        }
                    }
                }
            }

            // Убираем у товаров отметку new
            foreach ($products as $product) {
                $dbins = array('mailer_new' => 0);
                $this->db->where('id', $product['id']);
                $this->db->limit(1);
                $this->db->update('shop', $dbins);
                //$this->db->where('id',$product['id'])->limit(1)->update('shop',array('mailer_sale' => 0));
            }
        }

    }

    public function sms_send()
    {
        $this->db->where('status', 0);
        $this->db->order_by('id', 'ASC');
        $this->db->limit(1);
        $sms = $this->db->get('sms_mailers_cron')->result_array();
        if (isset($sms[0])) {
            $sms = $sms[0];
            $this->load->helper('sms_helper');
            $result = sms_send($sms['tel'], $sms['text']);
            $dbins = array(
                'status' => 1,
                'answer' => json_encode($result->ResultArray),
                'date' => date("Y-m-d H:i")
            );
            $this->db->where('id', $id)->limit(1)->update('sms_mailers_cron', $dbins);
        }
    }

    public function mailerSend()
    {
        $mailer_count_one_time = getOption('mailer_count_one_time');
        if (!$mailer_count_one_time) $mailer_count_one_time = 5;
        $this->db->where('complete', 0);
        $this->db->order_by('id', 'ASC');
        $this->db->limit($mailer_count_one_time);
        $mailers = $this->db->get('mailer_cron')->result_array();
        if ($mailers) {
            foreach ($mailers as $mailer) {
                if (isset($mailer['to_email']) && $mailer['to_email'] != NULL && $mailer['to_email'] != false && trim($mailer['to_email']) != "") {
                    $this->load->helper('mail_helper');
                    $result = mail_send($mailer['to_email'], $mailer['subject'], $mailer['message']);

                    //echo 'Отправлено письмо пользователю ' . $mailer['to_email'] . ':<br />' . $mailer['message'];
                    if (!$result) {
                        $admin_email = getOption('admin_email');
                        $admin_message = 'Ошибка отправки письма клиенту ID:' . $mailer['user_id'] . ' на email: "' . $mailer['to_mail'] . '"<br />Возможно это произошло по причине не правильного, либо не существующего e-mail адреса.<br /><a href="//' . $_SERVER['SERVER_NAME'] . '/admin/users/edit/' . $mailer['user_id'] . '/">Перейти к редактированию клиента в админке</a><br/><br/>
                        ';
                        mail_send($admin_email, 'Ошибка отправки рассылки на адрес: ' . $mailer['to_mail'], $admin_message);
                        $this->db->where('id', $mailer['id'])->limit(1)->update('mailer_cron', array('complete' => 1, 'error' => 1, 'complete_date' => date("Y-m-d H:i"), 'complete_unix' => time()));
                        $this->db->where('id', $mailer['user_id'])->limit(1)->update('users', array('mailer' => 0));
                    } else $this->db->where('id', $mailer['id'])->limit(1)->update('mailer_cron', array('complete' => 1, 'complete_date' => date("Y-m-d H:i"), 'complete_unix' => time()));
                }

            }
        }
    }


    public function create_yml_bigest_price()
    {
        $this->load->helper('sstring_helper');
        $template = getOption('export_yml_template');
        $domain = $_SERVER['SERVER_NAME'];
        if ($template) {
            $template = str_replace('[DATE]', date("Y-m-d"), $template);
            $template = str_replace('[TIME]', date("H:i"), $template);
            $template = str_replace('[SERVER_NAME]', $domain, $template);
            $tkdzst = $this->main->getMain();
            $template = str_replace('[COMPANY]', $tkdzst['title'], $template);
            $usd_to_uah = getCurrencyValue("UAH");
            $usd_to_rur = getCurrencyValue("RUB");
            $yml_bigest_price = getOption('yml_bigest_price');

            $currencies = '
<currency id="UAH" rate="1"/>
<currency id="USD" rate="' . (1 / $usd_to_uah) . '"/>
<currency id="RUR" rate="' . (1 / $usd_to_rur) . '"/>
';
            $template = str_replace('[CURRENCIES]', $currencies, $template);


            if (!$yml_bigest_price) $yml_bigest_price = 1;
            $categories = $this->model_categories->getCategories(1, 'shop');
            $categories_yml = "";
            if ($categories) {
                foreach ($categories as $cat) {
                    if ($cat['id'] != 12 && $cat['id'] != 35 && $cat['id'] != 20 && $cat['id'] != 17 && $cat['id'] != 19) {
                        $categories_yml .= '
<category id="' . $cat['id'] . '">' . $cat['name'] . '</category>';
                    }
                }
            }
            $template = str_replace('[CATEGORIES]', $categories_yml, $template);

            $shop = $this->shop->getArticles(-1, -1, 'DESC', 1);
            $offers_yml = "";
            if ($shop) {
                $export_yml_currency = getOption('export_yml_currency');
                $export_yml_currency_coefficient = getCurrencyValue($export_yml_currency);
                foreach ($shop as $a) {
                    $sizes = "";
                    $warehouse = json_decode($a['warehouse'], true);
                    $razmeri = explode('*', $a['razmer']);
                    if (is_array($razmeri)) {
                        foreach ($razmeri as $r) {
                            if (isset($warehouse[$r]) && $warehouse[$r] > 0)
                                $sizes .= $r . '|';
                        }
                        $sizes = substr($sizes, 0, -1);
                    } else $sizes = $razmeri;

                    $category = $this->model_categories->getCategoryById($a['category_id']);
                    //if($a['discount'] > 0) $category = $this->model_categories->getCategoryById(19);

                    if ($yml_bigest_price) {                                                          // Надбавка
                        $res = $a['price'] / 100 * $yml_bigest_price;
                        $price = $a['price'] + $res;
                    }

                    $offers_yml .= '
<offer available="true" id="' . $a['id'] . '">
	<url>http://' . $domain . getFullUrl($a) . '</url>
	<price>' . (getNewPrice($price, $a['discount']) * $export_yml_currency_coefficient) . '</price>';
//                    if($a['discount'] > 0)
//                        $offers_yml .= '<oldprice>' . round($price * $export_yml_currency_coefficient, 2) . '</oldprice>';
                    // Оптовые цены:
                    $offers_yml .= '
    <prices>
      <price>
        <value>' . (getNewPrice($a['price'], $a['discount']) * $export_yml_currency_coefficient) . '</value>
       <quantity>4</quantity>
      </price>
    </prices>
                    ';
                    $offers_yml .= '
	<currencyId>' . $export_yml_currency . '</currencyId>
	<categoryId>' . $category['id'] . '</categoryId>
	<categoryName>' . $category['name'] . '</categoryName>';
                    if ($a['image'] != '') {
                        $offers_yml .= '
	<picture>http://' . $domain . $a['image'] . '</picture>
	';
                    }
                    // Выгружаем все картинки
                    $images = $this->images->getByShopId($a['id'], 1, 1);
                    if ($images) {
                        foreach ($images as $img) {
                            $offers_yml .= '
	<picture>http://' . $domain . $img['image'] . '</picture>
	';
                        }
                    }


                    $descr = getAnons($a['content'], 200);
                    //$words = string_words_count($descr);

                    $name = $a['name'] . ' (' . $a['color'] . ')';
                    if (strpos($name, $category['name']) === false)
                        $name = $category['name_one'] . ' ' . $name;
                    $name .= ' ТМ «PEONY»';

                    // TAGS
                    $tags = getTags($a, $category);

                    $offers_yml .= '
	<delivery>true</delivery>
	<name>' . $name . '</name>
	<keywords>' . $tags . '</keywords>
	<vendor>PEONY</vendor>
	<vendorCode>' . $a['articul'] . '</vendorCode>
	<country_of_origin>Украина</country_of_origin>
	<description><![CDATA[' . $descr . ']]></description>
	<param name="Цвет">' . $a['color'] . '</param>
	<param name="Ткань">' . $a['tkan'] . '</param>
	';
                    if ($a['discount'] > 0) {
                        $offers_yml .= '<discount>' . $a['discount'] . '</discount>
    ';
                    }
                    if ($a['season'] != '') {
                        $offers_yml .= '<param name="Cезон">' . $a['season'] . '</param>
	';
                    }
                    if ($a['sostav'] != '') {
                        $offers_yml .= '<param name="Состав">' . $a['sostav'] . '</param>
	';
                    }

                    if ($a['height'] != '') {
                        $offers_yml .= '<param name="Длина" unit="см">' . $a['height'] . '</param>
	';
                    }
                    if ($a['hand_height'] != '') {
                        $offers_yml .= '<param name="Длина рукава" unit="см">' . $a['hand_height'] . '</param>
	';
                    }
                    $offers_yml .= '
	<param name="Размеры">' . $sizes . '</param>
</offer>
';
                }

                $template = str_replace('[OFFERS]', $offers_yml, $template);
            }
        }

        save_ftp_file('for-partners.xml', $template);
//		header("Content-Type: text/xml");
//		header("Expires: Thu, 19 Feb 1998 13:24:18 GMT");
//		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
//		header("Cache-Control: no-cache, must-revalidate");
//		header("Cache-Control: post-check=0,pre-check=0");
//		header("Cache-Control: max-age=0");
//		header("Pragma: no-cache");
//		echo $template;
    }

    public function price_download_count()
    {
        $this->load->model('Model_main', 'main');
        $tkdzst = $this->main->getMain();

        $price_downloads_count_yesterday = $tkdzst['price_downloads_count_today'];
        $dbins = array(
            'price_downloads_count_yesterday' => $price_downloads_count_yesterday,
            'price_downloads_count_today' => 0
        );
        $this->db->where('id', 1)->limit(1)->update('main', $dbins);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */