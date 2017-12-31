<?php

namespace Mx\Deepdivedylan\Site;

class Language {
	/**
	 * application domain
	 * @var string $domain
	 **/
	protected $domain;
	/**
	 * current locale as reported by `locale -a`
	 * @var string $locale
	 **/
	protected $locale;

	/**
	 * constructor for this Language
	 *
	 * @param string $newDomain new domain for the gettext application
	 * @param string $newLocale new locale, as compatible with `locale -a`
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws SessionNotActiveException if session is inactive
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 **/
	public function __construct(string $newDomain, string $newLocale) {
		try {
			$this->setDomain($newDomain);
			$this->setLocale($newLocale);
		} catch(\InvalidArgumentException | SessionNotActiveException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}

	/**
	 * accessor method for domain
	 *
	 * @return string value of domain
	 **/
	public function getDomain(): string {
		return($this->domain);
	}

	/**
	 * mutator method for domain
	 *
	 * @param string $newDomain new value of domain
	 * @throws \InvalidArgumentException if $newDomain is invalid
	 **/
	public function setDomain(string $newDomain) : string {
		$newDomain = trim($newDomain);
		$newDomain = filter_var($newDomain, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		if(empty($newDomain) === true) {
			throw(new \InvalidArgumentException("invalid domain"));
		}

		$this->domain = $newDomain;
	}

	/**
	 * accessor method for locale
	 *
	 * @return string value of locale
	 **/
	public function getLocale() : string {
		return($this->locale);
	}


	/**
	 * mutator method for locale
	 *
	 * @param string $newLocale new value of locale
	 * @throws \InvalidArgumentException if $newLocale is invalid
	 * @throws SessionNotActiveException if session is inactive
	 **/
	public function setLocale(string $newLocale) : void {
		if(session_status() !== PHP_SESSION_ACTIVE) {
			throw(new SessionNotActiveException("session inactive"));
		}

		$newLocale = trim($newLocale);
		$newLocale = filter_var($newLocale, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		// validate whether the locale is syntactically correct
		$validLocaleRegexp = "/^([a-z]{2})_([A-Z]{2})\.[Uu][Tt][Ff]8$/";
		if(preg_match($validLocaleRegexp, $newLocale) !== 1) {
			throw(new \InvalidArgumentException("invalid locale"));
		}

		$this->locale = $newLocale;
		$_SESSION["locale"] = $this->locale;
	}

	/**
	 * sets up the locale; this is meant to be executed after starting the session
	 **/
	public function setupLocale() : void {
		putenv("LANG=" . $this->locale);
		setlocale(LC_ALL, $this->locale);
		bindtextdomain($this->domain, dirname(__DIR__, 2) . "/locale");
		bind_textdomain_codeset($this->domain, "UTF-8");
		textdomain($this->domain);
	}

	/**
	 * switches locale and stores it in the session
	 *
	 * @param string $newLocale locale to switch to
	 * @throws \InvalidArgumentException if $newLocale is invalid
	 * @throws SessionNotActiveException if session is inactive
	 **/
	public function switchLocale(string $newLocale) : void {
		// verify the locale exists
		$locale = trim($newLocale);
		if(self::validateLocale($newLocale) === false) {
			throw(new \InvalidArgumentException("invalid locale"));
		}

		// set the locale
		try {
			$this->setLocale($newLocale);
		} catch(\InvalidArgumentException | SessionNotActiveException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}

	public static function guessLocale() : string {
		$locale = "";

		// first, try the session
		if(session_status() !== PHP_SESSION_ACTIVE) {
			$locale = $_SESSION["locale"];
		} else if(empty($_COOKIE["locale"]) === false) {
			$locale = trim(filter_input(INPUT_COOKIE, "locale", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
		} else if(empty($_GET["locale"]) === false) {
			$locale = trim(filter_input(INPUT_GET, "locale", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
		} else {
			$headers = array_change_key_case(apache_request_headers(), CASE_UPPER);
		}
	}

	/**
	 * determines whether the locale is supported
	 *
	 * @param string $newLocale locale to search for
	 * @return bool true if supported, false if not
	 **/
	public static function validateLocale(string $newLocale) : bool {
		$output = trim(shell_exec("locale - a"));
		$locales = explode(PHP_EOL, $output);
		return(array_find($newLocale, $locales) !== false);
	}
}
