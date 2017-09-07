<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 *
 * @phpVersion 5.6.0
 */

namespace NasExt\Forms\Tests\Tests;

use NasExt;
use Tester;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @author Ales Wita
 * @license MIT
 */
final class DependentSelectBoxDataTest extends Tester\TestCase
{
	/**
	 * @return void
	 */
	public function testOne()
	{
		$data = new NasExt\Forms\Controls\DependentSelectBoxData;

		Tester\Assert::same([], $data->getItems());
		Tester\Assert::same(null, $data->getValue());
	}


	/**
	 * @return void
	 */
	public function testTwo()
	{
		$items = [1, 2, 3, 4, 5];
		$value = 'foo';

		$data = new NasExt\Forms\Controls\DependentSelectBoxData($items, $value);

		Tester\Assert::same($items, $data->getItems());
		Tester\Assert::same($value, $data->getValue());
	}


	/**
	 * @return void
	 */
	public function testThree()
	{
		$items = [1, 2, 3, 4, 5];
		$value = 'foo';

		$data = new NasExt\Forms\Controls\DependentSelectBoxData;

		$data->setItems($items)
			->setValue($value);

		Tester\Assert::same($items, $data->getItems());
		Tester\Assert::same($value, $data->getValue());
	}
}


$test = new DependentSelectBoxDataTest;
$test->run();
