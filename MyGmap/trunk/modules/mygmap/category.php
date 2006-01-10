<?php
include("../../mainfile.php");
$category_form = '';

	include('class/mygmap_classes.php');
	$categoryHandler =& new MyGmapCategoryHandler($GLOBALS['xoopsDB']);
	  case 'insert':
	    if(!XoopsMultiTokenHandler::quickValidate('gcatedit_insert'))
			redirect_header(XOOPS_URL."/modules/mygmap/",1,'Token Error');
		$categoryObject =& $categoryHandler->create();
		$categoryObject->setFormVars($_POST,'');
		if (!$categoryHandler->insert($categoryObject,false,true)) {
			include(XOOPS_ROOT_PATH.'/header.php');
			$categoryObject->setFormVars($_POST,'');
			$categoryObject->defineFormElementsForGMap();
			showCategoryForm(
				$category_form,
				floatval($_POST['mygmap_category_lat']),
				floatval($_POST['mygmap_category_lng']),
				intval($_POST['mygmap_category_zoom']),
				$categoryHandler->getErrors());
			include(XOOPS_ROOT_PATH.'/footer.php');
		redirect_header(XOOPS_URL."/modules/mygmap/",1,'');
		exit();
		break;
	  case 'save':
	    if(!XoopsMultiTokenHandler::quickValidate('gcatedit_save'))
			redirect_header(XOOPS_URL."/modules/mygmap/",1,'Token Error');
		if (isset($_POST['mygmap_category_id'])) {
				if (!$categoryHandler->insert($categoryObject,false,true)) {
					include(XOOPS_ROOT_PATH.'/header.php');
					$categoryObject->setFormVars($_POST,'');
					$categoryObject->defineFormElementsForGMap();
					showCategoryForm(
						$category_form,
						floatval($_POST['mygmap_category_lat']),
						floatval($_POST['mygmap_category_lng']),
						intval($_POST['mygmap_category_zoom']),
						$categoryHandler->getErrors());
					include(XOOPS_ROOT_PATH.'/footer.php');
				}
				redirect_header(XOOPS_URL."/modules/mygmap/",1,'');
				exit();
			}
		}
		break;
	  case 'new':
		include(XOOPS_ROOT_PATH.'/header.php');
		$categoryObject->setFormVars($_POST,'');
		$categoryObject->defineFormElementsForGMap();
		showCategoryForm(
			$category_form,
			floatval($_POST['mygmap_category_lat']),
			floatval($_POST['mygmap_category_lng']),
			intval($_POST['mygmap_category_zoom']),
			'');
		include(XOOPS_ROOT_PATH.'/footer.php');
	  default:
		include(XOOPS_ROOT_PATH.'/header.php');
				$categoryObject->defineFormElementsForGMap();
				showCategoryForm(
					$category_form,
					$categoryObject->getVar('mygmap_category_lat'),
					$categoryObject->getVar('mygmap_category_lng'),
					$categoryObject->getVar('mygmap_category_zoom'),
					'');
			}
		}
	}
}
function showCategoryForm($form, $lat, $lng, $zoom, $errmsg) {
	$GLOBALS['xoopsTpl']->assign('mygmap_API', $GLOBALS['xoopsModuleConfig']['mygmap_api']);
	$GLOBALS['xoopsTpl']->assign('errmsg', $errmsg);
	$GLOBALS['xoopsTpl']->assign('mygmap_credit', $GLOBALS['mygmap_credit']);
?>