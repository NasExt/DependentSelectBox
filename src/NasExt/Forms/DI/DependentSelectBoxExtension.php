<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace NasExt\Forms\DI;

use NasExt;
use Nette;


/**
 * Class DependentSelectBoxExtension
 * @package NasExt\Forms\DI
 * @author Ondra Votava <ondra.votava@pixidos.com>
 * @author Ales Wita
 */
class DependentSelectBoxExtension extends Nette\DI\CompilerExtension
{
	/**
	 * @param Nette\PhpGenerator\ClassType
	 * @return void
	 */
	public function afterCompile(Nette\PhpGenerator\ClassType $class)
	{
		$initialize = $class->getMethod('initialize');
		$initialize->addBody(__CLASS__ . '::registerControls();');
	}


	/**
	 * @return void
	 */
	public static function registerControls()
	{
		Nette\Forms\Container::extensionMethod('addDependentSelectBox', function (Nette\Forms\Container $container, string $name, string $label, $parents, callable $dependentCallback, $multiple = false) {
			return $container[$name] = new NasExt\Forms\Controls\DependentSelectBox($label, $parents, $dependentCallback, $multiple);
		});
	}
}
