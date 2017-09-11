<?php
// source: C:\xampp\htdocs\easyweb\app\dependent-select-box\tests\app\presenters/templates/Base/dependentSelect2.latte

use Latte\Runtime as LR;

class Template4520c37f79 extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
		/* line 1 */ $_tmp = $this->global->uiControl->getComponent("dependentSelectForm2");
		if ($_tmp instanceof Nette\Application\UI\IRenderable) $_tmp->redrawControl(null, false);
		$_tmp->render();
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}

}
