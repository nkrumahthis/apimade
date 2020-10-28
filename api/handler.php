<?php
    $conn = new mysqli('localhost','root', '', 'restAPI');

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['id'])) {
            $id = $conn->real_escape_string($_GET['id']);
            $sql = $conn->query("SELECT name, age FROM customers WHERE id='$id'");
            $data = $sql->fetch_assoc();
        } else {
            $data = array();
            $sql = $conn->query("SELECT name, age FROM customers");
            while ($d = $sql->fetch_assoc())
                $data[] = $d;
        }

        exit(json_encode($data));
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['name']) && isset($_POST['age'])) {
            $name = $conn->real_escape_string($_POST['name']);
            $age = $conn->real_escape_string($_POST['age']);

            $conn->query("INSERT INTO customers (name,age,addedOn) VALUES ('$name', '$age', NOW())");
            exit(json_encode(array("status" => 'success')));
        } else
            exit(json_encode(array("status" => 'failed', 'reason' => 'Check Your Inputs')));
    } else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        if (!isset($_GET['id']))
            exit(json_encode(array("status" => 'failed', 'reason' => 'Check Your Inputs')));

        $customerID = $conn->real_escape_string($_GET['id']);
        $data = urldecode(file_get_contents('php://input'));

        if (strpos($data, '=') !== false) {
            $allPairs = array();
            $data = explode('&', $data);
            foreach($data as $pair) {
                $pair = explode('=', $pair);
                $allPairs[$pair[0]] = $pair[1];
            }

            if (isset($allPairs['name']) && isset($allPairs['age'])) {
                $conn->query("UPDATE customers SET age='".$allPairs['age']."', name='".$allPairs['name']."' WHERE id='$customerID'");
            } else if (isset($allPairs['name'])) {
                $conn->query("UPDATE customers SET name='".$allPairs['name']."' WHERE id='$customerID'");
            } else if (isset($allPairs['age'])) {
                $conn->query("UPDATE customers SET age='".$allPairs['age']."' WHERE id='$customerID'");
            } else
                exit(json_encode(array("status" => 'failed', 'reason' => 'Check Your Inputs')));

            exit(json_encode(array("status" => 'success')));
        } else
            exit(json_encode(array("status" => 'failed', 'reason' => 'Check Your Inputs')));
    } else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        if (!isset($_GET['id']))
            exit(json_encode(array("status" => 'failed', 'reason' => 'Check Your Inputs')));

        $customerID = $conn->real_escape_string($_GET['id']);
        $conn->query("DELETE FROM customers WHERE id='$customerID'");
        exit(json_encode(array("status" => 'success')));
    }
?>