<?php

/** 
* @desc клас для розбору та генерації XML
*/
class pbXml
{
	private static $errors = array(
		1  => 'Невідомий тип запиту',
		2  => 'Абонента не знайдено',
		3  => 'Невірний формат грошової суми',
		4  => 'Невірний формат дати',
		5  => 'Доступ з даного IP не передбачено',
		6  => 'Знайдено більше одного абонента. Уточніть параметри пошуку',
		7  => 'Дублювання платежу.',
		8  => 'Критична помилка.',
		99 => 'Помилка'
	);

/**
 * xml2array() will convert the given XML text to an array in the XML structure.
 * Link: http://www.bin-co.com/php/scripts/xml2array/
 * Arguments : $contents - The XML text
 *             $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
 *             $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
 * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure.
 * Examples: $array =  xml2array(file_get_contents('feed.xml'));
 *           $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
*/

	public static function xml2array($content, $getAttributes = 1, $priority = 'attribute'/*'tag'*/)
	{
		if (!function_exists('xml_parser_create')) return array();

	    //Get the XML parser of PHP - PHP must have this module for the parser to work
		$parser = xml_parser_create('');
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($content), $xmlValues);
		xml_parser_free($parser);

		if(!$xmlValues) return;

	    //Initializations
		$array = array();
		$parents = array();
		$openedTags = array();
		$arr = array();
		$repeatedTagIndex = array(); //Multiple tags with same name will be turned into an array

		$current = &$array;
 
		foreach($xmlValues as $data)
		{
			unset($attributes, $value);//Remove existing values, or there will be trouble
 
			//This command will extract these variables into the foreach scope
			// tag(string), type(string), level(int), attributes(array).
			extract($data);//We could use the array by itself, but this cooler.
 
			$result = array();
			$attributesData = array();
 
			if (isset($value))
			{
				if ($priority == 'tag') $result = $value;
				else $result['value'] = $value;//Put the value in a assoc array if we are in the 'Attribute' mode
			}
 
        	//Set the attributes too.
			if (isset($attributes) and $getAttributes)
			{
				foreach ($attributes as $attr => $val)
				{
					if ($priority == 'tag') $attributesData[$attr] = $val;
					else $result['attr'][$attr] = $val;
				}
			}
 
        	//See tag status and do the needed.
			if ($type == 'open')
			{
				$parent[$level - 1] = &$current;

				if (!is_array($current) or !in_array($tag, array_keys($current)))
				{
					$current[$tag] = $result;
					if ($attributesData) $current[$tag . '_attr'] = $attributesData;
					$repeatedTagIndex[$tag . '_' . $level] = 1;
 
					$current = &$current[$tag]; 
				}
				else
				{ 
					if (isset($current[$tag][0]))
					{
						$current[$tag][$repeatedTagIndex[$tag . '_' . $level]] = $result;
						$repeatedTagIndex[$tag . '_' . $level]++;
					}
					else
					{//This section will make the value an array if multiple tags with the same name appear together
						$current[$tag] = array($current[$tag], $result);//This will combine the existing item and the new item together to make an array
						$repeatedTagIndex[$tag . '_' . $level] = 2;
 
						if (isset($current[$tag . '_attr']))
						{ //The attribute of the last(0th) tag must be moved as well
							$current[$tag]['0_attr'] = $current[$tag . '_attr'];
							unset($current[$tag . '_attr']);
						} 
					}

					$lastItemIndex = $repeatedTagIndex[$tag . '_' . $level] - 1;
					$current = &$current[$tag][$lastItemIndex];
				} 
			}
			else if ($type == 'complete')
			{ //Tags that ends in 1 line '&lt;tag />'
            //See if the key is already taken.
				if (!isset($current[$tag]))
				{ //New Key
					$current[$tag] = $result;
					$repeatedTagIndex[$tag . '_' . $level] = 1;
					if ($priority == 'tag' and $attributesData) $current[$tag . '_attr'] = $attributesData;
 				}
				else
				{ //If taken, put all things inside a list(array)
					if (isset($current[$tag][0]) and is_array($current[$tag]))
					{//If it is already an array... 
						// ...push the new element into that array.
						$current[$tag][$repeatedTagIndex[$tag . '_' . $level]] = $result;
 
						if ($priority == 'tag' and $getAttributes and $attributesData)
						{
							$current[$tag][$repeatedTagIndex[$tag . '_' . $level] . '_attr'] = $attributesData;
						}

						$repeatedTagIndex[$tag . '_' . $level]++; 
					}
					else
					{ //If it is not an array...
						$current[$tag] = array($current[$tag], $result); //...Make it an array using using the existing value and the new value
						$repeatedTagIndex[$tag . '_' . $level] = 1;

						if ($priority == 'tag' and $getAttributes)
						{
							if (isset($current[$tag . '_attr']))
							{ //The attribute of the last(0th) tag must be moved as well 
								$current[$tag]['0_attr'] = $current[$tag . '_attr'];
								unset($current[$tag . '_attr']);
							}
 
							if ($attributesData)
							{
								$current[$tag][$repeatedTagIndex[$tag . '_' . $level] . '_attr'] = $attributesData;
							}
						}

						$repeatedTagIndex[$tag . '_' . $level]++; //0 and 1 index is already taken
					}
				} 
			}
			else if($type == 'close')
			{ //End of tag '&lt;/tag>'
				$current = &$parent[$level - 1];
			}
		}

		return self::htmlspecialcharsRecursive($array);
	}

	static function htmlspecialcharsRecursive($data)
	{
		if (is_array($data))
		{
	    	$result = array();
			foreach ($data as $key => $value)
			{
				$result[$key] = self::htmlspecialcharsRecursive($value);
			}

			return $result;
		}

		if (is_string($data))
		{
			return htmlspecialchars($data);
		}

		return $data;
	}

	protected static function iterateParameters($parameters = array()) 
	{
	    $result = '';
		
		if ($parameters) 
		{
			foreach ($parameters as $key => &$parameter) 
			{
				$parameter = htmlspecialchars($parameter, ENT_COMPAT);

				if ($key and (!(int) $key))
				{
					$result .= ' ' . $key . '="' . $parameter . '"';
				}
				else
				{
					$result .= ' ' . $parameter;
				}
			}
		}
			
		return $result;
	}

	protected static function tag($tagName = '', $value = '', $parameters = array(), 
						$autoclose = false) 
	{
		$result = '<' . $tagName . self::iterateParameters($parameters);

		if (!$autoclose)
		{
			$result .= '>' . $value . '</' . $tagName . '>';
		}
		else
		{
			$result .= '/>';
		}

		return $result;
	}

	public static function error($code, $msg = '')
	{
		if (!$msg)
		{
			$msg = self::$errors[$code];
		}

		$xml  = '<Data xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="ErrorInfo" code="' . $code . '">';
		$xml .= '<Message>' . $msg . '</Message>';
		$xml .= '</Data>';	

		return $xml;
	}

	public static function payerInfo($payer, $billInd, $pn = '')
	{
		$value  = self::tag('Fio', $payer['name']);
		$value .= self::tag('Phone', $payer['phone']);
		if (!$pn)
			$value .= self::tag('Address', $payer['address']);

		$params = array();
		$params['billIdentifier'] = $billInd;
		if ($pn)
			$params['ls'] = $pn;

		return self::tag('PayerInfo', $value, $params);
	}

	public static function companyInfo($company)
	{
		$value  = self::tag('CompanyCode', $company['id']);
//		$value .= self::tag('CompanyName', $company['name']);

		$params = array();
		if ($company['mfo'])
			$params['mfo'] = $company['mfo'];
		if ($company['okpo'])
			$params['okpo'] = $company['okpo'];
		if ($company['account'])
			$params['account'] = $company['account'];

		return self::tag('CompanyInfo', $value, $params);
	}

	public static function debtInfo($debt)
	{
		$value  = self::tag('Year', $debt['year']);
		$value .= self::tag('Month', $debt['month']);
		$value .= self::tag('Charge', $debt['charge']);
		$value .= self::tag('Balance', 0.00);
		$value .= self::tag('Recalc', 0.00);
		$value .= self::tag('Subsidies', 0.00);
		$value .= self::tag('Remission', 0.00);
		$value .= self::tag('LastPaying', $debt['last_paying']);

		$params = array();
		$params['amountToPay'] = $debt['sum'];
		$params['debt'] = $debt['sum'];

		return self::tag('DebtInfo', $value, $params);
	}

	public static function data($inXml, $schema, $type, $ref = '')
	{
		$params = array();
		$params['xmlns:xsi'] = $schema;
		$params['xsi:type']  = $type;
		if ($ref)
		{
			$params['reference'] = $ref;
		}

		return self::tag('Data', $inXml, $params);
	}
}

?>
