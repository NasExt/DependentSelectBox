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

use Nette;


/**
 * DependentSelectBox
 *
 * @author Jáchym Toušek
 * @author Dusan Hudak
 */
class DependentSelectBox extends Nette\Forms\Controls\SelectBox implements Nette\Application\UI\ISignalReceiver
{
	/** @var string */
	const SIGNAL_NAME = 'load';

	/** @var array */
	private $parents;

	/** @var callable */
	private $dependentCallback;

	/** @var bool */
	private $multiple;

	/** @var bool */
	private $disabledWhenEmpty;

	/** @var bool */
	protected $disabled;

	/** @var mixed */
	private $tempValue;


	/**
	 * @param string
	 * @param array|Nette\Forms\Controls\BaseControl
	 * @param callable
	 * @param bool
	 */
	public function __construct(string $label, $parents, callable $dependentCallback, $multiple = false)
	{
		$this->parents = !is_array($parents) ? [$parents] : $parents;
		$this->setDependentCallback($dependentCallback);
		$this->multiple = $multiple;

		parent::__construct($label);
	}


	/**
	 * @throws Nette\InvalidStateException
	 * @return Nette\Utils\Html
	 */
	public function getControl()
	{
		$this->tryLoadItems();
		$control = parent::getControl();

        $attrs = ['multiple' => $this->multiple];

		if ($this->dependentCallback !== null) {
			$form = $this->getForm();

			if (!$form || !$form instanceof Nette\Application\UI\Form) {
				throw new Nette\InvalidStateException('NasExt\\Forms\\Controls\\DependentSelectBox supports only Nette\\Application\\UI\\Form.');
			}


			$parents = [];

			foreach ($this->parents as $parent) {
				$parents[$parent->getName()] = $parent->getHtmlId();
			}

            $attrs['data-dependentselectbox-parents'] = Nette\Utils\Json::encode($parents);
            $attrs['data-dependentselectbox'] = $form->getPresenter()->link($this->lookupPath('Nette\\Application\\UI\\Presenter') . Nette\ComponentModel\IComponent::NAME_SEPARATOR . self::SIGNAL_NAME . '!');
		}

        $control->addAttributes($attrs);
		return $control;
	}


	/**
	 * @return string|int
	 */
	public function getValue()
	{
		$this->tryLoadItems();

		if ($this->multiple) {
			return array_values(array_intersect($this->value, array_keys($this->items)));
		}

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
			if ($this->multiple){
				$this->setMultipleValue($this->tempValue);

			} else {
				parent::setValue($this->tempValue);
			}
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
	public function setDisabledWhenEmpty(bool $value = true)
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


			$data = $this->getData([$parentsValues]);
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

				$this->loadHttpData();// ??
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

		if ($signal === self::SIGNAL_NAME && $presenter->isAjax()) {
			if ($this->dependentCallback === null) {
				throw new Nette\InvalidStateException('Dependent callback not set.');
			}

			$parentsNames = [];
			foreach ($this->parents as $parent) {
				$parentsNames[$parent->getName()] = $presenter->getParameter($parent->getName());
			}


			$data = $this->getData([$parentsNames]);

			$presenter->payload->dependentselectbox = [
				'id' => $this->getHtmlId(),
				'items' => $this->prepareItems($data->getItems()),
				'value' => $data->getValue(),
				'prompt' => $this->getPrompt(),
				'disabledWhenEmpty' => $this->disabledWhenEmpty,
			];
			$presenter->sendPayload();
		}
	}


	/**
	 * @param array
	 * @return array
	 */
	private function prepareItems(array $items)
	{
		$newItems = [];

		foreach ($items as $key => $item) {
			if ($item instanceof Nette\Utils\Html) {
				$newItems[] = [
					'key' => $item->getValue(),
					'value' => $item->getText(),
				];

				end($newItems);
				$key = key($newItems);

				foreach ($item->attrs as $attr => $val) {
					$newItems[$key]['attributes'][$attr] = $val;
				}

			} else {
				$newItems[] = [
					'key' => $key,
					'value' => $item,
				];
			}
		}

		return $newItems;
	}


	/**
	 * @throws Exception
	 * @param array
	 * @return NasExt\Forms\Controls\DependentSelectBoxData
	 */
	private function getData(array $args = [])
	{
		$data = Nette\Utils\Callback::invokeArgs($this->dependentCallback, $args);

		if (!$data instanceof DependentSelectBoxData) {
			throw new \Exception('Callback for "' . $this->getHtmlId() . '" must return NasExt\\Forms\\Controls\\DependentSelectBoxData instance!');
		}

		return $data;
	}


	/**
	 * @return string
	 */
	public function getHtmlName()
	{
		return parent::getHtmlName() . ($this->multiple ? '[]' : '');
	}


	/**
	 * @return void
	 */
	public function loadHttpData()
	{
		if (!$this->multiple) {
			parent::loadHttpData();
			return;
		}

		$this->value = array_keys(array_flip($this->getHttpData(Nette\Forms\Form::DATA_TEXT)));
		if (is_array($this->disabled)) {
			$this->value = array_diff($this->value, array_keys($this->disabled));
		}
	}


	/**
	 * @throws Nette\InvalidArgumentException
	 * @param  array
	 * @return self
	 */
	private function setMultipleValue($values)
	{
		if (is_scalar($values) || $values === null) {
			$values = (array) $values;

		} elseif (!is_array($values)) {
			throw new Nette\InvalidArgumentException('Value must be array or null, ' . gettype($values) . ' given in field "' . $this->name . '".');
		}


		$flip = [];
		foreach ($values as $value) {
			if (!is_scalar($value) && !method_exists($value, '__toString')) {
				throw new Nette\InvalidArgumentException('Values must be scalar, ' . gettype($value) . ' given in field "' . $this->name . '".');
			}

			$flip[(string) $value] = true;
		}


		$values = array_keys($flip);
		if ($this->checkAllowedValues && ($diff = array_diff($values, array_keys($this->items)))) {
			$set = Nette\Utils\Strings::truncate(implode(', ', array_map(function ($s) { return var_export($s, true); }, array_keys($this->items))), 70, '...');
			$vals = (count($diff) > 1 ? 's' : '') . " '" . implode("', '", $diff) . "'";
			throw new Nette\InvalidArgumentException('Value ' . $vals . ' are out of allowed set [' . $set . '] in field "' . $this->name . '".');
		}

		$this->value = $values;
		return $this;
	}
}
