<?php
 
class Tables
{
	function __construct()
	{
		global $f3;
 
		$f3->set('main_dir',$f3->get('main_dir'));
		$f3->set('tables',$f3->get('tables'));
	}
 
	function mainPage()
	{
		global $f3;
		$f3->set('my_title',$f3->get('main_title'));
		$f3->set('content','mainPage.htm');
		echo Template::instance()->render('layout.htm');
	}	
 
	function select($table)
	{
		global $db,$f3;
 
		$f3->set('table',$table);
	    
		$table_data=$f3->get('tables.'.$table);
 
		$f3->set('table_primary',$table_data[col][$table_data[primary]][0]);
		$f3->set('table_col',$table_data[col]);
		$f3->set('my_title',$f3->get('main_title').' - '.$table_data[lable]);
		
 	/*Фильтер сортировки*/
        if($f3->get('cur_table')==$table && $f3->get('cur_flt')!=''){
            
            $flt.='`'.$table_data[name].'`.`'.$f3->get('cur_flt').'`';
            if($f3->get('fltDir')==1){$flt.=' DESC';}
        }else{
             $flt='null';
        }
        /*Конец Фильтер сортировки*/
		$query_cols="";
		$query_tables="`".$table_data[name]."`";
		$query_where="";
		
		foreach($table_data[col] as $key=>$val)
		{
            $query_cols.=$query_cols==""?"":", ";
            if(!isset($val[foreign]))
            {
                $query_cols.="`".$table_data[name]."`.`".$val[0]."`";
            }
            if ($_GET['serach_field']){
		        $query_where.="`".$table_data[name]."`.`".$val[0]."`"."LIKE '%".$_GET['serach_field']."%' OR";
		    }
		}
		$query_where = substr($query_where, 0, -2);
		$query.="SELECT ".$query_cols." FROM ".$query_tables.($query_where==""?"":" WHERE ".$query_where)." order by ".$flt.';';
         $f3->set('query',$query);
		$f3->set('result',$db->exec($query));
 
		$f3->set('content','select.htm');
		echo Template::instance()->render('layout.htm');
	}
 
	function edit($table, $id)
	{
		global $db,$f3;
 
		$table_data=$f3->get('tables.'.$table);
 
 
		if(isset($id))
		{
			$id=urldecode($id);
			$result=$db->exec("SELECT * FROM `$table_data[name]` WHERE `".$table_data[col][$table_data[primary]][0]."`='$id'");
 
			$f3->set('my_title',$f3->get('main_title').' - '.$table_data[lable].' - Изменение данных');
			$f3->set('action','edit');
			$f3->set('id',$id);
		}
		else
		{
			$f3->set('my_title',$f3->get('main_title').' - '.$table_data[lable].' - Добавление данных');
			$f3->set('action','add');
		}
 
		foreach($table_data[col] as $key=>$val)
		{
			if(isset($result)){$table_data[col][$key][]=$result[0][$val[0]];}
			if(isset($val[foreign]))
			{

				$as_pos=strpos($val[foreign][select],' as ');
if($as_pos!==false){$val[foreign][select]=substr($val[foreign][select],0,$as_pos);}

				$table_data[col][$key][select]=$db->exec("SELECT `".$val[foreign][field]."` AS `0`, ".$val[foreign][select]." AS `1` FROM `".$val[foreign][table]."`");
			}
		}
 
		$f3->set('table_col',$table_data[col]);
 
		$f3->set('table',$table);
 
		$f3->set('content','edit.htm');
		echo Template::instance()->render('layout.htm');
	}
 
	function save($saver,$table,$id)
	{
		global $f3;
 
		$table_data=$f3->get('tables.'.$table);
 
		if(isset($id))
		{
			$this->update($saver,$table,$id,$table_data);
		}
		else
		{
			$this->insert($saver,$table,$table_data);
		}
 
		$f3->reroute("/".$table);
	}
 
	function update($saver,$table,$id,$table_data)
	{
		global $db;
		$values_str='';
 
		foreach($saver as $key => $val)
			{
				if($values_str!='')$values_str.=',';
				$values_str.="`$key`='$val'";
			}
		$db->exec("UPDATE `$table_data[name]` SET $values_str WHERE `".$table_data[col][$table_data[primary]][0]."`='$id'");
	}
 
	function insert($saver,$table,$table_data)
	{
		global $db;
		$values_str='';
		$columns_str='';
 
		foreach($saver as $key => $val)
			if($val!='')
			{
				if($columns_str!=''){$columns_str.=',';$values_str.=',';}
				$columns_str.="`$key`";
				$values_str.="'$val'";
			}
		$db->exec("INSERT INTO `$table_data[name]` ($columns_str) VALUES ($values_str)");
	}
 
	function delete($table,$id)
	{
		global $db,$f3;
		$id=urldecode($id);
		$table_data=$f3->get('tables.'.$table);
 
		$db->exec("DELETE FROM `$table_data[name]` WHERE `".$table_data[col][$table_data[primary]][0]."`='$id'");
 
		$f3->reroute("/".$table);
	}
	public function balance($table,$id){
	    global $db,$f3;
	    $table_data=$f3->get('bal.'.$table);
	    $f3->set('table_primary',$table_data[col][$table_data[primary]][0]);
		$f3->set('table_col',$table_data[col]);
		$f3->set('my_title',$f3->get('main_title').' - '.$table_data[lable]);
	    
        $id=(int)urldecode($id);
        $id=$id!==null?$id:0;
        $q1="SELECT b.*,
            c.`worker_id`,c.`summary`,c.`rate`,c.`payday`,c.`paid`,
            w.`name` as 'w_name',w.`access`,
            SUM(`history`.sum) as balance
            FROM `credits` as c
            inner join `borrower` as b using(`borrower_id`)
            inner join `workers` as w USING(`worker_id`)
            left join `history` USING(`borrower_id`) WHERE `borrower_id`=$id";
        
	   	$f3->set('result',$db->exec($q1));
         
		$f3->set('content','balance.htm');
		echo Template::instance()->render('layout.htm');
	}
 
}
?>