<?php

include( "../old-config.php");
include( "../old-db.php");

$g_id  = isset( $_GET['ad']) ? (int) $_GET['ad'] : 0;
$g_kod = isset( $_GET['code']) ? trim( strip_tags( $_GET['code'])) : '';

$success = true;
$errors = array();

if ($g_id < 1) $success = false;

if ($g_kod == '') $success = false;

if ($success) {

	$db = new db();
	if ( ! $db->connect()) exit(/*mysql_error()*/);

	$db->sql = "SELECT email FROM hirdetes WHERE id='$g_id' AND aktivkod='$g_kod'";
	$db->query();

	if (mysql_num_rows( $db->rs) < 1) {
		$success = false;
		array_push($errors, "Ad does not exist");
	} else {
		$rs = mysql_fetch_assoc( $db->rs);
		$g_email = $rs['email'];
	}
}

header( 'Content-Type: text/html; charset=UTF-8');

if ($success) {

	$db->sql = "UPDATE hirdetes SET aktiv='1',aktivedon=NOW(),lastmodified=NOW() WHERE id='$g_id' AND aktivkod='$g_kod'";
	$db->query();
	//print $db->sql;

	//header ("Location: hirdetesek.php?ad=$g_id");

	//check if user also needs to be activated
	$db->sql = "SELECT nev, weblap FROM users WHERE email='$g_email' AND aktivkod='$g_kod' AND aktiv=0 LIMIT 1";
	$db->query();

	if (mysql_num_rows( $db->rs) > 0) {
		$rs = mysql_fetch_assoc( $db->rs);
		$g_nev = $rs['nev'];
		$g_weblap = $rs['weblap'];	//this is the user's encrypted password

	    $db->sql = "INSERT INTO oauth_users (username, password, first_name, last_name) values ('$g_email', '$g_weblap', '$g_nev', NOW())";
	    $db->query();

	    $db->sql = "UPDATE users SET aktiv=1, weblap='' WHERE email='$g_email' AND aktivkod='$g_kod'";
	    $db->query();
	}
?>

	<h3>Your ad has been activated</h3>
	<p><a href="<?php echo $url; ?>/#/hirdetes/<?php echo $g_id; ?>/detail">Click here to view</a></p>

<?php

} else {

?>

	<h3>Ad activation has been unsuccessful</h3>
	<p>Incorrect data provided</p>
	<p>
		To activate your ad you need to provide your ad's id number and activation code
		You can find these details in the email we sent when you posted your ad
	</p>

<?php

}

?>
