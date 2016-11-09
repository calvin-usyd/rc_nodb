<?php
//Grab all the files in the directory and display in json format.
class posters extends common{
	function posterDetail(){
		$dataObj = json_decode(file_get_contents('php://input'), true);
		
		$DBArray = json_decode(file_get_contents('../d_student.json'), true);
		//var_dump(basename($dataObj['pdfUrl']));
		//var_dump($DBArray);
		$newArray = array();
		$pdfUrl = explode('?', $dataObj['pdfUrl'])[0];
		
		foreach($DBArray as $dbObj){
			
			if ( 
				isset($dbObj[0]['poster']) &&  basename($dbObj[0]['poster']) == basename($pdfUrl) || 
				isset($dbObj['poster']) &&  basename($dbObj['poster']) == basename($pdfUrl)){
				$newArray[] = array('email'=>$dbObj['email'], 'project'=>$dbObj['project'], 'category'=>$dbObj['category'], 'supervisor'=>$dbObj['supervisor'], 'student1'=>$dbObj['student1'], 'student2'=>$dbObj['student2'], 'student3'=>$dbObj['student3']);
				break;
			}
		}
		echo json_encode($newArray);
	}
	
	function search(){
		$newArray = array();
		
		$dataObj = json_decode(file_get_contents('php://input'), true);
		
		if (($dataObj['key']) == ''){
			$newArray = array("code"=>"fail", "msg"=>"Empty keyword!!");
			echo json_encode($newArray);
			die();
		}
		
		$DBArray = json_decode(file_get_contents('../d_student.json'), true);
		//var_dump(basename($dataObj['pdfUrl']));
		//var_dump($DBArray);
		
		$x=1;
		$millis = round(microtime(true) * 1000);
		foreach($DBArray as $dbObj){
			if ($dbObj['type'] != str_replace(date('Y'), '', $dataObj['type'])){
				continue;
			}
			
			if ( isset($dbObj['poster']) && (
				stripos($dbObj['student1'], $dataObj['key']) !== false || 
				stripos($dbObj['student2'], $dataObj['key']) !== false || 
				stripos($dbObj['student3'], $dataObj['key']) !== false || 
				stripos($dbObj['project'], $dataObj['key']) !== false || 
				stripos($dbObj['supervisor'], $dataObj['key']) !== false || 
				stripos($dbObj['poster'], $dataObj['key']) !== false
				)){
				$imageUrl = str_replace('/pdf/', '/jpg/', $dbObj['poster']);
				$imageUrl = preg_replace('"\.pdf$"', '.jpg', $imageUrl);
				
				$newArray[] = array(
					'imageId'=>'s'.$x, 
					'pdfUrl'=>'../'.$dbObj['poster'] . '?' . $millis, 
					'imageUrl'=>'../'.$imageUrl . '?' . $millis, 
					'student1'=>$dbObj['student1'], 
					'student2'=>$dbObj['student2'], 
					'student3'=>$dbObj['student3'], 
					'imageName'=>str_replace( '_', ' ', basename($dbObj['poster']))
				);
				
				$x++;
			}
		}
		echo json_encode($newArray);
	}
	
	function populateVotes($graduateType){
		$voteDBArray = json_decode(file_get_contents(date('Y').'_d_result_'.$graduateType.'.json'), true);
		
		session_start();
		//echo $_SESSION['email'] . ']';
		if ($_SESSION['email'] == ''){
			echo json_encode(array('code'=>'failed', 'message'=>'Session expired! Please login again.', ));
			die();
		}
		foreach($voteDBArray as $voteDBObj){
			if ($voteDBObj['email'] == $_SESSION['email'] || $voteDBObj[0]['email'] == $_SESSION['email']){
				echo json_encode($voteDBObj);
				break;
			}
		}
	}
	
	function loadPanel(){
		$this->result = array("code"=>"success", "msg"=>"Access granted.");
		
		if (!$this->validAuth()){
			$this->result = array("code"=>"fail", "msg"=>"Access denied!!");
		}
		
		echo json_encode($this->result);
		
		die();
	}
	
	/*function loadImageUG(){
		$this->process('Undergraduate');
	}

	function loadImagePGCE(){
		$this->process('PostgraduateCE');
	}

	function loadImagePGPM(){
		$this->process('PostgraduatePM');
	}*/

	function loadImages2014(){
		
		$dir = "../images/posters_2014/jpg/";
		
		$this->loadImageFromDir($dir);
	}
	
	function loadImagesYearType($year, $graduateType){
		
		$dir = "../images/posters_".$year."/".$graduateType."/jpg/";
		
		$this->loadImageFromDir($dir);
	}
	
	function loadImages($graduateType){
		
		/*if (!$this->validAuth()){
			$this->result = array("code"=>"fail", "msg"=>"Access denied!!");
			echo json_encode($this->result);
			//header('Location: ');
			die();
		}*/
		
		$dir = "../images/posters_".date('Y')."/". $graduateType ."/jpg/";
//echo $dir;
		$this->loadImageFromDir($dir);
	}

	private function loadImageFromDir($dir){
		
		// Open a directory, and read its contents
		if (is_dir($dir)){
		  if ($dh = opendir($dir)){
			//$jsonStartStr = '{"posters":[';
			$jsonStartStr = '[';
			$jsonStr = '';
			$x = 1;
			$millis = round(microtime(true) * 1000);
			
			while (($file = readdir($dh)) !== false){
			  if (!in_array($file,array(".",".."))) {
				
				$info = pathinfo( $file );
				/*try{
					$info['extension'];
				}catch(Exception $ex){continue;}
				*/
				if ($info['extension'] == 'jpg'){
					$name1 = $info['filename'];
					$name = str_replace( '_', ' ', $name1);
					$name = str_replace( '-', ' ', $name1);
					
					$jsonStr .= '{"imageId":"' . $x . '"';
					$jsonStr .= ',"pdfUrl":"' . str_replace('/jpg/', '/pdf/', $dir) . str_replace('.jpg', '.pdf', $file) . '?' . $millis . '"';
					$jsonStr .= ',"imageUrl":"' . $dir . $file . '?' . $millis . '"';
					$jsonStr .= ',"student1":"' . explode('_', $name1)[0] . '"';
					$jsonStr .= ',"student2":"' . explode('_', $name1)[1] . '"';
					$jsonStr .= ',"student3":"' . explode('_', $name1)[2] . '"';
					$jsonStr .= ',"type":"' . $_SESSION['type'] . '"';
					$jsonStr .= ',"imageName":"' . str_replace( '_', ' ', $name) . '"},';
					$x++;
				}
			  }
			}
			
			if ($this->endsWith($jsonStr, ',') === true){
				$jsonStr = rtrim($jsonStr, ",");
			}
			
			echo $jsonStartStr . $jsonStr . ']';
			
			closedir($dh);
		  }
		}else{
			echo "Directory not found!";
		}
	}
	function startsWith($haystack, $needle)
	{
		return $needle === "" || strpos($haystack, $needle) === 0;
	}
	function endsWith($haystack, $needle)
	{
		return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}
}
?>