<?php
class MainController
{
     /*Авоторизация*/
    function __construct()
	{
	    global $f3;
		$user=new Security();
		$f3->set('user_name',$_COOKIE['user_name']);
	}
	
	function exitPage()
	{
		$user=new Security();
		$user->exitPage();
	}
    /*Конец Авоторизация*/
	function mainPage()
	{
	    global $f3;
		$page=new Tables();
		$f3->set('filt_dst',true);
		if(isset($_COOKIE)){
		     $f3->set('qukie',$_COOKIE);
		}else{
		$page->mainPage();
		}
	}
 
	function tablePage($f3,$params=NULL)
	{
		$page=new Tables();
		$page->select(isset($params[table])?(int)$params[table]:0);
	}
 
	function editPage($f3,$params=NULL)
	{
		$page=new Tables();
		$page->edit(isset($params[table])?(int)$params[table]:0, isset($params[id])?$params[id]:NULL);
	}
 
	function savePage($f3,$params=NULL)
	{
		$params[id]=isset($params[id])?$params[id]:NULL;
 
		$page=new Tables();
		$page->save($f3->get('POST.saver'), isset($params[table])?(int)$params[table]:0, $params[id]);
	}
 
	function deletePage($f3,$params=NULL)
	{
		$params[id]=isset($params[id])?$params[id]:NULL;
 
		$page=new Tables();
		$page->delete(isset($params[table])?(int)$params[table]:0, $params[id]);
	}
    function balancePage($f3,$params){
        $page=new Tables();
		$page->balance(isset($params[table])?(int)$params[table]:0, isset($params[id])?$params[id]:NULL);
    }
    function filterPage($f3,$params=NULL){
        $page=new Tables();
        $f3->set('cur_table',isset($params[table])?(int)$params[table]:0);
        $f3->set('cur_flt',isset($params[colmn])?urldecode((string)$params[colmn]):NULL);
        
        $this->tablePage($f3,$params);
    }
    function descFilterPage($f3,$params=NULL){
        $f3->set('fltDir',1);
        $this->filterPage($f3,$params);
    }
}
?>