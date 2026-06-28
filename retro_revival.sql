-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2026 at 03:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `retro_revival`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `Cart_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`Cart_ID`, `User_ID`, `Created_At`) VALUES
(1, 4, '2026-06-27 04:19:27'),
(2, 5, '2026-06-27 10:38:35');

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

CREATE TABLE `cart_item` (
  `CartItem_ID` int(11) NOT NULL,
  `Cart_ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `CartItem_Quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_item`
--

INSERT INTO `cart_item` (`CartItem_ID`, `Cart_ID`, `Product_ID`, `CartItem_Quantity`) VALUES
(1, 1, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `Category_ID` int(11) NOT NULL,
  `Category_Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`Category_ID`, `Category_Name`) VALUES
(1, 'Clothing'),
(2, 'Shoes'),
(3, 'Accessories');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `Order_ID` int(11) NOT NULL,
  `Buyer_ID` int(11) NOT NULL,
  `Order_TotalAmount` decimal(10,2) NOT NULL,
  `Order_Status` enum('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending',
  `Order_ShippingAddress` text NOT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `OrderItem_ID` int(11) NOT NULL,
  `Order_ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `Seller_ID` int(11) NOT NULL,
  `OrderItem_Quantity` int(11) NOT NULL DEFAULT 1,
  `OrderItem_Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `Payment_ID` int(11) NOT NULL,
  `Order_ID` int(11) NOT NULL,
  `Payment_Method` varchar(50) NOT NULL,
  `Payment_Status` enum('pending','successful','failed') DEFAULT 'pending',
  `Payment_Date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `Product_ID` int(11) NOT NULL,
  `Seller_ID` int(11) NOT NULL,
  `Category_ID` int(11) NOT NULL,
  `Product_Name` varchar(150) NOT NULL,
  `Product_Description` text DEFAULT NULL,
  `Product_Price` decimal(10,2) NOT NULL,
  `Product_Size` varchar(20) DEFAULT NULL,
  `Product_ConditionStatus` enum('New','Excellent','Good','Well-loved') NOT NULL,
  `Product_ConditionDetails` text DEFAULT NULL,
  `Product_Stock` int(11) NOT NULL DEFAULT 1,
  `Product_Status` enum('pending','approved','rejected','sold_out') NOT NULL DEFAULT 'pending',
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`Product_ID`, `Seller_ID`, `Category_ID`, `Product_Name`, `Product_Description`, `Product_Price`, `Product_Size`, `Product_ConditionStatus`, `Product_ConditionDetails`, `Product_Stock`, `Product_Status`, `Created_At`) VALUES
(1, 2, 1, 'Vintage Baju Kurung', 'Preloved traditional baju kurung in excellent condition.', 45.00, 'M', '', '', 1, 'approved', '2026-06-15 05:02:05'),
(5, 2, 1, 'Duckie T-shirt', 'Preloved graphic t-shirt suitable for casual wear', 20.02, 'L', 'Good', 'Colour is still nice. Minor fading on the print, but no holes or major stains', 1, 'approved', '2026-06-16 04:40:18'),
(6, 4, 2, 'Grey Converse Shoes', 'Converse shoes in color grey', 35.90, '39', 'Good', 'only wore it for 5 times and its still in good condition', 0, 'sold_out', '2026-06-27 09:24:35'),
(7, 4, 2, '70s Vintage New Balance', 'Rare blue new balance shoes', 65.00, '38', 'Excellent', 'Only wore it once', 1, 'approved', '2026-06-27 10:10:01'),
(8, 4, 1, 'Grey Hoodie', 'old grey hoodie', 10.00, 'M', 'Well-loved', 'good to wear for winter', 1, 'pending', '2026-06-28 05:20:26'),
(9, 4, 2, 'Puma Sneakers', 'old puma sneakers', 15.00, '38', 'Well-loved', 'can still be use for walking', 1, 'pending', '2026-06-28 05:26:07');

-- --------------------------------------------------------

--
-- Table structure for table `product_image`
--

CREATE TABLE `product_image` (
  `ProductImage_ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `ProductImage_Path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_image`
--

INSERT INTO `product_image` (`ProductImage_ID`, `Product_ID`, `ProductImage_Path`) VALUES
(4, 5, 'images/products/duckie_t-shirt.png'),
(5, 6, 'images/products/converse_grey_shoes'),
(6, 7, 'images/products/blue_newbalance_shoes.png'),
(7, 8, 'images/products/prod_6a40af1aa0ca29.20962670.png'),
(8, 9, 'images/products/prod_6a40b06f463755.50204756.png');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `User_ID` int(11) NOT NULL,
  `User_Name` varchar(100) NOT NULL,
  `User_Email` varchar(100) NOT NULL,
  `User_Password` varchar(255) NOT NULL,
  `User_PhoneNumber` varchar(20) DEFAULT NULL,
  `User_Address` text DEFAULT NULL,
  `User_Role` enum('buyer','seller','admin') NOT NULL,
  `User_Status` enum('active','inactive') DEFAULT 'active',
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`User_ID`, `User_Name`, `User_Email`, `User_Password`, `User_PhoneNumber`, `User_Address`, `User_Role`, `User_Status`, `Created_At`) VALUES
(1, 'Alice Tan', 'alice.tan@example.com', 'abc123', '0123456789', 'Cyberjaya', 'admin', 'active', '2026-06-14 12:05:07'),
(2, 'Daniel Lee', 'daniel.lee@example.com', 'def456', '0198765432', 'Klang', 'seller', 'active', '2026-06-14 12:05:07'),
(3, 'Sarah Qistina', 'sarah.qistina@example.com', 'ghi789', '0172233445', 'Shah Alam', 'buyer', 'active', '2026-06-14 12:05:07'),
(4, 'aliah', 'alia67@gmail.com', '$2y$10$7ieXsc37EVQSSWoacpl83O7P8KUiINjUPV9E0K622dW5bV/c68chG', '012345678', 'Malaysia', 'seller', 'active', '2026-06-27 03:54:11'),
(5, 'maria', 'mar_14@gmail.com', '$2y$10$VBzsbfWJQ/rSd12UALb8lOgY4tBBUvJkx6Wd3K/.LcJhtkatIeqpm', '018765432', '', 'admin', 'active', '2026-06-27 10:23:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`Cart_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`CartItem_ID`),
  ADD KEY `Cart_ID` (`Cart_ID`),
  ADD KEY `Product_ID` (`Product_ID`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`Category_ID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`Order_ID`),
  ADD KEY `Buyer_ID` (`Buyer_ID`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`OrderItem_ID`),
  ADD KEY `Order_ID` (`Order_ID`),
  ADD KEY `Product_ID` (`Product_ID`),
  ADD KEY `Seller_ID` (`Seller_ID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`Payment_ID`),
  ADD KEY `Order_ID` (`Order_ID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`Product_ID`),
  ADD KEY `Seller_ID` (`Seller_ID`),
  ADD KEY `Category_ID` (`Category_ID`);

--
-- Indexes for table `product_image`
--
ALTER TABLE `product_image`
  ADD PRIMARY KEY (`ProductImage_ID`),
  ADD KEY `Product_ID` (`Product_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `User_Email` (`User_Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `Cart_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `CartItem_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `Category_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `Order_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `OrderItem_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `Product_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_image`
--
ALTER TABLE `product_image`
  MODIFY `ProductImage_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`);

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_item_ibfk_1` FOREIGN KEY (`Cart_ID`) REFERENCES `cart` (`Cart_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_item_ibfk_2` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`Buyer_ID`) REFERENCES `user` (`User_ID`);

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`Order_ID`) REFERENCES `orders` (`Order_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`),
  ADD CONSTRAINT `order_item_ibfk_3` FOREIGN KEY (`Seller_ID`) REFERENCES `user` (`User_ID`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`Order_ID`) REFERENCES `orders` (`Order_ID`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`Seller_ID`) REFERENCES `user` (`User_ID`),
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`Category_ID`) REFERENCES `category` (`Category_ID`);

--
-- Constraints for table `product_image`
--
ALTER TABLE `product_image`
  ADD CONSTRAINT `product_image_ibfk_1` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
