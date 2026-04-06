<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .sidebar {
        width: 350px;
        min-height: 100vh;
        background-color: #FFA4A4;
        padding: 30px 25px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .sidebar h2 {
        color: #fff;
        font-weight: 600;
        margin-bottom: 40px;
        line-height: 1.3;
    }

    .menu {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .menu a {
        color: #fff;
        text-decoration: none;
        font-size: 16px;
        padding: 18px 25px;
        border-radius: 12px;
    }

    .menu a.active {
        background: #fff;
        color: #FFA4A4;
        font-weight: 500;
    }

    .menu a:hover {
        background: rgba(255,255,255,0.2);
    }

    .logout {
        margin-top: 40px;
    }

    .logout a {
        display: block;
        background: #FF5C5C;
        color: #fff;
        text-align: center;
        padding: 14px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 500;
    }

    .logout a:hover {
        opacity: 0.9;
    }
</style>

<div class="sidebar">
    <div>
        <h2>Dashboard<br>Officer</h2>

        <div class="menu">
            <a href="index.php" 
            class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">
            Home
            </a>

            <a href="product-management.php" 
            class="<?= ($current_page == 'product-management.php') ? 'active' : '' ?>">
            Product Management
            </a>

            <a href="generate-reports.php" 
            class="<?= ($current_page == 'generate-reports.php') ? 'active' : '' ?>">
            Generate Report
            </a>

            <a href="transactions.php" 
            class="<?= ($current_page == 'transactions.php') ? 'active' : '' ?>">
            Transaction Management
            </a>
        </div>
    </div>

    <div class="logout">
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>