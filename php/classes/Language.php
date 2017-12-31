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
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 **/
	public function __construct(string $newDomain, string $newLocale) {
		try {
			$this->setDomain($newDomain);
			$this->setLocale($newLocale);
		} catch(\InvalidArgumentException | \Exception | \TypeError $exception) {
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
	 **/
	public function setLocale(string $newLocale) : void {
		$newLocale = trim($newLocale);
		$newLocale = filter_var($newLocale, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		// validate whether the locale is syntactically correct
		$validLocaleRegexp = "/^([a-z]{2})_([A-Z]{2})\.[Uu][Tt][Ff]8$/";
		if(preg_match($validLocaleRegexp, $newLocale) !== 1) {
			throw(new \InvalidArgumentException("invalid locale"));
		}

		$this->locale = $newLocale;
	}

	public function setupLocale() : void {

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
