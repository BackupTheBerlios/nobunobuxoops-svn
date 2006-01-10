<?php
include("../../mainfile.php");
$area_form = '';

	include('class/mygmap_classes.php');
	$areaHandler =& new MyGmapAreaHandler($GLOBALS['xoopsDB']);
	  case 'insert':
	    if(!XoopsMultiTokenHandler::quickValidate('gareaedit_insert'))
			redirect_header(XOOPS_URL."/modules/mygmap/",1,'Token Error');
		$areaObject =& $areaHandler->create();
		$areaObject->setFormVars($_POST,'');
		if (!$areaHandler->insert($areaObject,false,true)) {
			include(XOOPS_ROOT_PATH.'/header.php');
			$areaObject->setFormVars($_POST,'');
			$areaObject->defineFormElementsForGMap();
			showAreaForm(
				$area_form,
				floatval($_POST['mygmap_area_lat']),
				floatval($_POST['mygmap_area_lng']),
				intval($_POST['mygmap_area_zoom']),
				$areaHandler->getErrors());
			include(XOOPS_ROOT_PATH.'/footer.php');
		redirect_header(XOOPS_URL."/modules/mygmap/",1,'');
		exit();
		break;
	  case 'save':
	    if(!XoopsMultiTokenHandler::quickValidate('gareaedit_save'))
			redirect_header(XOOPS_URL."/modules/mygmap/",1,'Token Error');
		if (isset($_POST['mygmap_area_id'])) {
				if (!$areaHandler->insert($areaObject,false,true)) {
					include(XOOPS_ROOT_PATH.'/header.php');
					$areaObject->setFormVars($_POST,'');
					$areaObject->defineFormElementsForGMap();
					showAreaForm(
						$area_form,
						floatval($_POST['mygmap_area_lat']),
						floatval($_POST['mygmap_area_lng']),
						intval($_POST['mygmap_area_zoom']),
						$areaHandler->getErrors());
					include(XOOPS_ROOT_PATH.'/footer.php');
				}
				redirect_header(XOOPS_URL."/modules/mygmap/",1,'');
				exit();
			}
		}
		break;
	  case 'new':
		include(XOOPS_ROOT_PATH.'/header.php');
		$areaObject->setFormVars($_POST,'');
		$areaObject->defineFormElementsForGMap();
		showAreaForm(
			$area_form,
			floatval($_POST['mygmap_area_lat']),
			floatval($_POST['mygmap_area_lng']),
			intval($_POST['mygmap_area_zoom']),
			'');
		include(XOOPS_ROOT_PATH.'/footer.php');
	  default:
		include(XOOPS_ROOT_PATH.'/header.php');
				$areaObject->defineFormElementsForGMap();
				showAreaForm(
					$area_form,
					$areaObject->getVar('mygmap_area_lat'),
					$areaObject->getVar('mygmap_area_lng'),
					$areaObject->getVar('mygmap_area_zoom'),
					'');
			}
		}
	}
}
function showAreaForm($form, $lat, $lng, $zoom, $errmsg) {
	$GLOBALS['xoopsTpl']->assign('mygmap_API', $GLOBALS['xoopsModuleConfig']['mygmap_api']);
	$GLOBALS['xoopsTpl']->assign('errmsg', $errmsg);
	$GLOBALS['xoopsTpl']->assign('mygmap_credit', $GLOBALS['mygmap_credit']);
?>