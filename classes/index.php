<?php
class StudentIndex extends common{

	public function restrictFileType(){
		$allowed = array('application/pdf');
		
		if(!in_array($_FILES['uploadedfile']['type'], $allowed)){
			echo "Invalid file type! Only PDf is allowed. Please click back and upload again.";
			die();
		}
	}
	
	public function upload(){
		//check file type;
		$this->restrictFileType();
		
		//Delete file
		$this->deleteFile();
		
		// Where the file is going to be placed 
		$year = date("Y");
		$pdf_path = "images/posters_".$year."/".$_SESSION['type']."/pdf/";
		$jpg_path = "images/posters_".$year."/".$_SESSION['type']."/jpg/";
		//$qthumbApi = 'http://quantumfi.com.au/api/thumbifi/process.php?rel=50&pdf=';
		$qthumbApi = 'http://quantumfi.net/api/thumbifi/process.php?rel=50&pdf=';
		if (!file_exists($pdf_path)) {
			mkdir($pdf_path, 0777, true);
		}
		if (!file_exists($jpg_path)) {
			mkdir($jpg_path, 0777, true);
		}
		
		/* Add the original filename to our target path.  
		Result is "uploads/filename.extension" */
		$fn_pdf = str_replace(' ', '_', basename( $_FILES['uploadedfile']['name']));
		$fnTmp_pdf = $_FILES['uploadedfile']['tmp_name'];
		$target_path_pdf = $pdf_path . $fn_pdf; 

		$fn_jpg = str_replace('.pdf', '.jpg', $fn_pdf);
		
		if(move_uploaded_file($fnTmp_pdf, $target_path_pdf))
		{
			chmod($target_path_pdf, 0755);
			
			//$servUrl = "http://" . $_SERVER['HTTP_HOST'] . '/rc/' ;
			$servUrl = "http://" . $_SERVER['HTTP_HOST'] . '/' . basename(dirname($_SERVER['PHP_SELF'])) . '/';
			
			$img = $jpg_path . $fn_jpg;
			
			file_put_contents($img, file_get_contents($qthumbApi . $servUrl . $target_path_pdf));
			
			chmod($img, 0755);
			
			$this->bindFnUser($target_path_pdf);
			
			header('Location: '.$servUrl.'upload-success.html');
			
			//die();
		} else{
			echo "There was an error uploading the file, please try again!";
		}

	}
		
	private function checkAndStrtSess(){
		if (session_status() == PHP_SESSION_NONE || session_id() == '') {
			session_start();
		}
	}
	
	private function deleteFile(){
		$this->checkAndStrtSess();
		
		$email = $_SESSION['email'];
		
		$objArray = json_decode(file_get_contents('d_student.json'), true);

		if (!is_array($objArray)){
			$objArray = array();
		}
		
		$newArray = array();
		
		foreach ($objArray as &$Obj) 
		{	
			if ($email == $Obj['email']){
			//delete PDF
				$pdf = $Obj['poster'];
				if (file_exists($pdf)){
					if (!unlink($pdf))
					  {
						//echo ("Error deleting $pdf");
					  }
						else
					  {
						//echo ("Deleted $pdf");
					  }
				}
				//delete JPG
				$jpgFile = str_replace('pdf', 'jpg', $Obj['poster']);
				
				if (file_exists($jpgFile)){
					if (!unlink($jpgFile))
					  {
						//echo ("Error deleting $jpgFile");
					  }
						else
					  {
						//echo ("Deleted $jpgFile");
					  }
				}
			}
		}
		//delete existing poster here
	}
	
	private function bindFnUser($fn){
		$this->checkAndStrtSess();
		
		$email = $_SESSION['email'];
		
		$objArray = json_decode(file_get_contents('d_student.json'), true);

		if (!is_array($objArray)){
			$objArray = array();
		}
		
		$newArray = array();
		
		foreach ($objArray as &$Obj) 
		{	
			if ($email == $Obj['email']){

				$user = array(
					"email"=>$Obj['email'], 
					"pass"=>$Obj['pass'],
					"servUrl"=>$Obj['servUrl'],
					"type"=>$Obj['type'],
					"category"=>$_POST['category'], 
					"student1"=>$_POST['student1'], 
					"student2"=>$_POST['student2'], 
					"student3"=>$_POST['student3'], 
					"project"=>$_POST['projectN'], 
					"supervisor"=>$_POST['supervisorN'], 
					"poster"=>$fn);
				//json_encode($objArray);
				array_push($newArray, $user);
			}else{
				array_push($newArray, $Obj);
			}
		}
		file_put_contents('d_student.json', json_encode($newArray));
	}
	
	public function jsonDetail(){
		$this->result = array("code"=>"fail", "msg"=>"Invalid login credential!");

		if (!$this->validAuth()){
			$this->result = array("code"=>"fail", "msg"=>"Access denied!!");
			echo json_encode($this->result);
			die();
		}

		$this->objArray = json_decode(file_get_contents('d_student.json'), true);

		if (!is_array($this->objArray)){
			$this->objArray = array();
		}
		//$this->objData['email'] = 'cal_wc85@yahoo.com';
		foreach ($this->objArray as &$Obj) 
		{
			if ($_SESSION['email'] == $Obj['email']){
				$Obj['pass'] = "******";//masked sensitive data
				$this->result = $Obj;
				break;
			}
		}
		echo json_encode($this->result);
	}
}
?>