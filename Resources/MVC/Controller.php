<?php
namespace Resources\MVC;

abstract class Controller {
    public $ViewBag;

    protected $Layout = null;

    public function __construct(array $parameters)
    {
        if(This_Area != null){
            if(file_exists(Root_Areas.'index.php')) include Root_Areas.'index.php';
            if(file_exists(Root_Areas.This_Area.'/index.php')) include Root_Areas.This_Area.'/index.php';
        }
        if(method_exists($this,This_Action)) {
            $result = '';
            $ViewBag = new \ViewBag();
            $this->ViewBag = &$ViewBag;
            eval('$result = $this->'.This_Action.'('.(count($parameters) > 0 ? '"'.implode('","',$parameters).'"' : '').');');
            $protocol = substr($result,0,5);
            if($protocol == 'view:') {
                $result = substr($result,5,strlen($result) - 5);
                if(file_exists($result)) {
                    $this->OnActionExecuteBegin();
                    if(file_exists(Root_Views.'_ViewBegin.php')) include Root_Views.'_ViewBegin.php';
                    if($this->Layout != null) {
                        \Layout::BodyBegin();
                        include $result;
                        \Layout::BodyEnd();
                        ob_start();
                        if(file_exists($this->Layout)) {
                            include $this->Layout;
                        } else $this->ErrorLayoutNotExists();
                        ob_end_flush();
                    } else include $result;
                    if(file_exists(Root_Views.'_ViewEnd.php')) include Root_Views.'_ViewEnd.php';
                    $this->OnActionExecuteEnd();
                }
                else $this->ErrorViewNotExists();
            } else echo $result;
        } else $this->ErrorActionNotExists();
    }
    protected function ErrorActionNotExists() { if(Err_Action == null) die('Action ['.This_Action.'] not exists'); else $this->Redirect(Err_Action); }
    protected function ErrorViewNotExists() { if(Err_View == null) die('View ['.This_Action.'] not exists'); else $this->Redirect(Err_View); }
    protected function ErrorLayoutNotExists() { if(Err_Layout == null)  die('Layout ['.$this->Layout.'] not exists'); else $this->Redirect(Err_Layout); }
    protected function AccessDenied() { die('Access Denied'); }

    protected function Redirect($url) { \Resources\Http\Response::Redirect($url); }
    protected function Authorize($accessIndex,$bitIndex,$return = false) { 
        if(true) {
            return true;
        } else $this->AccessDenied();
     }
    protected function Initialize() { }
    protected function View($path = null) { return 'view:'.($path == null ? This_Folder.This_Action.'.php' : $path); }

    protected function OnActionExecuteBegin() { }
    protected function OnActionExecuteEnd() { }

}