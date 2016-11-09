<?php
class register{
	
	public function checkReg($userTypeFile){
		$this->userTypeFile = $userTypeFile . '.json';
		$this->servUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/'; 
		$this->registered = 0 ;
		$this->result = array("code"=>"fail", "msg"=>"Server error!");
		$this->objData = json_decode(file_get_contents("php://input"), true);
		$this->students = json_decode(file_get_contents($this->userTypeFile), true);
		
		//Return fail when email is empty
		if (!isset($this->objData['email'])){
			$this->result = array("code"=>"fail", "msg"=>"Empty email.");
			echo json_encode($this->result);
			return;
		}
		//Return fail when name is empty
		if (!isset($this->objData['regName'])){
			$this->result = array("code"=>"fail", "msg"=>"Empty name.");
			echo json_encode($this->result);
			return;
		}
		//Return fail when email is format is not USYD
		/*if ((strrpos($this->objData['email'], '@uni.sydney.edu.au') === false) && strrpos($userTypeFile, 'student') > -1){
			$this->result = array("code"=>"fail", "msg"=>"Please use Sydney University email address (xxxx@uni.sydney.edu.au).");
			echo json_encode($this->result);
			return;
		}
		if (
			(!preg_match('/sydney.edu.au/', $this->objData['email']) || 
			preg_match('/uni.sydney.edu.au/', $this->objData['email'])) &&
			strrpos($userTypeFile, 'voter') > -1){
			
				$this->result = array("code"=>"fail", "msg"=>"Please use Sydney University email address (xxxx@sydney.edu.au).");
			echo json_encode($this->result);
			return;
		}*/
		//Initialize data array for a new empty file
		if (!is_array($this->students)){
			$this->students = array();
		}
		
		foreach ($this->students as &$studentObj) 
		{
			if ($this->objData['email'] == $studentObj['email']){
				$this->result = array("code"=>"fail", "msg"=>"You have already registered! Please check your email inbox for your password.");
				
				$this->registered = 1;
				break;
			}
		}

		$this->ProcessUnreg( );
		
		echo json_encode($this->result);
	}

	private function ProcessUnreg(){
		
		if ( !$this->registered){
		 //try
		  //{
			$pass = substr(hash('sha512',rand()),0,12);
			
			$this->savePass($pass);
			$this->sendPassMail($pass);
			
			$this->result = array("code"=>"success", "msg"=>"Password has been sent to your email. Please click the login link above to login to your account");
			
		  //}catch(Exception $ex){$result = '{"code":"fail", "msg":"Password couldn\'t be saved."}'}
		}
	}

	private function savePass($pass)
	{
		$user = array("email"=>$this->objData['email'], "name"=>$this->objData['regName'], "pass"=>$pass, "type"=>$this->objData['type'], "servUrl"=>$this->servUrl);
		
		array_push($this->students, $user);
		
		file_put_contents($this->userTypeFile, json_encode($this->students));
	}

	private function sendPassMail($pass)
	{
		$qMAPI = 'http://quantumfi.net/api/sendmail/process.php';

		$to = $this->objData['email'];
		$frN = 'Sydney Poster Presentation team';
		$fr = 'no-reply@sydney.edu.au';
		$sbj = 'Password for Poster Presentation '.date("Y");
		$msg = 'Dear '.$this->objData['regName'].', <br/><br/>Please use the email and password below to login to Poster Presentation '.date("Y").' account:<br/><br/><b>'.$this->objData['email'].'<br/>'.$pass.'</b><br/><br/>This is an auto-generated email. Please don\'t reply to this email.<br/><br/>Sincerely,<br/>Poster Presentation team<br/><br/>';
		
		$cnt_array = array('fr'=>$fr, 'to'=>$to, 'subject'=>$sbj, 'message'=>$msg, 'frName'=>$frN);
		$cnt_json = json_encode($cnt_array);
		
		$this->post_json($qMAPI, $cnt_json);
	}

	private function post_json($url, $json_content)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json_content);

		$json_response = curl_exec($curl);

		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if ( $status != 201 ) {
			//die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
		}

		curl_close($curl);

		$response = json_decode($json_response, true);
	}
}
?>