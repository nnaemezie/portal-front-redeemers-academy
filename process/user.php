<?php

	//output msg variable
	$msg = '';

	//school checker
	function checkSchool($schoolType){
		if ($schoolType == 'trihs') {
			require_once('config/configTrihs.php');
		}elseif($schoolType == 'traco'){
			require_once('config/configTraco.php');
		}
	}

	//login handler
	if (isset($_POST['login'])) {

		$schoolType = $_POST['schoolType'];
		checkSchool($schoolType);
		require_once('database.php');

		$username = $database->escape_string($_POST['username']);
		$password = md5($database->escape_string($_POST['password']));
		$schoolType = $database->escape_string($_POST['schoolType']);

		$employee_data = " SELECT * FROM employee_data where admno = '$username' && password = '$password'  ";
		$student_data = " SELECT * FROM student_data where admno = '$username' && password = '$password'  ";

		$result_employee = $database->query($employee_data);
		$result_student = $database->query($student_data);

		if ($result_employee->num_rows == 1) {
			$row = $result_employee->fetch_assoc();
			$_session['sd'] = $row;
			$msg = 'success';
		}elseif ($result_student->num_rows == 1) {
			$row = $result_student->fetch_assoc();
			$_session['sd'] = $row;
			$msg = 'success';
		}else{
			$msg = 'login error ! username or password incorrect.';
		}

		$data = array();
		$data['msg'] = $msg;
		$data['schoolType'] = $schoolType;

		echo json_encode($data);
	}

	//loads class to result checking form
	if (isset($_POST['loadClass'])) {

		$data = array();
		$schoolType = $_POST['schoolType'];
		checkSchool($schoolType);
		require_once('database.php');

		$sql = "SELECT * FROM class";
		$result = $database->query($sql);
		if ($result->num_rows > 0) {
			$output = '<option value="">Please Select Class</option>';
			while ($row = $result->fetch_assoc()) {
				$output .= '<option value="'.$row["level"].$row["name"].'">'.$row["level"].$row["name"].'</option>';
			}
			$data['class'] = $output;
		}

		$sql = "SELECT * FROM term ORDER BY session DESC";
		$result = $database->query($sql);
		if ($result->num_rows > 0) {
			$output = '<option value="">Please Select Term</option>';
			while ($row = $result->fetch_assoc()) {
				$output .= '<option value="'.$row["termtype"].' '.$row["session"].'">'.$row["termtype"].' '.$row["session"].'</option>';
			}
			$data['term'] = $output;
		}
		echo json_encode($data);
	}

	//check's for scratch card validity
	if (isset($_POST['pin'])) {
		$data = array();

		$schoolType = $_POST['schoolType'];
		$admno		 = $_POST['admno'];
		$class		 = $_POST['myclass'];
		$term 			 = $_POST['term'];
		$pin				 = $_POST['pin'];

		checkSchool($schoolType);
		require_once('database.php');

		$sql = "SELECT * FROM card WHERE pin = '$pin' LIMIT 1 ";
		$result = $database->query($sql);
		$row = $result->fetch_assoc();

		if ($result->num_rows == 1) {
			if ($row['term'] != '' || $row['matchid'] != '' || $row['class'] != '') {

				if ($row['matchid'] == $admno && $row['term'] == $term && $row['class'] == $class) {
					$data['output'] = "used by me";
				}else{
					$data['output'] = "used by another";
				}

			}elseif($row['term'] == '' && $row['matchid'] == ''){
				$data['output'] = "used by none";
			}
		}else{
			$data['output'] = "invalid";
		}

		echo json_encode($data);
	}

	//check's for result availability
	if (isset($_POST['cardAvai'])) {
		$data = array();

		$schoolType = $_POST['rschool'];
		$admno		 = $_POST['radmno'];
		$class		 	 = $_POST['rclass'];
		$term 			 = $_POST['rterm'];
		$pin				 = $_POST['rpin'];

		checkSchool($schoolType);
		require_once('database.php');

		$sql = 'SELECT * FROM published_result WHERE class = "'.$class.'" AND admno = "'.$admno.'" AND term = "'.$term.'" ';
		$result = $database->query($sql);
		$row = $result->fetch_assoc();

		if ($result->num_rows > 0) {
			$data['output'] = "avaliable";
		}else{
			$data['output'] = "not avaliable";
		}

		echo json_encode($data);
	}