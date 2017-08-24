<?php
define('DB_Host', 'localhost');
define('DB_Name', 'Resturant');
define('DB_User', 'root');
define('DB_Pass', '');
define('DB_Exception',\PDO::ERRMODE_EXCEPTION);

define('App_Version', '1.0.0');
define('App_Cache', time());
define('App_Name', 'سامانه ثبت غذا');
define('App_Site', 'http://www.ime.co.ir');
define('App_Builder', 'اداره ICT بورس کالا');
define('App_Builder_Site', 'http://www.sajadsalimzadeh.ir/');

define('Err_404', Root_Http.'Error/Err_404');
define('Err_Controller', Err_404);
define('Err_Action', Err_404);
define('Err_View', Err_404);
define('Err_Layout', Err_404);

date_default_timezone_set("Asia/Tehran");