<?php
/*--------------[Constants]------------------*/
const Black = 30,Red = 31,Green = 32,Brown = 33,Blue = 34,Purple = 35,Cyan = 36,LightGray = 37,
DarkGray = 30,LightRed = 31,LightGreen = 32,Yellow = 33,LightBlue = 34,LightPurple = 35,LightCyan = 36,White = 37;

define('Templates', 'Resources/Templates/');
define('COLOR',is_bool(strpos('Windows',php_uname('s'))));
define('Root', str_replace('\\', '/', __DIR__.'/'));

$error = error_reporting();
error_reporting(E_ERROR | E_PARSE);
function getoptions() {
    global $argv,$argc;
    $options = array();
    for($i = 0;$i < $argc;$i++) {
        $arg = $argv[$i];
        if(strlen($arg) > 1 && $arg[0] == '-') {
            if($arg[1] == '-') {
                $options[substr($arg,2,strlen($arg))] = ($i + 1 < $argc && $argv[$i + 1][0] != '-' ? $argv[++$i] : true); 
            }
            else {
                $value = substr($arg,2,strlen($arg));
                $options[$arg[1]] = (is_bool($value) ? true : $value);
            }
        }
    }
    return $options;
}
function getargument() {
    global $argv,$argc;
    if($argc > 1) {
        $value = $argv[1];
        if($value[0] != '-') {
            array_splice($argv,1,1);
            $argc--;
            return $value;
        }
    }
}
$options = getoptions();
/*--------------[Message]------------------*/
function error($message) { setForeColor(Red); echo 'error: '; resetForeColor(); echo $message."\n"; }
function success($message) { setForeColor(Green); echo 'success: '; resetForeColor(); echo $message."\n"; }
function warning($message) { setForeColor(Yellow); echo 'warning: '; resetForeColor(); echo $message."\n"; }
function notrecognize($message) { setForeColor(Red); echo 'not recognize: '; resetForeColor(); echo $message."\n"; }
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
    try {
        if (!file_exists($path)) {
            if(mkdir($path, 0777)) success("$path created.");
            else error("can not create directory $path."); 
        } else {
            warning("$path already exists.");
        }
    }
    catch(Exception $e) { error("create directory $path failed , ".$ex->getMessage()); }
}
function createFile($path, $content = null, $overwrite = false)
{
    try {
        if ($overwrite || !file_exists($path)) {
            $file = fopen($path, 'w');
            if ($file) {
                if ($content != null) {
                    fwrite($file, $content);
                }
                fclose($file);
                chmod($path, 0777); 
                success("$path created.");
            } else error("can not create file $path.");
        } else {
            warning("$path already exists.");
        }
    }
    catch(Exception $e) { error("create directory $path failed , ".$ex->getMessage()); }
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

function wdt() {
    global $options;
    $arg = getargument();
    if($arg != null) {
        if ($arg == 'new') new_usage();
        elseif ($arg == 'update') update_usage();
        elseif ($arg == 'map') map_usage();
        else notrecognize($arg);
    }
    else {
        if(isset($options['v']) || isset($options['version'])) wdt_version();
        elseif(isset($options['h']) || isset($options['help'])) wdt_help();
        else wdt_help();
    }
}
function wdt_help() {
    help(
        'php wdt [new,map,install,update] [Arguments] [Options]',
        array(
            '-h , --help'=>'',
            '-v , --version'=>'version',
        )
    );
}
function wdt_version() { echo Version."\r\n"; }
/*--------------[New]------------------*/
function new_usage() {
    global $options;
    $arg = getargument();
    if($arg) 
    {
        if ($arg == 'project') new_project_usage();
        elseif ($arg == 'area') new_area_usage();
        elseif ($arg == 'model') new_model_usage();
        elseif ($arg == 'controller') new_controller_usage();
        else notrecognize($arg);
    }
    else 
    {
        if(isset($options['h']) || isset($options['help'])) new_help();
        else new_project();
    }
    
}
function new_help() {
    help(
        'php wdt new [project,area,model,controller,view] [Options]',
        array(
            '-h , --help'=>''
        )
    );
}
function new_project_usage() {
    global $options;
    if(isset($options['h']) || isset($options['help'])) new_project_help();
    else new_project();
}
function new_project_help() {
    help(
        'php wdt new project [Options]',
        array(
            '-h , --help'=>'',
            '-a , --areas'=>'Create Areas Folder',
            '-f , --fonts'=>'Create Fonts Folder',
            '-i , --images'=>'Create Images Folder',
            '-l , --'=>'Create Layouts Folder',
        )
    );
}
function new_project() {
    global $options;

    createDirectory(Root. 'Assets');
    createDirectory(Root. 'Contents');

    createDirectory(Root. 'Controllers');
    new_controller('Home');

    createDirectory(Root. 'Models');
    new_model('User');

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
function new_area_usage() {
    $arg = getargument();
    if($arg != null) new_area($arg);
    else new_area_help();
}
function new_area_help() {
    help(
        'php wdt new area [Requirements: name] [Options]',
        array(
            '-h , --help'=>''
        )
    );
}
function new_area($name)
{
    if(file_exists(Root.'Areas')) {
        createDirectory('Areas/'. $name);
        $path = 'Areas/'.$name.'/';
        createDirectory($path. 'Contents');
        createDirectory($path. 'Controllers');
        createDirectory($path. 'Models');
        createDirectory($path. 'Scripts');
        createDirectory($path. 'Views');
        createFile($path.'Views/_ViewBegin.php');
        createFile($path.'Views/_ViewEnd.php');
    } else warning('first create Areas directory.');
}
function new_model_usage() {
    $arg = getargument();
    if($arg != null) new_model($arg);
    else new_model_help();
}
function new_model_help() {
    help(
        'php wdt new model [Requirements: name] [Options]',
        array(
            '-h , --help'=>'',
            '-a , --area'=>'in wich area do you want to create?',
            '-t , --tablename'=>'',
            '-p , --primarykey'=>''
        )
    );
}
function new_model($name)
{
    $options = getoptions();
    $path = (isset($options['a']) ? 'Areas/'.$options['a'].'/Models/' : 'Models/');
    $namespace = (isset($options['a']) ? $options['a'].'' : 'Models');
    $tablename = (isset($options['t']) ? "\n\tpublic function getTbl() {return '".$options['t']."';}" : '');
    $primarykey = (isset($options['p']) ? "\n\tpublic function getPK() {return '".$options['p']."';}" : '');
    $content = file_get_contents(Templates.'Models.php');
    $content = str_replace('{namespace}', $namespace, $content);
    $content = str_replace('{name}', $name, $content);
    $content = str_replace('{tablename}', $tablename, $content);
    $content = str_replace('{primarykey}', $primarykey, $content);
    createFile($path. $name.'.php', $content);
}
function new_controller_usage() {
    $arg = getargument();
    if($arg != null) new_controller($arg);
    else new_controller_help();
}
function new_controller_help() {
    help(
        'php wdt make area [Requirements: name] [Options]',
        array(
            '-h , --help'=>'',
            '-a , --area'=>'in wich area do you want to create?',
        )
    );
}
function new_controller($name)
{
    $area = (isset($options['a']) ? $options['a'] : (isset($options['area']) ? $options['area'] : null));

    $options = getoptions();
    $cpath = ($area != null ? 'Areas/'.$area.'/Controllers/' : 'Controllers/');
    $vpath = ($area != null ? 'Areas/'.$area.'/Views/' : 'Views/');
    $namespace = ($area != null ? $area.'\Controllers' : 'Controllers');

    createDirectory($vpath.$name);
    createFile($vpath.$name.'/Index.php', '');

    $name = $name.'Controller';
    $content = file_get_contents(Templates.'Controller.php');
    $content = str_replace('{namespace}', $namespace, $content);
    $content = str_replace('{name}', $name, $content);
    createFile($cpath. $name.'.php', $content);
}
/*--------------[Update]------------------*/
define('Github', 'https://github.com/sajadsalimzadeh/');
define('Repository', 'WDT.PHP.Framework');
define('Branch', 'master');

function update_usage() {
    global $options;
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

/*--------------[Map]------------------*/
function map_usage() {
    global $argv,$argc,$options;
    if($argc > 2) { 
        if ($argv[2] == 'db') map_db();
        if (isset($options['h']) || isset($options['help'])) map_help();
    }
}
function map_help() {
    help(
        'php wdt map [db]',
        array(
            '-h , -help'
        )
    );
}
function map_db()
{
    $options = getopt('duplsra');
    if(isset($options['d'],$options['u'],$options['p'])) {
        try {
            $conn = new PDO("mysql:host=$host;database=$dbname", $options['-u'], $options['-p']);
            $column = "";
            $statement = $conn->prepare("SHOW TABLES FROM $dbname WHERE Tables_in_$options[d] LIKE '".(isset($options['l']) ? $options['l'] : "%")."';");
            $statement->execute();
            $tables = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($tables as $table) {
                $tablename = $table[$column];
                $name = str_replace($search, $replace, $tablename);
                new_model($name);
                new_controller($name);
            }
        } catch (PDOException $ex) {
            error($ex->getMessage());
        }
    }
    else error('please username and password');
}

/*--------------[Program]------------------*/
wdt();
error_reporting($error);