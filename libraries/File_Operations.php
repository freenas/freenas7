<?php
/** ensure this file is being included by a parent file */
if( !defined( '_JEXEC' ) && !defined( '_VALID_MOS' ) ) die( 'Restricted access' );
/**
 * This file allows to dynamically switch between file.system based mode and FTP based mode
 */

require_once( dirname(__FILE__).'/FTP.php');
if( !extension_loaded('ftp')) {
	require_once( dirname(__FILE__).'/FTP/Socket.php');	
}

function ext_isFTPMode() {	
	return $GLOBALS['file_mode'] == 'ftp';
}
/**
 * This class is a wrapper for all of the needed filesystem functions.
 * It allows us to use the same function name for FTP and File System Mode
 *
 */
class ext_File {
	function chmod( $item, $mode ) {
		if( ext_isFTPMode() ) {
			if( !empty( $item['name'])) {
				$item = $item['name'];
			}
			return $GLOBALS['FTPCONNECTION']->chmod( $item, $mode );
		}
		else {
			return @chmod( utf8_decode($item), $mode );
		}
	}
	
	function chmodRecursive( $item, $mode ) {
		if( ext_isFTPMode() ) {
			return $GLOBALS['FTPCONNECTION']->chmodRecursive( $item, $mode );
		}
		else {
			return chmod_recursive( utf8_decode($item), $mode );
		}
	}
	function copy( $from, $to ) {
		if( ext_isFTPMode() ) {
			
			$fh = ext_ftp_make_local_copy( $from, true );
			$res = $GLOBALS['FTPCONNECTION']->fput( $fh, $to );
			
			fclose( $fh );
			
			return $res;
		}
		else {
			return copy( utf8_decode($from), utf8_decode($to) );
		}
	}
	function copy_dir($abs_item, $abs_new_item) {
		if( ext_isFTPMode() ) {
			$tmp_dir = ext_ftp_make_local_copy( $abs_item );
			$res = $GLOBALS['FTPCONNECTION']->putRecursive( $tmp_dir, $abs_new_item );
			remove( $tmp_dir );
			return $res;
		}
		else {
			return copy_dir($abs_item,$abs_new_item);
		}
	}
	function mkdir( $dir, $perms ) {
		if( ext_isFTPMode() ) {
			$res = $GLOBALS['FTPCONNECTION']->mkdir( $dir );
			return $res;
		}
		else {
			return mkdir( utf8_decode($dir), $perms );
		}
	}
	function mkfile( $file ) {
		if( ext_isFTPMode() ) {
			$tmp = tmpfile();
			return $GLOBALS['FTPCONNECTION']->fput( $tmp, $file );
		}
		else {
			return @touch( utf8_decode($file) );
		}
	}
	function unlink( $item ) {
		if( ext_isFTPMode() ) {
			return $GLOBALS['FTPCONNECTION']->rm( $item );
		}
		else {
			return unlink( utf8_decode($item) );
		}
	}
	
	function rmdir( $dir ) {
		if( ext_isFTPMode() ) {
			return $GLOBALS['FTPCONNECTION']->rm( $item );
		}
		else {
			return rmdir( $dir );
		}
	}
	function remove( $item ) {
		if( ext_isFTPMode() ) {
			return $GLOBALS['FTPCONNECTION']->rm( $item, true );
		}
		else {
			return remove( utf8_decode($item) );
		}
	}
	function rename( $oldname, $newname ) {
		if( ext_isFTPMode() ) {
			if( is_array( $oldname )) {
				$oldname = $oldname['name'];
			}
			return $GLOBALS['FTPCONNECTION']->rename( $oldname, $newname );
		}
		else {
			return rename( utf8_decode($oldname), utf8_decode($newname) );
		}
	}
	function opendir( $dir ) {
		if( ext_isFTPMode() ) {
			return getCachedFTPListing( $dir );
		}
		else {
			return opendir( utf8_decode($dir) );
		}
	}
	function readdir( &$handle ) {
		if( ext_isFTPMode() ) {
			$current = current($handle );next( $handle );
			return $current;
		}
		else {
			return readdir( $handle );
		}
	}
	function scandir( $dir ) {
		if( ext_isFTPMode() ) {
			return getCachedFTPListing( $dir );
		}
		else {
			return scandir( $dir );
		}
	}
	function closedir( &$handle ) {		
		if( ext_isFTPMode() ) {
			return;
		}
		else {
			return closedir( $handle );
		}
	}
	function file_exists( $file ) {
		if( ext_isFTPMode() ) {
			if( $file == '/' ) return true; // The root directory always exists
			
			$dir = $GLOBALS['FTPCONNECTION']->pwd();
			if( !is_array( $file )) {
				$dir = dirname( $file );
				$file = array( 'name' => basename( $file ) );
			}
			$list = getCachedFTPListing( $dir );
			
			if( is_array( $list )) {
				
				foreach( $list as $item ) {
					if( $item['name'] == $file['name'] )
						return true;
				}
			}
			return false;
		}
		else {
			return file_exists( utf8_decode($file) );
		}
	}
	function filesize( $file ) {
		if( ext_isFTPMode() ) {
			if( isset( $file['size'])) {
				return ( $file['size']);
			}
			return $GLOBALS['FTPCONNECTION']->size( $file );
		}
		else {
			//return filesize( $file);
			return @exec("stat -f %z " . escapeshellarg($file));
		}
	}
	function fileperms( $file ) {
		if( ext_isFTPMode() ) {
			if( isset( $file['rights'])) {
				$perms = $file['rights'];
			} else {
				$info = get_item_info( dirname( $file), basename( $file ) );
				$perms = $info['rights'];
			}
			return decoct(bindec(decode_ftp_rights( $perms )));
		}
		else {
			return @fileperms( $file);
		}		
	}
	function filemtime( $file ) {
		if( ext_isFTPMode() ) {
			if( isset( $file['stamp'])) {
				return $file['stamp'];
			}
			$res = $GLOBALS['FTPCONNECTION']->mdtm( $file['name'] );
			if( !PEAR::isError( $res )) {
				return $res;
			}
		}
		else {
			return filemtime( $file);
		}
	}
	function move_uploaded_file( $uploadedfile, $to ) {
		if( ext_isFTPMode() ) {
			if( is_array( $uploadedfile )) {
				$uploadedfile = $uploadedfile['name'];
			}
			$uploadedfile = str_replace( "\\", '/', $uploadedfile );
			$to = str_replace( "\\", '/', $to );
			$res = $GLOBALS['FTPCONNECTION']->put( $uploadedfile, $to );
			return $res;
			
		}
		else {
			return move_uploaded_file( $uploadedfile, $to );
		}
	}
	function file_get_contents( $file ) {
		if( ext_isFTPMode() ) {
			$fh = tmpfile();
			
			$file = str_replace( "\\", '/', $file );
			if( $file[0] != '/' ) $file = '/'. $file; 
			$res = $GLOBALS['FTPCONNECTION']->fget( $file, $fh );
			if( PEAR::isError( $res )) {
				return false;
			}
			else {
				rewind( $fh );
				$contents = '';
				while( !feof( $fh)) {
					$contents .= fread( $fh, 2048 );
				}
				fclose( $fh );
				return $contents;
			}
		}
		else {
			return file_get_contents( utf8_decode($file) );
		}
	}
	function file_put_contents( $file, $data ) {
		if( ext_isFTPMode() ) {
			$tmp_file = tmpfile();
			fputs( $tmp_file, $data );
			rewind( $tmp_file );
			$res = $GLOBALS['FTPCONNECTION']->fput( $tmp_file, $file, true );
			
			fclose( $tmp_file );
			return $res;
		} 
		else {
			return file_put_contents( utf8_decode($file), $data );
		}
	}
	function fileowner( $file ) {
		if( ext_isFTPMode() ) {
			$info = posix_getpwnam( $file['user'] );
			return $info['uid'];
		}
		else {
			return fileowner( $file );
		}
	}
	function geteuid() {
		if( ext_isFTPMode() ) {
			$info = posix_getpwnam( $_SESSION['ftp_login'] );
			return $info['uid'];			
		}
		else {
			return posix_geteuid();
		}
	}
	function is_link( $abs_item ) {
		if( ext_isFTPMode() ) {
			return false;
		} else {
			return is_link( $abs_item );
		}
	}
	function is_writable( $file ) {
		global $isWindows;
		if( ext_isFTPMode() ) {
			if( $isWindows ) return true;
			if( !is_array( $file )) {
				$file = get_item_info(dirname($file), basename($file));
			}
			if( empty($file['rights'])) return true;
			$perms = $file['rights'];
			if( $_SESSION['ftp_login'] == $file['user']) {
				// FTP user is owner of the file
				return $perms[1] == 'w';
			}
			$fileinfo = posix_getpwnam( $file['user'] );
			$userinfo = posix_getpwnam( $_SESSION['ftp_login'] );
			
			if( $fileinfo['gid'] == $userinfo['gid']) {
				return $perms[4] == 'w';
			}
			else {
				return $perms[7] == 'w';
			}
		}
		else {
			return is_writable( $file );
		}
	}
	function is_readable( $file ) {
		if( ext_isFTPMode() ) {
			$perms = $file['rights'];
			if( $_SESSION['ftp_login'] == $file['user']) {
				// FTP user is owner of the file
				return $perms[0] == 'r';
			}
			$fileinfo = posix_getpwnam( $file['user'] );
			$userinfo = posix_getpwnam( $_SESSION['ftp_login'] );
			
			if( $fileinfo['gid'] == $userinfo['gid']) {
				return $perms[3] == 'r';
			}
			else {
				return $perms[6] == 'r';
			}
		}
		else {
			return is_readable( $file );
		}
	}
	/**
	 * determines if a file is deletable based on directory ownership, permissions,
	 * and php safemode.
	 * 
	 * @param string $dir The full path to the file
	 * @return boolean
	 */
	function is_deletable( $file ) {
		global $isWindows;
	
		// Note that if the directory is not owned by the same uid as this executing script, it will
		// be unreadable and I think unwriteable in safemode regardless of directory permissions.
		if(ini_get('safe_mode') == 1 && @$GLOBALS['ext_File']->geteuid() != $GLOBALS['ext_File']->fileowner($file)) {
			return false;
		}
	
		// if dir owner not same as effective uid of this process, then perms must be full 777.
		// No other perms combo seems reliable across system implementations
		
		if(!$isWindows && @$GLOBALS['ext_File']->geteuid() !== @$GLOBALS['ext_File']->fileowner($file)) {
			return (substr(decoct(@fileperms($file)),-3) == '777' || @is_writable(dirname($file)) );
		}
		if($isWindows && $GLOBALS['ext_File']->geteuid() != $GLOBALS['ext_File']->fileowner($file)) {
			return (substr(decoct(fileperms($file)),-3) == '777');
		}
		// otherwise if this process owns the directory, we can chmod it ourselves to delete it
		return @is_writable(dirname($file));
	}
	
	function is_chmodable( $file ) {
		global $isWindows;
		
		if( $isWindows ) {
			return true;
		}
		if( ext_isFTPMode() ) {
			return $_SESSION['ftp_login'] == $file['user'];
		} else {
			return @$GLOBALS['ext_File']->fileowner( $file ) == @$GLOBALS['ext_File']->geteuid();
		}

	}
}

function ext_ftp_make_local_copy( $abs_item, $use_filehandle=false ) {

	if( get_is_dir( $abs_item )) {
		$tmp_dir = _EXT_FTPTMP_PATH.'/'.uniqid('ext_tmpdir_').'/';
		$res = $GLOBALS['FTPCONNECTION']->getRecursive( $abs_item, $tmp_dir, true );
		if( PEAR::isError( $res )) {
			ext_Result::sendResult( 'list', false, 'Failed to fetch the directory via FTP: '.$res->getMessage() );
		}
		return $tmp_dir;
	}
	
	$abs_item = str_replace( "\\", '/', $abs_item );
	if( $abs_item[0] != '/' ) $abs_item = '/'. $abs_item; 
	
	if( !$use_filehandle ) {
		$tmp_file = tempnam( _EXT_FTPTMP_PATH, 'ext_ftp_dl_' );
	
		if( $tmp_file == 'false') {
			ext_Result::sendResult( 'list', false, 'The /ftp_tmp Directory must be writable in order to use this functionality in FTP Mode.');
		}
		$res = $GLOBALS['FTPCONNECTION']->get( $abs_item, $tmp_file, true );
		if( PEAR::isError( $res )) {
			ext_Result::sendResult( 'list', false, 'Failed to fetch the file via filehandle from FTP: '.$res->getMessage() );
		}
	}
	else {
		$tmp_file = tmpfile();
	
		$res = $GLOBALS['FTPCONNECTION']->fget( '/'.$abs_item, $tmp_file, true );
		if( PEAR::isError( $res )) {
			ext_Result::sendResult( 'list', false, 'Failed to fetch the file via FTP: '.$res->getMessage() );
		}
		rewind( $tmp_file );
	}
	return $tmp_file;
	
}

function &getCachedFTPListing( $dir, $force_refresh=false ) {
	if( $dir == '\\') $dir = '.';
	$dir = str_replace( '\\', '/', $dir );
	if( $dir != '' && $dir[0] != '/') {
		$dir = '/'.$dir;
	}
	
	$dir = str_replace($GLOBALS['home_dir'], '', $dir);
	
	if( empty( $GLOBALS['ftp_ls'][$dir] ) || $force_refresh ) {
		if( $dir == $GLOBALS['FTPCONNECTION']->pwd() ) {
			$dir = '';
		}
		$GLOBALS['ftp_ls'][$dir] = $GLOBALS['FTPCONNECTION']->ls( empty($dir) ? '.' : $dir );
		if( PEAR::isError( $GLOBALS['ftp_ls'][$dir] )) {
			//ext_Result::sendResult( 'list', false, $GLOBALS['ftp_ls'][$dir]->getMessage().': '.$dir);
		}
	}

	return $GLOBALS['ftp_ls'][$dir];
}
?>