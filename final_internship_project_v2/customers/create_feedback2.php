<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');
//this will checks for user authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    //this will checks for user authentication
    header('Location: ../login.php');
    exit();
}

$err = "";
$sucess="";

//filter_var is used for validating and sanitizing inputs
//trim gets rid of whitespaces
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cat = filter_var(trim($_POST['category']), FILTER_SANITIZE_STRING);
    $feed = filter_var(trim($_POST['feedback']), FILTER_SANITIZE_STRING);
    //user_id type casted to int
    $cust_id = (int)$_SESSION['user_id'];
     
    if (empty($cat) || empty($feed)) {
        $err = 'PLEASE FILL IN ALL FIELDS';
    }
    elseif (!preg_match('/^[a-zA-Z\s.,!?]+$/', $feed)) {
        $err = "Feedback can only contain letters, spaces, and basic punctuation.";
    }
     else {
        //prepare statemnets for enchanced security
        $sql = "INSERT INTO feedback (customer_id, category, feedback, created_at) VALUES (?, ?, ?, NOW())";
        $prestmt = $conn->prepare($sql);
        $prestmt->bind_param('iss', $cust_id, $cat, $feed);

        if ($prestmt->execute()) {
            $sucess= 'Your Feedback has been submitted successfully!';
            
        } else {
            
            error_log("The following error occured while submitting feedback: " . $prestmt->error);
        }

        $prestmt->close();
    }
}
?>
        <?php include('header2.php'); ?>

        <h2>Create Feedback</h2>

        <?php if (!empty($err)): ?>
            <div class="error-message"><?php echo $err; ?></div>
        <?php endif; ?>
        <?php if (!empty($sucess)): ?>
            <div class="success-message"><?php echo $sucess; ?></div>
        <?php endif; ?>
<form action="create_feedback2.php" method="POST">
    <strong>Category:</strong>
    <select name="category" id="category" required>
        <option value="">Select a category</option>
        <option value="Fiction">Fiction</option>
        <option value="Non-Fiction">Non-Fiction</option>
    </select><br>

    <strong>Feedback:</strong>
    <textarea name="feedback" id="feedback" required></textarea><br>

    <button type="submit">Submit Feedback</button>
</form>

<?php include('footer.php'); ?>
    

