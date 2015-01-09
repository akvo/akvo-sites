<?php
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/library');

// Initialise Zend Loader to handle Auto-loading
require_once 'Zend/Loader/Autoloader.php';
$oAutoLoader = Zend_Loader_Autoloader::getInstance();

// Initialise KWGPress Auto-loading
spl_autoload_register(function ($sClass) {

	if (strpos($sClass, 'KwgPress\\') === 0) {

		$sClassPath = str_replace('\\', '/', str_replace('KwgPress\\', '', $sClass)) . '.php';
		$sFullClassPath = dirname(__FILE__) . '/library/KwgPress/' . $sClassPath;

		if (is_file($sFullClassPath)) {
			require_once $sFullClassPath;
		}

	}

});