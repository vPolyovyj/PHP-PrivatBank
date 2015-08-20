<?php

/** 
* @desc клас, який реалізує набір допоміжних
* функцій для роботи з PrivatBank Debt API
*/

class pbLib
{
	/**
	* @desc знаходить ідентифікатор платежу в розібраному XML запиті
	* @param array $data array розібрана XML стрічка
	* @return int $reference
	*
	*/
	public static function getCheckRef($data)
	{
		return $data['Transfer']['Data']['CompanyInfo']['CheckReference']['value'];
	}

	/**
	* @desc знаходить ідентифікатор(наданий ПБ) платежу в розібраному XML запиті
	* @param array $data array розібрана XML стрічка
	* @return int $reference
	*
	*/
	public static function getPayId($data)
	{
		return $data['Transfer']['Data']['attr']['id'];
	}

	/**
	* @desc перевірка чи XML стрічка має тип ErrorInfo
	* @param array $data array розібрана XML стрічка
	* @return bool $res
	*
	*/
	public static function isError($data)
	{
		if (isset($data['Transfer']['Data']['attr']) &&
			$data['Transfer']['Data']['attr']['xsi:type'] == 'ErrorInfo')
		{
			return true;
		}

		return false;
	}
}

?>
