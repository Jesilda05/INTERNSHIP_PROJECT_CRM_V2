<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

// Check for user authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    //if not redirect to login
    header('Location: ../login.php');
    exit();
}

$err = "";
$sucess="";
//filter_var is used to validate the input
//trim is used to remove whitespaces
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cat = filter_var(trim($_POST['category']), FILTER_SANITIZE_STRING);
    $det = filter_var(trim($_POST['details']), FILTER_SANITIZE_STRING);
    $cust_id = (int)$_SESSION['user_id'];

    

    $quer = "SELECT id FROM customers WHERE id = ?";
    $pre_stmt = $conn->prepare($quer);
    $pre_stmt->bind_param('i', $cust_id);
    $pre_stmt->execute();
    $pre_stmt->store_result();

    if ($pre_stmt->num_rows === 0) {
        die('Error: Customer ID does not exist.');
    }
    $pre_stmt->close();

    if (empty($cat) || empty($det)) {
        $err = 'PLEASE FILL IN ALL FIELDS';
    }
    elseif (!preg_match('/^[a-zA-Z0-9\s.,!?]+$/', $det)) {
       $err="Details can only contain letters, numbers, spaces, and basic punctuation.";
    } else {
                //prepare statemnets for enchanced security

        
        $sql = "INSERT INTO quotations (customer_id, category, details, created_at) VALUES (?, ?, ?, NOW())";
        $prestmt = $conn->prepare($sql);


        $prestmt->bind_param('iss', $cust_id, $cat, $det);

        // Execute the prepared statement
        if ($prestmt->execute()) {
            $sucess='Your Quotation has been created successfully!';
        } else {
            echo "Error: " . $prestmt->error;
            error_log("The following error occurred while inserting the quotation: " . $prestmt->error);
        }

        $prestmt->close();
    }
}
?>
<?php include('header2.php'); ?>
<!DOCTYPE html>
<html>
    <head><link rel="stylesheet" href="../assets/css/cust_style.css">
    </head>
<h3>Create Quotation</h3>
<div class="form-container">
   
    <?php if (!empty($err)): ?>
            <div class="error-message"><?php echo $err; ?></div>
        <?php endif; ?>
        <?php if (!empty($sucess)): ?>
            <div class="success-message"><?php echo $sucess; ?></div>
        <?php endif; ?>


    <form action="create_quotation2.php" method="POST">
        <strong>Category:</strong>
        <select name="category" id="category" required>
            <option value="">Select a category</option>
            <option value="Fiction">Fiction</option>
            <option value="Non-Fiction">Non-Fiction</option>
        </select><br>

        <strong>Details:</strong>
        <textarea name="details" id="details" required></textarea><br>

        <button type="submit">Submit</button>
    </form>
</div>

</html>
<?php include('footer.php'); ?>