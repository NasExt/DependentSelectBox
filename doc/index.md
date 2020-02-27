# NasExt/DependentSelectBox
DependentSelectBox for [Nette Framework](https://nette.org). This dependent select box support dependence for more form controls , not only for select boxes but also dependence for text input, textarea and more.

## Installation
The best way to install NasExt/DependentSelectBox is using [Composer](http://getcomposer.org/):
```sh
$ composer require nasext/dependent-select-box
```


Initialization in your `bootstrap.php`:
```php
NasExt\Forms\DependentExtension::registerControls();
```


Or enable the extension using your neon config:
```neon
extensions:
	dependentSelectBox: NasExt\Forms\DependentExtension
```


Include from client-side folder:
```
dependentSelectBox.js
```


Initialize DependentSelectBox:
```js
// @param callback a handler to be called when Ajax requests complete
$('[data-dependentselectbox]').dependentSelectBox(callback);
```


## Usage
How to use DependentSelectBox in form:
```php
$form->addDependentSelectBox('name', 'Label', $dependentControl1, $dependentControl2, ...)
	->setDependentCallback(function ($values) {
		return new \NasExt\Forms\DependentData(items, value, prompt);
	})
```


```php
$country = [
	1 => 'Slovakia',
	2 => 'Czechia',
	3 => 'USA',
];

$citySlovakia = [
	1 => 'Bratislaba',
	2 => 'Kosice',
	3 => 'Zilina',
];

$cityCzechia = [
	1 => 'Praha',
	2 => 'Brno',
	3 => 'Ostrava',
];

$cityUsa = [
	1 => 'Toronto',
	2 => 'Philadelphia',
	3 => 'Boston',
];

$street1 = [
	1 => 'street1-1',
	2 => 'street1-2',
	3 => 'street1-3',
];

$street2 = [
	1 => 'street2-1',
	2 => 'street2-2',
	3 => 'street2-3',
];

$street3 = [
	1 => 'street3-1',
	2 => 'street3-2',
	3 => 'street3-3',
];


$form->addSelect('country', 'Country', $country)
	->setPrompt('--- Select ---');

$form->addText('text', 'Text')
	->setAttribute('placeholder', 'Text');

$form->addDependentSelectBox('city', 'City', $form['country'])
	->setDependentCallback(function ($values) use ($citySlovakia, $cityCzechia, $cityUsa) {
		$data = new \NasExt\Forms\DependentData;

		if ($values['country'] === 1) {
			$data->setItems($citySlovakia)->setPrompt('---');

		} elseif ($values['country'] === 2) {
			$data->setItems($cityCzechia)->setPrompt('---');

		} elseif ($values['country'] === 3) {
			$data->setItems($cityUsa)->setPrompt('---');
		}

		return $data;
	})
	->setPrompt('--- Select country first ---');

$form->addDependentSelectBox('street', 'Street', $form['city'], $form['text'])
	->setDependentCallback(function ($values) use ($street1, $street2, $street3) {
		$data = new \NasExt\Forms\DependentData;

		if ($values['city'] === 1) {
			if (!empty($values['text'])) {
				$street1 = array_merge($street1, [10 => 'Value from Text input: ' . $values['text']]);
			}

			$data->setItems($street1);

		} elseif ($values['city'] === 2) {
			if (!empty($values['text'])) {
				$street2 = array_merge($street2, [10 => 'Value from Text input: ' . $values['text']]);
			}

			$data->setItems($street2);

		} elseif ($values['city'] === 3) {
			if (!empty($values['text'])) {
				$street3 = array_merge($street3, [10 => 'Value from Text input: ' . $values['text']]);
			}

			$data->setItems($street3);


		return $data;
	})
	->setPrompt('--- Select ---');
```


You can set the select box as disabled with `setDisabledWhenEmpty(true)` when empty, but remember, a disabled select box does not support validation.
```php
$form->addDependentSelectBox('city', 'City', $form['country'])
	->setDependentCallback(function ($values) use ($citySlovakia, $cityCzechia, $cityUsa) {
		$data = new \NasExt\Forms\Controls\DependentSelectBoxData;

		if ($values['country'] === 1) {
			$data->setItems($citySlovakia);

		} elseif ($values['country'] === 2) {
			$data->setItems($cityCzechia);

		} elseif ($values['country'] === 3) {
			$data->setItems($cityUsa);
		}

		return $data;
	})
	->setDisabledWhenEmpty(true)
	->setPrompt('--- Select ---');
```
