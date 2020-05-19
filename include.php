<?
global $DBType, $DB, $MESS, $APPLICATION;
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	'vek3w_tags',
	array(
		'TAGTab' => 'classes/general/tab.php',
		'TAGiblock' => 'classes/general/iblock.php',
	)
);
?>