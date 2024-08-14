<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

// Check user authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../login.php');
    exit();
}

// User ID type casted to int
$cust_id = (int)$_SESSION['user_id'];

// Check if updating an existing ticket
if (isset($_GET['id'])) {
    if (filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $id = (int)$_GET['id'];

        // Corrected SQL query to select all columns
        $query = "SELECT * FROM feedback WHERE id=? AND customer_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $id, $cust_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $feedback = $result->fetch_assoc();

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $cat = filter_var(trim($_POST['category']), FILTER_SANITIZE_STRING);
                    $text = filter_var(trim($_POST['feedback']), FILTER_SANITIZE_STRING);

                    if (empty($cat) || empty($text)) {
                        $error = "All fields are required.";
                    } elseif (!preg_match('/^[a-zA-Z\s.,!?]+$/', $text)) {
                        $error = "Feedback can only contain letters, spaces, and basic punctuation.";
                    }else {
                        $upd_sql = "UPDATE feedback SET category = ?, feedback = ? WHERE id = ? AND customer_id = ?";
                        $stmt = $conn->prepare($upd_sql);
                        $stmt->bind_param('ssii', $cat, $text, $id, $cust_id);

                        if ($stmt->execute()) {
                            $success = 'Feedback updated successfully.';
                        } else {
                            $error = 'Error updating feedback: ' . $stmt->error;
                        }
                    }
                }
            } else {
                // Handle case where feedback not found
                $error = 'Feedback not found.';
            }
            $stmt->close();
        } else {
            error_log("Error executing query: " . $stmt->error);
        }
    }
}

// Handle creating a new ticket
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['id'])) {
    // ... code to create a new ticket ...
}

?>

<?php include('header2.php'); ?>

<h3><?php echo isset($id) ? 'Update Ticket' : 'Create Ticket'; ?></h3>

<?php if (isset($error)) : ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>
<?php if (isset($success)) : ?>
    <div class="success"><?php echo $success; ?></div>
<?php endif; ?>

<form action="<?php echo isset($id) ? $_SERVER['PHP_SELF'] . '?id=' . $id : 'create_quotation2.php'; ?>" method="POST">
    <?php if (isset($id)) : ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>">
    <?php endif; ?>
    <input type="hidden" name="customer_id" value="<?php echo $cust_id; ?>">
    <label for="category">Category:</label>
    <select name="category" id="category" required>
        <option value="">Select a category</option>
        <option value="Fiction" <?php echo isset($feedback['category']) && $feedback['category'] === 'Fiction' ? 'selected' : ''; ?>>Fiction</option>
        <option value="Non-Fiction" <?php echo isset($feedback['category']) && $feedback['category'] === 'Non-Fiction' ? 'selected' : ''; ?>>Non-Fiction</option>
    </select><br> 
    <strong>feedback:</label>
    <textarea name="feedback" id="feedback" required><?php if (isset($feedback)) echo $feedback['feedback']; ?></textarea>
    <button type="submit"><?php echo isset($id) ? 'Update' : 'Submit'; ?></button>
</form>


<?php include('footer.php'); ?>
