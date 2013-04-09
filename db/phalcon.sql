SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `phalcon`
--

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

CREATE TABLE IF NOT EXISTS `access` (
  `object` varchar(55) NOT NULL,
  `action` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `value` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`object`,`action`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `access`
--

INSERT INTO `access` (`object`, `action`, `role_id`, `value`) VALUES
('AdminArea', 'access', 1, 'allow'),
('AdminArea', 'access', 3, 'allow'),
('Page', 'page_footer', 1, '<hr/>'),
('Page', 'page_footer', 2, '<hr/>'),
('Page', 'show_views', 1, 'allow'),
('Page', 'show_views', 2, 'allow');

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `widget_id` int(11) NOT NULL,
  `widget_order` int(5) NOT NULL DEFAULT '0',
  `layout` varchar(50) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `WIDGET_INDEX` (`widget_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=110 ;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `page_id`, `widget_id`, `widget_order`, `layout`, `params`) VALUES
(65, 2, 1, 1, 'middle', '{"title":"","html":"<p style=\\"text-align: center;\\">Phalcon Eye CMS<\\/p>\\r\\n"}'),
(67, 3, 1, 1, 'top', '{"title":"","html":"<h4 style=\\"font-style: italic;\\"><span style=\\"font-size:18px;\\">Wecome to Demo site!<\\/span><\\/h4>\\r\\n"}'),
(68, 1, 2, 2, 'middle', '{"title":"","class":"","menu":"Header menu","menu_id":"3"}'),
(70, 3, 1, 1, 'right', '{"title":"Right","html":"<p>This is right layout.<\\/p>\\r\\n"}'),
(71, 3, 1, 1, 'left', '{"title":"Left","html":"<p>This is right layout.<\\/p>\\r\\n"}'),
(72, 3, 1, 1, 'middle', '{"title":"About","html":"<p>Phalcon Eye - CMS based on Phalcon PHP Framework <a href=\\"https:\\/\\/github.com\\/phalcon\\/cphalcon\\" style=\\"margin: 0px; padding: 0px; border: 0px; color: rgb(65, 131, 196); text-decoration: none;\\">https:\\/\\/github.com\\/phalcon\\/cphalcon<\\/a>.&nbsp;<\\/p>\\r\\n"}'),
(90, 1, 3, 1, 'middle', '{"logo":"\\/public\\/img\\/phalconeye\\/PE_logo.png","show_title":"","show_auth":"1"}');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `locale` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `locale`, `icon`) VALUES
(1, 'English', 'en', '/public/img/phalconeye/languages/en'),
(2, 'Russian', 'ru', '/public/img/phalconeye/languages/ru.png');

-- --------------------------------------------------------

--
-- Table structure for table `language_translations`
--

CREATE TABLE IF NOT EXISTS `language_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) DEFAULT NULL,
  `original` longtext NOT NULL,
  `translated` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E3BB4E5282F1BAF4` (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`) VALUES
(3, 'Header menu');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `onclick` varchar(255) DEFAULT NULL,
  `target` varchar(10) DEFAULT NULL,
  `tooltip` varchar(255) DEFAULT NULL,
  `tooltip_position` varchar(10) DEFAULT 'top',
  `icon` varchar(255) DEFAULT NULL,
  `icon_position` varchar(10) NOT NULL DEFAULT 'left',
  `item_order` int(11) NOT NULL DEFAULT '1',
  `languages` varchar(150) DEFAULT NULL,
  `roles` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=89 ;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `title`, `menu_id`, `parent_id`, `page_id`, `url`, `onclick`, `target`, `tooltip`, `tooltip_position`, `icon`, `icon_position`, `item_order`, `languages`, `roles`) VALUES
(70, 'Home', 3, NULL, NULL, '/', NULL, NULL, '', 'top', NULL, 'left', 0, NULL, NULL),
(71, 'Github', 3, NULL, NULL, 'https://github.com/lantian/PhalconEye', NULL, '_blank', '<p><font color="#00ff00">Open source</font></p>\r\n', 'top', NULL, 'right', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type` varchar(25) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `layout` varchar(50) NOT NULL DEFAULT 'middle',
  `controller` varchar(50) DEFAULT NULL,
  `roles` varchar(150) DEFAULT NULL,
  `view_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `controller` (`controller`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `type`, `url`, `description`, `keywords`, `layout`, `controller`, `roles`, `view_count`) VALUES
(1, 'Header', 'header', NULL, 'Header content', '', 'middle', NULL, NULL, NULL),
(2, 'Footer', 'footer', NULL, 'Footer content', '', 'middle', NULL, NULL, NULL),
(3, 'Home', 'home', '/', 'PhalconEye Home Page', 'PhalconEye', 'top,right,middle,left', '', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL DEFAULT 'user',
  `undeletable` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `is_default`, `type`, `undeletable`) VALUES
(1, 'Admin', 'Administrator', 1, 'admin', 1),
(2, 'User', 'Default user role.', 0, 'user', 1),
(3, 'Guest', 'Guest role', 0, 'guest', 1);

-- --------------------------------------------------------

--
-- Table structure for table `session_data`
--

CREATE TABLE IF NOT EXISTS `session_data` (
  `session_id` varchar(35) NOT NULL,
  `data` text NOT NULL,
  `creation_date` int(15) unsigned NOT NULL,
  `modification_date` int(15) unsigned DEFAULT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(60) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`name`, `value`) VALUES
('system_default_language', 'en'),
('system_theme', 'default'),
('system_title', 'Phalcon Eye');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `username`, `password`, `email`, `creation_date`) VALUES
(1, 1, 'admin', '$2a$08$4QLw0knochQElXNkRLLUeuYLTCBNLmlmFDJTCcZ2LGOAX3Bz1bioS', 'admin@mail.com', '2013-02-05 21:22:45');

-- --------------------------------------------------------

--
-- Table structure for table `widgets`
--

CREATE TABLE IF NOT EXISTS `widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_paginated` tinyint(4) NOT NULL DEFAULT '0',
  `is_acl_controlled` tinyint(1) NOT NULL DEFAULT '1',
  `admin_form` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_id_name` (`module_id`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `widgets`
--

INSERT INTO `widgets` (`id`, `module_id`, `name`, `title`, `description`, `is_paginated`, `is_acl_controlled`, `admin_form`) VALUES
(1, NULL, 'HtmlBlock', '', 'Insert any HTML of you choice', 0, 1, 'action'),
(2, NULL, 'Menu', '', 'Render menu', 0, 1, 'Form_Admin_Widgets_Menu'),
(3, NULL, 'Header', '', 'Settings for header of you site.', 0, 1, 'Form_Admin_Widgets_Header');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `content_ibfk_1` FOREIGN KEY (`widget_id`) REFERENCES `widgets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `language_translations`
--
ALTER TABLE `language_translations`
  ADD CONSTRAINT `language_translations_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
