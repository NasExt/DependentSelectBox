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


	public function __construct($label = NULL, array $parents, callable $dependentCallback)
	{
		parent::__construct($label);
		//$this->setDisabled();
		$this->parents = (array)$parents;
		$this->dependentCallback = $dependentCallback;
	}


	/**
	 * Generates control's HTML element.
	 * @return Html
	 * @throws InvalidStateException
	 */
	public function getControl()
	{
		$this->tryLoadItems();
		/** @var $control Html */
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


	protected function tryLoadItems()
	{
		if ($this->shouldLoadItems()) {

			$parentsValues = array();
			foreach ($this->parents as $parent) {
				$parentsValues[$parent->getName()] = $parent->getValue();
			}
			$items = Callback::invokeArgs($this->dependentCallback, array($parentsValues));

			if ($items) {
				//$this->setDisabled(FALSE);
				//$this->setOmitted(FALSE);
				$this->loadHttpData();
				$this->setItems($items);
			} else {
				//$this->setDisabled();
			}
		}
	}


	/**
	 * @return boolean
	 */
	protected function shouldLoadItems()
	{
		foreach ($this->parents as $parent) {
			//bd($parent);
			if ($parent->hasErrors() /* || $parent->getValue() === NULL*/) {
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
	 * @return DependentSelectBox provides fluent interface
	 */
	public static function addDependentSelectBox(Container $container, $name, $label = NULL, array $parents, callable $dependentCallback)
	{
		$container[$name] = new self($label, $parents, $dependentCallback);
		return $container[$name];
	}
}
