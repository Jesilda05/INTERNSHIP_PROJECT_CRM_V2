<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

// Checking for user authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../login.php');
    exit();
}

// user_id type casted to int
$cust_id = (int)$_SESSION['user_id'];

// check if the feedback exits
if (isset($_GET['id'])) {
    if (filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $id = (int)$_GET['id'];

//select query
        $query = "SELECT * FROM tickets WHERE id=? AND customer_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $id, $cust_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $ticket = $result->fetch_assoc();

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $cat = filter_var(trim($_POST['category']), FILTER_SANITIZE_STRING);
                    $sub = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
                    $desc = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);



                    if (empty($cat) || empty($sub)|| empty($desc)) {
                        $error = "All fields are required.";
                    }
                    elseif (!preg_match('/^[a-zA-Z0-9\s.,!?]+$/', $desc)) {
                        $error = "Details can only contain letters, numbers, spaces, and basic punctuation.";
                    }
                    elseif (!preg_match('/^[a-zA-Z\s.,!?]+$/', $sub)) {
                        $error = "Feedback can only contain letters, spaces, and basic punctuation.";
                    } else {
                        $upd_sql = "UPDATE tickets SET category = ?, subject = ?,description = ? WHERE id = ? AND customer_id = ?";
                        $stmt = $conn->prepare($upd_sql);
                        $stmt->bind_param('sssii', $cat, $sub,$desc, $id, $cust_id);

                        if ($stmt->execute()) {
                            $success = 'ticket updated successfully.';
                        } else {
                            $error = 'Error updating ticket: ' . $stmt->error;
                        }
                    }
                }
            } else {
                // Handle case where feedback not found
                $error = 'tickets not found.';
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

<form action="<?php echo isset($id) ? $_SERVER['PHP_SELF'] . '?id=' . $id : 'create_ticket2.php'; ?>" method="POST">
    <?php if (isset($id)) : ?>
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
    <?php endif; ?>
    <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($cust_id); ?>">
    
    <label for="category">Category:</label>
    <select name="category" id="category" required>
        <option value="">Select a category</option>
        <option value="Fiction" <?php echo isset($ticket['category']) && $ticket['category'] === 'Fiction' ? 'selected' : ''; ?>>Fiction</option>
        <option value="Non-Fiction" <?php echo isset($ticket['category']) && $ticket['category'] === 'Non-Fiction' ? 'selected' : ''; ?>>Non-Fiction</option>
    </select><br>
    
    <label for="subject">Subject:</label>
    <input type="text" name="subject" id="subject" value="<?php echo isset($ticket['subject']) ? htmlspecialchars($ticket['subject']) : ''; ?>" required><br>

    <label for="description">Description:</label>
    <textarea name="description" id="description" required><?php echo isset($ticket['description']) ? htmlspecialchars($ticket['description']) : ''; ?></textarea><br>
    
    <button type="submit"><?php echo isset($id) ? 'Update' : 'Submit'; ?></button>
</form>

<?php include('footer.php'); ?>