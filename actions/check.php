<?php

	$totalSum = $data['Transfer']['Data']['TotalSum']['value'];

	if (is_numeric($totalSum))
	{	
		$payetNum = $data['Transfer']['Data']['PayerInfo']['attr']['ls'];

		$ref = $pbAdapter->insertPayment($payetNum, $totalSum);
		if ($ref)
		{
//			$pbXml .= '<Unit save="true" value="value1" type="S" name="name1"/>';
//			$pbXml .= '<Unit save="true" value="value2" type="S" name="name2"/>';
//			$pbXml .= '<Unit save="true" value="value3" type="S" name="name3"/>';
			$pbXml  = pbXml::data($pbXml, $schema, 'Gateway', $ref);
		}
		else
		{
			$pbXml = pbXml::error(99, 'Помилка внесення платежу');
		}
	}
	else
	{
		$pbXml = pbXml::error(3);
	}

?>