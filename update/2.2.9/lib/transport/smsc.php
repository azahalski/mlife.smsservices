<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices\Transport;

class Smsc{
	
	private $config;
	
	//�����������, �������� ������ ������� � �����
	function __construct($params) {
		$this->config = $this->getConfig($params);
	}
	
	/**********************************************************
	*   ����� ��� ��������� ������ ������������
	***********************************************************
	*	error - { "error": "authorise error", "error_code": 2 }
	*	error_code=1 - ������ � ����������.
	*	error_code=2 - �������� ����� ��� ������.
	*	error_code=4 - IP-����� �������� ������������.
	*	error_code=9 - ������� �������� ����� ���� ���������� �������� �� ��������� ������ ��������� ���� ������������ � ������� ������.
	***********************************************************
	*	ok - [{"sender": "<sender>"},...]
	***********************************************************/
	public function _getAllSender() {
		
		$url = 'https://smsc.ru/sys/get.php?get_senders=1&login='.$this->config->login.'&psw='.$this->config->passw.'&fmt=3';
		$response = $this->openHttp($url);
		
		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		return json_decode($response);
	}
	
	/**
	* ����� ��� �������� ���
	* @param string     		$phones    ����� ��������
	* @param string     		$mess      ����� ���������
	* @param string     		$time      ����� � UNIXTIME, 0-�� ���������
	* @param boolean, string   $sender    false - ����� �� ��������, ���� ���������� �����������
	* @return array    		��������� �������
	***********************************************************
	*	error - { "error": "authorise error", "error_code": 2 }
	*	error_code=1 - ������ � ����������.
	*	error_code=2 - �������� ����� ��� ������.
	*	error_code=3 - ������������ ������� �� ����� �������.
	*	error_code=4 - IP-����� �������� ������������ ��-�� ������ ������ � ��������.
	*	error_code=5 - �������� ������ ����.
	*	error_code=6 - ��������� ��������� (�� ������ ��� �� ����� �����������).
	*	error_code=7 - �������� ������ ������ ��������.
	*	error_code=8 - ��������� �� ��������� ����� �� ����� ���� ����������.
	*	error_code=9 - �������� ����� ������ ����������� ������� �� �������� SMS-��������� ���� ����� ���� ���������� �������� �� ��������� ��������� ��������� � ������� ������.
	***********************************************************
	*	ok - {"id": <id>,"cnt": <n>, "cost": "<cost>", "balance": "<balance>"} - �� ���������, ���������� ���, ��������� ��������, ����� ������
	***********************************************************
	*/
	public function _sendSms ($phones, $mess, $time=0, $sender=false) {
		
		if($time!=0 && $time>time()) {
			$time = '0'.$time;
		}
		else{
			$time = 0;
		}
		
		$phones = urlencode($phones);
		$mess = urlencode($mess);
		$charset = $this->config->charset;
		
		if(!$sender) {
			$sender = $this->config->sender;
		}
		
		$pp = '327698';
		$url =  'https://smsc.ru/sys/send.php?login='.$this->config->login.'&psw='.$this->config->passw.'&phones='.$phones.'&mes='.$mess.
				'&fmt=3&charset='.$charset.'&time='.$time.'&sender='.$sender.'&cost=3&pp='.$pp;
		$response = $this->openHttp($url);
		
		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		return json_decode($response);
		
	}
	
	/**********************************************************
	*   ����� ��� ��������� �������
	***********************************************************
	*	error - { "error": "authorise error", "error_code": 2 }
	*	error_code=1 - ������ � ����������.
	*	error_code=2 - �������� ����� ��� ������.
	*	error_code=4 - IP-����� �������� ������������.
	*	error_code=9 - ������� �������� ����� ������ �������� �� ��������� ������� � ������� ������.
	***********************************************************
	*	ok - { "balance": "178.10" }
	***********************************************************/
	public function _getBalance () {
	
		$url = 'https://smsc.ru/sys/balance.php?login='.$this->config->login.'&psw='.$this->config->passw.'&fmt=3';
		$response = $this->openHttp($url);

		if(!$response){
			$data = new \stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		return json_decode($response);
		
	}
	
	public function _getStatusSms($smsid,$phone=false) {
		
		$url = 'https://smsc.ru/sys/status.php?login='.$this->config->login.'&psw='.$this->config->passw.'&phone='.$phone.'&id='.$smsid.'&fmt=3';
		$response = $this->openHttp($url);

		if(!$response){
			$data = new stdClass();
			$data->error = 'Service is not available';
			$data->error_code = '9998';
			return $data;
		}
		
		$data = json_decode($response);
		
		if($data->error_code) return $data;
		
		$res = new \stdClass();
		$res->last_timestamp = $data->last_timestamp;
		$res->status = $this->_checkStatus($data->status);
		return $res;
		
	}
	
	/**
	 * �������� ������ ������� � ����� �� �������� ������
	 */
	private function getConfig($params) {
		
		$c = new \stdClass();
		$c->login = $params['login'];
		$c->passw = $params['passw'];
		$c->sender = $params['sender'];
		$c->charset = $params['charset'];
		
		return $c;
		
	}
	
	/**
	* ����� ��� �������� ��������
	* @param string     $url    ���
	* @param boolean     $method    false - GET, true - POST
	* @param string     $params    ��������� ��� POST �������
	* @return string    ��������� �������
	*/
	private function openHttp($url, $method = false, $params = null) {
	
		if (!function_exists('curl_init')) {
		    die('ERROR: CURL library not found!');
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, $method);
		if ($method == true && isset($params)) {
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}
		curl_setopt($ch,  CURLOPT_HTTPHEADER, array(
		    'Content-Length: '.strlen($params),
		    'Cache-Control: no-store, no-cache, must-revalidate',
		    "Expires: " . date("r")
		));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
		
	}
	
	/*
	* �������� ���� �������� � 1 ���
	1 - ������� �������� � �����
	2 - �������� �� ����
	3 - �������� ���������
	4 - ����������
	5 - ����������
	6 - ������� �������� �� �����
	7 - ���������� ���������
	8 - �������� �����
	9 - ��������� �� �������
	10 - ������������ �������
	11 - ����������� �����
	12 - ������ ��� �������� ���
	*/
	private function _checkStatus($code) {
	
		if($code==-1) return 2;
		if($code==2) return 14;
		if($code==0) return 3;
		if($code==1) return 4;
		if($code==3) return 5;
		if($code==20) return 7;
		if($code==22) return 8;
		if($code==23) return 9;
		if($code==24) return 10;
		if($code==25) return 11;
		
	}
	
}
?>