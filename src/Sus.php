<?php
namespace Dialect\Sus;

use Dialect\Sus\Exceptions\InvalidLengthException;

class Sus {
	protected $contractNumber;
	protected $organizationNumber;
	protected $date;
	protected $feedbackContractNumber;
	protected $payments = [];
	protected $notificationAddresses = [];


	public function __construct($contractNumber, $organizationNumber, $feedbackContractNumber, $date= null) {

		$this->contractNumber = $contractNumber;
		$this->feedbackContractNumber = $feedbackContractNumber;
		$this->organizationNumber = $organizationNumber;
		$this->date = $date ? Date("Ymd", strtotime($date)) : Date("Ymd", time());

	}

	public function addPersonNrPayment($id, $customerNo, $customerName, $infoText, $amount,  $paymentType = "08"){

		$this->payments[] = [
			"id" => $id,
			"customerno" => $customerNo,
			"customername" => $customerName,
			"infotext" => $infoText,
			"clearingno" => "",
			"accountno" => "",
			"accounttype" => "",
			"amount" => $amount,
			"paymenttype" => $paymentType
		];
		return $this;
	}

	public function addAccountPayment($id, $customerNo, $customerName, $clearingNo, $accountNo, $accountType, $infoText, $amount,  $paymentType = "08"){
		$this->payments[] = [
			"id" => $id,
			"customerno" => $customerNo,
			"customername" => $customerName,
			"infotext" => $infoText,
			"clearingno" => $clearingNo,
			"accountno" => $accountNo,
			"accounttype" => $accountType,
			"amount" => $amount,
			"paymenttype" => $paymentType
		];

		return $this;
	}

	public function addNotificationAddress($id,$address, $city, $postalCode){
		$this->notificationAddresses[] = [
			"id" => $id,
			"address" => $address,
			"city" => $city,
			"postalcode" => $postalCode
		];

		return $this;
	}





	public function raw(){
		$result = "";
		$result .= $this->getOpeningPost();
		$result .= $this->getPaymentPosts();
		$result .= $this->getNotificationAddressPosts();
		$result .= $this->getClosingPost();
		return $result;
	}

	public function download($filename = "file.sus"){
		return new Response($this->raw(), 200, array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' =>  'attachment; filename="'.$filename.'"'
		));
	}

	private function getOpeningPost(){
		$openingPost = $this->fillN("05", 2);
		$openingPost .= $this->fillN($this->contractNumber, 6);
		$openingPost .= $this->fillAN("", 50);
		$openingPost .= $this->fillN($this->date, 8);
		$openingPost .= $this->fillN($this->feedbackContractNumber, 5);
		$openingPost .= $this->fillN($this->organizationNumber, 10);
		$openingPost .= $this->fillAN("", 99);
		return $openingPost."\n";

	}

	private function getClosingPost(){
		$totalPayed = 0;

		foreach($this->payments as $payment){
			$totalPayed += $payment["amount"];
		}

		$res = "";
		$res .= $this->fillN("80", 2);
		$res .= $this->fillN($this->contractNumber, 6);
		$res .= $this->fillAN("", 50);
		$res .= $this->fillN($totalPayed, 15);
		$res .= $this->fillN("", 15);
		$res .= $this->fillN("", 15);
		$res .= $this->fillN("", 15);
		$res .= $this->fillN("", 15);
		$res .= $this->fillN("", 15);
		$res .= $this->fillAN("", 32);

		return $res."\n";
	}

	private function getPaymentPosts(){
		$res = "";

		foreach($this->payments as $payment){
			$res .= $this->fillN("30", 2);
			$res .= $this->fillN($this->contractNumber, 6);
			$res .= $this->fillAN($payment["id"], 44);
			$res .= $this->fillN("", 6);
			$res .= $this->fillAN("", 3);
			$res .= $this->fillAN($payment["customerno"], 12);
			$res .= $this->fillAN($payment["customername"], 36);
			$res .= $this->fillAN($payment["infotext"], 10);
			$res .= $this->fillAN("", 1);
			$res .= $this->fillN($payment["paymenttype"], 2);
			$res .= $this->fillN($payment["clearingno"], 5);
			$res .= $this->fillN($payment["accountno"], 10);
			$res .= $this->fillAN($payment["accounttype"], 2);
			$res .= $this->fillN("", 2);
			$res .= $this->fillN($payment["amount"], 15);
			$res .= $this->fillN("", 15);
			$res .= $this->fillAN("", 6);
			$res .= $this->fillN("", 3);
			$res .="\n";
		}

		return $res;
	}

	private function getNotificationAddressPosts(){
		$res = "";
		foreach($this->notificationAddresses as $address){
			$res .= $this->fillN("36", 2);
			$res .= $this->fillN($this->contractNumber, 6);
			$res .= $this->fillAN($address["id"], 44);
			$res .= $this->fillAN("", 6);
			$res .= $this->fillAN($address["address"], 35);
			$res .= $this->fillAN("", 35);
			$res .= $this->fillAN($address["city"], 35);
			$res .= $this->fillN($address["postalcode"], 5);
			$res .= $this->fillAN("", 12);
			$res .="\n";
		}

		return $res;
	}



	private function fillN($str, $length){
		$len = strlen($str);
		if($len > $length){
			throw new InvalidLengthException('Invalid length for: "'.$str.'", Length: '.$len.' Expected: '.$length);
		}

		return str_repeat("0", $length - $len).$str;
	}

	private function fillAN($str, $length){
		$len = strlen($str);
		if($len > $length){
			throw new InvalidLengthException('Invalid length for: "'.$str.'", Length: '.$len.' Expected: '.$length);
		}

		return $str.str_repeat(" ", $length - $len);
	}
}