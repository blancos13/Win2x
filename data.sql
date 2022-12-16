-- Adminer 4.7.4 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `battle`;
CREATE TABLE `battle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `price` decimal(20,2) DEFAULT '0.00',
  `commission` decimal(20,2) DEFAULT NULL,
  `winner_team` varchar(255) DEFAULT NULL,
  `winner_factor` decimal(8,2) DEFAULT NULL,
  `winner_ticket` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `battle` (`id`, `price`, `commission`, `winner_team`, `winner_factor`, `winner_ticket`, `status`, `hash`, `created_at`, `updated_at`) VALUES
(1,	0.00,	NULL,	NULL,	NULL,	NULL,	0,	'71718142bfe57e8b7cd822aed8bb16dd',	'2020-03-21 15:42:05',	'2020-03-21 15:42:05');

DROP TABLE IF EXISTS `battle_bets`;
CREATE TABLE `battle_bets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `price` decimal(20,2) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `win` int(1) DEFAULT '0',
  `win_sum` decimal(20,2) NOT NULL DEFAULT '0.00',
  `balType` varchar(255) DEFAULT NULL,
  `fake` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `battle_bets_user_id_foreign` (`user_id`),
  KEY `battle_bets_game_id_foreign` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `bonus`;
CREATE TABLE `bonus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sum` decimal(20,2) DEFAULT NULL,
  `bg` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `bonus` (`id`, `sum`, `bg`, `color`, `status`, `type`, `created_at`, `updated_at`) VALUES
(1,	0.10,	'#7d3585',	'#b660c0',	1,	'group',	'2019-05-19 10:29:41',	'2019-05-23 15:31:20'),
(2,	0.20,	'#f1cb30',	'#f7e390',	1,	'group',	'2019-05-19 10:30:27',	'2019-05-23 15:31:29'),
(3,	0.30,	'#a32331',	'#da5261',	1,	'group',	'2019-05-19 10:31:22',	'2019-05-23 15:31:35'),
(4,	0.20,	'#645c9e',	'#a09bc5',	1,	'group',	'2019-05-19 10:31:55',	'2019-05-23 15:33:45'),
(5,	0.50,	'#f1cb30',	'#f7e390',	1,	'group',	'2019-05-19 10:32:27',	'2019-05-23 15:33:50'),
(6,	0.10,	'#d06f1e',	'#eaa46a',	1,	'group',	'2019-05-19 10:32:44',	'2019-05-23 15:33:59'),
(7,	1.00,	'#bdcf71',	'#e0e9bd',	1,	'group',	'2019-05-19 10:33:09',	'2019-05-23 16:05:15'),
(8,	0.10,	'#599edd',	'#aecfee',	1,	'group',	'2019-05-19 10:33:31',	'2019-05-23 16:05:31'),
(9,	0.20,	'#d06f1e',	'#eaa46a',	1,	'group',	'2019-05-19 10:34:11',	'2019-05-23 16:05:35'),
(10,	0.30,	'#7d3585',	'#b660c0',	1,	'group',	'2019-05-19 10:34:28',	'2019-05-23 16:58:14'),
(11,	0.20,	'#a32331',	'#da5261',	1,	'group',	'2019-05-19 10:34:45',	'2019-05-23 16:58:19'),
(12,	0.50,	'#645c9e',	'#a09bc5',	1,	'group',	'2019-05-19 10:35:07',	'2019-05-23 16:58:32'),
(13,	0.10,	'#323232',	'#656565',	1,	'group',	'2019-05-19 10:35:27',	'2019-05-23 16:58:39'),
(14,	10.00,	'#bdcf71',	'#e0e9bd',	1,	'group',	'2019-05-19 10:35:51',	'2019-05-23 16:58:55'),
(15,	100.00,	'#7d3585',	'#b660c0',	1,	'refs',	'2019-05-19 13:26:30',	'2019-05-19 13:27:07'),
(16,	125.00,	'#f1cb30',	'#f7e390',	1,	'refs',	'2019-05-19 13:26:48',	'2019-05-19 15:25:06'),
(17,	150.00,	'#a32331',	'#da5261',	1,	'refs',	'2019-05-20 01:40:18',	'2019-05-20 01:44:01'),
(18,	175.00,	'#645c9e',	'#a09bc5',	1,	'refs',	'2019-05-20 01:40:35',	'2019-05-20 01:44:03'),
(19,	200.00,	'#f1cb30',	'#f7e390',	1,	'refs',	'2019-05-20 01:40:55',	'2019-05-20 01:44:04'),
(20,	250.00,	'#d06f1e',	'#eaa46a',	1,	'refs',	'2019-05-20 01:41:12',	'2019-05-20 01:44:06'),
(21,	300.00,	'#bdcf71',	'#e0e9bd',	0,	'refs',	'2019-05-20 01:41:28',	'2019-05-25 22:29:14'),
(22,	350.00,	'#599edd',	'#aecfee',	0,	'refs',	'2019-05-20 01:41:44',	'2019-05-25 22:29:00'),
(23,	400.00,	'#d06f1e',	'#eaa46a',	0,	'refs',	'2019-05-20 01:42:04',	'2019-05-25 22:28:11'),
(24,	450.00,	'#7d3585',	'#b660c0',	0,	'refs',	'2019-05-20 01:42:26',	'2019-05-25 22:27:56'),
(25,	500.00,	'#a32331',	'#da5261',	0,	'refs',	'2019-05-20 01:42:51',	'2019-05-25 22:27:39'),
(26,	600.00,	'#645c9e',	'#a09bc5',	0,	'refs',	'2019-05-20 01:43:07',	'2019-05-25 22:26:06'),
(27,	750.00,	'#323232',	'#656565',	0,	'refs',	'2019-05-20 01:43:24',	'2019-05-25 22:25:40'),
(28,	1000.00,	'#bdcf71',	'#e0e9bd',	0,	'refs',	'2019-05-20 01:43:43',	'2019-05-25 22:25:15');

DROP TABLE IF EXISTS `bonus_log`;
CREATE TABLE `bonus_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `sum` decimal(20,2) DEFAULT NULL,
  `remaining` int(11) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `bonus_log_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `bonus_log` (`id`, `user_id`, `sum`, `remaining`, `status`, `type`, `created_at`, `updated_at`) VALUES
(1,	1,	0.50,	1584795279,	2,	'group',	'2020-03-21 15:44:39',	'2020-03-21 15:59:27');

DROP TABLE IF EXISTS `crash`;
CREATE TABLE `crash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `multiplier` decimal(20,2) DEFAULT NULL,
  `profit` decimal(20,2) DEFAULT '0.00',
  `hash` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `crash` (`id`, `multiplier`, `profit`, `hash`, `status`, `created_at`, `updated_at`) VALUES
(1,	1.78,	0.00,	'b379a41c52b3ee763df69dba27a9690d',	2,	'2019-07-14 06:35:08',	'2020-03-21 15:42:33'),
(2,	1.02,	0.00,	'0b4fae4d6ebffe4dbe84e24b09badde9',	2,	'2020-03-21 15:42:33',	'2020-03-21 15:42:52'),
(3,	4.82,	0.00,	'2c4c60d3a15165299d79b351cd06d551',	2,	'2020-03-21 15:42:52',	'2020-03-21 15:43:37'),
(4,	2.03,	0.00,	'99c2b1cc05b1777d77d990810eb1e841',	2,	'2020-03-21 15:43:37',	'2020-03-21 15:44:08'),
(5,	2.58,	0.00,	'7c0f4d63012ba3e77bd13ee09c915651',	2,	'2020-03-21 15:44:08',	'2020-03-21 15:44:43'),
(6,	1.52,	0.00,	'dca94a002722b330d49de51b484cbe56',	2,	'2020-03-21 15:44:43',	'2020-03-21 15:45:09'),
(7,	2.78,	0.00,	'77266eb57a1e51659eb9ffd5ba2cff61',	2,	'2020-03-21 15:45:09',	'2020-03-21 15:45:45'),
(8,	2.01,	0.00,	'1f12e110dfb8bae70d8709e3b658555d',	2,	'2020-03-21 15:45:45',	'2020-03-21 15:46:16'),
(9,	4.03,	0.00,	'1d5f073a3ef72980a1a9a069ea46e183',	2,	'2020-03-21 15:46:16',	'2020-03-21 15:46:58'),
(10,	1.05,	0.00,	'b9fa39fcc2c2f109882d69479da96948',	2,	'2020-03-21 15:46:58',	'2020-03-21 15:47:18'),
(11,	1.21,	0.00,	'5523e247b9b8bed4ef706a2dcc378edf',	2,	'2020-03-21 15:47:18',	'2020-03-21 15:47:40'),
(12,	2.87,	0.00,	'b8b5ee825e4388e078a89b1fcab055a7',	2,	'2020-03-21 15:47:40',	'2020-03-21 15:48:17'),
(13,	1.00,	0.00,	'b8e343ec33a8b21c229e459527652f6c',	2,	'2020-03-21 15:48:17',	'2020-03-21 15:48:36'),
(14,	1.32,	0.00,	'413a3ffcba74aba6f71e7160a820402c',	2,	'2020-03-21 15:48:36',	'2020-03-21 15:48:59'),
(15,	1.48,	0.00,	'6380c4f44893799f229f7ba8c55b62c3',	2,	'2020-03-21 15:48:59',	'2020-03-21 15:49:25'),
(16,	1.29,	0.00,	'5872ac2fb1572cb037e7c3c3d4e0e243',	2,	'2020-03-21 15:49:25',	'2020-03-21 15:49:48'),
(17,	1.09,	0.00,	'38902ecd995b89815fa4f9c9d6d4e2e8',	2,	'2020-03-21 15:49:48',	'2020-03-21 15:50:08'),
(18,	3.06,	0.00,	'16118357e3a0f8f441a85d5a9145aa48',	2,	'2020-03-21 15:50:08',	'2020-03-21 15:50:46'),
(19,	4.05,	0.00,	'a2b2675c7916e52b4341fa6834f9004b',	2,	'2020-03-21 15:50:46',	'2020-03-21 15:51:28'),
(20,	1.27,	0.00,	'a2d9d428235b5438e3a363d030a17dd3',	2,	'2020-03-21 15:51:28',	'2020-03-21 15:51:51'),
(21,	1.00,	0.00,	'4d1878e36c222e9ae1472c6bb0dcb266',	2,	'2020-03-21 15:51:51',	'2020-03-21 15:52:10'),
(22,	1.47,	0.00,	'3cbbdb1172870d2362bd04a033a3fcdb',	2,	'2020-03-21 15:52:10',	'2020-03-21 15:52:36'),
(23,	1.35,	0.00,	'719703c9029f21bf6c65648cbfe3b7ce',	2,	'2020-03-21 15:52:36',	'2020-03-21 15:53:00'),
(24,	1.25,	0.00,	'621b79070ecfb7e181d197fbe7e67d14',	2,	'2020-03-21 15:53:00',	'2020-03-21 15:53:22'),
(25,	1.13,	0.00,	'ea4c7411e86d08a036c4beb8c31742db',	2,	'2020-03-21 15:53:22',	'2020-03-21 15:53:43'),
(26,	2.88,	0.00,	'c76ed421b7402d1c3463d7df0a2f30a6',	2,	'2020-03-21 15:53:44',	'2020-03-21 15:54:21'),
(27,	1.02,	0.00,	'bdca431d4221c0da7614c1d119267027',	2,	'2020-03-21 15:54:21',	'2020-03-21 15:54:40'),
(28,	1.29,	0.00,	'ff36c052c10d551bd6200c0a81ce5688',	2,	'2020-03-21 15:54:40',	'2020-03-21 15:55:03'),
(29,	1.04,	0.00,	'65720609bdac4c7ce6c6b725bf3ad9da',	2,	'2020-03-21 15:55:03',	'2020-03-21 15:55:23'),
(30,	5.55,	0.00,	'e28684af44c588f3a0c23e8842cc172d',	2,	'2020-03-21 15:55:23',	'2020-03-21 15:56:12'),
(31,	1.06,	0.00,	'ac80c5c71548dbc6ad43e0459540c0b2',	2,	'2020-03-21 15:56:12',	'2020-03-21 15:56:32'),
(32,	1.31,	0.00,	'683816063083f94a7c093d2ef0a3f55f',	2,	'2020-03-21 15:56:32',	'2020-03-21 15:56:55'),
(33,	1.22,	0.00,	'ec22b8dd2032f1b95af9b8f7b3b434dc',	2,	'2020-03-21 15:56:55',	'2020-03-21 15:57:17'),
(34,	1.09,	0.00,	'f7cd06660c3dbbdd66163babcf7c6b85',	2,	'2020-03-21 15:57:17',	'2020-03-21 15:57:38'),
(35,	1.38,	0.00,	'ecc883e8c478f9a54e53a7dc58e06ad3',	2,	'2020-03-21 15:57:38',	'2020-03-21 15:58:02'),
(36,	1.31,	0.00,	'927f9a8f12ee66e4f6c925adb80a2e29',	2,	'2020-03-21 15:58:03',	'2020-03-21 15:58:27'),
(37,	5.04,	0.00,	'128178a22b400d683e2474096ac4ffa5',	2,	'2020-03-21 15:58:27',	'2020-03-21 15:59:13'),
(38,	1.07,	0.00,	'a820927c4f58a8d6b38623ca8ea80ebd',	2,	'2020-03-21 15:59:13',	'2020-03-21 15:59:33'),
(39,	1.46,	0.00,	'9ef62018c8a74b8519e1fa24447c1d4c',	1,	'2020-03-21 15:59:33',	'2020-03-21 15:59:48');

DROP TABLE IF EXISTS `crash_bets`;
CREATE TABLE `crash_bets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `round_id` int(11) DEFAULT NULL,
  `price` decimal(20,2) DEFAULT NULL,
  `withdraw` decimal(8,2) DEFAULT NULL,
  `won` decimal(20,2) DEFAULT '0.00',
  `status` int(1) NOT NULL DEFAULT '0',
  `fake` tinyint(1) NOT NULL DEFAULT '0',
  `balType` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `crash_bets_user_id_foreign` (`user_id`),
  KEY `crash_bets_round_id_foreign` (`round_id`),
  CONSTRAINT `crash_bets_round_id_foreign` FOREIGN KEY (`round_id`) REFERENCES `crash` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crash_bets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `crash_bets` (`id`, `user_id`, `round_id`, `price`, `withdraw`, `won`, `status`, `fake`, `balType`, `created_at`, `updated_at`) VALUES
(1,	1,	7,	0.50,	1.42,	0.71,	1,	0,	'bonus',	'2020-03-21 12:45:09',	'2020-03-21 12:45:31');

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `deposits`;
CREATE TABLE `deposits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(20,2) DEFAULT NULL,
  `usd_amo` decimal(20,2) DEFAULT NULL,
  `trx` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `try` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `dice`;
CREATE TABLE `dice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `sum` decimal(20,2) DEFAULT NULL,
  `perc` decimal(8,2) DEFAULT '0.00',
  `vip` decimal(8,2) NOT NULL DEFAULT '0.00',
  `num` decimal(8,2) DEFAULT '0.00',
  `win` int(1) NOT NULL DEFAULT '0',
  `win_sum` decimal(20,2) DEFAULT NULL,
  `balType` varchar(255) DEFAULT NULL,
  `fake` int(1) NOT NULL DEFAULT '0',
  `hash` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `dice_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dice` (`id`, `user_id`, `sum`, `perc`, `vip`, `num`, `win`, `win_sum`, `balType`, `fake`, `hash`, `created_at`, `updated_at`) VALUES
(1,	1,	0.11,	50.00,	1.92,	83.46,	0,	-0.11,	'bonus',	0,	'3709e0fa67356315ed19cd70ad58d67b',	'2020-03-21 15:58:38',	'2020-03-21 15:58:38'),
(2,	1,	0.11,	50.00,	1.92,	70.75,	0,	-0.11,	'bonus',	0,	'8360f00d23ac8efb84280393155855c7',	'2020-03-21 15:58:40',	'2020-03-21 15:58:40'),
(3,	1,	0.11,	50.00,	1.92,	75.92,	0,	-0.11,	'bonus',	0,	'cb34f4b2ae3f34e0b14c3766940350ec',	'2020-03-21 15:58:42',	'2020-03-21 15:58:42'),
(4,	1,	0.11,	50.00,	1.92,	10.80,	1,	0.10,	'bonus',	0,	'0638b06b2699a077388ef6f48d4b4536',	'2020-03-21 15:58:43',	'2020-03-21 15:58:43'),
(5,	1,	0.11,	50.00,	1.92,	79.01,	0,	-0.11,	'bonus',	0,	'3e9c03b0869480fb8843b61d7c767a57',	'2020-03-21 15:58:43',	'2020-03-21 15:58:43'),
(6,	1,	0.11,	50.00,	1.92,	7.69,	1,	0.10,	'bonus',	0,	'03613a64b47aad0d7782ac1196136b3c',	'2020-03-21 15:58:44',	'2020-03-21 15:58:44');

DROP TABLE IF EXISTS `exchanges`;
CREATE TABLE `exchanges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sum` decimal(20,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `filter`;
CREATE TABLE `filter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `flip`;
CREATE TABLE `flip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `heads` int(11) DEFAULT NULL,
  `heads_from` int(11) DEFAULT NULL,
  `heads_to` int(11) DEFAULT NULL,
  `tails` int(11) DEFAULT NULL,
  `tails_from` int(11) DEFAULT NULL,
  `tails_to` int(11) DEFAULT NULL,
  `bank` decimal(20,2) DEFAULT NULL,
  `winner_id` int(11) DEFAULT NULL,
  `winner_ticket` int(11) DEFAULT NULL,
  `winner_sum` decimal(20,2) DEFAULT NULL,
  `balType` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash` text COLLATE utf8mb4_unicode_ci,
  `status` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `jackpot`;
CREATE TABLE `jackpot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `game_id` int(11) DEFAULT NULL,
  `winner_id` int(11) DEFAULT NULL,
  `winner_ticket` int(11) DEFAULT NULL,
  `winner_balance` decimal(20,2) DEFAULT NULL,
  `winner_bonus` decimal(20,2) DEFAULT NULL,
  `hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `jackpot` (`id`, `room`, `game_id`, `winner_id`, `winner_ticket`, `winner_balance`, `winner_bonus`, `hash`, `status`, `created_at`, `updated_at`) VALUES
(1,	'easy',	1,	NULL,	NULL,	NULL,	NULL,	'453bb6a4a283edda91ccc9e2dff66391',	0,	'2019-07-19 16:10:37',	'2019-07-19 16:10:37'),
(2,	'medium',	1,	NULL,	NULL,	NULL,	NULL,	'a24961dc38650dc5167f4113cf350904',	0,	'2019-07-19 16:10:47',	'2019-07-19 16:10:47'),
(3,	'hard',	1,	NULL,	NULL,	NULL,	NULL,	'5dddf1e15d4174041c2ea2692d8d865c',	0,	'2019-07-19 16:11:31',	'2019-07-19 16:11:31');

DROP TABLE IF EXISTS `jackpot_bets`;
CREATE TABLE `jackpot_bets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `game_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sum` decimal(20,2) NOT NULL DEFAULT '0.00',
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `win` int(1) NOT NULL DEFAULT '0',
  `fake` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `jackpot_bets` (`id`, `room`, `game_id`, `user_id`, `sum`, `color`, `balance`, `win`, `fake`, `created_at`, `updated_at`) VALUES
(1,	'easy',	1,	1,	15.00,	'be338b',	'balance',	0,	0,	'2019-07-19 16:11:25',	NULL),
(2,	'medium',	2,	1,	65.00,	'8b14a3',	'balance',	0,	0,	'2019-07-19 16:11:30',	NULL),
(3,	'hard',	3,	1,	138.00,	'f01e4f',	'balance',	0,	0,	'2019-07-19 16:11:35',	NULL);

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `secret` text,
  `order_id` varchar(255) DEFAULT NULL,
  `sum` decimal(20,2) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `system` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `profit`;
CREATE TABLE `profit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `sum` decimal(20,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `profit` (`id`, `game`, `sum`, `created_at`, `updated_at`) VALUES
(1,	'crash',	0.50,	'2020-03-21 15:45:45',	'2020-03-21 15:45:45');

DROP TABLE IF EXISTS `promocode`;
CREATE TABLE `promocode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `limit` int(1) NOT NULL DEFAULT '0',
  `amount` decimal(20,2) DEFAULT NULL,
  `count_use` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `promo_log`;
CREATE TABLE `promo_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `sum` decimal(20,2) DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `min` decimal(20,2) NOT NULL DEFAULT '0.01',
  `max` decimal(20,2) NOT NULL DEFAULT '50.00',
  `bets` int(11) NOT NULL DEFAULT '3',
  `time` int(11) NOT NULL DEFAULT '10',
  `status` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `rooms` (`id`, `name`, `title`, `min`, `max`, `bets`, `time`, `status`, `created_at`, `updated_at`) VALUES
(1,	'easy',	'Easy',	0.10,	100.00,	3,	30,	0,	'2018-11-27 00:41:43',	'2020-03-21 15:48:43'),
(2,	'medium',	'Medium',	50.00,	500.00,	5,	30,	0,	'2018-11-27 00:41:43',	'2020-03-21 15:48:43'),
(3,	'hard',	'Hard',	100.00,	5000.00,	5,	30,	0,	'2018-11-27 00:41:43',	'2020-03-21 15:48:43');

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'win2x.ru',
  `sitename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'WIN2X.ru',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'WIN2X.ru - Браузерные игры с выводом реальных средств',
  `description` text COLLATE utf8mb4_unicode_ci,
  `keywords` text COLLATE utf8mb4_unicode_ci,
  `vk_url` text COLLATE utf8mb4_unicode_ci,
  `vk_support_link` text COLLATE utf8mb4_unicode_ci,
  `vk_service_key` text COLLATE utf8mb4_unicode_ci,
  `censore_replace` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'i ♥ win2x',
  `chat_dep` decimal(20,2) DEFAULT '5.00',
  `fakebets` int(1) NOT NULL DEFAULT '0',
  `fake_min_bet` decimal(20,2) DEFAULT '0.01',
  `fake_max_bet` decimal(20,2) DEFAULT '1.00',
  `merchant_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipn_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_key` text COLLATE utf8mb4_unicode_ci,
  `private_key` text COLLATE utf8mb4_unicode_ci,
  `coinpayments_fee` decimal(20,2) NOT NULL DEFAULT '0.00',
  `coinpayments_min` decimal(20,2) NOT NULL DEFAULT '0.00',
  `pm_uid` int(11) DEFAULT NULL,
  `pm_pass` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_usd_wallet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_passphrase` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_fee` decimal(20,2) NOT NULL DEFAULT '0.00',
  `pm_min` decimal(20,2) NOT NULL DEFAULT '0.00',
  `profit_koef` decimal(20,2) DEFAULT '1.30',
  `jackpot_commission` int(11) DEFAULT '10',
  `wheel_timer` int(11) DEFAULT '15',
  `wheel_min_bet` decimal(20,2) DEFAULT '0.01',
  `wheel_max_bet` decimal(20,2) DEFAULT '100.00',
  `wheel_rotate` decimal(20,2) DEFAULT '0.00',
  `wheel_rotate2` decimal(20,2) DEFAULT '0.00',
  `wheel_rotate_start` decimal(20,2) DEFAULT '0.00',
  `crash_min_bet` decimal(20,2) DEFAULT '0.01',
  `crash_max_bet` decimal(20,2) DEFAULT '100.00',
  `crash_timer` int(11) DEFAULT '10',
  `battle_timer` int(11) DEFAULT '10',
  `battle_min_bet` decimal(20,2) DEFAULT '0.01',
  `battle_max_bet` decimal(20,2) DEFAULT '100.00',
  `battle_commission` int(11) DEFAULT '10',
  `dice_min_bet` decimal(20,2) DEFAULT '0.01',
  `dice_max_bet` decimal(20,2) DEFAULT '100.00',
  `flip_commission` int(11) DEFAULT '15',
  `flip_min_bet` decimal(20,2) DEFAULT '0.01',
  `flip_max_bet` decimal(20,2) DEFAULT '100.00',
  `exchange_min` decimal(20,2) DEFAULT '3.00',
  `exchange_curs` int(11) DEFAULT '3',
  `ref_perc` int(11) DEFAULT '5',
  `ref_sum` decimal(20,2) DEFAULT '0.01',
  `min_ref_withdraw` decimal(20,2) DEFAULT '5.00',
  `min_dep` decimal(20,2) DEFAULT '0.01',
  `max_dep` decimal(20,2) DEFAULT NULL,
  `min_dep_withdraw` decimal(20,2) DEFAULT '5.00',
  `bonus_group_time` int(11) DEFAULT '15',
  `max_active_ref` int(11) DEFAULT '8',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`id`, `domain`, `sitename`, `title`, `description`, `keywords`, `vk_url`, `vk_support_link`, `vk_service_key`, `censore_replace`, `chat_dep`, `fakebets`, `fake_min_bet`, `fake_max_bet`, `merchant_id`, `ipn_secret`, `public_key`, `private_key`, `coinpayments_fee`, `coinpayments_min`, `pm_uid`, `pm_pass`, `pm_usd_wallet`, `pm_passphrase`, `pm_fee`, `pm_min`, `profit_koef`, `jackpot_commission`, `wheel_timer`, `wheel_min_bet`, `wheel_max_bet`, `wheel_rotate`, `wheel_rotate2`, `wheel_rotate_start`, `crash_min_bet`, `crash_max_bet`, `crash_timer`, `battle_timer`, `battle_min_bet`, `battle_max_bet`, `battle_commission`, `dice_min_bet`, `dice_max_bet`, `flip_commission`, `flip_min_bet`, `flip_max_bet`, `exchange_min`, `exchange_curs`, `ref_perc`, `ref_sum`, `min_ref_withdraw`, `min_dep`, `max_dep`, `min_dep_withdraw`, `bonus_group_time`, `max_active_ref`) VALUES
(1,	'167.71.202.231',	'WIN2X',	'WIN2X',	NULL,	NULL,	NULL,	NULL,	NULL,	'i ♥ win2x',	0.00,	0,	0.10,	50.00,	NULL,	NULL,	NULL,	NULL,	0.50,	0.01,	NULL,	NULL,	NULL,	NULL,	1.99,	0.01,	1.30,	10,	15,	0.10,	1000.00,	292.90,	292.90,	1560538972.00,	0.10,	1000.00,	5,	15,	0.10,	1000.00,	10,	0.10,	1000.00,	10,	0.10,	1000.00,	300.00,	3,	5,	0.50,	100.00,	0.01,	1000.00,	0.01,	10,	10);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` text COLLATE utf8mb4_unicode_ci,
  `user_id` text COLLATE utf8mb4_unicode_ci,
  `password` text COLLATE utf8mb4_unicode_ci,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance` decimal(20,2) NOT NULL DEFAULT '0.00',
  `bonus` decimal(20,2) NOT NULL DEFAULT '0.00',
  `requery` decimal(20,3) NOT NULL DEFAULT '0.000',
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` int(1) NOT NULL DEFAULT '0',
  `is_moder` int(1) NOT NULL DEFAULT '0',
  `is_youtuber` int(1) NOT NULL DEFAULT '0',
  `fake` int(1) NOT NULL DEFAULT '0',
  `time` int(1) NOT NULL DEFAULT '0',
  `banchat` int(11) DEFAULT NULL,
  `banchat_reason` text COLLATE utf8mb4_unicode_ci,
  `ban` int(1) NOT NULL DEFAULT '0',
  `ban_reason` text COLLATE utf8mb4_unicode_ci,
  `link_trans` int(11) NOT NULL DEFAULT '0',
  `link_reg` int(11) NOT NULL DEFAULT '0',
  `ref_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_money` decimal(20,2) NOT NULL DEFAULT '0.00',
  `ref_money_all` decimal(20,2) NOT NULL DEFAULT '0.00',
  `remember_token` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `unique_id`, `username`, `avatar`, `user_id`, `password`, `email`, `balance`, `bonus`, `requery`, `ip`, `is_admin`, `is_moder`, `is_youtuber`, `fake`, `time`, `banchat`, `banchat_reason`, `ban`, `ban_reason`, `link_trans`, `link_reg`, `ref_id`, `ref_money`, `ref_money_all`, `remember_token`, `created_at`, `updated_at`) VALUES
(1,	'2233a528c4f2',	'Administrator',	'/img/no_avatar.jpg',	'admin',	'$2y$10$E/pHAmSKaKw1YCvX.LRfQeECdgO4NllWOrCTJt4mJPQtf9KQj3UTS',	'deletetion2k@gmail.com',	0.00,	0.47,	0.000,	'123.27.33.138',	1,	0,	0,	0,	0,	NULL,	NULL,	0,	NULL,	0,	0,	NULL,	0.00,	0.00,	NULL,	'2020-03-21 15:44:28',	'2020-03-21 15:58:44');

DROP TABLE IF EXISTS `wheel`;
CREATE TABLE `wheel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `winner_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(20,2) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `hash` text COLLATE utf8mb4_unicode_ci,
  `profit` decimal(20,2) NOT NULL DEFAULT '0.00',
  `ranked` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wheel` (`id`, `winner_color`, `price`, `status`, `hash`, `profit`, `ranked`, `created_at`, `updated_at`) VALUES
(1,	NULL,	NULL,	0,	'674c25b10569e9a322119373ccb854aa',	0.00,	0,	'2020-03-21 15:42:05',	'2020-03-21 15:42:05');

DROP TABLE IF EXISTS `wheel_bets`;
CREATE TABLE `wheel_bets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `game_id` int(11) DEFAULT NULL,
  `price` decimal(20,2) DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `win` int(1) NOT NULL DEFAULT '0',
  `win_sum` decimal(20,2) NOT NULL DEFAULT '0.00',
  `balance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fake` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `withdraw`;
CREATE TABLE `withdraw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `value` decimal(20,2) DEFAULT NULL,
  `valueWithCom` decimal(20,2) DEFAULT NULL,
  `wallet` varchar(255) DEFAULT NULL,
  `system` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `withdraw_user_id_foreign` (`user_id`),
  CONSTRAINT `withdraw_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2020-03-21 12:59:51
