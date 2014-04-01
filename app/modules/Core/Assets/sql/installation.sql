SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Dumping data for table `roles`
--

INSERT IGNORE INTO `roles` (`id`, `name`, `description`, `is_default`, `type`, `undeletable`) VALUES
(1, 'Admin', 'Administrator.', 0, 'admin', 1),
(2, 'User', 'Default user role.', 1, 'user', 1),
(3, 'Guest', 'Guest role.', 0, 'guest', 1);

-- Dumping data for table `access`
--

INSERT IGNORE INTO `access` (`object`, `action`, `role_id`, `value`) VALUES
('AdminArea', 'access', 1, 'allow'),
('AdminArea', 'access', 2, 'deny'),
('AdminArea', 'access', 3, 'deny');

--
-- Dumping data for table `pages`
--

INSERT IGNORE INTO `pages` (`id`, `title`, `type`, `url`, `description`, `keywords`, `layout`, `controller`, `roles`, `view_count`) VALUES
(1, 'Header', 'header', NULL, 'Header content', '', 'middle', NULL, NULL, NULL),
(2, 'Footer', 'footer', NULL, 'Footer content', '', 'middle', NULL, NULL, NULL),
(3, 'Home', 'home', '/', 'PhalconEye Home Page', 'PhalconEye', 'top_right_middle_left', NULL, NULL, 0);

--
-- Dumping data for table `widgets`
--

INSERT IGNORE INTO `widgets` (`id`, `module`, `name`, `description`, `is_paginated`, `is_acl_controlled`, `admin_form`, `enabled`) VALUES
(1, 'core', 'HtmlBlock', 'Insert any HTML of you choice', 0, 1, 'action', 1),
(2, 'core', 'Menu', 'Render menu', 0, 1, '\\Core\\Form\\Admin\\Widget\\Menu', 1),
(3, 'core', 'Header', 'Settings for header of you site.', 0, 1, '\\Core\\Form\\Admin\\Widget\\Header', 1);

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `page_id`, `widget_id`, `widget_order`, `layout`, `params`) VALUES
  (1, 3, 1, 1, 'top', '{"title":"Header","html_en":"<p>Header<\\/p>\\r\\n","html_ru":"","html":null,"roles":null,"content_id":"67"}'),
  (2, 3, 1, 1, 'right', '{"title":"Right","html_en":"<p>Right<\\/p>\\r\\n","html_ru":"","html":null,"roles":null,"content_id":"70"}'),
  (3, 3, 1, 1, 'left', '{"title":"Left","html_en":"<p>Left<\\/p>\\r\\n","html_ru":"","html":null,"roles":null,"content_id":"71"}'),
  (4, 3, 1, 1, 'middle', '{"title":"Content","html_en":"<p>Content<\\/p>\\r\\n","html_ru":"","html":null,"roles":null}'),
  (5, 3, 1, 2, 'top', '{"title":"Header2","html_en":"<p>Header2<\\/p>\\r\\n","html_ru":"","html":null,"roles":null}'),
  (6, 3, 1, 2, 'right', '{"title":"Right2","html_en":"<p>Right2<\\/p>\\r\\n","html_ru":"","html":null,"roles":null}'),
  (7, 3, 1, 2, 'middle', '{"title":"Content2","html_en":"<p>Content2<\\/p>\\r\\n","html_ru":"","html":null,"roles":null}'),
  (8, 3, 1, 2, 'left', '{"title":"Left2","html_en":"<p>Left2<\\/p>\\r\\n","html_ru":"","html":null,"roles":null}'),
  (9, 1, 3, 1, 'middle', '{"logo":"assets\\/img\\/core\\/pe_logo.png","show_title":null,"show_auth":"1","roles":null,"content_id":"112"}'),
  (10, 1, 2, 2, 'middle', '{"title":"","class":"","menu":"Default menu","menu_id":"1","roles":null}'),
  (11, 2, 1, 1, 'middle', '{"title":"","html_en":"<p style=\\"text-align: center;\\">PhalconEye v.0.4.0<\\/p>\\r\\n","roles":null}');

--
-- Dumping data for table `languages`
--

INSERT IGNORE INTO `languages` (`id`, `name`, `language`, `locale`, `icon`) VALUES
(1, 'English', 'en', 'en_US', NULL);

--
-- Dumping data for table `menus`
--

INSERT IGNORE INTO `menus` (`id`, `name`) VALUES
(1, 'Default menu');

--
-- Dumping data for table `menu_items`
--

INSERT IGNORE INTO `menu_items` (`id`, `title`, `menu_id`, `parent_id`, `page_id`, `url`, `onclick`, `target`, `tooltip`, `tooltip_position`, `icon`, `icon_position`, `item_order`, `languages`, `roles`, `is_enabled`) VALUES
(1, 'Home', 1, NULL, NULL, '/', NULL, NULL, NULL, 'top', 'files/PE_logo.png', 'left', 0, NULL, NULL, 1),
(2, 'Github', 1, NULL, NULL, 'https://github.com/lantian/PhalconEye', NULL, '_blank', '<p><b><span style="color:#FF0000;">G</span>it<span style="color:#FF0000;">H</span>ub Page</b></p>\r\n', 'right', 'files/github.gif', 'left', 1, NULL, NULL, 1);

--
-- Dumping data for table `packages`
--

INSERT IGNORE INTO `packages` (`id`, `name`, `type`, `title`, `description`, `version`, `author`, `website`, `enabled`, `is_system`) VALUES
(1, 'core', 'module', 'Core', 'PhalconEye Core', '0.4.0', 'PhalconEye Team', 'http://phalconeye.com/', 1, 1),
(2, 'user', 'module', 'Users', 'PhalconEye Users', '0.4.0', 'PhalconEye Team', 'http://phalconeye.com/', 1, 1);

--
-- Dumping data for table `settings`
--

INSERT IGNORE INTO `settings` (`name`, `value`) VALUES
('system_default_language', 'en'),
('system_theme', 'default'),
('system_title', 'Phalcon Eye');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
