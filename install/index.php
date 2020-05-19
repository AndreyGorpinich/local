<?
Class vek3w_tags extends CModule
{


    var $MODULE_ID = 'vek3w_tags';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = 'Y';

	function vek3w_tags()
	{
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path.'/version.php');

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		
		$this->MODULE_NAME = "Модуль срезов товаров";
		$this->MODULE_DESCRIPTION = " ";

	}

	function InstallEvents()
	{
		RegisterModuleDependences('main', 'OnAdminTabControlBegin', 'vek3w_tags', 'TAGTab', 'MyOnAdminTabControlBegin');
		RegisterModuleDependences('iblock', 'OnBeforeIBlockElementAdd', 'vek3w_tags', 'TAGiblock', 'TagsOnBeforeIBlockElement');
		RegisterModuleDependences('iblock', 'OnBeforeIBlockElementUpdate', 'vek3w_tags', 'TAGiblock', 'TagsOnBeforeIBlockElement');

		return TRUE;
	}

	function InstallOptions()
	{
		return TRUE;
	}


	function InstallPublic()
	{
		return TRUE;
	}



	function UnInstallEvents()
	{
		UnRegisterModuleDependences('main', 'OnAdminTabControlBegin', 'vek3w_tags', 'TAGTab', 'MyOnAdminTabControlBegin');
		UnRegisterModuleDependences('iblock', 'OnBeforeIBlockElementAdd', 'vek3w_tags', 'TAGiblock', 'TagsOnBeforeIBlockElement');
		UnRegisterModuleDependences('iblock', 'OnBeforeIBlockElementUpdate', 'vek3w_tags', 'TAGiblock', 'TagsOnBeforeIBlockElement');
		return TRUE;
	}

	function UnInstallOptions()
	{
		COption::RemoveOption('vek3w_tags');
		return TRUE;
	}



	function UnInstallPublic()
	{
		return TRUE;
	}


    function DoInstall()
    {
		global $APPLICATION, $step;
		RegisterModule('vek3w_tags');

		$keyGoodEvents = $this->InstallEvents();
		$keyGoodOptions = $this->InstallOptions();
		$keyGoodPublic = $this->InstallPublic();
		$APPLICATION->IncludeAdminFile(GetMessage('SPER_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/vek3w_tags/install/install.php');
    }


    function DoUninstall()
    {
		global $APPLICATION, $step;
		UnRegisterModule('vek3w_tags');

		$keyGoodEvents = $this->UnInstallEvents();
		$keyGoodOptions = $this->UnInstallOptions();
		$keyGoodPublic = $this->UnInstallPublic();
		$APPLICATION->IncludeAdminFile(GetMessage('SPER_UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/vek3w_tags/install/uninstall.php');
    }


}

?>