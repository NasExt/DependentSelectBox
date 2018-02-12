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
final class DependentMultiSelectBoxTest extends Tester\TestCase
{
	/**
	 * @return void
	 */
	public function testOne()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig($this->getConfig());

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'GET', ['action' => 'dependentMultiSelect1']);
		$response = $presenter->run($request);

		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);


		// check form
		$form = $presenter['dependentMultiSelectForm1'];

		Tester\Assert::true($form instanceof Nette\Application\UI\Form);


		// check dependent multi select
		$dependentMultiSelect = $form['dependentMultiSelect'];

		Tester\Assert::true($dependentMultiSelect instanceof NasExt\Forms\Controls\DependentMultiSelectBox);


		// check control
		$control = $dependentMultiSelect->getControl();

		Tester\Assert::true($control instanceof Nette\Utils\Html);


		// check source
		$source = (string) $response->getSource();
		$dom = Tester\DomQuery::fromHtml($source);


		// dependent select tag
		$data = $dom->find('select[name="dependentMultiSelect[]"]');

		Tester\Assert::count(1, $data);

		$foo = (array) $data[0];
		Tester\Assert::count(5, $foo['@attributes']);
		Tester\Assert::same($control->getAttribute('name'), $foo['@attributes']['name']);
		Tester\Assert::same($control->getAttribute('id'), $foo['@attributes']['id']);
		Tester\Assert::same('multiple', $foo['@attributes']['multiple']);
		Tester\Assert::same($control->getAttribute('data-dependentselectbox'), $foo['@attributes']['data-dependentselectbox']);
		Tester\Assert::same($control->getAttribute('data-dependentselectbox-parents'), $foo['@attributes']['data-dependentselectbox-parents']);
	}


	/**
	 * @return void
	 */
	public function testTwo()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig($this->getConfig());

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'POST', ['action' => 'dependentMultiSelect1'], ['_do' => 'dependentMultiSelectForm1-submit'], ['select' => 1, 'dependentMultiSelect' => [1]]);
		$response = $presenter->run($request);

		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);


		// check multi dependent select
		$dependentMultiSelect = $presenter['dependentMultiSelectForm1']['dependentMultiSelect'];

		Tester\Assert::true($dependentMultiSelect instanceof NasExt\Forms\Controls\DependentMultiSelectBox);
		Tester\Assert::same([1], $dependentMultiSelect->getValue());
	}


	/**
	 * @throws Nette\InvalidArgumentException Values '3', '4' are out of allowed set [1, 2] in field 'dependentMultiSelect'.
	 * @return void
	 */
	public function testThree()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig($this->getConfig());

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'POST', ['action' => 'dependentMultiSelect1'], ['_do' => 'dependentMultiSelectForm1-submit'], ['select' => 1, 'dependentMultiSelect' => [3, 4]]);
		$response = $presenter->run($request);

		$presenter['dependentMultiSelectForm1']->getValues();// must load values for throws exception
	}


	/**
	 * @return void
	 */
	public function testFour()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig($this->getConfig());

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'POST', ['action' => 'dependentMultiSelect1'], ['_do' => 'dependentMultiSelectForm1-submit']);
		$response = $presenter->run($request);


		// check form
		$form = $presenter['dependentMultiSelectForm1'];

		Tester\Assert::true($form->isSubmitted());
		Tester\Assert::true($form->isSuccess());
		Tester\Assert::same(['select' => null, 'dependentMultiSelect' => []], (array) $form->getValues());
	}


	/**
	 * @return void
	 */
	public function testFive()
	{
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';// make ajax request

		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig($this->getConfig());

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'GET', ['action' => 'dependentMultiSelect1', 'do' => 'dependentMultiSelectForm1-dependentMultiSelect-load', 'frm_dependentMultiSelectForm1_select' => 1]);
		$response = $presenter->run($request);

		Tester\Assert::true($response instanceof Nette\Application\Responses\JsonResponse);
		Tester\Assert::same([
			'id' => 'frm-dependentMultiSelectForm1-dependentMultiSelect',
			'items' => [
				1 => ['key' => 1, 'value' => 'First', 'attributes' => ['value' => 1]],
				2 => ['key' => 2, 'value' => 'Still first', 'attributes' => ['value' => 2]],
			],
			'value' => null,
			'prompt' => false,
			'disabledWhenEmpty' => null,
		], $response->getPayload()->dependentselectbox);
	}


	/**
	 * @throws NasExt\Forms\DependentCallbackException Dependent callback for "frm-dependentMultiSelectForm2-dependentMultiSelect" must be set!
	 * @return void
	 */
	public function testSix()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig($this->getConfig());

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'GET', ['action' => 'dependentMultiSelect2Exception1']);
		$response = $presenter->run($request);

		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);

		$response->getSource()->render();
	}


	/**
	 * @throws NasExt\Forms\DependentCallbackException Callback for "frm-dependentMultiSelectForm2-dependentMultiSelect" must return NasExt\Forms\DependentData instance!
	 * @return void
	 */
	public function testSeven()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig($this->getConfig());

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'GET', ['action' => 'dependentMultiSelect2Exception2']);
		$response = $presenter->run($request);

		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);

		$response->getSource()->render();
	}


	/**
	 * @return void
	 */
	public function testEight()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig($this->getConfig());

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'POST', ['action' => 'dependentMultiSelect2Disabled1'], ['_do' => 'dependentMultiSelectForm2-submit'], ['select' => 1, 'dependentMultiSelect' => [2]]);
		$response = $presenter->run($request);


		// check form
		$form = $presenter['dependentMultiSelectForm2'];

		Tester\Assert::true($form->isSubmitted());
		Tester\Assert::true($form->isSuccess());
		Tester\Assert::same(['select' => 1], (array) $form->getValues());
	}


	/**
	 * @throws Nette\InvalidArgumentException NasExt\Forms\Controls\DependentMultiSelectBox not supported disabled items!
	 * @return void
	 */
	public function testNine()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig($this->getConfig());

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'POST', ['action' => 'dependentMultiSelect2Disabled2'], ['_do' => 'dependentMultiSelectForm2-submit'], ['select' => 1, 'dependentMultiSelect' => [2]]);
		$response = $presenter->run($request);


		// check form
		//$form = $presenter['dependentMultiSelectForm2'];

		//Tester\Assert::true($form->isSubmitted());
		//Tester\Assert::true($form->isSuccess());
		//Tester\Assert::same(['select' => 1, 'dependentMultiSelect' => []], (array) $form->getValues());
	}


	/**
	 * @return void
	 */
	public function testTen()
	{
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig($this->getConfig());

		$container = $configurator->createContainer();
		$presenterFactory = $container->getByType('Nette\\Application\\IPresenterFactory');

		$presenter = $presenterFactory->createPresenter('Base');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Base', 'POST', ['action' => 'dependentMultiSelect2Disabled3'], ['_do' => 'dependentMultiSelectForm2-submit'], ['select' => 3]);
		$response = $presenter->run($request);


		// check form
		$form = $presenter['dependentMultiSelectForm2'];
		$form->getValues();// must load values for check if omitted

		Tester\Assert::true($form->isSubmitted());
		Tester\Assert::true($form->isSuccess());
		Tester\Assert::true($form['dependentMultiSelect']->isOmitted());
		Tester\Assert::same(['select' => 3], (array) $form->getValues());
	}


	/**
	 * @internal
	 */
	private function getConfig()
	{
		return Tester\FileMock::create('
extensions:
	dependentSelectBox: NasExt\Forms\DependentExtension

application:
	mapping:
		*: NasExt\Forms\Tests\App\Presenters\*Presenter

services:
	base.presenter:
		class: NasExt\Forms\Tests\App\Presenters\BasePresenter

	routing.router: NasExt\Forms\Tests\App\Router\Router::createRouter', 'neon');
	}
}


$test = new DependentMultiSelectBoxTest;
$test->run();
