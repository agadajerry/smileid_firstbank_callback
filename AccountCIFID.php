<?php
	define("AppKey", "ae819f1e854c4d06af2bf4b68f32493a");
	define("URL", "https://fi-bridge-dev.fbn-devops-dev-asenv.appserviceenvironment.net/api/v1/account/get-bvn-with-account-number");
	define("ID", "FGT");



	if ($_SERVER['REQUEST_METHOD'] === "POST") {
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	
		$account_number = $_POST['acc'] ?? NULL;
		$requestId = $_POST['requestId'] ?? NULL;
		$countryId = $_POST['countryId'] ?? NULL;
		$appKey = $_POST['AppKey'] ?? NULL;
		$appId = $_POST['AppId'] ?? NULL;
		$id = $_POST['id'] ?? NULL;
	
		// echo "Received POST data:\n";
		// print_r($_POST);
	
		if ($id == ID) {
			getAccountCIFID($account_number, $requestId, $countryId, $appKey, $appId);
		} else {
			echo "Incorrect parameter";
		}
	
	} else {
		echo "Incorrect request method";
	}

	function getAccountCIFID($account_number, $requestId, $countryId, $appKey, $appId) {
		if (empty($account_number) || empty($requestId) || empty($countryId)) {
			echo "Invalid input parameter";
			return;
		}


		$curl = curl_init(URL);
		$curl_req_data = [
			'AccountNumber' => $account_number,
			'RequestId' => $requestId,
			'CountryId' => $countryId,
		];

		
		$json_data = json_encode($curl_req_data);
		if (json_last_error() !== JSON_ERROR_NONE) {
			echo "JSON Encoding Error: " . json_last_error_msg();
			return;
		}

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($curl, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Accept: application/json',
			'Connection: Keep-Alive',
			"AppKey: $appKey",
			"AppId: $appId"
		]);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

		$curl_response = curl_exec($curl);
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if (curl_errno($curl)) {
			echo "cURL Error: " . curl_error($curl);
		} elseif ($http_status !== 200) {
			echo "HTTP Status: $http_status\nResponse: $curl_response";
		} else {
			echo "Response:\n$curl_response";
		}

		curl_close($curl);
	}