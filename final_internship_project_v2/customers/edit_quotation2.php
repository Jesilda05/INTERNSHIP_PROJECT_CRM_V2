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
        $query = "SELECT * FROM quotations WHERE id=? AND customer_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $id, $cust_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $quotation = $result->fetch_assoc(); // Renamed to quotation for clarity

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                
                    $cat = filter_var(trim($_POST['category']), FILTER_SANITIZE_STRING);
                    $det = filter_var(trim($_POST['details']), FILTER_SANITIZE_STRING);


                    if (empty($cat) || empty($det)) {
                        $error = "All fields are required.";
                    }
                    elseif (!preg_match('/^[a-zA-Z0-9\s.,!?]+$/', $det)) {
                        $error = "Details can only contain letters, numbers, spaces, and basic punctuation.";
                    }  else {
                        $upd_sql = "UPDATE quotations SET category = ?, details = ? WHERE id = ? AND customer_id = ?";
                        $stmt = $conn->prepare($upd_sql);
                        $stmt->bind_param('ssii', $cat, $det, $id, $cust_id);

                        if ($stmt->execute()) {
                            $success = 'Quotation updated successfully.';
                        } else {
                            $error = 'Error updating quotation: ' . $stmt->error;
                        }
                    }
                
                }
            } else {
                $error = 'Quotation not found.';
            }
            $stmt->close();
        } else {
            error_log("Error executing query: " . $stmt->error);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['id'])) {
}
?>

<?php include('header2.php'); ?>

<h3><?php echo isset($id) ? 'Update Quotation' : 'Create Quotation'; ?></h3>

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
        <option value="Fiction" <?php echo isset($quotation['category']) && $quotation['category'] === 'Fiction' ? 'selected' : ''; ?>>Fiction</option>
        <option value="Non-Fiction" <?php echo isset($quotation['category']) && $quotation['category'] === 'Non-Fiction' ? 'selected' : ''; ?>>Non-Fiction</option>
    </select><br>    <label for="details">Details:</label>
    <textarea name="details" id="details" required><?php if (isset($quotation)) echo $quotation['details']; ?></textarea>
    <button type="submit"><?php echo isset($id) ? 'Update' : 'Submit'; ?></button>
</form>

<?php include('footer.php'); ?>
