<?php

use ceLTIc\LTI;
use ceLTIc\LTI\Platform;
use ceLTIc\LTI\DataConnector;
use ceLTIc\LTI\ResourceLink;
use ceLTIc\LTI\UserResult;

/**
 * This page displays a simple task for Statics class
 * (the information passed to the LTI tool from the platform is hidden)
 *
 * @author  Kyle Tuck <kylejtuck@gmail.com>, Zsolt Szabo <szazs89@gmail.com>
 * @copyright  Kyle Tuck, Zsolt Szabo
 * @license  http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3
 */
require_once('lib.php');

// Initialise session and database
$db = null;
$ok = init($db, true);
// Initialise parameters
$dataConnector = DataConnector\DataConnector::getDataConnector($db, DB_TABLENAME_PREFIX);
$platform = Platform::fromRecordId($_SESSION['consumer_pk'], $dataConnector);
$platformCheck = new Platform($dataConnector);
$platformCheck->platformId = $platform->platformId;
$platformCheck->clientId = $platform->clientId;
$platformCheck->deploymentId = null;
if ($dataConnector->loadPlatform($platformCheck))
	$platform = $platformCheck;
$resourceLink = ResourceLink::fromRecordId($_SESSION['resource_pk'], $dataConnector);
$userResourceLink = ResourceLink::fromRecordId($_SESSION['user_resource_pk'], $dataConnector);
$userResult = UserResult::fromResourceLink($userResourceLink, $_SESSION['ltiUserId']);

$showVal = function($val) {
    return $val;
};

$page = <<< EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="content-language" content="EN" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title>{$showVal(APP_NAME)}</title>
</head>
<body>
EOD;


if ($ok) {
	$page .= "<!--\n";
	$membership = "<h2>Memberships Details for " . $resourceLink->title . "</h2>\n";
	if ($resourceLink->hasMembershipsService()) {
 		$membership .= "<p>Resource Link has <strong>memberships</strong> service.</p>\n";
		$members = $resourceLink->getMemberships(true);
		$membership .= "<h3>Members</h3>\n";
		$membership .= "<pre>";
		if (!empty($members)) {
		foreach ($members as $member) {
			if ($member->ltiUserId == $_SESSION['ltiUserId']) $userResult = $member;
			$membership .= $member->lastname . ", " . $member->firstname . ": ";
			$membership .= $member->isLearner()?"Student\n":"Instructor\n";
		}
		}
		$membership .= "</pre>\n";
	}
	$page .= "<h2>User Details</h2>\n";
 	$page .= "<pre>\n" . json_encode($userResult, JSON_PRETTY_PRINT) . "</pre>\n";
// 	$page .= "<pre>\n" . json_encode($tool, JSON_PRETTY_PRINT) . "</pre>\n";
	$otherDetails = "<h2>Custom Settings</h2>\n<h3>Resource Link Settings</h3>\n";
	$rlSettings = $resourceLink->getSettings();
	foreach ($rlSettings as $setting => $val) {
		$otherDetails .= "<p>" . $setting . ": " . $val . "</p>\n";
	}
	$otherDetails .= "<h3>Platform Settings</h3>\n";
	foreach ($platform->getSettings() as $setting => $val) {
		$otherDetails .= "<p>" . $setting . ": " . $val . "</p>\n";
	}
	$page .= $otherDetails;
	$page .= $membership;
	$page .= "-->\n";
} else {
	$page .= <<< EOD
	<p style="font-weight: bold; color: #f00;">There was an error initializing the LTI application.</p>
EOD;
	// Check for any messages to be displayed
	if (isset($_SESSION['error_message'])) {
		$page .= <<< EOD
	<p style="font-weight: bold; color: #f00;">ERROR: {$_SESSION['error_message']}</p>
EOD;
		unset($_SESSION['error_message']);
	}

	if (isset($_SESSION['message'])) {
		$page .= <<< EOD
	<p style="font-weight: bold; color: #00f;">{$_SESSION['message']}</p>
EOD;
		unset($_SESSION['message']);
	}
}

if( isset($_SESSION['mypars']) ){	// hopefully it is a POST
    $p = $_SESSION['mypars'];
}else{
    $p = new MyPars;
    $_SESSION['mypars'] = $p;
}
$p->check();
$page .= <<< EOD
A beam of length L={$p->L} m is supported at its both ends (A, B).<br>
A force F={$p->F} kN acts on the beam at the distance a={$p->a} m from its
left end.<br>
Determine the reaction forces A and B.
<form method='POST' action=''>
Reaction force A: <input size=6 name='{$p->pA}' value='{$p->vA}' > kN {$p->okA}<br>
Reaction force B: <input size=6 name='{$p->pB}' value='{$p->vB}' > kN {$p->okB}<br>
<input type='submit' name='chk' value='Check'>
EOD;
if( is_numeric($p->grade()) ){
    $page .= <<< EOD
Points: {$p->grade()}
EOD;
}
$page .= <<< EOD
</form>
</body>
</html>
EOD;

// Display page
echo $page;
?>
