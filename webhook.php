<?php
define("PARTNER_ID", "045");
define("API_KEY", "5888a5b2-74be-44fb-b54a-2686c981d17c");
define("USERNAME", "agadajerry");
define("PASSWORD", "idoko400");



include "connection.php";
header("Content-Type: application/json");


    $username = $_GET['username']; 
    $password = $_GET['password'];   
    // file_put_contents('username.txt', print_r($username. $password, true));

    if ($username !== USERNAME || $password !== PASSWORD) {
        echo json_encode(['msg' => 'Unauthorized']);
        http_response_code(401);
        exit();
    }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['FullData'])) {
        
        $data = $input['FullData'];
        

        
        // Create result array
        $resultArray = [
            'LastName' => $data['LastName'] ?? null,
            'LevelOfAccount' => $data['LevelOfAccount'] ?? null,
            'lgaOfOrigin' => $data['lgaOfOrigin'] ?? null,
            'lgaOfResidence' => $data['lgaOfResidence'] ?? null,
            'maritalStatus' => $data['maritalStatus'] ?? null,
            'message' => $data['message'] ?? null,
            'MiddleName' => $data['MiddleName'] ?? null,
            'nameOnCard' => $data['nameOnCard'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'nin' => $data['nin'] ?? null,
            'PhoneNumber1' => $data['PhoneNumber1'] ?? null,
            'PhoneNumber2' => $data['PhoneNumber2'] ?? null,
            'registrationDate' => $data['registrationDate'] ?? null,
            'residentialAddress' => $data['residentialAddress'] ?? null,
            'stateOfOrigin' => $data['stateOfOrigin'] ?? null,
            'stateOfResidence' => $data['stateOfResidence'] ?? null,
            'success' => $data['success'] ?? null,
            'title' => $data['title'] ?? null,
            'watchListed' => $data['watchListed'] ?? null,
            'FullName' => $input['FullName'] ?? null,
            'Gender' => $data['Gender'] ?? null,
            'IDNumber' => $input['IDNumber'] ?? null,
            'IDNumberPreviouslyRegistered' => $input['IDNumberPreviouslyRegistered'] ?? null,
            'IDType' => $input['IDType'] ?? null,
            'ConfidenceValue' => $input['ConfidenceValue'] ?? null,
            'Actions' => $input['Actions'] ?? null,
            'signature' => $input['signature'] ?? null,
            'timestamp' => $input['timestamp'] ?? null
        ];

        // If the success flag is false

        if ($input['FullData']['success']) {

            file_put_contents('input.txt', print_r($input, true));

                // check the validity of the signature

            if(confirmSignature($input['signature'],
            $input['timestamp'],
             PARTNER_ID, API_KEY) === false) {
               echo json_encode(['msg' => 'uNAUTHORIZED']);
               http_response_code(401);
               exit();
           }


            // Check if user exists based on iDNumber(BVN)
            $idNumber = $conn->real_escape_string($input['IDNumber']);

            $findUser = "SELECT * FROM customer_smile_id WHERE cust_id = '$idNumber'";
            $result = $conn->query($findUser);

            $jsonResult = json_encode($resultArray);

            // If user exists, delete old record and insert a new one
            if ($result->num_rows > 0) {

                $conn->query("DELETE FROM customer_smile_id WHERE cust_id='$idNumber'");

                $sql2 = "INSERT INTO customer_smile_id (cust_id, bio_data) VALUES ('$idNumber', '$jsonResult')";
                $result2 = $conn->query($sql2);
            } else {
                // Insert new user if not found
                $sql = "INSERT INTO customer_smile_id (cust_id, bio_data) VALUES ('$idNumber', '$jsonResult')";
                $conn->query($sql);
                $conn->close();
            }
        }

        // Send the response
        echo json_encode(['msg' => 'Successfully accessed!']);
        http_response_code(200);
    } else {
        // Invalid input
        echo json_encode(['msg' => 'Invalid input']);
        http_response_code(400);
    }
} else {
    // Method not allowed
    echo json_encode(['msg' => 'Method not allowed']);
    http_response_code(405);
}



function confirmSignature( string $receivedSignature, string $receivedTimestamp, string $partnerId, string $apiKey): bool
{

    $message = $receivedTimestamp . $partnerId . "sid_request";
    $generatedSignature = base64_encode(string: hash_hmac('sha256', $message, $apiKey, true));

    return $generatedSignature === $receivedSignature;
}




