<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace NasExt\Forms\Tests\App\Presenters;

use NasExt;
use Nette;


/**
 * @author Ales Wita
 * @license MIT
 */
final class BasePresenter extends Nette\Application\UI\Presenter
{
	/**
	 * @return void
	 */
	public function actionDependentSelect1()
	{
	}


	/**
	 * @return void
	 */
	public function actionDependentSelect2()
	{
	}


	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentDependentSelectForm1()
	{
		$form = new Nette\Application\UI\Form;

		$form->addSelect('select', 'Select', [1 => 'First', 2 => 'Second'])
			->setPrompt('---');

		$form->addDependentSelectBox('dependentSelect', 'Dependent select', $form['select'], function (array $values) {
			$data = new NasExt\Forms\Controls\DependentSelectBoxData();

			switch ($values['select']) {
				case 1:
					$data->setItems([1 => 'First', 2 => 'Still first'])
						->setPrompt('---');
					break;

				case 2:
					$data->setItems([3 => 'Second', 4 => 'Still second'])
						->setPrompt('---');
					break;
			}

			return $data;
		})
			->setPrompt('Select select first');

		return $form;
	}


	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentDependentSelectForm2()
	{
		$form = new Nette\Application\UI\Form;

		$form->addSelect('select', 'Select', [1 => 'First', 2 => 'Second'])
			->setPrompt('---');

		$form->addDependentSelectBox('dependentSelect', 'Dependent select', $form['select'])
			->setPrompt('Select select first');

		return $form;
	}
}
