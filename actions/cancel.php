<?php

	$ref = pbLib::getCheckRef($data);
	$res = $pbAdapter->cancelPayment($ref);
	if ($res)
	{
		$pbXml = pbXml::data('', $schema, 'Gateway', $ref);
	}
	else
	{
		$pbXml = pbXml::error(99, 'Помилка скасування платежу');
	}

?>