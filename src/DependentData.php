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


/**
 * @author Dusan Hudak
 * @author Ales Wita
 * @license MIT
 */
class DependentData
{
	/** @var array */
	private $items = [];

	/** @var string|int */
	private $value;

	/** @var string */
	private $prompt;


	/**
	 * @param array
	 * @param string|int
	 * @param string
	 */
	public function __construct(array $items = [], $value = null, $prompt = null)
	{
		$this->items = $items;
		$this->value = $value;
		$this->prompt = $prompt;
	}


	/**
	 * @param array
	 * @return self
	 */
	public function setItems(array $items)
	{
		$this->items = $items;
		return $this;
	}


	/**
	 * @param string|int
	 * @return self
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}


	/**
	 * @param string|int
	 * @return self
	 */
	public function setPrompt($value)
	{
		$this->prompt = $value;
		return $this;
	}


	/**
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}


	/**
	 * @return array
	 */
	public function getPreparedItems()
	{
		$items = [];
		foreach ($this->items as $key => $item) {
			if ($item instanceof Nette\Utils\Html) {
				$items[] = [
					'key' => $item->getValue(),
					'value' => $item->getText(),
				];

				end($items);
				$key = key($items);

				foreach ($item->attrs as $attr => $val) {
					$items[$key]['attributes'][$attr] = $val;
				}

			} else {
				$items[] = [
					'key' => $key,
					'value' => $item,
				];
			}
		}

		return $items;
	}


	/**
	 * @return string|int
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * @return string
	 */
	public function getPrompt()
	{
		return $this->prompt;
	}
}
