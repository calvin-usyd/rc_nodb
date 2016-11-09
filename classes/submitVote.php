<?php
class submitVote extends common
{
	function process($graduateType){
		
		if (!$this->validAuth()){
			$this->result = array("code"=>"fail", "msg"=>"Access denied!!");
			echo json_encode($this->result);
			die();
		}
		
		$isDealine = false;//HARD CODED SETTING
		
		if ($isDealine){
			$this->result = array("code"=>"fail", "msg"=>"Nomination has met the deadline, you can\'t vote!");
			echo json_encode($this->result);
			die();
		}
		
		$file = date('Y')."_d_result_".$graduateType.".json";

		if (!file_exists($file)){
			$fh = fopen($file, 'w') or die("Can't create file");
		}
		
		$objData = json_decode(file_get_contents("php://input"), true);

		$voteDBArray = json_decode(file_get_contents($file), true);

		$duplicateEmail = 0;

		$voteArray = array();

		if (!is_array($voteDBArray)){
			$voteDBArray = array();
		}

		$objData['email'] = $_SESSION['email'];
		//session_start();
		
		foreach($voteDBArray as $voteDBObj){
			if ($voteDBObj['email'] == '' || !isset($voteDBObj['email'])){
				continue;
			}

			if ($voteDBObj['email'] == $objData['email']){
				$duplicateEmail = 1;
				//changeVote();
				
				array_push($voteArray, $objData);
				
			}else{
				array_push($voteArray, $voteDBObj);
			}
		}

		if ($duplicateEmail == 0){
			array_push($voteArray, $objData);
		}

		file_put_contents($file, json_encode($voteArray));

		//$this->sendMail($objData, $_SESSION['name']);
		$this->sendMail($objData, $_SESSION['name'], 'you', $objData['email'], $graduateType);
		$this->sendMail($objData, 'Admin', $_SESSION['name'], 'cal_wc85@yahoo.com', $graduateType);
		$this->sendMail($objData, 'Admin', $_SESSION['name'], 'fernando.alonso@sydney.edu.au', $graduateType);
		
		$this->result = array("code"=>"success", "msg"=>"You selections have been successfully saved.");
		
		echo json_encode($this->result);
		die();
	}
	
	/*private function sendMailCalvin($objData, $name)
	{
		$qMAPI = 'http://quantumfi.com.au/api/sendmail/process.php';
		
		$posrter1 = $this->getStudents($objData['vote1']);
		$posrter2 = $this->getStudents($objData['vote2']);
		$posrter3 = $this->getStudents($objData['vote3']);
		$posrter4 = $this->getStudents($objData['vote4']);
		$posrter5 = $this->getStudents($objData['vote5']);
		
		$to = 'cal_wc85@yahoo.com';
		$frN = 'Sydney Poster Presentation Team';
		$fr = 'no-reply@sydney.edu.au';
		$sbj = 'Poster selections from Poster Presentation '.date("Y");
		$msg = 'Dear Fernando, <br/><br/>'.$name.' ('.$objData['email'].') submitted the following selection:<br/><br/><b>'.
			'<br/><br/>Selection 1:<br/> '.$posrter1['student1']. '<br/>'.$posrter1['student2']. '<br/>'.$posrter1['student3']. '<br/>'.
			'<br/><br/>Selection 2:<br/> '.$posrter2['student1']. '<br/>'.$posrter2['student2']. '<br/>'.$posrter2['student3']. '<br/>'.
			'<br/><br/>Selection 3:<br/> '.$posrter3['student1']. '<br/>'.$posrter3['student2']. '<br/>'.$posrter3['student3']. '<br/>'.
			'<br/><br/>Selection 4:<br/> '.$posrter4['student1']. '<br/>'.$posrter4['student2']. '<br/>'.$posrter4['student3']. '<br/>'.
			'<br/><br/>Selection 5:<br/> '.$posrter5['student1']. '<br/>'.$posrter5['student2']. '<br/>'.$posrter5['student3']. '<br/>'.
			'</b><br/><br/>This is an auto-generated email. Please don\'t reply to this email.<br/><br/>Sincerely,<br/>Poster Presentation Team<br/><br/>';
		
		$cnt_array = array('fr'=>$fr, 'to'=>$to, 'subject'=>$sbj, 'message'=>$msg, 'frName'=>$frN);
		$cnt_json = json_encode($cnt_array);
		
		$this->post_json($qMAPI, $cnt_json);
	}*/
	
	//private function sendMailFernando($objData, $name)
	private function sendMail($objData, $receiver_name, $name, $emailTo, $graduateType)
	{
		$qMAPI = 'http://quantumfi.net/api/sendmail/process.php';
		
		$poster1 = $this->getStudents($objData['vote1']);
		$poster2 = $this->getStudents($objData['vote2']);
		$poster3 = $this->getStudents($objData['vote3']);
		$poster4 = $this->getStudents($objData['vote4']);
		$poster5 = $this->getStudents($objData['vote5']);
		
		$imgVote1 = $this->imgFullUrl($objData['vote1']);
		$imgVote2 = $this->imgFullUrl($objData['vote2']);
		$imgVote3 = $this->imgFullUrl($objData['vote3']);
		$imgVote4 = $this->imgFullUrl($objData['vote4']);
		$imgVote5 = $this->imgFullUrl($objData['vote5']);
		
		//$to = 'fernando.alonso@sydney.edu.au';
		$to = $emailTo;
		//$to = 'fernando@sydney.edu.au';
		//$to = 'cal_wc85@yahoo.com';
		$frN = 'Sydney Poster Presentation Team';
		$fr = 'no-reply@sydney.edu.au';
		$sbj = 'Poster selections from Poster Presentation '.date("Y").' category : ' . $graduateType;
		$msg = 'Dear '.$receiver_name.', <br/><br/>'.$name.' ('.$objData['email'].') submitted the following selection:<br/><br/><b>'.
			'<br/><br/>Selection 1:<br/> '.$poster1['student1']. '<br/>'.$poster1['student2']. '<br/>'.$poster1['student3']. '<br/>'. $imgVote1. '<br/>'.
			'<br/><br/>Selection 2:<br/> '.$poster2['student1']. '<br/>'.$poster2['student2']. '<br/>'.$poster2['student3']. '<br/>'. $imgVote2. '<br/>'.
			'<br/><br/>Selection 3:<br/> '.$poster3['student1']. '<br/>'.$poster3['student2']. '<br/>'.$poster3['student3']. '<br/>'. $imgVote3. '<br/>'.
			//'<br/><br/>Selection 4:<br/> '.$poster4['student1']. '<br/>'.$poster4['student2']. '<br/>'.$poster4['student3']. '<br/>'. $imgVote4. '<br/>'.
			//'<br/><br/>Selection 5:<br/> '.$poster5['student1']. '<br/>'.$poster5['student2']. '<br/>'.$poster5['student3']. '<br/>'. $imgVote5. '<br/>'.
			'</b><br/><br/>This is an auto-generated email. Please don\'t reply to this email.<br/><br/>Sincerely,<br/>Poster Presentation Team<br/><br/>';
		
		$cnt_array = array('fr'=>$fr, 'to'=>$to, 'subject'=>$sbj, 'message'=>$msg, 'frName'=>$frN);
		$cnt_json = json_encode($cnt_array);
		
		$this->post_json($qMAPI, $cnt_json);
	}
	
	/*private function sendMail($objData, $name)
	{
		$qMAPI = 'http://quantumfi.com.au/api/sendmail/process.php';
			
		$to = $objData['email'];
		$frN = 'Sydney Poster Presentation Team';
		$fr = 'no-reply@sydney.edu.au';
		$sbj = 'Poster selections from Poster Presentation '.date("Y");
		$msg = 'Dear '.$name.', <br/><br/>Your poster selection at Poster Presentation '.date("Y").':<br/><br/><b>'.
			'<br/>'.$this->imgFullUrl($objData['vote1']).
			'<br/>'.$this->imgFullUrl($objData['vote2']).
			'<br/>'.$this->imgFullUrl($objData['vote3']).
			'<br/>'.$this->imgFullUrl($objData['vote4']).
			'<br/>'.$this->imgFullUrl($objData['vote5']).
			'</b><br/><br/>This is an auto-generated email. Please don\'t reply to this email.<br/><br/>Sincerely,<br/>Poster Presentation Team<br/><br/>';
		
		$cnt_array = array('fr'=>$fr, 'to'=>$to, 'subject'=>$sbj, 'message'=>$msg, 'frName'=>$frN);
		$cnt_json = json_encode($cnt_array);
		
		$this->post_json($qMAPI, $cnt_json);
	}*/
	
	private function imgFullUrl($url){
		$imgUrl = "http://" . $_SERVER['HTTP_HOST'] . '/rc';
		
		return str_replace('..', $imgUrl, $url);
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