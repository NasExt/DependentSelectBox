<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace NasExt\Forms\Tests\App\Router;

use Nette;


/**
 * @author Ales Wita
 * @license MIT
 */
final class Router
{
	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$route = new Nette\Application\Routers\RouteList;
		$route[] = new Nette\Application\Routers\Route('<presenter>/<action>[/<id>]', 'Base:default');
		return $route;
	}
}
