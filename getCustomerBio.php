<?php

include "connection.php";
header("Content-Type: application/json");


try {

    if (isset($_GET['custId'])) {


        $custId = $_GET['custId'];

        $stmt = $conn->prepare("SELECT * FROM customer_smile_id WHERE cust_id = ?");
        $stmt->bind_param("s", $custId); // "s" means the parameter is a string
        $stmt->execute();

        // Fetch the result
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode($data, JSON_PRETTY_PRINT);

            //delete the information of the customer from db
            $conn->query("DELETE FROM customer_smile_id WHERE cust_id='$custId'");

        } else {
            echo json_encode([]);
        }
        $stmt->close();
        $conn->close();

    } else {
        echo json_encode([
            "error" => "Missing query parameters!"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(["server_error" => "Server error occured"], JSON_PRETTY_PRINT);
}

?>