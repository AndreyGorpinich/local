<?
IncludeModuleLangFile(__FILE__);

class TAGTab
{



	function MyOnAdminTabControlBegin( &$form )
	{
		global $DB, $DBType, $APPLICATION;
		
		if(self::NeedAddTab())
		{

			$TABS['TO_PROP']['CONTEXT'] = self::getGroup();
			$TABS['TO_PROP']['NAME'] = "Свойства для среза";
			$TABS['TO_PROP']['DIV'] = "tab_tags_prop";

			$JSSCRIPT = self::getJS();
			$TABS['TO_PROP']['CONTEXT'] = $TABS['TO_PROP']['CONTEXT'].$JSSCRIPT;

			if($_REQUEST["SECTIONTOPROP"]){

				$APPLICATION->RestartBuffer();
					echo self::getPropList($_REQUEST["SECTIONTOPROP"],$DB);
				die();

			}

			self::addTabToForm($form,$TABS);

		}
	}

	function getPropList($section_id,$DB){




	$strSql = "
			SELECT 
					e.ID, p.IBLOCK_ELEMENT_ID, e.IBLOCK_SECTION_ID, p.VALUE_ENUM, p.VALUE, p.IBLOCK_PROPERTY_ID
			FROM 
					b_iblock_element_property AS p,  b_iblock_element AS e 
			WHERE
					e.ID = p.IBLOCK_ELEMENT_ID and e.IBLOCK_SECTION_ID = $section_id and (p.VALUE_ENUM !='' or p.VALUE !='')
			GROUP BY 
					p.VALUE,p.VALUE_ENUM
		";
		//p.IBLOCK_PROPERTY_ID 

		$res = $DB->Query($strSql, false, $err_mess.__LINE__);

		while($prop = $res->Fetch()) {

			$arFilter = array(
			  'ID' => $prop["IBLOCK_PROPERTY_ID"],  
			);

			$rsProperty = CIBlockProperty::GetList(
			 array(),
			 $arFilter
			);
			
			$property = $rsProperty->Fetch();
			if(
				!strstr($property['CODE'],"CML2")
				and
				!strstr($property['CODE'],"PHOTO")
			){


				if($prop["VALUE"]){

					$db_enum_list = CIBlockProperty::GetPropertyEnum($property["CODE"], Array(), Array("ID"=>$prop["VALUE"]));
					if($ar_enum_list = $db_enum_list->GetNext()){
						if($ar_enum_list["ID"]){
							$test[$property['CODE']][$ar_enum_list["ID"]] = $ar_enum_list["VALUE"];
						}
					}
					else{
						$test[$property['CODE']][] = $prop["VALUE"];
						}


				}

				/*if($prop["VALUE_ENUM"]){
					$db_enum_list2 = CIBlockProperty::GetPropertyEnum($property["CODE"], Array(), Array("ID"=>$prop["VALUE_ENUM"]));
						if($ar_enum_list2 = $db_enum_list2->GetNext()){
							$test[$property['CODE']][] = $ar_enum_list2["VALUE"];
						}
				}*/


			$ID_ELEMENT_LIST[$property['CODE']] = $property;
			$ID_ELEMENT_LIST[$property['CODE']]["VALUE"] = $test[$property['CODE']];
			}
		}

		$set_prop = self::setlistprop();

		if($ID_ELEMENT_LIST){


		$arrSetProp = $set_prop["JSON"]; 

			foreach($ID_ELEMENT_LIST as $PROP){
	

	
					$option='<option value="0">'.$PROP["NAME"].'</option>';
	
					foreach($PROP["VALUE"] as $key=>$value){
						if($arrSetProp[$PROP["CODE"]]!='0' and $arrSetProp[$PROP["CODE"]]==$key){
							$selected="selected";
						}
						else{
							$selected="";
						}

						$option.='<option '.$selected.' value="'.$key.'">'.$value.'</<option>';
		
					}

						if($arrSetProp[$PROP["CODE"]]!='0' and $arrSetProp[$PROP["CODE"]]){
							$bg="background:#efe";
							$title_prop = "".$PROP["NAME"]."";
						}
						else{
							$bg="background:#eee";
							$title_prop = '';
						}
	
						$select_prop.= "<span style='float:left;margin:10px'>".$title_prop."<br>
							<select name='TAGS[".$PROP["CODE"]."]' style='$bg;' >".$option."</select>
					</span>";
	
					}
			$select_prop.= '<input name="TAGS[SECTION_ID]" type="hidden" value="'.$section_id.'">';

			//$select_prop.= '<input name="" type="hidden" value="tab_tags_prop">';

			echo $select_prop;
		}
		else{

			echo $set_prop["TEXT"];

		}

	}



	function getGroup(){
		$set_prop = self::setlistprop();
		$sd = $set_prop["JSON"]["SECTION_ID"];

		$arFilter = array('IBLOCK_ID' => 14, 'ACTIVE' => 'Y'); 
		$rsSection = CIBlockSection::GetTreeList($arFilter, $arSelect); 

		while($arSection = $rsSection->Fetch()) {

			$o =  str_repeat("-",  $arSection["DEPTH_LEVEL"]);
			$arSection['NAME'] = $o.$arSection['NAME'];

			if($sd==$arSection['ID']){
				$selected="selected";
			}
			else{
				$selected=$sd;
			}

			$options.= '<option id="sd'.$arSection['ID'].'" '.$selected.' value="'.$arSection['ID'].'" >'.$arSection['NAME'].'</option>';

		}




		$select_group = '
							<tr>
								<td colspan="2">
									Цена от <input value="'.$set_prop["JSON"]["PRICE"]->TO.'" name="TAGS[PRICE][TO]"> 
										 до <input value="'.$set_prop["JSON"]["PRICE"]->FROM.'" name="TAGS[PRICE][FROM]">
								</td>
							</tr>
							<tr>
								<td style="vertical-align: top;" width="100px">
									<select id="SelectGroup" size="10" name="group_to_prop">'.$options.'</select>
								</td>';
		$select_group.='		<td style="vertical-align: top;" id="prop_list">'.$set_prop["TEXT"].'</td>
							</tr>
						';

		return $select_group;
	}


	function setlistprop(){
		if($_REQUEST['ID']){
			$arSelect = Array("ID","IBLOCK_ID", "PROPERTY_PROP_TO_FILTER","PROPERTY_PROP_JSON");
			$arFilter = Array("ID"=>IntVal($_REQUEST['ID']), "ACTIVE"=>"Y");
			$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
			$ob = $res->GetNextElement();
			$arProps = $ob->GetProperties();

					$set_prop = '';
					foreach($arProps["PROP_TO_FILTER"]["VALUE"] as $value ){
						$set_prop.= "<br>".$value;
					}
			$arr["TEXT"] = $set_prop;
			$arr["JSON"] = (array) json_decode(str_replace('&quot;', '"', $arProps["PROP_JSON"]["VALUE"]["TEXT"]));
	
			return  $arr;
		}
	}


	function addTabToForm($form,$TABS){

		foreach($TABS as $tab ){

		 	 $form->tabs[] = array(
					'DIV' => $tab['DIV'],
					'TAB' => $tab['NAME'],
					'ICON' => 'main_user_edit',
					'TITLE' => $tab['NAME'],
					'CONTENT' => $tab['CONTEXT']
			);

		}
	}



	function getJS(){
		$set_prop = self::setlistprop();
		$sd = $set_prop["JSON"]["SECTION_ID"];
		if($sd){
			$jstrigger = 'BX.ready(function(){BX.fireEvent(BX(sd'.$sd.'),"change")});';
		}

		$js = "
					<script>
							".$jstrigger."
							BX.bind(BX('SelectGroup'), 'change', jspost);
							
							function jspost(){

								var post = {};
								post['SECTIONTOPROP'] = BX(this).value;
								
								//console.log( );
								node = BX('prop_list'); //сюда будем вставлять полученный html
	
									if (!!node) {
									
										BX.ajax.post(
										  ' ',
											post,
											function (data) {
												node.innerHTML = data;
									
											}
										);
									
									}
							}
					</script>
		";
		return $js;
	}



	function NeedAddTab()
	{
		global $APPLICATION;
		
		$return = false;
		if(
			// ---- edit iblock element in admin_section ---- //
			(
				$APPLICATION->GetCurPage() == '/bitrix/admin/iblock_element_edit.php' &&
				//IntVal($_REQUEST['ID'])>0 &&
				$_REQUEST['bxpublic']!='Y'
			) ||
			// ---- edit product ---- //
			(
				$APPLICATION->GetCurPage() == '/bitrix/admin/cat_product_edit.php' &&
				IntVal($_REQUEST['ID'])>0 &&
				$_REQUEST['bxpublic']!='Y'
			) ||
			// ---- edit iblock element in work area ---- //
			(
				$APPLICATION->GetCurPage() == '/bitrix/admin/cat_product_edit.php' &&
				IntVal($_REQUEST['ID'])>0 &&
				$_REQUEST['bxpublic']=='Y'
			)
		)
		{
			$return = true;
		}
		return $return;
	}
}