NasExt/DependentSelectBox
===========================

DependentSelectBox for Nette Framework.
This dependent select box support dependence for more form controls , not only for select boxes but also dependence for text input, textarea and more.

Requirements
------------

NasExt/DependentSelectBox requires PHP 5.3.2 or higher.

- [Nette Framework](https://github.com/nette/nette)

Installation
------------

The best way to install NasExt/DependentSelectBox is using  [Composer](http://getcomposer.org/):

```sh
$ composer require nasext/dependent-select-box
```

Initialization in your `bootstrap.php`:

```php
NasExt\Forms\Controls\DependentSelectBox::register();
```

or enable the extension using your neon config
```yml
extensions:
	dependentSelectBox: NasExt\Forms\DI\DependentSelectBoxExtension
```

Include from client-side:
- dependentSelectBox.js

Initialize DependentSelectBox:
```js
$('[data-dependentselectbox]').dependentSelectBox();
```

## Usage

How to use DependentSelectBox in form:
````php

	$form->addDependentSelectBox('name', 'Label', array(dependent form controls), function ($values) use () {
				return new \NasExt\Forms\Controls\DependentSelectBoxData(items, valueForSet);
			})
```

````php
		$country = array(
			1 => 'Slovakia',
			2 => 'Czech',
			3 => 'Usa',
		);

		$citySlovakia = array(
			1 => 'Bratislaba',
			2 => 'Kosice',
			3 => 'Zilina',
		);

		$cityCzech = array(
			1 => 'Praha',
			2 => 'Brno',
			3 => 'Ostrava',
		);

		$cityUsa = array(
			1 => 'Toronto',
			2 => 'Philadelphia',
			3 => 'Boston',
		);

		$street1 = array(
			1 => 'street1-1',
			2 => 'street1-2',
			3 => 'street1-3',
		);

		$street2 = array(
			1 => 'street2-1',
			2 => 'street2-2',
			3 => 'street2-3',
		);


		$street3 = array(
			1 => 'street3-1',
			2 => 'street3-2',
			3 => 'street3-3',
		);


		$form->addSelect('country', 'Country', $country)
			->setPrompt('- Select -');

		$form->addText('text', 'Text')
			->setAttribute('placeholder', 'Text');

		$form->addDependentSelectBox('city', 'City', array($form["country"]), function ($values) use ($citySlovakia, $cityCzech, $cityUsa) {
			$data =  new \NasExt\Forms\Controls\DependentSelectBoxData();
			if ($values['country'] == 1) {
				return $data->setItems($citySlovakia);
			} elseif ($values['country'] == 2) {
				return $data->setItems($cityCzech);
			} elseif ($values['country'] == 3) {
				return $data->setItems($cityUsa);
			} else {
				return $data;
			}
		})->setPrompt('- Select -');

		$form->addDependentSelectBox('street', 'Street', array($form["city"], $form["text"]), function ($values) use ($street1, $street2, $street3) {
			$data =  new \NasExt\Forms\Controls\DependentSelectBoxData();
		
			if ($values['city'] == 1) {
				if (!empty($values["text"])) {
					$street1 = array_merge($street1, array(10 => 'Value from Text input: ' . $values["text"]));
				}
				return $data->setItems($street1);
			} elseif ($values['city'] == 2) {
				if (!empty($values["text"])) {
					$street2 = array_merge($street2, array(10 => 'Value from Text input: ' . $values["text"]));
				}
				return $data->setItems($street2);
			} elseif ($values['city'] == 3) {
				if (!empty($values["text"])) {
					$street3 = array_merge($street3, array(10 => 'Value from Text input: ' . $values["text"]));
				}
				return $data->setItems($street3);
			} else {
				return $data;
			}
		})->setPrompt('- Select -');
```

You can set select box as disabled with setDisabledWhenEmpty(TRUE) when is empty, but don't remember disabled select box does not support validation
````php
$form->addDependentSelectBox('city', 'City', array($form["country"]), function ($values) use ($citySlovakia, $cityCzech, $cityUsa) {
			$data =  new \NasExt\Forms\Controls\DependentSelectBoxData();
			if ($values['country'] == 1) {
				return $data->setItems($citySlovakia);
			} elseif ($values['country'] == 2) {
				return $data->setItems($cityCzech);
			} elseif ($values['country'] == 3) {
				return $data->setItems($cityUsa);
			} else {
				return $data;
			}
		})->setDisabledWhenEmpty(TRUE)->setPrompt('- Select -');
```

-----


Repository [http://github.com/nasext/dependentselectbox](http://github.com/nasext/dependentselectbox).
