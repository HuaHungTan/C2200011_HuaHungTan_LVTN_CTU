-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th8 08, 2025 lúc 04:39 PM
-- Phiên bản máy phục vụ: 10.4.18-MariaDB
-- Phiên bản PHP: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `lvtn_book`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `added_book`
--

CREATE TABLE `added_book` (
  `added_id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `price_in` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `type` enum('Thêm sách mới','Nhập thêm sách có sẵn') DEFAULT NULL,
  `added_by` int(11) DEFAULT 1,
  `added_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `added_book`
--

INSERT INTO `added_book` (`added_id`, `book_id`, `price_in`, `quantity`, `type`, `added_by`, `added_date`) VALUES
(1, 1, '120000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(2, 2, '120000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(3, 3, '180000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(4, 4, '200000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(5, 5, '95000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(6, 6, '130000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(7, 7, '400000.00', 20, 'Thêm sách mới', 1, '2025-08-08'),
(8, 8, '120000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(9, 9, '60000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(10, 10, '60000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(11, 11, '400000.00', 20, 'Thêm sách mới', 1, '2025-08-08'),
(12, 12, '200000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(13, 13, '200000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(14, 14, '200000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(15, 15, '100000.00', 30, 'Thêm sách mới', 1, '2025-08-08'),
(16, 16, '80000.00', 20, 'Thêm sách mới', 1, '2025-08-08'),
(17, 17, '200000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(18, 18, '200000.00', 50, 'Thêm sách mới', 1, '2025-08-08'),
(19, 19, '240000.00', 30, 'Thêm sách mới', 1, '2025-08-08'),
(20, 20, '220000.00', 35, 'Thêm sách mới', 1, '2025-08-08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `authors`
--

CREATE TABLE `authors` (
  `author_id` int(11) NOT NULL,
  `img_url` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `book_count` int(11) DEFAULT 0,
  `nationality` varchar(50) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `authors`
--

INSERT INTO `authors` (`author_id`, `img_url`, `name`, `book_count`, `nationality`, `bio`, `is_deleted`) VALUES
(1, 'data_image/author_images/PhungQuan.jpg', 'Phùng Quán', 2, 'Việt Nam', 'Ông là một trong những nhà văn, nhà thơ nổi bật của văn học Việt Nam, có sự nghiệp văn chương dài và đầy biến động', 0),
(2, 'data_image/author_images/TranDangKhoa.jpg', 'Trần Đăng Khoa', 2, 'Việt Nam', 'Ông là một nhà thơ nổi tiếng của Việt Nam, được biết đến với biệt danh “thần đồng thơ ca”. Ông được biết đến với những vần thơ hồn nhiên, trong sáng, thể hiện tình yêu quê hương, đất nước và những cảm xúc của tuổi thơ.', 0),
(3, 'data_image/author_images/NguyenNhatAnh.jpg', 'Nguyễn Nhật Ánh', 1, 'Việt Nam', 'Nhà văn Việt Nam nổi tiếng với các tác phẩm dành cho thiếu nhi và thanh thiếu niên', 0),
(4, 'data_image/author_images/HarukiMurakami.jpg', 'Haruki Murakami', 1, 'Nhật Bản', 'Tiểu thuyết gia người Nhật Bản, tác giả của những cuốn sách đầy chất suy tưởng và phiêu lưu', 0),
(5, 'data_image/author_images/JKRowling.jpg', 'J.K. Rowling', 6, 'Anh', 'Tác giả của bộ truyện Harry Potter, một trong những hiện tượng văn học nổi tiếng nhất thế kỷ 21', 0),
(6, 'data_image/author_images/cacmac.jpg', 'Karl Marx', 2, 'Đức', 'Nhà tư tưởng, triết học, nhà cách mạng thiên tài, tác giả nhiều bộ sách chính trị nổi tiếng', 0),
(7, 'data_image/author_images/thachlam.jpg', 'Thạch Lam', 1, 'Việt Nam', 'Một trong những nhà văn tiêu biểu của văn học lãng mạn Việt Nam', 0),
(8, 'data_image/author_images/DaleCarnegie.jpg', 'Dale Carnegie', 1, 'Mỹ', 'Nhà văn, nhà thuyết trình người Mỹ, người phát triển các lớp tự giáo dục, bán hàng, kĩ năng giao tiếp', 0),
(9, 'data_image/author_images/Miles_Kelly_Logo.jpg', 'Miles Kelly', 1, 'Anh', 'Nhóm tác giả chuyên sáng tạo các tác phẩm giáo dục khoa học', 0),
(10, 'data_image/author_images/RichardRestak.jpg', 'Richard Restak ', 1, 'Mỹ', 'Ông là một bác sĩ thần kinh học, nhà văn và giáo sư nổi tiếng người Mỹ, chuyên nghiên cứu về bộ não và trí nhớ con người', 0),
(11, 'data_image/author_images/JRBenton.jpg', 'Janetta Rebold Benton', 1, 'Mỹ', ' Bà là một giáo sư, nhà sử học nghệ thuật và tác giả nổi tiếng người Mỹ, chuyên về nghệ thuật thời Trung Cổ và Phục Hưng', 0),
(12, 'data_image/author_images/MichitakeAso.jpg', 'Michitake Aso', 1, 'Mỹ', 'Ông là một nhà sử học môi trường toàn cầu người Mỹ, chuyên nghiên cứu về lịch sử nông nghiệp, y tế và môi trường ở Việt Nam và Pháp trong thế kỷ 19–20\n', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price_in` decimal(10,2) DEFAULT NULL,
  `price_out` decimal(10,2) DEFAULT NULL,
  `price_discount` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `sold` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `publisher_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 3.0,
  `is_discounted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `books`
--

INSERT INTO `books` (`book_id`, `name`, `description`, `price_in`, `price_out`, `price_discount`, `quantity`, `sold`, `category_id`, `publisher_id`, `author_id`, `rating`, `is_discounted`, `created_at`, `updated_at`, `is_deleted`) VALUES
(1, 'Tuổi thơ dữ dội (Tập 1)', 'Tác phẩm nổi tiếng của Phùng Quán, kể về tuổi thơ đầy gian khó của thiếu niên thời chiến.', '120000.00', '156000.00', '132600.00', 50, 0, 6, 1, 1, '3.0', 1, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(2, 'Tuổi thơ dữ dội (Tập 2)', 'Tập 2 của tiểu thuyết Tuổi thơ dữ dội, tác phẩm nổi tiếng của Phùng Quán, kể về tuổi thơ đầy gian khó của thiếu niên thời chiến.', '120000.00', '156000.00', '132600.00', 50, 0, 6, 1, 1, '3.0', 1, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(3, 'Rừng Na-uy', 'Tiểu thuyết nổi tiếng của Haruki Murakami, khám phá tuổi trẻ, tình yêu và sự cô đơn.', '180000.00', '234000.00', '187200.00', 50, 0, 5, 3, 4, '3.0', 1, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(4, 'Harry Potter và Hòn đá Phù thủy', 'Tác phẩm đầu tiên trong loạt truyện Harry Potter huyền thoại của J.K. Rowling.', '200000.00', '260000.00', NULL, 50, 0, 5, 4, 5, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(5, 'Tôi thấy hoa vàng trên cỏ xanh', 'Tiểu thuyết nổi tiếng của Nguyễn Nhật Ánh, kể về tuổi thơ mộng mơ và hoài niệm của những đứa trẻ miền quê Việt Nam.', '95000.00', '123500.00', '104975.00', 50, 0, 5, 4, 3, '3.0', 1, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(6, 'Góc sân và khoảng trời', 'Tập thơ thiếu nhi của nhà thơ Trần Đăng Khoa', '130000.00', '169000.00', NULL, 50, 0, 6, 6, 2, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(7, 'Tư bản 1', 'Bộ Sách Kinh Điển Tư Bản Luận - Phê Phán Khoa Kinh Tế Chính Trị là công trình khoa học kinh điển của Karl Marx (C. Mác)', '400000.00', '520000.00', NULL, 20, 0, 1, 2, 6, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(8, 'Đắc nhân tâm', 'Một tác phẩm nổi tiếng của Dale Carnegie, không chỉ là một cẩm nang về nghệ thuật giao tiếp mà còn là chìa khóa mở ra cánh cửa thành công trong cuộc sống và công việc', '120000.00', '156000.00', NULL, 50, 0, 7, 6, 8, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(9, 'Hà Nội 36 phố phường', 'Cuốn sách mỏng, xinh xắn nhưng cho đến nay, khi người ta nói đến Hà Nội và những tác phẩm thể hiện được tinh hoa, vẻ đẹp của Hà Nội thì người ta vẫn nhắc đến Hà Nội 36 phố phường', '60000.00', '78000.00', NULL, 50, 0, 3, 6, 7, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(10, 'Hạt phù sa kì diệu', 'Truyện cổ tích mới âm hưởng trường ca về Thần Hạn say rượu, Thần Lụt gây mưa bão và những người dân chất phác nơi làng quê xanh mát bóng tre bên sông Kinh Thầy.', '60000.00', '78000.00', NULL, 50, 0, 6, 1, 2, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(11, 'Tư bản 2', 'Tập 2 của bộ Sách Kinh Điển Tư Bản Luận - Phê Phán Khoa Kinh Tế Chính Trị là công trình khoa học kinh điển của Karl Marx (C. Mác)', '400000.00', '520000.00', NULL, 20, 0, 1, 2, 6, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(12, 'Harry Potter và phòng chứa bí mật', 'Tác phẩm thứ hai trong loạt truyện Harry Potter huyền thoại của J.K. Rowling.', '200000.00', '260000.00', NULL, 50, 0, 5, 4, 5, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(13, 'Harry Potter và tên tù nhân ngục Azkaban', 'Tác phẩm thứ ba trong loạt truyện Harry Potter huyền thoại của J.K. Rowling.', '200000.00', '260000.00', NULL, 50, 0, 5, 4, 5, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(14, 'Harry Potter và chiếc cốc lửa', 'Tác phẩm thứ tư trong loạt truyện Harry Potter huyền thoại của J.K. Rowling.', '200000.00', '260000.00', NULL, 50, 0, 5, 4, 5, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(15, '500 dữ kiện về động vật ăn thịt', 'Cuốn sách chứa đựng thông tin về thế giới động vật, cũng như cung cấp những kiến thức cơ bản nhất về vòng đời, thức ăn, sinh sản, môi trường thích nghi, tập tính săn mồi khác của chúng', '100000.00', '130000.00', NULL, 30, 0, 2, 4, 9, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(16, 'Tăng cường trí nhớ, phòng bệnh Alzheimer', 'Tăng cường trí nhớ, phòng bệnh Alzheimer của Tiến sĩ Y khoa Richard Restak là một tác phẩm toàn diện, khám phá cách trí nhớ hoạt động và cách tối ưu hóa khả năng ghi nhớ.', '80000.00', '104000.00', NULL, 20, 0, 8, 4, 10, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(17, 'Harry Potter và Hội Phượng Hoàng', 'Tác phẩm thứ năm trong loạt truyện Harry Potter huyền thoại của J.K. Rowling.', '200000.00', '260000.00', NULL, 50, 0, 5, 4, 5, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(18, 'Harry Potter và hoàng tử lai', 'Tác phẩm thứ sáu trong loạt truyện Harry Potter huyền thoại của J.K. Rowling.', '200000.00', '260000.00', NULL, 50, 0, 5, 4, 5, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(19, 'Để hiểu nghệ thuật', 'Cuốn \"Để Hiểu Nghệ Thuật\" của Janetta Rebold Benton là một hướng dẫn sinh động và dễ tiếp cận dành cho bất kỳ ai muốn khám phá thế giới nghệ thuật thị giác một cách sâu sắc hơn', '240000.00', '312000.00', NULL, 30, 0, 4, 7, 11, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0),
(20, 'Cây cao su ở Việt Nam dưới góc nhìn lịch sử - sinh thái (1897-1975', 'Cuốn sách là một công trình nghiên cứu sâu sắc, kết hợp giữa lịch sử môi trường, y tế, nông nghiệp và chính trị Việt Nam trong gần một thế kỷ', '220000.00', '286000.00', NULL, 35, 0, 3, 8, 12, '3.0', 0, '2025-08-08 14:39:36', '2025-08-08 14:39:36', 0);

--
-- Bẫy `books`
--
DELIMITER $$
CREATE TRIGGER `trg_insert_added_book_history` AFTER INSERT ON `books` FOR EACH ROW BEGIN
  INSERT INTO added_book (
    book_id,
    price_in,
    quantity,
    type,
    added_by,
    added_date
  )
  VALUES (
    NEW.book_id,
    NEW.price_in,
    NEW.quantity,
    'Thêm sách mới',
    1, 
    CURDATE()
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_set_price_out` BEFORE INSERT ON `books` FOR EACH ROW BEGIN
  SET NEW.price_out = ROUND(NEW.price_in * 1.3, 2);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_book_count_after_delete` AFTER DELETE ON `books` FOR EACH ROW BEGIN
  UPDATE authors
  SET book_count = book_count - 1
  WHERE author_id = OLD.author_id;

  UPDATE publishers
  SET book_count = book_count - 1
  WHERE publisher_id = OLD.publisher_id;

  UPDATE categories
  SET book_count = book_count - 1
  WHERE category_id = OLD.category_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_book_count_after_insert` AFTER INSERT ON `books` FOR EACH ROW BEGIN
  UPDATE authors
  SET book_count = book_count + 1
  WHERE author_id = NEW.author_id;

  UPDATE publishers
  SET book_count = book_count + 1
  WHERE publisher_id = NEW.publisher_id;

  UPDATE categories
  SET book_count = book_count + 1
  WHERE category_id = NEW.category_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_price_out` BEFORE UPDATE ON `books` FOR EACH ROW BEGIN
  SET NEW.price_out = ROUND(NEW.price_in * 1.3, 2);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `book_images`
--

CREATE TABLE `book_images` (
  `img_id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `img_url` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `book_images`
--

INSERT INTO `book_images` (`img_id`, `book_id`, `img_url`, `is_primary`) VALUES
(1, 1, 'data_image/book_images/tuoithodudoi1.jpg', 1),
(2, 1, 'data_image/book_images/tuoithodudoi11.jpg', 0),
(3, 1, 'data_image/book_images/tuoithodudoi12.jpg', 0),
(4, 2, 'data_image/book_images/tuoithodudoi2.jpg', 1),
(5, 2, 'data_image/book_images/tuoithodudoi21.jpg', 0),
(6, 2, 'data_image/book_images/tuoithodudoi22.jpg', 0),
(7, 3, 'data_image/book_images/rungnauy.jpg', 1),
(8, 3, 'data_image/book_images/rungnauy1.jpg', 0),
(9, 3, 'data_image/book_images/rungnauy2.jpg', 0),
(10, 4, 'data_image/book_images/HPvahondaphuthuy.jpg', 1),
(11, 4, 'data_image/book_images/HPvahondaphuthuy1.jpg', 0),
(12, 4, 'data_image/book_images/HPvahondaphuthuy2.jpg', 0),
(13, 5, 'data_image/book_images/toithayhoavangtrencoxanh.jpg', 1),
(14, 5, 'data_image/book_images/toithayhoavangtrencoxanh1.jpg', 0),
(15, 5, 'data_image/book_images/toithayhoavangtrencoxanh2.jpg', 0),
(16, 6, 'data_image/book_images/gocsanvakhoangtroi.jpg', 1),
(17, 6, 'data_image/book_images/gocsanvakhoangtroi1.jpg', 0),
(18, 6, 'data_image/book_images/gocsanvakhoangtroi2.jpg', 0),
(19, 7, 'data_image/book_images/tuban1.jpg', 1),
(20, 7, 'data_image/book_images/tuban11.jpg', 0),
(21, 7, 'data_image/book_images/tuban12.jpg', 0),
(22, 8, 'data_image/book_images/dacnhantam.jpg', 1),
(23, 8, 'data_image/book_images/dacnhantam1.jpg', 0),
(24, 8, 'data_image/book_images/dacnhantam2.jpg', 0),
(25, 9, 'data_image/book_images/hanoi36.jpg', 1),
(26, 9, 'data_image/book_images/hanoi361.jpg', 0),
(27, 9, 'data_image/book_images/hanoi362.jpg', 0),
(28, 10, 'data_image/book_images/hatphusakidieu.jpg', 1),
(29, 10, 'data_image/book_images/hatphusakidieu1.jpg', 0),
(30, 10, 'data_image/book_images/hatphusakidieu2.jpg', 0),
(31, 11, 'data_image/book_images/tuban2.jpg', 1),
(32, 11, 'data_image/book_images/tuban21.jpg', 0),
(33, 11, 'data_image/book_images/tuban22.jpg', 0),
(34, 12, 'data_image/book_images/HarryPotter2.jpg', 1),
(35, 12, 'data_image/book_images/HarryPotter21.jpg', 0),
(36, 12, 'data_image/book_images/HarryPotter22.jpg', 0),
(37, 13, 'data_image/book_images/HarryPotter3.jpg', 1),
(38, 13, 'data_image/book_images/HarryPotter31.jpg', 0),
(39, 13, 'data_image/book_images/HarryPotter32.jpg', 0),
(40, 14, 'data_image/book_images/HarryPotter4.jpg', 1),
(41, 14, 'data_image/book_images/HarryPotter41.jpg', 0),
(42, 14, 'data_image/book_images/HarryPotter42.jpg', 0),
(43, 15, 'data_image/book_images/500dulieu.jpg', 1),
(44, 15, 'data_image/book_images/500dulieu1.jpg', 0),
(45, 16, 'data_image/book_images/tangcuongtrinho.jpg', 1),
(46, 16, 'data_image/book_images/tangcuongtrinho1.jpg', 0),
(47, 16, 'data_image/book_images/tangcuongtrinho2.jpg', 0),
(48, 17, 'data_image/book_images/HarryPotter5.jpg', 1),
(49, 17, 'data_image/book_images/HarryPotter51.jpg', 0),
(50, 17, 'data_image/book_images/HarryPotter52.jpg', 0),
(51, 18, 'data_image/book_images/HarryPotter6.jpg', 1),
(52, 18, 'data_image/book_images/HarryPotter61.jpg', 0),
(53, 18, 'data_image/book_images/HarryPotter62.jpg', 0),
(54, 19, 'data_image/book_images/dehieunghethuat.jpg', 1),
(55, 19, 'data_image/book_images/dehieunghethuat1.jpg', 0),
(56, 19, 'data_image/book_images/dehieunghethuat2.jpg', 0),
(57, 20, 'data_image/book_images/caycaosu.jpg', 1),
(58, 20, 'data_image/book_images/caycaosu1.jpg', 0),
(59, 20, 'data_image/book_images/caycaosu2.jpg', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `final_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Bẫy `carts`
--
DELIMITER $$
CREATE TRIGGER `trg_cart_shipping_fee` BEFORE INSERT ON `carts` FOR EACH ROW BEGIN
  SET NEW.shipping_fee = IF(NEW.total_price >= 500000, 0, 30000);
  SET NEW.final_amount = NEW.total_price + NEW.shipping_fee;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_cart_shipping_fee_update` BEFORE UPDATE ON `carts` FOR EACH ROW BEGIN
  SET NEW.shipping_fee = IF(NEW.total_price >= 500000, 0, 30000);
  SET NEW.final_amount = NEW.total_price + NEW.shipping_fee;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_details`
--

CREATE TABLE `cart_details` (
  `cart_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price_out` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `book_count` int(11) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `book_count`, `is_deleted`) VALUES
(1, 'Chính trị - Kinh Tế', 2, 0),
(2, 'Khoa học - Kỹ thuật', 1, 0),
(3, 'Lịch sử - Văn hóa', 2, 0),
(4, 'Nghệ thuật - Thiết kế', 1, 0),
(5, 'Tiểu thuyết', 8, 0),
(6, 'Sách thiếu nhi', 4, 0),
(7, 'Tâm lý - Kỹ năng sống', 1, 0),
(8, 'Sức khỏe - Dinh dưỡng', 1, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `discount`
--

CREATE TABLE `discount` (
  `discount_id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `discount_percent` decimal(4,2) DEFAULT NULL CHECK (`discount_percent` between 0 and 100),
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `discount`
--

INSERT INTO `discount` (`discount_id`, `book_id`, `discount_percent`, `start_date`, `end_date`, `is_deleted`) VALUES
(1, 1, '15.00', '2025-07-15', '2025-09-30', 0),
(2, 2, '15.00', '2025-07-15', '2025-09-30', 0),
(3, 5, '15.00', '2025-07-15', '2025-09-30', 0),
(4, 3, '20.00', '2025-07-15', '2025-09-30', 0);

--
-- Bẫy `discount`
--
DELIMITER $$
CREATE TRIGGER `trg_restore_price_on_discount_hide` AFTER UPDATE ON `discount` FOR EACH ROW BEGIN
  IF OLD.is_deleted = 0 AND NEW.is_deleted = 1 THEN
    UPDATE books
    SET price_discount = NULL,
        is_discounted = 0
    WHERE book_id = NEW.book_id AND is_deleted = 0;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_price_discount` AFTER INSERT ON `discount` FOR EACH ROW BEGIN
  IF NEW.is_deleted = 0 AND NEW.start_date <= CURDATE() AND NEW.end_date >= CURDATE() THEN
    UPDATE books
    SET price_discount = price_out * (1 - NEW.discount_percent / 100),
        is_discounted = 1
    WHERE book_id = NEW.book_id AND is_deleted = 0;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_price_discount_on_change` AFTER UPDATE ON `discount` FOR EACH ROW BEGIN
  -- Chỉ cập nhật nếu không bị xóa và thời gian đang hiệu lực
  IF NEW.is_deleted = 0 AND NEW.start_date <= CURDATE() AND NEW.end_date >= CURDATE() THEN
    UPDATE books
    SET price_discount = price_out * (1 - NEW.discount_percent / 100),
        is_discounted = 1
    WHERE book_id = NEW.book_id AND is_deleted = 0;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorites`
--

CREATE TABLE `favorites` (
  `favorite_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `liked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `final_amount` decimal(10,2) DEFAULT 0.00,
  `payment_method` enum('COD','Online') DEFAULT 'COD',
  `paid_at` datetime DEFAULT NULL,
  `status` enum('Chờ duyệt','Đang giao','Đã thanh toán','Hoàn thành','Đã hủy') DEFAULT 'Chờ duyệt',
  `order_date` date DEFAULT curdate(),
  `updated_date` date DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Bẫy `orders`
--
DELIMITER $$
CREATE TRIGGER `trg_calculate_shipping_fee` BEFORE INSERT ON `orders` FOR EACH ROW BEGIN
  SET NEW.shipping_fee = IF(NEW.total_price >= 500000, 0, 30000);
  SET NEW.final_amount = NEW.total_price + NEW.shipping_fee;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price_out` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `publishers`
--

CREATE TABLE `publishers` (
  `publisher_id` int(11) NOT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `book_count` int(11) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `publishers`
--

INSERT INTO `publishers` (`publisher_id`, `logo_url`, `name`, `book_count`, `is_deleted`) VALUES
(1, 'data_image/publisher_images/nxbkimdong.jpg', 'NXB Kim Đồng', 3, 0),
(2, 'data_image/publisher_images/nxbsuthat.jpg', 'NXB Chính trị quốc gia Sự thật', 2, 0),
(3, 'data_image/publisher_images/nxbhoinhavan.jpg', 'NXB Hội nhà văn', 1, 0),
(4, 'data_image/publisher_images/nxbtre.jpg', 'NXB Trẻ', 9, 0),
(5, 'data_image/publisher_images/nxbgiaoduc.jpg', 'NXB Giáo dục', 0, 0),
(6, 'data_image/publisher_images/nxbvanhoc.jpg', 'NXB Văn học', 3, 0),
(7, 'data_image/publisher_images/nxbthegioi.jpg', 'NXB Thế giới', 1, 0),
(8, 'data_image/publisher_images/nxbtonghoptphcm.jpg', 'NXB Tổng hợp Thành phố Hồ Chí Minh', 1, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `refunds`
--

CREATE TABLE `refunds` (
  `refund_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `refund_method` enum('Chuyển khoản','MOMO','VNPAY') DEFAULT 'Chuyển khoản',
  `refund_reason` enum('Khách hàng hủy đơn','Admin hủy đơn') DEFAULT NULL,
  `status` enum('Đang chờ','Đã hoàn') DEFAULT 'Đang chờ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `status` enum('Chờ duyệt','Đã duyệt') DEFAULT 'Chờ duyệt',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `name` varchar(100) DEFAULT NULL,
  `avt` varchar(255) DEFAULT 'data_image/avatar/default.jpg',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`, `name`, `avt`, `phone`, `address`, `created_at`, `updated_at`, `reset_token`, `reset_token_expiry`, `is_deleted`) VALUES
(1, 'huahungtan001@gmail.com', '123456', 'admin', 'Hứa Hùng Tấn', 'data_image/avatar/huahungtan.jpg', '0947227152', 'Phú Tân, Phú Tân, Cà Mau', '2025-08-08 14:39:36', '2025-08-08 14:39:36', NULL, NULL, 0),
(2, 'tanc2200011@student.ctu.edu.vn', '123456', 'customer', 'Hùng Tấn Hứa', 'data_image/avatar/hungtanhua.jpg', '0352164171', 'Phú Tân, Phú Tân, Cà Mau', '2025-08-08 14:39:36', '2025-08-08 14:39:36', NULL, NULL, 0),
(3, 'dlxyz1234@gmail.com', '123456', 'customer', 'Lý Phượng Minh', 'data_image/avatar/lyphuongminh.jpg', '0947258310', 'An Khánh, Ninh Kiều, Cần Thơ', '2025-08-08 14:39:36', '2025-08-08 14:39:36', NULL, NULL, 0),
(4, 'boan697@gmail.com', '123456', 'customer', 'Võ Kim Anh', 'data_image/avatar/vokimanh.jpg', '0918255774', 'Mỹ Xuyên, Long Xuyên, An Giang', '2025-08-08 14:39:36', '2025-08-08 14:39:36', NULL, NULL, 0),
(5, 'cus1test@gmail.com', '123456', 'customer', 'Nguyễn Văn Hòa', 'data_image/avatar/default.jpg', '0918255775', 'Long Điền, Đông Hải, Bạc Liêu', '2025-08-08 14:39:36', '2025-08-08 14:39:36', NULL, NULL, 0),
(6, 'cus2test@gmail.com', '123456', 'customer', 'Lê Hoàng Hải', 'data_image/avatar/default.jpg', '0947241775', 'Phước Long, Phước Long, Bạc Liêu', '2025-08-08 14:39:36', '2025-08-08 14:39:36', NULL, NULL, 0),
(7, 'cus3test@gmail.com', '123456', 'customer', 'Nguyễn Diệu Vân', 'data_image/avatar/default.jpg', '0918254175', 'Đông Thạnh, Châu Thành, Hậu Giang', '2025-08-08 14:39:36', '2025-08-08 14:39:36', NULL, NULL, 0),
(8, 'cus4test@gmail.com', '123456', 'customer', 'Lâm Thanh Mỹ', 'data_image/avatar/default.jpg', '0947252376', 'An Đức, Ba Tri, Bến Tre', '2025-08-08 14:39:36', '2025-08-08 14:39:36', NULL, NULL, 0),
(9, 'cus5test@gmail.com', '123456', 'customer', 'Nguyễn Hoàng Thịnh', 'data_image/avatar/default.jpg', '0918258879', 'Thuận Hòa, Long Mỹ, Hậu Giang', '2025-08-08 14:39:36', '2025-08-08 14:39:36', NULL, NULL, 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `added_book`
--
ALTER TABLE `added_book`
  ADD PRIMARY KEY (`added_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `added_by` (`added_by`);

--
-- Chỉ mục cho bảng `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`author_id`);

--
-- Chỉ mục cho bảng `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `publisher_id` (`publisher_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Chỉ mục cho bảng `book_images`
--
ALTER TABLE `book_images`
  ADD PRIMARY KEY (`img_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `cart_details`
--
ALTER TABLE `cart_details`
  ADD PRIMARY KEY (`cart_id`,`book_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Chỉ mục cho bảng `discount`
--
ALTER TABLE `discount`
  ADD PRIMARY KEY (`discount_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Chỉ mục cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favorite_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`book_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_id`,`book_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Chỉ mục cho bảng `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`publisher_id`);

--
-- Chỉ mục cho bảng `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`refund_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `added_book`
--
ALTER TABLE `added_book`
  MODIFY `added_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `authors`
--
ALTER TABLE `authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `book_images`
--
ALTER TABLE `book_images`
  MODIFY `img_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `discount`
--
ALTER TABLE `discount`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `publishers`
--
ALTER TABLE `publishers`
  MODIFY `publisher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `refunds`
--
ALTER TABLE `refunds`
  MODIFY `refund_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `added_book`
--
ALTER TABLE `added_book`
  ADD CONSTRAINT `added_book_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `added_book_ibfk_2` FOREIGN KEY (`added_by`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`publisher_id`),
  ADD CONSTRAINT `books_ibfk_3` FOREIGN KEY (`author_id`) REFERENCES `authors` (`author_id`);

--
-- Các ràng buộc cho bảng `book_images`
--
ALTER TABLE `book_images`
  ADD CONSTRAINT `book_images_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Các ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `cart_details`
--
ALTER TABLE `cart_details`
  ADD CONSTRAINT `cart_details_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`),
  ADD CONSTRAINT `cart_details_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Các ràng buộc cho bảng `discount`
--
ALTER TABLE `discount`
  ADD CONSTRAINT `discount_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Các ràng buộc cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Các ràng buộc cho bảng `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Các ràng buộc cho bảng `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `refunds_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `review_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
