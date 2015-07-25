<?php

	$ref = pbLib::getCheckRef($data);
	if ($ref && $pbAdapter->confirmPayment($ref))
	{
		$pbXml = pbXml::data('', $schema, 'Gateway', $ref);
	}

	if (!$pbXml)
	{
		$pbXml = pbXml::error(99, 'Помилка підтвердження платежу');
	}

?>