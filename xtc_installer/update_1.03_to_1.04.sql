#Tomcraft - 2010-02-03 - changed database_version
UPDATE database_version SET version = 'xtcM_1.0.4.0';

#vr - 2010-02-02 - Revised English Counties, thx to Chris
delete from zones where zone_country_id = '222';

INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','BAS','Bath and North East Somerset');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','BDF','Bedfordshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','WBK','Berkshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','BBD','Blackburn with Darwen');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','BPL','Blackpool');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','BPL','Bournemouth');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','BNH','Brighton and Hove');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','BST','Bristol');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','BKM','Buckinghamshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','CAM','Cambridgeshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','CHS','Cheshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','CON','Cornwall');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','DUR','County Durham');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','CMA','Cumbria');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','DAL','Darlington');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','DER','Derby');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','DBY','Derbyshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','DEV','Devon');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','DOR','Dorset');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','ERY','East Riding of Yorkshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','ESX','East Sussex');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','ESS','Essex');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','GLS','Gloucestershire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','LND','Greater London');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','MAN','Greater Manchester');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','HAL','Halton');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','HAM','Hampshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','HPL','Hartlepool');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','HEF','Herefordshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','HRT','Hertfordshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','KHL','Hull');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','IOW','Isle of Wight');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','KEN','Kent');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','LAN','Lancashire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','LCE','Leicester');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','LEC','Leicestershire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','LIN','Lincolnshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','LUT','Luton');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','MDW','Medway');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','MER','Merseyside');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','MDB','Middlesbrough');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','MDB','Milton Keynes');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','NFK','Norfolk');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','NTH','Northamptonshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','NEL','North East Lincolnshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','NLN','North Lincolnshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','NSM','North Somerset');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','NBL','Northumberland');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','NYK','North Yorkshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','NGM','Nottingham');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','NTT','Nottinghamshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','OXF','Oxfordshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','PTE','Peterborough');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','PLY','Plymouth');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','POL','Poole');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','POR','Portsmouth');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','RCC','Redcar and Cleveland');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','RUT','Rutland');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','SHR','Shropshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','SOM','Somerset');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','STH','Southampton');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','SOS','Southend-on-Sea');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','SGC','South Gloucestershire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','SYK','South Yorkshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','STS','Staffordshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','STT','Stockton-on-Tees');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','STE','Stoke-on-Trent');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','SFK','Suffolk');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','SRY','Surrey');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','SWD','Swindon');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','TFW','Telford and Wrekin');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','THR','Thurrock');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','TOB','Torbay');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','TYW','Tyne and Wear');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','WRT','Warrington');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','WAR','Warwickshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','WMI','West Midlands');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','WSX','West Sussex');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','WYK','West Yorkshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','WIL','Wiltshire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','WOR','Worcestershire');
INSERT INTO zones (zone_id, zone_country_id, zone_code, zone_name) VALUES ('', '222','YOR','York');

# BOF - DokuMan - 2010-02-11 - set default separator sign to semicolon ';' instead of tabulator '\t'
UPDATE configuration SET configuration_value = ';' WHERE configuration_key = 'CSV_SEPERATOR';

# BOF - Tomcraft - 2010-02-16 - Update Countries (delete Yugoslavia, add Serbia and Monetegro)
delete from countries;
INSERT INTO countries VALUES(1, 'Afghanistan', 'AF', 'AFG', 1, 1);
INSERT INTO countries VALUES(2, 'Albania', 'AL', 'ALB', 1, 1);
INSERT INTO countries VALUES(3, 'Algeria', 'DZ', 'DZA', 1, 1);
INSERT INTO countries VALUES(4, 'American Samoa', 'AS', 'ASM', 1, 1);
INSERT INTO countries VALUES(5, 'Andorra', 'AD', 'AND', 1, 1);
INSERT INTO countries VALUES(6, 'Angola', 'AO', 'AGO', 1, 1);
INSERT INTO countries VALUES(7, 'Anguilla', 'AI', 'AIA', 1, 1);
INSERT INTO countries VALUES(8, 'Antarctica', 'AQ', 'ATA', 1, 1);
INSERT INTO countries VALUES(9, 'Antigua and Barbuda', 'AG', 'ATG', 1, 1);
INSERT INTO countries VALUES(10, 'Argentina', 'AR', 'ARG', 1, 1);
INSERT INTO countries VALUES(11, 'Armenia', 'AM', 'ARM', 1, 1);
INSERT INTO countries VALUES(12, 'Aruba', 'AW', 'ABW', 1, 1);
INSERT INTO countries VALUES(13, 'Australia', 'AU', 'AUS', 1, 1);
INSERT INTO countries VALUES(14, 'Austria', 'AT', 'AUT', 5, 1);
INSERT INTO countries VALUES(15, 'Azerbaijan', 'AZ', 'AZE', 1, 1);
INSERT INTO countries VALUES(16, 'Bahamas', 'BS', 'BHS', 1, 1);
INSERT INTO countries VALUES(17, 'Bahrain', 'BH', 'BHR', 1, 1);
INSERT INTO countries VALUES(18, 'Bangladesh', 'BD', 'BGD', 1, 1);
INSERT INTO countries VALUES(19, 'Barbados', 'BB', 'BRB', 1, 1);
INSERT INTO countries VALUES(20, 'Belarus', 'BY', 'BLR', 1, 1);
INSERT INTO countries VALUES(21, 'Belgium', 'BE', 'BEL', 1, 1);
INSERT INTO countries VALUES(22, 'Belize', 'BZ', 'BLZ', 1, 1);
INSERT INTO countries VALUES(23, 'Benin', 'BJ', 'BEN', 1, 1);
INSERT INTO countries VALUES(24, 'Bermuda', 'BM', 'BMU', 1, 1);
INSERT INTO countries VALUES(25, 'Bhutan', 'BT', 'BTN', 1, 1);
INSERT INTO countries VALUES(26, 'Bolivia', 'BO', 'BOL', 1, 1);
INSERT INTO countries VALUES(27, 'Bosnia and Herzegowina', 'BA', 'BIH', 1, 1);
INSERT INTO countries VALUES(28, 'Botswana', 'BW', 'BWA', 1, 1);
INSERT INTO countries VALUES(29, 'Bouvet Island', 'BV', 'BVT', 1, 1);
INSERT INTO countries VALUES(30, 'Brazil', 'BR', 'BRA', 1, 1);
INSERT INTO countries VALUES(31, 'British Indian Ocean Territory', 'IO', 'IOT', 1, 1);
INSERT INTO countries VALUES(32, 'Brunei Darussalam', 'BN', 'BRN', 1, 1);
INSERT INTO countries VALUES(33, 'Bulgaria', 'BG', 'BGR', 1, 1);
INSERT INTO countries VALUES(34, 'Burkina Faso', 'BF', 'BFA', 1, 1);
INSERT INTO countries VALUES(35, 'Burundi', 'BI', 'BDI', 1, 1);
INSERT INTO countries VALUES(36, 'Cambodia', 'KH', 'KHM', 1, 1);
INSERT INTO countries VALUES(37, 'Cameroon', 'CM', 'CMR', 1, 1);
INSERT INTO countries VALUES(38, 'Canada', 'CA', 'CAN', 1, 1);
INSERT INTO countries VALUES(39, 'Cape Verde', 'CV', 'CPV', 1, 1);
INSERT INTO countries VALUES(40, 'Cayman Islands', 'KY', 'CYM', 1, 1);
INSERT INTO countries VALUES(41, 'Central African Republic', 'CF', 'CAF', 1, 1);
INSERT INTO countries VALUES(42, 'Chad', 'TD', 'TCD', 1, 1);
INSERT INTO countries VALUES(43, 'Chile', 'CL', 'CHL', 1, 1);
INSERT INTO countries VALUES(44, 'China', 'CN', 'CHN', 1, 1);
INSERT INTO countries VALUES(45, 'Christmas Island', 'CX', 'CXR', 1, 1);
INSERT INTO countries VALUES(46, 'Cocos (Keeling) Islands', 'CC', 'CCK', 1, 1);
INSERT INTO countries VALUES(47, 'Colombia', 'CO', 'COL', 1, 1);
INSERT INTO countries VALUES(48, 'Comoros', 'KM', 'COM', 1, 1);
INSERT INTO countries VALUES(49, 'Congo', 'CG', 'COG', 1, 1);
INSERT INTO countries VALUES(50, 'Cook Islands', 'CK', 'COK', 1, 1);
INSERT INTO countries VALUES(51, 'Costa Rica', 'CR', 'CRI', 1, 1);
INSERT INTO countries VALUES(52, 'Cote D''Ivoire', 'CI', 'CIV', 1, 1);
INSERT INTO countries VALUES(53, 'Croatia', 'HR', 'HRV', 1, 1);
INSERT INTO countries VALUES(54, 'Cuba', 'CU', 'CUB', 1, 1);
INSERT INTO countries VALUES(55, 'Cyprus', 'CY', 'CYP', 1, 1);
INSERT INTO countries VALUES(56, 'Czech Republic', 'CZ', 'CZE', 1, 1);
INSERT INTO countries VALUES(57, 'Denmark', 'DK', 'DNK', 1, 1);
INSERT INTO countries VALUES(58, 'Djibouti', 'DJ', 'DJI', 1, 1);
INSERT INTO countries VALUES(59, 'Dominica', 'DM', 'DMA', 1, 1);
INSERT INTO countries VALUES(60, 'Dominican Republic', 'DO', 'DOM', 1, 1);
INSERT INTO countries VALUES(61, 'East Timor', 'TP', 'TMP', 1, 1);
INSERT INTO countries VALUES(62, 'Ecuador', 'EC', 'ECU', 1, 1);
INSERT INTO countries VALUES(63, 'Egypt', 'EG', 'EGY', 1, 1);
INSERT INTO countries VALUES(64, 'El Salvador', 'SV', 'SLV', 1, 1);
INSERT INTO countries VALUES(65, 'Equatorial Guinea', 'GQ', 'GNQ', 1, 1);
INSERT INTO countries VALUES(66, 'Eritrea', 'ER', 'ERI', 1, 1);
INSERT INTO countries VALUES(67, 'Estonia', 'EE', 'EST', 1, 1);
INSERT INTO countries VALUES(68, 'Ethiopia', 'ET', 'ETH', 1, 1);
INSERT INTO countries VALUES(69, 'Falkland Islands (Malvinas)', 'FK', 'FLK', 1, 1);
INSERT INTO countries VALUES(70, 'Faroe Islands', 'FO', 'FRO', 1, 1);
INSERT INTO countries VALUES(71, 'Fiji', 'FJ', 'FJI', 1, 1);
INSERT INTO countries VALUES(72, 'Finland', 'FI', 'FIN', 1, 1);
INSERT INTO countries VALUES(73, 'France', 'FR', 'FRA', 1, 1);
INSERT INTO countries VALUES(74, 'France, Metropolitan', 'FX', 'FXX', 1, 1);
INSERT INTO countries VALUES(75, 'French Guiana', 'GF', 'GUF', 1, 1);
INSERT INTO countries VALUES(76, 'French Polynesia', 'PF', 'PYF', 1, 1);
INSERT INTO countries VALUES(77, 'French Southern Territories', 'TF', 'ATF', 1, 1);
INSERT INTO countries VALUES(78, 'Gabon', 'GA', 'GAB', 1, 1);
INSERT INTO countries VALUES(79, 'Gambia', 'GM', 'GMB', 1, 1);
INSERT INTO countries VALUES(80, 'Georgia', 'GE', 'GEO', 1, 1);
INSERT INTO countries VALUES(81, 'Germany', 'DE', 'DEU', 5, 1);
INSERT INTO countries VALUES(82, 'Ghana', 'GH', 'GHA', 1, 1);
INSERT INTO countries VALUES(83, 'Gibraltar', 'GI', 'GIB', 1, 1);
INSERT INTO countries VALUES(84, 'Greece', 'GR', 'GRC', 1, 1);
INSERT INTO countries VALUES(85, 'Greenland', 'GL', 'GRL', 1, 1);
INSERT INTO countries VALUES(86, 'Grenada', 'GD', 'GRD', 1, 1);
INSERT INTO countries VALUES(87, 'Guadeloupe', 'GP', 'GLP', 1, 1);
INSERT INTO countries VALUES(88, 'Guam', 'GU', 'GUM', 1, 1);
INSERT INTO countries VALUES(89, 'Guatemala', 'GT', 'GTM', 1, 1);
INSERT INTO countries VALUES(90, 'Guinea', 'GN', 'GIN', 1, 1);
INSERT INTO countries VALUES(91, 'Guinea-bissau', 'GW', 'GNB', 1, 1);
INSERT INTO countries VALUES(92, 'Guyana', 'GY', 'GUY', 1, 1);
INSERT INTO countries VALUES(93, 'Haiti', 'HT', 'HTI', 1, 1);
INSERT INTO countries VALUES(94, 'Heard and Mc Donald Islands', 'HM', 'HMD', 1, 1);
INSERT INTO countries VALUES(95, 'Honduras', 'HN', 'HND', 1, 1);
INSERT INTO countries VALUES(96, 'Hong Kong', 'HK', 'HKG', 1, 1);
INSERT INTO countries VALUES(97, 'Hungary', 'HU', 'HUN', 1, 1);
INSERT INTO countries VALUES(98, 'Iceland', 'IS', 'ISL', 1, 1);
INSERT INTO countries VALUES(99, 'India', 'IN', 'IND', 1, 1);
INSERT INTO countries VALUES(100, 'Indonesia', 'ID', 'IDN', 1, 1);
INSERT INTO countries VALUES(101, 'Iran (Islamic Republic of)', 'IR', 'IRN', 1, 1);
INSERT INTO countries VALUES(102, 'Iraq', 'IQ', 'IRQ', 1, 1);
INSERT INTO countries VALUES(103, 'Ireland', 'IE', 'IRL', 1, 1);
INSERT INTO countries VALUES(104, 'Israel', 'IL', 'ISR', 1, 1);
INSERT INTO countries VALUES(105, 'Italy', 'IT', 'ITA', 1, 1);
INSERT INTO countries VALUES(106, 'Jamaica', 'JM', 'JAM', 1, 1);
INSERT INTO countries VALUES(107, 'Japan', 'JP', 'JPN', 1, 1);
INSERT INTO countries VALUES(108, 'Jordan', 'JO', 'JOR', 1, 1);
INSERT INTO countries VALUES(109, 'Kazakhstan', 'KZ', 'KAZ', 1, 1);
INSERT INTO countries VALUES(110, 'Kenya', 'KE', 'KEN', 1, 1);
INSERT INTO countries VALUES(111, 'Kiribati', 'KI', 'KIR', 1, 1);
INSERT INTO countries VALUES(112, 'Korea, Democratic People''s Republic of', 'KP', 'PRK', 1, 1);
INSERT INTO countries VALUES(113, 'Korea, Republic of', 'KR', 'KOR', 1, 1);
INSERT INTO countries VALUES(114, 'Kuwait', 'KW', 'KWT', 1, 1);
INSERT INTO countries VALUES(115, 'Kyrgyzstan', 'KG', 'KGZ', 1, 1);
INSERT INTO countries VALUES(116, 'Lao People''s Democratic Republic', 'LA', 'LAO', 1, 1);
INSERT INTO countries VALUES(117, 'Latvia', 'LV', 'LVA', 1, 1);
INSERT INTO countries VALUES(118, 'Lebanon', 'LB', 'LBN', 1, 1);
INSERT INTO countries VALUES(119, 'Lesotho', 'LS', 'LSO', 1, 1);
INSERT INTO countries VALUES(120, 'Liberia', 'LR', 'LBR', 1, 1);
INSERT INTO countries VALUES(121, 'Libyan Arab Jamahiriya', 'LY', 'LBY', 1, 1);
INSERT INTO countries VALUES(122, 'Liechtenstein', 'LI', 'LIE', 1, 1);
INSERT INTO countries VALUES(123, 'Lithuania', 'LT', 'LTU', 1, 1);
INSERT INTO countries VALUES(124, 'Luxembourg', 'LU', 'LUX', 1, 1);
INSERT INTO countries VALUES(125, 'Macau', 'MO', 'MAC', 1, 1);
INSERT INTO countries VALUES(126, 'Macedonia, The Former Yugoslav Republic of', 'MK', 'MKD', 1, 1);
INSERT INTO countries VALUES(127, 'Madagascar', 'MG', 'MDG', 1, 1);
INSERT INTO countries VALUES(128, 'Malawi', 'MW', 'MWI', 1, 1);
INSERT INTO countries VALUES(129, 'Malaysia', 'MY', 'MYS', 1, 1);
INSERT INTO countries VALUES(130, 'Maldives', 'MV', 'MDV', 1, 1);
INSERT INTO countries VALUES(131, 'Mali', 'ML', 'MLI', 1, 1);
INSERT INTO countries VALUES(132, 'Malta', 'MT', 'MLT', 1, 1);
INSERT INTO countries VALUES(133, 'Marshall Islands', 'MH', 'MHL', 1, 1);
INSERT INTO countries VALUES(134, 'Martinique', 'MQ', 'MTQ', 1, 1);
INSERT INTO countries VALUES(135, 'Mauritania', 'MR', 'MRT', 1, 1);
INSERT INTO countries VALUES(136, 'Mauritius', 'MU', 'MUS', 1, 1);
INSERT INTO countries VALUES(137, 'Mayotte', 'YT', 'MYT', 1, 1);
INSERT INTO countries VALUES(138, 'Mexico', 'MX', 'MEX', 1, 1);
INSERT INTO countries VALUES(139, 'Micronesia, Federated States of', 'FM', 'FSM', 1, 1);
INSERT INTO countries VALUES(140, 'Moldova, Republic of', 'MD', 'MDA', 1, 1);
INSERT INTO countries VALUES(141, 'Monaco', 'MC', 'MCO', 1, 1);
INSERT INTO countries VALUES(142, 'Mongolia', 'MN', 'MNG', 1, 1);
INSERT INTO countries VALUES(143, 'Montenegro', 'ME', 'MNE', 1, 1);
INSERT INTO countries VALUES(144, 'Montserrat', 'MS', 'MSR', 1, 1);
INSERT INTO countries VALUES(145, 'Morocco', 'MA', 'MAR', 1, 1);
INSERT INTO countries VALUES(146, 'Mozambique', 'MZ', 'MOZ', 1, 1);
INSERT INTO countries VALUES(147, 'Myanmar', 'MM', 'MMR', 1, 1);
INSERT INTO countries VALUES(148, 'Namibia', 'NA', 'NAM', 1, 1);
INSERT INTO countries VALUES(149, 'Nauru', 'NR', 'NRU', 1, 1);
INSERT INTO countries VALUES(150, 'Nepal', 'NP', 'NPL', 1, 1);
INSERT INTO countries VALUES(151, 'Netherlands', 'NL', 'NLD', 1, 1);
INSERT INTO countries VALUES(152, 'Netherlands Antilles', 'AN', 'ANT', 1, 1);
INSERT INTO countries VALUES(153, 'New Caledonia', 'NC', 'NCL', 1, 1);
INSERT INTO countries VALUES(154, 'New Zealand', 'NZ', 'NZL', 1, 1);
INSERT INTO countries VALUES(155, 'Nicaragua', 'NI', 'NIC', 1, 1);
INSERT INTO countries VALUES(156, 'Niger', 'NE', 'NER', 1, 1);
INSERT INTO countries VALUES(157, 'Nigeria', 'NG', 'NGA', 1, 1);
INSERT INTO countries VALUES(158, 'Niue', 'NU', 'NIU', 1, 1);
INSERT INTO countries VALUES(159, 'Norfolk Island', 'NF', 'NFK', 1, 1);
INSERT INTO countries VALUES(160, 'Northern Mariana Islands', 'MP', 'MNP', 1, 1);
INSERT INTO countries VALUES(161, 'Norway', 'NO', 'NOR', 1, 1);
INSERT INTO countries VALUES(162, 'Oman', 'OM', 'OMN', 1, 1);
INSERT INTO countries VALUES(163, 'Pakistan', 'PK', 'PAK', 1, 1);
INSERT INTO countries VALUES(164, 'Palau', 'PW', 'PLW', 1, 1);
INSERT INTO countries VALUES(165, 'Panama', 'PA', 'PAN', 1, 1);
INSERT INTO countries VALUES(166, 'Papua New Guinea', 'PG', 'PNG', 1, 1);
INSERT INTO countries VALUES(167, 'Paraguay', 'PY', 'PRY', 1, 1);
INSERT INTO countries VALUES(168, 'Peru', 'PE', 'PER', 1, 1);
INSERT INTO countries VALUES(169, 'Philippines', 'PH', 'PHL', 1, 1);
INSERT INTO countries VALUES(170, 'Pitcairn', 'PN', 'PCN', 1, 1);
INSERT INTO countries VALUES(171, 'Poland', 'PL', 'POL', 1, 1);
INSERT INTO countries VALUES(172, 'Portugal', 'PT', 'PRT', 1, 1);
INSERT INTO countries VALUES(173, 'Puerto Rico', 'PR', 'PRI', 1, 1);
INSERT INTO countries VALUES(174, 'Qatar', 'QA', 'QAT', 1, 1);
INSERT INTO countries VALUES(175, 'Reunion', 'RE', 'REU', 1, 1);
INSERT INTO countries VALUES(176, 'Romania', 'RO', 'ROM', 1, 1);
INSERT INTO countries VALUES(177, 'Russian Federation', 'RU', 'RUS', 1, 1);
INSERT INTO countries VALUES(178, 'Rwanda', 'RW', 'RWA', 1, 1);
INSERT INTO countries VALUES(179, 'Saint Kitts and Nevis', 'KN', 'KNA', 1, 1);
INSERT INTO countries VALUES(180, 'Saint Lucia', 'LC', 'LCA', 1, 1);
INSERT INTO countries VALUES(181, 'Saint Vincent and the Grenadines', 'VC', 'VCT', 1, 1);
INSERT INTO countries VALUES(182, 'Samoa', 'WS', 'WSM', 1, 1);
INSERT INTO countries VALUES(183, 'San Marino', 'SM', 'SMR', 1, 1);
INSERT INTO countries VALUES(184, 'Sao Tome and Principe', 'ST', 'STP', 1, 1);
INSERT INTO countries VALUES(185, 'Saudi Arabia', 'SA', 'SAU', 1, 1);
INSERT INTO countries VALUES(186, 'Senegal', 'SN', 'SEN', 1, 1);
INSERT INTO countries VALUES(187, 'Serbia', 'RS', 'SRB', 1, 1);
INSERT INTO countries VALUES(188, 'Seychelles', 'SC', 'SYC', 1, 1);
INSERT INTO countries VALUES(189, 'Sierra Leone', 'SL', 'SLE', 1, 1);
INSERT INTO countries VALUES(190, 'Singapore', 'SG', 'SGP', 4, 1);
INSERT INTO countries VALUES(191, 'Slovakia (Slovak Republic)', 'SK', 'SVK', 1, 1);
INSERT INTO countries VALUES(192, 'Slovenia', 'SI', 'SVN', 1, 1);
INSERT INTO countries VALUES(193, 'Solomon Islands', 'SB', 'SLB', 1, 1);
INSERT INTO countries VALUES(194, 'Somalia', 'SO', 'SOM', 1, 1);
INSERT INTO countries VALUES(195, 'South Africa', 'ZA', 'ZAF', 1, 1);
INSERT INTO countries VALUES(196, 'South Georgia and the South Sandwich Islands', 'GS', 'SGS', 1, 1);
INSERT INTO countries VALUES(197, 'Spain', 'ES', 'ESP', 3, 1);
INSERT INTO countries VALUES(198, 'Sri Lanka', 'LK', 'LKA', 1, 1);
INSERT INTO countries VALUES(199, 'St. Helena', 'SH', 'SHN', 1, 1);
INSERT INTO countries VALUES(200, 'St. Pierre and Miquelon', 'PM', 'SPM', 1, 1);
INSERT INTO countries VALUES(201, 'Sudan', 'SD', 'SDN', 1, 1);
INSERT INTO countries VALUES(202, 'Suriname', 'SR', 'SUR', 1, 1);
INSERT INTO countries VALUES(203, 'Svalbard and Jan Mayen Islands', 'SJ', 'SJM', 1, 1);
INSERT INTO countries VALUES(204, 'Swaziland', 'SZ', 'SWZ', 1, 1);
INSERT INTO countries VALUES(205, 'Sweden', 'SE', 'SWE', 1, 1);
INSERT INTO countries VALUES(206, 'Switzerland', 'CH', 'CHE', 1, 1);
INSERT INTO countries VALUES(207, 'Syrian Arab Republic', 'SY', 'SYR', 1, 1);
INSERT INTO countries VALUES(208, 'Taiwan', 'TW', 'TWN', 1, 1);
INSERT INTO countries VALUES(209, 'Tajikistan', 'TJ', 'TJK', 1, 1);
INSERT INTO countries VALUES(210, 'Tanzania, United Republic of', 'TZ', 'TZA', 1, 1);
INSERT INTO countries VALUES(211, 'Thailand', 'TH', 'THA', 1, 1);
INSERT INTO countries VALUES(212, 'Togo', 'TG', 'TGO', 1, 1);
INSERT INTO countries VALUES(213, 'Tokelau', 'TK', 'TKL', 1, 1);
INSERT INTO countries VALUES(214, 'Tonga', 'TO', 'TON', 1, 1);
INSERT INTO countries VALUES(215, 'Trinidad and Tobago', 'TT', 'TTO', 1, 1);
INSERT INTO countries VALUES(216, 'Tunisia', 'TN', 'TUN', 1, 1);
INSERT INTO countries VALUES(217, 'Turkey', 'TR', 'TUR', 1, 1);
INSERT INTO countries VALUES(218, 'Turkmenistan', 'TM', 'TKM', 1, 1);
INSERT INTO countries VALUES(219, 'Turks and Caicos Islands', 'TC', 'TCA', 1, 1);
INSERT INTO countries VALUES(220, 'Tuvalu', 'TV', 'TUV', 1, 1);
INSERT INTO countries VALUES(221, 'Uganda', 'UG', 'UGA', 1, 1);
INSERT INTO countries VALUES(222, 'Ukraine', 'UA', 'UKR', 1, 1);
INSERT INTO countries VALUES(223, 'United Arab Emirates', 'AE', 'ARE', 1, 1);
INSERT INTO countries VALUES(224, 'United Kingdom', 'GB', 'GBR', 1, 1);
INSERT INTO countries VALUES(225, 'United States', 'US', 'USA', 2, 1);
INSERT INTO countries VALUES(226, 'United States Minor Outlying Islands', 'UM', 'UMI', 1, 1);
INSERT INTO countries VALUES(227, 'Uruguay', 'UY', 'URY', 1, 1);
INSERT INTO countries VALUES(228, 'Uzbekistan', 'UZ', 'UZB', 1, 1);
INSERT INTO countries VALUES(229, 'Vanuatu', 'VU', 'VUT', 1, 1);
INSERT INTO countries VALUES(230, 'Vatican City State (Holy See)', 'VA', 'VAT', 1, 1);
INSERT INTO countries VALUES(231, 'Venezuela', 'VE', 'VEN', 1, 1);
INSERT INTO countries VALUES(232, 'Viet Nam', 'VN', 'VNM', 1, 1);
INSERT INTO countries VALUES(233, 'Virgin Islands (British)', 'VG', 'VGB', 1, 1);
INSERT INTO countries VALUES(234, 'Virgin Islands (U.S.)', 'VI', 'VIR', 1, 1);
INSERT INTO countries VALUES(235, 'Wallis and Futuna Islands', 'WF', 'WLF', 1, 1);
INSERT INTO countries VALUES(236, 'Western Sahara', 'EH', 'ESH', 1, 1);
INSERT INTO countries VALUES(237, 'Yemen', 'YE', 'YEM', 1, 1);
INSERT INTO countries VALUES(238, 'Zaire', 'ZR', 'ZAR', 1, 1);
INSERT INTO countries VALUES(239, 'Zambia', 'ZM', 'ZMB', 1, 1);
INSERT INTO countries VALUES(240, 'Zimbabwe', 'ZW', 'ZWE', 1, 1);

#vr - 2010-03-01 - Additional index on specials, thx to Georg
alter table specials
add index idx_specials_products_id (products_id);