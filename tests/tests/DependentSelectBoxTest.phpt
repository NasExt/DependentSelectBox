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
use Nette;
use Tester;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @author Ales Wita
 * @license MIT
 */
final class DependentSelectBoxTest extends Tester\TestCase
{
	/**
	 * @return void
	 */
	public function testOne()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig(__DIR__ . '/../app/config/config.neon');

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'GET', ['action' => 'dependentSelect1']);
		$response = $presenter->run($request);

		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);


		// check form
		$form = $presenter['dependentSelectForm1'];

		Tester\Assert::true($form instanceof Nette\Application\UI\Form);


		// check dependent select
		$dependentSelect = $form['dependentSelect'];

		Tester\Assert::true($dependentSelect instanceof NasExt\Forms\Controls\DependentSelectBox);


		// check control
		$control = $dependentSelect->getControl();

		Tester\Assert::true($control instanceof Nette\Utils\Html);


		// check source
		$source = (string) $response->getSource();
		$dom = Tester\DomQuery::fromHtml($source);


		// dependent select tag
		$data = $dom->find('select[name="dependentSelect"]');

		Tester\Assert::count(1, $data);

		$foo = (array) $data[0];
		Tester\Assert::count(4, $foo['@attributes']);
		Tester\Assert::same($control->getAttribute('name'), $foo['@attributes']['name']);
		Tester\Assert::same($control->getAttribute('id'), $foo['@attributes']['id']);
		Tester\Assert::same($control->getAttribute('data-dependentselectbox'), $foo['@attributes']['data-dependentselectbox']);
		Tester\Assert::same($control->getAttribute('data-dependentselectbox-parents'), $foo['@attributes']['data-dependentselectbox-parents']);

		Tester\Assert::same('Select select first', $foo['option']);
	}


	/**
	 * @return void
	 */
	public function testTwo()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig(__DIR__ . '/../app/config/config.neon');

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'POST', ['action' => 'dependentSelect1'], ['_do' => 'dependentSelectForm1-submit'], ['select' => 1, 'dependentSelect' => 1]);
		$response = $presenter->run($request);

		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);


		// check dependent select
		$dependentSelect = $presenter['dependentSelectForm1']['dependentSelect'];

		Tester\Assert::true($dependentSelect instanceof NasExt\Forms\Controls\DependentSelectBox);
		Tester\Assert::same(1, $dependentSelect->getValue());
	}


	/**
	 * @throws Nette\InvalidArgumentException Value '3' is out of allowed set [1, 2] in field 'dependentSelect'.
	 * @return void
	 */
	public function testThree()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig(__DIR__ . '/../app/config/config.neon');

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'POST', ['action' => 'dependentSelect1'], ['_do' => 'dependentSelectForm1-submit'], ['select' => 1, 'dependentSelect' => 3]);
		$response = $presenter->run($request);
	}


	/**
	 * @return void
	 */
	public function testFour()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig(__DIR__ . '/../app/config/config.neon');

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'POST', ['action' => 'dependentSelect1'], ['_do' => 'dependentSelectForm1-submit']);
		$response = $presenter->run($request);


		// check form
		$form = $presenter['dependentSelectForm1'];

		Tester\Assert::true($form->isSubmitted());
		Tester\Assert::true($form->isSuccess());
		Tester\Assert::same(['select' => null, 'dependentSelect' => null], (array) $form->getValues());
	}


	/**
	 * @return void
	 */
	public function testFive()
	{
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';// make ajax request

		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig(__DIR__ . '/../app/config/config.neon');

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'GET', ['action' => 'dependentSelect1', 'do' => 'dependentSelectForm1-dependentSelect-load', 'select' => 1]);
		$response = $presenter->run($request);

		Tester\Assert::true($response instanceof Nette\Application\Responses\JsonResponse);
		Tester\Assert::same([
			'id' => 'frm-dependentSelectForm1-dependentSelect',
			'items' => [
				0 => ['key' => 1, 'value' => 'First'],
				1 => ['key' => 2, 'value' => 'Still first'],
			],
			'value' => null,
			'prompt' => '---',
			'disabledWhenEmpty' => null,
		], $response->getPayload()->dependentselectbox);
	}


	/**
	 * @throws NasExt\Forms\DependentCallbackException Dependent callback for "frm-dependentSelectForm2-dependentSelect" must be set!
	 * @return void
	 */
	public function testSix()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig(__DIR__ . '/../app/config/config.neon');

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'GET', ['action' => 'dependentSelect2Exception1']);
		$response = $presenter->run($request);

		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);

		$response->getSource()->render();
	}


	/**
	 * @throws NasExt\Forms\DependentCallbackException Callback for "frm-dependentSelectForm2-dependentSelect" must return NasExt\Forms\DependentData instance!
	 * @return void
	 */
	public function testSeven()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig(__DIR__ . '/../app/config/config.neon');

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'GET', ['action' => 'dependentSelect2Exception2']);
		$response = $presenter->run($request);

		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);

		$response->getSource()->render();
	}


	/**
	 * @return void
	 */
	/*public function testEight()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig(__DIR__ . '/../app/config/config.neon');

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'POST', ['action' => 'dependentSelect2Disabled1'], ['_do' => 'dependentSelectForm2-submit'], ['select' => 1, 'dependentSelect' => 3]);
		$response = $presenter->run($request);

		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);
		//Tester\Assert::true($presenter['dependentSelectForm1']['dependentSelect']->isDisabled());


		// check source
		$source = (string) $response->getSource();
		$dom = Tester\DomQuery::fromHtml($source);

		var_dump($dom);


	}*/
}


$test = new DependentSelectBoxTest;
$test->run();
