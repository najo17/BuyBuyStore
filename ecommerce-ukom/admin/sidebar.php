    <?php
    // Mengambil nama file halaman yang sedang dibuka
    // Contoh: jika URL adalah product-management.php, maka hasilnya "product-management.php"
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>

    <style>
        /* Styling utama sidebar */
        .sidebar {
            width: 350px;
            min-height: 100vh;
            background-color: #FFA4A4;
            padding: 30px 25px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Styling judul sidebar */
        .sidebar h2 {
            color: #fff;
            font-weight: 600;
            margin-bottom: 40px;
            line-height: 1.3;
        }

        /* Container menu sidebar */
        .menu {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        /* Styling link menu */
        .menu a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            padding: 18px 25px;
            border-radius: 12px;
            /* transition: background 0.3s, color 0.3s; */
        }

        /* Styling menu yang sedang aktif */
        .menu a.active {
            background: #fff;
            color: #FFA4A4;
            font-weight: 500;
        }

        /* Efek hover saat cursor diarahkan ke menu */
        .menu a:hover {
            background: rgba(255,255,255,0.2);
        }

        /* Jarak atas untuk area logout */
        .logout {
            margin-top: 40px;
        }

        /* Styling tombol logout */
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

        /* Efek hover tombol logout */
        .logout a:hover {
            opacity: 0.9;
        }
    </style>

    <!-- Container sidebar -->
    <div class="sidebar">
        <div>
            <!-- Judul sidebar -->
            <h2>Dashboard<br>Admin</h2>

            <!-- Menu navigasi sidebar -->
            <div class="menu">

                <!-- Link menuju halaman Home -->
                <a href="index.php" 
                class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">
                Home
                </a>

                <!-- Link menuju halaman User Management -->
                <a href="user-management.php" 
                class="<?= ($current_page == 'user-management.php') ? 'active' : '' ?>">
                User Management
                </a>

                <!-- Link menuju halaman Product Management -->
                <a href="product-management.php" 
                class="<?= ($current_page == 'product-management.php') ? 'active' : '' ?>">
                Product Management
                </a>

                <!-- Link menuju halaman Generate Report -->
                <a href="generate-reports.php" 
                class="<?= ($current_page == 'generate-reports.php') ? 'active' : '' ?>">
                Generate Report
                </a>

                <!-- Link menuju halaman Transaction Management -->
                <a href="transactions.php" 
                class="<?= ($current_page == 'transactions.php') ? 'active' : '' ?>">
                Transaction Management
                </a>

                <!-- Link menuju halaman Backup & Restore -->
                <a href="backup_restore.php" 
                class="<?= ($current_page == 'backup_restore.php') ? 'active' : '' ?>">
                Data Backup/Restore
                </a>
            </div>
        </div>

        <!-- Tombol logout -->
        <div class="logout">
            <a href="../auth/logout.php">Logout</a>
        </div>
    </div>