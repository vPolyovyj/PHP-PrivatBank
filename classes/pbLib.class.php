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
		return $data['Transfer']['Data']['ServiceGroup']['Service']['CompanyInfo']['CheckReference']['value'];
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
