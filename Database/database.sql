-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 18, 2019 at 07:54 AM
-- Server version: 10.1.40-MariaDB-cll-lve
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simcypla_landa`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `attachment_for` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `attachment` varchar(256) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `attachments`
--

INSERT INTO `attachments` (`id`, `uploaded_by`, `attachment_for`, `name`, `attachment`, `created_at`) VALUES
(1, 1, 11, 'Business Letter', 'L8sAlvKiqwKOP6VuKVq7KIX564RrDvKZ.jpg', '2019-01-27 13:53:55');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `school` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `address` varchar(40) NOT NULL,
  `postalcode` int(11) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `type` enum('headquarters','branch') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `school`, `email`, `address`, `postalcode`, `phone`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Headquarters', 1, 'info@pool.com', 'Nakuru CBD', NULL, '078965412', 'headquarters', '2019-01-08 06:51:03', '2019-01-08 06:51:03'),
(2, 'Headquarters', 2, 'school@example.com', '', NULL, '', NULL, NULL, '0000-00-00 00:00:00'),
(3, 'Headquarters', 3, 'wings@example.com', '', NULL, '+731702568', NULL, NULL, '0000-00-00 00:00:00'),
(4, 'Headquarters', 4, 'sample@example.com', '', NULL, '123456789', NULL, NULL, '0000-00-00 00:00:00'),
(5, 'Hong Kong ', 1, 'hongkong@example.com', 'Hong Kong', NULL, '12345678', NULL, NULL, '0000-00-00 00:00:00'),
(6, 'Dubai', 1, 'dubai@example.com', 'Dubai, UAE', NULL, '12345678', NULL, NULL, '0000-00-00 00:00:00'),
(7, 'Dallas Texas', 1, 'dallas@example.com', 'Dallas, Texas', NULL, '12345678', NULL, NULL, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `branchmessages`
--

CREATE TABLE `branchmessages` (
  `id` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `type` enum('sms','email') NOT NULL,
  `contact` varchar(64) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` longtext,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Sent','Failed') NOT NULL DEFAULT 'Sent',
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `branchmessages`
--

INSERT INTO `branchmessages` (`id`, `receiver`, `type`, `contact`, `subject`, `message`, `sent_at`, `status`, `school`, `branch`) VALUES
(1, 6, 'email', 'dubai@example.com', 'Simcy Creative App', 'How are you liking our app?', '2019-01-27 13:44:21', 'Sent', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `code` varchar(2) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `code`, `name`) VALUES
(1, 'AF', 'Afghanistan'),
(2, 'AL', 'Albania'),
(3, 'DZ', 'Algeria'),
(4, 'DS', 'American Samoa'),
(5, 'AD', 'Andorra'),
(6, 'AO', 'Angola'),
(7, 'AI', 'Anguilla'),
(8, 'AQ', 'Antarctica'),
(9, 'AG', 'Antigua and Barbuda'),
(10, 'AR', 'Argentina'),
(11, 'AM', 'Armenia'),
(12, 'AW', 'Aruba'),
(13, 'AU', 'Australia'),
(14, 'AT', 'Austria'),
(15, 'AZ', 'Azerbaijan'),
(16, 'BS', 'Bahamas'),
(17, 'BH', 'Bahrain'),
(18, 'BD', 'Bangladesh'),
(19, 'BB', 'Barbados'),
(20, 'BY', 'Belarus'),
(21, 'BE', 'Belgium'),
(22, 'BZ', 'Belize'),
(23, 'BJ', 'Benin'),
(24, 'BM', 'Bermuda'),
(25, 'BT', 'Bhutan'),
(26, 'BO', 'Bolivia'),
(27, 'BA', 'Bosnia and Herzegovina'),
(28, 'BW', 'Botswana'),
(29, 'BV', 'Bouvet Island'),
(30, 'BR', 'Brazil'),
(31, 'IO', 'British Indian Ocean Territory'),
(32, 'BN', 'Brunei Darussalam'),
(33, 'BG', 'Bulgaria'),
(34, 'BF', 'Burkina Faso'),
(35, 'BI', 'Burundi'),
(36, 'KH', 'Cambodia'),
(37, 'CM', 'Cameroon'),
(38, 'CA', 'Canada'),
(39, 'CV', 'Cape Verde'),
(40, 'KY', 'Cayman Islands'),
(41, 'CF', 'Central African Republic'),
(42, 'TD', 'Chad'),
(43, 'CL', 'Chile'),
(44, 'CN', 'China'),
(45, 'CX', 'Christmas Island'),
(46, 'CC', 'Cocos (Keeling) Islands'),
(47, 'CO', 'Colombia'),
(48, 'KM', 'Comoros'),
(49, 'CG', 'Congo'),
(50, 'CK', 'Cook Islands'),
(51, 'CR', 'Costa Rica'),
(52, 'HR', 'Croatia (Hrvatska)'),
(53, 'CU', 'Cuba'),
(54, 'CY', 'Cyprus'),
(55, 'CZ', 'Czech Republic'),
(56, 'DK', 'Denmark'),
(57, 'DJ', 'Djibouti'),
(58, 'DM', 'Dominica'),
(59, 'DO', 'Dominican Republic'),
(60, 'TP', 'East Timor'),
(61, 'EC', 'Ecuador'),
(62, 'EG', 'Egypt'),
(63, 'SV', 'El Salvador'),
(64, 'GQ', 'Equatorial Guinea'),
(65, 'ER', 'Eritrea'),
(66, 'EE', 'Estonia'),
(67, 'ET', 'Ethiopia'),
(68, 'FK', 'Falkland Islands (Malvinas)'),
(69, 'FO', 'Faroe Islands'),
(70, 'FJ', 'Fiji'),
(71, 'FI', 'Finland'),
(72, 'FR', 'France'),
(73, 'FX', 'France, Metropolitan'),
(74, 'GF', 'French Guiana'),
(75, 'PF', 'French Polynesia'),
(76, 'TF', 'French Southern Territories'),
(77, 'GA', 'Gabon'),
(78, 'GM', 'Gambia'),
(79, 'GE', 'Georgia'),
(80, 'DE', 'Germany'),
(81, 'GH', 'Ghana'),
(82, 'GI', 'Gibraltar'),
(83, 'GK', 'Guernsey'),
(84, 'GR', 'Greece'),
(85, 'GL', 'Greenland'),
(86, 'GD', 'Grenada'),
(87, 'GP', 'Guadeloupe'),
(88, 'GU', 'Guam'),
(89, 'GT', 'Guatemala'),
(90, 'GN', 'Guinea'),
(91, 'GW', 'Guinea-Bissau'),
(92, 'GY', 'Guyana'),
(93, 'HT', 'Haiti'),
(94, 'HM', 'Heard and Mc Donald Islands'),
(95, 'HN', 'Honduras'),
(96, 'HK', 'Hong Kong'),
(97, 'HU', 'Hungary'),
(98, 'IS', 'Iceland'),
(99, 'IN', 'India'),
(100, 'IM', 'Isle of Man'),
(101, 'ID', 'Indonesia'),
(102, 'IR', 'Iran (Islamic Republic of)'),
(103, 'IQ', 'Iraq'),
(104, 'IE', 'Ireland'),
(105, 'IL', 'Israel'),
(106, 'IT', 'Italy'),
(107, 'CI', 'Ivory Coast'),
(108, 'JE', 'Jersey'),
(109, 'JM', 'Jamaica'),
(110, 'JP', 'Japan'),
(111, 'JO', 'Jordan'),
(112, 'KZ', 'Kazakhstan'),
(113, 'KE', 'Kenya'),
(114, 'KI', 'Kiribati'),
(115, 'KP', 'Korea, Democratic People\'s Republic of'),
(116, 'KR', 'Korea, Republic of'),
(117, 'XK', 'Kosovo'),
(118, 'KW', 'Kuwait'),
(119, 'KG', 'Kyrgyzstan'),
(120, 'LA', 'Lao People\'s Democratic Republic'),
(121, 'LV', 'Latvia'),
(122, 'LB', 'Lebanon'),
(123, 'LS', 'Lesotho'),
(124, 'LR', 'Liberia'),
(125, 'LY', 'Libyan Arab Jamahiriya'),
(126, 'LI', 'Liechtenstein'),
(127, 'LT', 'Lithuania'),
(128, 'LU', 'Luxembourg'),
(129, 'MO', 'Macau'),
(130, 'MK', 'Macedonia'),
(131, 'MG', 'Madagascar'),
(132, 'MW', 'Malawi'),
(133, 'MY', 'Malaysia'),
(134, 'MV', 'Maldives'),
(135, 'ML', 'Mali'),
(136, 'MT', 'Malta'),
(137, 'MH', 'Marshall Islands'),
(138, 'MQ', 'Martinique'),
(139, 'MR', 'Mauritania'),
(140, 'MU', 'Mauritius'),
(141, 'TY', 'Mayotte'),
(142, 'MX', 'Mexico'),
(143, 'FM', 'Micronesia, Federated States of'),
(144, 'MD', 'Moldova, Republic of'),
(145, 'MC', 'Monaco'),
(146, 'MN', 'Mongolia'),
(147, 'ME', 'Montenegro'),
(148, 'MS', 'Montserrat'),
(149, 'MA', 'Morocco'),
(150, 'MZ', 'Mozambique'),
(151, 'MM', 'Myanmar'),
(152, 'NA', 'Namibia'),
(153, 'NR', 'Nauru'),
(154, 'NP', 'Nepal'),
(155, 'NL', 'Netherlands'),
(156, 'AN', 'Netherlands Antilles'),
(157, 'NC', 'New Caledonia'),
(158, 'NZ', 'New Zealand'),
(159, 'NI', 'Nicaragua'),
(160, 'NE', 'Niger'),
(161, 'NG', 'Nigeria'),
(162, 'NU', 'Niue'),
(163, 'NF', 'Norfolk Island'),
(164, 'MP', 'Northern Mariana Islands'),
(165, 'NO', 'Norway'),
(166, 'OM', 'Oman'),
(167, 'PK', 'Pakistan'),
(168, 'PW', 'Palau'),
(169, 'PS', 'Palestine'),
(170, 'PA', 'Panama'),
(171, 'PG', 'Papua New Guinea'),
(172, 'PY', 'Paraguay'),
(173, 'PE', 'Peru'),
(174, 'PH', 'Philippines'),
(175, 'PN', 'Pitcairn'),
(176, 'PL', 'Poland'),
(177, 'PT', 'Portugal'),
(178, 'PR', 'Puerto Rico'),
(179, 'QA', 'Qatar'),
(180, 'RE', 'Reunion'),
(181, 'RO', 'Romania'),
(182, 'RU', 'Russian Federation'),
(183, 'RW', 'Rwanda'),
(184, 'KN', 'Saint Kitts and Nevis'),
(185, 'LC', 'Saint Lucia'),
(186, 'VC', 'Saint Vincent and the Grenadines'),
(187, 'WS', 'Samoa'),
(188, 'SM', 'San Marino'),
(189, 'ST', 'Sao Tome and Principe'),
(190, 'SA', 'Saudi Arabia'),
(191, 'SN', 'Senegal'),
(192, 'RS', 'Serbia'),
(193, 'SC', 'Seychelles'),
(194, 'SL', 'Sierra Leone'),
(195, 'SG', 'Singapore'),
(196, 'SK', 'Slovakia'),
(197, 'SI', 'Slovenia'),
(198, 'SB', 'Solomon Islands'),
(199, 'SO', 'Somalia'),
(200, 'ZA', 'South Africa'),
(201, 'GS', 'South Georgia South Sandwich Islands'),
(202, 'SS', 'South Sudan'),
(203, 'ES', 'Spain'),
(204, 'LK', 'Sri Lanka'),
(205, 'SH', 'St. Helena'),
(206, 'PM', 'St. Pierre and Miquelon'),
(207, 'SD', 'Sudan'),
(208, 'SR', 'Suriname'),
(209, 'SJ', 'Svalbard and Jan Mayen Islands'),
(210, 'SZ', 'Swaziland'),
(211, 'SE', 'Sweden'),
(212, 'CH', 'Switzerland'),
(213, 'SY', 'Syrian Arab Republic'),
(214, 'TW', 'Taiwan'),
(215, 'TJ', 'Tajikistan'),
(216, 'TZ', 'Tanzania, United Republic of'),
(217, 'TH', 'Thailand'),
(218, 'TG', 'Togo'),
(219, 'TK', 'Tokelau'),
(220, 'TO', 'Tonga'),
(221, 'TT', 'Trinidad and Tobago'),
(222, 'TN', 'Tunisia'),
(223, 'TR', 'Turkey'),
(224, 'TM', 'Turkmenistan'),
(225, 'TC', 'Turks and Caicos Islands'),
(226, 'TV', 'Tuvalu'),
(227, 'UG', 'Uganda'),
(228, 'UA', 'Ukraine'),
(229, 'AE', 'United Arab Emirates'),
(230, 'GB', 'United Kingdom'),
(231, 'US', 'United States'),
(232, 'UM', 'United States minor outlying islands'),
(233, 'UY', 'Uruguay'),
(234, 'UZ', 'Uzbekistan'),
(235, 'VU', 'Vanuatu'),
(236, 'VA', 'Vatican City State'),
(237, 'VE', 'Venezuela'),
(238, 'VN', 'Vietnam'),
(239, 'VG', 'Virgin Islands (British)'),
(240, 'VI', 'Virgin Islands (U.S.)'),
(241, 'WF', 'Wallis and Futuna Islands'),
(242, 'EH', 'Western Sahara'),
(243, 'YE', 'Yemen'),
(244, 'ZR', 'Zaire'),
(245, 'ZM', 'Zambia'),
(246, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `courseinstructor`
--

CREATE TABLE `courseinstructor` (
  `id` int(11) NOT NULL,
  `instructor` int(11) NOT NULL,
  `course` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `courseinstructor`
--

INSERT INTO `courseinstructor` (`id`, `instructor`, `course`) VALUES
(3, 5, 5),
(4, 6, 5),
(5, 7, 5),
(6, 5, 6),
(7, 7, 7),
(8, 5, 8),
(9, 6, 8),
(10, 7, 8),
(11, 6, 9),
(12, 6, 10);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `period` varchar(255) NOT NULL,
  `practical_classes` int(11) NOT NULL,
  `theory_classes` int(11) NOT NULL,
  `status` enum('Available','Unavailable') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `school`, `branch`, `name`, `price`, `image`, `duration`, `period`, `practical_classes`, `theory_classes`, `status`, `created_at`) VALUES
(5, 1, 1, 'Light Cars', 23, 'Un8vDrfjDMGQQg3esafeoYvb4Lz78SFH.png', 28, 'Days', 5, 5, 'Available', '2019-01-27 13:22:05'),
(6, 1, 1, 'Luxury Cars', 199, 'YYcNt6zHvnORg435ieZGlXtyH35FJeU8.png', 3, 'Months', 10, 10, 'Available', '2019-01-27 13:28:26'),
(7, 1, 1, 'Motor Cycle', 79, 'rjKfoNL1IX9Stugz8KeKRbZKfqFWfytm.png', 5, 'Weeks', 6, 5, 'Available', '2019-01-27 13:30:52'),
(8, 1, 1, 'Heavy Truck', 499, 'HvRvBksX4z2IcmvKcBFTI2iDfZbLHPSU.png', 5, 'Months', 25, 29, 'Available', '2019-01-27 13:32:32'),
(9, 1, 1, 'Sample File', 56, 'vg5FgKL8eiDrSb7jdrIdWyTsDnkAVdMj.png', 7, 'Weeks', 6, 4, 'Available', '2019-03-27 05:30:29'),
(10, 1, 1, 'Demo', 5, 'j73LyPKTV0sHSQ7PRzcSMrO9MTB3grIy.png', 2, 'Days', 4, 5, 'Available', '2019-06-16 14:29:46');

-- --------------------------------------------------------

--
-- Table structure for table `coursesenrolled`
--

CREATE TABLE `coursesenrolled` (
  `id` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `student` int(11) NOT NULL,
  `course` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `total_theory` int(11) NOT NULL,
  `total_practical` int(11) NOT NULL,
  `completed_theory` int(11) NOT NULL DEFAULT '0',
  `completed_practical` int(11) NOT NULL DEFAULT '0',
  `completed_on` date DEFAULT NULL,
  `status` enum('Pending','Complete') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `coursesenrolled`
--

INSERT INTO `coursesenrolled` (`id`, `school`, `branch`, `student`, `course`, `created_at`, `updated_at`, `total_theory`, `total_practical`, `completed_theory`, `completed_practical`, `completed_on`, `status`) VALUES
(1, 1, 1, 11, 5, '2019-01-27 13:50:27', '2019-01-27 13:50:27', 5, 5, 0, 0, NULL, 'Pending'),
(2, 1, 1, 12, 7, '2019-01-27 13:51:31', '2019-01-27 13:51:31', 6, 5, 0, 0, NULL, 'Pending'),
(3, 1, 1, 13, 8, '2019-01-27 13:52:15', '2019-01-27 13:52:15', 25, 29, 0, 0, NULL, 'Pending'),
(4, 1, 1, 11, 6, '2019-01-27 13:52:36', '2019-01-27 14:00:25', 10, 10, 1, 0, NULL, 'Pending'),
(5, 1, 1, 14, 9, '2019-03-28 17:13:25', '2019-03-28 17:13:25', 6, 4, 0, 0, NULL, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `code` varchar(56) NOT NULL,
  `symbol` varchar(19) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `code`, `symbol`) VALUES
(1, 'United Arab Emirates Dirham', 'AED', '\\u062f.\\u0625'),
(4, 'Armenia Dram', 'AMD', 'AMD'),
(5, 'Netherlands Antilles Guilder', 'ANG', '\\u0192'),
(7, 'Argentina Peso', 'ARS', '$'),
(8, 'Australia Dollar', 'AUD', '$'),
(9, 'Aruba Guilder', 'AWG', '\\u0192'),
(11, 'Bosnia and Herzegovina Convertible Marka', 'BAM', 'KM'),
(13, 'Bangladesh Taka', 'BDT', 'Tk'),
(15, 'Bahrain Dinar', 'BHD', 'BHD'),
(17, 'Bermuda Dollar', 'BMD', '$'),
(18, 'Brunei Darussalam Dollar', 'BND', '$'),
(19, 'Bolivia Boliviano', 'BOB', '$b'),
(20, 'Brazil Real', 'BRL', 'R$'),
(21, 'Bahamas Dollar', 'BSD', '$'),
(24, 'Botswana Pula', 'BWP', 'P'),
(26, 'Belize Dollar', 'BZD', 'BZ$'),
(27, 'Canada Dollar', 'CAD', '$'),
(29, 'Switzerland Franc', 'CHF', 'CHF'),
(30, 'Chile Peso', 'CLP', '$'),
(31, 'China Yuan Renminbi', 'CNY', '\\xa5'),
(32, 'Colombia Peso', 'COP', 'p.'),
(33, 'Costa Rica Colon', 'CRC', '\\u20a1'),
(34, 'Cuba Convertible Peso', 'CUC', 'CUC'),
(35, 'Cuba Peso', 'CUP', '\\u20b1'),
(37, 'Czech ReKoruna', 'CZK', 'K\\u010d'),
(39, 'Denmark Krone', 'DKK', 'kr'),
(40, 'Dominican RePeso', 'DOP', 'RD$'),
(42, 'Egypt Pound', 'EGP', '\\xa3'),
(45, 'Euro Member Countries', 'EUR', '\\u20ac'),
(48, 'United Kingdom Pound', 'GBP', '\\xa3'),
(52, 'Gibraltar Pound', 'GIP', '\\xa3'),
(55, 'Guatemala Quetzal', 'GTQ', 'Q'),
(57, 'Hong Kong Dollar', 'HKD', 'HK$'),
(58, 'Honduras Lempira', 'HNL', 'L'),
(59, 'Croatia Kuna', 'HRK', 'kn'),
(61, 'Hungary Forint', 'HUF', 'Ft'),
(62, 'Indonesia Rupiah', 'IDR', 'Rp'),
(63, 'Israel Shekel', 'ILS', '\\u20aa'),
(65, 'India Rupee', 'INR', 'Rs'),
(67, 'Iran Rial', 'IRR', 'IRR'),
(68, 'Iceland Krona', 'ISK', 'kr'),
(70, 'Jamaica Dollar', 'JMD', 'J$'),
(71, 'Jordan Dinar', 'JOD', 'JOD'),
(72, 'Japan Yen', 'JPY', '\\xa5'),
(73, 'Kenya Shilling', 'KES', 'KSh'),
(78, 'Korea (South) Won', 'KRW', '\\u20a9'),
(79, 'Kuwait Dinar', 'KWD', '\\u0643'),
(80, 'Cayman Islands Dollar', 'KYD', '$'),
(83, 'Lebanon Pound', 'LBP', '\\xa3'),
(87, 'Lithuania Litas', 'LTL', 'Lt'),
(88, 'Latvia Lat', 'LVL', 'Ls'),
(93, 'Macedonia Denar', 'MKD', '\\u0434\\u0435\\u043d'),
(98, 'Mauritius Rupee', 'MUR', 'Rs'),
(101, 'Mexico Peso', 'MXN', '$'),
(102, 'Malaysia Ringgit', 'MYR', 'RM'),
(107, 'Norway Krone', 'NOK', 'kr'),
(108, 'Nepal Rupee', 'NPR', 'Rs'),
(109, 'New Zealand Dollar', 'NZD', '$'),
(110, 'Oman Rial', 'OMR', 'OMR'),
(112, 'Peru Nuevo Sol', 'PEN', 'S/.'),
(114, 'Philippines Peso', 'PHP', '\\u20b1'),
(115, 'Pakistan Rupee', 'PKR', 'Rs'),
(116, 'Poland Zloty', 'PLN', 'z\\u0142'),
(119, 'Romania New Leu', 'RON', 'lei'),
(121, 'Russia Ruble', 'RUB', '\\u0440\\u0443\\u0431'),
(123, 'Saudi Arabia Riyal', 'SAR', 'SAR'),
(127, 'Sweden Krona', 'SEK', 'kr'),
(128, 'Singapore Dollar', 'SGD', '$'),
(135, 'El Salvador Colon', 'SVC', '$'),
(137, 'Swaziland Lilangeni', 'SZL', 'SZL'),
(138, 'Thailand Baht', 'THB', '\\u0e3f'),
(142, 'Tonga Paanga', 'TOP', 'TOP'),
(143, 'Turkey Lira', 'TRY', 'TRY'),
(147, 'Tanzania Shilling', 'TZS', 'TSh'),
(148, 'Ukraine Hryvna', 'UAH', '\\u20b4'),
(150, 'United States Dollar', 'USD', '$'),
(151, 'Uruguay Peso', 'UYU', '$U'),
(153, 'Venezuela Bolivar', 'VEF', 'Bs'),
(154, 'Viet Nam Dong', 'VND', '\\u20ab'),
(155, 'Vanuatu Vatu', 'VUV', 'VUV'),
(158, 'East Caribbean Dollar', 'XCD', '$'),
(163, 'South Africa Rand', 'ZAR', 'R'),
(164, 'Zimbabwe Dollar', 'ZWD', 'Z$');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `course` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `retakes` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` enum('Unpublished','Published') NOT NULL DEFAULT 'Unpublished',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `school`, `branch`, `course`, `name`, `retakes`, `description`, `status`, `updated_at`) VALUES
(1, 1, 1, 9, 'Final Exam', 0, 'This is the last exam ever!', 'Unpublished', '2019-03-29 07:48:01'),
(4, 1, 1, 9, 'Business Letter', 5, 'wrb fnfn', 'Published', '2019-03-29 08:06:44'),
(5, 1, 1, 8, 'Truck exam', 3, 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec', 'Unpublished', '2019-05-27 05:06:05');

-- --------------------------------------------------------

--
-- Table structure for table `examsreports`
--

CREATE TABLE `examsreports` (
  `id` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `course` int(11) NOT NULL,
  `exam` int(11) NOT NULL,
  `student` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `correctlyAnswered` int(11) NOT NULL,
  `totalQuestions` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `examsreports`
--

INSERT INTO `examsreports` (`id`, `school`, `branch`, `course`, `exam`, `student`, `score`, `correctlyAnswered`, `totalQuestions`) VALUES
(1, 1, 1, 9, 4, 1, 75, 3, 4),
(2, 1, 1, 9, 4, 1, 75, 3, 4),
(3, 1, 1, 9, 4, 14, 75, 3, 4),
(4, 1, 1, 9, 4, 14, 75, 3, 4),
(5, 1, 1, 8, 5, 1, 67, 2, 3),
(6, 1, 1, 8, 5, 1, 100, 3, 3),
(7, 1, 1, 8, 5, 1, 100, 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `fleet`
--

CREATE TABLE `fleet` (
  `id` int(11) NOT NULL,
  `carno_` varchar(32) NOT NULL,
  `carplate` varchar(255) NOT NULL,
  `make` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `modelyear` varchar(6) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `instructor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fleet`
--

INSERT INTO `fleet` (`id`, `carno_`, `carplate`, `make`, `model`, `modelyear`, `school`, `branch`, `instructor`) VALUES
(1, '123', 'KHJ573', 'TOYOTA', 'COROLLA', '2018', 1, 1, 6),
(2, '34', 'HGT876', 'Lexus', 'RX320', '2017', 1, 1, 7),
(3, '56', 'HGY567', 'BMW', 'X6', '2019', 1, 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `reference` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `student` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `item` varchar(256) NOT NULL,
  `amountpaid` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `reference`, `school`, `branch`, `student`, `amount`, `item`, `amountpaid`, `created_at`, `updated_at`) VALUES
(1, 935061, 1, 1, 11, 23, 'Light Cars', 8, '2019-01-27 13:50:27', '2019-01-27 13:52:58'),
(2, 280025, 1, 1, 12, 79, 'Motor Cycle', 4, '2019-01-27 13:51:31', '0000-00-00 00:00:00'),
(3, 202658, 1, 1, 13, 499, 'Heavy Truck', 0, '2019-01-27 13:52:15', '0000-00-00 00:00:00'),
(4, 507585, 1, 1, 11, 199, 'Luxury Cars', 187, '2019-01-27 13:52:36', '2019-01-27 13:53:14');

-- --------------------------------------------------------

--
-- Table structure for table `lectureprogress`
--

CREATE TABLE `lectureprogress` (
  `id` int(11) NOT NULL,
  `student` int(11) NOT NULL,
  `lecture` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `course` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  `chapter` int(11) NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lectureprogress`
--

INSERT INTO `lectureprogress` (`id`, `student`, `lecture`, `school`, `branch`, `course`, `class`, `chapter`, `started_at`) VALUES
(1, 14, 54, 1, 1, 9, 6, 5, '2019-03-28 17:49:06'),
(2, 14, 54, 1, 1, 9, 6, 5, '2019-03-28 17:49:06'),
(3, 14, 54, 1, 1, 9, 6, 5, '2019-03-28 17:49:06'),
(4, 14, 54, 1, 1, 9, 6, 5, '2019-03-28 17:49:06'),
(5, 14, 54, 1, 1, 9, 6, 5, '2019-03-28 17:49:06'),
(6, 14, 14, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(7, 14, 13, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(8, 14, 16, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(9, 14, 16, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(10, 14, 16, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(11, 14, 16, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(12, 14, 13, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(13, 14, 14, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(14, 14, 16, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(15, 14, 13, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(16, 14, 54, 1, 1, 9, 6, 5, '2019-03-28 17:49:06'),
(17, 14, 54, 1, 1, 9, 6, 5, '2019-03-28 17:49:06'),
(18, 14, 16, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(19, 14, 13, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(20, 14, 54, 1, 1, 9, 6, 5, '2019-03-28 17:49:06'),
(21, 14, 54, 1, 1, 9, 6, 5, '2019-03-28 17:49:06'),
(22, 14, 18, 1, 1, 9, 6, 19, '2019-03-28 17:49:06'),
(23, 14, 19, 1, 1, 9, 6, 19, '2019-03-28 17:49:06'),
(24, 14, 54, 1, 1, 9, 6, 5, '2019-03-28 17:49:06'),
(25, 14, 18, 1, 1, 9, 6, 19, '2019-03-28 17:49:06'),
(26, 14, 25, 1, 1, 9, 6, 14, '2019-03-28 17:49:06'),
(27, 14, 27, 1, 1, 9, 6, 12, '2019-03-28 17:49:06'),
(28, 14, 26, 1, 1, 9, 6, 12, '2019-03-28 17:49:06'),
(29, 14, 20, 1, 1, 9, 6, 19, '2019-03-28 17:49:06'),
(30, 14, 53, 1, 1, 9, 6, 5, '2019-03-28 17:49:06'),
(31, 14, 52, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(32, 14, 31, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(33, 14, 30, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(34, 14, 28, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(35, 14, 25, 1, 1, 9, 6, 14, '2019-03-28 17:49:06'),
(36, 14, 28, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(37, 14, 30, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(38, 14, 52, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(39, 14, 52, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(40, 14, 31, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(41, 14, 30, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(42, 14, 28, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(43, 14, 54, 1, 1, 9, 6, 5, '2019-03-28 17:49:06'),
(44, 14, 53, 1, 1, 9, 6, 5, '2019-03-28 17:49:06'),
(45, 14, 52, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(46, 14, 31, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(47, 14, 30, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(48, 14, 28, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(49, 14, 14, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(50, 14, 13, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(51, 14, 16, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(52, 14, 16, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(53, 14, 13, 1, 1, 9, 6, 7, '2019-03-28 17:49:06'),
(54, 14, 14, 1, 1, 9, 6, 7, '2019-03-28 17:49:06');

-- --------------------------------------------------------

--
-- Table structure for table `lectures`
--

CREATE TABLE `lectures` (
  `id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `type` enum('pdf','link','text','downloads','video') NOT NULL,
  `content` text NOT NULL,
  `indexing` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `course` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  `chapter` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lectures`
--

INSERT INTO `lectures` (`id`, `title`, `description`, `type`, `content`, `indexing`, `school`, `branch`, `course`, `class`, `chapter`) VALUES
(13, 'vdfvjfdbv link', 'sdvbjds n sdjvisdvdsv linkdes', 'link', 'https://unsplash.com/', 2, 1, 1, 9, 6, 7),
(14, 'dfvdfv', 'fdvfdv link desc', 'link', 'https://unsplash.com/photos/jCWpF3uteec', 3, 1, 1, 9, 6, 7),
(16, 'testyy', 'teshsd jksdbv vtexyt', 'text', 'https://unsplash.com/', 1, 1, 1, 9, 6, 7),
(18, 'vdfvjfdbv link rruu', 'dfknvkdf lkdfnvkdfv', 'link', 'https://unsplash.com/', 1, 1, 1, 9, 6, 19),
(19, 'vdfvjfdbv link rruu', 'dfknvkdf lkdfnvkdfv', 'link', 'https://unsplash.com/', 2, 1, 1, 9, 6, 19),
(20, 'vdfvjfdbv link rruu', 'dfknvkdf lkdfnvkdfv', 'link', 'https://unsplash.com/', 3, 1, 1, 9, 6, 19),
(25, 'testyyuuuuuuu', 'vh j jh https://unsplash.com/', 'link', 'https://unsplash.com/', 1, 1, 1, 9, 6, 14),
(26, 'erfrevreverv yyyyyyyyyy', 'erfer', 'text', 'https://unsplash.com/', 1, 1, 1, 9, 6, 12),
(27, 'hhhhhhhhhhh', 'jkerbjbger', 'link', 'https://unsplash.com/', 2, 1, 1, 9, 6, 12),
(28, 'fdbdfb', 'dfbdfb', 'text', 'm m m ,', 4, 1, 1, 9, 6, 7),
(30, 'fdbdfb', 'ewfwef', 'link', 'http://localhost.com/landa/course/6/curriculum', 5, 1, 1, 9, 6, 7),
(31, 'wefewf', 'fwefwe', 'text', 'kjbkjbkjbkj', 6, 1, 1, 9, 6, 7),
(52, 'Laws of the road', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.\r\n\r\nDonec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo.', 'pdf', 'hLauPkRqpBBigKHygcM1SnE5lIrNAHlB.pdf', 7, 1, 1, 9, 6, 7),
(53, 'Sucurity Tips when driving', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.\r\n\r\nDonec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo.', 'downloads', '3a8uAKRIgzv4FHXgcWiNgv8vliiG8Uff.png', 1, 1, 1, 9, 6, 5),
(54, 'Basic mechanic tutorial', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.\r\n\r\nDonec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo.', 'video', 'niCtJgQpeJQLRUetJp63Id9bOkPSo0Fb.mp4', 2, 1, 1, 9, 6, 5),
(55, 'Truck type', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec', 'text', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec', 1, 1, 1, 8, 7, 23),
(56, 'Truck usage manual', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec', 'pdf', 'wjs5P2D0q3AwPfOOtorZCcXfc77saWkU.pdf', 2, 1, 1, 8, 7, 23),
(57, 'Website of truck firms', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec', 'link', 'https://google.com', 3, 1, 1, 8, 7, 23),
(60, 'Images of trucks', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec', 'downloads', 'https://google.com', 4, 1, 1, 8, 7, 23),
(61, 'demo', 'lscsdcsdv', 'pdf', 'E4rcKssVidoMTKnHeJwkpLO44jivOCSH.pdf', 2, 1, 1, 9, 6, 14);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `note_by` int(11) NOT NULL,
  `note_for` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `note_by`, `note_for`, `note`, `created_at`) VALUES
(1, 1, 11, 'She is a good girl', '2019-01-27 13:53:29');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `type` enum('newaccount','payment','delete','message','calendar') DEFAULT NULL,
  `class` enum('personal','school','branch','system') NOT NULL,
  `message` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user`, `school`, `branch`, `type`, `class`, `message`, `created_at`) VALUES
(1, 11, 1, 1, 'newaccount', 'personal', 'Account created by <strong>John Doe</strong>', '2019-01-27 13:50:27'),
(2, 1, 1, 1, 'newaccount', 'branch', 'New Student account created for <strong>Mark Angelo</strong>.', '2019-01-27 13:50:27'),
(3, 11, 1, 1, 'payment', 'personal', 'You made a payment of <strong>$5.00</strong>.', '2019-01-27 13:50:27'),
(4, 1, 1, 1, 'payment', 'branch', 'A payment of <strong>$5.00</strong> has been received from <strong>Mark Angelo</strong>.', '2019-01-27 13:50:27'),
(5, 12, 1, 1, 'newaccount', 'personal', 'Account created by <strong>John Doe</strong>', '2019-01-27 13:51:31'),
(6, 1, 1, 1, 'newaccount', 'branch', 'New Student account created for <strong>Vera Watson</strong>.', '2019-01-27 13:51:31'),
(7, 12, 1, 1, 'payment', 'personal', 'You made a payment of <strong>$4.00</strong>.', '2019-01-27 13:51:31'),
(8, 1, 1, 1, 'payment', 'branch', 'A payment of <strong>$4.00</strong> has been received from <strong>Vera Watson</strong>.', '2019-01-27 13:51:31'),
(9, 13, 1, 1, 'newaccount', 'personal', 'Account created by <strong>John Doe</strong>', '2019-01-27 13:52:15'),
(10, 1, 1, 1, 'newaccount', 'branch', 'New Student account created for <strong>Emmah Miles</strong>.', '2019-01-27 13:52:15'),
(11, 11, 1, 1, 'payment', 'personal', 'You made a payment of <strong>$199.00</strong>.', '2019-01-27 13:52:36'),
(12, 1, 1, 1, 'payment', 'branch', 'A payment of <strong>$199.00</strong> has been received from <strong>Mark Angelo</strong>.', '2019-01-27 13:52:36'),
(13, 11, 1, 1, 'payment', 'personal', 'You made a payment of <strong>$3.00</strong>.', '2019-01-27 13:52:58'),
(14, 1, 1, 1, 'payment', 'branch', 'A payment of <strong>$3.00</strong> has been received from <strong>Mark Angelo</strong>.', '2019-01-27 13:52:58'),
(15, 11, 1, 1, 'payment', 'personal', 'You made a payment of <strong>$12.00</strong>.', '2019-01-27 13:53:09'),
(16, 1, 1, 1, 'payment', 'branch', 'A payment of <strong>$12.00</strong> has been received from <strong>Mark Angelo</strong>.', '2019-01-27 13:53:09'),
(17, 11, 1, 1, 'delete', 'personal', 'A payment record of <strong>$12.00</strong> has been deleted from your account.', '2019-01-27 13:53:14'),
(18, 1, 1, 1, 'delete', 'branch', 'A payment record of <strong>$12.00</strong> has been deleted by <strong>John Doe</strong>.', '2019-01-27 13:53:14'),
(19, 14, 1, 1, 'newaccount', 'personal', 'Account created by <strong>John Doe</strong>', '2019-02-11 07:47:44'),
(20, 1, 1, 1, 'newaccount', 'branch', 'New Student account created for <strong>Tony Stark</strong>.', '2019-02-11 07:47:44'),
(21, 1, 1, 1, 'message', 'branch', 'SMS sent to all students by <strong>John Doe</strong>.', '2019-04-16 11:58:09'),
(22, 11, 1, 1, 'payment', 'personal', 'You made a payment of <strong>$6.00</strong>.', '2019-06-16 14:44:00'),
(23, 1, 1, 1, 'payment', 'branch', 'A payment of <strong>$6.00</strong> has been received from <strong>Mark Angelo</strong>.', '2019-06-16 14:44:00'),
(24, 11, 1, 1, 'delete', 'personal', 'Invoice #357751 of <strong>$499.00</strong> has been deleted from your account.', '2019-06-16 14:54:55'),
(25, 1, 1, 1, 'delete', 'branch', 'Invoice #357751 of <strong>$499.00</strong>  has been deleted by <strong>John Doe</strong>.', '2019-06-16 14:54:55');

-- --------------------------------------------------------

--
-- Table structure for table `onlineclasschapters`
--

CREATE TABLE `onlineclasschapters` (
  `id` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `course` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `indexing` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `onlineclasschapters`
--

INSERT INTO `onlineclasschapters` (`id`, `school`, `branch`, `course`, `class`, `title`, `description`, `indexing`) VALUES
(5, 1, 1, 9, 6, 'efwef 1', 'ewfwefe', 2),
(7, 1, 1, 9, 6, 'alkscjcjsd   ---  intrude', 'dscdsjhchsdb ksdcjbsdjv', 1),
(12, 1, 1, 9, 6, 'sdcvds . 4', 'ssssssss', 4),
(14, 1, 1, 9, 6, 'gerreg re g ebgrijer 6666', 'dsv', 5),
(19, 1, 1, 9, 6, 'test', 'test', 3),
(22, 1, 1, 9, 1, 'Introduction to Driving', 'This is the first chapter of driving', 1),
(23, 1, 1, 8, 7, 'What is a truck', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec', 1);

-- --------------------------------------------------------

--
-- Table structure for table `onlineclasses`
--

CREATE TABLE `onlineclasses` (
  `id` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `course` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `status` enum('Unpublished','Published') NOT NULL DEFAULT 'Unpublished',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `onlineclasses`
--

INSERT INTO `onlineclasses` (`id`, `school`, `branch`, `course`, `name`, `description`, `status`, `updated_at`) VALUES
(1, 1, 1, 9, 'Daniel Kimeli', 'dcsbchusbcuvd', 'Unpublished', '2019-03-27 15:00:19'),
(5, 1, 1, 9, 'Signer', '', 'Unpublished', '2019-03-27 15:09:39'),
(6, 1, 1, 9, 'Driving a light vehicle ', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.\r\n\r\nDonec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo.', 'Published', '2019-03-27 15:13:51'),
(7, 1, 1, 8, 'Introduction to trucks', 'This is an important class', 'Unpublished', '2019-05-27 04:59:01');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `student` int(11) NOT NULL,
  `invoice` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `method` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `school`, `branch`, `student`, `invoice`, `amount`, `method`, `created_at`) VALUES
(1, 1, 1, 11, 1, 5, 'Cash', '2019-01-27 13:50:27'),
(2, 1, 1, 12, 2, 4, 'Cash', '2019-01-27 13:51:31'),
(3, 1, 1, 11, 4, 199, 'Paypal', '2019-01-27 13:52:36'),
(4, 1, 1, 11, 1, 3, 'Mpesa', '2019-01-22 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `course` int(11) NOT NULL,
  `exam` int(11) NOT NULL,
  `question` varchar(256) NOT NULL,
  `answers` text NOT NULL,
  `correct` text NOT NULL,
  `indexing` int(11) NOT NULL,
  `required` enum('yes','no') NOT NULL,
  `type` enum('multiple','single') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `school`, `branch`, `course`, `exam`, `question`, `answers`, `correct`, `indexing`, `required`, `type`) VALUES
(7, 1, 1, 9, 4, 'we jw f wefj nxnac sj1?', '[\"sdvdsv\",\"sdv\",\"dsvsd\",\"vdsvdsv\"]', '[\"0\",\"1\",\"0\",\"0\"]', 3, 'yes', 'single'),
(10, 1, 1, 9, 4, 'this is intrusion?', '[\"sdfsdf\",\"sdfsdfsd last\"]', '[\"1\",\"0\"]', 1, 'yes', 'multiple'),
(15, 1, 1, 9, 4, 'sdvsdvdsv?', '[\"sdvsdv\",\"dsvd\",\"sdvsdvdsv\",\"svdsv\"]', '[\"0\",\"0\",\"1\",\"1\"]', 4, 'yes', 'multiple'),
(16, 1, 1, 9, 4, 'How old am i?', '[\"1\",\"12\",\"23\",\"56\"]', '[\"0\",\"0\",\"1\",\"0\"]', 2, 'yes', 'single'),
(17, 1, 1, 8, 5, 'What is your name?', '[\"Dan\",\"Kimeli\",\"Dude\",\"Moses\"]', '[\"1\",\"1\",\"0\",\"0\"]', 1, 'yes', 'multiple'),
(18, 1, 1, 8, 5, 'Your gender?', '[\"Male\",\"Female\"]', '[\"1\",\"0\"]', 2, 'yes', 'single'),
(19, 1, 1, 8, 5, 'How old are you?', '[\"23\",\"26\",\"32\"]', '[\"1\",\"0\",\"0\"]', 3, 'yes', 'multiple');

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `id` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `subject` varchar(256) DEFAULT NULL,
  `days` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` enum('class','payment') NOT NULL,
  `send_via` enum('email','sms') NOT NULL,
  `timing` enum('before_due','after_due') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reminders`
--

INSERT INTO `reminders` (`id`, `school`, `subject`, `days`, `message`, `type`, `send_via`, `timing`) VALUES
(1, 1, 'Payment reminder', 1, 'Hello [firstname],\r\n\r\nWe hope you are doing well.\r\nWe are am writing to remind you that your payment of $[amountdue] is due on [duedate].\r\nPlease settle as soon as possible to avoid class interruption \r\n\r\nCheers!\r\nLanda Team\r\n                                    ', 'payment', 'email', 'after_due'),
(2, 1, 'Payment reminder', 1, 'Hello [firstname],\r\n\r\nWe hope you are doing well.\r\nWe are am writing to remind you that your payment of $[amountdue] is due on [duedate].\r\nPlease settle as soon as possible to avoid class interruption \r\n\r\nCheers!\r\nPool Driving\r\n                                    ', 'payment', 'email', 'before_due'),
(3, 1, 'Payment reminder', 1, 'Hello [firstname],\r\n\r\nWe hope you are doing well.\r\nWe are am writing to remind you that your payment of $[amountdue] is due on [duedate].\r\nPlease settle as soon as possible to avoid class interruption \r\n\r\nCheers!\r\nPool Driving\r\n                                    ', 'payment', 'email', 'before_due');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `start` varchar(255) NOT NULL,
  `end` varchar(255) NOT NULL,
  `course` int(11) NOT NULL,
  `student` int(11) NOT NULL,
  `instructor` int(11) NOT NULL,
  `class_type` enum('Practical','Theory') NOT NULL,
  `car` int(11) DEFAULT NULL,
  `status` enum('New','Complete','Missed') NOT NULL,
  `student_google_id` varchar(64) DEFAULT NULL,
  `instructor_google_id` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `school`, `branch`, `start`, `end`, `course`, `student`, `instructor`, `class_type`, `car`, `status`, `student_google_id`, `instructor_google_id`, `created_at`) VALUES
(1, 1, 1, '2019-01-02 00:00:00', '2019-01-03 00:00:00', 7, 11, 6, 'Theory', 1, 'Missed', NULL, NULL, '2019-01-27 14:00:06'),
(2, 1, 1, '2019-01-10 00:00:00', '2019-01-11 00:00:00', 6, 11, 6, 'Theory', 3, 'Complete', NULL, NULL, '2019-01-27 14:00:25'),
(3, 1, 1, '2019-01-16 00:00:00', '2019-01-17 00:00:00', 7, 12, 6, 'Practical', 2, 'Missed', NULL, NULL, '2019-01-27 14:00:55'),
(4, 1, 1, '2019-01-30 00:00:00', '2019-01-31 00:00:00', 6, 11, 6, 'Theory', 2, 'New', NULL, NULL, '2019-01-27 14:01:23'),
(5, 1, 1, '2019-01-29 02:30:00', '2019-01-29 03:00:00', 7, 11, 6, 'Practical', 2, 'New', NULL, NULL, '2019-01-27 14:01:55'),
(6, 1, 1, '2019-01-29 08:00:00', '2019-01-29 08:30:00', 6, 11, 6, 'Theory', 2, 'New', NULL, NULL, '2019-01-27 14:02:18'),
(7, 1, 1, '2019-01-10 08:00:00', '2019-01-10 08:30:00', 7, 11, 7, 'Theory', 2, 'Missed', NULL, NULL, '2019-01-27 14:03:06'),
(8, 1, 1, '2019-02-02 10:30:00', '2019-02-02 11:00:00', 6, 11, 6, 'Theory', 1, 'New', NULL, NULL, '2019-01-27 14:03:40'),
(9, 1, 1, '2019-01-28 00:00:00', '2019-01-29 00:00:00', 5, 11, 6, 'Theory', 1, 'New', NULL, NULL, '2019-01-28 15:09:23'),
(10, 1, 1, '2019-03-08 00:00:00', '2019-03-09 00:00:00', 7, 14, 6, 'Theory', 2, 'New', NULL, NULL, '2019-03-03 18:26:48'),
(11, 1, 1, '2019-04-15 00:00:00', '2019-04-16 00:00:00', 6, 11, 7, 'Practical', 2, 'New', NULL, NULL, '2019-04-12 13:47:47'),
(12, 1, 1, '2019-06-04 00:00:00', '2019-06-05 00:00:00', 6, 11, 5, 'Theory', 2, 'New', NULL, NULL, '2019-06-16 15:13:10'),
(13, 1, 1, '2019-06-18 00:00:00', '2019-06-19 00:00:00', 7, 11, 5, 'Theory', 3, 'New', NULL, NULL, '2019-06-16 15:13:34'),
(14, 1, 1, '2019-06-20 00:00:00', '2019-06-21 00:00:00', 7, 12, 6, 'Theory', 2, 'New', NULL, NULL, '2019-06-16 15:14:02'),
(16, 1, 1, '2019-06-16 20:25:00', '2019-06-16 21:25:00', 8, 12, 6, 'Theory', 3, 'New', NULL, NULL, '2019-06-16 17:22:16'),
(17, 1, 1, '2019-06-16 20:00:00', '2019-06-16 20:30:00', 9, 12, 5, 'Theory', 2, 'New', NULL, NULL, '2019-06-16 17:22:49'),
(18, 1, 1, '2019-06-16 17:00:00', '2019-06-16 17:30:00', 6, 11, 7, 'Theory', 2, 'New', NULL, NULL, '2019-06-16 17:24:42'),
(19, 1, 1, '2019-06-16 15:00:00', '2019-06-16 15:30:00', 6, 11, 6, 'Practical', 2, 'New', NULL, NULL, '2019-06-17 06:23:55'),
(20, 1, 1, '2019-06-19 10:00:00', '2019-06-19 10:30:00', 7, 12, 6, 'Theory', 2, 'New', NULL, NULL, '2019-06-17 09:23:42'),
(21, 1, 1, '2019-06-19 10:00:00', '2019-06-19 10:30:00', 7, 12, 6, 'Theory', 2, 'New', NULL, NULL, '2019-06-17 09:24:48'),
(22, 1, 1, '2019-06-19 10:00:00', '2019-06-19 10:30:00', 7, 12, 6, 'Theory', 2, 'New', NULL, NULL, '2019-06-17 09:25:02'),
(23, 1, 1, '2019-06-19 10:00:00', '2019-06-19 10:30:00', 7, 12, 6, 'Theory', 2, 'New', NULL, NULL, '2019-06-17 09:25:50'),
(24, 1, 1, '2019-06-19 10:00:00', '2019-06-19 10:30:00', 7, 12, 6, 'Theory', 2, 'New', '2vn5njpm5c6rkc5f40cl7tf1n8', NULL, '2019-06-17 09:29:20'),
(27, 1, 1, '2019-06-21 11:00:00', '2019-06-21 11:30:00', 6, 14, 6, 'Theory', 2, 'New', NULL, 'fk4d6j4aolki9ijhu5jffcsekk', '2019-06-17 10:17:49');

-- --------------------------------------------------------

--
-- Table structure for table `schoolmessages`
--

CREATE TABLE `schoolmessages` (
  `id` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `type` enum('sms','email') NOT NULL,
  `contact` varchar(64) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` longtext,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Sent','Failed') NOT NULL DEFAULT 'Sent',
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `schoolmessages`
--

INSERT INTO `schoolmessages` (`id`, `receiver`, `type`, `contact`, `subject`, `message`, `sent_at`, `status`, `school`, `branch`) VALUES
(1, 2, 'email', '+254720783834', NULL, 'Hello, this is a demo text message.', '2019-01-27 13:11:56', 'Sent', 1, 1),
(2, 2, 'email', 'school@example.com', 'Your request has been approved', 'Your request has been approved. \r\nWelcome on board', '2019-01-27 13:12:36', 'Sent', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(30) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(100) NOT NULL,
  `currency` varchar(8) NOT NULL DEFAULT 'USD',
  `timezone` varchar(32) NOT NULL DEFAULT 'Africa/Nairobi',
  `status` enum('Active','Suspended') NOT NULL,
  `payment_reminders` enum('On','Off') NOT NULL DEFAULT 'On',
  `class_reminders` enum('On','Off') NOT NULL DEFAULT 'On',
  `multibooking` enum('Enabled','Disabled') NOT NULL DEFAULT 'Enabled',
  `class_sms_notifications` enum('Enabled','Disabled') NOT NULL DEFAULT 'Enabled',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`id`, `name`, `email`, `phone`, `address`, `currency`, `timezone`, `status`, `payment_reminders`, `class_reminders`, `multibooking`, `class_sms_notifications`, `created_at`) VALUES
(1, 'Simcy Driving', 'demo@simcycreative.com', '+254720783834', 'Nakuru, Kenya.', 'USD', 'Africa/Nairobi', 'Active', 'On', 'On', 'Enabled', 'Disabled', '2019-01-08 06:51:03'),
(2, 'Rocky Driving School', 'school@example.com', '+254720783834', 'Nairobi, Kenya.', 'USD', 'Africa/Nairobi', 'Active', 'On', 'On', 'Enabled', 'Enabled', '2019-01-27 12:59:54'),
(3, 'Wings Driving School', 'wings@example.com', '+731702568', 'Nairobi, Kenya.', 'USD', 'Africa/Nairobi', 'Active', 'On', 'On', 'Enabled', 'Enabled', '2019-01-27 13:01:38'),
(4, 'Sample School', 'sample@example.com', '123456789', 'Somewhere, Uptown.', 'USD', 'Africa/Nairobi', 'Active', 'On', 'On', 'Enabled', 'Enabled', '2019-01-27 13:02:38');

-- --------------------------------------------------------

--
-- Table structure for table `studentexamresults`
--

CREATE TABLE `studentexamresults` (
  `id` int(11) NOT NULL,
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `course` int(11) NOT NULL,
  `exam` int(11) NOT NULL,
  `question` varchar(256) NOT NULL,
  `answer` text NOT NULL,
  `correct` enum('yes','no') NOT NULL,
  `indexing` int(11) NOT NULL,
  `student` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `studentexamresults`
--

INSERT INTO `studentexamresults` (`id`, `school`, `branch`, `course`, `exam`, `question`, `answer`, `correct`, `indexing`, `student`) VALUES
(1, 1, 1, 9, 4, 'this is intrusion?', '[\"sdfsdfsd last\"]', 'no', 1, 1),
(2, 1, 1, 9, 4, 'How old am i?', '[\"sdfsdfsd last\",\"23\"]', 'yes', 2, 1),
(3, 1, 1, 9, 4, 'we jw f wefj nxnac sj1?', '[\"sdfsdfsd last\",\"23\",\"dsvsd\"]', 'no', 3, 1),
(4, 1, 1, 9, 4, 'sdvsdvdsv?', '[\"sdfsdfsd last\",\"23\",\"dsvsd\",\"sdvsdvdsv\"]', 'yes', 4, 1),
(5, 1, 1, 9, 4, 'this is intrusion?', '[\"sdfsdfsd last\"]', 'no', 1, 1),
(6, 1, 1, 9, 4, 'How old am i?', '[\"sdfsdfsd last\",\"23\"]', 'yes', 2, 1),
(7, 1, 1, 9, 4, 'we jw f wefj nxnac sj1?', '[\"sdfsdfsd last\",\"23\",\"dsvsd\"]', 'no', 3, 1),
(8, 1, 1, 9, 4, 'sdvsdvdsv?', '[\"sdfsdfsd last\",\"23\",\"dsvsd\",\"sdvsdvdsv\"]', 'yes', 4, 1),
(9, 1, 1, 9, 4, 'this is intrusion?', '[\"sdfsdfsd last\"]', 'no', 1, 1),
(10, 1, 1, 9, 4, 'How old am i?', '[\"sdfsdfsd last\",\"23\"]', 'yes', 2, 1),
(11, 1, 1, 9, 4, 'we jw f wefj nxnac sj1?', '[\"sdfsdfsd last\",\"23\",\"sdv\"]', 'yes', 3, 1),
(12, 1, 1, 9, 4, 'sdvsdvdsv?', '[\"sdfsdfsd last\",\"23\",\"sdv\",\"sdvsdvdsv\"]', 'yes', 4, 1),
(13, 1, 1, 9, 4, 'this is intrusion?', '[\"sdfsdf\"]', 'yes', 1, 1),
(14, 1, 1, 9, 4, 'How old am i?', '[\"sdfsdf\",\"23\"]', 'yes', 2, 1),
(15, 1, 1, 9, 4, 'we jw f wefj nxnac sj1?', '[\"sdfsdf\",\"23\",\"dsvsd\"]', 'no', 3, 1),
(16, 1, 1, 9, 4, 'sdvsdvdsv?', '[\"sdfsdf\",\"23\",\"dsvsd\",\"sdvsdvdsv\"]', 'yes', 4, 1),
(17, 1, 1, 9, 4, 'this is intrusion?', '[\"sdfsdf\"]', 'yes', 1, 14),
(18, 1, 1, 9, 4, 'How old am i?', '[\"sdfsdf\",\"23\"]', 'yes', 2, 14),
(19, 1, 1, 9, 4, 'we jw f wefj nxnac sj1?', '[\"sdfsdf\",\"23\",\"dsvsd\"]', 'no', 3, 14),
(20, 1, 1, 9, 4, 'sdvsdvdsv?', '[\"sdfsdf\",\"23\",\"dsvsd\",\"svdsv\"]', 'yes', 4, 14),
(21, 1, 1, 9, 4, 'this is intrusion?', '[\"sdfsdf\"]', 'yes', 1, 14),
(22, 1, 1, 9, 4, 'How old am i?', '[\"sdfsdf\",\"23\"]', 'yes', 2, 14),
(23, 1, 1, 9, 4, 'we jw f wefj nxnac sj1?', '[\"sdfsdf\",\"23\",\"sdvdsv\"]', 'no', 3, 14),
(24, 1, 1, 9, 4, 'sdvsdvdsv?', '[\"sdfsdf\",\"23\",\"sdvdsv\",\"svdsv\"]', 'yes', 4, 14),
(25, 1, 1, 8, 5, 'What is your name?', '[\"Dude\"]', 'no', 1, 1),
(26, 1, 1, 8, 5, 'Your gender?', '[\"Dude\",\"Male\"]', 'yes', 2, 1),
(27, 1, 1, 8, 5, 'How old are you?', '[\"Dude\",\"Male\",\"23\"]', 'yes', 3, 1),
(28, 1, 1, 8, 5, 'What is your name?', '[\"Kimeli\"]', 'yes', 1, 1),
(29, 1, 1, 8, 5, 'Your gender?', '[\"Kimeli\",\"Male\"]', 'yes', 2, 1),
(30, 1, 1, 8, 5, 'How old are you?', '[\"Kimeli\",\"Male\",\"23\",\"26\"]', 'yes', 3, 1),
(31, 1, 1, 8, 5, 'What is your name?', '[\"Kimeli\"]', 'yes', 1, 1),
(32, 1, 1, 8, 5, 'Your gender?', '[\"Kimeli\",\"Male\"]', 'yes', 2, 1),
(33, 1, 1, 8, 5, 'How old are you?', '[\"Kimeli\",\"Male\",\"23\"]', 'yes', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `timeline`
--

CREATE TABLE `timeline` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `activity` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `timeline`
--

INSERT INTO `timeline` (`id`, `user`, `activity`, `created_at`) VALUES
(1, 11, 'Account created by <strong>John Doe</strong>', '2019-01-27 13:50:27'),
(2, 11, 'Enrolled to <strong>Light Cars</strong> course.', '2019-01-27 13:50:27'),
(3, 12, 'Account created by <strong>John Doe</strong>', '2019-01-27 13:51:31'),
(4, 12, 'Enrolled to <strong>Motor Cycle</strong> course.', '2019-01-27 13:51:31'),
(5, 13, 'Account created by <strong>John Doe</strong>', '2019-01-27 13:52:15'),
(6, 13, 'Enrolled to <strong>Heavy Truck</strong> course.', '2019-01-27 13:52:15'),
(7, 11, 'Enrolled to <strong>Luxury Cars</strong> course.', '2019-01-27 13:52:36'),
(8, 11, 'Completed a Theory class for <strong>Luxury Cars</strong> course.', '2019-01-27 14:00:25'),
(9, 14, 'Account created by <strong>John Doe</strong>', '2019-02-11 07:47:44'),
(10, 14, 'Enrolled to <strong>Sample File</strong> course.', '2019-03-28 14:13:25'),
(11, 11, 'Enrolled to <strong>Heavy Truck</strong> course.', '2019-06-16 14:44:00');

-- --------------------------------------------------------

--
-- Table structure for table `timezones`
--

CREATE TABLE `timezones` (
  `id` int(11) NOT NULL,
  `name` varchar(31) NOT NULL,
  `zone` varchar(272) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `timezones`
--

INSERT INTO `timezones` (`id`, `name`, `zone`) VALUES
(1, '(UTC-11:00) Pacific/Pago_Pago', 'Pacific/Pago_Pago'),
(2, '(UTC-11:00) Pacific/Niue', 'Pacific/Niue'),
(3, '(UTC-11:00) Pacific/Midway', 'Pacific/Midway'),
(4, '(UTC-10:00) Pacific/Tahiti', 'Pacific/Tahiti'),
(5, '(UTC-10:00) America/Adak', 'America/Adak'),
(6, '(UTC-10:00) Pacific/Rarotonga', 'Pacific/Rarotonga'),
(7, '(UTC-10:00) Pacific/Honolulu', 'Pacific/Honolulu'),
(8, '(UTC-09:30) Pacific/Marquesas', 'Pacific/Marquesas'),
(9, '(UTC-09:00) Pacific/Gambier', 'Pacific/Gambier'),
(10, '(UTC-09:00) America/Sitka', 'America/Sitka'),
(11, '(UTC-09:00) America/Anchorage', 'America/Anchorage'),
(12, '(UTC-09:00) America/Yakutat', 'America/Yakutat'),
(13, '(UTC-09:00) America/Metlakatla', 'America/Metlakatla'),
(14, '(UTC-09:00) America/Juneau', 'America/Juneau'),
(15, '(UTC-09:00) America/Nome', 'America/Nome'),
(16, '(UTC-08:00) Pacific/Pitcairn', 'Pacific/Pitcairn'),
(17, '(UTC-08:00) America/Tijuana', 'America/Tijuana'),
(18, '(UTC-08:00) America/Vancouver', 'America/Vancouver'),
(19, '(UTC-08:00) America/Los_Angeles', 'America/Los_Angeles'),
(20, '(UTC-08:00) America/Whitehorse', 'America/Whitehorse'),
(21, '(UTC-08:00) America/Dawson', 'America/Dawson'),
(22, '(UTC-07:00) America/Cambridge_B', 'America/Cambridge_Bay'),
(23, '(UTC-07:00) America/Mazatlan', 'America/Mazatlan'),
(24, '(UTC-07:00) America/Boise', 'America/Boise'),
(25, '(UTC-07:00) America/Creston', 'America/Creston'),
(26, '(UTC-07:00) America/Yellowknife', 'America/Yellowknife'),
(27, '(UTC-07:00) America/Phoenix', 'America/Phoenix'),
(28, '(UTC-07:00) America/Chihuahua', 'America/Chihuahua'),
(29, '(UTC-07:00) America/Dawson_Cree', 'America/Dawson_Creek'),
(30, '(UTC-07:00) America/Inuvik', 'America/Inuvik'),
(31, '(UTC-07:00) America/Ojinaga', 'America/Ojinaga'),
(32, '(UTC-07:00) America/Denver', 'America/Denver'),
(33, '(UTC-07:00) America/Edmonton', 'America/Edmonton'),
(34, '(UTC-07:00) America/Hermosillo', 'America/Hermosillo'),
(35, '(UTC-07:00) America/Fort_Nelson', 'America/Fort_Nelson'),
(36, '(UTC-06:00) America/El_Salvador', 'America/El_Salvador'),
(37, '(UTC-06:00) America/Indiana/Tel', 'America/Indiana/Tell_City'),
(38, '(UTC-06:00) America/Costa_Rica', 'America/Costa_Rica'),
(39, '(UTC-06:00) America/Indiana/Kno', 'America/Indiana/Knox'),
(40, '(UTC-06:00) America/Bahia_Bande', 'America/Bahia_Banderas'),
(41, '(UTC-06:00) America/Guatemala', 'America/Guatemala'),
(42, '(UTC-06:00) America/Belize', 'America/Belize'),
(43, '(UTC-06:00) America/Managua', 'America/Managua'),
(44, '(UTC-06:00) America/Swift_Curre', 'America/Swift_Current'),
(45, '(UTC-06:00) America/Mexico_City', 'America/Mexico_City'),
(46, '(UTC-06:00) America/Resolute', 'America/Resolute'),
(47, '(UTC-06:00) America/Regina', 'America/Regina'),
(48, '(UTC-06:00) America/Rankin_Inle', 'America/Rankin_Inlet'),
(49, '(UTC-06:00) America/Rainy_River', 'America/Rainy_River'),
(50, '(UTC-06:00) America/North_Dakot', 'America/North_Dakota/New_Salem'),
(51, '(UTC-06:00) America/North_Dakot', 'America/North_Dakota/Center'),
(52, '(UTC-06:00) America/North_Dakot', 'America/North_Dakota/Beulah'),
(53, '(UTC-06:00) America/Tegucigalpa', 'America/Tegucigalpa'),
(54, '(UTC-06:00) America/Monterrey', 'America/Monterrey'),
(55, '(UTC-06:00) Pacific/Galapagos', 'Pacific/Galapagos'),
(56, '(UTC-06:00) America/Chicago', 'America/Chicago'),
(57, '(UTC-06:00) America/Merida', 'America/Merida'),
(58, '(UTC-06:00) America/Winnipeg', 'America/Winnipeg'),
(59, '(UTC-06:00) America/Menominee', 'America/Menominee'),
(60, '(UTC-06:00) America/Matamoros', 'America/Matamoros'),
(61, '(UTC-05:00) America/Iqaluit', 'America/Iqaluit'),
(62, '(UTC-05:00) America/Rio_Branco', 'America/Rio_Branco'),
(63, '(UTC-05:00) America/Lima', 'America/Lima'),
(64, '(UTC-05:00) America/Kentucky/Mo', 'America/Kentucky/Monticello'),
(65, '(UTC-05:00) America/Kentucky/Lo', 'America/Kentucky/Louisville'),
(66, '(UTC-05:00) America/Cayman', 'America/Cayman'),
(67, '(UTC-05:00) America/Pangnirtung', 'America/Pangnirtung'),
(68, '(UTC-05:00) America/Panama', 'America/Panama'),
(69, '(UTC-05:00) America/Jamaica', 'America/Jamaica'),
(70, '(UTC-05:00) America/Detroit', 'America/Detroit'),
(71, '(UTC-05:00) America/Indiana/Win', 'America/Indiana/Winamac'),
(72, '(UTC-05:00) America/Eirunepe', 'America/Eirunepe'),
(73, '(UTC-05:00) America/Indiana/Vin', 'America/Indiana/Vincennes'),
(74, '(UTC-05:00) America/New_York', 'America/New_York'),
(75, '(UTC-05:00) America/Grand_Turk', 'America/Grand_Turk'),
(76, '(UTC-05:00) America/Nassau', 'America/Nassau'),
(77, '(UTC-05:00) America/Guayaquil', 'America/Guayaquil'),
(78, '(UTC-05:00) America/Havana', 'America/Havana'),
(79, '(UTC-05:00) America/Indiana/Ind', 'America/Indiana/Indianapolis'),
(80, '(UTC-05:00) America/Indiana/Mar', 'America/Indiana/Marengo'),
(81, '(UTC-05:00) America/Indiana/Pet', 'America/Indiana/Petersburg'),
(82, '(UTC-05:00) America/Indiana/Vev', 'America/Indiana/Vevay'),
(83, '(UTC-05:00) America/Nipigon', 'America/Nipigon'),
(84, '(UTC-05:00) America/Port-au-Pri', 'America/Port-au-Prince'),
(85, '(UTC-05:00) America/Thunder_Bay', 'America/Thunder_Bay'),
(86, '(UTC-05:00) America/Cancun', 'America/Cancun'),
(87, '(UTC-05:00) America/Bogota', 'America/Bogota'),
(88, '(UTC-05:00) Pacific/Easter', 'Pacific/Easter'),
(89, '(UTC-05:00) America/Toronto', 'America/Toronto'),
(90, '(UTC-05:00) America/Atikokan', 'America/Atikokan'),
(91, '(UTC-04:00) America/Marigot', 'America/Marigot'),
(92, '(UTC-04:00) America/St_Barthele', 'America/St_Barthelemy'),
(93, '(UTC-04:00) America/St_Kitts', 'America/St_Kitts'),
(94, '(UTC-04:00) America/St_Lucia', 'America/St_Lucia'),
(95, '(UTC-04:00) America/La_Paz', 'America/La_Paz'),
(96, '(UTC-04:00) America/St_Thomas', 'America/St_Thomas'),
(97, '(UTC-04:00) America/St_Vincent', 'America/St_Vincent'),
(98, '(UTC-04:00) America/Lower_Princ', 'America/Lower_Princes'),
(99, '(UTC-04:00) America/Thule', 'America/Thule'),
(100, '(UTC-04:00) America/Manaus', 'America/Manaus'),
(101, '(UTC-04:00) America/Caracas', 'America/Caracas'),
(102, '(UTC-04:00) America/Martinique', 'America/Martinique'),
(103, '(UTC-04:00) America/Antigua', 'America/Antigua'),
(104, '(UTC-04:00) America/Tortola', 'America/Tortola'),
(105, '(UTC-04:00) America/Moncton', 'America/Moncton'),
(106, '(UTC-04:00) America/Montserrat', 'America/Montserrat'),
(107, '(UTC-04:00) Atlantic/Bermuda', 'Atlantic/Bermuda'),
(108, '(UTC-04:00) America/Santo_Domin', 'America/Santo_Domingo'),
(109, '(UTC-04:00) America/Port_of_Spa', 'America/Port_of_Spain'),
(110, '(UTC-04:00) America/Porto_Velho', 'America/Porto_Velho'),
(111, '(UTC-04:00) America/Puerto_Rico', 'America/Puerto_Rico'),
(112, '(UTC-04:00) America/Anguilla', 'America/Anguilla'),
(113, '(UTC-04:00) America/Kralendijk', 'America/Kralendijk'),
(114, '(UTC-04:00) America/Halifax', 'America/Halifax'),
(115, '(UTC-04:00) America/Curacao', 'America/Curacao'),
(116, '(UTC-04:00) America/Barbados', 'America/Barbados'),
(117, '(UTC-04:00) America/Glace_Bay', 'America/Glace_Bay'),
(118, '(UTC-04:00) America/Goose_Bay', 'America/Goose_Bay'),
(119, '(UTC-04:00) America/Grenada', 'America/Grenada'),
(120, '(UTC-04:00) America/Guadeloupe', 'America/Guadeloupe'),
(121, '(UTC-04:00) America/Dominica', 'America/Dominica'),
(122, '(UTC-04:00) America/Blanc-Sablo', 'America/Blanc-Sablon'),
(123, '(UTC-04:00) America/Aruba', 'America/Aruba'),
(124, '(UTC-04:00) America/Guyana', 'America/Guyana'),
(125, '(UTC-04:00) America/Boa_Vista', 'America/Boa_Vista'),
(126, '(UTC-03:30) America/St_Johns', 'America/St_Johns'),
(127, '(UTC-03:00) America/Paramaribo', 'America/Paramaribo'),
(128, '(UTC-03:00) Atlantic/Stanley', 'Atlantic/Stanley'),
(129, '(UTC-03:00) America/Cuiaba', 'America/Cuiaba'),
(130, '(UTC-03:00) America/Santiago', 'America/Santiago'),
(131, '(UTC-03:00) America/Belem', 'America/Belem'),
(132, '(UTC-03:00) America/Miquelon', 'America/Miquelon'),
(133, '(UTC-03:00) America/Campo_Grand', 'America/Campo_Grande'),
(134, '(UTC-03:00) America/Argentina/S', 'America/Argentina/Salta'),
(135, '(UTC-03:00) America/Punta_Arena', 'America/Punta_Arenas'),
(136, '(UTC-03:00) Antarctica/Palmer', 'Antarctica/Palmer'),
(137, '(UTC-03:00) America/Recife', 'America/Recife'),
(138, '(UTC-03:00) America/Bahia', 'America/Bahia'),
(139, '(UTC-03:00) America/Montevideo', 'America/Montevideo'),
(140, '(UTC-03:00) Antarctica/Rothera', 'Antarctica/Rothera'),
(141, '(UTC-03:00) America/Asuncion', 'America/Asuncion'),
(142, '(UTC-03:00) America/Argentina/S', 'America/Argentina/San_Juan'),
(143, '(UTC-03:00) America/Argentina/R', 'America/Argentina/Rio_Gallegos'),
(144, '(UTC-03:00) America/Argentina/M', 'America/Argentina/Mendoza'),
(145, '(UTC-03:00) America/Argentina/L', 'America/Argentina/La_Rioja'),
(146, '(UTC-03:00) America/Argentina/J', 'America/Argentina/Jujuy'),
(147, '(UTC-03:00) America/Argentina/C', 'America/Argentina/Cordoba'),
(148, '(UTC-03:00) America/Argentina/C', 'America/Argentina/Catamarca'),
(149, '(UTC-03:00) America/Argentina/B', 'America/Argentina/Buenos_Aires'),
(150, '(UTC-03:00) America/Araguaina', 'America/Araguaina'),
(151, '(UTC-03:00) America/Argentina/U', 'America/Argentina/Ushuaia'),
(152, '(UTC-03:00) America/Santarem', 'America/Santarem'),
(153, '(UTC-03:00) America/Cayenne', 'America/Cayenne'),
(154, '(UTC-03:00) America/Argentina/S', 'America/Argentina/San_Luis'),
(155, '(UTC-03:00) America/Fortaleza', 'America/Fortaleza'),
(156, '(UTC-03:00) America/Maceio', 'America/Maceio'),
(157, '(UTC-03:00) America/Godthab', 'America/Godthab'),
(158, '(UTC-03:00) America/Argentina/T', 'America/Argentina/Tucuman'),
(159, '(UTC-02:00) America/Noronha', 'America/Noronha'),
(160, '(UTC-02:00) America/Sao_Paulo', 'America/Sao_Paulo'),
(161, '(UTC-02:00) Atlantic/South_Geor', 'Atlantic/South_Georgia'),
(162, '(UTC-01:00) Atlantic/Azores', 'Atlantic/Azores'),
(163, '(UTC-01:00) Atlantic/Cape_Verde', 'Atlantic/Cape_Verde'),
(164, '(UTC-01:00) America/Scoresbysun', 'America/Scoresbysund'),
(165, '(UTC+00:00) Atlantic/St_Helena', 'Atlantic/St_Helena'),
(166, '(UTC+00:00) Africa/Accra', 'Africa/Accra'),
(167, '(UTC+00:00) Atlantic/Reykjavik', 'Atlantic/Reykjavik'),
(168, '(UTC+00:00) Antarctica/Troll', 'Antarctica/Troll'),
(169, '(UTC+00:00) Atlantic/Faroe', 'Atlantic/Faroe'),
(170, '(UTC+00:00) Europe/London', 'Europe/London'),
(171, '(UTC+00:00) Europe/Lisbon', 'Europe/Lisbon'),
(172, '(UTC+00:00) Atlantic/Canary', 'Atlantic/Canary'),
(173, '(UTC+00:00) Europe/Jersey', 'Europe/Jersey'),
(174, '(UTC+00:00) Europe/Isle_of_Man', 'Europe/Isle_of_Man'),
(175, '(UTC+00:00) Europe/Guernsey', 'Europe/Guernsey'),
(176, '(UTC+00:00) Atlantic/Madeira', 'Atlantic/Madeira'),
(177, '(UTC+00:00) Africa/Abidjan', 'Africa/Abidjan'),
(178, '(UTC+00:00) Europe/Dublin', 'Europe/Dublin'),
(179, '(UTC+00:00) Africa/Monrovia', 'Africa/Monrovia'),
(180, '(UTC+00:00) America/Danmarkshav', 'America/Danmarkshavn'),
(181, '(UTC+00:00) Africa/El_Aaiun', 'Africa/El_Aaiun'),
(182, '(UTC+00:00) Africa/Freetown', 'Africa/Freetown'),
(183, '(UTC+00:00) Africa/Dakar', 'Africa/Dakar'),
(184, '(UTC+00:00) Africa/Conakry', 'Africa/Conakry'),
(185, '(UTC+00:00) Africa/Bissau', 'Africa/Bissau'),
(186, '(UTC+00:00) Africa/Lome', 'Africa/Lome'),
(187, '(UTC+00:00) Africa/Banjul', 'Africa/Banjul'),
(188, '(UTC+00:00) Africa/Bamako', 'Africa/Bamako'),
(189, '(UTC+00:00) Africa/Casablanca', 'Africa/Casablanca'),
(190, '(UTC+00:00) Africa/Nouakchott', 'Africa/Nouakchott'),
(191, '(UTC+00:00) Africa/Ouagadougou', 'Africa/Ouagadougou'),
(192, '(UTC+00:00) Africa/Sao_Tome', 'Africa/Sao_Tome'),
(193, '(UTC+01:00) Europe/Rome', 'Europe/Rome'),
(194, '(UTC+01:00) Europe/Budapest', 'Europe/Budapest'),
(195, '(UTC+01:00) Europe/San_Marino', 'Europe/San_Marino'),
(196, '(UTC+01:00) Europe/Sarajevo', 'Europe/Sarajevo'),
(197, '(UTC+01:00) Europe/Skopje', 'Europe/Skopje'),
(198, '(UTC+01:00) Europe/Stockholm', 'Europe/Stockholm'),
(199, '(UTC+01:00) Europe/Belgrade', 'Europe/Belgrade'),
(200, '(UTC+01:00) Europe/Podgorica', 'Europe/Podgorica'),
(201, '(UTC+01:00) Europe/Tirane', 'Europe/Tirane'),
(202, '(UTC+01:00) Europe/Vaduz', 'Europe/Vaduz'),
(203, '(UTC+01:00) Europe/Vatican', 'Europe/Vatican'),
(204, '(UTC+01:00) Europe/Busingen', 'Europe/Busingen'),
(205, '(UTC+01:00) Europe/Vienna', 'Europe/Vienna'),
(206, '(UTC+01:00) Europe/Copenhagen', 'Europe/Copenhagen'),
(207, '(UTC+01:00) Europe/Warsaw', 'Europe/Warsaw'),
(208, '(UTC+01:00) Europe/Prague', 'Europe/Prague'),
(209, '(UTC+01:00) Europe/Monaco', 'Europe/Monaco'),
(210, '(UTC+01:00) Europe/Paris', 'Europe/Paris'),
(211, '(UTC+01:00) Europe/Bratislava', 'Europe/Bratislava'),
(212, '(UTC+01:00) Europe/Amsterdam', 'Europe/Amsterdam'),
(213, '(UTC+01:00) Africa/Algiers', 'Africa/Algiers'),
(214, '(UTC+01:00) Europe/Berlin', 'Europe/Berlin'),
(215, '(UTC+01:00) Europe/Ljubljana', 'Europe/Ljubljana'),
(216, '(UTC+01:00) Africa/Bangui', 'Africa/Bangui'),
(217, '(UTC+01:00) Europe/Luxembourg', 'Europe/Luxembourg'),
(218, '(UTC+01:00) Africa/Brazzaville', 'Africa/Brazzaville'),
(219, '(UTC+01:00) Europe/Oslo', 'Europe/Oslo'),
(220, '(UTC+01:00) Europe/Zurich', 'Europe/Zurich'),
(221, '(UTC+01:00) Africa/Ceuta', 'Africa/Ceuta'),
(222, '(UTC+01:00) Europe/Brussels', 'Europe/Brussels'),
(223, '(UTC+01:00) Europe/Madrid', 'Europe/Madrid'),
(224, '(UTC+01:00) Europe/Malta', 'Europe/Malta'),
(225, '(UTC+01:00) Europe/Andorra', 'Europe/Andorra'),
(226, '(UTC+01:00) Europe/Zagreb', 'Europe/Zagreb'),
(227, '(UTC+01:00) Europe/Gibraltar', 'Europe/Gibraltar'),
(228, '(UTC+01:00) Africa/Ndjamena', 'Africa/Ndjamena'),
(229, '(UTC+01:00) Africa/Libreville', 'Africa/Libreville'),
(230, '(UTC+01:00) Africa/Malabo', 'Africa/Malabo'),
(231, '(UTC+01:00) Africa/Tunis', 'Africa/Tunis'),
(232, '(UTC+01:00) Africa/Kinshasa', 'Africa/Kinshasa'),
(233, '(UTC+01:00) Africa/Luanda', 'Africa/Luanda'),
(234, '(UTC+01:00) Africa/Porto-Novo', 'Africa/Porto-Novo'),
(235, '(UTC+01:00) Africa/Niamey', 'Africa/Niamey'),
(236, '(UTC+01:00) Africa/Douala', 'Africa/Douala'),
(237, '(UTC+01:00) Africa/Lagos', 'Africa/Lagos'),
(238, '(UTC+02:00) Africa/Maputo', 'Africa/Maputo'),
(239, '(UTC+02:00) Asia/Nicosia', 'Asia/Nicosia'),
(240, '(UTC+02:00) Africa/Lusaka', 'Africa/Lusaka'),
(241, '(UTC+02:00) Europe/Tallinn', 'Europe/Tallinn'),
(242, '(UTC+02:00) Africa/Lubumbashi', 'Africa/Lubumbashi'),
(243, '(UTC+02:00) Europe/Sofia', 'Europe/Sofia'),
(244, '(UTC+02:00) Europe/Vilnius', 'Europe/Vilnius'),
(245, '(UTC+02:00) Africa/Blantyre', 'Africa/Blantyre'),
(246, '(UTC+02:00) Africa/Bujumbura', 'Africa/Bujumbura'),
(247, '(UTC+02:00) Africa/Cairo', 'Africa/Cairo'),
(248, '(UTC+02:00) Africa/Kigali', 'Africa/Kigali'),
(249, '(UTC+02:00) Africa/Khartoum', 'Africa/Khartoum'),
(250, '(UTC+02:00) Asia/Amman', 'Asia/Amman'),
(251, '(UTC+02:00) Europe/Riga', 'Europe/Riga'),
(252, '(UTC+02:00) Europe/Mariehamn', 'Europe/Mariehamn'),
(253, '(UTC+02:00) Africa/Gaborone', 'Africa/Gaborone'),
(254, '(UTC+02:00) Europe/Uzhgorod', 'Europe/Uzhgorod'),
(255, '(UTC+02:00) Europe/Kiev', 'Europe/Kiev'),
(256, '(UTC+02:00) Africa/Johannesburg', 'Africa/Johannesburg'),
(257, '(UTC+02:00) Asia/Jerusalem', 'Asia/Jerusalem'),
(258, '(UTC+02:00) Asia/Damascus', 'Asia/Damascus'),
(259, '(UTC+02:00) Africa/Windhoek', 'Africa/Windhoek'),
(260, '(UTC+02:00) Europe/Chisinau', 'Europe/Chisinau'),
(261, '(UTC+02:00) Africa/Tripoli', 'Africa/Tripoli'),
(262, '(UTC+02:00) Asia/Famagusta', 'Asia/Famagusta'),
(263, '(UTC+02:00) Asia/Gaza', 'Asia/Gaza'),
(264, '(UTC+02:00) Asia/Hebron', 'Asia/Hebron'),
(265, '(UTC+02:00) Europe/Bucharest', 'Europe/Bucharest'),
(266, '(UTC+02:00) Europe/Athens', 'Europe/Athens'),
(267, '(UTC+02:00) Africa/Harare', 'Africa/Harare'),
(268, '(UTC+02:00) Europe/Zaporozhye', 'Europe/Zaporozhye'),
(269, '(UTC+02:00) Africa/Mbabane', 'Africa/Mbabane'),
(270, '(UTC+02:00) Europe/Helsinki', 'Europe/Helsinki'),
(271, '(UTC+02:00) Africa/Maseru', 'Africa/Maseru'),
(272, '(UTC+02:00) Asia/Beirut', 'Asia/Beirut'),
(273, '(UTC+02:00) Europe/Kaliningrad', 'Europe/Kaliningrad'),
(274, '(UTC+03:00) Africa/Mogadishu', 'Africa/Mogadishu'),
(275, '(UTC+03:00) Europe/Kirov', 'Europe/Kirov'),
(276, '(UTC+03:00) Africa/Addis_Ababa', 'Africa/Addis_Ababa'),
(277, '(UTC+03:00) Africa/Kampala', 'Africa/Kampala'),
(278, '(UTC+03:00) Europe/Istanbul', 'Europe/Istanbul'),
(279, '(UTC+03:00) Africa/Asmara', 'Africa/Asmara'),
(280, '(UTC+03:00) Africa/Juba', 'Africa/Juba'),
(281, '(UTC+03:00) Europe/Minsk', 'Europe/Minsk'),
(282, '(UTC+03:00) Antarctica/Syowa', 'Antarctica/Syowa'),
(283, '(UTC+03:00) Africa/Nairobi', 'Africa/Nairobi'),
(284, '(UTC+03:00) Indian/Mayotte', 'Indian/Mayotte'),
(285, '(UTC+03:00) Europe/Moscow', 'Europe/Moscow'),
(286, '(UTC+03:00) Asia/Riyadh', 'Asia/Riyadh'),
(287, '(UTC+03:00) Indian/Comoro', 'Indian/Comoro'),
(288, '(UTC+03:00) Indian/Antananarivo', 'Indian/Antananarivo'),
(289, '(UTC+03:00) Africa/Dar_es_Salaa', 'Africa/Dar_es_Salaam'),
(290, '(UTC+03:00) Africa/Djibouti', 'Africa/Djibouti'),
(291, '(UTC+03:00) Europe/Volgograd', 'Europe/Volgograd'),
(292, '(UTC+03:00) Asia/Kuwait', 'Asia/Kuwait'),
(293, '(UTC+03:00) Asia/Aden', 'Asia/Aden'),
(294, '(UTC+03:00) Asia/Baghdad', 'Asia/Baghdad'),
(295, '(UTC+03:00) Asia/Qatar', 'Asia/Qatar'),
(296, '(UTC+03:00) Europe/Simferopol', 'Europe/Simferopol'),
(297, '(UTC+03:00) Asia/Bahrain', 'Asia/Bahrain'),
(298, '(UTC+03:30) Asia/Tehran', 'Asia/Tehran'),
(299, '(UTC+04:00) Europe/Saratov', 'Europe/Saratov'),
(300, '(UTC+04:00) Asia/Baku', 'Asia/Baku'),
(301, '(UTC+04:00) Indian/Reunion', 'Indian/Reunion'),
(302, '(UTC+04:00) Asia/Tbilisi', 'Asia/Tbilisi'),
(303, '(UTC+04:00) Europe/Samara', 'Europe/Samara'),
(304, '(UTC+04:00) Asia/Yerevan', 'Asia/Yerevan'),
(305, '(UTC+04:00) Asia/Muscat', 'Asia/Muscat'),
(306, '(UTC+04:00) Europe/Ulyanovsk', 'Europe/Ulyanovsk'),
(307, '(UTC+04:00) Indian/Mahe', 'Indian/Mahe'),
(308, '(UTC+04:00) Asia/Dubai', 'Asia/Dubai'),
(309, '(UTC+04:00) Indian/Mauritius', 'Indian/Mauritius'),
(310, '(UTC+04:00) Europe/Astrakhan', 'Europe/Astrakhan'),
(311, '(UTC+04:30) Asia/Kabul', 'Asia/Kabul'),
(312, '(UTC+05:00) Indian/Kerguelen', 'Indian/Kerguelen'),
(313, '(UTC+05:00) Asia/Dushanbe', 'Asia/Dushanbe'),
(314, '(UTC+05:00) Indian/Maldives', 'Indian/Maldives'),
(315, '(UTC+05:00) Asia/Tashkent', 'Asia/Tashkent'),
(316, '(UTC+05:00) Asia/Karachi', 'Asia/Karachi'),
(317, '(UTC+05:00) Asia/Samarkand', 'Asia/Samarkand'),
(318, '(UTC+05:00) Asia/Yekaterinburg', 'Asia/Yekaterinburg'),
(319, '(UTC+05:00) Asia/Aqtau', 'Asia/Aqtau'),
(320, '(UTC+05:00) Antarctica/Mawson', 'Antarctica/Mawson'),
(321, '(UTC+05:00) Asia/Oral', 'Asia/Oral'),
(322, '(UTC+05:00) Asia/Atyrau', 'Asia/Atyrau'),
(323, '(UTC+05:00) Asia/Ashgabat', 'Asia/Ashgabat'),
(324, '(UTC+05:00) Asia/Aqtobe', 'Asia/Aqtobe'),
(325, '(UTC+05:30) Asia/Kolkata', 'Asia/Kolkata'),
(326, '(UTC+05:30) Asia/Colombo', 'Asia/Colombo'),
(327, '(UTC+05:45) Asia/Kathmandu', 'Asia/Kathmandu'),
(328, '(UTC+06:00) Indian/Chagos', 'Indian/Chagos'),
(329, '(UTC+06:00) Asia/Almaty', 'Asia/Almaty'),
(330, '(UTC+06:00) Asia/Urumqi', 'Asia/Urumqi'),
(331, '(UTC+06:00) Asia/Bishkek', 'Asia/Bishkek'),
(332, '(UTC+06:00) Asia/Qyzylorda', 'Asia/Qyzylorda'),
(333, '(UTC+06:00) Antarctica/Vostok', 'Antarctica/Vostok'),
(334, '(UTC+06:00) Asia/Dhaka', 'Asia/Dhaka'),
(335, '(UTC+06:00) Asia/Omsk', 'Asia/Omsk'),
(336, '(UTC+06:00) Asia/Thimphu', 'Asia/Thimphu'),
(337, '(UTC+06:30) Indian/Cocos', 'Indian/Cocos'),
(338, '(UTC+06:30) Asia/Yangon', 'Asia/Yangon'),
(339, '(UTC+07:00) Asia/Pontianak', 'Asia/Pontianak'),
(340, '(UTC+07:00) Asia/Phnom_Penh', 'Asia/Phnom_Penh'),
(341, '(UTC+07:00) Indian/Christmas', 'Indian/Christmas'),
(342, '(UTC+07:00) Asia/Novokuznetsk', 'Asia/Novokuznetsk'),
(343, '(UTC+07:00) Asia/Jakarta', 'Asia/Jakarta'),
(344, '(UTC+07:00) Asia/Hovd', 'Asia/Hovd'),
(345, '(UTC+07:00) Asia/Ho_Chi_Minh', 'Asia/Ho_Chi_Minh'),
(346, '(UTC+07:00) Asia/Bangkok', 'Asia/Bangkok'),
(347, '(UTC+07:00) Asia/Krasnoyarsk', 'Asia/Krasnoyarsk'),
(348, '(UTC+07:00) Asia/Novosibirsk', 'Asia/Novosibirsk'),
(349, '(UTC+07:00) Asia/Tomsk', 'Asia/Tomsk'),
(350, '(UTC+07:00) Asia/Vientiane', 'Asia/Vientiane'),
(351, '(UTC+07:00) Antarctica/Davis', 'Antarctica/Davis'),
(352, '(UTC+07:00) Asia/Barnaul', 'Asia/Barnaul'),
(353, '(UTC+08:00) Asia/Irkutsk', 'Asia/Irkutsk'),
(354, '(UTC+08:00) Asia/Hong_Kong', 'Asia/Hong_Kong'),
(355, '(UTC+08:00) Asia/Kuala_Lumpur', 'Asia/Kuala_Lumpur'),
(356, '(UTC+08:00) Asia/Kuching', 'Asia/Kuching'),
(357, '(UTC+08:00) Asia/Macau', 'Asia/Macau'),
(358, '(UTC+08:00) Australia/Perth', 'Australia/Perth'),
(359, '(UTC+08:00) Asia/Makassar', 'Asia/Makassar'),
(360, '(UTC+08:00) Asia/Manila', 'Asia/Manila'),
(361, '(UTC+08:00) Asia/Ulaanbaatar', 'Asia/Ulaanbaatar'),
(362, '(UTC+08:00) Asia/Singapore', 'Asia/Singapore'),
(363, '(UTC+08:00) Asia/Taipei', 'Asia/Taipei'),
(364, '(UTC+08:00) Asia/Choibalsan', 'Asia/Choibalsan'),
(365, '(UTC+08:00) Asia/Brunei', 'Asia/Brunei'),
(366, '(UTC+08:00) Asia/Shanghai', 'Asia/Shanghai'),
(367, '(UTC+08:30) Asia/Pyongyang', 'Asia/Pyongyang'),
(368, '(UTC+08:45) Australia/Eucla', 'Australia/Eucla'),
(369, '(UTC+09:00) Asia/Dili', 'Asia/Dili'),
(370, '(UTC+09:00) Asia/Chita', 'Asia/Chita'),
(371, '(UTC+09:00) Asia/Khandyga', 'Asia/Khandyga'),
(372, '(UTC+09:00) Asia/Jayapura', 'Asia/Jayapura'),
(373, '(UTC+09:00) Asia/Seoul', 'Asia/Seoul'),
(374, '(UTC+09:00) Pacific/Palau', 'Pacific/Palau'),
(375, '(UTC+09:00) Asia/Tokyo', 'Asia/Tokyo'),
(376, '(UTC+09:00) Asia/Yakutsk', 'Asia/Yakutsk'),
(377, '(UTC+09:30) Australia/Darwin', 'Australia/Darwin'),
(378, '(UTC+10:00) Asia/Ust-Nera', 'Asia/Ust-Nera'),
(379, '(UTC+10:00) Pacific/Saipan', 'Pacific/Saipan'),
(380, '(UTC+10:00) Pacific/Guam', 'Pacific/Guam'),
(381, '(UTC+10:00) Antarctica/DumontDU', 'Antarctica/DumontDUrville'),
(382, '(UTC+10:00) Asia/Vladivostok', 'Asia/Vladivostok'),
(383, '(UTC+10:00) Australia/Lindeman', 'Australia/Lindeman'),
(384, '(UTC+10:00) Australia/Brisbane', 'Australia/Brisbane'),
(385, '(UTC+10:00) Pacific/Port_Moresb', 'Pacific/Port_Moresby'),
(386, '(UTC+10:00) Pacific/Chuuk', 'Pacific/Chuuk'),
(387, '(UTC+10:30) Australia/Adelaide', 'Australia/Adelaide'),
(388, '(UTC+10:30) Australia/Broken_Hi', 'Australia/Broken_Hill'),
(389, '(UTC+11:00) Pacific/Guadalcanal', 'Pacific/Guadalcanal'),
(390, '(UTC+11:00) Antarctica/Casey', 'Antarctica/Casey'),
(391, '(UTC+11:00) Antarctica/Macquari', 'Antarctica/Macquarie'),
(392, '(UTC+11:00) Pacific/Kosrae', 'Pacific/Kosrae'),
(393, '(UTC+11:00) Pacific/Norfolk', 'Pacific/Norfolk'),
(394, '(UTC+11:00) Pacific/Noumea', 'Pacific/Noumea'),
(395, '(UTC+11:00) Pacific/Pohnpei', 'Pacific/Pohnpei'),
(396, '(UTC+11:00) Australia/Sydney', 'Australia/Sydney'),
(397, '(UTC+11:00) Pacific/Efate', 'Pacific/Efate'),
(398, '(UTC+11:00) Australia/Melbourne', 'Australia/Melbourne'),
(399, '(UTC+11:00) Australia/Lord_Howe', 'Australia/Lord_Howe'),
(400, '(UTC+11:00) Australia/Hobart', 'Australia/Hobart'),
(401, '(UTC+11:00) Australia/Currie', 'Australia/Currie'),
(402, '(UTC+11:00) Asia/Srednekolymsk', 'Asia/Srednekolymsk'),
(403, '(UTC+11:00) Pacific/Bougainvill', 'Pacific/Bougainville'),
(404, '(UTC+11:00) Asia/Sakhalin', 'Asia/Sakhalin'),
(405, '(UTC+11:00) Asia/Magadan', 'Asia/Magadan'),
(406, '(UTC+12:00) Pacific/Funafuti', 'Pacific/Funafuti'),
(407, '(UTC+12:00) Asia/Kamchatka', 'Asia/Kamchatka'),
(408, '(UTC+12:00) Pacific/Wake', 'Pacific/Wake'),
(409, '(UTC+12:00) Pacific/Tarawa', 'Pacific/Tarawa'),
(410, '(UTC+12:00) Pacific/Wallis', 'Pacific/Wallis'),
(411, '(UTC+12:00) Pacific/Fiji', 'Pacific/Fiji'),
(412, '(UTC+12:00) Pacific/Nauru', 'Pacific/Nauru'),
(413, '(UTC+12:00) Asia/Anadyr', 'Asia/Anadyr'),
(414, '(UTC+12:00) Pacific/Majuro', 'Pacific/Majuro'),
(415, '(UTC+12:00) Pacific/Kwajalein', 'Pacific/Kwajalein'),
(416, '(UTC+13:00) Antarctica/McMurdo', 'Antarctica/McMurdo'),
(417, '(UTC+13:00) Pacific/Enderbury', 'Pacific/Enderbury'),
(418, '(UTC+13:00) Pacific/Tongatapu', 'Pacific/Tongatapu'),
(419, '(UTC+13:00) Pacific/Fakaofo', 'Pacific/Fakaofo'),
(420, '(UTC+13:00) Pacific/Auckland', 'Pacific/Auckland'),
(421, '(UTC+13:45) Pacific/Chatham', 'Pacific/Chatham'),
(422, '(UTC+14:00) Pacific/Apia', 'Pacific/Apia'),
(423, '(UTC+14:00) Pacific/Kiritimati', 'Pacific/Kiritimati');

-- --------------------------------------------------------

--
-- Table structure for table `usermessages`
--

CREATE TABLE `usermessages` (
  `id` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `type` enum('sms','email') NOT NULL,
  `contact` varchar(64) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` longtext,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Sent','Failed') NOT NULL DEFAULT 'Sent',
  `school` int(11) NOT NULL,
  `branch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usermessages`
--

INSERT INTO `usermessages` (`id`, `receiver`, `type`, `contact`, `subject`, `message`, `sent_at`, `status`, `school`, `branch`) VALUES
(1, 1, 'sms', '+254720783834', NULL, 'hello there?', '2019-01-27 13:47:55', 'Sent', 1, 1),
(2, 1, 'email', 'demo@simcycreative.com', 'Monitoring Request', 'Please come for your exam evaluation next week ', '2019-01-27 13:48:27', 'Sent', 1, 1),
(3, 11, 'sms', '12345678', NULL, 'test', '2019-04-16 11:58:10', 'Sent', 1, 1),
(4, 12, 'sms', '12345678', NULL, 'test', '2019-04-16 11:58:10', 'Sent', 1, 1),
(5, 13, 'sms', '12345678', NULL, 'test', '2019-04-16 11:58:10', 'Sent', 1, 1),
(6, 14, 'sms', '123456789', NULL, 'test', '2019-04-16 11:58:10', 'Sent', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fname` varchar(16) NOT NULL,
  `lname` varchar(16) NOT NULL,
  `email` varchar(64) NOT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `phone` varchar(16) DEFAULT NULL,
  `address` varchar(256) DEFAULT NULL,
  `password` varchar(256) NOT NULL,
  `avatar` varchar(256) DEFAULT NULL,
  `token` varchar(256) DEFAULT NULL,
  `school` int(11) DEFAULT NULL,
  `branch` int(11) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff','superadmin','instructor','student') NOT NULL,
  `position` varchar(40) DEFAULT NULL,
  `permissions` varchar(100) DEFAULT NULL,
  `status` enum('Active','Suspended','Inactive') NOT NULL DEFAULT 'Active',
  `lastnotification` datetime NOT NULL,
  `lang` varchar(32) NOT NULL DEFAULT 'en_US',
  `google_access_token` varchar(600) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `gender`, `date_of_birth`, `phone`, `address`, `password`, `avatar`, `token`, `school`, `branch`, `course`, `role`, `position`, `permissions`, `status`, `lastnotification`, `lang`, `google_access_token`, `created_at`) VALUES
(1, 'John', 'Doe', 'demo@simcycreative.com', 'Male', '1995-09-29', '+254720783834', 'Nairobi, Kenya.', '4b77d2bd1a52aaaaf118084aa8bd4e3d6128d98a35a89acb48bd416918efbb6d', NULL, '', 1, 1, NULL, 'superadmin', NULL, NULL, 'Active', '2019-06-17 17:06:37', 'en_US', NULL, '2019-01-26 06:04:26'),
(2, 'Marry', 'Jane', 'school@example.com', NULL, '0000-00-00', NULL, NULL, '4b77d2bd1a52aaaaf118084aa8bd4e3d6128d98a35a89acb48bd416918efbb6d', NULL, NULL, 2, 2, NULL, 'admin', NULL, NULL, 'Active', '0000-00-00 00:00:00', 'en_US', NULL, '2019-01-27 12:59:54'),
(3, 'Peter', 'Njoroge', 'wings@example.com', NULL, '0000-00-00', '+731702568', NULL, '6ac48a2afe478c7bc288477fcabcaa9aeea1dc48ac2a5e12452bf7d7b0bea841', NULL, NULL, 3, 3, NULL, 'admin', NULL, NULL, 'Active', '0000-00-00 00:00:00', 'en_US', NULL, '2019-01-27 13:01:38'),
(4, 'Jane', 'Doe', 'sample@example.com', NULL, '0000-00-00', '123456789', NULL, 'f6be45706d100fb54d31c0418f93bb6145524f08a71739c0aee65f2c55acc6f8', NULL, NULL, 4, 4, NULL, 'admin', NULL, NULL, 'Active', '0000-00-00 00:00:00', 'en_US', NULL, '2019-01-27 13:02:38'),
(5, 'Peter', 'Cage', 'instructor@example.com', 'Male', '0000-00-00', '123456789', NULL, '9f3db9cd477aacd435011dce6a0ea0a8267ebc0c50431ac41031362883785db6', NULL, NULL, 1, 1, NULL, 'instructor', NULL, NULL, 'Active', '0000-00-00 00:00:00', 'en_US', NULL, '2019-01-27 13:16:09'),
(6, 'Daisy', 'Queen', 'queen@example.com', 'Female', '0000-00-00', '123456789', NULL, '4b77d2bd1a52aaaaf118084aa8bd4e3d6128d98a35a89acb48bd416918efbb6d', NULL, '', 1, 1, NULL, 'instructor', NULL, NULL, 'Active', '0000-00-00 00:00:00', 'en_US', 'ya29.GlsqB2EZKfGXvVOH1_zFuuA_2KbmMielXarn1A5EGQGYEnL9Wv56eFz2YY1Pn2MOyJgNTcAJFFGh6A6Zdx9A2KcudvPleXRLeGf_eU1_qsdDSn0YB6QJs6kA0dfK', '2019-01-27 13:16:45'),
(7, 'Marry', 'Gomez', 'marry@example.com', 'Female', '0000-00-00', '12345678', NULL, '3a15231438caa842b8415bd29c985927d4b57bbd01acb774764709975c018896', NULL, NULL, 1, 1, NULL, 'instructor', NULL, NULL, 'Active', '0000-00-00 00:00:00', 'en_US', NULL, '2019-01-27 13:17:15'),
(8, 'selena', 'Gomez', 'staff@example.com', 'Female', '2019-01-23', '12345678', 'Nairobi, Kenya.', 'ee1c7851b23d25b1daa3e566b49390f8e08e000c60eb818349aa0fced15f0627', NULL, NULL, 1, 1, NULL, 'staff', NULL, 'read_write_delete', 'Active', '0000-00-00 00:00:00', 'en_US', NULL, '2019-01-27 13:36:52'),
(9, 'Justin', 'Timber', 'timber@example.com', '', '0000-00-00', '12345678', NULL, 'fe7f077dc4526047ab33b15a940354ab70c015d0aaab17abb24d2c1030a0be42', NULL, NULL, 1, 5, NULL, 'staff', NULL, 'read_write_delete', 'Active', '0000-00-00 00:00:00', 'en_US', NULL, '2019-01-27 13:39:42'),
(10, 'Moses', 'Mmoja', 'moses@example.com', '', '0000-00-00', '12345678', NULL, 'c4f83e18d0f3ee600635e0c7fc5d9eafcb2916d299a62cdeace9af9f397ae896', NULL, NULL, 1, 7, NULL, 'staff', NULL, 'read_write', 'Active', '0000-00-00 00:00:00', 'en_US', NULL, '2019-01-27 13:40:21'),
(11, 'Mark', 'Angelo', 'mark@example.com', 'Male', '0000-00-00', '12345678', NULL, 'a5b48dcac447c1439291ce13774ffd0638a78f547efa56275ed4f2dc632b5b7e', NULL, NULL, 1, 1, NULL, 'student', NULL, '', 'Active', '0000-00-00 00:00:00', 'en_US', NULL, '2019-01-27 13:50:27'),
(12, 'Vera', 'Watson', 'vera@example.com', 'Male', '0000-00-00', '12345678', NULL, '4b77d2bd1a52aaaaf118084aa8bd4e3d6128d98a35a89acb48bd416918efbb6d', NULL, '', 1, 1, NULL, 'student', NULL, '', 'Active', '0000-00-00 00:00:00', 'en_US', 'ya29.GlwqB8oyktdeo0Ugrw6zuWQrHyF34HHoQ46RGfn6q5a6oABCPbZd-mCEYZCICIdDKioWZ7UBt-6Rxr0EcDrWnSDCKNkH04hY5WfEkuXdGl7L8bu75RCCVbRuV0CKiA', '2019-01-27 13:51:31'),
(13, 'Emmah', 'Miles', 'emmah@example.com', 'Female', '0000-00-00', '12345678', NULL, 'ca0f4f891bcd34722c6f4bd5d24ce6f74b1460a985d33945d58d2b888eea43e6', NULL, NULL, 1, 1, NULL, 'student', NULL, '', 'Active', '0000-00-00 00:00:00', 'en_US', NULL, '2019-01-27 13:52:15'),
(14, 'Tony', 'Stark', 'stark@example.com', 'Male', '0000-00-00', '123456789', NULL, '4b77d2bd1a52aaaaf118084aa8bd4e3d6128d98a35a89acb48bd416918efbb6d', NULL, NULL, 1, 1, NULL, 'student', NULL, '', 'Active', '0000-00-00 00:00:00', 'en_US', NULL, '2019-02-11 07:47:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploadedby` (`uploaded_by`),
  ADD KEY `attachment_for` (`attachment_for`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schoolid` (`school`);

--
-- Indexes for table `branchmessages`
--
ALTER TABLE `branchmessages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receiver` (`receiver`),
  ADD KEY `school` (`school`),
  ADD KEY `branch` (`branch`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courseinstructor`
--
ALTER TABLE `courseinstructor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instructor` (`instructor`),
  ADD KEY `course` (`course`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `school` (`school`),
  ADD KEY `branch` (`branch`);

--
-- Indexes for table `coursesenrolled`
--
ALTER TABLE `coursesenrolled`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student` (`student`),
  ADD KEY `school` (`school`),
  ADD KEY `branch` (`branch`),
  ADD KEY `course` (`course`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch` (`branch`),
  ADD KEY `school` (`school`),
  ADD KEY `course` (`course`);

--
-- Indexes for table `examsreports`
--
ALTER TABLE `examsreports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class` (`exam`),
  ADD KEY `course` (`course`),
  ADD KEY `school` (`school`),
  ADD KEY `branch` (`branch`),
  ADD KEY `student` (`student`);

--
-- Indexes for table `fleet`
--
ALTER TABLE `fleet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch` (`branch`),
  ADD KEY `school` (`school`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch` (`branch`),
  ADD KEY `school` (`school`),
  ADD KEY `student` (`student`);

--
-- Indexes for table `lectureprogress`
--
ALTER TABLE `lectureprogress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapter` (`chapter`),
  ADD KEY `class` (`class`),
  ADD KEY `school` (`school`),
  ADD KEY `branch` (`branch`),
  ADD KEY `course` (`course`),
  ADD KEY `lecture` (`lecture`),
  ADD KEY `student` (`student`);

--
-- Indexes for table `lectures`
--
ALTER TABLE `lectures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapter` (`chapter`),
  ADD KEY `class` (`class`),
  ADD KEY `school` (`school`),
  ADD KEY `branch` (`branch`),
  ADD KEY `course` (`course`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `postby` (`note_by`),
  ADD KEY `note_for` (`note_for`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `school` (`school`),
  ADD KEY `branch` (`branch`),
  ADD KEY `user` (`user`);

--
-- Indexes for table `onlineclasschapters`
--
ALTER TABLE `onlineclasschapters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class` (`class`),
  ADD KEY `course` (`course`),
  ADD KEY `school` (`school`),
  ADD KEY `branch` (`branch`);

--
-- Indexes for table `onlineclasses`
--
ALTER TABLE `onlineclasses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch` (`branch`),
  ADD KEY `school` (`school`),
  ADD KEY `course` (`course`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice` (`invoice`),
  ADD KEY `branch` (`branch`),
  ADD KEY `school` (`school`),
  ADD KEY `payments_ibfk_4` (`student`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class` (`exam`),
  ADD KEY `course` (`course`),
  ADD KEY `school` (`school`),
  ADD KEY `branch` (`branch`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company` (`school`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch` (`branch`),
  ADD KEY `instructor` (`instructor`),
  ADD KEY `school` (`school`),
  ADD KEY `course` (`course`),
  ADD KEY `student` (`student`),
  ADD KEY `car` (`car`);

--
-- Indexes for table `schoolmessages`
--
ALTER TABLE `schoolmessages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receiver` (`receiver`),
  ADD KEY `school` (`school`),
  ADD KEY `branch` (`branch`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `studentexamresults`
--
ALTER TABLE `studentexamresults`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class` (`exam`),
  ADD KEY `course` (`course`),
  ADD KEY `school` (`school`),
  ADD KEY `branch` (`branch`),
  ADD KEY `student` (`student`);

--
-- Indexes for table `timeline`
--
ALTER TABLE `timeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`);

--
-- Indexes for table `timezones`
--
ALTER TABLE `timezones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usermessages`
--
ALTER TABLE `usermessages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receiver` (`receiver`),
  ADD KEY `school` (`school`),
  ADD KEY `branch` (`branch`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `company` (`school`),
  ADD KEY `branch` (`branch`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `branchmessages`
--
ALTER TABLE `branchmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `courseinstructor`
--
ALTER TABLE `courseinstructor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `coursesenrolled`
--
ALTER TABLE `coursesenrolled`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `examsreports`
--
ALTER TABLE `examsreports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `fleet`
--
ALTER TABLE `fleet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lectureprogress`
--
ALTER TABLE `lectureprogress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `lectures`
--
ALTER TABLE `lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `onlineclasschapters`
--
ALTER TABLE `onlineclasschapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `onlineclasses`
--
ALTER TABLE `onlineclasses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `schoolmessages`
--
ALTER TABLE `schoolmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `studentexamresults`
--
ALTER TABLE `studentexamresults`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `timeline`
--
ALTER TABLE `timeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `timezones`
--
ALTER TABLE `timezones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=424;

--
-- AUTO_INCREMENT for table `usermessages`
--
ALTER TABLE `usermessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attachments_ibfk_2` FOREIGN KEY (`attachment_for`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_ibfk_1` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `branchmessages`
--
ALTER TABLE `branchmessages`
  ADD CONSTRAINT `branchmessages_ibfk_1` FOREIGN KEY (`receiver`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `branchmessages_ibfk_2` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `branchmessages_ibfk_3` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `courseinstructor`
--
ALTER TABLE `courseinstructor`
  ADD CONSTRAINT `courseinstructor_ibfk_1` FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `courseinstructor_ibfk_2` FOREIGN KEY (`instructor`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `coursesenrolled`
--
ALTER TABLE `coursesenrolled`
  ADD CONSTRAINT `coursesenrolled_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coursesenrolled_ibfk_2` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coursesenrolled_ibfk_3` FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student` FOREIGN KEY (`student`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exams_ibfk_3` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `examsreports`
--
ALTER TABLE `examsreports`
  ADD CONSTRAINT `examsreports_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `examsreports_ibfk_2` FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `examsreports_ibfk_3` FOREIGN KEY (`exam`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `examsreports_ibfk_4` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `examsreports_ibfk_5` FOREIGN KEY (`student`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fleet`
--
ALTER TABLE `fleet`
  ADD CONSTRAINT `fleet_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fleet_ibfk_2` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`student`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lectureprogress`
--
ALTER TABLE `lectureprogress`
  ADD CONSTRAINT `lectureprogress_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lectureprogress_ibfk_2` FOREIGN KEY (`chapter`) REFERENCES `onlineclasschapters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lectureprogress_ibfk_3` FOREIGN KEY (`class`) REFERENCES `onlineclasses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lectureprogress_ibfk_4` FOREIGN KEY (`lecture`) REFERENCES `lectures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lectureprogress_ibfk_5` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lectureprogress_ibfk_6` FOREIGN KEY (`student`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lectureprogress_ibfk_7` FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lectures`
--
ALTER TABLE `lectures`
  ADD CONSTRAINT `lectures_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lectures_ibfk_2` FOREIGN KEY (`chapter`) REFERENCES `onlineclasschapters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lectures_ibfk_3` FOREIGN KEY (`class`) REFERENCES `onlineclasses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lectures_ibfk_4` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lectures_ibfk_5` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lectures_ibfk_6` FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`note_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`note_for`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `onlineclasschapters`
--
ALTER TABLE `onlineclasschapters`
  ADD CONSTRAINT `onlineclasschapters_ibfk_1` FOREIGN KEY (`class`) REFERENCES `onlineclasses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `onlineclasschapters_ibfk_2` FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `onlineclasschapters_ibfk_3` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `onlineclasschapters_ibfk_4` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `onlineclasses`
--
ALTER TABLE `onlineclasses`
  ADD CONSTRAINT `onlineclasses_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `onlineclasses_ibfk_2` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `onlineclasses_ibfk_3` FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_ibfk_4` FOREIGN KEY (`student`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `questions_ibfk_3` FOREIGN KEY (`exam`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `questions_ibfk_4` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`instructor`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_3` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_4` FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_5` FOREIGN KEY (`student`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_6` FOREIGN KEY (`car`) REFERENCES `fleet` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `schoolmessages`
--
ALTER TABLE `schoolmessages`
  ADD CONSTRAINT `schoolmessages_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schoolmessages_ibfk_2` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schoolmessages_ibfk_3` FOREIGN KEY (`receiver`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `studentexamresults`
--
ALTER TABLE `studentexamresults`
  ADD CONSTRAINT `studentexamresults_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studentexamresults_ibfk_2` FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studentexamresults_ibfk_3` FOREIGN KEY (`exam`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studentexamresults_ibfk_4` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studentexamresults_ibfk_5` FOREIGN KEY (`student`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `timeline`
--
ALTER TABLE `timeline`
  ADD CONSTRAINT `timeline_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `usermessages`
--
ALTER TABLE `usermessages`
  ADD CONSTRAINT `usermessages_ibfk_1` FOREIGN KEY (`receiver`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usermessages_ibfk_2` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usermessages_ibfk_3` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`branch`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`school`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
