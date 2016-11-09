<?php
class Report extends common{

	function analyse(){
		$undergraduateCEArray = $this->getReportResult('UndergraduateCE', 1);
		$postgraduateCEArray = $this->getReportResult('PostgraduateCE', 0);
		$undergraduatePMArray = $this->getReportResult('UndergraduatePM', 1);
		$postgraduatePMArray = $this->getReportResult('PostgraduatePM', 0);
		
		//echo json_encode($newArray3);
		echo json_encode(array(
			'UndergraduateCE'=>$undergraduateCEArray,
			'PostgraduateCE'=>$postgraduateCEArray,
			'UndergraduatePM'=>$undergraduatePMArray,
			'PostgraduatePM'=>$postgraduatePMArray
		));
	}
	function getAllPosterTitle(){
		$file = "../d_student.json";

		$objArray = json_decode(file_get_contents($file), true);

		if (!is_array($objArray)){
			$result = array('code'=>'fail', 'msg'=>'Empty records!');
			echo json_encode($result);
			die();
		}
		
		$newArray = array();
		
		foreach($objArray as $obj){
			if ($obj['project'] != null ){
				$newArray[] = $obj['project'];
			}
		}
		
		echo json_encode($newArray);
	}
	
	function posterObj($url, $point){
		return array(
			"name"=>explode('?', basename($url))[0], 
			"point"=>$point
		);
	}
	
	function getReportResult($graduateType, $nominateMoreThanCount){
		$file = "../panel/".date('Y')."_d_result_".$graduateType.".json";

		if (!file_exists($file)){
			return;
		}
		
		$objArray = json_decode(file_get_contents($file), true);

		if (!is_array($objArray)){
			$result = array('code'=>'fail', 'msg'=>'Empty records!');
			echo json_encode($result);
			die();
		}
		
		$newArray = array();
		
		foreach($objArray as $obj){
			//$point = ($graduateType == 'UndergraduateCE') ? 5 : 3;
			$point = 3;
			if ($obj['vote1']){
				//array_push($newArray, $obj['vote1']);
				$newArray[] = $this->posterObj($obj['vote1'], $point--);
			}
			if ($obj['vote2']){
				//array_push($newArray, $obj['vote2']);
				$newArray[] = $this->posterObj($obj['vote2'], $point--);
			}
			if ($obj['vote3']){
				//array_push($newArray, $obj['vote3']);
				$newArray[] = $this->posterObj($obj['vote3'], $point--);
			}
			if ($obj['vote4']){
				//array_push($newArray, $obj['vote2']);
				$newArray[] = $this->posterObj($obj['vote4'], $point--);
			}
			if ($obj['vote5']){
				//array_push($newArray, $obj['vote3']);
				$newArray[] = $this->posterObj($obj['vote5'], $point--);
			}
		}
		
		$newArray2 = array();
		
		foreach($newArray as $obj){
			//$obj = explode('?', $obj);
			//$obj = $obj[0];
			//var_dump( $obj);
			if ($obj == '' || $obj == null || !isset($obj) || !$obj){
				//echo $obj;
				continue;
			}
			
			if (isset($newArray2[$obj['name']])){
				$newArray2[$obj['name']] = $newArray2[$obj['name']] + $obj['point'];
			}else{
				$newArray2[$obj['name']] = $obj['point'];
				//$newArray2[] = array('img'=>$obj, 'total'=>1);
			}
		}
		
		$newArray3 = array();
		$newArrayUndergraduate = array();
		$newArrayPostgraduate = array();
		
		foreach($newArray2 as $obj => $key){
			
			if ($obj == '' || $obj == null || !isset($obj) || !$obj){
				//echo $obj;
				continue;
				
			}elseif ($key > $nominateMoreThanCount){			
				$studentArry = $this->getStudents($obj);
				
				//$newArray3 = array('project'=>$studentArry['project'],'student1'=>$studentArry['student1'],'student2'=>$studentArry['student2'],'student3'=>$studentArry['student3'],'total'=>$key);
				$newArray3[] = array(
					'project'=>$studentArry['project'],
					'student1'=>$studentArry['student1'],
					'student2'=>$studentArry['student2'],
					'student3'=>$studentArry['student3'],
					'pdf'=>$studentArry['pdf'],
					'jpg'=>$obj,
					'total'=>$key
				);
				
				/*if ($studentArry['type'] == 'Undergraduate'){
					$newArrayUndergraduate[] = $newArray3;
					
				}else{
					$newArrayPostgraduate[] = $newArray3;
				}*/
			}
		}
		
		//krsort($newArrayUndergraduate);
		//krsort($newArrayUndergraduate);
		krsort($newArray3);
		
		/*echo json_encode(array(
			'Undergraduate'=>$newArrayUndergraduate,
			'Postgraduate'=>$newArrayPostgraduate
			)
		);*/
		return $newArray3;
		//echo json_encode($newArray3);
	}
}
?>