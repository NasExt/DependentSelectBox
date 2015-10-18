<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Forms\Controls;

use Nette\Application\UI\Form;
use Nette\Application\UI\ISignalReceiver;
use Nette\Application\UI\Presenter;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Container;
use Nette\InvalidStateException;
use Nette\Utils\Callback;
use Nette\Utils\Html;
use Nette\Utils\Json;

/**
 * DependentSelectBox
 *
 * @author Jáchym Toušek
 * @author Dusan Hudak
 */
class DependentSelectBox extends SelectBox implements ISignalReceiver
{

	/** @var string signal name */
	const SIGNAL_NAME = 'load';

	/** @var BaseControl[] */
	private $parents;

	/** @var callable */
	private $dependentCallback;

	/** @var  bool */
	private $disabledWhenEmpty;


	/**
	 * @param string $label
	 * @param array $parents
	 * @param callable $dependentCallback
	 * @param bool $disabledWhenEmpty
	 */
	public function __construct($label = NULL, array $parents, callable $dependentCallback, $disabledWhenEmpty = FALSE)
	{
		parent::__construct($label);
		$this->parents = (array)$parents;
		$this->dependentCallback = $dependentCallback;
		$this->disabledWhenEmpty = $disabledWhenEmpty;
	}


	/**
	 * @return Html
	 * @throws InvalidStateException
	 */
	public function getControl()
	{
		$control = parent::getControl();

		if ($this->dependentCallback !== NULL) {
			$form = $this->getForm();
			if (!$form || !$form instanceof Form) {
				throw new InvalidStateException("DependentSelectBox supports only Nette\\Application\\UI\\Form.");
			}

			$control->attrs['data-dependentselectbox'] = $form->getPresenter()->link(
				$this->lookupPath('Nette\Application\UI\Presenter') . self::NAME_SEPARATOR . self::SIGNAL_NAME . '!'
			);

			$parents = array();
			foreach ($this->parents as $parent) {
				$parents[$parent->getName()] = $parent->getHtmlId();
			}

			$control->attrs['data-dependentselectbox-parents'] = Json::encode($parents);
		}
		return $control;
	}


	/**
	 * This method will be called when the component becomes attached to Form.
	 * @param  Nette\ComponentModel\IComponent
	 * @return void
	 */
	protected function attached($form)
	{
		parent::attached($form);

		if ($form instanceof \Nette\Forms\Form) {
			$this->tryLoadItems();
		}
	}


	protected function tryLoadItems()
	{
		if ($this->shouldLoadItems()) {

			$parentsValues = array();
			foreach ($this->parents as $parent) {
				$parentsValues[$parent->getName()] = $parent->getValue();
			}

			$items = Callback::invokeArgs($this->dependentCallback, array($parentsValues));
			if ($items) {
				if ($this->disabledWhenEmpty == TRUE) {
					$this->setDisabled(FALSE);
					$this->setOmitted(FALSE);
				}
				$this->loadHttpData();
				$this->setItems($items);
			} else {
				if ($this->disabledWhenEmpty == TRUE) {
					$this->setDisabled();
				}
			}
		}
	}


	/**
	 * @return boolean
	 */
	protected function shouldLoadItems()
	{
		foreach ($this->parents as $parent) {
			if ($parent->hasErrors()) {
				return FALSE;
			}
		}
		return TRUE;
	}


	/**
	 * @param callable $callback
	 * @return DependentSelectBox provides fluent interface
	 */
	public function setDependentCallback($callback)
	{
		$this->dependentCallback = $callback;
		return $this;
	}


	/**
	 * @param string $signal
	 * @throws \Nette\InvalidStateException
	 */
	public function signalReceived($signal)
	{
		/** @var Presenter $presenter */
		$presenter = $this->lookup('Nette\Application\UI\Presenter');
		if ($signal === self::SIGNAL_NAME && $presenter->isAjax()) {

			if (!is_callable($this->dependentCallback)) {
				throw new InvalidStateException('Dependent callback not set.');
			}

			$parentsValues = array();
			foreach ($this->parents as $parent) {
				$parentsValues[$parent->getName()] = $presenter->getParameter($parent->getName());
			}

			$items = Callback::invokeArgs($this->dependentCallback, array($parentsValues));
			$presenter->payload->dependentselectbox = array(
				'id' => $this->getHtmlId(),
				'items' => $items,
				'prompt' => $this->getPrompt(),
				'disabledWhenEmpty' => $this->disabledWhenEmpty,
			);

			$presenter->sendPayload();
		}
	}


	/********************* registration ****************** */

	/**
	 * Adds addDependentSelectBox() method to \Nette\Forms\Form
	 */
	public static function register()
	{
		Container::extensionMethod('addDependentSelectBox', callback(__CLASS__, 'addDependentSelectBox'));
	}


	/**
	 * @param Container $container
	 * @param string $name
	 * @param string $label
	 * @param array $parents
	 * @param callable $dependentCallback
	 * @param bool $disabledWhenEmpty
	 * @return DependentSelectBox provides fluent interface
	 */
	public static function addDependentSelectBox(Container $container, $name, $label = NULL, array $parents, callable $dependentCallback, $disabledWhenEmpty = FALSE)
	{
		$container[$name] = new self($label, $parents, $dependentCallback, $disabledWhenEmpty);
		return $container[$name];
	}
}
