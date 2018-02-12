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
	 * @return void
	 */
	public function actionDependentSelect2Exception1()
	{
		$this->setView('dependentSelect2');
	}


	/**
	 * @return void
	 */
	public function actionDependentSelect2Exception2()
	{
		$this['dependentSelectForm2']['dependentSelect']->setDependentCallback(function () {});
		$this->setView('dependentSelect2');
	}


	/**
	 * @return void
	 */
	public function actionDependentSelect2Disabled1()
	{
		$this['dependentSelectForm2']['dependentSelect']->setDependentCallback([$this, 'dependentCallback'])
			->setDisabled();

		$this->setView('dependentSelect2');
	}


	/**
	 * @return void
	 */
	public function actionDependentSelect2Disabled2()
	{
		$this['dependentSelectForm2']['dependentSelect']->setDependentCallback([$this, 'dependentCallback'])
			->setDisabled([2]);

		$this->setView('dependentSelect2');
	}


	/**
	 * @return void
	 */
	public function actionDependentSelect2Disabled3()
	{
		$this['dependentSelectForm2']['dependentSelect']->setDependentCallback([$this, 'dependentCallback'])
			->setDisabledWhenEmpty();

		$this->setView('dependentSelect2');
	}


	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentDependentSelectForm1()
	{
		$form = new Nette\Application\UI\Form;

		$form->addSelect('select', 'Select', [1 => 'First', 2 => 'Second'])
			->setPrompt('---');

		$form->addDependentSelectBox('dependentSelect', 'Dependent select', $form['select'])
			->setDependentCallback([$this, 'dependentCallback'])
			->setPrompt('Select select first');

		return $form;
	}


	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentDependentSelectForm2()
	{
		$form = new Nette\Application\UI\Form;

		$form->addSelect('select', 'Select', [1 => 'First', 2 => 'Second', 3 => 'Third'])
			->setPrompt('---');

		$form->addDependentSelectBox('dependentSelect', 'Dependent select', $form['select'])
			->setPrompt('Select select first');

		return $form;
	}


	/**
	 * @return void
	 */
	public function actionDependentMultiSelect1()
	{
	}


	/**
	 * @return void
	 */
	public function actionDependentMultiSelect2()
	{
	}


	/**
	 * @return void
	 */
	public function actionDependentMultiSelect2Exception1()
	{
		$this->setView('dependentMultiSelect2');
	}


	/**
	 * @return void
	 */
	public function actionDependentMultiSelect2Exception2()
	{
		$this['dependentMultiSelectForm2']['dependentMultiSelect']->setDependentCallback(function () {});
		$this->setView('dependentMultiSelect2');
	}


	/**
	 * @return void
	 */
	public function actionDependentMultiSelect2Disabled1()
	{
		$this['dependentMultiSelectForm2']['dependentMultiSelect']->setDependentCallback([$this, 'dependentCallback'])
			->setDisabled();

		$this->setView('dependentMultiSelect2');
	}


	/**
	 * @return void
	 */
	public function actionDependentMultiSelect2Disabled2()
	{
		$this['dependentMultiSelectForm2']['dependentMultiSelect']->setDependentCallback([$this, 'dependentCallback'])
			->setDisabled([2]);

		$this->setView('dependentMultiSelect2');
	}


	/**
	 * @return void
	 */
	public function actionDependentMultiSelect2Disabled3()
	{
		$this['dependentMultiSelectForm2']['dependentMultiSelect']->setDependentCallback([$this, 'dependentCallback'])
			->setDisabledWhenEmpty();

		$this->setView('dependentMultiSelect2');
	}


	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentDependentMultiSelectForm1()
	{
		$form = new Nette\Application\UI\Form;

		$form->addSelect('select', 'Select', [1 => 'First', 2 => 'Second'])
			->setPrompt('---');

		$form->addDependentMultiSelectBox('dependentMultiSelect', 'Dependent multi select', $form['select'])
			->setDependentCallback([$this, 'dependentCallback']);

		return $form;
	}


	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentDependentMultiSelectForm2()
	{
		$form = new Nette\Application\UI\Form;

		$form->addSelect('select', 'Select', [1 => 'First', 2 => 'Second', 3 => 'Third'])
			->setPrompt('---');

		$form->addDependentMultiSelectBox('dependentMultiSelect', 'Dependent multi select', $form['select']);

		return $form;
	}


	/**
	 * @param array $values
	 * @return NasExt\Forms\DependentData
	 */
	public function dependentCallback(array $values)
	{
		$data = new NasExt\Forms\DependentData;

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
	}
}
