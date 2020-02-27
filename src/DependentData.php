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

use Nette;


/**
 * @property array $items
 * @property string|int $value
 * @property string $prompt
 *
 * @author Dusan Hudak
 * @author Ales Wita
 * @license MIT
 */
class DependentData
{
	use Nette\SmartObject;

	/** @var array */
	private $items = [];

	/** @var string|int */
	private $value;

	/** @var string */
	private $prompt;


	/**
	 * @param array $items
	 * @param string|int $value
	 * @param string $prompt
	 */
	public function __construct(array $items = [], $value = null, $prompt = null)
	{
		$this->items = $items;
		$this->value = $value;
		$this->prompt = $prompt;
	}


	/**
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}


	/**
	 * @param array $items
	 * @return self
	 */
	public function setItems(array $items)
	{
		$this->items = $items;
		return $this;
	}


	/**
	 * @return string|int
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * @param string|int $value
	 * @return self
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getPrompt()
	{
		return $this->prompt;
	}


	/**
	 * @param string $value
	 * @return self
	 */
	public function setPrompt($value)
	{
		$this->prompt = $value;
		return $this;
	}


	/**
	 * @param array $disabledItems
	 * @return array
	 */
	public function getPreparedItems($disabledItems = null)
	{
		$items = [];
		foreach ($this->items as $key => $item) {
			$elements = [];
			if (is_array($item)) {
				$groupItems = [];
				foreach ($item as $innerKey => $innerItem) {
					$el = $this->getPreparedElement($innerKey, $innerItem, $disabledItems);
					$this->addElementToItemsList($groupItems, $el);
				}

				$items[$key] = [
					'key' => $key,
					'value' => $groupItems,
				];

			} else {
				$el = $this->getPreparedElement($key, $item, $disabledItems);
				$this->addElementToItemsList($items, $el);
			}
		}
		// make a List so the order of items is preserved when sent as JSON to client
		return array_values($items);
	}


	/**
	 * @param string $key
	 * @param mixed $item
	 * @param array|null $disabledItems
	 * @return Nette\Utils\Html
	 */
	private function getPreparedElement($key, $item, $disabledItems = null)
	{
		if (!($item instanceof Nette\Utils\Html)) {
			$el = Nette\Utils\Html::el('option')->value($key)->setText($item);

		} else {
			$el = $item;
		}

		// disable element
		if (is_array($disabledItems) && array_key_exists($key, $disabledItems) && $disabledItems[$key] === true) {
			$el->disabled(true);
		}

		return $el;
	}


	/**
	 * @param array &$items
	 * @param Nette\Utils\Html $el
	 */
	private function addElementToItemsList(&$items, $el)
	{
		$items[$el->getAttribute('value')] = [
			'key' => $el->getValue(),
			'value' => $el->getText(),
		];
		end($items);
		$lKey = key($items);
		foreach ($el->attrs as $attr => $val) {
			$items[$lKey]['attributes'][$attr] = $val;
		}
	}
}
