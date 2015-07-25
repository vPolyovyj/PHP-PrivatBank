<?php

class pbLib
{
	/**
	* get unique reference from data array
	* @param array $data array generated from xml string
	* @return int $refrence
	*
	*/
	public static function getCheckRef($data)
	{
		return $data['Transfer']['Data']['ServiceGroup']['Service']['CompanyInfo']['CheckReference']['value'];
	}

	/**
	* check whether a XML string has type ErrorInfo
	* @param array $data array generated from xml string
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