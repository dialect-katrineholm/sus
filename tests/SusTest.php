<?php
use Tests\TestCase;
use Carbon\Carbon;
use \Dialect\Sus\Sus;
class SusTest extends TestCase
{

	public function setUp(){
		Parent::setUp();

	}


	/** @test */
	public function can_generate_opening_post(){
		$contract_number = "124876";
		$feedback_contract_number = "33455";
		$date = Carbon::now()->format("Ymd");
		$org_nr = "7946395648";

		$sus = new Sus($contract_number, $org_nr, $feedback_contract_number, $date);
		$data = $sus->raw();
		$this->assertContains($contract_number, $data);
		$this->assertContains($feedback_contract_number, $data);
		$this->assertContains($date, $data);
		$this->assertContains($org_nr, $data);


	}

	/** @test */
	public function can_add_payments(){
		$contract_number = "123456";
		$feedback_contract_number = "54321";
		$date = Carbon::now()->format("Ymd");
		$org_nr = "0123456789";
		$sus = new Sus($contract_number, $org_nr, $feedback_contract_number, $date);
		$sus = $sus->addPersonNrPayment(12345, 199105262019, "Markus Stromgren", "TESTCOMP", 12200);
		$data = $sus->raw();
		$this->assertContains("199105262019", $data);
		$this->assertContains("12345", $data);
		$this->assertContains("Markus Stromgren", $data);
		$this->assertContains("TESTCOMP", $data);
		$this->assertContains("12200",$data);
	}

	/** @test */
	public function can_add_notification_address(){
		$contract_number = "123456";
		$feedback_contract_number = "54321";
		$date = Carbon::now()->format("Ymd");
		$org_nr = "0123456789";
		$sus = new Sus($contract_number, $org_nr, $feedback_contract_number, $date);
		$sus = $sus->addPersonNrPayment(12345, 199105262019, "Markus Stromgren", "TESTCOMP", 12200)
		           ->addNotificationAddress(12345, "Torpvagen 12", "Katrineholm", "64134");

		$data = $sus->raw();
		$this->assertContains("Torpvagen 12", $data);
		$this->assertContains("Katrineholm", $data);
		$this->assertContains("64134", $data);


	}
	/** @test */
	public function can_generate_closing_post(){
		$contract_number = "123456";
		$feedback_contract_number = "54321";
		$date = Carbon::now()->format("Ymd");
		$org_nr = "0123456789";

		$sus = new Sus($contract_number, $org_nr, $feedback_contract_number, $date);
		$sus = $sus->addPersonNrPayment(12345, 199105262019, "Markus Stromgren", "TESTCOMP", 22222)
					->addPersonNrPayment(12345, 199105262019, "Markus Stromgren", "TESTCOMP", 22222);

		$data = $sus->raw();
		$this->assertContains("44444", $data);


	}





}