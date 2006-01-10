<?php
include("../../mainfile.php");
$marker_form = '';

	include('class/mygmap_classes.php');
	$markerHandler =& new MyGmapMarkerHandler($GLOBALS['xoopsDB']);
	  case 'insert':
	    if(!XoopsMultiTokenHandler::quickValidate('gmapedit_insert')) {
			redirect_header(XOOPS_URL."/modules/mygmap/",1,'Token Error');
		}
		$markerObject =& $markerHandler->create();
		$markerObject->setFormVars($_POST,'');
		if (!$markerHandler->insert($markerObject,false,true)) {
			include(XOOPS_ROOT_PATH.'/header.php');
			$markerObject->setFormVars($_POST,'');
			$markerObject->defineFormElementsForGMap();
			showMarkerForm($marker_form,
						   floatval($_POST['mygmap_marker_lat']),
						   floatval($_POST['mygmap_marker_lng']),
						   intval($_POST['mygmap_marker_zoom']),
						   $markerHandler->getErrors());
			include(XOOPS_ROOT_PATH.'/footer.php');
		}
		redirect_header(XOOPS_URL."/modules/mygmap/?cat=".$markerObject->getVar('mygmap_marker_category_id'),1,'');
		exit();
		break;
	  case 'save':
	    if(!XoopsMultiTokenHandler::quickValidate('gmapedit_save'))
			redirect_header(XOOPS_URL."/modules/mygmap/",1,'Token Error');
		if (isset($_POST['mygmap_marker_id'])) {
				if (!$markerHandler->insert($markerObject,false,true)) {
					include(XOOPS_ROOT_PATH.'/header.php');
					$markerObject->setFormVars($_POST,'');
					$markerObject->defineFormElementsForGMap();
					showMarkerForm($marker_form,
								   floatval($_POST['mygmap_marker_lat']),
								   floatval($_POST['mygmap_marker_lng']),
								   intval($_POST['mygmap_marker_zoom']),
								   $markerHandler->getErrors());
					include(XOOPS_ROOT_PATH.'/footer.php');
				}
				redirect_header(XOOPS_URL."/modules/mygmap/?cat=".$markerObject->getVar('mygmap_marker_category_id'),1,'');
				exit();
			}
		}
		break;
	  case 'new':
		include(XOOPS_ROOT_PATH.'/header.php');
		$markerObject->setFormVars($_POST,'');
		$markerObject->defineFormElementsForGMap();
		showMarkerForm($marker_form,
					   floatval($_POST['mygmap_lat']),
					   floatval($_POST['mygmap_lng']),
					   intval($_POST['mygmap_zoom']),
					   '');
		include(XOOPS_ROOT_PATH.'/footer.php');
	  default:
		include(XOOPS_ROOT_PATH.'/header.php');
				$markerObject->defineFormElementsForGMap();
				showMarkerForm($marker_form,
							   $markerObject->getVar('mygmap_marker_lat'),
							   $markerObject->getVar('mygmap_marker_lng'),
							   $markerObject->getVar('mygmap_marker_zoom'),
							   '');
			}
		}
	}
}
function showMarkerForm($form, $lat, $lng, $zoom, $errmsg) {
	$GLOBALS['xoopsTpl']->assign('mygmap_API', $GLOBALS['xoopsModuleConfig']['mygmap_api']);
	$GLOBALS['xoopsTpl']->assign('errmsg', $errmsg);
	$GLOBALS['xoopsTpl']->assign('mygmap_credit', $GLOBALS['mygmap_credit']);
?>