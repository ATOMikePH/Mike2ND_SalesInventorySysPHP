<?php
require_once '../config.php';
class Login extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function index(){
		echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
	}
	public function login(){
		extract($_POST);

		$qry = $this->conn->query("SELECT * from users where username = '$username' and password = md5('$password')");
		if($qry->num_rows > 0){
			foreach($qry->fetch_array() as $k => $v){
				if(!is_numeric($k) && $k != 'password'){
					$this->settings->set_userdata($k,$v);
				}

			}
			$userId = $this->settings->userdata('id');
			$this->settings->set_userdata('login_type',1);
			$this->updateLastLogin($userId);
			$this->conn->query("UPDATE `users` SET `session_status` = '1' WHERE `id` = '$userId'");
// Add log to system_log
$user_id = $userId;
$user_name = $username;
$system = ""; // Set your system name here
$action_description = "succesfully Logged In the System."; // Or whatever description you want
$type = 1; // Set the type according to your log type

$this->conn->query("INSERT INTO `system_log` (`user_id`, `user_name`, `system`, `action_description`, `type`, `datetime`) VALUES ('$user_id', '$user_name', '$system', '$action_description', '$type', NOW())");

echo json_encode(array('status'=>'success', 'message'=>'Login Successful!'));
}else{
return json_encode(array('status'=>'incorrect','last_qry'=>"SELECT * from users where username = '$username' and password = md5('$password') "));
}
}


	public function logout() {
		$userId = $this->settings->userdata('id');
	
		// Update the last login time
		$this->updateLastLogin($userId);
		$this->conn->query("UPDATE `users` SET `session_status` = '0' WHERE `id` = '$userId'");
		
		// Unset all of the session variables
		$_SESSION = array();

		// Add log to system_log
$user_id = $userId;
$user_name = "";
$system = ""; // Set your system name here
$action_description = "succesfully Logged Out the System."; // Or whatever description you want
$type = 2; // Set the type according to your log type

$this->conn->query("INSERT INTO `system_log` (`user_id`, `user_name`, `system`, `action_description`, `type`, `datetime`) VALUES ('$user_id', '$user_name', '$system', '$action_description', '$type', NOW())");		

		// Destroy the session
		session_destroy();
	
		// Return a JSON response instead of redirecting
		echo json_encode(array('status' => 'success', 'redirect' => base_url . 'admin/login.php'));
	}
	public function updateLastLogin($userId) {
		$lastLogin = date('Y-m-d H:i:s'); // Current date and time
		$this->conn->query("UPDATE `users` SET `last_login` = '$lastLogin' WHERE `id` = '$userId'");
	}

	function login_user(){
		extract($_POST);
		$qry = $this->conn->query("SELECT * from users where username = '$username' and password = md5('$password') and `type` = 2 ");
		if($qry->num_rows > 0){
			foreach($qry->fetch_array() as $k => $v){
				$this->settings->set_userdata($k,$v);
			}
			$this->settings->set_userdata('login_type',2);
		$resp['status'] = 'success';
		}else{
		$resp['status'] = 'incorrect';
		}
		if($this->conn->error){
			$resp['status'] = 'failed';
			$resp['_error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	public function logout_user(){
		if($this->settings->sess_des()){
			redirect('./');
		}
	}
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
	case 'login':
		echo $auth->login();
		break;
	case 'login_user':
		echo $auth->login_user();
		break;
	case 'logout':
		echo $auth->logout();
		break;
	case 'logout_user':
		echo $auth->logout_user();
		break;
	default:
		echo $auth->index();
		break;
}

