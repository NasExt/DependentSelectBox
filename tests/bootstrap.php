<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

require_once __DIR__ . '/../src/NasExt/Forms/DependentData.php';
require_once __DIR__ . '/../src/NasExt/Forms/Controls/BackCompatibility.php';

if (@!include __DIR__ . '/../../../../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer install`';
	exit(1);
}

require_once __DIR__ . '/app/presenters/BasePresenter.php';
require_once __DIR__ . '/app/router/Router.php';

Tester\Environment::setup();

define('TEMP_DIR', __DIR__ . '/tmp/' . lcg_value());
@mkdir(dirname(TEMP_DIR));
@mkdir(TEMP_DIR);
