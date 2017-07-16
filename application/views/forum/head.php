<html>
    <head>
        <title><?=$title?></title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="keywords" content="<?=$keywords?>" />
        <meta name="description" content="<?=$description?>" />
        <meta name="robots" content="<?=$robots?>" />
        <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
        
        <script type="text/javascript" src="/js/jquery-1.7.1.min.js"></script>
        
        <link rel="stylesheet" href="/css/forum.css" type="text/css" />
        
        <script type="text/javascript" src="/js/tiny_mce/jquery.tinymce.js"></script>
        <script type="text/javascript">
                $().ready(function() {
                        $('textarea.tinymce').tinymce({
                                // Location of TinyMCE script
                                script_url : '/js/tiny_mce/tiny_mce.js',
        
                                // General options
                                theme : "simple",
                                plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
        
                                // Theme options
                                theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
                                theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                                theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl,|,fullscreen",
                                theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
                                theme_advanced_toolbar_location : "top",
                                theme_advanced_toolbar_align : "left",
                                theme_advanced_statusbar_location : "bottom",
                                theme_advanced_resizing : true,
        
                                // Example content CSS (should be your site CSS)
                                content_css : "css/content.css",
        
                                // Drop lists for link/image/media/template dialogs
                                template_external_list_url : "lists/template_list.js",
                                external_link_list_url : "lists/link_list.js",
                                external_image_list_url : "lists/image_list.js",
                                media_external_list_url : "lists/media_list.js",
        
                                // Replace values for the template plugin
                                template_replace_values : {
                                        username : "Some User",
                                        staffid : "991234"
                                }
                        });
                });
        </script>
        <script type="text/javascript" src="/js/slimbox2.js"></script>
        <link rel="stylesheet" href="/css/slimbox2.css" type="text/css" media="screen" />
        <script type="text/javascript" src="/js/forum/styleswi.js"></script>
<script type="text/javascript" src="/js/forum/forum_fn.js"></script>

<link href="/css/forum/print000.css" rel="stylesheet" type="text/css" media="print" title="printonly">
<link href="/css/forum/style000.css" rel="stylesheet" type="text/css" media="screen, projection">

<link href="/css/forum/normal00.css" rel="stylesheet" type="text/css" title="A">
<link href="/css/forum/medium00.css" rel="alternate stylesheet" type="text/css" title="A+">
<link href="/css/forum/large000.css" rel="alternate stylesheet" type="text/css" title="A++">
    </head>
    <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">