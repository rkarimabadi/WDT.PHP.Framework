<?php
//Bundle::Add('WDT',Root_Assets_Http.'WDT/UI/style.css');
//Bundle::Add('WDT',Root_Assets_Http.'WDT/UI/script.js');

if(file_exists(Root_Contents.'style.css')) Bundle::Add('Style',Root_Contents_Http.'style.css');
if(This_Area != null) 
{
    if(file_exists(Root_Areas.This_Area.'/Contents/style.css')) Bundle::Add('Style',Root_Areas_Http.This_Area.'Contents/style.css');
    if(file_exists(Root_Areas.This_Area.'/Contents/'.This_Controller.'.css')) Bundle::Add('Style',Root_Areas_Http.This_Area.'Contents/'.This_Controller.'.css');
    if(file_exists(This_Folder.'Contents/style.css')) Bundle::Add('Style',This_Folder_Http.'/Contents/style.css');
    if(file_exists(This_Folder.'Contents/'.This_Action.'.css')) Bundle::Add('Style',This_Folder_Http.This_Controller.'/Contents/'.This_Action.'.css');
} 
else 
{
    if(file_exists(Root_Contents.This_Controller.'.css')) Bundle::Add('Style',Root_Contents_Http.This_Controller.'.css');
    if(file_exists(This_Folder.'Contents/style.css')) Bundle::Add('Style',This_Folder_Http.'/Contents/style.css');
    if(file_exists(This_Folder.'Contents/'.This_Action.'.css')) Bundle::Add('Style',This_Folder_Http.'/Contents/'.This_Action.'.css');
}

if(file_exists(Root_Scripts.'script.js')) Bundle::Add('Script',Root_Scripts_Http.'script.js');
if(This_Area != null) 
{
    if(file_exists(Root_Areas.This_Area.'/Scripts/script.js')) Bundle::Add('Script',Root_Areas_Http.This_Area.'Scripts/script.js');
    if(file_exists(Root_Areas.This_Area.'/Scripts/'.This_Controller.'.js')) Bundle::Add('Script',Root_Areas_Http.This_Area.'Scripts/'.This_Controller.'.js');
    if(file_exists(This_Folder.'Scripts/script.js')) Bundle::Add('Script',This_Folder_Http.'/Scripts/script.js');
    if(file_exists(This_Folder.'Scripts/'.This_Action.'.js')) Bundle::Add('Script',This_Folder_Http.'/Scripts/'.This_Action.'.js');
}
else
{
    if(file_exists(Root_Scripts.This_Controller.'.js')) Bundle::Add('Script',Root_Scripts_Http.This_Controller.'.js');
    if(file_exists(This_Folder.'Contents/script.js')) Bundle::Add('Script',This_Folder_Http.'/Contents/script.js');
    if(file_exists(This_Folder.'Contents/'.This_Action.'.js')) Bundle::Add('Script',This_Folder_Http.'/Contents/'.This_Action.'.js');
}
?>