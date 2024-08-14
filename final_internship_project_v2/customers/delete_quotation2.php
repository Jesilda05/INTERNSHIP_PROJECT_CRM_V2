<?php

session_start();
include("../mainconn/db_connect.php");
include("../mainconn/authentication.php");
//checking for user authentication
if (!isset($_SESSION["user_id"]) || $_SESSION['role'] !== 'Customer') {
   //if not redirect to login
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    if (filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $id = (int)$_GET['id'];
        $cust_id = (int)$_SESSION['user_id'];
       
        $sql = "DELETE FROM quotations WHERE id = ? AND customer_id = ?";
        $prestmt = $conn->prepare($sql);
        //checking if sql query was prepared correctly
        if ($prestmt) {
            $prestmt->bind_param('ii', $id, $cust_id);
            if ($prestmt->execute()) {
                echo 'Your quotation has been deleted successfully!';
            } else {
                error_log("Error deleting quotation: " . $prestmt->error);
                  }
            $prestmt->close();
        } else {
            error_log("Error preparing statement: " . $conn->error);
        }
    }
    else{
        echo "INVALID ID ";
    }
}
else{
    echo "id not set";
}




header("Location: manage_quotations2.php");
exit();
?>
