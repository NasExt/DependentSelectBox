<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace NasExt\Forms\Controls;

use NasExt;
use Nette;


/**
 * @author Jáchym Toušek
 * @author Dusan Hudak
 * @author Ales Wita
 * @license MIT
 */
class DependentSelectBox extends Nette\Forms\Controls\SelectBox implements Nette\Application\UI\ISignalReceiver
{
	/** @var string */
	const SIGNAL_NAME = 'load';

	/** @var bool */
	protected $disabled;

	/** @var array */
	private $parents;

	/** @var callable */
	private $dependentCallback;

	/** @var bool */
	private $disabledWhenEmpty;

	/** @var mixed */
	private $tempValue;


	/**
	 * @param string
	 * @param array|Nette\Forms\Controls\BaseControl
	 * @param callable
	 */
	public function __construct($label, $parents, $dependentCallback = null)
	{
		$this->parents = !is_array($parents) ? [$parents] : $parents;
		$this->dependentCallback = $dependentCallback;

		parent::__construct($label);
	}


	/**
	 * @throws Nette\InvalidStateException
	 * @return Nette\Utils\Html
	 */
	public function getControl()
	{
        $attrs = [];
		$this->tryLoadItems();
		$control = parent::getControl();

		$form = $this->getForm();

		if (!($form instanceof Nette\Application\UI\Form)) {
			throw new Nette\InvalidStateException('NasExt\\Forms\\Controls\\DependentSelectBox supports only Nette\\Application\\UI\\Form.');
		}


		$parents = [];
		foreach ($this->parents as $parent) {
			$parents[$parent->getName()] = $parent->getHtmlId();
		}


		$attrs['data-dependentselectbox-parents'] = Nette\Utils\Json::encode($parents);
		$attrs['data-dependentselectbox'] = $form->getPresenter()->link($this->lookupPath('Nette\\Application\\UI\\Presenter') . Nette\ComponentModel\IComponent::NAME_SEPARATOR . self::SIGNAL_NAME . '!');

		$control->addAttributes($attrs);
		return $control;
	}


	/**
	 * @return string|int
	 */
	public function getValue()
	{
		$this->tryLoadItems();
		return parent::getValue();
	}


	/**
	 * @param bool|array
	 * @return self
	 */
	public function setDisabled($value = true)
	{
		$this->disabled = $value;
		return $this;
	}


	/**
	 * @param string|int
	 * @return self
	 */
	public function setValue($value)
	{
		$this->tempValue = $value;
		return $this;
	}


	/**
	 * @param  array
	 * @param  bool
	 * @return self
	 */
	public function setItems(array $items, $useKeys = true)
	{
		parent::setItems($items, $useKeys);

		if ($this->tempValue !== '') {// '' it's prompt value
			parent::setValue($this->tempValue);
		}

		return $this;
	}


	/**
	 * @param callable
	 * @return self
	 */
	public function setDependentCallback(callable $callback)
	{
		$this->dependentCallback = $callback;
		return $this;
	}


	/**
	 * @param bool
	 * @return self
	 */
	public function setDisabledWhenEmpty($value = true)
	{
		$this->disabledWhenEmpty = $value;
		return $this;
	}


	/**
	 * @return void
	 */
	protected function tryLoadItems()
	{
		if ($this->parents === array_filter($this->parents, function ($p) {return !$p->hasErrors();})) {
			$parentsValues = [];

			foreach ($this->parents as $parent) {
				$parentsValues[$parent->getName()] = $parent->getValue();
			}

			$data = $this->getDependentData([$parentsValues]);
			$items = $data->getItems();


			if ($this->getForm()->isSubmitted()) {
				$this->setValue($this->value);

			} elseif ($this->value !== null) {
				$this->setValue($this->value);

			} elseif ($this->tempValue !== null) {
				$this->setValue($this->tempValue);

			} else {
				$this->setValue($data->getValue());
			}


			if (count($items) > 0) {
				if ($this->disabledWhenEmpty === true && $this->disabled !== true) {
					$this->setDisabled(false);
					$this->setOmitted(false);
				}

				if ($this->disabled === true) {
					$this->setDisabled(true);
				}

				$this->loadHttpData();
				$this->setItems($items);

			} else {
				if ($this->disabledWhenEmpty === true) {
					$this->setDisabled();
				}
			}
		}
	}


	/**
	 * @throws Nette\InvalidStateException
	 * @param string
	 * @return void
	 */
	public function signalReceived($signal)
	{
		$presenter = $this->lookup('Nette\\Application\\UI\\Presenter');

		if ($presenter->isAjax() && $signal === self::SIGNAL_NAME) {
			if ($this->dependentCallback === null) {
				throw new Nette\InvalidStateException('Dependent callback not set.');
			}

			$parentsNames = [];
			foreach ($this->parents as $parent) {
				$parentsNames[$parent->getName()] = $presenter->getParameter($parent->getName());
			}


			$data = $this->getDependentData([$parentsNames]);

			$presenter->payload->dependentselectbox = [
				'id' => $this->getHtmlId(),
				'items' => $data->getPreparedItems(),
				'value' => $data->getValue(),
				'prompt' => $data->getPrompt() === null ? $this->getPrompt() : $data->getPrompt(),
				'disabledWhenEmpty' => $this->disabledWhenEmpty,
			];
			$presenter->sendPayload();
		}
	}


	/**
	 * @throws Exception
	 * @param array
	 * @return NasExt\Forms\Controls\DependentData
	 */
	private function getDependentData(array $args = [])
	{
		if ($this->dependentCallback === null) {
			throw new \Exception('Dependent callback for "' . $this->getHtmlId() . '" must be set!');
		}

		$dependentData = Nette\Utils\Callback::invokeArgs($this->dependentCallback, $args);

		if (!($dependentData instanceof NasExt\Forms\DependentData) && !($dependentData instanceof \NasExt\Forms\Controls\DependentSelectBoxData)) {
			throw new \Exception('Callback for "' . $this->getHtmlId() . '" must return NasExt\\Forms\\Controls\\DependentData instance!');
		}

		return $dependentData;
	}
}
