<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('./application/thumbs/ThumbLib.inc.php');
function CreateThumb($sizex, $sizey, $image, $folder)
{
    $filethumb = false;

    if ($sizex > 0 && $sizey > 0 && !empty($image) && file_exists('.'.$image) && !empty($folder)){
	if (!is_dir('./upload/thumbs/'.$folder))
	    mkdir('./upload/thumbs/'.$folder, 0777);
       
	$ex = end(explode('.', $image));
	$filename = end(explode('/', $image));
	$filethumb = '/upload/thumbs/'.$folder.'/'.$sizex.'_'.$sizey.'_'.$filename;
	   
	if (!file_exists('.'.$filethumb)){
	    $thumb = PhpThumbFactory::create('.'.$image);
	    $thumb->adaptiveResize($sizex, $sizey);
	    $thumb->save('.'.$filethumb, $ex);
	}
    }
   
    return $filethumb;
}

function CreateThumb2($sizex, $sizey, $image, $folder)
{
    $filethumb = false;

    if ($sizex > 0 && $sizey > 0 && !empty($image) && file_exists('.'.$image) && !empty($folder)){
	if (!is_dir('./upload/thumbs/'.$folder))
	    mkdir('./upload/thumbs/'.$folder, 0777);
       
	$ex = end(explode('.', $image));
	$filename = end(explode('/', $image));
	$filethumb = '/upload/thumbs/'.$folder.'/'.$sizex.'_'.$sizey.'_'.$filename;
	   
	if (!file_exists('.'.$filethumb)){
	    $thumb = PhpThumbFactory::create('.'.$image);
	    $thumb->resize($sizex, $sizey);
	    $thumb->save('.'.$filethumb, $ex);
	}
    }
   
    return $filethumb;
}