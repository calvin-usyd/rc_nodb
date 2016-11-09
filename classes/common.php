<?php
class common{

	protected function validAuth(){
		$servUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/';
		//echo $servUrl ;
		session_start();
		//return (isset($_SESSION['email']) && isset($_SESSION['serv']) && $_SESSION['serv'] == $servUrl);
		return (isset($_SESSION['email']) && isset($_SESSION['serv']));
	}
	
	protected function getStudents($jpgFn){
		$pdfFn = str_replace('.jpg', '.pdf', $jpgFn);
		$pdfFn = explode('?', $pdfFn);
		$pdfFn = $pdfFn[0];
		
		$DBArray = json_decode(file_get_contents('../d_student.json'), true);
		//var_dump(basename($dataObj['pdfUrl']));
		//var_dump($DBArray);
		$newArray = array('project'=>'-', 'student1'=>'-', 'student2'=>'-', 'student3'=>'-');
		
		foreach($DBArray as $dbObj){
			
			if (
				isset($dbObj[0]['poster']) &&  basename($dbObj[0]['poster']) == basename($pdfFn) || 
				isset($dbObj['poster']) &&  basename($dbObj['poster']) == basename($pdfFn)){
				//set to another array to hide password
				$newArray = array(
					'project'=>$dbObj['project'], 
					'student1'=>$dbObj['student1'], 
					'student2'=>$dbObj['student2'], 
					'student3'=>$dbObj['student3'], 
					'type'=>$dbObj['type'], 
					'pdf'=>$dbObj['poster']
				);
				
				break;
			}
		}
		return $newArray;
	}
}
?>