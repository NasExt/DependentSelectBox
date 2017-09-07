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


/**
 * @author Dusan Hudak
 */
class DependentSelectBoxData
{
	/** @var array */
	private $items = [];

	/** @var string|int */
	private $value;


	/**
	 * @param array
	 * @param string|int
	 */
	public function __construct(array $items = [], $value = null)
	{
		$this->items = $items;
		$this->value = $value;
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
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}


	/**
	 * @return string|int
	 */
	public function getValue()
	{
		return $this->value;
	}
}
