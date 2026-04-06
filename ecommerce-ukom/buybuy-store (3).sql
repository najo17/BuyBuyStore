-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Apr 2026 pada 05.08
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `buybuy-store`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(2, 'admin', '0192023a7bbd73250516f069df18b500');

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category`, `price`, `stock`, `image`, `created_at`) VALUES
(10, 'Baseball Shirt', 'This pure cotton jersey baseball shirt is the ideal choice for sports lovers. In a relaxed fit with a pinstriped print and a button-through front, it\'s easily paired with shorts and trousers. A badge on the chest adds a varsity-inspired touch.', 'Boy', 99000.00, 100, 'Baseball-shirt.webp', '2026-04-05 12:05:59'),
(11, 'Green Stripped Polo', 'With bold stripes adding a hint of sporty, rugby style, this comfy cotton tee will stand out from the scrum on casual days.', 'Boy', 109000.00, 100, 'green-stripped-polo.webp', '2026-04-05 12:07:04'),
(12, 'Denim Shorts', 'A comfy classic for casual days, our light-wash denim shorts with pre-worn fading and rip-and-repair detail are designed in a longer length. Made from durable denim with a little stretch to keep up with every adventure.Responsibly sourced cotton is grown with all-natural growing methods, using less water to produce, and using no harmful chemicals. Safer for their skin and helping protect their world.', 'Boy', 99000.00, 100, 'denim-shorts.webp', '2026-04-05 12:08:05'),
(13, 'Denim Cargo Jeans', 'Crafted from cotton denim, these pull-on jeans are designed with a barrel-leg shape. Baggy at the leg and tapered at the ankle, they\'re made for easy movement and comfort.', 'Boy', 149000.00, 100, 'denim-cargo-jeans.webp', '2026-04-05 12:08:53'),
(14, 'Black Butterfly T-Shirt', 'A go-to top thats as easy as it is eye-catching. With its relaxed, boxy fit and a flutter of delicate butterflies, its made from cool cotton, perfect for dressing up or keeping it casual.', 'Girl', 99000.00, 100, 'black-butterfly-tshirt.webp', '2026-04-05 12:09:47'),
(15, 'Pink Cat T-Shirt', 'Bright and fun, this comfy cotton tee is made for feline fans.', 'Girl', 89000.00, 99, 'pink-cat-tshirt.webp', '2026-04-05 12:10:19'),
(16, 'Floral Woven Skirt', 'Filled with flowers, this tiered A-line skirt is ready to twirl into today\'s adventures. A fully elasticated waistband and cotton lining ensures all-day comfort.', 'Girl', 109000.00, 100, 'floral-woven-skirt.webp', '2026-04-05 12:11:00'),
(17, '2 Pack Ribbed Leggings', 'Two pairs of full-length leggings to mix, match and layer. Each pair features a wide, elasticated waistband for comfort and easy dressing.', 'Girl', 89000.00, 100, '2-pack-ribbed-leggings.webp', '2026-04-05 12:11:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sales`
--

INSERT INTO `sales` (`id`, `transaction_id`, `product_id`, `product_name`, `price`, `quantity`, `subtotal`, `created_at`) VALUES
(16, 16, 15, 'Pink Cat T-Shirt', 0, 1, 89000, '2026-04-05 12:16:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `total` int(11) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `shipping_address` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_proof` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `customer_id`, `customer_name`, `total`, `payment_method`, `shipping_address`, `status`, `created_at`, `payment_proof`) VALUES
(16, 8, 'aufa', 89000, 'transfer', NULL, 'cancelled', '2026-04-05 12:16:43', '1775391403_denim-shorts.webp');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `plain_password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','officer','customer') NOT NULL DEFAULT 'officer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `full_name`, `address`, `email`, `password`, `plain_password`, `created_at`, `role`) VALUES
(1, 'admin', NULL, NULL, NULL, '0192023a7bbd73250516f069df18b500', NULL, '2026-02-21 15:01:20', 'admin'),
(5, 'najo', NULL, NULL, 'n@gmail.com', '202cb962ac59075b964b07152d234b70', '123', '2026-02-21 15:28:55', 'officer'),
(8, 'aufa', 'aufa', 'JL. depok', 'aufa@gmail.com', '202cb962ac59075b964b07152d234b70', NULL, '2026-02-21 16:14:53', 'customer'),
(9, 'rehan', NULL, NULL, 'r@gmail.com', '698d51a19d8a121ce581499d7b701668', NULL, '2026-02-21 16:19:06', 'customer');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
