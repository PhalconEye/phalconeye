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
(1, 'Header', 'header', NULL, 'Header content', '', 'middle', NULL, NULL, 0),
(2, 'Footer', 'footer', NULL, 'Footer content', '', 'middle', NULL, NULL, 0),
(3, 'Home', 'home', '/', 'PhalconEye Home Page', 'PhalconEye', 'top_right_middle_left', NULL, NULL, 0),
(4, 'Forms', NULL, 'forms', 'Test Page', NULL, 'middle', NULL, NULL, 0);

--
-- Dumping data for table `widgets`
--

INSERT IGNORE INTO `widgets` (`id`, `name`, `module`, `description`, `is_paginated`, `is_acl_controlled`, `admin_form`, `enabled`) VALUES
(1, 'HtmlBlock', 'core', 'Insert any HTML of you choice', 0, 1, 'action', 1),
(2, 'Menu', 'core', 'Render menu', 0, 1, '\\Core\\Form\\Admin\\Widget\\Menu', 1),
(3, 'Header', 'core', 'Settings for header of you site.', 0, 1, '\\Core\\Form\\Admin\\Widget\\Header', 1),
(4, 'Slider', 'core', 'Dynamic content slider', 0, 1, 'action', 1),
(5, 'Demo', NULL, 'Displays forms and other structures', 0, 0, NULL, 1);

--
-- Dumping data for table `content`
--

INSERT IGNORE INTO `content` (`id`, `page_id`, `widget_id`, `widget_order`, `layout`, `params`) VALUES
(1, 1, 3, 1, 'middle', '{"logo":"assets\\/application\\/img\\/core\\/pe_logo_white.png","show_title":null,"show_auth":"1","roles":null,"content_id":"9"}'),
(2, 1, 2, 2, 'middle', '{"title":"","class":"","menu":"Default menu","menu_id":"1","roles":null,"content_id":"10"}'),
(3, 3, 4, 1, 'top', '{"slides":["<img alt=\\"Slide 1\\" src=\\"\\/files\\/demo\\/slide1.jpg\\" \\/>","<div class=\\"text-center\\">\\r\\n<h2>Simplified page administration:<\\/h2>\\r\\n\\r\\n<p><img alt=\\"Title\\" src=\\"\\/files\\/demo\\/admin.page.png\\" \\/><\\/p>\\r\\n<\\/div>\\r\\n","<div class=\\"text-center\\">\\r\\n<h2>Powered by Phalcon Framework<\\/h2>\\r\\n<img alt=\\"Phalcon code\\" src=\\"\\/files\\/demo\\/code.png\\" \\/><\\/div>\\r\\n","<div style=\\"max-width: 640px; position: relative; margin: 0 auto\\"><br \\/>\\r\\n<br \\/>\\r\\n<img alt=\\"Title\\" src=\\"\\/files\\/demo\\/walle.cube.jpg\\" \\/>\\r\\n<div class=\\"bx-caption\\"><span>Flexible Multi MVC Architecture targeting High Performance<\\/span><\\/div>\\r\\n<\\/div>\\r\\n"],"height":null,"duration":"5000","speed":"500","auto":"1","auto_hover":"1","controls":"1","video":null,"pager":"1","roles":null}'),
(4, 2, 1, 1, 'middle', '{"title":null,"html_en":"<div style=\\"display:table; width: 100%\\">\\r\\n<div class=\\"desktop-visible\\" style=\\"display:table-cell; padding-left: 2em\\">\\r\\n  <h3>Useful resources:<\\/h3>\\r\\n  <ul>\\r\\n    <li><a href=\\"http:\\/\\/phalconeye.com\\/\\">Website<\\/a><\\/li>\\r\\n    <li><a href=\\"http:\\/\\/docs.phalconeye.com\\">Documentation<\\/a><\\/li>\\r\\n    <li><a href=\\"http:\\/\\/forum.phalconeye.com\\">Forum<\\/a><\\/li>\\r\\n    <li><a href=\\"https:\\/\\/github.com\\/lantian\\/PhalconEye\\">Source Code<\\/a><\\/li>\\r\\n    <li><a href=\\"http:\\/\\/phalconphp.com\\">Phalcon Framework<\\/a><\\/li>\\r\\n  <\\/ul>\\r\\n<\\/div>\\r\\n\\r\\n<div class=\\"phone-hidden\\" style=\\"display:table-cell; padding-left: 2em\\">\\r\\n  <h3>Contact Info:<\\/h3>\\r\\n  <p><strong>Address<\\/strong>: @Robots Factory, Wordwide<\\/p>\\r\\n  <p><strong>Email<\\/strong>: <a style=\\"text-decoration: none; border-bottom: 1px dashed #000\\" href=\\"mailto:robots@example.com\\">robots@example.com<\\/a><\\/p>\\r\\n  <p><strong>Phone<\\/strong>: +01 234 567 890<\\/p>\\r\\n  <p><strong>Fax<\\/strong>: +01 234 567 890<\\/p>\\r\\n  <p><strong>Location<\\/strong>: <a href=\\"\\/\\">view map<\\/a><\\/p>\\r\\n<\\/div>\\r\\n\\r\\n<div style=\\"display:table-cell; padding-left: 2em\\">\\r\\n  <div class=\\"phone-hidden\\">\\r\\n    <h3>Shortcuts:<\\/h3>\\r\\n    <p><a href=\\"\\/\\">T&amp;C<\\/a> | <a href=\\"\\/\\">Privacy Policy<\\/a> | <a href=\\"\\/\\">Site Map<\\/a><\\/p>\\r\\n  <\\/div>\\r\\n  <h3>Follow us:<\\/h3>\\r\\n  <ul class=\\"social-icons\\" style=\\"float: left; margin: 0\\">\\r\\n\\t<li class=\\"rss\\"><a title=\\"RSS Feeds\\" href=\\"\\/\\">\\u00a0<\\/a><\\/li>\\r\\n\\t<li class=\\"facebook\\"><a title=\\"Facebook\\" href=\\"\\/\\">\\u00a0<\\/a><\\/li>\\r\\n\\t<li class=\\"gplus\\"><a title=\\"Google Plus\\" href=\\"\\/\\">\\u00a0<\\/a><\\/li>\\r\\n\\t<li class=\\"twitter\\"><a title=\\"Twitter\\" href=\\"\\/\\">\\u00a0<\\/a><\\/li>\\r\\n\\t<li class=\\"youtube\\"><a title=\\"YouTube\\" href=\\"\\/\\">\\u00a0<\\/a><\\/li>\\r\\n    <li class=\\"skype\\"><a title=\\"Skype\\" href=\\"\\/\\">\\u00a0<\\/a><\\/li>\\r\\n  <\\/ul>\\r\\n  <small style=\\"float: left; line-height: 30px\\">\\u00a0 Credits: <a target=\\"_blank\\" rel=\\"nofollow\\" href=\\"http:\\/\\/www.spiceupyourblog.com\\">SUYB<\\/a><\\/small>\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n\\r\\n<div class=\\"text-center\\" style=\\"padding-bottom: 20px;margin: 20px 20px 0 20px\\">PhalconEye v.0.4.0<\\/div>\\r\\n","roles":null}'),
(5, 3, 1, 1, 'left', '{"title":"TOP 10 Robots","html_en":"<ul style=\\"list-style:square\\">\\r\\n\\t<li>Marvin<\\/li>\\r\\n\\t<li>Optimus Prime<\\/li>\\r\\n\\t<li>Wall-E<\\/li>\\r\\n\\t<li>C-3PO<\\/li>\\r\\n\\t<li>R2D2<\\/li>\\r\\n\\t<li>Johnny 5<\\/li>\\r\\n\\t<li>Daimos<\\/li>\\r\\n\\t<li>Sonny<\\/li>\\r\\n\\t<li>Josef<\\/li>\\r\\n\\t<li>Ratchet<\\/li>\\r\\n<\\/ul>\\r\\n","roles":null,"content_id":"5"}'),
(6, 3, 1, 2, 'left', '{"title":"Latest Parts","html_en":"<ul style=\\"list-style:square\\">\\r\\n\\t<li>Jet-Pack<\\/li>\\r\\n\\t<li>LIDAR A\\/V Sensor<\\/li>\\r\\n\\t<li>Global SatNav<\\/li>\\r\\n\\t<li>Hydraulic Effector<\\/li>\\r\\n\\t<li>UltraSonic Aerial<\\/li>\\r\\n\\t<li>HC-500 Tracked ATR Platform<\\/li>\\r\\n\\t<li>Tactical KIT X4<\\/li>\\r\\n\\t<li>Carbon-Fibre Body<\\/li>\\r\\n<\\/ul>\\r\\n","roles":null,"content_id":"6"}'),
(7, 3, 1, 1, 'middle', '{"title":"Typography","html_en":"<h1>H1 Heading<\\/h1>\\r\\n\\r\\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer semper egestas nunc in volutpat. <a>Fusce adipiscing<\\/a> velit ac eros tempor iaculis. Phasellus venenatis mollis augue, non posuere odio placerat in.<\\/p>\\r\\n\\r\\n<h2>H2 Heading<\\/h2>\\r\\n\\r\\n<p><a>Etiam volutpat<\\/a> ultrices lectus. Fusce eu felis erat. Donec congue interdum elit, sed ornare magna convallis lacinia. In hac habitasse platea dictumst. Mauris volutpat consectetur accumsan.<\\/p>\\r\\n\\r\\n<h3>H3 Heading<\\/h3>\\r\\n\\r\\n<p>Cras diam justo, sodales quis lobortis sed, lobortis vel mauris. Sed a mollis nunc. Quisque semper condimentum lectus, eget laoreet ipsum auctor et. Quisque sagittis luctus augue, id fringilla enim <a>euismod quis<\\/a>.<\\/p>\\r\\n\\r\\n<h4>H4 Heading<\\/h4>\\r\\n\\r\\n<p>Nullam blandit, elit at euismod rutrum, tortor nibh posuere mauris, in volutpat diam ante ac dui. Sed velit massa, imperdiet placerat tristique et, consectetur a lorem. Praesent aliquet turpis in quam tempor eu pulvinar nibh luctus.<\\/p>\\r\\n","roles":null,"content_id":"7"}'),
(8, 3, 1, 2, 'middle', '{"title":"Regular Content","html_en":"<p>\\r\\n  <span>Regular text<\\/span>\\r\\n  <strong>Strong text<\\/strong>\\r\\n  <em>Emphasized text<\\/em>\\r\\n<\\/p>","roles":null,"content_id":"8"}'),
(9, 3, 1, 1, 'right', '{"title":"TOP 10 Robots","html_en":"<ul style=\\"list-style:square\\">\\r\\n  <li>Marvin<\\/li>\\r\\n  <li>Optimus Prime<\\/li>\\r\\n  <li>Wall-E<\\/li>\\r\\n  <li>C-3PO<\\/li>\\r\\n  <li>R2D2<\\/li>\\r\\n  <li>Johnny 5<\\/li>\\r\\n  <li>Daimos<\\/li>\\r\\n  <li>Sonny<\\/li>\\r\\n  <li>Josef<\\/li> \\r\\n  <li>Ratchet<\\/li>\\r\\n<\\/ul>","roles":null,"content_id":"9"}'),
(10, 3, 1, 2, 'right', '{"title":"Latest Parts","html_en":"<ul style=\\"list-style:square\\">\\r\\n\\t<li>Jet-Pack<\\/li>\\r\\n\\t<li>LIDAR A\\/V Sensor<\\/li>\\r\\n\\t<li>Global SatNav<\\/li>\\r\\n\\t<li>Hydraulic Effector<\\/li>\\r\\n\\t<li>UltraSonic Aerial<\\/li>\\r\\n\\t<li>HC-500 Tracked ATR Platform<\\/li>\\r\\n\\t<li>Tactical KIT X4<\\/li>\\r\\n\\t<li>Carbon-Fibre Body<\\/li>\\r\\n<\\/ul>\\r\\n","roles":null,"content_id":"10"}'),
(13, 4, 5, 0, 'middle', '[]');

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

INSERT IGNORE INTO `menu_items` (`id`, `title`, `menu_id`, `parent_id`, `page_id`, `url`, `onclick`, `target`, `tooltip`, `tooltip_position`, `icon`, `icon_position`, `languages`, `roles`, `is_enabled`, `item_order`) VALUES
(1, 'Home', 1, NULL, NULL, '/', NULL, NULL, NULL, 'top', 'files/PE_logo.png', 'left', NULL, NULL, 1, 0),
(2, 'Forms', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'top', NULL, 'left', NULL, NULL, 1, 1),
(3, 'Text elements', 1, 2, NULL, 'page/forms#robot1', NULL, NULL, NULL, 'top', NULL, 'left', NULL, NULL, 1, 0),
(4, 'Github', 1, NULL, NULL, 'https://github.com/lantian/PhalconEye', NULL, '_blank', '<p><b><span style="color:#FF0000;">G</span>it<span style="color:#FF0000;">H</span>ub Page</b></p>\r\n', 'right', 'files/github.png', 'left', NULL, NULL, 1, 2),
(5, 'Control elements', 1, 2, NULL, 'page/forms#color', NULL, NULL, NULL, 'top', NULL, 'left', NULL, NULL, 1, 1),
(6, 'File elements', 1, 2, NULL, 'page/forms#scheme', NULL, NULL, NULL, 'top', NULL, 'left', NULL, NULL, 1, 2);

--
-- Dumping data for table `packages`
--

INSERT IGNORE INTO `packages` (`id`, `name`, `type`, `title`, `description`, `version`, `author`, `website`, `enabled`, `is_system`, `data`) VALUES
(1, 'core', 'module', 'Core', 'PhalconEye Core', '0.4.0', 'PhalconEye Team', 'http://phalconeye.com/', 1, 1, NULL),
(2, 'user', 'module', 'Users', 'PhalconEye Users', '0.4.0', 'PhalconEye Team', 'http://phalconeye.com/', 1, 1, NULL),
(3, 'demo', 'widget', 'Demo Widget', 'Displays forms and other structures', '0.4.0', 'PhalconEye Team', 'http://phalconeye.com/', 1, 0, '{"widget_id":"5"}'),
(4, 'light', 'theme', 'LightEye', 'Lighter version of the default theme.', '0.4.0', 'PhalconEye Team', 'http://phalconeye.com/', 1, 0, NULL);

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
