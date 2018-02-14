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
final class DependentDataTest extends Tester\TestCase
{
	/**
	 * @return void
	 */
	public function testOne()
	{
		$data = new NasExt\Forms\DependentData;

		Tester\Assert::same([], $data->getItems());
		Tester\Assert::same(null, $data->getValue());
		Tester\Assert::same(null, $data->getPrompt());
	}


	/**
	 * @return void
	 */
	public function testTwo()
	{
		$items = [1, 2, 3, 4, 5];
		$value = 'foo';
		$prompt = '---';

		$data = new NasExt\Forms\DependentData($items, $value, $prompt);

		Tester\Assert::same($items, $data->getItems());
		Tester\Assert::same([
			['key' => 0, 'value' => '1', 'attributes' => ['value' => 0, 'disabled' => true]],
			['key' => 1, 'value' => '2', 'attributes' => ['value' => 1]],
			['key' => 2, 'value' => '3', 'attributes' => ['value' => 2]],
			['key' => 3, 'value' => '4', 'attributes' => ['value' => 3]],
			['key' => 4, 'value' => '5', 'attributes' => ['value' => 4, 'disabled' => true]],
		], $data->getPreparedItems([0 => true, 4 => true]));
		Tester\Assert::same($value, $data->getValue());
		Tester\Assert::same($prompt, $data->getPrompt());
	}


	/**
	 * @return void
	 */
	public function testThree()
	{
		$items = [1, 2, 3, 4, 5];
		$value = 'foo';
		$prompt = '---';

		$data = new NasExt\Forms\DependentData;

		$data->setItems($items)
			->setValue($value)
			->setPrompt($prompt);

		Tester\Assert::same($items, $data->getItems());
		Tester\Assert::same([
			['key' => 0, 'value' => '1', 'attributes' => ['value' => 0, 'disabled' => true]],
			['key' => 1, 'value' => '2', 'attributes' => ['value' => 1]],
			['key' => 2, 'value' => '3', 'attributes' => ['value' => 2]],
			['key' => 3, 'value' => '4', 'attributes' => ['value' => 3]],
			['key' => 4, 'value' => '5', 'attributes' => ['value' => 4, 'disabled' => true]],
		], $data->getPreparedItems([0 => true, 4 => true]));
		Tester\Assert::same($value, $data->getValue());
		Tester\Assert::same($prompt, $data->getPrompt());
	}
}


$test = new DependentDataTest;
$test->run();
