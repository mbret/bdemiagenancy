<?php

//Site root dir
define('DIR_ROOT',		PUBLIC_PATH);//$_SERVER['DOCUMENT_ROOT']);
//Images dir (root relative)
define('DIR_IMAGES',	PUBLIC_PATH . '/images');
//Files dir (root relative)
define('DIR_FILES',		PUBLIC_PATH . '/files');


//Width and height of resized image
define('WIDTH_TO_LINK', 500);
define('HEIGHT_TO_LINK', 500);

//Additional attributes class and rel
define('CLASS_LINK', 'lightview');
define('REL_LINK', 'lightbox');

date_default_timezone_set('Asia/Yekaterinburg');
?>
