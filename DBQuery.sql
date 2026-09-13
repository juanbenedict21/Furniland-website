SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;

CREATE TABLE `cart` (
    `cartID` int(10) NOT NULL,
    `userID` int(10) NOT NULL,
    `productID` int(10) NOT NULL,
    `quantity` int(5) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE `products` (
    `productID` int(10) NOT NULL,
    `productName` varchar(30) NOT NULL,
    `description` varchar(255) NOT NULL,
    `price` float NOT NULL,
    `image` varchar(100) NOT NULL,
    `vendorID` int(10) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

INSERT INTO
    `products` (
        `productID`,
        `productName`,
        `description`,
        `price`,
        `image`,
        `vendorID`
    )
VALUES (
        4,
        'Minimalism Drawer',
        'Ini drawer super minimalis',
        2500000,
        '693e35c80dad63.49455923.jpeg',
        4
    ),
    (
        5,
        'Wooden Dining Table',
        'ini buat makan keluarga',
        3000000,
        '693e35f0b6f076.30888156.jpeg',
        2
    ),
    (
        6,
        'Elegant Drawer',
        'drawer classic elegant',
        500000,
        '693e360e0df2d8.30880085.jpeg',
        2
    ),
    (
        7,
        'Comfort Couch',
        'ini gokil bgt dah',
        5287500,
        '693e362ddf5557.56906273.jpeg',
        3
    ),
    (
        8,
        'Two Chair',
        'ini buat ngobrol senja',
        3453340,
        '693e364aad9ad9.22435640.jpeg',
        2
    ),
    (
        9,
        'Makeup Desk',
        'ini buat mekap mekap cantik',
        834579,
        '693e3661580263.58479208.jpeg',
        3
    ),
    (
        10, 
        'Dreamy Mattress', 
        'Kasur super nyaman untuk tidur nyenyak dan mimpi indah', 
        3200000, 
        'img1.png', 
        2
        ),
    (
        11, 
        'Scholar Desk', 
        'Meja belajar modern dengan desain minimalis dan fungsional', 
        1250000, 
        'img2.png', 
        3
    ),
    (
        12,
        'Elegance Wardrobe', 
        'Lemari elegan dengan banyak ruang penyimpanan', 
        4500000, 
        'img3.png', 
        4
    ),
    (
        13, 
        'Comfy Sofa', 
        'Sofa empuk untuk bersantai bersama keluarga', 
        5000000, 
        'img4.png', 
        2
    ),
    (
        14, 
        'Gourmet Dining Table', 
        'Meja makan stylish untuk pengalaman makan kelas atas', 
        3800000, 
        'img5.png', 
        3
    ),
    (
        15, 
        'Café Lounge Table', 
        'Meja cafe cantik untuk santai dan ngobrol', 
        1950000, 
        'img6.png', 
        4);
    ;

CREATE TABLE `transactions` (
    `transactionID` int(10) NOT NULL,
    `userID` int(10) NOT NULL,
    `totalPrice` float NOT NULL,
    `transactionDate` date NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

INSERT INTO
    `transactions` (
        `transactionID`,
        `userID`,
        `totalPrice`,
        `transactionDate`
    )
VALUES (1, 2, 6787500, '2025-12-14'),
    (2, 2, 2500000, '2025-12-14'),
    (3, 4, 834579, '2025-12-14'),
    (4, 4, 23653700, '2025-12-14');

CREATE TABLE `transaction_details` (
    `detailID` int(10) NOT NULL,
    `transactionID` int(10) NOT NULL,
    `productID` int(10) NOT NULL,
    `quantity` int(5) NOT NULL,
    `subtotal` int(10) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

INSERT INTO
    `transaction_details` (
        `detailID`,
        `transactionID`,
        `productID`,
        `quantity`,
        `subtotal`
    )
VALUES (1, 1, 7, 1, 5287500),
    (2, 1, 6, 3, 1500000),
    (3, 2, 4, 1, 2500000),
    (4, 3, 9, 1, 834579),
    (5, 4, 7, 4, 21150000),
    (6, 4, 9, 3, 2503737);

CREATE TABLE `users` (
    `userID` int(10) NOT NULL,
    `username` varchar(20) NOT NULL,
    `email` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `gender` varchar(6) NOT NULL,
    `dob` date NOT NULL,
    `role` varchar(6) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

INSERT INTO
    `users` (
        `userID`,
        `username`,
        `email`,
        `password`,
        `gender`,
        `dob`,
        `role`
    )
VALUES (
        1,
        'cadmin',
        'c@gmail.com',
        'cccccccc',
        'Female',
        '2025-12-14',
        'Admin'
    ),
    (
        2,
        'bbbb',
        'b@gmail.com',
        'bbbbbbbb',
        'Male',
        '2025-12-09',
        'Member'
    );

CREATE TABLE `vendors` (
    `vendorID` int(10) NOT NULL,
    `vendorName` varchar(20) NOT NULL,
    `location` varchar(100) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

INSERT INTO
    `vendors` (
        `vendorID`,
        `vendorName`,
        `location`
    )
VALUES (
        2,
        'IKEA Alam Sutera',
        'Tangerang'
    ),
    (3, 'INFORMA', 'Jakarta Utara'),
    (4, 'HomeBrew', 'Tangerang');

ALTER TABLE `cart`
ADD PRIMARY KEY (`cartID`),
ADD KEY `userID` (`userID`),
ADD KEY `productID` (`productID`);

ALTER TABLE `products`
ADD PRIMARY KEY (`productID`),
ADD KEY `fk_products_vendor` (`vendorID`);

ALTER TABLE `transactions`
ADD PRIMARY KEY (`transactionID`),
ADD KEY `userID` (`userID`);

ALTER TABLE `transaction_details`
ADD PRIMARY KEY (`detailID`),
ADD KEY `transactionID` (`transactionID`),
ADD KEY `productID` (`productID`);

ALTER TABLE `users`
ADD PRIMARY KEY (`userID`),
ADD UNIQUE KEY `username` (`username`),
ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `vendors` ADD PRIMARY KEY (`vendorID`);

ALTER TABLE `cart`
MODIFY `cartID` int(10) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 8;

ALTER TABLE `products`
MODIFY `productID` int(10) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 16;

ALTER TABLE `transactions`
MODIFY `transactionID` int(10) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 5;

ALTER TABLE `transaction_details`
MODIFY `detailID` int(10) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 7;

ALTER TABLE `users`
MODIFY `userID` int(10) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 5;

ALTER TABLE `vendors`
MODIFY `vendorID` int(10) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 5;

ALTER TABLE `cart`
ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `products` (`productID`) ON DELETE CASCADE;

ALTER TABLE `products`
ADD CONSTRAINT `fk_products_vendor` FOREIGN KEY (`vendorID`) REFERENCES `vendors` (`vendorID`) ON UPDATE CASCADE;

ALTER TABLE `transactions`
ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

ALTER TABLE `transaction_details`
ADD CONSTRAINT `transaction_details_ibfk_1` FOREIGN KEY (`transactionID`) REFERENCES `transactions` (`transactionID`) ON DELETE CASCADE,
ADD CONSTRAINT `transaction_details_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `products` (`productID`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;