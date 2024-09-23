<?php
$dev_data = array('id'=>'-1','firstname'=>'Michael','lastname'=>'Cabalona','username'=>'ATOMUS1','password'=>'21232f297a57a5a743894a0e4a801fc3','last_login'=>'','date_updated'=>'','date_added'=>'');
if(!defined('base_url')) define('base_url','http://localhost/atom_sms/');
if(!defined('base_app')) define('base_app', str_replace('\\','/',__DIR__).'/' );
if(!defined('dev_data')) define('dev_data',$dev_data);
if(!defined('DB_SERVER')) define('DB_SERVER',"127.0.0.1");
if(!defined('DB_USERNAME')) define('DB_USERNAME',"root");
if(!defined('DB_PASSWORD')) define('DB_PASSWORD',"");
if(!defined('DB_NAME')) define('DB_NAME',"atom_sms");
if(!defined('DB_PORT')) define('DB_PORT',"3306");
if(!defined('DB_CHARSET')) define('DB_CHARSET',"utf8");

$config = array(
    'base_url' => base_url,
    'base_app' => base_app,
    'dev_data' => $dev_data,
    'DB_SERVER' => DB_SERVER,
    'DB_USERNAME' => DB_USERNAME,
    'DB_PASSWORD' => DB_PASSWORD,
    'DB_NAME' => DB_NAME,
    'DB_PORT' => DB_PORT,
    'DB_CHARSET' => DB_CHARSET
);
?>
