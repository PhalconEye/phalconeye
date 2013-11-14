SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- Dumping data for table `access`
--

INSERT IGNORE INTO `access` (`object`, `action`, `role_id`, `value`) VALUES
('AdminArea', 'access', 2, 'allow'),
('AdminArea', 'access', 3, 'deny');

--
-- Dumping data for table `content`
--

INSERT IGNORE INTO `content` (`id`, `page_id`, `widget_id`, `widget_order`, `layout`, `params`) VALUES
(67, 3, 1, 1, 'top', '{"title":"Header","html_en":"<p>Header<\\/p>\\r\\n","html_ru":"","html":null,"roles":null,"content_id":"67"}'),
(70, 3, 1, 1, 'right', '{"title":"Right","html_en":"<p>Right<\\/p>\\r\\n","html_ru":"","html":null,"roles":null,"content_id":"70"}'),
(71, 3, 1, 1, 'left', '{"title":"Left","html_en":"<p>Left<\\/p>\\r\\n","html_ru":"","html":null,"roles":null,"content_id":"71"}'),
(72, 3, 1, 1, 'middle', '{"title":"Content","html_en":"<p>Content<\\/p>\\r\\n","html_ru":"","html":null,"roles":null}'),
(108, 3, 1, 2, 'top', '{"title":"Header2","html_en":"<p>Header2<\\/p>\\r\\n","html_ru":"","html":null,"roles":null}'),
(109, 3, 1, 2, 'right', '{"title":"Right2","html_en":"<p>Right2<\\/p>\\r\\n","html_ru":"","html":null,"roles":null}'),
(110, 3, 1, 2, 'middle', '{"title":"Content2","html_en":"<p>Content2<\\/p>\\r\\n","html_ru":"","html":null,"roles":null}'),
(111, 3, 1, 2, 'left', '{"title":"Left2","html_en":"<p>Left2<\\/p>\\r\\n","html_ru":"","html":null,"roles":null}'),
(112, 1, 3, 1, 'middle', '{"logo":"\\/assets\\/img\\/core\\/pe_logo.png","show_title":null,"show_auth":"1","roles":null,"content_id":"112"}'),
(113, 1, 2, 2, 'middle', '{"title":"","class":"","menu":"Default menu","menu_id":"1","roles":null}');

--
-- Dumping data for table `languages`
--

INSERT IGNORE INTO `languages` (`id`, `name`, `locale`, `icon`) VALUES
(1, 'English', 'en', NULL);

--
-- Dumping data for table `language_translations`
--

INSERT IGNORE INTO `language_translations` (`id`, `language_id`, `original`, `translated`) VALUES
(2, 1, 'Dashboard', 'Dashboard'),
(3, 1, 'Manage', 'Manage'),
(4, 1, 'Users and Roles', 'Users and Roles'),
(5, 1, 'Pages', 'Pages'),
(6, 1, 'Menus', 'Menus'),
(7, 1, 'Languages', 'Languages'),
(8, 1, 'Files', 'Files'),
(9, 1, 'Settings', 'Settings'),
(10, 1, 'System', 'System'),
(11, 1, 'Performance', 'Performance'),
(12, 1, 'Access Rights', 'Access Rights'),
(13, 1, 'Back to site', 'Back to site'),
(14, 1, 'Logout', 'Logout'),
(15, 1, 'Home', 'Home'),
(16, 1, 'PhalconEye', 'PhalconEye'),
(17, 1, 'PhalconEye Home Page', 'PhalconEye Home Page'),
(18, 1, 'Login', 'Login'),
(19, 1, 'Register', 'Register'),
(20, 1, 'Github', 'Github'),
(21, 1, 'System mode', 'System mode'),
(22, 1, 'Available resources', 'Available resources'),
(23, 1, 'Resource Name', 'Resource Name'),
(24, 1, 'Actions', 'Actions'),
(25, 1, 'Options', 'Options'),
(26, 1, 'Edit', 'Edit'),
(27, 1, 'Edit Access', 'Edit Access'),
(28, 1, 'Editing access rights of "%currentObject%", for:', 'Editing access rights of "%currentObject%", for:'),
(29, 1, 'Access', 'Access'),
(30, 1, 'ACCESS_OBJECT_ADMINAREA_ACTION_ACCESS', 'ACCESS_OBJECT_ADMINAREA_ACTION_ACCESS'),
(31, 1, 'Save', 'Save'),
(32, 1, '<div class="alert alert-success">Settings saved!</div>', '<div class="alert alert-success">Settings saved!</div>'),
(33, 1, 'Not Found', 'Not Found'),
(34, 1, 'Use you email or username to login.', 'Use you email or username to login.'),
(35, 1, 'Login (email or username)', 'Login (email or username)'),
(36, 1, 'Password', 'Password'),
(37, 1, 'Welcome, ', 'Welcome, '),
(38, 1, 'View count:', 'View count:'),
(39, 1, 'Are you really want to delete this language?', 'Are you really want to delete this language?'),
(40, 1, 'Browse', 'Browse'),
(41, 1, '|', '|'),
(42, 1, 'Create new language', 'Create new language'),
(43, 1, 'Compiling...', 'Compiling...'),
(44, 1, 'Compile languages', 'Compile languages'),
(45, 1, 'Id', 'Id'),
(46, 1, 'Name', 'Name'),
(47, 1, 'Locale', 'Locale'),
(48, 1, 'Icon', 'Icon'),
(49, 1, 'Delete', 'Delete'),
(50, 1, 'Users', 'Users'),
(51, 1, 'Are you really want to delete this user?', 'Are you really want to delete this user?'),
(52, 1, 'Roles', 'Roles'),
(53, 1, 'Create new user', 'Create new user'),
(54, 1, 'Create new role', 'Create new role'),
(55, 1, 'Username', 'Username'),
(56, 1, 'Email', 'Email'),
(57, 1, 'Role', 'Role'),
(58, 1, 'Creation Date', 'Creation Date'),
(59, 1, 'Are you really want to delete this role?', 'Are you really want to delete this role?'),
(60, 1, 'Description', 'Description'),
(61, 1, 'Is default?', 'Is default?'),
(62, 1, 'Yes', 'Yes'),
(63, 1, 'No', 'No'),
(64, 1, 'User Creation', 'User Creation'),
(65, 1, 'Create new user.', 'Create new user.'),
(66, 1, 'Select user role', 'Select user role'),
(67, 1, 'Create', 'Create'),
(68, 1, 'Cancel', 'Cancel'),
(69, 1, 'Role Creation', 'Role Creation'),
(70, 1, 'Create new role.', 'Create new role.'),
(71, 1, 'Is Default', 'Is Default'),
(72, 1, 'Are you really want to delete this page?', 'Are you really want to delete this page?'),
(73, 1, 'Create new page', 'Create new page'),
(74, 1, 'Title', 'Title'),
(75, 1, 'Url', 'Url'),
(76, 1, 'Layout', 'Layout'),
(77, 1, 'Controller', 'Controller'),
(78, 1, 'Are you really want to delete this menu?', 'Are you really want to delete this menu?'),
(79, 1, 'Create new menu', 'Create new menu'),
(80, 1, 'Menu items', 'Menu items'),
(81, 1, 'Performance settings', 'Performance settings'),
(82, 1, 'Cache prefix', 'Cache prefix'),
(83, 1, 'Example "pe_"', 'Example "pe_"'),
(84, 1, 'Cache lifetime', 'Cache lifetime'),
(85, 1, 'This determines how long the system will keep cached data before reloading it from the database server. A shorter cache lifetime causes greater database server CPU usage, however the data will be more current.', 'This determines how long the system will keep cached data before reloading it from the database server. A shorter cache lifetime causes greater database server CPU usage, however the data will be more current.'),
(86, 1, 'Cache adapter', 'Cache adapter'),
(87, 1, 'Cache type. Where cache will be stored.', 'Cache type. Where cache will be stored.'),
(88, 1, 'Files location', 'Files location'),
(89, 1, 'Memcached host', 'Memcached host'),
(90, 1, 'Memcached port', 'Memcached port'),
(91, 1, 'Create a persitent connection to memcached?', 'Create a persitent connection to memcached?'),
(92, 1, 'A MongoDB connection string', 'A MongoDB connection string'),
(93, 1, 'Mongo database name', 'Mongo database name'),
(94, 1, 'Mongo collection in the database', 'Mongo collection in the database'),
(95, 1, 'Clear cache', 'Clear cache'),
(96, 1, 'All system cache will be cleared.', 'All system cache will be cleared.'),
(97, 1, 'Page Creation', 'Page Creation'),
(98, 1, 'Create new page.', 'Create new page.'),
(99, 1, 'Page will be available under http://ph.loc/page/[URL NAME]', 'Page will be available under http://ph.loc/page/[URL NAME]'),
(100, 1, 'Keywords', 'Keywords'),
(101, 1, 'Controller and action name that will handle this page. Example: NameController->someAction', 'Controller and action name that will handle this page. Example: NameController->someAction'),
(102, 1, 'If no value is selected, will be allowed to all (also as all selected).', 'If no value is selected, will be allowed to all (also as all selected).'),
(103, 1, 'Page not saved! Dou you want to leave?', 'Page not saved! Dou you want to leave?'),
(104, 1, 'Error while saving...', 'Error while saving...'),
(105, 1, 'Save (NOT  SAVED)', 'Save (NOT  SAVED)'),
(106, 1, 'Header', 'Header'),
(107, 1, 'Footer', 'Footer'),
(108, 1, 'If you switch to new layout you will lose some widgets, are you shure?', 'If you switch to new layout you will lose some widgets, are you shure?'),
(110, 1, 'Manage page', 'Manage page'),
(111, 1, 'Change layout', 'Change layout'),
(112, 1, 'Select layout type for current page', 'Select layout type for current page'),
(113, 1, 'Saving...', 'Saving...'),
(120, 1, 'HTML block, for:', 'HTML block, for:'),
(121, 1, 'HtmlBlock', 'HtmlBlock'),
(122, 1, 'Close', 'Close'),
(123, 1, 'Save changes', 'Save changes'),
(124, 1, 'Test', 'Test'),
(125, 1, 'dwadaw', 'dwadaw'),
(126, 1, 'dwad', 'dwad'),
(127, 1, 'Last', 'Last'),
(128, 1, 'First', 'First'),
(129, 1, 'Manage language', 'Manage language'),
(130, 1, 'Are you really want to delete this translation?', 'Are you really want to delete this translation?'),
(131, 1, 'Add new item', 'Add new item'),
(132, 1, 'Search', 'Search'),
(133, 1, 'Original', 'Original'),
(134, 1, 'Translated', 'Translated'),
(135, 1, 'About', 'About'),
(136, 1, 'Show_views', 'Show_views'),
(137, 1, 'ACCESS_OBJECT_\\CORE\\MODEL\\PAGE_ACTION_SHOW_VIEWS', 'ACCESS_OBJECT_\\CORE\\MODEL\\PAGE_ACTION_SHOW_VIEWS'),
(138, 1, 'Page_footer', 'Page_footer'),
(139, 1, 'ACCESS_OBJECT_\\CORE\\MODEL\\PAGE_OPTION_SHOW_VIEWS', 'ACCESS_OBJECT_\\CORE\\MODEL\\PAGE_OPTION_SHOW_VIEWS'),
(140, 1, 'System settings', 'System settings'),
(141, 1, 'All system settings here.', 'All system settings here.'),
(142, 1, 'Site name', 'Site name'),
(143, 1, 'Theme', 'Theme'),
(144, 1, 'Default language', 'Default language'),
(145, 1, 'fsefse', 'fsefse'),
(147, 1, 'Menu Creation', 'Menu Creation'),
(148, 1, 'Create new menu.', 'Create new menu.'),
(149, 1, 'Language Creation', 'Language Creation'),
(150, 1, 'Create new language.', 'Create new language.'),
(151, 1, 'Manage menu', 'Manage menu'),
(152, 1, 'Are you really want to delete this menu item?', 'Are you really want to delete this menu item?'),
(153, 1, 'Saved...', 'Saved...'),
(154, 1, 'Items: ', 'Items: '),
(155, 1, 'Remove', 'Remove'),
(156, 1, 'Select file', 'Select file'),
(157, 1, 'Edit menu item', 'Edit menu item'),
(158, 1, 'This menu item will be available under menu or parent menu item.', 'This menu item will be available under menu or parent menu item.'),
(159, 1, 'Link type', 'Link type'),
(160, 1, 'Select url type', 'Select url type'),
(161, 1, 'Page', 'Page'),
(162, 1, 'Start typing to see pages variants.', 'Start typing to see pages variants.'),
(163, 1, 'Onclick', 'Onclick'),
(164, 1, 'Type JS action that will be performed when this menu item is selected.', 'Type JS action that will be performed when this menu item is selected.'),
(165, 1, 'Tooltip', 'Tooltip'),
(166, 1, 'Tooltip position', 'Tooltip position'),
(167, 1, 'Icon position', 'Icon position'),
(168, 1, 'Choose the language in which the menu item will be displayed. If no one selected - will be displayed at all.', 'Choose the language in which the menu item will be displayed. If no one selected - will be displayed at all.'),
(169, 1, 'Register you account!', 'Register you account!'),
(170, 1, 'Register your account!', 'Register your account!'),
(171, 1, 'Password Repeat', 'Password Repeat'),
(172, 1, 'Field ''Username'' is required!', 'Field ''Username'' is required!'),
(173, 1, 'Field ''Password Repeat'' is required!', 'Field ''Password Repeat'' is required!'),
(174, 1, 'Value of field ''email'' must have a valid e-mail format', 'Value of field ''email'' must have a valid e-mail format'),
(175, 1, 'Value of field ''password'' is less than the minimum 6 characters', 'Value of field ''password'' is less than the minimum 6 characters'),
(176, 1, 'Value of field ''repeatPassword'' is less than the minimum 6 characters', 'Value of field ''repeatPassword'' is less than the minimum 6 characters'),
(177, 1, '<div class="alert alert-success">Cache cleared!</div><div class="alert alert-success">Settings saved!</div>', '<div class="alert alert-success">Cache cleared!</div><div class="alert alert-success">Settings saved!</div>'),
(178, 1, 'Packages', 'Packages'),
(179, 1, 'Modules', 'Modules'),
(180, 1, 'Blog', 'Blog'),
(181, 1, 'Ad', 'Ad'),
(182, 1, 'Menu', 'Menu'),
(183, 1, 'Select menu that will be rendered.', 'Select menu that will be rendered.'),
(184, 1, 'Menu css class', 'Menu css class'),
(185, 1, 'Start typing to see menus variants', 'Start typing to see menus variants'),
(186, 1, 'Example', 'Example'),
(187, 1, 'Packages management', 'Packages management'),
(188, 1, 'Themes', 'Themes'),
(189, 1, 'Widgets', 'Widgets'),
(190, 1, 'Plugins', 'Plugins'),
(191, 1, 'Create new package', 'Create new package'),
(192, 1, 'Upload new package', 'Upload new package'),
(193, 1, 'Disable', 'Disable'),
(194, 1, 'Uninstall', 'Uninstall'),
(195, 1, 'Enable', 'Enable'),
(199, 1, 'Package Creation', 'Package Creation'),
(200, 1, 'Create new package.', 'Create new package.'),
(201, 1, 'Version', 'Version'),
(202, 1, 'Type package version. Ex.: 0.5.7', 'Type package version. Ex.: 0.5.7'),
(203, 1, 'Author', 'Author'),
(204, 1, 'How create this package? Identify youself!', 'How create this package? Identify youself!'),
(205, 1, 'Website', 'Website'),
(206, 1, 'Where user will look for new version?', 'Where user will look for new version?'),
(207, 1, 'Package type', 'Package type'),
(208, 1, 'Who create this package? Identify youself!', 'Who create this package? Identify youself!'),
(209, 1, 'name is required', 'name is required'),
(210, 1, 'title is required', 'title is required'),
(211, 1, 'version is required', 'version is required'),
(212, 1, 'enabled is required', 'enabled is required'),
(213, 1, 'Name must be in lowecase and contains only letter', 'Name must be in lowecase and contains only letter'),
(214, 1, 'Value of field ''name'' doesn''t match regular expression', 'Value of field ''name'' doesn''t match regular expression'),
(215, 1, 'Name must be in lowecase and contains only letter.', 'Name must be in lowecase and contains only letter.'),
(218, 1, 'Header comments for each file in package', 'Header comments for each file in package'),
(219, 1, 'Header comments', 'Header comments'),
(220, 1, 'This text will be placed in each file of package. User comments block /**  **/', 'This text will be placed in each file of package. User comments block /**  **/'),
(221, 1, 'This text will be placed in each file of package. Use comment block /**  **/.', 'This text will be placed in each file of package. Use comment block /**  **/.'),
(223, 1, 'Are you really want to remove this package? Once removed, it can not be restored.', 'Are you really want to remove this package? Once removed, it can not be restored.'),
(224, 1, 'Value of field ''name'' is already present in another record', 'Value of field ''name'' is already present in another record'),
(236, 1, 'Export', 'Export'),
(237, 1, 'Packages management - Modules', 'Packages management - Modules'),
(238, 1, 'Packages management - Plugins', 'Packages management - Plugins'),
(239, 1, 'No packages', 'No packages'),
(240, 1, 'Packages management - Widgets', 'Packages management - Widgets'),
(241, 1, 'Packages management - Themes', 'Packages management - Themes'),
(246, 1, 'Settings for header of you site.', 'Settings for header of you site.'),
(247, 1, 'Logo image (url)', 'Logo image (url)'),
(248, 1, 'Show site title', 'Show site title'),
(249, 1, 'Show authentication links (logo, register, logout, etc)', 'Show authentication links (logo, register, logout, etc)'),
(253, 1, 'Libraries', 'Libraries'),
(254, 1, 'Packages management - Libraries', 'Packages management - Libraries'),
(255, 1, 'Edit package', 'Edit package'),
(256, 1, 'Menu Editing', 'Menu Editing'),
(257, 1, 'Edit Menu', 'Edit Menu'),
(258, 1, 'Edit this menu.', 'Edit this menu.'),
(259, 1, 'Edit this package.', 'Edit this package.'),
(260, 1, 'Name must be in lowecase and contains only letters.', 'Name must be in lowecase and contains only letters.'),
(261, 1, 'Package with that name already exist!', 'Package with that name already exist!'),
(262, 1, 'Version must be in correct format: 1.0.0.0', 'Version must be in correct format: 1.0.0.0'),
(263, 1, 'Version must be in correct format: 1.0.0 or 1.0.0.0', 'Version must be in correct format: 1.0.0 or 1.0.0.0'),
(264, 1, 'Export Package', 'Export Package'),
(265, 1, 'Modules dependecy', 'Modules dependecy'),
(266, 1, 'Select package dependency (not necessarily).', 'Select package dependency (not necessarily).'),
(270, 1, 'Select package you want to install (zip extension).', 'Select package you want to install (zip extension).'),
(271, 1, 'Package', 'Package'),
(272, 1, 'Install new package', 'Install new package'),
(273, 1, 'Upload', 'Upload'),
(276, 1, '<div class="alert alert-success">Package installed!</div>', '<div class="alert alert-success">Package installed!</div>'),
(277, 1, '<div class="alert alert-info">Please, select zip file...</div><div class="alert alert-success">Package installed!</div>', '<div class="alert alert-info">Please, select zip file...</div><div class="alert alert-success">Package installed!</div>'),
(278, 1, '<div class="alert alert-info">Please, select zip file...</div>', '<div class="alert alert-info">Please, select zip file...</div>'),
(281, 1, '<div class="alert alert-error">This package requires the presence of the following modules:<br/>- module "dawdr" (v.12412)<br/>- library "test" (v.1.0.0.0)<br/></div>', '<div class="alert alert-error">This package requires the presence of the following modules:<br/>- module "dawdr" (v.12412)<br/>- library "test" (v.1.0.0.0)<br/></div>'),
(282, 1, '<div class="alert alert-error">To install this package you need update:<br/></div>', '<div class="alert alert-error">To install this package you need update:<br/></div>'),
(283, 1, '<div class="alert alert-error">To install this package you need update:<br/>- module "user". Current version: v.0.4.0. Required: v.0.5.0 <br/></div>', '<div class="alert alert-error">To install this package you need update:<br/>- module "user". Current version: v.0.4.0. Required: v.0.5.0 <br/></div>'),
(284, 1, '<div class="alert alert-error">To install this package you need update:<br/>- module "user", current version: v.0.4.0, required: v.0.5.0 <br/></div>', '<div class="alert alert-error">To install this package you need update:<br/>- module "user", current version: v.0.4.0, required: v.0.5.0 <br/></div>'),
(285, 1, '<div class="alert alert-error">To install this package you need update:<br/>- module "user" up to: v.0.5.0. Current version: v.0.4.0 <br/></div>', '<div class="alert alert-error">To install this package you need update:<br/>- module "user" up to: v.0.5.0. Current version: v.0.4.0 <br/></div>'),
(286, 1, '<div class="alert alert-info">There was some errors during installation:title is required</div><div class="alert alert-success">Package installed!</div>', '<div class="alert alert-info">There was some errors during installation:title is required</div><div class="alert alert-success">Package installed!</div>'),
(291, 1, '<div class="alert alert-error">This package already installed.</div>', '<div class="alert alert-error">This package already installed.</div>'),
(292, 1, '<div class="alert alert-info">Failed to install module widget... Check logs.</div><div class="alert alert-success">Package installed!</div>', '<div class="alert alert-info">Failed to install module widget... Check logs.</div><div class="alert alert-success">Package installed!</div>'),
(294, 1, '<div class="alert alert-error">This package requires the presence of the following modules:<br/>- library "test" (v.1.0.0.0)<br/></div>', '<div class="alert alert-error">This package requires the presence of the following modules:<br/>- library "test" (v.1.0.0.0)<br/></div>'),
(295, 1, 'Dispatch Complete', 'Dispatch Complete'),
(296, 1, 'Wgg', 'Wgg'),
(297, 1, 'module settings', 'module settings'),
(298, 1, 'This module has no settings', 'This module has no settings'),
(299, 1, 'This module has no settings...', 'This module has no settings...'),
(300, 1, 'here can be blog settings', 'here can be blog settings'),
(301, 1, '<div class="alert alert-error">Newer version of this package already installed.</div>', '<div class="alert alert-error">Newer version of this package already installed.</div>'),
(302, 1, '<div class="alert alert-success">Package updated to verion !</div>', '<div class="alert alert-success">Package updated to verion !</div>'),
(303, 1, '<div class="alert alert-success">Package updated to version !</div>', '<div class="alert alert-success">Package updated to version !</div>'),
(304, 1, '<div class="alert alert-success">Package updated to version 1.0.0.3!</div>', '<div class="alert alert-success">Package updated to version 1.0.0.3!</div>'),
(306, 1, 'All system cache will be cleaned.', 'All system cache will be cleaned.'),
(307, 1, 'Test11', 'Test11'),
(308, 1, '<script src="/external/jquery/jquery-1.8.3.min.js" type="text/javascript"></script>', '<script src="/external/jquery/jquery-1.8.3.min.js" type="text/javascript"></script>'),
(309, 1, '<script src="/external/jquery/jquery-1.8.3.min.js" type="text/javascript"></script><script src="/external/jquery/jquery-ui-1.9.0.custom.min.js" type="text/javascript"></script><script src="/external/bootstrap/bootstrap.min.js" type="text/javascript"></script><script src="/external/ckeditor/ckeditor.js" type="text/javascript"></script><script src="/assets/js/core/core.js" type="text/javascript"></script><script src="/assets/js/core/modal.js" type="text/javascript"></script><script src="/external/peadmin/javascript.js" type="text/javascript"></script><script src="/external/peadmin/ajaxplorer.js" type="text/javascript"></script>', '<script src="/external/jquery/jquery-1.8.3.min.js" type="text/javascript"></script><script src="/external/jquery/jquery-ui-1.9.0.custom.min.js" type="text/javascript"></script><script src="/external/bootstrap/bootstrap.min.js" type="text/javascript"></script><script src="/external/ckeditor/ckeditor.js" type="text/javascript"></script><script src="/assets/js/core/core.js" type="text/javascript"></script><script src="/assets/js/core/modal.js" type="text/javascript"></script><script src="/external/peadmin/javascript.js" type="text/javascript"></script><script src="/external/peadmin/ajaxplorer.js" type="text/javascript"></script>'),
(310, 1, 'Language Editing', 'Language Editing'),
(311, 1, 'Edit Language', 'Edit Language'),
(312, 1, 'Edit this language.', 'Edit this language.'),
(313, 1, 'Left', 'Left'),
(314, 1, 'Right', 'Right'),
(315, 1, 'dwa', 'dwa'),
(316, 1, 'gsrg', 'gsrg'),
(317, 1, 'gdrg', 'gdrg'),
(318, 1, 'fse', 'fse'),
(319, 1, 'fwaf', 'fwaf'),
(320, 1, 'fes', 'fes'),
(321, 1, 'fesf', 'fesf'),
(322, 1, 'fesfse', 'fesfse'),
(323, 1, 'daw', 'daw'),
(324, 1, 'htfh', 'htfh'),
(325, 1, 'fwa', 'fwa'),
(326, 1, 'wa', 'wa'),
(327, 1, 'fa', 'fa'),
(328, 1, 'Field ''Email'' is required!', 'Field ''Email'' is required!'),
(329, 1, 'Field ''Password'' is required!', 'Field ''Password'' is required!'),
(330, 1, 'You will use your email address to login.', 'You will use your email address to login.'),
(331, 1, 'Passwords must be at least 6 characters in length.', 'Passwords must be at least 6 characters in length.'),
(332, 1, 'Enter your password again for confirmation.', 'Enter your password again for confirmation.'),
(333, 1, 'Create new menu item', 'Create new menu item'),
(334, 1, 'Admin panel', 'Admin panel'),
(335, 1, 'Login or password are incorrect!', 'Login or password are incorrect!'),
(336, 1, 'Test2', 'Test2'),
(337, 1, 'NonTest', 'NonTest'),
(338, 1, 'awfawdfw', 'awfawdfw'),
(339, 1, 'dawdwa', 'dawdwa'),
(340, 1, 'Normal', 'Normal'),
(341, 1, 'Test flkedfj aw;kdl ;awkfl;skl;dkawl; kl;srkgl; krl;gksel;fkse ;kfl;se kgl;kr l;gkdl gdrg dr ', 'Test flkedfj aw;kdl ;awkfl;skl;dkawl; kl;srkgl; krl;gksel;fkse ;kfl;se kgl;kr l;gkdl gdrg dr '),
(343, 1, 'Home1', 'Home1'),
(344, 1, 'Test1', 'Test1'),
(345, 1, 'Test21111111', 'Test21111111');

--
-- Dumping data for table `menus`
--

INSERT IGNORE INTO `menus` (`id`, `name`) VALUES
(1, 'Default menu');

--
-- Dumping data for table `menu_items`
--

INSERT IGNORE INTO `menu_items` (`id`, `title`, `menu_id`, `parent_id`, `page_id`, `url`, `onclick`, `target`, `tooltip`, `tooltip_position`, `icon`, `icon_position`, `item_order`, `languages`, `roles`) VALUES
(1, 'Home', 1, NULL, NULL, '/', NULL, NULL, NULL, 'top', '/files/PE_logo.png', 'left', 0, NULL, NULL),
(2, 'Github', 1, NULL, NULL, 'https://github.com/lantian/PhalconEye', NULL, '_blank', '<p><b><span style="color:#FF0000;">G</span>it<span style="color:#FF0000;">H</span>ub Page</b></p>\r\n', 'left', '/files/github-10-512.gif', 'left', 1, NULL, NULL);

--
-- Dumping data for table `packages`
--

INSERT IGNORE INTO `packages` (`id`, `name`, `type`, `title`, `description`, `version`, `author`, `website`, `enabled`, `is_system`) VALUES
(1, 'core', 'module', 'Core', 'PhalconEye Core', '0.4.0', 'PhalconEye Team', 'http://phalconeye.com/', 1, 1),
(2, 'user', 'module', 'Users', 'PhalconEye Users', '0.4.0', 'PhalconEye Team', 'http://phalconeye.com/', 1, 1);

--
-- Dumping data for table `pages`
--

INSERT IGNORE INTO `pages` (`id`, `title`, `type`, `url`, `description`, `keywords`, `layout`, `controller`, `roles`, `view_count`) VALUES
(1, 'Header', 'header', NULL, 'Header content', '', 'middle', NULL, NULL, NULL),
(2, 'Footer', 'footer', NULL, 'Footer content', '', 'middle', NULL, NULL, NULL),
(3, 'Home', 'home', '/', 'PhalconEye Home Page', 'PhalconEye', 'top,right,middle,left', NULL, NULL, 0);

--
-- Dumping data for table `roles`
--

INSERT IGNORE INTO `roles` (`id`, `name`, `description`, `is_default`, `type`, `undeletable`) VALUES
(1, 'Admin', 'Administrator', 0, 'admin', 1),
(2, 'User', 'Default user role.', 1, 'user', 1),
(3, 'Guest', 'Guest role', 0, 'guest', 1);

--
-- Dumping data for table `settings`
--

INSERT IGNORE INTO `settings` (`name`, `value`) VALUES
('system_default_language', 'en'),
('system_theme', 'default'),
('system_title', 'Phalcon Eye');

--
-- Dumping data for table `widgets`
--

INSERT IGNORE INTO `widgets` (`id`, `module`, `name`, `description`, `is_paginated`, `is_acl_controlled`, `admin_form`, `enabled`) VALUES
(1, 'core', 'HtmlBlock', 'Insert any HTML of you choice', 0, 1, 'action', 1),
(2, 'core', 'Menu', 'Render menu', 0, 1, '\\Core\\Form\\Admin\\Widget\\Menu', 1),
(3, 'core', 'Header', 'Settings for header of you site.', 0, 1, '\\Core\\Form\\Admin\\Widget\\Header', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
