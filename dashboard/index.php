<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit();
}
include '../includes/config.php';

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

$status_check = mysqli_query($conn, "SELECT status FROM users WHERE user_id = '$user_id'");
$user_status = mysqli_fetch_assoc($status_check)['status'];

if ($user_status !== 'active' && $user_role !== 'admin') {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
        <title>DEMS - Approval Pending</title>
    </head>

    <body class="auth-body">
        <div class="auth-card" style="text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">⏳</div>
            <h2 style="color: var(--secondary); margin-bottom: 1rem;">Approval Pending</h2>
            <p style="color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem;">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!<br>
                Your account status is currently <strong class="status-pill status-pending"><?php echo strtoupper($user_status); ?></strong>.
                Please wait for an administrator to activate your access.
            </p>
            <a href="../logout/" style="color: var(--danger); font-weight: 600;">Logout</a>
        </div>
    </body>

    </html>
<?php
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>CoreFinance - <?php echo ucfirst($user_role); ?> Dashboard</title>
</head>

<body>
    <header>
        <h2><span style="color:var(--primary);">Core</span><span style="color:var(--accent);">Finance</span> <?php echo ($user_role === 'admin' || $user_role === 'co-admin') ? "Management" : "Dashboard"; ?></h2>
        <div class="user-info">
            <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
            <a href="../logout/" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="container <?php echo ($user_role === 'admin' || $user_role === 'co-admin') ? 'full-width' : ''; ?>">
        <?php if ($user_role === 'admin' || $user_role === 'co-admin'): ?>
            <div class="main-content">
                <div class="card">
                    <h3>User Management</h3>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Status</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $users = mysqli_query($conn, "SELECT * FROM users WHERE user_id != '$user_id' ORDER BY role DESC, status ASC");
                                while ($u = mysqli_fetch_assoc($users)) {
                                    $target_role = $u['role'];
                                    $can_manage = false;

                                    if ($user_role === 'admin') {
                                        $can_manage = true;
                                    } elseif ($user_role === 'co-admin') {
                                        if ($target_role === 'user') $can_manage = true;
                                    }

                                    echo "<tr>
                                        <td>" . htmlspecialchars($u['username']) . "</td>
                                        <td><span class='status-pill status-" . strtolower($u['status']) . "'>" . strtoupper($u['status']) . "</span></td>
                                        <td>" . ucfirst($u['role']) . "</td>
                                        <td>";

                                    if ($can_manage) {
                                        if ($u['status'] !== 'active') echo "<a href='../includes/process.php?approve=" . $u['user_id'] . "' class='action-link' style='color:var(--primary);'>Approve</a> ";
                                        else echo "<a href='../includes/process.php?suspend=" . $u['user_id'] . "' class='action-link' style='color:var(--warning);'>Suspend</a> ";

                                        if ($target_role === 'user') {
                                            if ($user_role === 'admin') {
                                                echo "<a href='../includes/process.php?make_co_admin=" . $u['user_id'] . "' class='action-link' style='color:var(--accent);'>Make Co-Admin</a> ";
                                            }
                                        } else {
                                            echo "<a href='../includes/process.php?revoke_role=" . $u['user_id'] . "' class='action-link' style='color:var(--text-muted);'>Demote</a> ";
                                        }

                                        echo "<a href='../includes/process.php?delete_user=" . $u['user_id'] . "' class='action-link text-danger' onclick='return confirm(\"Remove user?\")'>Remove</a>";
                                    } else {
                                        echo "<span style='color:var(--text-muted); font-size:0.9em; font-style:italic;'><span style='margin-right:5px;'></span>Protected</span>";
                                    }

                                    echo "</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="main-content">
                <div class="card">
                    <h3>Add New Expense</h3>
                    <form action="../includes/process.php" method="POST" class="horizontal-form" id="expenseForm" novalidate>
                        <div class="input-wrapper">
                            <select name="category_id" required>
                                <option value="">Select Category</option>
                                <?php
                                $cq = mysqli_query($conn, "SELECT * FROM categories WHERE user_id IS NULL OR user_id = '$user_id' ORDER BY cat_name ASC");
                                while ($c = mysqli_fetch_assoc($cq)) echo "<option value='{$c['cat_id']}'>" . htmlspecialchars($c['cat_name']) . "</option>";
                                ?>
                            </select>
                            <span class="error-message"></span>
                        </div>
                        <div class="input-wrapper">
                            <input type="text" name="item" placeholder="Description" required>
                            <span class="error-message"></span>
                        </div>
                        <div class="input-wrapper" style="position:relative;">
                            <div style="position:relative;">
                                <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none;">Rs.</span>
                                <input type="number" name="cost" placeholder="0.00" step="0.01" style="padding-left: 35px;" required>
                            </div>
                            <span class="error-message"></span>
                        </div>
                        <div class="input-wrapper">
                            <input type="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                            <span class="error-message"></span>
                        </div>
                        <button type="submit" name="add_expense" style="height: 42px;">Add</button>
                    </form>
                </div>

                <div class="card">
                    <h3>Recent Expenses</h3>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Cost</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                $res = mysqli_query($conn, "SELECT e.*, c.cat_name FROM expenses e LEFT JOIN categories c ON e.category_id = c.cat_id WHERE e.user_id = '$user_id' ORDER BY e.expense_date DESC");
                                while ($r = mysqli_fetch_assoc($res)): 
                                    $total += $r['cost'];
                                ?>
                                    <tr>
                                        <td>
                                            <div style='font-weight:500;'><?php echo htmlspecialchars($r['item_name']); ?></div>
                                            <div style='font-size:0.8rem; color:var(--text-muted);'><?php echo ($r['cat_name'] ?? 'General'); ?></div>
                                        </td>
                                        <td style='font-weight:600;'>Rs. <?php echo number_format($r['cost'], 2); ?></td>
                                        <td style='color:var(--text-muted); font-size:0.9rem;'><?php echo $r['expense_date']; ?></td>
                                        <td>
                                            <button 
                                                class='action-link edit-btn' 
                                                data-id="<?php echo $r['id']; ?>"
                                                data-item="<?php echo htmlspecialchars($r['item_name'], ENT_QUOTES); ?>"
                                                data-cost="<?php echo $r['cost']; ?>"
                                                data-date="<?php echo $r['expense_date']; ?>"
                                                data-category="<?php echo $r['category_id']; ?>"
                                            >Edit</button>
                                            <span style='color:var(--text-muted); margin:0 0.25rem;'>|</span>
                                            <a href='../includes/process.php?delete=<?php echo $r['id']; ?>' class='action-link text-danger' onclick='return confirm("Delete record?")'>Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td style="text-align:right; font-weight:700; color: #ffffff; background-color: #1e293b;">Total Spent:</td>
                                    <td colspan="3" style="font-weight:700; color: #ffffff; background-color: #1e293b;">Rs. <?php echo number_format($total, 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="sidebar">
                <div class="card">
                    <h3>Spending Distribution</h3>
                    <div style="position: relative; height: 250px; display: flex; justify-content: center; align-items: center;">
                        <canvas id="expenseChart"></canvas>
                        <div id="noDataMessage" style="display: none; color: var(--text-muted); text-align: center;"> Add an expense to see the chart!</div>
                    </div>
                </div>

                <div class="card">
                    <h3>My Categories</h3>
                    <form action="../includes/process.php" method="POST" id="addCategoryForm" style="display:flex; gap:0.5rem; margin-bottom: 1rem; align-items: flex-start;" novalidate>
                        <div class="input-wrapper" style="flex-grow:1;">
                            <input type="text" name="new_cat_name" placeholder="New Category">
                            <span class="error-message"></span>
                        </div>
                        <button type="submit" name="add_category" style="width: auto; padding: 0.75rem 1rem; height: auto;">+</button>
                    </form>
                    <ul class="category-list">
                        <?php
                        $uc = mysqli_query($conn, "SELECT * FROM categories WHERE user_id = '$user_id'");
                        if (mysqli_num_rows($uc) > 0) {
                            while ($ct = mysqli_fetch_assoc($uc)) {
                                echo "<li>" . htmlspecialchars($ct['cat_name']) . " <a href='../includes/process.php?delete_category=" . $ct['cat_id'] . "' class='btn-sm-danger'>&times;</a></li>";
                            }
                        } else {
                            echo "<li style='color:var(--text-muted); font-style:italic;'>No custom categories</li>";
                        }
                        ?>
                    </ul>
                </div>


            </div>
        <?php endif; ?>
    </div>

    <?php if ($user_role === 'user'): ?>
        <?php
        $labels = [];
        $data = [];
        $cq = mysqli_query($conn, "SELECT c.cat_name, SUM(e.cost) as tot FROM expenses e LEFT JOIN categories c ON e.category_id = c.cat_id WHERE e.user_id = '$user_id' GROUP BY c.cat_name");
        while ($chart = mysqli_fetch_assoc($cq)) {
            $labels[] = $chart['cat_name'] ?? 'General';
            $data[] = $chart['tot'];
        }
        ?>
        <script>
            const chartLabels = <?php echo json_encode($labels); ?>;
            const chartData = <?php echo json_encode($data); ?>;
        </script>
        <script src="../assets/js/script.js?v=<?php echo time(); ?>" defer></script>
        <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <?php else: ?>
         <script src="../assets/js/script.js?v=<?php echo time(); ?>" defer></script>
         <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <?php endif; ?>

    <footer>
        &copy; <?php echo date("Y"); ?> CoreFinance. All rights reserved.
    </footer>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3 style="margin-top:0;">Update Transaction</h3>
            <form action="../includes/process.php" method="POST" id="updateForm" novalidate>
                <input type="hidden" name="id" id="edit_id">

                <div class="form-group">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Category</label>
                    <select name="category_id" id="edit_category" required>
                        <option value="">Select Category</option>
                        <?php
                        $cq_modal = mysqli_query($conn, "SELECT * FROM categories WHERE user_id IS NULL OR user_id = '$user_id' ORDER BY cat_name ASC");
                        while ($cm = mysqli_fetch_assoc($cq_modal)) {
                            echo "<option value='{$cm['cat_id']}'>" . htmlspecialchars($cm['cat_name']) . "</option>";
                        }
                        ?>
                    </select>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Item Name</label>
                    <input type="text" name="item" id="edit_item" required>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Cost (Rs.)</label>
                    <input type="number" name="cost" id="edit_cost" step="0.01" required>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Date</label>
                    <input type="date" name="date" id="edit_date" required>
                    <span class="error-message"></span>
                </div>

                <button type="submit" name="update_expense" style="margin-top:1rem;">Save Changes</button>
            </form>
        </div>
    </div>
</body>

</html>
