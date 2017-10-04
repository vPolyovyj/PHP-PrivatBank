<?php

/** 
* @desc демонстраційний клас, який імплементує та реалізує
* інтерфейс для роботи з PrivatBank Debt API
*/

require_once 'pb.class.php';

class pbDemo implements pb
{
	private $companies = array(
		1 => array('id' => 1, 'name' => 'ТОВ "StarBacks"', 'mfo' => '1242143','okpo' => '23412341234', 'account' => '260012323', 'code' => 131),
		2 => array('id' => 2, 'name' => 'МіськГаз', 'mfo' => '434324','okpo' => '87456456543', 'account' => '245545455', 'code' => 291),
		3 => array('id' => 2, 'name' => 'МіськВода', 'mfo' => '344565','okpo' => '46435435543', 'account' => '4657385865', 'code' => 232)
	);

	private $payers = array(
		1 => array('id' => 1, 'name' => 'Алан Квотермейн', 'num' => '111111', 'phone' => '69315143', 'address_id' => 1),
		2 => array('id' => 2, 'name' => 'Сайрес Сміт', 'num' => '111222', 'phone' => '25364453', 'address_id' => 2),
		3 => array('id' => 3, 'name' => 'Залізний Дроворуб', 'num' => '333333', 'phone' => '3453524', 'address_id' => 3),
		4 => array('id' => 4, 'name' => 'Нік Адамс', 'num' => '222444', 'phone' => '25434254', 'address_id' => 4),
		5 => array('id' => 5, 'name' => 'Урфін Джус', 'num' => '333555', 'phone' => '63736465', 'address_id' => 5),
		6 => array('id' => 6, 'name' => 'Аліса із\'Задзеркалля', 'num' => '888888', 'phone' => '53532424', 'address_id' => 6)
	);

	private $addresses = array(
		1 => array('id' => 1, 'name' => 'м. Львів, вул. Дудаєва, буд. 1, кв. 3'),
		2 => array('id' => 2, 'name' => 'м. Івано-Франківськ, вул. Галицька, буд. 5, кв. 21'),
		3 => array('id' => 3, 'name' => 'м. Київ, вул. Андріївський Узвіз, буд. 14А, кв. 9'),
		4 => array('id' => 4, 'name' => 'м. Одеса, вул. Рішельє, буд. 31, кв. 1'),
		5 => array('id' => 5, 'name' => 'м. Одеса, вул. Рішельє, буд. 31, кв. 33'),
		6 => array('id' => 6, 'name' => 'м. Лондон, вул. Пел Мел, буд. 1, кв. 1')
	);

	private $services = array(
		1 => array('id' => 1, 'name' => 'LAN10M', 'price' => 100.00),
		2 => array('id' => 2, 'name' => 'LAN30M', 'price' => 150.00),
		3 => array('id' => 3, 'name' => 'Газ', 	  'price' => 150.00),
		4 => array('id' => 4, 'name' => 'Вода',   'price' => 150.00)
	);

	private $payerServices = array(
		1 => array('id' => 1, 'service_id' => 1, 'payer_id' => 1),
		2 => array('id' => 2, 'service_id' => 1, 'payer_id' => 2),
		3 => array('id' => 3, 'service_id' => 1, 'payer_id' => 3),
		4 => array('id' => 4, 'service_id' => 2, 'payer_id' => 3),
		5 => array('id' => 5, 'service_id' => 2, 'payer_id' => 4),
		6 => array('id' => 6, 'service_id' => 1, 'payer_id' => 5),
		7 => array('id' => 7, 'service_id' => 3, 'payer_id' => 6),
		8 => array('id' => 8, 'service_id' => 4, 'payer_id' => 6)
	);

	private $debts = array(
		1 => array('id' => 1, 'service_id' => 1, 'payer_id' => 1, 'sum' => 200.00, 'balance' => 100.00, 'company_id' => 1),
		2 => array('id' => 2, 'service_id' => 1, 'payer_id' => 2, 'sum' => 100.00, 'balance' => 300.00, 'company_id' => 1),
		3 => array('id' => 3, 'service_id' => 1, 'payer_id' => 3, 'sum' => 100.00, 'balance' => 100.00, 'company_id' => 1),
		4 => array('id' => 4, 'service_id' => 2, 'payer_id' => 3, 'sum' => 250.00, 'balance' => 20.00, 'company_id' => 1),
		5 => array('id' => 5, 'service_id' => 2, 'payer_id' => 4, 'sum' => 300.00, 'balance' => 50.00, 'company_id' => 1),
		6 => array('id' => 6, 'service_id' => 1, 'payer_id' => 5, 'sum' => 0.00  , 'balance' => 30.00, 'company_id' => 1),
		7 => array('id' => 7, 'service_id' => 3, 'payer_id' => 6, 'sum' => 450.00, 'balance' => 30.00, 'company_id' => 2),
		8 => array('id' => 8, 'service_id' => 4, 'payer_id' => 6, 'sum' => 500.00, 'balance' => 21.00, 'company_id' => 3)
	);

	public function getPayerByNum($num)
	{
		$result = array();

		foreach ($this->payers as $payer)
		{
			if ($payer['num'] == $num)
			{
				$address = $this->getPayerAddress($payer['id']);
				$payer['address'] = $address['name'];

				$result = $payer;
				break;
			}
		}

		return $result;
	}

	public function selectPayersByNum($num)
	{
		$result = array();

		foreach ($this->payers as $payer)
		{
			if (strpos($payer['num'], $num) !== false)
			{
				$address = $this->getPayerAddress($payer['id']);
				$payer['address'] = $address['name'];

				$result[] = $payer;
			}
		}

		return $result;
	}

	public function selectPayersByAddr($queryAddr)
	{
		$result = array();

		foreach ($this->payers as $payer)
		{
			$address = $this->getPayerAddress($payer['id']);

			if (strpos($address['name'], 'вул. ' . $queryAddr['street']) !== false &&
				strpos($address['name'], 'буд. ' . $queryAddr['house'])  !== false && (
				!$queryAddr['flat'] || $queryAddr['flat'] &&
				strpos($address['name'], 'кв. ' . $queryAddr['flat']) !== false))
			{
				$payer['address'] = $address['name'];

				$result[] = $payer;
			}
		}

		return $result;
	}

	public function getCompanyByService($serviceCode)
	{
		$company = array();

		foreach ($this->debts as $debt)
		{
			if ($debt['service_id'] == $serviceCode)
			{
				$company = $this->companies[$debt['company_id']];
			}
		}

		return $company;
	}

	public function getPayerAddress($payerId)
	{
		return $this->addresses[$payerId]; 
	}

	public function selectDebts($payerId, $serviceCode = '')
	{
		$result = array();

		foreach ($this->debts as $debt)
		{
			$service = $this->services[$debt['service_id']];
			if ($serviceCode && $serviceCode != $debt['service_id'])
			{
				continue;
			}

			if ($debt['payer_id'] == $payerId)
			{
				$debt['charge']        = 0.0;
				$debt['last_paying']   = 0.0;
				$debt['service_name']  = $service['name'];
				$debt['service_price'] = $service['price'];

				$debt['year']  = date('Y');
				$debt['month'] = date('m');

				$result[] = $debt;
			}
		}

		return $result;
	}

	public function generateCheckRef()
	{
		$min = 10000000;
		$max = 99999999;

		return mt_rand($min, $max);
	}

	public function insertPayment($payerNum, $sum) { return $this->generateCheckRef(); }

	public function confirmPayment($ref) { return true; }

	public function cancelPayment($ref) { return true; }
}

?>
