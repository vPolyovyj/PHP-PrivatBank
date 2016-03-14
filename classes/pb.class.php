<?php

/** 
* @desc базовий клас (інтерфейс), який описує множину функцій,
* які вимагаються для проведення платежів через PrivatBank Debt API
*/
interface pb
{
	/**
	* @desc пошук платника за особовим рахунком
	* @param string $num особовий рахунок платника
	* @return array $result дані платника
	* результуючий масив повнен мати таку структуру:
	*	array('name' => '...', 'num' => '...', 'phone' => '...', address => '...')
	*/
	public function getPayerByNum($num);

	/**
	* @desc пошук платників зі схожим особовим номером
	* @param string $num особовий номер
	* @return array $result перелік платників
	* результуючий масив повнен мати таку структуру:
	*	array(0 =>
	*		array('name' => '...', 'num' => '...', 'phone' => '...', address => '...'),
	*		...
	*	)
	*/
	public function selectPayersByNum($num);

	/**
	* @desc Пошук платників за адресою
	* @param array $adderss адреса: array('street' => '...', 'house' =>  '...[/...]', 'flat' => '...')
	* @return array $result перелік платників
	* результуючий масив повнен мати таку структуру:
	*	array(0 =>
	*		array('name' => '...', 'num' => '...', 'phone' => '...', address => '...'),
	*		...
	*	)
	*/
	public function selectPayersByAddr($queryAddr);

	/**
	* @desc пошук платника за ресурсом
	* @param string $num особовий рахунок платника
	* @return array $result дані платника
	* результуючий масив повнен мати таку структуру:
	*	array('name' => '...', 'num' => '...', 'phone' => '...', address => '...')
	*/
	public function getPayerByResource($resource);

	/**
	* @desc пошук платників зі схожим ресурсом
	* @param string $num особовий номер
	* @return array $result перелік платників
	* результуючий масив повнен мати таку структуру:
	*	array(0 =>
	*		array('name' => '...', 'num' => '...', 'phone' => '...', address => '...'),
	*		...
	*	)
	*/
	public function selectPayersByResource($resource);

	/**
	* @desc пошук компанії(провайдер) за кодом послуги
	* @param int $serviceCode id послуги
	* @return array $company дані компанії(провайдера)
	* результуючий масив повнен мати таку структуру:
	*	array('name' => '...', 'okpo' => '...', 'mfo' => '...', account => '...')
	*/
	public function getCompanyByService($serviceCode);

	/**
	* @desc пошук адреси платника за id
	* @param int $payerId id платника
	* @return array $address адреса платника
	* результуючий масив повнен мати таку структуру:
	*	array('name' => '...')
	*/
	public function getPayerAddress($payerId);

	/**
	* @desc інформація про стан розрахунків платника
	* @param int $payerId id платника 
	* @param int $serviceCode id послуги
	* @return array $result розрахунки платника
	* результуючий масив повнен мати таку структуру:
	* array(0 =>
	*	array('service_id' => '...', 'payer_id' => '...', 'sum' => '...', 'company_id' => '...',
	*		  'service_name' => '...', 'service_price' => '...'),
	*	...
	* )
	*/
	public function selectDebts($payerId, $serviceCode = '');

	/**
	* @desc генерування унікального ідентифікатора платежу (використовується у запитах XML)
	* @return int $refrence 
	*
	*/
	function generateCheckRef();

	/**
	* @desc вставка платежу в БД
	* @param int $payerNum
	* @param double $sum
	* @return bool $res 
	*
	*/
	function insertPayment($payerNum, $sum);

	/**
	* @desc підтвердження платежу
	* @param int $ref
	* @param int $payId
	* @return bool $res 
	*
	*/
	function confirmPayment($ref, $payId = '');

	/**
	* @desc скасування платежу відповідно до запиту банку
	* @param int $ref
	* @return bool $res 
	*
	*/
	function cancelPayment($ref);
}

?>
