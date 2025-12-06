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
define('TOOL_ID', 'rating');
define('SESSION_NAME', 'php-rating');
define('APP_NAME', 'Rating');
define('APP_VERSION', '5.0.0');
define('VENDOR_CODE', 'spvsp');
define('VENDOR_NAME', 'SPV Software Products');
define('VENDOR_DESCRIPTION', 'Provider of open source educational tools.');
define('VENDOR_URL', 'http://www.spvsoftwareproducts.com/');
define('VENDOR_EMAIL', 'stephen@spvsoftwareproducts.com');

###
###  Database connection settings
###
define('DB_NAME', '');  // e.g. 'mysql:dbname=MyDb;host=localhost' or 'sqlite:php-rating.sqlitedb'
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('DB_TABLENAME_PREFIX', '');
define('DB_TABLE_PAR', 'item' );	// table of items / tasks
define('DB_TABLE_ANS', 'rating');	// table of answers, e.g. ratings
define('CUSTOM_PAR_FIELDS', <<< EOD
item_title varchar(200) NOT NULL,
item_text text,
item_url varchar(200) DEFAULT NULL,
max_rating int(2) NOT NULL DEFAULT '5',
step int(1) NOT NULL DEFAULT '1',
visible tinyint(1) NOT NULL DEFAULT '0',
sequence int(3) NOT NULL DEFAULT '0'
EOD
);
define('CUSTOM_ANS_FIELDS', <<< EOD
rating decimal(10,2) NOT NULL
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
