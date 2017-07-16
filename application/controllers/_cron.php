<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Cron extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Model_shop','shop');
        $this->load->model('Model_images','images');
        $this->load->helper('file');
        $this->load->helper('translit_helper');
        $this->load->library('zip');
    }

    // Заливаем фотки во временную папку
    private function create_images_folder($path = "./upload/temp/zip_price/", $folder_name = "catalog")
    {
        $folder_name = iconv('utf-8','cp1251',$folder_name);

        // Очищаем временную папку
        delete_files($path, true);

        $path_img = $path.$folder_name.'/';
        if(!is_dir($path_img)) mkdir($path_img, 0777) ;


        $articles = $this->shop->getArticles(-1,-1,"ASC",1);
        if($articles)
        {
            $count = count($articles);
            for($i = 0; $i < $count; $i++)
            {
                $a = $articles[$i];
                $image = '.'.$a['image'];
                if(file_exists($image))
                {

                    $pos = strrpos($image,'.');
                    $extension = substr($image,$pos);

                    $file_name = "";
                    if(($i + 1) < 10) $file_name .= "0";
                    if(($i + 1) < 100) $file_name .= "0";
                    $file_name .= ($i+1)."-";
                    $file_name .= $a['articul'];
                    $file_name .= '-'.$a['name'];
                    $file_name .= '-'.$a['color'];


                    $file_name  = translitRuToEn($file_name);
                    copy($image,$path_img.$file_name.$extension);

                }
                $images = $this->images->getByShopId($a['id'], 1);
                if($images)
                {
                    $icount = count($images);
                    for($i2 = 0; $i2 < $icount; $i2++)
                    {
                        $image = '.'.$images[$i2]['image'];

                        $pos = strrpos($image,'.');
                        $extension = substr($image,$pos);

                        $file_name = "";
                        if(($i + 1) < 10) $file_name .= "0";
                        if(($i + 1) < 100) $file_name .= "0";
                        $file_name .= ($i+1)."-";
                        $file_name .= $a['articul'];
                        $file_name .= '-'.$a['name'];
                        $file_name .= '-'.$a['color'];
                        $file_name .= '-'.($i2+2);


                        $file_name  = translitRuToEn($file_name);
                        copy($image,$path_img.$file_name.$extension);
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
        $this->excel->getActiveSheet()->setCellValue('М1', 'Наличие');
        $this->excel->getActiveSheet()->setCellValue('N1', 'Ткань');
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
        $this->excel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);

        //merge cell A1 until D1
        //$this->excel->getActiveSheet()->mergeCells('A1:D1');
        //set aligment to center for that merged cell (A1 to D1)
        $this->excel->getActiveSheet()->getStyle('A1:Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->excel->getActiveSheet()->getStyle('F:H')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        // ЗАПОЛНЯЕМ ЗНАЧЕНИЯМИ

        $articles = $this->shop->getArticles(-1, -1, $order_by = "DESC", 1);
        if($articles)
        {
            $currensy_grn = $this->model_options->getOption('usd_to_uah');
            $currensy_rub = $this->model_options->getOption('usd_to_rur');

            $count = count($articles);
            for($i = 0; $i < $count; $i++)
            {
                $a = $articles[$i];
                $cat = $this->model_categories->getCategoryById($a['category_id']);
                $url = 'http://'.$_SERVER['SERVER_NAME'].'/'.$cat['url'].'/'.$a['url'].'/';

                $a['razmer'] = str_replace('*',', ', $a['razmer']);
                $n = $i + 2;
                $no = "";
                if(($i + 1) < 10) $no .= "0";
                if(($i + 1) < 100) $no .= "0";
                $no .= ($i+1);
                $this->excel->getActiveSheet()->setCellValueExplicit('A'.$n, $no,PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('B'.$n, $a['articul'],PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('C'.$n, $a['name'].' ('.$a['color'].')',PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('D'.$n, $a['color'],PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('E'.$n, $a['razmer'],PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('F'.$n, $a['price']." USD",PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('G'.$n, ($a['price'] * $currensy_grn)." грн.",PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValueExplicit('H'.$n, ($a['price'] * $currensy_rub)." р",PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->setCellValue('I'.$n, $url);
                $this->excel->getActiveSheet()->getCell('I'.$n)->getHyperlink()->setUrl($url);
                $this->excel->getActiveSheet()->setCellValue('J'.$n, 'http://'.$_SERVER['SERVER_NAME'].$a['image']);
                $this->excel->getActiveSheet()->getCell('J'.$n)->getHyperlink()->setUrl($url);
                $this->excel->getActiveSheet()->setCellValueExplicit('K'.$n, $cat['name'],PHPExcel_Cell_DataType::TYPE_STRING);
                $content = 'Цвет: '.$a['color'];
                $content .= ' Ткань: '.$a['tkan'];
                $content .= ' Состав: '.$a['sostav'];
                $content .= strip_tags($a['content']);
                $this->excel->getActiveSheet()->setCellValueExplicit('L'.$n, $content,PHPExcel_Cell_DataType::TYPE_STRING);

                $nal = 'in stock';

                $this->excel->getActiveSheet()->setCellValueExplicit('M'.$n, 'in stock',PHPExcel_Cell_DataType::TYPE_STRING);

                $this->excel->getActiveSheet()->setCellValueExplicit('N'.$n, $a['tkan'],PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }


        if($date_in_name) $filename = date("Y-m-d").'_'.$filename;




        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');



        if($save_to_file)
        {
            $objWriter->save($path.$filename);
        }
        else
        {
            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
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
        if(is_file($filename))
            @unlink($filename);

        if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
            exit("Невозможно открыть <$filename>\n");
        }

        $zip->addFile($path.$xlsfile, $xlsfile);

        $files = get_filenames($path.$folder_name);

        $count = count($files);
        for($i = 0; $i < $count; $i++)
        {
            $f = $files[$i];
            $file = $path.$folder_name.'/'.$f;
            $zip->addFile($file, $folder_name.'/'.$f);
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


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */