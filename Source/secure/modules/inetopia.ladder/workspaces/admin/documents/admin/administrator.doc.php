<?php

	global $gl_oVars;

	# fetch url data
	$iLadderId	= (int)$_GET['ladder_id'];
	//$iGameId = (int)$_GET['game_id'];
	
	# classes & objects
	$cLadderSystem	= new InetopiaLadder( $gl_oVars->cDBInterface );
	$cGamePool = new GamePool( $gl_oVars->cDBInterface );
	$cAdministrator		= new Administrator( $gl_oVars->cDBInterface );

	
	
	# fetch ladderdata
	$oLadder = $cLadderSystem->GetLadderbyID( $iLadderId );
	$oGame = $cGamePool->GetGameById( $oLadder->game_id );
	
	
	
	
	# ---------------------------------------------------------------------
	# D E L E T E  A D M I N 
	# ---------------------------------------------------------------------
	if( $_GET['a'] == 'delete_admin' )
	{
		// $iAdminId = (int)$_GET['admin_id'];
		$iAdminPermissionId = (int)$_GET['permission_id'];
		# 
		# execute sql-query directly
		#
		/*
		$sql_query =" DELETE FROM `{$GLOBALS['g_egltb_admin_permissions']}` ".
					" WHERE module_id='".MODULEID_INETOPIA_CUP."' && data='{$iCupId}' && admin_id={$iAdminId} && permissions='cup' ";
		$qre = $gl_oVars->cDBInterface->Query( $sql_query );*/
		
		
		if( $cAdministrator->DeleteAdminPermission($iAdminPermissionId) )
		{
			$gl_oVars->cTpl->assign( 'success', 	true );
			$gl_oVars->cTpl->assign( 'msg_type', 	'success' );
			$gl_oVars->cTpl->assign( 'msg_title', 	'Admin entfernt' );
			$gl_oVars->cTpl->assign( 'msg_text', 	'Der Administrator wurde erfolgreich aus der Rechteliste des Cups entfernt.' );
			
			PageNavigation::Location( $gl_oVars->sURLFile.'?page='.$gl_oVars->sURLRealPage.'&ladder_id='.$oLadder->id );
		}
		else
		{
			$gl_oVars->cTpl->assign( 'msg_type', 	'error' );
			$gl_oVars->cTpl->assign( 'msg_title', 	'Fehler' );
			$gl_oVars->cTpl->assign( 'msg_text', 	'Der Administrator konnte nicht aus der Rechteliste des Cups entfernt werden.' );
		}
	}
	# ---------------------------------------------------------------------
	# A D D   A D M I N (only permissions) 
	# ---------------------------------------------------------------------
	elseif( $_GET['a'] == 'add_admin' )
	{
		// add by Admin-ID
		$iAdminId = (int)$_POST['admin_id'];
		

		$oAdmin = $cAdministrator->GetAdmin( $iAdminId );
		if( $oAdmin )
		{
			// define object
			$add_obj =  array(	"admin_id"		=> $iAdminId,
								"permissions" 	=> "ladder",
								"module_id"		=> MODULEID_INETOPIA_LADDER,
								"data"			=> $oLadder->id );
			// admin exists?
			$oCupAdminPermission = $gl_oVars->cDBInterface->FetchObject( $gl_oVars->cDBInterface->Query($gl_oVars->cDBInterface->CreateSelectQuery( $GLOBALS['g_egltb_admin_permissions'], $add_obj) ));
			$add_obj["created"]	= EGL_TIME;
			
			// execute query
			if( !$oCupAdminPermission )
			{
				$sql_query = $gl_oVars->cDBInterface->CreateInsertQuery( $GLOBALS['g_egltb_admin_permissions'], $add_obj );
				$qre = $gl_oVars->cDBInterface->Query( $sql_query );
				if( $qre )
				{
					
					$gl_oVars->cTpl->assign( 'success', 	true );
					$gl_oVars->cTpl->assign( 'msg_type', 	'success' );
					$gl_oVars->cTpl->assign( 'msg_title', 	'Admin hinzugef�gt' );
					$gl_oVars->cTpl->assign( 'msg_text', 	'Der Administrator wurde erfolgreich hinzugef�gt.' );
					
					PageNavigation::Location( $gl_oVars->sURLFile.'?page='.$gl_oVars->sURLRealPage.'&ladder_id='.$oLadder->id );
				}
				else
				{
					$gl_oVars->cTpl->assign( 'msg_type', 	'error' );
					$gl_oVars->cTpl->assign( 'msg_title', 	'Fehler' );
					$gl_oVars->cTpl->assign( 'msg_text', 	'Der Administrator konnte nicht der Rechteliste hinzugef�gt werden.' );
				}
			}
			else
			{
				$gl_oVars->cTpl->assign( 'msg_type', 	'error' );
				$gl_oVars->cTpl->assign( 'msg_title', 	'Fehler' );
				$gl_oVars->cTpl->assign( 'msg_text', 	'Die Rechte wurden bereits f�r diesen Admin vergeben' );
			}

		}
		else
		{
			$gl_oVars->cTpl->assign( 'msg_type', 	'error' );
			$gl_oVars->cTpl->assign( 'msg_title', 	'Fehler' );
			$gl_oVars->cTpl->assign( 'msg_text', 	'Die Administrator-ID existiert nicht.' );
		}

	}
		
	
	
	if( $oLadder )$aLadderAdministrator = $cLadderSystem->GetLadderAdministrator( $iLadderId );
	if( $oLadder )$aAdministrator = $cAdministrator->GetAdminList();
	
	
	
	$gl_oVars->cTpl->assign( 'adminlist', $aAdministrator );
	$gl_oVars->cTpl->assign( 'ladderadministrator', $aLadderAdministrator );
	
	
	# provide template with data
	$gl_oVars->cTpl->assign( 'ladder', 	$oLadder );
	$gl_oVars->cTpl->assign( 'game', $oGame );
?>