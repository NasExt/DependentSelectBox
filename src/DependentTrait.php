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
 * @author Ales Wita
 * @license MIT
 */
trait DependentTrait
{
	/** @var array */
	private $parents;

	/** @var callable */
	private $dependentCallback;

	/** @var bool */
	private $disabledWhenEmpty;

	/** @var mixed */
	private $tempValue;


	/**
	 * @return Nette\Utils\Html
	 */
	public function getControl() : Nette\Utils\Html
	{
		$this->tryLoadItems();

		$attrs = [];
		$control = parent::getControl();
		$form = $this->getForm();

		$parents = [];
		foreach ($this->parents as $parent) {
			$parents[$this->getNormalizeName($parent)] = $parent->getHtmlId();
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

		if (!in_array($this->tempValue, [null, '', []], true)) {
			return $this->tempValue;
		}

		return parent::getValue();
	}


	/**
	 * @param string|int $value
	 * @return self
	 */
	public function setValue($value)
	{
		$this->tempValue = $value;
		return $this;
	}


	/**
	 * @param array $items
	 * @param bool $useKeys
	 * @return self
	 */
	public function setItems(array $items, bool $useKeys = true)
	{
		parent::setItems($items, $useKeys);

		if (!in_array($this->tempValue, [null, '', []], true)) {
			parent::setValue($this->tempValue);
		}

		return $this;
	}


	/**
	 * @param array $args
	 * @return NasExt\Forms\DependentData
	 * @throws NasExt\Forms\DependentCallbackException
	 */
	private function getDependentData(array $args = [])
	{
		if ($this->dependentCallback === null) {
			throw new NasExt\Forms\DependentCallbackException('Dependent callback for "' . $this->getHtmlId() . '" must be set!');
		}

		$dependentData = call_user_func_array($this->dependentCallback, $args);

		if (!($dependentData instanceof NasExt\Forms\DependentData) && !($dependentData instanceof NasExt\Forms\Controls\DependentSelectBoxData)) {
			throw new NasExt\Forms\DependentCallbackException('Callback for "' . $this->getHtmlId() . '" must return NasExt\\Forms\\DependentData instance!');
		}

		return $dependentData;
	}


	/**
	 * @param callable $callback
	 * @return self
	 */
	public function setDependentCallback(callable $callback)
	{
		$this->dependentCallback = $callback;
		return $this;
	}


	/**
	 * @param bool $value
	 * @return self
	 */
	public function setDisabledWhenEmpty($value = true)
	{
		$this->disabledWhenEmpty = $value;
		return $this;
	}


	/**
	 * @param Nette\Forms\Controls\BaseControl $parent
	 * @return string
	 */
	private function getNormalizeName(Nette\Forms\Controls\BaseControl $parent)
	{
		return str_replace('-', '_', $parent->getHtmlId());
	}
}
