<?php

class Security
{
	
	public $user_data;
	
	function __construct()
	{
		global $f3;
		
		$f3->set('main_dir',$f3->get('main_dir'));		
		$this->loginTest();
	}
	
	function loginTest()
	{
		global $db,$f3,$user_data;
		// Извлекается логин и пароль из переданных данных
		$user_data['login']= isset($_POST['security_login']) ? $_POST['security_login'] : (isset($_COOKIE['security_login']) ? $_COOKIE['security_login'] : '');
		$user_data['password']=isset($_POST['security_password']) ? $_POST['security_password'] : (isset($_COOKIE['security_password']) ? $_COOKIE['security_password'] : '');
		
		// Изначально устанавливается id пользователя=0 (пользователя нет)
		$user_data['id']=0;
		$login_error='Ошибка передачи данных: данные не переданы';
		// Если заданы логин и пароль, проверяется их актуальность
		if($user_data['login'] != '' && $user_data['password'] != '')
		{
			$query="SELECT s.`id`,w.`access`,w.`name`* FROM `s_a` as s inner join `workers` as w on w.`worker_id`=s.`id` WHERE `login`='".preg_replace("/[^a-zA-Z0-9_]/","",$user_data['login'])."' AND `password`='".md5($user_data['password'])."'";
			$result=$db->exec($query);

			// Если пользователь с такими данными найден
			if (count($result)>0)
			{
				// Данные о пользователе сохраняются в переменную
				$user_data['id']=$result[0]['id'];
				$rec=$result->fetch_assoc();
				$user_data['rights']=$rec['access'];
				$user_data['name']=$rec['name'];
				// время жизни COOKIE-данных продлевается на 24 часа
				$cookie_time=time() + 24 * 3600;
				// Логин и пароль сохраняются в COOKIE пользователя
				setcookie('security_login', $user_data['login'], $cookie_time, "/", $_SERVER['HTTP_HOST']);
				setcookie('security_password', $user_data['password'], $cookie_time, "/", $_SERVER['HTTP_HOST']);
				setcookie('user_name', $user_data['name'], $cookie_time, "/", $_SERVER['HTTP_HOST']);
				
				// При попытке совершения любого действия над таблицами, проверяется, есть ли у пользователя право на данное действие
				if($user_data['rights']<10 && (strpos($_SERVER[REQUEST_URI],'/save')!==false || strpos($_SERVER[REQUEST_URI],'/delete/')!==false))
				{
					throw new Exception( "Security error" );
				}
			}
			else
			{
				$login_error='Неправильный логин или пароль.';	
			}
		}
		if(isset($login_error) || $user_data['id']==0)
		{
			$this->loginPage($login_error);
		}
	}
	
	function loginPage($login_error)
	{
		global $f3;
		
		$f3->set('my_title',$f3->get('main_title').' - Страница авторизации');
		$f3->set('login_error',$login_error);
		$f3->set('content','mainPage.htm');
		echo Template::instance()->render('layout.htm');
		exit;
	}
	
	function exitPage()
	{
		global $f3;
		
		$cookie_time=time() - 3600;
		// Логин и пароль сохраняются в COOKIE пользователя
		setcookie('security_login', $user_data['login'], $cookie_time, "/", $_SERVER['HTTP_HOST']);
		setcookie('security_password', $user_data['password'], $cookie_time, "/", $_SERVER['HTTP_HOST']);
		setcookie('user_name', $user_data['name'], $cookie_time, "/", $_SERVER['HTTP_HOST']);
		
		$f3->reroute('/');
	}
}
?>