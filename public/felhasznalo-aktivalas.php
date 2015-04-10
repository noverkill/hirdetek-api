<?php

include( "../old-config.php");
include( "../old-db.php");

$g_kod = isset( $_GET['kod']) ? trim( strip_tags( $_GET['kod'])) : '';
$g_email = isset( $_GET['email']) ? trim( strip_tags( $_GET['email'])) : '';

$success = true;
$errors = array();

if ($g_kod == '') $success = false;
if ($g_email == '') $success = false;

if($success) {

	$db = new db();
	if ( ! $db->connect()) exit(/*mysql_error()*/);

	$db->sql = "SELECT nev, weblap FROM users WHERE email='$g_email' AND aktivkod='$g_kod' LIMIT 1";
	$db->query();
	//print $db->sql;

	if (mysql_num_rows( $db->rs) < 1) {
		$success = false;
		array_push($errors, "Nincs ilyen felhasznalo!");
	} else {
		$rs = mysql_fetch_assoc( $db->rs);
		$g_nev = $rs['nev'];
		$g_weblap = $rs['weblap'];	//this is the user's encrypted password (temporarily)
	}
}

header( 'Content-Type: text/html; charset=UTF-8');

if ($success) {

    $db->sql = "INSERT INTO oauth_users (username, password, first_name, last_name) values ('$g_email', '$g_weblap', '$g_nev', NOW())";
    $db->query();

    $db->sql = "UPDATE users SET aktiv=1, weblap='' WHERE email='$g_email' AND aktivkod='$g_kod'";#
    $db->query();

	?>

		<h3>Sikeresen aktiválta a felhasznaloi fiokjat!</h3>
		<p><a href="<?php echo $url; ?>/#/login">Kattintson ide a bejelentkezeshez</a></p>

	<?php

} else {

	?>

		<h3>Sikertelen felhasznaloi fiok aktiválás!</h3>
		<p>Hibás aktiváló adatok!</p>
		<p>Az aktivalasahoz szukseg van a felhasznaloi fiok sorszamara es az aktivalo kodra</p>

	<?php

}

?>
