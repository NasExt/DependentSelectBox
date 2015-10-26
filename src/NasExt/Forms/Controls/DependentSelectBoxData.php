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

use Nette\Object;

/**
 * @author Dusan Hudak
 */
class DependentSelectBoxData extends Object
{
	/** @var array */
	private $items = array();

	/** @var  NULL */
	private $value;


	/**
	 * @param array $items
	 * @param null $value
	 */
	public function __construct(array $items = array(), $value = NULL)
	{
		$this->items = $items;
		$this->value = $value;
		return $this;
	}


	/**
	 * @param array $items
	 * @return $this
	 */
	public function setItems(array $items)
	{
		$this->items = $items;
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
	 * @param mixed $value
	 * @return $this
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}


	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}
}