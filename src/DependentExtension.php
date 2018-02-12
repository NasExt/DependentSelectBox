<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace NasExt\Forms;

use NasExt;
use Nette;


/**
 * @author Ondra Votava <ondra.votava@pixidos.com>
 * @author Ales Wita
 * @license MIT
 */
class DependentExtension extends Nette\DI\CompilerExtension
{
	/**
	 * @param Nette\PhpGenerator\ClassType $class
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
		Nette\Forms\Container::extensionMethod('addDependentSelectBox', function (Nette\Forms\Container $container, $name, $label, Nette\Forms\IControl ...$parents) {
			return $container[$name] = new NasExt\Forms\Controls\DependentSelectBox($label, $parents);
		});

		Nette\Forms\Container::extensionMethod('addDependentMultiSelectBox', function (Nette\Forms\Container $container, $name, $label, Nette\Forms\IControl ...$parents) {
			return $container[$name] = new NasExt\Forms\Controls\DependentMultiSelectBox($label, $parents);
		});
	}
}
