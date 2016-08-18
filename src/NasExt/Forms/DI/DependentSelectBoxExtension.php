<?php
/**
 * Created by PhpStorm.
 * User: Ondra Votava
 * Date: 18.08.2016
 * Time: 21:00
 */

namespace NasExt\Forms\DI;

use NasExt;
use Nette;
use Nette\PhpGenerator as Code;

/**
 * Class DependentSelectBoxExtension
 * @package NasExt\Forms\DI
 * @author Ondra Votava <ondra.votava@pixidos.com>
 */
class DependentSelectBoxExtension extends Nette\DI\CompilerExtension
{
	public function afterCompile(Code\ClassType $class)
	{
		parent::afterCompile($class);

		$init = $class->methods['initialize'];
		$init->addBody('NasExt\Forms\Controls\DependentSelectBox::register();');
	}



	/**
	 * @param \Nette\Configurator $configurator
	 */
	public static function register(Nette\Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Nette\DI\Compiler $compiler) {
			$compiler->addExtension('dependentSelectBox', new DependentSelectBoxExtension());
		};
	}
}