<?php
class login{

	function process($userTypeFile){
		$this->result = array("code"=>"fail", "msg"=>"Invalid login credential!");

		$this->objData = json_decode(file_get_contents("php://input"), true);
		
		$this->objArray = json_decode(file_get_contents($userTypeFile . '.json'), true);
		
		if (!is_array($this->objArray)){
			$this->objArray = array();
		}
		
		foreach ($this->objArray as &$Obj) 
		{	
			if (isset($this->objData['email']) && $this->objData['email'] == $Obj['email']){
				if ($this->objData['pass'] == $Obj['pass']){
				 session_start(); 
				 $_SESSION['email'] = $Obj['email'];
				 $_SESSION['serv'] = $Obj['servUrl'];
				 $_SESSION['name'] = $Obj['name'];
				 $_SESSION['type'] = $Obj['type'];
				 
				 $this->result = array("code"=>"success", "servUrl"=>$Obj['servUrl']);
				 
				 break;
				}
			}
		}
		echo json_encode($this->result);
	}
	
	function clearSession(){
		session_start(); 
		unset($_SESSION['email']);
		$url  = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/';
		header('Location: '. $url . 'login.html');
	}
}
?>