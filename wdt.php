<?php
/*--------------[Constants]------------------*/
const Black = 30,Red = 31,Green = 32,Brown = 33,Blue = 34,Purple = 35,Cyan = 36,LightGray = 37,
DarkGray = 30,LightRed = 31,LightGreen = 32,Yellow = 33,LightBlue = 34,LightPurple = 35,LightCyan = 36,White = 37;

define('Templates', 'Resources/Templates/');
define('COLOR',is_bool(strpos('Windows',php_uname('s'))));
echo is_bool(strpos('Windows',php_uname('s')));
define('Root', str_replace('\\', '/', __DIR__.'/'));
/*--------------[Message]------------------*/
function error($message) { setForeColor(Red); echo 'error: '; resetForeColor(); echo $message."\n"; }
function success($message) { setForeColor(Green); echo 'success: '; resetForeColor(); echo $message."\n"; }
function warning($message) { setForeColor(Yellow); echo 'warning: '; resetForeColor(); echo $message."\n"; }
function resetForeColor() {echo (COLOR ? "\033[" : "").LightGray."m";}
function setForeColor($colorCode) {echo (COLOR ? "\033[" : "").$colorCode."m";}
/*--------------[IO]------------------*/
function getFiles($source) {
    $files = scandir($source);
    unset($files[0]);
    unset($files[1]);
    return $files;
}
function deleteDirectory($source)
{
    $files = getFiles($source);
    foreach ($files as $file) {
        if (is_dir($source.$file)) deleteDirectory($source.$file.'/');
        else unlink($source.$file);
    }
    rmdir($source);
}
function copyDirectory($source, $destination,$overwrite = true)
{
    if (!file_exists($destination)) createDirectory($destination);
    $files = getFiles($source);
    foreach ($files as $file) {
        if (in_array($file, array(".",".."))) continue;
        if (is_dir($source.$file)) {
            copyDirectory($source.$file.'/', $destination.$file.'/');
        } 
        elseif($overwrite || !file_exists($destination.$file)) copy($source.$file, $destination.$file);
    }
}
function createDirectory($path)
{
    if (!file_exists($path)) {
        mkdir($path, 0777);
        success("$path created.");
    } else {
        warning("$path already exists.");
    }
}
function createFile($path, $content = null, $overwrite = false)
{
    if ($overwrite || !file_exists($path)) {
        $file = fopen($path, 'w');
        if (is_bool($file)) {
            error("can not create file.");
        } else {
            if ($content != null) {
                fwrite($file, $content);
            }
            fclose($file);
            chmod($path, 0777); 
            success("$path created.");
        }
    } else {
        warning("$path already exists.");
    }
}
/*--------------[Help]------------------*/
function help_string($str,$left = 3,$length = 25) {
    $str = str_pad($str,$left + strlen($str),' ',STR_PAD_LEFT);
    return str_pad($str,$length,' ',STR_PAD_RIGHT);
}
function help_usage($usage) {return '  '.$usage."\r\n";}
function help_option() {}
function help($usage, array $options = [])
{
    resetForeColor();
    echo "Usage:\r\n";
    if(is_array($usage)) foreach($usage as $value) echo help_usage($value);
    else echo help_usage($usage);
    
    if (count($options) > 0) {
        echo "\r\nOptions:\r\n";
        foreach ($options as $key => $value) {
            echo help_string($key);
            echo "$value\r\n";
        }
    }
    resetForeColor();
}
/*--------------[WDT]------------------*/
define('Version','1.0.0');

function wdt_usage() {
    $options = getopt('hv',array('version','help','new','update','make','map'));
    if(isset($options['v']) || isset($options['version'])) wdt_version();
    elseif(isset($options['h']) || isset($options['help'])) wdt_help();
    elseif(isset($options['new'])) new_usage();
    elseif(isset($options['update'])) update_usage();
    elseif(isset($options['make'])) make_usage();
    elseif(isset($options['map'])) map_usage();
}
function wdt_help() {
    help(
        'php wdt [new,make,map,install,update] [Arguments] [Options]',
        array(
            '-h , --help'=>'',
            '-v , --version'=>'version',
        )
    );
}
function wdt_version() { echo Version."\r\n"; }
/*--------------[New]------------------*/
function new_usage() {
    $options = getopt('h',array('help'));
    if(isset($options['h']) || isset($options['help'])) new_help();
    else new_project();
}
function new_help() {
    help(
        'php wdt new [Options]',
        array(
            '-h , --help'=>'',
            '-a , --areas'=>'Areas Folder',
        )
    );
}
function new_project() {
    $options = getopt('a',array('areas'));

    createDirectory(Root. 'Assets');
    createDirectory(Root. 'Contents');
    createDirectory(Root. 'Controllers');
    createDirectory(Root. 'Models');
    createDirectory(Root. 'Scripts');
    createDirectory(Root. 'Views');

    createFile(Root. '.htaccess', file_get_contents(Templates.'.htaccess'));
    createFile(Root. 'config.json', file_get_contents(Templates.'config.json'));
    createFile(Root. 'bundels.json', file_get_contents(Templates.'config.json'));
    
    if (isset($options['a'])) createDirectory(Root. 'Areas');
    if (isset($options['f'])) createDirectory(Root. 'Fonts');
    if (isset($options['i'])) createDirectory(Root. 'Images');
    if (isset($options['l'])) createDirectory(Root. 'Layouts');
}
/*--------------[Update]------------------*/
define('Github', 'https://github.com/sajadsalimzadeh/');
define('Repository', 'WDT.PHP.Framework');
define('Branch', 'master');

function update_usage() {
    $options = getopt('h::',array('help::'));
    var_dump($options);
    if(isset($options['h']) || isset($options['help'])) update_help();
    else update_project();
}
function update_help() {
    help(
        'php wdt update [Options]',
        array(
            '-h , --help'=>''
        )
    );
}
function update_project()
{
    $extract = ".update/";
    $folder = $extract.Repository.'-'.Branch.'/';
    $filename = $extract."temp.zip";

    createDirectory($extract);

    $file = fopen($filename, "w");
    fwrite($file, file_get_contents(Github.Repository."/archive/".Branch.".zip"));
    fclose($file);

    $zip = new ZipArchive;
    
    if ($zip->open($filename) != "true") {
        error("Unable to open the Zip File");
    } else {
        $zip->extractTo($extract);
        copyDirectory($folder, '',false);
        $zip->close();
    }
    deleteDirectory($extract);
    success('Framework updated.');
}
/*--------------[Make]------------------*/
function make_usage() {
    if ($second == 'area') {
        make_area($argv[2]);
    } elseif ($second == 'model') {
        if ($countv > 2) {
            make_model($argv[2], $params);
        } else {
            warning('please enter name like this -> make:model [name]');
        }
    } elseif ($second == 'controller') {
        if ($countv > 2) {
            make_controller($argv[2], $params);
        } else {
            warning('please enter name like this -> make:controller [name]');
        }
    }
}
function make_area($name)
{
    createDirectory('Areas/'. $name);
    $path = 'Areas/'.$name.'/';
    createDirectory($path. 'Contents');
    createDirectory($path. 'Controllers');
    createDirectory($path. 'Models');
    createDirectory($path. 'Scripts');
    createDirectory($path. 'Views');
    createFile($path.'Views/_ViewBegin.php');
    createFile($path.'Views/_ViewEnd.php');
}
function make_model($name, $params)
{
    $path = (isset($params['-a']) ? 'Areas/'.$params['-a'].'/Models/' : 'Models/');
    $namespace = (isset($params['-a']) ? $params['-a'].'' : 'Models');
    $tablename = (isset($params['-t']) ? "\n\tpublic function getTbl() {return '".$params['-t']."';}" : '');
    $primarykey = (isset($params['-p']) ? "\n\tpublic function getPK() {return '".$params['-p']."';}" : '');
    $content = file_get_contents(Templates.'Models.txt');
    $content = str_replace('{namespace}', $namespace, $content);
    $content = str_replace('{name}', $name, $content);
    $content = str_replace('{tablename}', $tablename, $content);
    $content = str_replace('{primarykey}', $primarykey, $content);
    createFile($path. $name.'.php', $content);
}
function make_controller($name, $params)
{
    $cpath = (isset($params['-a']) ? 'Areas/'.$params['-a'].'/Controllers/' : 'Controllers/');
    $vpath = (isset($params['-a']) ? 'Areas/'.$params['-a'].'/Views/' : 'Views/');
    $namespace = (isset($params['-a']) ? $params['-a'].'\Controllers' : 'Controllers');

    createDirectory($vpath.$name);
    createFile($vpath.$name.'/Index.php', '');

    $name = $name.'Controller';
    $content = file_get_contents(Templates.'Controller.txt');
    $content = str_replace('{namespace}', $namespace, $content);
    $content = str_replace('{name}', $name, $content);
    createFile($cpath. $name.'.php', $content);
}
/*--------------[Map]------------------*/
function map_usage() {
    global $argv,$argc;
    if($argc == 2) 
    if ($second == 'db') {
        if ($countv > 2) {
            if (isset($params['-u']) && isset($params['-p'])) {
                map_db($argv[2], $params);
            } else {
                warning('please enter username and password');
            }
        } else {
            warning('please enter dbname like this -> map:db [name]');
        }
    }
}
function map_help() {}
function map_db($dbname, $params)
{
    $host = (isset($params['-h']) ? $params['-h'] : 'localhost');
    $like = (isset($params['-l']) ? $params['-l'] : '%');
    $search = (isset($params['-s']) ? $params['-s'] : '');
    $replace = (isset($params['-r']) ? $params['-r'] : '');
    $area = (isset($params['-a']) ? $params['-a'] : null);
    try {
        $conn = new PDO("mysql:host=$host;database=$dbname", $params['-u'], $params['-p']);
        $filename = basename(__FILE__);
        $column = "Tables_in_$dbname";
        unset($params['-p']);
        $statement = $conn->prepare("SHOW TABLES FROM $dbname WHERE $column LIKE '$like';");
        $statement->execute();
        $tables = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($tables as $table) {
            $tablename = $table[$column];
            $name = str_replace($search, $replace, $tablename);
            if ($tablename != $name) {
                $params['-t'] = $tablename;
            } else {
                unset($params['-t']);
            }
            make_model($name, $params);
            make_controller($name, $params);
        }
    } catch (PDOException $ex) {
        error($ex->getMessage());
    }
}

/*--------------[Program]------------------*/
echo php_uname('s');
if ($argc == 1) wdt_help();
else {
    if ($argv[1] == '--new') new_usage();
    elseif ($argv[1] == '--update') update_usage();
    elseif ($argv[1] == '--make') make_usage();
    elseif ($argv[1] == '--map') map_usage();
    else wdt_usage();
}
