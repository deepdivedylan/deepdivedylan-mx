<?php
require_once(dirname(__DIR__) . "/php/classes/autoload.php");
require_once(dirname(__DIR__) . "/vendor/autoload.php");

use Mx\Deepdivedylan\Site\Language;

if(session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$locale = $_SESSION["locale"] ?? Language::guessLocale();
$language = new Language("deepdivedylan-mx", $locale);
$language->sendContentLanguageHeader();
?>
<!DOCTYPE html>
<html lang="<?php echo $language->getLocaleAbbreviation(); ?>">
	<head>
		<meta charset="UTF-8" />
		<title><?php echo _("Deep Dive Dylan"); ?></title>
	</head>
	<body>
		<h1><?php echo _("Deep Dive Dylan"); ?></h1>
		<p><?php echo _("Bienvendios al sitio"); ?></p>
	</body>
</html>
