<?php

require_once('../config.php');

Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}

	function terminate_all() {
		global $conn;
	
		session_destroy();
	
		// Update session_status to 0 for all users
		$conn->query("UPDATE users SET session_status = 0");
	
		// Return success response
		echo json_encode(array('status' => 'success', 'redirect' => base_url . 'admin/login.php', 'message' => 'All sessions terminated successfully.'));
		exit;
	}

	function backup_system() {
		$response = array();
		
		try {
			require_once '../admin/backup_restore/BackupMyProject.php';
	
			// Check if the backup was successful
			if (isset($backup) && $backup instanceof BackupMyProject) {
				$response['status'] = 'success';
				$response['message'] = 'System backup completed successfully';
				$response['backup_file'] = $backup->backup_file;
			} else {
				$response['status'] = 'error';
				$response['message'] = 'An error occurred during system backup';
			}
		} catch (Exception $e) {
			$response['status'] = 'error';
			$response['message'] = 'An error occurred during system backup: ' . $e->getMessage();
		}
	
		echo json_encode($response);
		exit;
	}

	function backup_database() {
		$response = array();
		
		try {
			require_once '../admin/backup_restore/backupDatabase.php';
	
			if (isset($backup) && $backup instanceof Backup_Database) {
				$response['status'] = 'success';
				$response['message'] = 'Database backup completed successfully';
				$response['backup_file'] = $backup->getBackupFile();
			} else {
				$response['status'] = 'error';
				$response['message'] = 'An error occurred during database backup';
			}
		} catch (Exception $e) {
			$response['status'] = 'error';
			$response['message'] = 'An error occurred during database backup: ' . $e->getMessage();
		}
	
		echo json_encode($response);
		exit;
	}

	function restore_database()
	{
		$response = array();

		function restoreMysqlDB($filePath, $conn)
		{
			$sql = '';
			$error = '';
		
			if (file_exists($filePath)) {
				$lines = file($filePath);
		
				foreach ($lines as $line) {
		
	
					if (substr($line, 0, 2) == '--' || $line == '') {
						continue;
					}
		
					$sql .= $line;
		
					if (substr(trim($line), - 1, 1) == ';') {
						$result = mysqli_query($conn, $sql);
						if (! $result) {
							$error .= mysqli_error($conn) . "\n";
						}
						$sql = '';
					}
				}
		
				if ($error) {
					$response = array(
						"type" => "error",
						"message" => $error
					);
				} else {
					$response = array(
						"type" => "success",
						"message" => "Database Restore Completed Successfully."
					);
				}
			} // end if file exists
			return $response;
		}
		
	
		if (!empty($_FILES)) {
			$filePath = $_FILES["backup_file"]["tmp_name"];
			$conn = mysqli_connect("localhost", "root", "", "atom_sms");
	
			$response = restoreMysqlDB($filePath, $conn);

		} else {
			$response = array(
				"type" => "error",
				"message" => "No file selected for restoration."
			);
		}
	
		echo json_encode($response);
		exit();
	}
	

	function save_supplier(){
		extract($_POST);
	
		// Retrieve the active user_id from the session
		$user_id = $_SESSION['userdata']['id'];
	
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
	
		$check = $this->conn->query("SELECT * FROM `supplier_list` WHERE `name` = '{$name}' " . (!empty($id) ? " AND id != {$id} " : ""))->num_rows;
	
		if($this->capture_err())
			return $this->capture_err();
	
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Supplier Name already exists.";
			return json_encode($resp);
			exit;
		}
	
		if(empty($id)){
			$sql = "INSERT INTO `supplier_list` SET {$data} ";
			$save = $this->conn->query($sql);
	
			if($save){
				// Retrieve the id and name of the newly added supplier
				$id = $this->conn->insert_id;
				$result = $this->conn->query("SELECT `name` FROM `supplier_list` WHERE id = '{$id}'");
	
				if ($result && $result->num_rows > 0) {
					$row = $result->fetch_assoc();
					$name = $this->conn->real_escape_string($row['name']);
				}
			}
	
			$action_description = 'added';
		}else{
			$sql = "UPDATE `supplier_list` SET {$data} WHERE id = '{$id}' ";
			$save = $this->conn->query($sql);
			$action_description = 'modified';
		}
	
		if($save){
			// Log the activity in user_log
			$log_sql = "INSERT INTO `user_log` (user_id, supplier_name, action_description, type, datetime) VALUES ('$user_id', '$name', '$action_description', '2', NOW())";
			$this->conn->query($log_sql);
	
			$resp['status'] = 'success';
			if(empty($id)){
				$res['msg'] = "New supplier successfully saved.";
			}else{
				$res['msg'] = "Supplier successfully updated.";
			}
	
			$this->settings->set_flashdata('success', $res['msg']);
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
	
		return json_encode($resp);
	}
	
	function delete_supplier(){
		extract($_POST);
	
		// Retrieve the active user_id from the session
		$user_id = $_SESSION['userdata']['id'];
	
		// Check if $id is set before trying to use it
		if (isset($id)) {
			// Retrieve the name of the supplier to be deleted based on target isset $id
			$result = $this->conn->query("SELECT `name` FROM `supplier_list` WHERE id = '{$id}'");
	
			if ($result && $result->num_rows > 0) {
				$row = $result->fetch_assoc();
				$name = $this->conn->real_escape_string($row['name']);
	
				// Log the activity in user_log for deletion before the actual deletion
				$log_sql = "INSERT INTO `user_log` (user_id, supplier_name, action_description, type, datetime) VALUES ('$user_id', '$name', 'deleted', '2', NOW())";
				$this->conn->query($log_sql);
			} else {
				// Handle error if the supplier with the given $id is not found
				$resp['status'] = 'failed';
				$resp['error'] = 'Supplier not found.';
				return json_encode($resp);
			}
	
			// Actual deletion of the supplier
			$del = $this->conn->query("DELETE FROM `supplier_list` WHERE id = '{$id}'");
	
			if($del){
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success', "Supplier successfully deleted.");
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		} else {
			// Handle error if $id is not set
			$resp['status'] = 'failed';
			$resp['error'] = 'Supplier ID not set.';
		}
	
		return json_encode($resp);
	}

	function archive_supplier(){
		$id = $_POST['id']; 
		// Check if status is 0
		$get = $this->conn->query("SELECT * FROM supplier_list WHERE id = '{$id}'");
			if(!$get){
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
				return json_encode($resp);
			}
			
			if($get->num_rows > 0){
				$res = $get->fetch_array();
				
				$del = $this->conn->query("UPDATE `supplier_list` SET status = 0 WHERE id = '{$id}'");
				
				if($del){
					$resp['status'] = 'success';
					$this->settings->set_flashdata('success',"Supplier Successfully moved to Archive Section.");
					
				}else{
					$resp['status'] = 'failed';
					$resp['error'] = $this->conn->error;
				}
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = 'No supplier found with the provided ID.';
			}
			
			return json_encode($resp);
		}

		function restore_supplier(){
			$id = $_POST['id']; 
			// Check if status is 0
			$get = $this->conn->query("SELECT * FROM supplier_list WHERE id = '{$id}'");
				if(!$get){
					$resp['status'] = 'failed';
					$resp['error'] = $this->conn->error;
					return json_encode($resp);
				}
				
				if($get->num_rows > 0){
					$res = $get->fetch_array();
					
					$del = $this->conn->query("UPDATE `supplier_list` SET status = 1 WHERE id = '{$id}'");
					
					if($del){
						$resp['status'] = 'success';
						$this->settings->set_flashdata('success',"Supplier Successfully moved to Supplier Section.");
						
					}else{
						$resp['status'] = 'failed';
						$resp['error'] = $this->conn->error;
					}
				} else {
					$resp['status'] = 'failed';
					$resp['error'] = 'No supplier found with the provided ID.';
				}
				
				return json_encode($resp);
			}

	function save_category(){
		extract($_POST);
	
		// Retrieve the active user_id from the session
		$user_id = $_SESSION['userdata']['id'];
	
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
	
		$check = $this->conn->query("SELECT * FROM `categories` WHERE `name` = '{$name}' " . (!empty($id) ? " AND id != {$id} " : ""))->num_rows;
	
		if($this->capture_err())
			return $this->capture_err();
	
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Category Name already exists.";
			return json_encode($resp);
			exit;
		}
	
		if(empty($id)){
			$sql = "INSERT INTO `categories` SET {$data} ";
			$save = $this->conn->query($sql);
	
			if($save){
				// Retrieve the name of the newly added category
				$name = $this->conn->real_escape_string($name);
	
				// Log the activity in user_log for addition
				$log_sql = "INSERT INTO `user_log` (user_id, category_name, action_description, type, datetime) VALUES ('$user_id', '$name', 'added', '3', NOW())";
				$this->conn->query($log_sql);
			}
	
			$action_description = 'added';
		}

		else{
			$sql = "UPDATE `categories` SET {$data} WHERE id = '{$id}' ";
			$save = $this->conn->query($sql);
	
			// Log the activity in user_log for modification
			$log_sql = "INSERT INTO `user_log` (user_id, category_name, action_description, type, datetime) VALUES ('$user_id', '$name', 'modified', '3', NOW())";
			$this->conn->query($log_sql);
	
			$action_description = 'modified';
		}
	
		if($save){
			$resp['status'] = 'success';
			if(empty($id)){
				$res['msg'] = "New category successfully saved.";
			}else{
				$res['msg'] = "Category successfully updated.";
			}
	
			$this->settings->set_flashdata('success', $res['msg']);
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
	
		return json_encode($resp);
	}
	
	function delete_category(){
		extract($_POST);
	
		// Retrieve the active user_id from the session
		$user_id = $_SESSION['userdata']['id'];
	
		// Check if $id is set before trying to use it
		if (isset($id)) {
			// Retrieve the name of the category to be deleted based on target isset $id
			$result = $this->conn->query("SELECT `name` FROM `categories` WHERE id = '{$id}'");
			
			if ($result && $result->num_rows > 0) {
				$row = $result->fetch_assoc();
				$name = $this->conn->real_escape_string($row['name']);
	
				// Log the activity in user_log for deletion before the actual deletion
				$log_sql = "INSERT INTO `user_log` (user_id, category_name, action_description, type, datetime) VALUES ('$user_id', '$name', 'deleted', '3', NOW())";
				$this->conn->query($log_sql);
			} else {
				// Handle error if the category with the given $id is not found
				$resp['status'] = 'failed';
				$resp['error'] = 'Category not found.';
				return json_encode($resp);
			}
	
			// Actual deletion of the category
			$del = $this->conn->query("DELETE FROM `categories` WHERE id = '{$id}'");
	
			if ($del) {
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success', "Category successfully deleted.");
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		} else {
			// Handle error if $id is not set
			$resp['status'] = 'failed';
			$resp['error'] = 'Category ID not set.';
		}
	
		return json_encode($resp);
	}
	
	


	function save_expense(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `expenses` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Expense data already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `expenses` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `expenses` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id)){
				$res['msg'] = "New Expense Data successfully saved.";
				$id = $this->conn->insert_id;
			}else{
				$res['msg'] = "Expense successfully updated.";
			}
		$this->settings->set_flashdata('success',$res['msg']);
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}

	function delete_expense(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `expenses` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Expense Data successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	
	function save_item(){
		$item_id = '';
		extract($_POST);
		$data = "";
		$existing_sku = ''; // Initialize variable to store existing SKU
	
		// Loop through POST data to construct the SQL query
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id', 'sku' , 'unit_id'))){ // Exclude sku from being added to data
				if(!empty($data)) $data .=",";
				if ($k != 'sku') {
					if (is_array($v)) {
						// Convert array to string
						$v = implode(",", $v);
					}
					$v = $this->conn->real_escape_string($v);
					$data .= " `{$k}`='{$v}' ";
				}
			}
		}
	
		// Retrieve existing SKU if available
		if(isset($id) && !empty($id)) {
			$existing_sku_query = $this->conn->query("SELECT `sku` FROM `item_list` WHERE `id` = '{$id}'");
			if($existing_sku_query->num_rows > 0) {
				$existing_sku = $existing_sku_query->fetch_assoc()['sku'];
			}
		}
	
		// Generate SKU based on category if not provided and no existing SKU
	    if(empty($sku) && isset($category_id)){
			$category = $this->conn->query("SELECT `name` FROM `categories` WHERE `id` = '{$category_id}'")->fetch_assoc()['name'];
			$category_parts = explode(' ', $category);
			$sku_prefix = substr($category_parts[0], 0, 3); // Use first word only
			if(isset($category_parts[1])) {
				$sku_prefix .= substr($category_parts[1], 0, 3); // Append first three characters of second word if available
			}
			
			// Check for unique SKU
			$sku_count = 1;
			$sku = strtoupper($sku_prefix) . '_' . str_pad($sku_count, 3, '0', STR_PAD_LEFT);
			while(true) {
				$existing_sku_query = $this->conn->query("SELECT COUNT(*) as count FROM `item_list` WHERE `sku` = '{$sku}'");
				$existing_sku_count = $existing_sku_query->fetch_assoc()['count'];
				if($existing_sku_count == 0) {
					break; // Unique SKU found
				}
				$sku_count++;
				$sku = strtoupper($sku_prefix) . '_' . str_pad($sku_count, 3, '0', STR_PAD_LEFT);
			}
	
			$data .= ", `sku`='{$sku}' "; // Append generated SKU here
		} elseif (!empty($existing_sku)) {
			$sku = $existing_sku; // Use existing SKU if available
		}

		

	
		// Handle image upload
		if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
			// Check file type
			$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
			if (!in_array($_FILES['image']['type'], $allowed_types)) {
				// Handle invalid file type
				$resp['status'] = 'failed';
				$resp['msg'] = 'Invalid file type. Please upload a valid image.';
				return json_encode($resp);
			}
	
			// Remove old image if updating
			if (!empty($id)) {
				$old_image_path = $this->conn->query("SELECT `image` FROM `item_list` WHERE `id` = '{$id}'")->fetch_assoc()['image'];
				if (!empty($old_image_path) && file_exists($old_image_path)) {
					unlink($old_image_path);
				}
			}
	
			$image_path = '../uploads/item/' . basename($_FILES['image']['name']);
			move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
			$data .= ", `image`='{$image_path}' ";
		}

		
	
		$check = $this->conn->query("SELECT * FROM `item_list` WHERE `name` = '{$name}' AND `brand_id` = '{$brand_id}' " . (!empty($id) ? " AND id != {$id} " : ""))->num_rows;
		if($this->capture_err())
			return $this->capture_err();
	
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Item with the same name and brand already exists. Please choose different brand.";
			return json_encode($resp);
			exit;
		}
	
		if(empty($id)){
			$sql = "INSERT INTO `item_list` SET {$data} ";
			$save = $this->conn->query($sql);
			$item_id = $this->conn->insert_id;
			$action_description = 'added';
		}else{
			$sql = "UPDATE `item_list` SET {$data} WHERE id = '{$id}' ";
			$save = $this->conn->query($sql);
			$item_id = $id;
			$action_description = 'modified';
		}

		$existing_unit_ids = [];
		$existing_units_sql = "SELECT unit_id FROM `price_unit_order` WHERE item_id = '$item_id'";
		$existing_units_result = $this->conn->query($existing_units_sql);
		if ($existing_units_result->num_rows > 0) {
			while ($row = $existing_units_result->fetch_assoc()) {
				$existing_unit_ids[] = $row['unit_id'];
			}
		}
		
// Initialize $unit_ids variable
$unit_ids = isset($_POST['unit_id']) && is_array($_POST['unit_id']) ? $_POST['unit_id'] : [];

// Update existing records and insert new records
foreach ($existing_unit_ids as $existing_unit_id) {
    if (!in_array($existing_unit_id, $unit_ids)) {
        // Update status column to 0 for records where the unit_id is removed
        $update_status_sql = "UPDATE `price_unit_order` SET status = 0 WHERE item_id = '$item_id' AND unit_id = '$existing_unit_id'";
        $update_status_result = $this->conn->query($update_status_sql);
        if (!$update_status_result) {
            // Handle error if update fails
            $resp['status'] = 'failed';
            $resp['msg'] = 'Failed to update status in price_unit_order table.';
            return json_encode($resp);
        }
    } elseif (in_array($existing_unit_id, $unit_ids)) {
        // If the unit ID exists, update its status to 1
        $update_status_sql = "UPDATE `price_unit_order` SET status = 1 WHERE item_id = '$item_id' AND unit_id = '$existing_unit_id'";
        $update_status_result = $this->conn->query($update_status_sql);
        if (!$update_status_result) {
            // Handle error if update fails
            $resp['status'] = 'failed';
            $resp['msg'] = 'Failed to update status in price_unit_order table.';
            return json_encode($resp);
        }
    }
}

// Insert new records for unit IDs that do not have existing records
foreach ($unit_ids as $unit_id) {
    if (!in_array($unit_id, $existing_unit_ids)) {
        // Insert new record for unit ID
        $insert_sql = "INSERT INTO `price_unit_order` (item_id, unit_id, status) VALUES ('$item_id', '$unit_id', 1)";
        $insert_result = $this->conn->query($insert_sql);
        if (!$insert_result) {
            // Handle error if insert fails
            $resp['status'] = 'failed';
            $resp['msg'] = 'Failed to insert records in price_unit_order table.';
            return json_encode($resp);
        }
    }
}

// If the loop completes successfully, set the success response
$resp['status'] = 'success';
if (empty($id)) {
    $this->settings->set_flashdata('success', "New Item successfully added.");
} else {
    $this->settings->set_flashdata('success', "Item successfully updated.");
}

return json_encode($resp);
	}
	
	function delete_item(){
		extract($_POST);
	
		// Retrieve the name and image path of the item to be deleted
		$item_info = $this->conn->query("SELECT `name`, `image` FROM `item_list` WHERE `id` = '{$id}'")->fetch_assoc();
		$item_name = $item_info['name'];
		$image_path = $item_info['image'];
	
		// Log the activity in user_log
		$user_id = $_SESSION['userdata']['id'];
		$log_sql = "INSERT INTO `user_log` (user_id, item_name, action_description, type, datetime) VALUES ('$user_id', '$item_name', 'deleted', '1', NOW())";
		$log_result = $this->conn->query($log_sql);
	
		if ($log_result) {
			// Log entry successful, proceed with the deletion
			if (!empty($image_path) && file_exists($image_path)) {
				// Delete the image file from the server if it exists
				unlink($image_path);
			}
	
			$del = $this->conn->query("DELETE FROM `item_list` WHERE id = '{$id}'");
	
			if ($del) {
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success', "Item successfully deleted.");
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		} else {
			// Log entry failed
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
	
		return json_encode($resp);
	}

	function archive_item(){
		$id = $_POST['id']; 
		// Check if status is 0
		$get = $this->conn->query("SELECT * FROM item_list WHERE id = '{$id}'");
			if(!$get){
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
				return json_encode($resp);
			}
			
			if($get->num_rows > 0){
				$res = $get->fetch_array();
				
				$del = $this->conn->query("UPDATE `item_list` SET status = 0 WHERE id = '{$id}'");
				
				if($del){
					$resp['status'] = 'success';
					$this->settings->set_flashdata('success',"Item Successfully moved to Archive Section.");
					
				}else{
					$resp['status'] = 'failed';
					$resp['error'] = $this->conn->error;
				}
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = 'No item found with the provided ID.';
			}
			
			return json_encode($resp);
		}

		function restore_item(){
			$id = $_POST['id']; 
			// Check if status is 0
			$get = $this->conn->query("SELECT * FROM item_list WHERE id = '{$id}'");
				if(!$get){
					$resp['status'] = 'failed';
					$resp['error'] = $this->conn->error;
					return json_encode($resp);
				}
				
				if($get->num_rows > 0){
					$res = $get->fetch_array();
					
					$del = $this->conn->query("UPDATE `item_list` SET status = 1 WHERE id = '{$id}'");
					
					if($del){
						$resp['status'] = 'success';
						$this->settings->set_flashdata('success',"Item Successfully moved to Archive Section.");
						
					}else{
						$resp['status'] = 'failed';
						$resp['error'] = $this->conn->error;
					}
				} else {
					$resp['status'] = 'failed';
					$resp['error'] = 'No item found with the provided ID.';
				}
				
				return json_encode($resp);
			}

	function delete_supplier_product() {
		global $conn;
	
		$spId = $_POST['spId'];
	
		// Execute SQL query to delete the record with the given sp_id
		$stmt = $conn->prepare("DELETE FROM `supplier_product` WHERE sp_id = ?");
		$stmt->bind_param("i", $spId);
	
		$response = array();
	
		if ($stmt->execute()) {
			$response['status'] = 'success';
		} else {
			$response['status'] = 'error';
			$response['error'] = $stmt->error;
		}
	
		echo json_encode($response);
		exit;
	}

	function update_supplier_product() {
		global $conn;
	
		$productId = $_POST['productId'];
		$supplierId = $_POST['supplierId'];
		$data = json_decode($_POST['data'], true);
	
		// Check if supplier_id and product_id exist in the referenced tables
		$supplierCheck = $conn->query("SELECT * FROM `supplier_list` WHERE id = $supplierId")->fetch_assoc();
		$productCheck = $conn->query("SELECT * FROM `item_list` WHERE id = $productId")->fetch_assoc();
	

		
		if (!$supplierCheck || !$productCheck) {
			// Supplier or product doesn't exist, handle accordingly (e.g., show an error message)
			$response['status'] = 'error';
			$response['error'] = 'Supplier or product does not exist.';
			echo json_encode($response);
			exit;
		}
	
		foreach ($data as $item) {
			$unitId = $item['unitId'];
			$supplierPrice = $item['supplierPrice'];
	
			if ($supplierPrice == 0) {
				$status = 0;
			} else {
				$status = 1; // Set status to 1 if supplier_price is not 0
			}
	
			// Check if the record already exists
			$existingRecord = $conn->query("SELECT * FROM `supplier_product` WHERE supplier_id = $supplierId AND product_id = $productId AND unit_id = $unitId")->fetch_assoc();
	
			if ($existingRecord) {
				// If the record exists, update it
				$stmt = $conn->prepare("UPDATE `supplier_product` SET status = ?, supplier_price = ? WHERE supplier_id = ? AND product_id = ? AND unit_id = ?");
				$stmt->bind_param("iisii", $status, $supplierPrice, $supplierId, $productId, $unitId);
			} else {
				// If the record doesn't exist, insert a new one
				$stmt = $conn->prepare("INSERT INTO `supplier_product` (supplier_id, product_id, unit_id, status, supplier_price) VALUES (?, ?, ?, ?, ?)");
				$stmt->bind_param("iiisi", $supplierId, $productId, $unitId, $status, $supplierPrice);
			}
	
			if ($stmt->execute()) {
				$response['status'] = 'success';
			} else {
				$response['status'] = 'error';
				$response['error'] = $stmt->error;
			}
		}
	
		echo json_encode($response);
		exit;
	}
	
	function update_pu() {
		$data = json_decode($_POST['data'], true);
		$responses = array();
		foreach ($data as $pu_id => $fields) {
			$field = $fields['field'];
			$value = $this->conn->real_escape_string($fields['value']);
			
			// Update the specified field for the unit
			$update_query = "UPDATE price_unit_order SET $field = '$value' WHERE pu_id = '$pu_id'";
			$result = $this->conn->query($update_query);
			
			// Check if the update was successful
			if ($result) {
				$responses[$pu_id] = 'success';
			} else {
				$responses[$pu_id] = 'failed';
			}
		}
		// Return the response
		echo json_encode(array('status' => $responses));
	}


	function save_po(){
		if(empty($_POST['id'])){
			$prefix = "PO";
			$code = sprintf("%'.04d",1);
			while(true){
				$check_code = $this->conn->query("SELECT * FROM `purchase_order_list` where po_code ='".$prefix.'-'.$code."' ")->num_rows;
				if($check_code > 0){
					$code = sprintf("%'.04d",$code+1);
				}else{
					break;
				}
			}
			$_POST['po_code'] = $prefix."-".$code;
		}
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id')) && !is_array($_POST[$k])){
				if(!is_numeric($v))
				$v= $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=", ";
				$data .=" `{$k}` = '{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `purchase_order_list` set {$data}";
		}else{
			$sql = "UPDATE `purchase_order_list` set {$data} where id = '{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
			$po_id = $this->conn->insert_id;
			else
			$po_id = $id;
			$resp['id'] = $po_id;
			$data = "";
			foreach($item_id as $k => $v) {
				if(!empty($data)) $data .= ", ";
				// Check if the unit exists for the current product and item
				if(isset($unit_arr[$v][$unit[$k]])) {
					// Use the unit value from the $unit_arr array
					$unit_value = $unit_arr[$v][$unit[$k]];
				} else {
					// Default to the value from the form if the unit is not found
					$unit_value = $unit[$k];
				}
				$data .= "('{$po_id}','{$v}','{$qty[$k]}','{$price[$k]}','{$unit_value}','{$total[$k]}')";
			}
			if(!empty($data)){
				$this->conn->query("DELETE FROM `po_items` where po_id = '{$po_id}'");
				$save = $this->conn->query("INSERT INTO `po_items` (`po_id`,`item_id`,`quantity`,`price`,`unit`,`total`) VALUES {$data}");
				if(!$save){
					$resp['status'] = 'failed';
					if(empty($id)){
						$this->conn->query("DELETE FROM `purchase_order_list` where id '{$po_id}'");
					}
					$resp['msg'] = 'PO has failed to save. Error: '.$this->conn->error;
					$resp['sql'] = "INSERT INTO `po_items` (`po_id`,`item_id`,`quantity`,`price`,`unit`,`total`) VALUES {$data}";
				}
			}
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'An error occured. Error: '.$this->conn->error;
		}
		if($resp['status'] == 'success'){
			if(empty($id)){
				$this->settings->set_flashdata('success'," New Purchase Order was successfully created.");
			}else{
				$this->settings->set_flashdata('success'," Purchase Order's Details successfully updated.");
			}
		}

		return json_encode($resp);
	}
	
	
	function archive_po(){
		$id = $_POST['id']; 
		// Check if status is 0
		$get = $this->conn->query("SELECT * FROM purchase_order_list WHERE id = '{$id}'");
			if(!$get){
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
				return json_encode($resp);
			}
			
			if($get->num_rows > 0){
				$res = $get->fetch_array();
				
				$del = $this->conn->query("UPDATE `purchase_order_list` SET sstatus = 0 WHERE id = '{$id}'");
				
				if($del){
					$resp['status'] = 'success';
					$this->settings->set_flashdata('success',"Purchase Order Successfully moved to Archive Section.");
					
				}else{
					$resp['status'] = 'failed';
					$resp['error'] = $this->conn->error;
				}
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = 'No purchase record found with the provided ID.';
			}
			
			return json_encode($resp);
		}

	

		

		function archive_category(){
			$id = $_POST['id']; 
			// Check if status is 0
			$get = $this->conn->query("SELECT * FROM categories WHERE id = '{$id}'");
				if(!$get){
					$resp['status'] = 'failed';
					$resp['error'] = $this->conn->error;
					return json_encode($resp);
				}
				
				if($get->num_rows > 0){
					$resp = $get->fetch_array();
					
					$del = $this->conn->query("UPDATE `categories` SET status = 0 WHERE id = '{$id}'");
					
					if($del){
						$resp['status'] = 'success';
						$this->settings->set_flashdata('success',"Category data successfully moved to Archive Section.");
						
					}else{
						$resp['status'] = 'failed';
						$resp['error'] = $this->conn->error;
					}
				} else {
					$resp['status'] = 'failed';
					$resp['error'] = 'Error: Contact developer.';
				}
				
				return json_encode($resp);
			}

			
		function restore_category(){
			$id = $_POST['id']; 
			// Check if status is 0
			$get = $this->conn->query("SELECT * FROM categories WHERE id = '{$id}'");
				if(!$get){
					$resp['status'] = 'failed';
					$resp['error'] = $this->conn->error;
					return json_encode($resp);
				}
				
				if($get->num_rows > 0){
					$resp = $get->fetch_array();
					
					$del = $this->conn->query("UPDATE `categories` SET status = 1 WHERE id = '{$id}'");
					
					if($del){
						$resp['status'] = 'success';
						$this->settings->set_flashdata('success',"Category data successfully moved to Category Section.");
						
					}else{
						$resp['status'] = 'failed';
						$resp['error'] = $this->conn->error;
					}
				} else {
					$resp['status'] = 'failed';
					$resp['error'] = 'Error: Contact developer.';
				}
				
				return json_encode($resp);
			}

	function delete_po(){
		extract($_POST);
		// Check if status is 0
		$check_status = $this->conn->query("SELECT * FROM purchase_order_list WHERE id = '{$id}' AND status = 0");
		if($check_status->num_rows > 0) {
			// Delete the row if status is 0
			$del = $this->conn->query("DELETE FROM `purchase_order_list` WHERE id = '{$id}'");
			if($del){
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success',"Selected Purchase Order Successfully deleted.");
				// Additional operations after deletion
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = 'Please delete first the orders received from this Purchase Order.';
		}
		return json_encode($resp);
	}

	function restore_po(){
		$id = $_POST['id']; 
		$get = $this->conn->query("SELECT * FROM purchase_order_list WHERE id = '{$id}'");
		
		if(!$get){
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
		}
		
		if($get->num_rows > 0){
			$res = $get->fetch_array();
			
			$del = $this->conn->query("UPDATE `purchase_order_list` SET sstatus = 1 WHERE id = '{$id}'");
			
			if($del){
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success',"Purchase Order's successfully recovered to Purchase Section.");
				
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = 'No purchase record found with the provided ID.';
		}
		
		return json_encode($resp);
	}

	function save_receiving(){
		if(empty($_POST['id'])){
			$prefix = "BO";
			$code = sprintf("%'.04d",1);
			while(true){
				$check_code = $this->conn->query("SELECT * FROM `back_order_list` where bo_code ='".$prefix.'-'.$code."' ")->num_rows;
				if($check_code > 0){
					$code = sprintf("%'.04d",$code+1);
				}else{
					break;
				}
			}
			$_POST['bo_code'] = $prefix."-".$code;
		}else{
			$get = $this->conn->query("SELECT * FROM back_order_list where receiving_id = '{$_POST['id']}' ");
			if($get->num_rows > 0){
				$res = $get->fetch_array();
				$bo_id = $res['id'];
				$_POST['bo_code'] = $res['bo_code'];	
			}else{

				$prefix = "BO";
				$code = sprintf("%'.04d",1);
				while(true){
					$check_code = $this->conn->query("SELECT * FROM `back_order_list` where bo_code ='".$prefix.'-'.$code."' ")->num_rows;
					if($check_code > 0){
						$code = sprintf("%'.04d",$code+1);
					}else{
						break;
					}
				}
				$_POST['bo_code'] = $prefix."-".$code;

			}
		}
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','bo_code','supplier_id','po_id')) && !is_array($_POST[$k])){
				if(!is_numeric($v))
				$v= $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=", ";
				$data .=" `{$k}` = '{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `receiving_list` set {$data}";
		}else{
			$sql = "UPDATE `receiving_list` set {$data} where id = '{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
			$r_id = $this->conn->insert_id;
			else
			$r_id = $id;
			$resp['id'] = $r_id;
			if(!empty($id)){
				$stock_ids = $this->conn->query("SELECT stock_ids FROM `receiving_list` where id = '{$id}'")->fetch_array()['stock_ids'];
				$this->conn->query("DELETE FROM `stock_list` where id in ({$stock_ids})");
			}
			$stock_ids= array();
			foreach($item_id as $k =>$v){
				if(!empty($data)) $data .=", ";
				$sql = "INSERT INTO stock_list (`item_id`,`quantity`,`price`,`unit`,`total`,`type`) VALUES ('{$v}','{$qty[$k]}','{$price[$k]}','{$unit[$k]}','{$total[$k]}','1')";
				$this->conn->query($sql);
				$stock_ids[] = $this->conn->insert_id;
				if($qty[$k] < $oqty[$k]){
					$bo_ids[] = $k;
				}
			}
			if(count($stock_ids) > 0){
				$stock_ids = implode(',',$stock_ids);
				$this->conn->query("UPDATE `receiving_list` set stock_ids = '{$stock_ids}' where id = '{$r_id}'");
			}
			if(isset($bo_ids)){
				$this->conn->query("UPDATE `purchase_order_list` set status = 1 where id = '{$po_id}'");
				if($from_order == 2){
					$this->conn->query("UPDATE `back_order_list` set status = 1 where id = '{$form_id}'");
				}
				if(!isset($bo_id)){
					$sql = "INSERT INTO `back_order_list` set 
							bo_code = '{$bo_code}',	
							receiving_id = '{$r_id}',	
							po_id = '{$po_id}',	
							supplier_id = '{$supplier_id}',	
							discount_perc = '{$discount_perc}',	
							tax_perc = '{$tax_perc}'
						";
				}else{
					$sql = "UPDATE `back_order_list` set 
							receiving_id = '{$r_id}',	
							po_id = '{$form_id}',	
							supplier_id = '{$supplier_id}',	
							discount_perc = '{$discount_perc}',	
							tax_perc = '{$tax_perc}',
							where bo_id = '{$bo_id}'
						";
				}
				$bo_save = $this->conn->query($sql);
				if(!isset($bo_id))
				$bo_id = $this->conn->insert_id;
				$stotal =0; 
				$data = "";
				foreach($item_id as $k =>$v){
					if(!in_array($k,$bo_ids))
						continue;
					$total = ($oqty[$k] - $qty[$k]) * $price[$k];
					$stotal += $total;
					if(!empty($data)) $data.= ", ";
					$data .= " ('{$bo_id}','{$v}','".($oqty[$k] - $qty[$k])."','{$price[$k]}','{$unit[$k]}','{$total}') ";
				}
				$this->conn->query("DELETE FROM `bo_items` where bo_id='{$bo_id}'");
				$save_bo_items = $this->conn->query("INSERT INTO `bo_items` (`bo_id`,`item_id`,`quantity`,`price`,`unit`,`total`) VALUES {$data}");
				if($save_bo_items){
					$discount = $stotal * ($discount_perc /100);
					$stotal -= $discount;
					$tax = $stotal * ($tax_perc /100);
					$stotal += $tax;
					$amount = $stotal;
					$this->conn->query("UPDATE back_order_list set amount = '{$amount}', discount='{$discount}', tax = '{$tax}' where id = '{$bo_id}'");
				}

			}else{
				$this->conn->query("UPDATE `purchase_order_list` set status = 2 where id = '{$po_id}'");
				if($from_order == 2){
					$this->conn->query("UPDATE `back_order_list` set status = 2 where id = '{$form_id}'");
				}
			}
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'An error occured. Error: '.$this->conn->error;
		}
		if($resp['status'] == 'success'){
			if(empty($id)){
				$this->settings->set_flashdata('success'," New Stock was Successfully received.");
			}else{
				$this->settings->set_flashdata('success'," Received Stock's Details Successfully updated.");
			}
		}

		return json_encode($resp);
	}
	function delete_receiving(){
		extract($_POST);
		$qry = $this->conn->query("SELECT * from  receiving_list where id='{$id}' ");
		if($qry->num_rows > 0){
			$res = $qry->fetch_array();
			$ids = $res['stock_ids'];
		}
		if(isset($ids) && !empty($ids))
		$this->conn->query("DELETE FROM stock_list where id in ($ids) ");
		$del = $this->conn->query("DELETE FROM receiving_list where id='{$id}' ");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Received Order's Details Successfully deleted.");

			if(isset($res)){
				if($res['from_order'] == 1){
					$this->conn->query("UPDATE purchase_order_list set status = 0 where id = '{$res['form_id']}' ");
				}
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function delete_bo(){
		extract($_POST);
		$bo =$this->conn->query("SELECT * FROM `back_order_list` where id = '{$id}'");
		if($bo->num_rows >0)
		$bo_res = $bo->fetch_array();
		$del = $this->conn->query("DELETE FROM `back_order_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Purchase Order Data Successfully Deleted.");
			$qry = $this->conn->query("SELECT `stock_ids` from  receiving_list where form_id='{$id}' and from_order = '2' ");
			if($qry->num_rows > 0){
				$res = $qry->fetch_array();
				$ids = $res['stock_ids'];
				$this->conn->query("DELETE FROM stock_list where id in ($ids) ");

				$this->conn->query("DELETE FROM receiving_list where form_id='{$id}' and from_order = '2' ");
			}
			if(isset($bo_res)){
				$check = $this->conn->query("SELECT * FROM `receiving_list` where from_order = 1 and form_id = '{$bo_res['po_id']}' ");
				if($check->num_rows > 0){
					$this->conn->query("UPDATE `purchase_order_list` set status = 1 where id = '{$bo_res['po_id']}' ");
				}else{
					$this->conn->query("UPDATE `purchase_order_list` set status = 0 where id = '{$bo_res['po_id']}' ");
				}
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_sreturn(){
		if(empty($_POST['id'])){
			$prefix = "R";
			$code = sprintf("%'.04d",1);
			while(true){
				$check_code = $this->conn->query("SELECT * FROM `supp_returnlist` where return_code ='".$prefix.'-'.$code."' ")->num_rows;
				if($check_code > 0){
					$code = sprintf("%'.04d",$code+1);
				}else{
					break;
				}
			}
			$_POST['return_code'] = $prefix."-".$code;
		}
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id')) && !is_array($_POST[$k])){
				if(!is_numeric($v))
				$v= $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=", ";
				$data .=" `{$k}` = '{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `supp_returnlist` set {$data}";
		}else{
			$sql = "UPDATE `supp_returnlist` set {$data} where id = '{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
			$return_id = $this->conn->insert_id;
			else
			$return_id = $id;
			$resp['id'] = $return_id;
			$data = "";
			$sids = array();
			$get = $this->conn->query("SELECT * FROM `supp_returnlist` where id = '{$return_id}'");
			if($get->num_rows > 0){
				$res = $get->fetch_array();
				if(!empty($res['stock_ids'])){
					$this->conn->query("DELETE FROM `stock_list` where id in ({$res['stock_ids']}) ");
				}
			}
			foreach($item_id as $k =>$v){
				$sql = "INSERT INTO `stock_list` set item_id='{$v}', `quantity` = '{$qty[$k]}', `unit` = '{$unit[$k]}', `price` = '{$price[$k]}', `total` = '{$total[$k]}', `type` = 2 ";
				$save = $this->conn->query($sql);
				if($save){
					$sids[] = $this->conn->insert_id;
				}
			}
			$sids = implode(',',$sids);
			$this->conn->query("UPDATE `supp_returnlist` set stock_ids = '{$sids}' where id = '{$return_id}'");
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'An error occured. Error: '.$this->conn->error;
		}
		if($resp['status'] == 'success'){
			if(empty($id)){
				$this->settings->set_flashdata('success'," New Supplier Returned Item Record was Successfully created.");
			}else{
				$this->settings->set_flashdata('success'," Supplier Returned Item Record's Successfully updated.");
			}
		}

		return json_encode($resp);
	}
	function delete_sreturn(){
		extract($_POST);
		$get = $this->conn->query("SELECT * FROM supp_returnlist where id = '{$id}'");
		if($get->num_rows > 0){
			$res = $get->fetch_array();
		}
		$del = $this->conn->query("DELETE FROM `supp_returnlist` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Returned Item Record's Successfully deleted.");
			if(isset($res)){
				$this->conn->query("DELETE FROM `stock_list` where id in ({$res['stock_ids']})");
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	
	
	function reset_data() {
		extract($_POST);
	

		$resetTables = [];
	

		$tablesToReset = [
			'expenses',
			'sales_list',
			'stock_list',
			'back_order_list',
			'cust_returnlist',
			'item_list',
			'purchase_order_list',
			'po_items',
			'bo_items',
			'categories',
			'receiving_list',
			'supp_returnlist',
			'supplier_list',
			'supplier_product',
			'user_log',
			'system_log',
			'user_meta',
			'vendors',
		];
		foreach ($tablesToReset as $table) {
			$deleteQuery = "DELETE FROM `$table`";
			$deleteResult = $this->conn->query($deleteQuery);
		
			if ($deleteResult) {
				$resetTables[] = $table;
				error_log("Deleted data from table: $table");
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
				error_log("Error deleting data from table $table: " . $this->conn->error);
				return json_encode($resp);
			}
		}
		
	
		if (count($resetTables) == count($tablesToReset)) {
			$successMessage = "All data reset completed!";
		} else {
			$successMessage = "Reset completed for: " . implode(', ', $resetTables);
		}
		
		$resp['status'] = 'success';
		$resp['message'] = $successMessage;
		$this->settings->set_flashdata('success', $successMessage);
		
		echo json_encode($resp);
	}
	

		function save_sale(){
		if(empty($_POST['id'])){
			$prefix = "SALE";
			$code = sprintf("%'.04d",1);
			while(true){
				$check_code = $this->conn->query("SELECT * FROM `sales_list` where sales_code ='".$prefix.'-'.$code."' ")->num_rows;
				if($check_code > 0){
					$code = sprintf("%'.04d",$code+1);
				}else{
					break;
				}
			}
			$_POST['sales_code'] = $prefix."-".$code;
		}
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id')) && !is_array($_POST[$k])){
				if(!is_numeric($v))
				$v= $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=", ";
				$data .=" `{$k}` = '{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `sales_list` set {$data}";
		}else{
			$sql = "UPDATE `sales_list` set {$data} where id = '{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
			$sale_id = $this->conn->insert_id;
			else
			$sale_id = $id;
			$resp['id'] = $sale_id;
			$data = "";
			$sids = array();
			$cogs_total = 0; // Initialize COGS total
	
			// Fetch the COGS for each item and calculate COGS total
			foreach($item_id as $k => $v){
				$cogs = $this->conn->query("SELECT cogs FROM `item_list` WHERE id = '{$v}'")->fetch_assoc()['cogs'];
				$cogs_total += $cogs * $qty[$k];
	
				$sql = "INSERT INTO `stock_list` SET item_id='{$v}', `quantity` = '{$qty[$k]}', `unit` = '{$unit[$k]}', `price` = '{$price[$k]}', `total` = '{$total[$k]}', `type` = 2, `cogs` = '{$cogs}', `date_created` = '{$transaction_datetime}'";
				$save = $this->conn->query($sql);
				if($save){
					$sids[] = $this->conn->insert_id;
				}
			}
			$sids = implode(',',$sids);
			$this->conn->query("UPDATE `sales_list` set stock_ids = '{$sids}', cogs_total = '{$cogs_total}' where id = '{$sale_id}'");
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'An error occurred. Error: '.$this->conn->error;
		}
		if($resp['status'] == 'success'){
			if(empty($id)){
				$this->settings->set_flashdata('success'," New Sales Record was Successfully created.");
			}else{
				$this->settings->set_flashdata('success'," Sales Record's Successfully updated.");
			}
		}
	
		return json_encode($resp);
	}

	function archive_sale(){
		$id = $_POST['id']; 
		$get = $this->conn->query("SELECT * FROM sales_list WHERE id = '{$id}'");
		
		if(!$get){
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
		}
		
		if($get->num_rows > 0){
			$res = $get->fetch_array();
			
			$del = $this->conn->query("UPDATE `sales_list` SET status = 0 WHERE id = '{$id}'");
			
			if($del){
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success',"Sales Record's Successfully moved to Archive Section.");
				if(isset($res)){
					$this->conn->query("UPDATE `stock_list` SET status= 0 where id in ({$res['stock_ids']})");
				}
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = 'No sales record found with the provided ID.';
		}
		
		return json_encode($resp);
	}

	function restore_sale(){
		$id = $_POST['id']; 
		$get = $this->conn->query("SELECT * FROM sales_list WHERE id = '{$id}'");
		
		if(!$get){
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
		}
		
		if($get->num_rows > 0){
			$res = $get->fetch_array();
			
			$del = $this->conn->query("UPDATE `sales_list` SET status = 1 WHERE id = '{$id}'");
			
			if($del){
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success',"Sales Record's successfully recovered to Sales Section.");
				if(isset($res)){
					$this->conn->query("UPDATE `stock_list` SET status= 1 where id in ({$res['stock_ids']})");
				}
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = 'No sales record found with the provided ID.';
		}
		
		return json_encode($resp);
	}

	function delete_sale(){
		extract($_POST);
		$get = $this->conn->query("SELECT * FROM sales_list where id = '{$id}'");
		if($get->num_rows > 0){
			$res = $get->fetch_array();
		}
		$del = $this->conn->query("DELETE FROM `sales_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Sales Record's Successfully deleted.");
			if(isset($res)){
				$this->conn->query("DELETE FROM `stock_list` where id in ({$res['stock_ids']})");
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}

	function save_discount(){
		extract($_POST);
	
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
	
		$check = $this->conn->query("SELECT * FROM `discounts` WHERE `name` = '{$name}' " . (!empty($id) ? " AND id != {$id} " : ""))->num_rows;
	
		if($this->capture_err())
			return $this->capture_err();
	
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Discount Name already exists.";
			return json_encode($resp);
			exit;
		}
	
		if(empty($id)){
			$sql = "INSERT INTO `discounts` SET {$data} ";
			$save = $this->conn->query($sql);
	
			if($save){
				// Retrieve the name of the newly added category
				$name = $this->conn->real_escape_string($name);
	
			}

		}else{
			$sql = "UPDATE `discounts` SET {$data} WHERE id = '{$id}' ";
			$save = $this->conn->query($sql);
	
		}
	
		if($save){
			$resp['status'] = 'success';
			if(empty($id)){
				$res['msg'] = "New Discount successfully saved.";
			}else{
				$res['msg'] = "Discount successfully updated.";
			}
	
			$this->settings->set_flashdata('success', $res['msg']);
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
	
		return json_encode($resp);
	}

	function delete_discount(){
		extract($_POST);
	
		// Check if $id is set before trying to use it
		if (isset($id)) {
			// Retrieve the name of the category to be deleted based on target isset $id
			$result = $this->conn->query("SELECT `name` FROM `discounts` WHERE id = '{$id}'");
			
			if ($result && $result->num_rows > 0) {
				$row = $result->fetch_assoc();
				$name = $this->conn->real_escape_string($row['name']);
	
			} else {
				// Handle error if the category with the given $id is not found
				$resp['status'] = 'failed';
				$resp['error'] = 'Discount not found.';
				return json_encode($resp);
			}
	
			// Actual deletion of the category
			$del = $this->conn->query("DELETE FROM `discounts` WHERE id = '{$id}'");
	
			if ($del) {
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success', "Discount successfully deleted.");
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		} else {
			// Handle error if $id is not set
			$resp['status'] = 'failed';
			$resp['error'] = 'Discount ID not set.';
		}
	
		return json_encode($resp);
	}

	function archive_discount(){
		$id = $_POST['id']; 
		// Check if status is 0
		$get = $this->conn->query("SELECT * FROM discounts WHERE id = '{$id}'");
			if(!$get){
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
				return json_encode($resp);
			}
			
			if($get->num_rows > 0){
				$resp = $get->fetch_array();
				
				$del = $this->conn->query("UPDATE `discounts` SET status = 0 WHERE id = '{$id}'");
				
				if($del){
					$resp['status'] = 'success';
					$this->settings->set_flashdata('success',"Discount data successfully moved to Archive Section.");
					
				}else{
					$resp['status'] = 'failed';
					$resp['error'] = $this->conn->error;
				}
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = 'Error: Contact developer.';
			}
			
			return json_encode($resp);
		}

		
	function restore_discount(){
		$id = $_POST['id']; 
		// Check if status is 0
		$get = $this->conn->query("SELECT * FROM discounts WHERE id = '{$id}'");
			if(!$get){
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
				return json_encode($resp);
			}
			
			if($get->num_rows > 0){
				$resp = $get->fetch_array();
				
				$del = $this->conn->query("UPDATE `discounts` SET status = 1 WHERE id = '{$id}'");
				
				if($del){
					$resp['status'] = 'success';
					$this->settings->set_flashdata('success',"Discount data successfully moved to Discount Section.");
					
				}else{
					$resp['status'] = 'failed';
					$resp['error'] = $this->conn->error;
				}
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = 'Error: Contact developer.';
			}
			
			return json_encode($resp);
		}

	function save_vendor(){
		extract($_POST);
	
		// Check if 'vendor_name' is set in $_POST
		if (!isset($vendor_name)) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Vendor name is required.";
			return json_encode($resp);
		}
	
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
	
		// Check if 'vendor_name' exists in the 'vendors' table
		$check = $this->conn->query("SELECT * FROM `vendors` WHERE `vendor_name` = '{$vendor_name}' " . (!empty($id) ? " AND `id` != {$id} " : ""))->num_rows;
	
		if ($this->capture_err())
			return $this->capture_err();
	
		if ($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Vendor data already exists.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `vendors` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `vendors` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id)){
				$res['msg'] = "New Vendor Data successfully saved.";
				$id = $this->conn->insert_id;
			}else{
				$res['msg'] = "Vendor successfully updated.";
			}
		$this->settings->set_flashdata('success',$res['msg']);
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	
	function delete_vendor(){
		extract($_POST);
	
		$this->conn->query("SET FOREIGN_KEY_CHECKS=0");
	
		$del = $this->conn->query("DELETE FROM `vendors` WHERE id = '{$id}'");
	
		$this->conn->query("SET FOREIGN_KEY_CHECKS=1");
	
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Vendor Data successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	function save_pi(){
		if(empty($_POST['id'])){
			$prefix = "INVOICE";
			$code = sprintf("%'.04d",1);
			while(true){
				$check_code = $this->conn->query("SELECT * FROM `purchase_invoices` where invoice_number ='".$prefix.'-'.$code."' ")->num_rows;
				if($check_code > 0){
					$code = sprintf("%'.04d",$code+1);
				}else{
					break;
				}
			}
			$_POST['invoice_number'] = $prefix."-".$code;
		}
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id')) && !is_array($_POST[$k])){
				if(!is_numeric($v))
				$v= $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=", ";
				$data .=" `{$k}` = '{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `purchase_invoices` set {$data}";
		}else{
			$sql = "UPDATE `purchase_invoices` set {$data} where id = '{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] ='success';
			if(empty($id))
			$pi_id = $this->conn->insert_id;
		else
		    $pi_id = $id;
		    $resp['id'] = $pi_id;
			$data = "";
			foreach($item_name as $k =>$v){
				if(!empty($data)) $data .=", ";
				$data .= "('{$pi_id}','{$v}','{$quantity[$k]}','{$price[$k]}','{$unit[$k]}','{$total[$k]}')";
			}
			if(!empty($data)){
				$this->conn->query("DELETE FROM `pi_items` where invoice_id = '{$pi_id}'");
				$save = $this->conn->query("INSERT INTO `pi_items` (`invoice_id`,`item_name`,`quantity`,`price`,`unit`,`total`) VALUES {$data}");
				if(!$save){
					$resp['status'] = 'failed';
					if(empty($id)){
						$this->conn->query("DELETE FROM `purchase_invoices` where id '{$pi_id}'");
					}
					$resp['msg'] = 'PI has failed to save. Error: '.$this->conn->error;
					$resp['sql'] = "INSERT INTO `pi_items` (`invoice_id`,`item_name`,`quantity`,`price`,`unit`,`total`) VALUES {$data}";
				}
			}
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'An error occured. Error: '.$this->conn->error;
		}
		if($resp['status'] == 'success'){
			if(empty($id)){
				$this->settings->set_flashdata('success'," New Invoice was successfully saved.");
			}else{
				$this->settings->set_flashdata('success'," Payable Invoice's Details successfully updated.");
			}
		}

    return json_encode($resp);
}

function save_unit(){
	extract($_POST);

	$data = "";
	foreach($_POST as $k => $v){
		if(!in_array($k, array('id'))){
			if(!empty($data)) $data .= ",";
			$data .= " `{$k}`='{$v}' ";
		}
	}

	$check = $this->conn->query("SELECT * FROM `units` WHERE `name` = '{$name}' " . (!empty($id) ? " AND id != {$id} " : ""))->num_rows;

	if($this->capture_err())
		return $this->capture_err();

	if($check > 0){
		$resp['status'] = 'failed';
		$resp['msg'] = "Unit already exists.";
		return json_encode($resp);
		exit;
	}

	if(empty($id)){
		$sql = "INSERT INTO `units` SET {$data} ";
		$save = $this->conn->query($sql);

		if($save){
			// Retrieve the name of the newly added brand
			$name = $this->conn->real_escape_string($name);

		}

	}else{
		$sql = "UPDATE `units` SET {$data} WHERE id = '{$id}' ";
		$save = $this->conn->query($sql);

	}

	if($save){
		$resp['status'] = 'success';
		if(empty($id)){
			$res['msg'] = "New Unit successfully saved.";
		}else{
			$res['msg'] = "Unit successfully updated.";
		}

		$this->settings->set_flashdata('success', $res['msg']);
	}else{
		$resp['status'] = 'failed';
		$resp['err'] = $this->conn->error . "[{$sql}]";
	}

	return json_encode($resp);
}

function delete_unit(){
	extract($_POST);

	// Check if $id is set before trying to use it
	if (isset($id)) {
		// Retrieve the name of the category to be deleted based on target isset $id
$result = $this->conn->query("SELECT `name` FROM `units` WHERE id = '{$id}'");

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $unit = $this->conn->real_escape_string($row['name']); // Corrected line


		} else {
			// Handle error if the brand with the given $id is not found
			$resp['status'] = 'failed';
			$resp['error'] = 'Unit not found.';
			return json_encode($resp);
		}

		// Actual deletion of the category
		$del = $this->conn->query("DELETE FROM `units` WHERE id = '{$id}'");

		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Unit successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
	} else {
		// Handle error if $id is not set
		$resp['status'] = 'failed';
		$resp['error'] = 'Unit ID not set.';
	}

	return json_encode($resp);
}

function save_brand(){
	extract($_POST);

	$data = "";
	foreach($_POST as $k => $v){
		if(!in_array($k, array('id'))){
			if(!empty($data)) $data .= ",";
			$data .= " `{$k}`='{$v}' ";
		}
	}

	$check = $this->conn->query("SELECT * FROM `brands` WHERE `name` = '{$name}' " . (!empty($id) ? " AND id != {$id} " : ""))->num_rows;

	if($this->capture_err())
		return $this->capture_err();

	if($check > 0){
		$resp['status'] = 'failed';
		$resp['msg'] = "Brand Name already exists.";
		return json_encode($resp);
		exit;
	}

	if(empty($id)){
		$sql = "INSERT INTO `brands` SET {$data} ";
		$save = $this->conn->query($sql);

		if($save){
			// Retrieve the name of the newly added brand
			$name = $this->conn->real_escape_string($name);

		}

	}else{
		$sql = "UPDATE `brands` SET {$data} WHERE id = '{$id}' ";
		$save = $this->conn->query($sql);

	}

	if($save){
		$resp['status'] = 'success';
		if(empty($id)){
			$res['msg'] = "New Brand successfully saved.";
		}else{
			$res['msg'] = "Brand successfully updated.";
		}

		$this->settings->set_flashdata('success', $res['msg']);
	}else{
		$resp['status'] = 'failed';
		$resp['err'] = $this->conn->error . "[{$sql}]";
	}

	return json_encode($resp);
}

function delete_brand(){
	extract($_POST);

	// Check if $id is set before trying to use it
	if (isset($id)) {
		// Retrieve the name of the category to be deleted based on target isset $id
		$result = $this->conn->query("SELECT `name` FROM `brands` WHERE id = '{$id}'");
		
		if ($result && $result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$name = $this->conn->real_escape_string($row['name']);

		} else {
			// Handle error if the brand with the given $id is not found
			$resp['status'] = 'failed';
			$resp['error'] = 'Brand not found.';
			return json_encode($resp);
		}

		// Actual deletion of the category
		$del = $this->conn->query("DELETE FROM `brands` WHERE id = '{$id}'");

		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Brand successfully deleted.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
	} else {
		// Handle error if $id is not set
		$resp['status'] = 'failed';
		$resp['error'] = 'Brand ID not set.';
	}

	return json_encode($resp);
}

function archive_unit(){
	$id = $_POST['id']; 
	// Check if status is 0
	$get = $this->conn->query("SELECT * FROM units WHERE id = '{$id}'");
		if(!$get){
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
		}
		
		if($get->num_rows > 0){
			$resp = $get->fetch_array();
			
			$del = $this->conn->query("UPDATE `units` SET status = 0 WHERE id = '{$id}'");
			
			if($del){
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success',"Unit data successfully moved to Archive Section.");
				
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = 'Error: Contact developer.';
		}
		
		return json_encode($resp);
	}

	
function restore_unit(){
	$id = $_POST['id']; 
	// Check if status is 0
	$get = $this->conn->query("SELECT * FROM units WHERE id = '{$id}'");
		if(!$get){
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
		}
		
		if($get->num_rows > 0){
			$resp = $get->fetch_array();
			
			$del = $this->conn->query("UPDATE `units` SET status = 1 WHERE id = '{$id}'");
			
			if($del){
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success',"Unit data successfully moved to Unit Section.");
				
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = 'Error: Contact developer.';
		}
		
		return json_encode($resp);
	}

function archive_brand(){
	$id = $_POST['id']; 
	// Check if status is 0
	$get = $this->conn->query("SELECT * FROM brands WHERE id = '{$id}'");
		if(!$get){
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
		}
		
		if($get->num_rows > 0){
			$resp = $get->fetch_array();
			
			$del = $this->conn->query("UPDATE `brands` SET status = 0 WHERE id = '{$id}'");
			
			if($del){
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success',"Brand data successfully moved to Archive Section.");
				
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = 'Error: Contact developer.';
		}
		
		return json_encode($resp);
	}

	
function restore_brand(){
	$id = $_POST['id']; 
	// Check if status is 0
	$get = $this->conn->query("SELECT * FROM brands WHERE id = '{$id}'");
		if(!$get){
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
		}
		
		if($get->num_rows > 0){
			$resp = $get->fetch_array();
			
			$del = $this->conn->query("UPDATE `brands` SET status = 1 WHERE id = '{$id}'");
			
			if($del){
				$resp['status'] = 'success';
				$this->settings->set_flashdata('success',"Brand data successfully moved to Brand Section.");
				
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = 'Error: Contact developer.';
		}
		
		return json_encode($resp);
	}
	
} //main close , wag idelete , road to syntax error




$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {	
	case 'terminate_all':
		echo $Master->terminate_all();
	break;
	case 'backup_system':
		echo $Master->backup_system();
	break;
	case 'backup_database':
		echo $Master->backup_database();
	break;
	case 'restore_database':
		echo $Master->restore_database();
	break;
	case 'save_supplier':
		echo $Master->save_supplier();
	break;
	case 'delete_supplier':
		echo $Master->delete_supplier();
	break;
	case 'archive_supplier':
		echo $Master->archive_supplier();
	break;
	case 'restore_supplier':
		echo $Master->restore_supplier();
	break;
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'archive_category':
		echo $Master->archive_category();
	break;
	case 'restore_category':
		echo $Master->restore_category();
	break;
	case 'save_expense':
		echo $Master->save_expense();
	break;
	case 'delete_expense':
		echo $Master->delete_expense();
	break;
	case 'save_item':
		echo $Master->save_item();
	break;
	case 'delete_item':
		echo $Master->delete_item();
	break;
	case 'archive_item':
		echo $Master->archive_item();
	break;
	case 'restore_item':
		echo $Master->restore_item();
	break;
	case 'delete_supplier_product':
        echo $Master->delete_supplier_product();
    break;
	case 'update_supplier_product':
        echo $Master->update_supplier_product();
    break;
	case 'update_pu':
        echo $Master->update_pu();
    break;
	case 'save_po':
		echo $Master->save_po();
	break;
	case 'archive_po':
		echo $Master->archive_po();
	break;
	case 'delete_po':
		echo $Master->delete_po();
	break;
	case 'restore_po':
		echo $Master->restore_po();
	break;
	case 'save_receiving':
		echo $Master->save_receiving();
	break;
	case 'delete_receiving':
		echo $Master->delete_receiving();
	break;
	case 'save_sreturn':
		echo $Master->save_sreturn();
	break;
	case 'delete_sreturn':
		echo $Master->delete_sreturn();
	break;
	case 'save_sale':
		echo $Master->save_sale();
	break;
	case 'archive_sale':
		echo $Master->archive_sale();
	break;
	case 'restore_sale':
		echo $Master->restore_sale();
	break;
	case 'delete_sale':
		echo $Master->delete_sale();
	break;
	case 'reset_data':
		echo $Master->reset_data();
	break;
	case 'save_vendor':
		echo $Master->save_vendor();
	break;
	case 'delete_vendor':
		echo $Master->delete_vendor();
	break;
	case 'save_discount':
		echo $Master->save_discount();
	break;
	case 'delete_discount':
		echo $Master->delete_discount();
	break;
	case 'archive_discount':
		echo $Master->archive_discount();
	break;
	case 'restore_discount':
		echo $Master->restore_discount();
	break;
	case 'save_unit':
		echo $Master->save_unit();
	break;
	case 'delete_unit':
		echo $Master->delete_unit();
	break;
	case 'archive_unit':
		echo $Master->archive_unit();
	break;
	case 'restore_unit':
		echo $Master->restore_unit();
	break;
	case 'save_brand':
		echo $Master->save_brand();
	break;
	case 'delete_brand':
		echo $Master->delete_brand();
	break;
	case 'archive_brand':
		echo $Master->archive_brand();
	break;
	case 'restore_brand':
		echo $Master->restore_brand();
	break;
	
	default:
		// echo $sysset->index();
		break;
}

