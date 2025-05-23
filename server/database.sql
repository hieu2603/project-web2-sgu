-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2025 at 08:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web2_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_email` varchar(255) NOT NULL,
  `account_password` varchar(255) NOT NULL,
  `account_status` varchar(10) NOT NULL DEFAULT 'Active',
  `account_role` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `account_name`, `account_email`, `account_password`, `account_status`, `account_role`) VALUES
(1, 'Nguyễn Văn Hiếu', 'nguyenvanhieu2603@gmail.com', 'a73dcf3f8da447d571974f46f09a2607', 'Active', 'Admin'),
(2, 'Nguyễn Hoàng Hải', 'hainguyen1205@gmail.com', '827dcaf2b3d5011ddae3c7fd9f29bc7c', 'Active', 'User'),
(3, 'Đoàn Tuấn Tài', 'taismile@gmail.com', '9c8d8d1af7c282924d29a50f2abf22ad', 'Active', 'User'),
(4, 'Tài Khoản User 1', 'user1@gmail.com', '1537fc6ecd4168ad802afa56ed350d45', 'Active', 'User');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(2, 'Áo khoác'),
(1, 'Áo thun'),
(3, 'Quần');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_cost` decimal(12,2) NOT NULL,
  `order_status` varchar(255) NOT NULL DEFAULT 'Chưa xác nhận',
  `account_id` int(11) DEFAULT NULL,
  `receiver_name` varchar(255) NOT NULL,
  `phone_number` varchar(12) NOT NULL,
  `province` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `ward` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `payment_method` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_cost`, `order_status`, `account_id`, `receiver_name`, `phone_number`, `province`, `district`, `ward`, `address`, `order_date`, `payment_method`) VALUES
(1, 400000.00, 'Thành công', 3, 'Nguyen Van A', '0777011640', 'Thành phố Hồ Chí Minh', 'Quận 5', 'Phường 4', 'Đại Học Sài Gòn', '2025-05-20 00:02:03', 'Tiền mặt'),
(2, 900000.00, 'Thành công', 3, 'Nguyen Van A', '0777011640', 'Thành phố Hồ Chí Minh', 'Quận Tân Phú', 'Phường Tây Thạnh', 'Đại Học Sài Gòn', '2025-05-21 00:04:35', 'Trực tuyến'),
(3, 400000.00, 'Hủy đơn', 3, 'Nguyen Van A', '0777011640', 'Thành phố Hà Nội', 'Quận Hai Bà Trưng', 'Phường Bách Khoa', 'SGU', '2025-05-22 00:06:28', 'Tiền mặt'),
(4, 600000.00, 'Thành công', 2, 'Nguyen Van B', '0777011640', 'Thành phố Hà Nội', 'Huyện Gia Lâm', 'Xã Đặng Xá', 'SGU', '2025-05-22 00:15:38', 'Tiền mặt'),
(5, 300000.00, 'Thành công', 2, 'Nguyen Van B', '0777011640', 'Thành phố Hồ Chí Minh', 'Quận 5', 'Phường 4', 'SGU', '2025-05-23 00:21:08', 'Trực tuyến'),
(6, 500000.00, 'Thành công', 4, 'Nguyen Van C', '0777011640', 'Thành phố Hà Nội', 'Quận Nam Từ Liêm', 'Phường Xuân Phương', 'SGU', '2025-05-24 00:35:45', 'Tiền mặt'),
(7, 200000.00, 'Thành công', 4, 'Nguyen Van C', '0777011640', 'Tỉnh Bình Dương', 'Thành phố Thủ Dầu Một', 'Phường Phú Mỹ', 'SGU', '2025-05-24 00:37:20', 'Trực tuyến');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `product_quantity`) VALUES
(1, 1, 5, 2),
(2, 2, 3, 3),
(3, 3, 6, 1),
(4, 4, 7, 2),
(5, 5, 8, 2),
(6, 5, 1, 1),
(7, 6, 10, 1),
(8, 6, 1, 2),
(9, 7, 9, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_description` varchar(1000) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_image2` varchar(255) NOT NULL,
  `product_image3` varchar(255) NOT NULL,
  `product_image4` varchar(255) NOT NULL,
  `product_price` decimal(12,2) NOT NULL,
  `product_color` varchar(100) NOT NULL,
  `product_status` varchar(255) NOT NULL DEFAULT 'Đang bán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `category_id`, `product_description`, `product_image`, `product_image2`, `product_image3`, `product_image4`, `product_price`, `product_color`, `product_status`) VALUES
(1, 'Áo thun 1', 1, 'Mô tả sản phẩm áo thun 1', '../assets/img/1748018680_thumb_ao-thun-1.jpg', '../assets/img/1748018680_sub_0_ao-thun-1.jpg', '../assets/img/1748018680_sub_1_ao-thun-1.jpg', '../assets/img/1748018680_sub_2_ao-thun-1.jpg', 100000.00, 'Đen', 'Đang bán'),
(2, 'Áo thun 2', 1, 'Mô tả sản phẩm áo thun 2', '../assets/img/1748018736_thumb_ao-thun-2.jpg', '../assets/img/1748018736_sub_0_ao-thun-2.jpg', '../assets/img/1748018736_sub_1_ao-thun-2.jpg', '../assets/img/1748018736_sub_2_ao-thun-2.jpg', 200000.00, 'Xám', 'Đang bán'),
(3, 'Áo thun 3', 1, 'Mô tả sản phẩm áo thun 3', '../assets/img/1748018793_thumb_ao-thun-3.jpg', '../assets/img/1748018793_sub_0_ao-thun-3.jpg', '../assets/img/1748018793_sub_1_ao-thun-3.jpg', '../assets/img/1748018793_sub_2_ao-thun-3.jpg', 300000.00, 'Trắng', 'Đang bán'),
(4, 'Áo khoác 1', 2, 'Mô tả sản phẩm áo khoác 1', '../assets/img/1748018835_thumb_ao-khoac-1.jpg', '../assets/img/1748018835_sub_0_ao-khoac-1.jpg', '../assets/img/1748018835_sub_1_ao-khoac-1.jpg', '../assets/img/1748018835_sub_2_ao-khoac-1.jpg', 100000.00, 'Trắng', 'Đang bán'),
(5, 'Áo khoác 2', 2, 'Mô tả sản phẩm áo khoác 2', '../assets/img/1748018885_thumb_ao-khoac-2.jpg', '../assets/img/1748018885_sub_0_ao-khoac-2.jpg', '../assets/img/1748018885_sub_1_ao-khoac-2.jpg', '../assets/img/1748018885_sub_2_ao-khoac-2.jpg', 200000.00, 'Đen', 'Đang bán'),
(6, 'Áo thun 4', 1, 'Mô tả sản phẩm áo thun 4', '../assets/img/1748019132_thumb_ao-thun-4.jpg', '../assets/img/1748019132_sub_0_ao-thun-4.jpg', '../assets/img/1748019132_sub_1_ao-thun-4.jpg', '../assets/img/1748019132_sub_2_ao-thun-4.jpg', 400000.00, 'Xám đen', 'Đang bán'),
(7, 'Áo khoác 3', 2, 'Mô tả sản phẩm áo khoác 3', '../assets/img/1748019325_thumb_ao-khoac-3.jpg', '../assets/img/1748019325_sub_0_ao-khoac-3.jpg', '../assets/img/1748019325_sub_1_ao-khoac-3.jpg', '../assets/img/1748019325_sub_2_ao-khoac-3.jpg', 300000.00, 'Đỏ', 'Ngừng bán'),
(8, 'Quần 1', 3, 'Mô tả sản phẩm quần 1', '../assets/img/1748019374_thumb_quan-1.jpg', '../assets/img/1748019374_sub_0_quan-1.jpg', '../assets/img/1748019374_sub_1_quan-1.jpg', '../assets/img/1748019374_sub_2_quan-1.jpg', 100000.00, 'Đen', 'Đang bán'),
(9, 'Quần 2', 3, 'Mô tả sản phẩm quần 2', '../assets/img/1748019417_thumb_quan-2.jpg', '../assets/img/1748019417_sub_0_quan-2.jpg', '../assets/img/1748019417_sub_1_quan-2.jpg', '../assets/img/1748019417_sub_2_quan-2.jpg', 200000.00, 'Xanh da trời', 'Đang bán'),
(10, 'Quần 3', 3, 'Mô tả sản phẩm quần 3', '../assets/img/1748019464_thumb_quan-3.jpg', '../assets/img/1748019464_sub_0_quan-3.jpg', '../assets/img/1748019464_sub_1_quan-3.jpg', '../assets/img/1748019464_sub_2_quan-3.jpg', 300000.00, 'Xanh nước biển', 'Đang bán');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_addresses`
--

CREATE TABLE `shipping_addresses` (
  `shipping_address_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `receiver_name` varchar(255) NOT NULL,
  `phone_number` varchar(12) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `ward` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_addresses`
--

INSERT INTO `shipping_addresses` (`shipping_address_id`, `account_id`, `receiver_name`, `phone_number`, `payment_method`, `province`, `district`, `ward`, `address`) VALUES
(1, 4, 'Nguyen Van C', '0777011640', 'Tiền mặt', 'Thành phố Hồ Chí Minh', 'Quận Tân Phú', 'Phường Sơn Kỳ', 'SGU');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `account_email` (`account_email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `shipping_addresses`
--
ALTER TABLE `shipping_addresses`
  ADD PRIMARY KEY (`shipping_address_id`),
  ADD KEY `account_id` (`account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `shipping_addresses`
--
ALTER TABLE `shipping_addresses`
  MODIFY `shipping_address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `shipping_addresses`
--
ALTER TABLE `shipping_addresses`
  ADD CONSTRAINT `shipping_addresses_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
