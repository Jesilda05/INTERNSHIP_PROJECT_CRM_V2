<?php
session_start(); 
include('../mainconn/db_connect.php'); 
include('../mainconn/authentication.php'); 
//this will check for user authentication

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../login.php');
    exit();
}

$err = "";
$sucess="";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //filter_var is used for validating and sanitizing inputs
//trim gets rid of whitespaces
    $cat = filter_var(trim($_POST['category']), FILTER_SANITIZE_STRING);
    $sub = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
    $desc = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
    //user_id type casted to int

    $cust_id = (int)$_SESSION['user_id'];

    if (empty($cat) || empty($sub) || empty($desc)) {
        $err = "Please fill in all fields.";
    } elseif (!preg_match('/^[a-zA-Z0-9\s.,!?]+$/', $desc)) {
        $err = "Details can only contain letters, numbers, spaces, and basic punctuation.";
    }
    elseif (!preg_match('/^[a-zA-Z\s.,!?]+$/', $sub)) {
        $err = "Feedback can only contain letters, spaces, and basic punctuation.";
    }else {
                //prepare statemnets for enchanced security

        $sql = "INSERT INTO tickets (customer_id, category, subject, description, created_at) VALUES (?,?,?,?, NOW())";
        $prestmt = $conn->prepare($sql);

        if ($prestmt) {
            $prestmt->bind_param('isss', $cust_id, $cat, $sub, $desc);

            if ($prestmt->execute()) {
                $sucess= 'Your ticket has been created successfully!';
            } else {
                error_log("Error occurred while creating ticket: " . $prestmt->error);
            }

            $prestmt->close();
        } else {
            error_log("The statement could not be prepared due to the following error: " . $conn->error);
        }
    }
}
?>

<?php include('header2.php'); ?>

<h3>Create Ticket</h3>

<?php if (!empty($err)): ?>
            <div class="error-message"><?php echo $err; ?></div>
        <?php endif; ?>
        <?php if (!empty($sucess)): ?>
            <div class="success-message"><?php echo $sucess; ?></div>
        <?php endif; ?>

<form action="create_ticket2.php" method="POST">
    Category:
    <select name="category" id="category" required>
        <option value="">Select a category</option>
        <option value="Fiction">Fiction</option>
        <option value="Non-Fiction">Non-Fiction</option>
    </select><br>

    Subject:
    <input type="text" name="subject" id="subject" required><br>

    Description:
    <textarea name="description" id="description" required></textarea><br>

    <button type="submit">Submit Ticket</button>
</form>

<?php include('footer.php'); ?>
