<?php

	$ref = pbLib::getCheckRef($data);
	$payId = pbLib::getPayId($data);
	if ($ref && $payId && $pbAdapter->confirmPayment($ref, $payId))
	{
		$pbXml = pbXml::data('', $schema, 'Gateway', $ref);
	}

	if (!$pbXml)
	{
		$pbXml = pbXml::error(99, 'Помилка підтвердження платежу');
	}

?>