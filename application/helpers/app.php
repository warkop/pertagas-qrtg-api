<?php
function app_info($key=''){
	$app_info = [
		'title' => 'Energeek Laravel 5.5',
		'description' => 'Energeek Laravel 5.5 Template',
		'name' => 'Energeek Laravel 5.5',
		'shortname' => 'ELA',
		'icon' => aset_extends('img/e-logo.png'),
		'client' => [
			'shortname' => 'Geek',
			'fullname' => 'Energeek',
			'city' => 'Kota Surabaya',
			'category' => 'Private'
		],
		'copyright' => [
			'year' => '2019',
			'text' => '&copy; 2019 Energeek The E-Government Solution'
		],
		'vendor' => [
			'company' => 'Energeek The E-Government Solution',
			'office' => 'Jl Baratajaya 3/16, Surabaya, Jawa Timur',
			'contact' => [
				'phone' => '+62 856-3306-260',
				'email' => 'aditya.tanjung@energeek.co.id',
				'instagram' => 'https://www.instagram.com/energeek.co.id/'
			],
			'site' => 'http://energeek.co.id/'
		]
	];

	$error=0;
	if(empty($key)){
		$result = $app_info;
	}else{
		$result = false;
		$key = explode('.', $key);
		if(is_array($key)){
			$temp = $app_info;
			for ($i=0; $i < count($key); $i++) {
				$error++;
				if(is_array($temp) and count($temp) > 0){
					if(array_key_exists($key[$i], $temp)){
						$error--;
						$result = $temp[$key[$i]];
						$temp = $temp[$key[$i]];
					}
				}
			}
		}
	}

	if($error > 0){
		$result = false;
	}

	return $result;
}

function encText($value='', $md5=false)
{
	$result = $value.'-energeek';

	return (($md5 == false)? $result : md5($result));
}

?>