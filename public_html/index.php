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
		<title>
	</head>
