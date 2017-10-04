<?php

	$y = date('Y');
	$m = date('m');

	$isError = false;
	$serviceCode = '';
	if (isset($data['Transfer']['Data']['attr']['presearchId']))
	{
		$presearchId = $data['Transfer']['Data']['attr']['presearchId'];

		$currentPayer = $pbAdapter->getPayerByNum($presearchId);
		if (!$currentPayer)
		{
			$pbXml = pbXml::error(2);
			$isError = true;
		}
		else
		{
			$payerDebts = $pbAdapter->selectDebts($currentPayer['id'], $serviceCode);
		}
	}
	else if (isset($data['Transfer']['Data']['Unit']))
	{
		$billIdentifier = $data['Transfer']['Data']['Unit']['attr']['value'];

		$currentPayer = $pbAdapter->getPayerByNum($billIdentifier);
		if (!$currentPayer)
		{
			$currentPayer = $pbAdapter->getPayerByResource($billIdentifier);
		}

		if (!$currentPayer)
		{
			$pbXml = pbXml::error(2);
			$isError = true;
		}
		else
		{
			$pbXml .= '<Message>Данні про заборгованість можна отримати в Касі!</Message>';
//			$pbXml .= '<DopData>';
//			$pbXml .= '<Dop name="name" value="значение"/>';
//			$pbXml .= '</DopData>';

			$payerDebts = $pbAdapter->selectDebts($currentPayer['id'], $serviceCode);
		}
	}

	if (!$isError)
	{                                 	
		$pbXml .=  pbXml::payerInfo($currentPayer, $currentPayer['num']);
		$pbXml .= '<ServiceGroup>';

		foreach ($payerDebts as $debt)
		{
			$currentCompany = $pbAdapter->getCompanyByService($debt['service_id']);

			$tariff = '';
			if (isset($debt['service_price']) && $debt['service_price'] != '')
			{
				$tariff = ' metersGlobalTarif="' . $debt['service_price'] . '"';
			}

			$pbXml .= '<DebtService' . $tariff . ' serviceCode="' . $debt['service_id'] . '">';
			$pbXml .= '<DopData>';
			$pbXml .= '<Dop name="login" value="' . $currentPayer['user_login'] . '"/>';
			$pbXml .= '</DopData>';
			$pbXml .=  pbXml::companyInfo($currentCompany);
			$pbXml .=  pbXml::debtInfo($debt);
//			$pbXml .= '<MeterData>';
//			$pbXml .= '<Meter previosValue="213" tarif="0.01" delta="2341234" name="Телекомунікаційні послуги"/>';
//			$pbXml .= '</MeterData>';
			$pbXml .= '<ServiceName>' . $debt['service_name'] . '</ServiceName>';
			$pbXml .= '<Destination>Оплата за послугу "' . $debt['service_name'] . '" від ' . $currentPayer['name'] . '</Destination>';
			$pbXml .=  pbXml::payerInfo($currentPayer, $currentPayer['num'], $currentPayer['num']);
			$pbXml .= '</DebtService>';
		}
		
		$pbXml .= '</ServiceGroup>';

		$pbXml = pbXml::data($pbXml, $schema, 'DebtPack');
	}

?>