-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 30, 2025 at 08:36 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `foodfusion_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `community_cookbook`
--

CREATE TABLE `community_cookbook` (
  `entry_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `cuisine_type` varchar(50) DEFAULT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `claps` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_cookbook`
--

INSERT INTO `community_cookbook` (`entry_id`, `user_id`, `title`, `cuisine_type`, `content`, `image_url`, `claps`, `created_at`) VALUES
(6, 2, 'Hamburger', 'American', 'Why is Burger King selling fired chickens?', './uploads/1758384151_event_italian_workshop.jpeg', 2, '2025-09-20 16:02:31'),
(7, 1, 'Avocado Toast', 'American', 'A quick and healthy avocado toast topped with lime and chili flakes.', './uploads/1759170407_recipe_avocado_toast.jpeg', NULL, '2025-09-29 18:26:47'),
(8, 1, 'Classic Lasagna', 'Italian', 'Layers of pasta, rich meat sauce, and creamy cheese baked to perfection.', './uploads/1759170655_recipe_lasagna.jpeg', NULL, '2025-09-29 18:30:55'),
(9, 1, 'Thai Noodles', 'Thai', 'Stir-fried noodles with vegetables, peanuts, and authentic Thai spices.', './uploads/1759170674_recipe_thai_noodles.jpg', NULL, '2025-09-29 18:31:14'),
(10, 1, 'Veggie Panini', 'Mediterranean', 'Grilled panini filled with roasted vegetables and mozzarella cheese.', './uploads/1759170695_recipe_veggie_panini.jpeg', NULL, '2025-09-29 18:31:35'),
(11, 1, 'Quinoa Salad', 'Fusion', 'A refreshing quinoa salad with cherry tomatoes, cucumber, and feta.', './uploads/1759170721_recipe_quinoa_salad.jpeg', NULL, '2025-09-29 18:32:01'),
(12, 1, 'Chocolate Lava Cake', 'French', 'Warm chocolate cake with a gooey molten center, served with ice cream.', './uploads/1759170746_recipe_lava_cake.jpeg', NULL, '2025-09-29 18:32:26');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`message_id`, `name`, `email`, `subject`, `message`, `submitted_at`) VALUES
(1, 'Bhone Myint Thu', 'bmt@gmail.com', 'Request', 'Could you please make some more recipes?', '2025-09-19 18:30:54'),
(2, 'Bhone Myint Thu', 'bmt@gmail.com', 'Request', 'Could you please make some more recipes?', '2025-09-19 18:31:59');

-- --------------------------------------------------------

--
-- Table structure for table `culinary_trends`
--

CREATE TABLE `culinary_trends` (
  `trend_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `cover_img_src` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `culinary_trends`
--

INSERT INTO `culinary_trends` (`trend_id`, `title`, `description`, `cover_img_src`, `created_at`) VALUES
(1, 'Plant-Based Revolution', 'More chefs are embracing plant-based ingredients to create delicious, sustainable meals.', './assets/images/trend_plantbased.jpeg', '2025-09-24 04:32:44'),
(2, 'Zero-Waste Cooking', 'Creative ways to minimize food waste are becoming mainstream in home and professional kitchens.', './assets/images/trend_zerowaste.jpeg', '2025-09-24 04:32:44'),
(3, 'Global Fusion Flavors', 'Chefs are blending cuisines from around the world, creating unique flavor combinations.', './assets/images/trend_fusion.jpeg', '2025-09-24 04:32:44'),
(4, 'Fermentation Revival', 'Fermented foods like kimchi, kombucha, and sourdough are gaining popularity for their flavors and health benefits.', './assets/images/trend_fermentation.jpeg', '2025-09-27 19:30:19'),
(5, 'Comfort Food Reinvented', 'Classic comfort dishes are being modernized with gourmet techniques and global influences.', './assets/images/trend_comfortfood.jpeg', '2025-09-27 19:30:19'),
(6, 'Tech-Driven Kitchens', 'Smart appliances and AI-driven cooking tools are transforming how chefs and home cooks prepare meals.', './assets/images/trend_techkitchen.jpeg', '2025-09-27 19:30:19');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `event_date` datetime NOT NULL,
  `cover_img_src` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `description`, `event_date`, `cover_img_src`, `location`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Italian Cooking Workshop', 'Learn to make fresh pasta and sauces like a pro.', '2025-10-05 18:00:00', './assets/images/event_italian_workshop.jpeg', 'Culinary Studio, NYC', 1, '2025-09-18 19:21:40', '2025-09-18 19:21:40'),
(2, 'Vegan Baking Class', 'Delicious plant-based desserts and breads.', '2025-10-12 15:00:00', './assets/images/event_vegan_baking.jpeg', 'Green Kitchen, LA', 2, '2025-09-18 19:21:40', '2025-09-18 19:21:40'),
(3, 'Sushi Making Night', 'Master the art of sushi rolling with our chef.', '2025-10-15 17:00:00', './assets/images/event_sushi_making.jpeg', 'Sushi Bar, SF', 1, '2025-09-18 19:21:40', '2025-09-18 19:21:40'),
(4, 'French Pastry Workshop', 'Learn to bake croissants, eclairs, and more.', '2025-10-20 16:00:00', './assets/images/event_french_pastry.jpeg', 'Baker\'s Studio, Boston', 2, '2025-09-18 19:21:40', '2025-09-18 19:21:40'),
(5, 'Mediterranean Feast', 'Hands-on cooking class featuring Mediterranean cuisine.', '2025-10-25 18:30:00', './assets/images/event_mediterranean.jpeg', 'Culinary Center, Miami', 1, '2025-09-18 19:21:40', '2025-09-18 19:21:40'),
(6, 'Chocolate & Dessert Masterclass', 'Explore advanced chocolate and dessert techniques.', '2025-10-30 14:00:00', './assets/images/event_chocolate.jpeg', 'Sweet Lab, Chicago', 2, '2025-09-18 19:21:40', '2025-09-18 19:21:40');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `cover_img_src` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `cuisine_type` varchar(50) DEFAULT NULL,
  `dietary_preference` varchar(50) DEFAULT NULL,
  `difficulty` enum('Easy','Medium','Hard') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`recipe_id`, `user_id`, `title`, `description`, `cover_img_src`, `is_featured`, `cuisine_type`, `dietary_preference`, `difficulty`, `created_at`) VALUES
(1, 1, 'Spicy Thai Noodles', 'A quick and flavorful noodle dish with a spicy kick.', './assets/images/recipe_thai_noodles.jpg', 1, 'Thai', 'Vegetarian', 'Medium', '2025-09-18 18:30:44'),
(2, 2, 'Classic Lasagna', 'Rich layers of pasta, beef, and cheese baked to perfection.', './assets/images/recipe_lasagna.jpeg', 1, 'Italian', 'Non-Vegetarian', 'Hard', '2025-09-18 18:30:44'),
(3, 1, 'Avocado Toast', 'Simple yet delicious breakfast option with fresh avocado.', './assets/images/recipe_avocado_toast.jpeg', 1, 'American', 'Vegan', 'Easy', '2025-09-18 18:30:44'),
(6, 1, 'Mediterranean Quinoa Salad', 'A refreshing salad with quinoa, fresh vegetables, and a lemon-olive oil dressing.', './uploads/1759001356_recipe_quinoa_salad.jpeg', 1, 'Mediterranean', 'Vegan', 'Easy', '2025-09-27 19:29:16'),
(10, 1, 'Chicken Tikka Masala', 'Tender chicken pieces cooked in a creamy, spiced tomato sauce.', './uploads/1759002911_recipe_tikka_masala.jpeg', 1, 'Indian', 'Non-Vegetarian', 'Medium', '2025-09-27 19:55:11'),
(11, 1, 'Chocolate Lava Cake', 'Decadent molten chocolate cake perfect for dessert lovers.', './uploads/1759003003_recipe_lava_cake.jpeg', 1, 'French', 'Vegetarian', 'Hard', '2025-09-27 19:56:43'),
(13, 1, 'Grilled Veggie Panini', 'A warm sandwich loaded with grilled vegetables, melted cheese, and fresh herbs.', './assets/images/recipe_veggie_panini.jpeg', 0, 'Italian', 'Vegetarian', 'Easy', '2025-09-27 19:59:31');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `resource_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `resource_type` enum('RecipeCard','Tutorial','Video','Infographic') DEFAULT NULL,
  `file_url` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` enum('Culinary','Educational') DEFAULT 'Culinary'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`resource_id`, `title`, `resource_type`, `file_url`, `uploaded_at`, `category`) VALUES
(1, 'Lasagna Recipe Card', 'RecipeCard', './assets/resources/culinary/LASAGNA-recipe-card.pdf', '2025-09-20 17:39:46', 'Culinary'),
(2, 'Vegetarian Stir-Fry Recipe Card', 'RecipeCard', './assets/resources/culinary/veg_stir_fry.pdf', '2025-09-20 17:39:46', 'Culinary'),
(3, 'Spaghetti Recipe Card', 'RecipeCard', './assets/resources/culinary/spaghetti.pdf', '2025-09-20 17:39:46', 'Culinary'),
(4, 'Pasta Recipe Card', 'RecipeCard', './assets/resources/culinary/pasta.pdf', '2025-09-20 17:39:46', 'Culinary'),
(5, 'Hamburger Recipe', 'RecipeCard', './assets/resources/culinary/hamburger.pdf', '2025-09-20 17:39:46', 'Culinary'),
(6, 'Mastering Knife Skills', 'Tutorial', './assets/resources/culinary/mastering_knife_skills.pdf', '2025-09-20 17:39:46', 'Culinary'),
(7, 'Cooking Basics', 'Tutorial', './assets/resources/culinary/cooking_basics.pdf', '2025-09-20 17:39:46', 'Culinary'),
(8, 'Fundamentals of Cooking', 'Tutorial', './assets/resources/culinary/fundamentals_of_cooking.pdf', '2025-09-20 17:39:46', 'Culinary'),
(9, 'Cooking Demonstration Guide', 'Tutorial', './assets/resources/culinary/cooking_demonstration_guide.pdf', '2025-09-20 17:39:46', 'Culinary'),
(10, 'Herbs & Spices Info', 'Infographic', './assets/resources/culinary/herbs_spice_info.jpeg', '2025-09-20 17:39:46', 'Culinary'),
(11, 'Spice Elements Set', 'Infographic', './assets/resources/culinary/spice_elements_set.jpeg', '2025-09-20 17:39:46', 'Culinary'),
(12, 'Beef Temperature Chart', 'Infographic', './assets/resources/culinary/beef_temp_chart.jpeg', '2025-09-20 17:39:46', 'Culinary'),
(13, 'Internal Temperature for Meat', 'Infographic', './assets/resources/culinary/internal_temp_for_meat.jpeg', '2025-09-20 17:39:46', 'Culinary'),
(14, 'Safe Internal Temperature Guide', 'Infographic', './assets/resources/culinary/safe_internal_temp.pdf', '2025-09-20 17:39:46', 'Culinary'),
(15, 'How to Make Sushi at Home', 'Video', 'https://www.youtube.com/embed/joweUxpHaqc?si=x48ZqUpp-WC31bh7', '2025-09-20 17:39:46', 'Culinary'),
(16, 'Kitchen Hacks You Must Know! 16 Quick & Brilliant Tricks That Work Like Magic', 'Video', 'https://www.youtube.com/embed/-X4TEu5Jb84?si=fRlcJig_HTSJng1r', '2025-09-20 17:39:46', 'Culinary'),
(17, 'Japanese egg soft-boiled omelette rice (omurice) Japanese Street Food in Korea / Korean Street Food', 'Video', 'https://www.youtube.com/embed/jdK9i6kzbfY?si=r88_LzG0-kUUJWO0', '2025-09-20 17:39:46', 'Culinary'),
(18, 'Every Way To Cook Eggs', 'Video', 'https://www.youtube.com/embed/b52h7kraC3A?si=cq9VCcnmTcuNGW4e', '2025-09-20 17:39:46', 'Culinary'),
(19, 'Introduction to Solar Power', 'Tutorial', './assets/resources/educational/basic_solar_energy.pdf', '2025-09-20 18:09:18', 'Educational'),
(20, 'Solar Panel Installation Guide', 'RecipeCard', './assets/resources/educational/solar_installation_guide.pdf', '2025-09-20 18:09:18', 'Educational'),
(21, 'How Solar Panels Work', 'Video', 'https://www.youtube.com/embed/xKxrkht7CpY?si=rIReUuuKV98hE5IA', '2025-09-20 18:09:18', 'Educational'),
(22, 'Wind Energy Explained', 'Tutorial', './assets/resources/educational/wind_energy_explained.pdf', '2025-09-20 18:09:18', 'Educational'),
(23, 'Wind Turbine Diagram', 'Infographic', './assets/resources/educational/wind_turbine_infographic.jpeg', '2025-09-20 18:09:18', 'Educational'),
(24, 'How Do Wind Turbines Work?', 'Video', 'https://www.youtube.com/embed/xy9nj94xvKA?si=3UaPJINSoM7bPaxn', '2025-09-20 18:09:18', 'Educational'),
(25, 'Hydropower Basics', 'Tutorial', './assets/resources/educational/hydropower_basics.pdf', '2025-09-20 18:09:18', 'Educational'),
(26, 'Hydropower Plant Process', 'Infographic', './assets/resources/educational/hydropower_process.jpeg', '2025-09-20 18:09:18', 'Educational'),
(27, 'Hydropower 101', 'Video', 'https://www.youtube.com/embed/q8HmRLCgDAI?si=Cl1iAK_Gqbqf32V5', '2025-09-20 18:09:18', 'Educational'),
(28, 'Geothermal Energy Overview', 'Tutorial', './assets/resources/educational/geothermal_overview.pdf', '2025-09-20 18:09:18', 'Educational'),
(29, 'Geothermal Plant Layout', 'Infographic', './assets/resources/educational/geothermal_plant.jpeg', '2025-09-20 18:09:18', 'Educational');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `password_hash` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.png',
  `failed_attempts` int(11) DEFAULT 0,
  `lock_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `is_admin`, `password_hash`, `profile_picture`, `failed_attempts`, `lock_until`, `created_at`, `updated_at`) VALUES
(1, 'Bhone ', 'Myint Thu', 'b@gmail.com', 1, '$2y$10$kkhVw9LLozWkZbk7opm8DecwhjLiPCLlXBRuD2cnbJfAQeUwc2Jl.', '1758396627_Gemini_Generated_Image_z325b8z325b8z325.png', 0, NULL, '2025-09-08 17:43:47', '2025-09-27 19:21:48'),
(2, 'testuser', '1', 'testuser1@gmail.com', 0, '$2y$10$KY7E3Qcg2BL.ThNFVYUzW.vIDMq.Dkaal1ydaq7ZsVZWTWiLr.yhq', NULL, 0, '2025-09-27 21:33:27', '2025-09-09 17:57:46', '2025-09-27 19:30:27'),
(5, 'BhoneMyint', 'Thu', 'bmt12@gmail.com', 0, '$2y$10$3CdfMMEMe.NhjavGpElKB.wCb3RZTKDpTUKSmUclT1b.SKQVk.qnm', NULL, 0, NULL, '2025-09-14 03:01:04', '2025-09-20 16:07:46'),
(6, 'TestUser', '2', 'testuser2@gmail.com', 0, '$2y$10$o3tnW87WLf4yXL7fVayu2eiiUWUAl54CqFB/LuhCSzs3GHtFmgsJS', NULL, 0, NULL, '2025-09-18 17:52:43', '2025-09-27 18:08:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `community_cookbook`
--
ALTER TABLE `community_cookbook`
  ADD PRIMARY KEY (`entry_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `culinary_trends`
--
ALTER TABLE `culinary_trends`
  ADD PRIMARY KEY (`trend_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`recipe_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`resource_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `community_cookbook`
--
ALTER TABLE `community_cookbook`
  MODIFY `entry_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `culinary_trends`
--
ALTER TABLE `culinary_trends`
  MODIFY `trend_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `recipe_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `resource_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `community_cookbook`
--
ALTER TABLE `community_cookbook`
  ADD CONSTRAINT `community_cookbook_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
