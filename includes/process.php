<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['add_expense'])) {
    $item = mysqli_real_escape_string($conn, $_POST['item']);
    $cost = mysqli_real_escape_string($conn, $_POST['cost']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $cat_id = mysqli_real_escape_string($conn, $_POST['category_id']);

    if (empty($cat_id)) {
        header("Location: ../dashboard/?msg=error_cat");
        exit();
    }

    $sql = "INSERT INTO expenses (user_id, item_name, cost, expense_date, category_id) 
            VALUES ('$user_id', '$item', '$cost', '$date', '$cat_id')";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../dashboard/?msg=added");
    } else {
        die("Error adding expense: " . mysqli_error($conn));
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $sql = "DELETE FROM expenses WHERE id=$id AND user_id=$user_id";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../dashboard/?msg=deleted");
    } else {
        die("Error deleting expense: " . mysqli_error($conn));
    }
}

if (isset($_POST['update_expense'])) {
    $id = intval($_POST['id']);
    $item = mysqli_real_escape_string($conn, $_POST['item']);
    $cost = mysqli_real_escape_string($conn, $_POST['cost']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $cat_id = mysqli_real_escape_string($conn, $_POST['category_id']);

    $sql = "UPDATE expenses 
            SET item_name='$item', cost='$cost', expense_date='$date', category_id='$cat_id' 
            WHERE id=$id AND user_id=$user_id";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../dashboard/?msg=updated");
    } else {
        die("Error updating record: " . mysqli_error($conn));
    }
}

if (isset($_POST['add_category'])) {
    $cat_name = mysqli_real_escape_string($conn, trim($_POST['new_cat_name']));

    $check_query = "SELECT cat_id FROM categories WHERE (user_id = '$user_id' OR user_id IS NULL) AND cat_name = '$cat_name'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        header("Location: ../dashboard/?msg=cat_exists");
        exit();
    }

    $sql = "INSERT INTO categories (cat_name, user_id) VALUES ('$cat_name', '$user_id')";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../dashboard/?msg=cat_added");
    } else {
        die("Error creating category: " . mysqli_error($conn));
    }
}

if (isset($_GET['delete_category'])) {
    $cat_id = intval($_GET['delete_category']);

    $check_query = "SELECT id FROM expenses WHERE category_id = $cat_id AND user_id = $user_id";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        header("Location: ../dashboard/?msg=cat_in_use");
        exit();
    }

    $sql = "DELETE FROM categories WHERE cat_id = $cat_id AND user_id = $user_id";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../dashboard/?msg=cat_deleted");
    } else {
        die("Error deleting category: " . mysqli_error($conn));
    }
}

function can_manage($conn, $target_id)
{
    $my_role = $_SESSION['role'];

    if ($my_role === 'admin') return true;

    $t_query = mysqli_query($conn, "SELECT role FROM users WHERE user_id = $target_id");
    if (mysqli_num_rows($t_query) === 0) return false;

    $target_role = mysqli_fetch_assoc($t_query)['role'];

    if ($target_role === 'admin') return false;

    if ($target_role === 'co-admin') return false;

    return true;
}

if (isset($_GET['approve'])) {
    $uid = intval($_GET['approve']);
    if (!can_manage($conn, $uid)) die("Access Denied: You do not have permission to manage this user.");

    mysqli_query($conn, "UPDATE users SET status='active' WHERE user_id=$uid");
    header("Location: ../dashboard/?msg=user_approved");
}

if (isset($_GET['suspend'])) {
    $uid = intval($_GET['suspend']);
    if (!can_manage($conn, $uid)) die("Access Denied: You do not have permission to manage this user.");

    mysqli_query($conn, "UPDATE users SET status='suspended' WHERE user_id=$uid");
    header("Location: ../dashboard/?msg=user_suspended");
}



if (isset($_GET['make_co_admin'])) {
    $uid = intval($_GET['make_co_admin']);
    if ($_SESSION['role'] !== 'admin') die("Access Denied: Only Admins can promote users.");
    if (!can_manage($conn, $uid)) die("Access Denied.");

    mysqli_query($conn, "UPDATE users SET role='co-admin' WHERE user_id=$uid");
    header("Location: ../dashboard/?msg=role_updated");
}

if (isset($_GET['revoke_role'])) {
    $uid = intval($_GET['revoke_role']);
    if (!can_manage($conn, $uid)) die("Access Denied: You cannot demote this user.");

    mysqli_query($conn, "UPDATE users SET role='user' WHERE user_id=$uid");
    header("Location: ../dashboard/?msg=role_revoked");
}

if (isset($_GET['delete_user'])) {
    $uid = intval($_GET['delete_user']);
    if (!can_manage($conn, $uid)) die("Access Denied: You cannot delete this user.");

    if (!mysqli_query($conn, "DELETE FROM expenses WHERE user_id=$uid")) {
        die("Error deleting user expenses: " . mysqli_error($conn));
    }
    
    if (!mysqli_query($conn, "DELETE FROM categories WHERE user_id=$uid")) {
        die("Error deleting user categories: " . mysqli_error($conn));
    }

    if (mysqli_query($conn, "DELETE FROM users WHERE user_id=$uid")) {
        header("Location: ../dashboard/?msg=user_removed");
    } else {
        die("Error deleting user: " . mysqli_error($conn));
    }
}
?>