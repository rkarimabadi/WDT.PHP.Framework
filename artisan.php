<?php 

/*--------------[Constants]------------------*/
const Black = 30,Red = 31,Green = 32,Brown = 33,Blue = 34,Purple = 35,Cyan = 36,LightGray = 37,
DarkGray = 30,LightRed = 31,LightGreen = 32,Yellow = 33,LightBlue = 34,LightPurple = 35,LightCyan = 36,White = 37;

define('Samples','Resources/Samples/');
define('Github','https://github.com/sajadsalimzadeh/');
define('Repository','WDT.PHP.Framework');
define('Branch','master');
define('Root',str_replace('\\','/',__DIR__.'/'));
/*--------------[Message]------------------*/
function error($message) {setForeColor(Red); echo 'error: '; resetForeColor(); echo $message."\n"; }
function success($message) {setForeColor(Green); echo 'success: '; resetForeColor(); echo $message."\n"; }
function warning($message) {setForeColor(Yellow); echo 'warning: '; resetForeColor(); echo $message."\n"; }

function resetForeColor() {echo "\033[".LightGray."m";}
function setForeColor($colorCode) {echo "\033[".$colorCode."m";}

function copyFoldeFiles($source,$destination) {
    $source .= '/';
    $files = scandir($source);
    foreach ($files as $file) {
      if (in_array($file, array(".",".."))) continue;
      if (copy($source.$file, $destination.$file)) $delete[] = $source.$file;
    }
    foreach ($delete as $file) unlink($file);
}
/*--------------[IO]------------------*/
function createFolder($path,$name = null) {
    if(!file_exists($path.$name)) { mkdir($path.$name); success("$name created."); }
    else warning("$path$name already exists.");
}
function createFile($path,$name = null,$content = null,$overwrite = false) {
    if($overwrite || !file_exists($path.$name)) {
        if(file_exists($path)) {
            $file = fopen($path.$name,'w');
            if(is_bool($file)) error("can not create file.");
            else {
                if($content != null) fwrite($file,$content);
                fclose($file);
                success("$name created.");
            }
        } else error("Directory $path not exists.");
    } else warning("$path$name already exists.");
}
/*--------------[Help]------------------*/
function createHelp($usage,array $arguments = [],array $options = [],$help = null) {
    $maxLength = 0;
    foreach ($arguments as $value) if(strlen($value['name']) > $maxLength) $maxLength = strlen($value['name']);
    foreach ($options as $value) if(strlen($value['name']) > $maxLength) $maxLength = strlen($value['name']);
    setForeColor(Yellow);
    echo "Usage:\r\n";
    resetForeColor();
    echo '  '.$usage."\r\n";
    if(count($arguments) > 0) {
        setForeColor(Yellow);
        echo "Arguments:\r\n";
        foreach($arguments as $value) {
            setForeColor(Green);
            echo "  $value[name]";
            resetForeColor();
            echo str_repeat(' ',$maxLength + 3 - strlen($value['name']));
            echo "$value[text]\r\n";
        }
    }
    if(count($options) > 0) {
        setForeColor(Yellow);
        echo "\r\nOptions:\r\n";
        foreach($options as $value) {
            setForeColor(Green);
            echo "  $value[name]";
            resetForeColor();
            echo str_repeat(' ',$maxLength + 3 - strlen($value['name']));
            echo "$value[text]\r\n";
        }
    }
    if($help != null) {
        setForeColor(Yellow);
        echo "\r\nHelp:\r\n";
        resetForeColor();
        echo "  $help\r\n";
    }
    resetForeColor();
}
/*--------------[Project]------------------*/
function project_new() {
    createFolder(Root,'Assets');
    createFolder(Root,'Contents');
    createFolder(Root,'Controllers');
    createFolder(Root,'Fonts');
    createFolder(Root,'Images');
    createFolder(Root,'Layouts');
    createFolder(Root,'Models');
    createFolder(Root,'Scripts');
    createFolder(Root,'Views');

    createFile(Root,'.htaccess',file_get_contents(Samples.'htaccess.txt'));
    createFile(Root,'config.php',file_get_contents(Samples.'config.txt'));
    
    if(in_array('-a',$argv)) createFolder(Root,'Areas');
}
function project_update() {
    $extract = ".update";
    $folder = $extract.'/'.Repository.'-'.Branch;
    $filename = $extract."/temp.zip";

    createFolder($extract);

    $file = fopen($filename, "w");
    fwrite($file,file_get_contents(Github.Repository."/archive/".Branch.".zip"));
    fclose($file);

    $zip = new ZipArchive;
    
    if($zip->open($filename) != "true") error("Unable to open the Zip File");
    else {
        $zip->extractTo($extract);
        copyFoldeFiles($folder,'');
        $zip->close();
    }
    if(file_exists($filename)) unlink($filename);
    if(file_exists($folder)) rmdir($folder);
    if(file_exists($extract)) rmdir($extract);
    success('Framework updated.');
}
/*--------------[Make]------------------*/
function make_area($name) {
    createFolder('Areas/',$name);
    $path = 'Areas/'.$name.'/';
    createFolder($path,'Contents');
    //createFile($path.'Contents/','style.css');
    createFolder($path,'Controllers');
    createFolder($path,'Models');
    createFolder($path,'Scripts');
    //createFile($path.'Scripts/','script.css');
    createFolder($path,'Views');
    createFile($path.'Views/','_ViewBegin.php');
    createFile($path.'Views/','_ViewEnd.php');
}
function make_model($name,$params) {
    $path = (isset($params['-a']) ? 'Areas/'.$params['-a'].'/Models/' : 'Models/');
    $namespace = (isset($params['-a']) ? $params['-a'].'' : 'Models');
    $tablename = (isset($params['-t']) ? "\n\tpublic function getTbl() {return '".$params['-t']."';}" : '');
    $primarykey = (isset($params['-p']) ? "\n\tpublic function getPK() {return '".$params['-p']."';}" : '');
    $content = file_get_contents(Samples.'Models.txt');
    $content = str_replace('{namespace}',$namespace,$content);
    $content = str_replace('{name}',$name,$content);
    $content = str_replace('{tablename}',$tablename,$content);
    $content = str_replace('{primarykey}',$primarykey,$content);
    createFile($path,$name.'.php',$content);
}
function make_controller($name,$params) {
    $cpath = (isset($params['-a']) ? 'Areas/'.$params['-a'].'/Controllers/' : 'Controllers/');
    $vpath = (isset($params['-a']) ? 'Areas/'.$params['-a'].'/Views/' : 'Views/');
    $namespace = (isset($params['-a']) ? $params['-a'].'\Controllers' : 'Controllers');

    createFolder($vpath,$name);
    createFile($vpath.$name.'/','Index.php','');

    $name = $name.'Controller';
    $content = file_get_contents(Samples.'Controller.txt');
    $content = str_replace('{namespace}',$namespace,$content);
    $content = str_replace('{name}',$name,$content);
    createFile($cpath,$name.'.php',$content);
}
/*--------------[Map]------------------*/
function map_db($dbname,$params) {
    $host = (isset($params['-h']) ? $params['-h'] : 'localhost');
    $like = (isset($params['-l']) ? $params['-l'] : '%');
    $search = (isset($params['-s']) ? $params['-s'] : '');
    $replace = (isset($params['-r']) ? $params['-r'] : '');
    $area = (isset($params['-a']) ? $params['-a'] : null);
    try {
        $conn = new PDO("mysql:host=$host;database=$dbname",$params['-u'],$params['-p']);
        $filename = basename(__FILE__);
        $column = "Tables_in_$dbname";
        unset($params['-p']);
        $statement = $conn->prepare("SHOW TABLES FROM $dbname WHERE $column LIKE '$like';");
        $statement->execute();
        $tables = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach($tables as $table) {
            $tablename = $table[$column];
            $name = str_replace($search,$replace,$tablename);
            if($tablename != $name) $params['-t'] = $tablename;
            else unset($params['-t']);
            make_model($name,$params);
            make_controller($name,$params);
        }
    } catch(PDOException $ex) {error($ex->getMessage());}
}

/*--------------[Program]------------------*/

$countv = count($argv);
$params = array();
for($i = 2;$i < $countv;$i++) {
    $explode = explode('=',$argv[$i]);
    if(count($explode) > 1) $params[$explode[0]] = $explode[1];
}


if($countv == 1) {
    createHelp("",
    [],
    []
    );
}
elseif($argv[1] == 'help') {
    if($argv[2] == 'new') {
        createHelp("new [Options]",
        [],
        [
            ["name"=>"-a, --areas","text"=>"Create Areas Folder"],
            ["name"=>"-f, --fonts","text"=>"Create Fonts Folder"],
            ["name"=>"-l, --layouts","text"=>"Create Layouts Folder"]
        ]);
    }
}
else
{
    if($argv[1] == 'new') {
        project_new();
    }
    if($argv[1] == 'update') {
        project_update();
    }

    $first = $second = '';
    $action = explode(':',$argv[1]);
    if(count($action) > 0) $first = $action[0];
    if(count($action) > 1) $second = $action[1];

    if($first == 'make') {
        if($second == 'area') {
            make_area($argv[2]);
        }
        elseif($second == 'model') {
            if($countv > 2) make_model($argv[2],$params);
            else warning('please enter name like this -> make:model [name]');
        }
        elseif($second == 'controller') {
            if($countv > 2) make_controller($argv[2],$params);
            else warning('please enter name like this -> make:controller [name]');
        }
    }
    elseif($first == 'map') {
        if($second == 'db') {
            if($countv > 2) {
                if(isset($params['-u']) && isset($params['-p'])) map_db($argv[2],$params);
                else warning('please enter username and password');
            } else warning('please enter dbname like this -> map:db [name]');
        }
    }
    if(in_array('build',$argv)) {
        mkdir('');
    }
}