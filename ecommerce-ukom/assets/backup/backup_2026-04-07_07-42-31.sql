DROP TABLE IF EXISTS admin;

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO admin VALUES("2","admin","0192023a7bbd73250516f069df18b500");



DROP TABLE IF EXISTS products;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO products VALUES("10","Baseball Shirt","This pure cotton jersey baseball shirt is the ideal choice for sports lovers. In a relaxed fit with a pinstriped print and a button-through front, it\'s easily paired with shorts and trousers. A badge on the chest adds a varsity-inspired touch.","Boy","99000.00","100","Baseball-shirt.webp","2026-04-05 19:05:59");
INSERT INTO products VALUES("11","Green Stripped Polo","With bold stripes adding a hint of sporty, rugby style, this comfy cotton tee will stand out from the scrum on casual days.","Boy","109000.00","100","green-stripped-polo.webp","2026-04-05 19:07:04");
INSERT INTO products VALUES("12","Denim Shorts","A comfy classic for casual days, our light-wash denim shorts with pre-worn fading and rip-and-repair detail are designed in a longer length. Made from durable denim with a little stretch to keep up with every adventure.Responsibly sourced cotton is grown with all-natural growing methods, using less water to produce, and using no harmful chemicals. Safer for their skin and helping protect their world.","Boy","99000.00","100","denim-shorts.webp","2026-04-05 19:08:05");
INSERT INTO products VALUES("13","Denim Cargo Jeans","Crafted from cotton denim, these pull-on jeans are designed with a barrel-leg shape. Baggy at the leg and tapered at the ankle, they\'re made for easy movement and comfort.","Boy","149000.00","100","denim-cargo-jeans.webp","2026-04-05 19:08:53");
INSERT INTO products VALUES("14","Black Butterfly T-Shirt","A go-to top thats as easy as it is eye-catching. With its relaxed, boxy fit and a flutter of delicate butterflies, its made from cool cotton, perfect for dressing up or keeping it casual.","Girl","99000.00","100","black-butterfly-tshirt.webp","2026-04-05 19:09:47");
INSERT INTO products VALUES("15","Pink Cat T-Shirt","Bright and fun, this comfy cotton tee is made for feline fans.","Girl","89000.00","99","pink-cat-tshirt.webp","2026-04-05 19:10:19");
INSERT INTO products VALUES("16","Floral Woven Skirt","Filled with flowers, this tiered A-line skirt is ready to twirl into today\'s adventures. A fully elasticated waistband and cotton lining ensures all-day comfort.","Girl","109000.00","98","floral-woven-skirt.webp","2026-04-05 19:11:00");
INSERT INTO products VALUES("17","2 Pack Ribbed Leggings","Two pairs of full-length leggings to mix, match and layer. Each pair features a wide, elasticated waistband for comfort and easy dressing.","Girl","89000.00","100","2-pack-ribbed-leggings.webp","2026-04-05 19:11:36");



DROP TABLE IF EXISTS sales;

CREATE TABLE `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO sales VALUES("16","16","15","Pink Cat T-Shirt","0","1","89000","2026-04-05 19:16:43");
INSERT INTO sales VALUES("17","17","16","Floral Woven Skirt","0","2","218000","2026-04-07 07:07:45");



DROP TABLE IF EXISTS transactions;

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `total` int(11) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `shipping_address` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_proof` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO transactions VALUES("16","8","aufa","89000","transfer","","cancelled","2026-04-05 19:16:43","1775391403_denim-shorts.webp");
INSERT INTO transactions VALUES("17","8","aufa","218000","transfer","JL. depok","approved","2026-04-07 07:07:45","1775520465_logo.png");



DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `plain_password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','officer','customer') NOT NULL DEFAULT 'officer',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users VALUES("1","admin","","","","0192023a7bbd73250516f069df18b500","","2026-02-21 22:01:20","admin");
INSERT INTO users VALUES("5","najo","","","n@gmail.com","202cb962ac59075b964b07152d234b70","123","2026-02-21 22:28:55","officer");
INSERT INTO users VALUES("8","aufa","aufa","JL. depok","aufa@gmail.com","202cb962ac59075b964b07152d234b70","","2026-02-21 23:14:53","customer");
INSERT INTO users VALUES("9","rehan","","","r@gmail.com","698d51a19d8a121ce581499d7b701668","","2026-02-21 23:19:06","customer");



