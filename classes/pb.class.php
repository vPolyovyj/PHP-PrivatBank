<?php

/** 
* @desc base class (interface), which describes a set of functions required for payment through the PrivatBank Debt API
*
*/
interface pb
{
	/**
	* @desc Select payer from billing DB by personal number
	* @param string $num payer's personal number
	* @return array $result 
	* array must have the next structure:
	*	array('name' => '...', 'num' => '...', 'phone' => '...', address => '...')
	*/
	public function getPayerByNum($num);

	/**
	* @desc Select payers from billing DB by similarity to personal number
	* @param string $num payer's personal number
	* @return array $result
	* array must have the next structure:
	*	array(0 =>
	*		array('name' => '...', 'num' => '...', 'phone' => '...', address => '...'),
	*		...
	*	)
	*/
	public function selectPayersByNum($num);

	/**
	* @desc Select payers from billing DB by his address
	* @param array $adderss payer's address: array('street' => '...', 'house' =>  '...[/...]', 'flat' => '...')
	* @return array $result
	* array must have the next structure:
	*	array(0 =>
	*		array('name' => '...', 'num' => '...', 'phone' => '...', address => '...'),
	*		...
	*	)
	*/
	public function selectPayersByAddr($queryAddr);

	/**
	* @desc Select company from billing DB by service code (The payer must know which company he pays and what service)
	* @param int $serviceCode service's unique id
	* @return array $company 
	* array must have the next structure:
	*	array('name' => '...', 'okpo' => '...', 'mfo' => '...', account => '...')
	*/
	public function getCompanyByService($serviceCode);

	/**
	* @desc Select payer's address from billing DB by payer id
	* @param int $payerId payer's unique id
	* @return array $address 
	* array must have the next structure:
	*	array('name' => '...')
	*/
	public function getPayerAddress($payerId);

	/**
	* @desc select payer's debt from billing DB
	* @param int $payerId payer's unique id
	* @param int $serviceCode service's unique id
	* @return array $result 
	* array must have the next structure:
	* array(0 =>
	*	array('service_id' => '...', 'payer_id' => '...', 'sum' => '...', 'company_id' => '...',
	*		  'service_name' => '...', 'service_price' => '...'),
	*	...
	* )
	*/
	public function selectDebts($payerId, $serviceCode = '');

	/**
	* @desc generate unique reference for pay checking
	* @return string $refrence 
	*
	*/
	function generateCheckRef();

	/**
	* @desc insert payment to db
	* @param int $payerNum
	* @param double $sum
	* @return bool $res 
	*
	*/
	function insertPayment($payerNum, $sum);

	/**
	* @desc confirm payment after insert
	* @param string $ref
	* @return bool $res 
	*
	*/
	function confirmPayment($ref);

	/**
	* @desc cancel payment by bank request
	* @param string $ref
	* @return bool $res 
	*
	*/
	function cancelPayment($ref);
}

?>