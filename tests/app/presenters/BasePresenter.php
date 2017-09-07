<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
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
	public function actionDefault()
	{
	}


	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Nette\Application\UI\Form
	{
		$form = new Nette\Application\UI\Form;

		$form->addSelect('select', 'Select', [1 => 'First', 2 => 'Second'])
			->setPrompt('---');

		$form->addDependentSelectBox('dependentSelect', 'Dependent select', $form['select'], function (array $values): NasExt\Forms\Controls\DependentSelectBoxData {
			$data = new NasExt\Forms\Controls\DependentSelectBoxData;

			switch ($values['select']) {
				case 1:
					$data->setItems([1 => 'First']);
					break;

				case 2:
					$data->setItems([2 => 'Second']);
					break;
			}

			return $data;
		})
			->setPrompt('---');

		return $form;
	}
}
