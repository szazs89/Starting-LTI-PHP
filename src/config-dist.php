<?php

use ceLTIc\LTI\Util;
use ceLTIc\LTI\Enum\LogLevel;

/**
 * This page contains the configuration settings for the application.
 *
 * @author  Stephen P Vickers <stephen@spvsoftwareproducts.com>
 * @copyright  SPV Software Products
 * @license  http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3
 */
###
###  Application settings
###
// Uncomment the next line to log all PHP messages
//  error_reporting(E_ALL);
// Set the application logging level
Util::$logLevel = LogLevel::Error;

// Specify a prefix (starting with '/') when the REQUEST_URI server variable is missing the first part of the real path
define('REQUEST_URI_PREFIX', '');

###
###  App specific settings
###
define('TOOL_ID', 'starting_lti13');
define('SESSION_NAME', 'php-starting');
define('TOOL_BASE_URL', '');
define('TOOL_UUID', '896e90f4-a44f-4b8d-990d-265c063b68c9');  // Linux command: uuidgen
define('APP_NAME', 'Starting LTI 1.3');
define('APP_DESCRIPTION', 'Simple LTI app.');
define('APP_VERSION', '0.1.0');
define('APP_URL', 'https://github.com/szazs89/Starting-LTI-PHP/');
define('VENDOR_CODE', 'szazs');
define('VENDOR_NAME', 'Zsolt Szabo');
define('VENDOR_DESCRIPTION', 'Independent developer');
define('VENDOR_URL', 'https://github.com/szazs89');
define('VENDOR_EMAIL', 'szazs89@gmail.com');
define('INSTRUCTOR_ONLY', true);
define('DEFAULT_DISABLED', true);

###
###  Database connection settings
###
define('DB_NAME', '');  // e.g. 'mysql:dbname=MyDb;host=localhost' or 'sqlite:php-rating.sqlitedb'
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('DB_TABLENAME_PREFIX', '');
define('DB_TABLE_ANS', 'mytool');	// table of answers, e.g. ratings
define('CUSTOM_ANS_FIELDS', <<< EOD
username varchar(6) NOT NULL,
grade int(2),
ansA float,
ansB float,
valA float NOT NULL,
valB float NOT NULL,
parF int(2) NOT NULL,
parL int(2) NOT NULL,
para float NOT NULL,
eta   int(4),
start datetime NOT NULL
EOD
);


###
###  LTI 1.3 Security settings
###
define('SIGNATURE_METHOD', 'RS256');
define('KID', '');  // A random string to identify the key value
define('PRIVATE_KEY', <<< EOD
-----BEGIN RSA PRIVATE KEY-----
Insert private key here
-----END RSA PRIVATE KEY-----
EOD
);

###
###  Dynamic registration settings
###
define('AUTO_ENABLE', false);
define('ENABLE_FOR_DAYS', 0);
?>
