INSERT IGNORE INTO `roles` (`id`, `name`, `description`, `is_default`, `type`, `undeletable`) VALUES
(1, 'Admin', 'Administrator.', 0, 'admin', 1),
(2, 'User', 'Default user role.', 1, 'user', 1),
(3, 'Guest', 'Guest role.', 0, 'guest', 1);
--END

INSERT IGNORE INTO `access` (`object`, `action`, `role_id`, `value`) VALUES
('BackofficeArea', 'access', 1, 'allow'),
('BackofficeArea', 'access', 2, 'deny'),
('BackofficeArea', 'access', 3, 'deny');
--END

INSERT IGNORE INTO `pages` (`id`, `title`, `type`, `url`, `description`, `keywords`, `layout`, `controller`, `roles`, `view_count`) VALUES
(1, 'Header', 'header', NULL, 'Header content', '', 'middle', NULL, NULL, 0),
(2, 'Footer', 'footer', NULL, 'Footer content', '', 'middle', NULL, NULL, 0),
(3, 'Home', 'home', '/', 'Home Page', '', 'top_right_middle_left', NULL, NULL, 0),
(4, 'Forms', NULL, 'forms', 'Test Page', NULL, 'middle', NULL, NULL, 0);
--END

INSERT IGNORE INTO `languages` (`id`, `name`, `language`, `locale`, `icon`) VALUES
(1, 'English', 'en', 'en_US', NULL);
--END

INSERT IGNORE INTO `settings` (`name`, `value`) VALUES
('system_default_language', 'en'),
('system_title', 'Phalcon Eye');
--END

INSERT IGNORE INTO `users` VALUES (1,1,'admin','$2a$08$WKGFYJWeG2TqSEgfQV4OauagzrBj4oWB5tlzWeWsl3/X/Oyg82ivC','admin@mail.com', NOW(), NULL);
--END