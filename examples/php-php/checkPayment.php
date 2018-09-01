<?php
	//пример checkPayment.php
	//скрипт проверки оплаты на стороне продавца
	
	//подключаем библиотеку
	require_once __DIR__.'/MF2Pay.php';
	$client = new MF2PayClient();
	
	//небольшая функция фильтрации данных
	function DataFilter($string) {
		$string = strip_tags($string);
		$string = stripslashes($string);
		$string = htmlspecialchars($string);
		$string = trim($string);
		return $string;
	}
	
	//получаем данные из запроса. Используйте тот метод, что указали при создании платежа
	//на примере POST:
	$ID = DataFilter($_POST['id']);
	$Code = DataFilter($_POST['code']);
	//id - идентификатор платежа в системе MF2Pay
	//code - 24-символьный код для проверки платежа
	
	//пример, выбираем данные о платеже из БД продавца
	//подключаемся к БД, данные для примера
	$db_host = 'localhost';
	$db_user = 'root';
	$db_pass = '';
	$db_name = 'testbase';
	
	$db = mysql_connect($db_host, $db_user, $db_pass) or die(mysql_error());
	mysql_select_db($db_name, $db);
	
	$query = mysql_query("SELECT code FROM payments WHERE paySID='$ID' AND code='$Code' LIMIT 1", $db);
	//где в данном примере paySID - это id платежа из MF2Pay. Назван иначе, чтобы не путать id продажи (на стороне продавца) и id платежа (на стороне сервиса).
	if(mysql_num_rows($query) == 0) {
		//платеж не найден в БД продавца
		die("Ошибка, такой платеж не был найден");
	} else {
		//В БД нашли такой платеж. Но выполним еще одну проверку
		//check() возвратит true, если платеж завершен успешно или false, если он не оплачен или отменен
		if($client->check($ID)) {
			//GREETINGS! Платеж был успешно завершен. Здесь можно выдать покупателю товар или отметить его
			//TODO: sth else
		}
	}