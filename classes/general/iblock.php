<?

class TAGiblock
{
	 function TagsOnBeforeIBlockElement(&$arFields) {
		global $DB;
		$arFields["PROPERTY_VALUES"]["PROP_TO_FILTER"]=[];
		foreach($_REQUEST['TAGS'] as $key=>$value){

			if($value!='0' or $value or $value!=0){
							 $strSql = "
									SELECT 
											CODE,NAME
									FROM 
											b_iblock_property 
									WHERE
											CODE = '$key'
								";

							$res = $DB->Query($strSql, false, $err_mess.__LINE__);
							$prop = $res->Fetch();

							$db_enum_list = CIBlockProperty::GetPropertyEnum($key, Array(), Array("ID"=>$value));
								if($ar_enum_list = $db_enum_list->GetNext()){
										if($ar_enum_list["ID"]){
											$arFields["PROPERTY_VALUES"]["PROP_TO_FILTER"][] = $prop["NAME"].":".$ar_enum_list["VALUE"];
										}
									}else{
										if($key=="PRICE")
										{
											if($value["TO"]){
												$arFields["PROPERTY_VALUES"]["PROP_TO_FILTER"][] = "Цена От:".$value["TO"];
											}
											if($value["FROM"]){
												$arFields["PROPERTY_VALUES"]["PROP_TO_FILTER"][] = "Цена До:".$value["FROM"];
											}
										}

									}

						}

		}
		 //$_REQUEST['form_element_'.$arFields['IBLOCK_ID'].'_active_tab'] = 'tab_tags_prop';

			$arFields["PROPERTY_VALUES"]["PROP_JSON"]["VALUE"]["TEXT"] = json_encode($_REQUEST['TAGS']);
			$arFields["PROPERTY_VALUES"]["PROP_JSON"]["VALUE"]["TYPE"] =  "text";
		 //print_r($arFields);
		 //return false;

	}
}

