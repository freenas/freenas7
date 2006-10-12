<?
/*
    filechooser.php
    Copyright © 2006 Volker Theile (votdev@gmx.de)
    All rights reserved.

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are met:

    1. Redistributions of source code must retain the above copyright notice,
       this list of conditions and the following disclaimer.

    2. Redistributions in binary form must reproduce the above copyright
       notice, this list of conditions and the following disclaimer in the
       documentation and/or other materials provided with the distribution.

    THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
    INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
    AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
    AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
    OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
    SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
    INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
    CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
    ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
    POSSIBILITY OF SUCH DAMAGE.
*/
require("guiconfig.inc");

class FileChooser
{
	var	$cfg = array();

	function FileChooser()
	{
    // Settings.
		$this->cfg['id'] = 'FileBrowser'; // html DOM id
		$this->cfg['footer'] = true; // show footer
		$this->cfg['sort'] = true; // show sorting header
		$this->cfg['lineNumbers'] = true; // show/hide column
		$this->cfg['showFileSize'] = true; // show/hide column
		$this->cfg['showFileModDate'] = true; // show/hide column
		$this->cfg['showFileType'] = true; // show/hide column
		$this->cfg['calcFolderSizes'] = false; // calculate folder sizes (increases processing time)
		$this->cfg['simpleType'] = true; // display MIME type, or "simple" file type (MIME type increases processing time)
		$this->cfg['separateFolders'] = true; // sort folders on top of files
		$this->cfg['naturalSort'] = true; // natural sort files, as opposed to regular sort (files with capital letters get sorted first)
		$this->cfg['filterShowFiles'] = '*';
    $this->cfg['filterHideFiles'] = '.*';
    $this->cfg['filterShowFolders'] = '*';
    $this->cfg['filterHideFolders'] = '.,..,.*';
		$this->cfg['dateFormat'] = 'F d, Y g:i A'; // date format.

		// Get path if browsing a tree.
		$path = (isset($_GET['p'])) ? $_GET['p'] : FALSE;

    // Check if file exists.
    if(!file_exists($path)) {
      print_info_box("File not found <{$path}>");
      $path = $this->get_parent_dir($path);
    }

    // If no path is available, set it to root.
    if(!$path) {
      $path = "/";
    }

    $dir = $path;

		// Extract path if necessary.
    if(is_file($dir)) {
      $dir = dirname($dir).'/';
    }

		// Get sorting vars from URL, if nothing is set, sort by N [file Name].
		$this->cfg['sortMode'] = (isset($_GET['N']) ? 'N' :
			        				 (isset($_GET['S']) ? 'S' :
			         				 (isset($_GET['T']) ? 'T' :
			         				 (isset($_GET['M']) ? 'M' : 'N' ))));

		// Get sort ascending or descending.
		$this->cfg['sortOrder'] =
			isset($_GET[$this->cfg['sortMode']]) ?
			$_GET[$this->cfg['sortMode']] : 'A';

		// Create array of files in tree.
		$files = $this->make_file_array($dir);

		// Get size of arrays before sort.
		$totalFolders = sizeof($files['folders']);
		$totalFiles = sizeof($files['files']);

		// Sort files.
		$files = $this->sort_files($files);

    // Display address bar.
    echo $this->address_bar($path);

		// Display file list.
		echo $this->file_list($dir, $files);

    // Display command bar.
		echo $this->command_bar();
	}

  function make_file_array($dir)
	{
		if(!function_exists('mime_content_type'))
		{
		   function mime_content_type($file)
		   {
			   $file = escapeshellarg($file);
			   $type = `file -bi $file`;
		   	   $expl = explode(";", $type);
		   	   return $expl[0];
		   }
		}

		$dirArray	= array();
		$folderArray = array();
		$folderInfo = array();
		$fileArray = array();
		$fileInfo = array();
		
		$content = $this->get_content($dir);

		foreach($content as $file)
    {
			if(is_dir($dir.$file)) // is a folder
			{
				// store elements of folder in sub array
				$folderInfo['name']	= $file;
				$folderInfo['mtime'] = @filemtime($dir.$file);
				$folderInfo['type'] = 'Folder';
				// calc folder size ?
				$folderInfo['size'] =
					$this->cfg['calcFolderSizes'] ?
					$this->get_folder_size($dir.$file) :
					'-';
				$folderInfo['rowType'] = 'fr';
				$folderArray[] = $folderInfo;
			}
			else // is a file
			{
				// store elements of file in sub array
				$fileInfo['name'] = $file;
				$fileInfo['mtime'] = @filemtime($dir.$file);
				$fileInfo['type'] = $this->cfg['simpleType'] ?
					$this->get_extension($dir.$file) :
					mime_content_type($dir.$file);
				$fileInfo['size'] = @filesize($dir.$file);
				$fileInfo['rowType'] = 'fl';
				$fileArray[] = $fileInfo;
			}
		}

		$dirArray['folders'] = $folderArray;
		$dirArray['files'] = $fileArray;

		return $dirArray;
  }

  function get_content($dir)
  {
    $folders = array();
    $files = array();
     
    $handle = @opendir($dir);
    while($file = @readdir($handle)) {
      if(is_dir("$dir/$file"))
        $folders[] = $file;
      elseif(is_file("$dir/$file"))
        $files[] = $file;
    }
    @closedir($handle);

    $folders = $this->filter_content($folders, $this->cfg['filterShowFolders'], $this->cfg['filterHideFolders']);
    $files = $this->filter_content($files, $this->cfg['filterShowFiles'], $this->cfg['filterHideFiles']);

    return array_merge($folders, $files);
  }

  function filter_content($arr, $allow, $hide)
  {
    $allow = $this->make_regex($allow);
    $hide = $this->make_regex($hide);
  
    $ret = array();
    $ret = preg_grep("/$allow/", $arr);
    $ret = preg_grep("/$hide/",  $ret, PREG_GREP_INVERT);
  
    return $ret;
  }
  
  function make_regex($filter)
  {
    $regex = str_replace('.', '\.', $filter);
    $regex = str_replace('/', '\/', $regex);
    $regex = str_replace('*', '.+', $regex);
    $regex = str_replace(',', '$|^', $regex);
    return "^$regex\$";
  }

  function get_extension($filename)
	{
		$justfile = explode("/", $filename);
		$justfile = $justfile[(sizeof($justfile)-1)];
    	$expl = explode(".", $justfile);
		if(sizeof($expl)>1 && $expl[sizeof($expl)-1])
		{
    		return $expl[sizeof($expl)-1];
    	}
		else
		{
			return '?';
		}
	}

  function get_parent_dir($dir)
	{
  	$expl = explode("/", substr($dir, 0, -1));
  	return  substr($dir, 0, -strlen($expl[(sizeof($expl)-1)].'/'));
  }

	function format_size($bytes)
	{
		if(is_integer($bytes) && $bytes > 0)
		{
			$formats = array("%d bytes","%.1f kb","%.1f mb","%.1f gb","%.1f tb");
			$logsize = min((int)(log($bytes)/log(1024)), count($formats)-1);
			return sprintf($formats[$logsize], $bytes/pow(1024, $logsize));
		}
		// is a folder without calculated size
		else if(!is_integer($bytes) && $bytes == '-')
		{
			return '-';
		}
		else
		{
			return '0 bytes';
		}
	}

	function get_folder_size($dir)
	{
		$size = 0;
		if ($handle = opendir($dir))
		{
			while (false !== ($file = readdir($handle)))
			{
				if ($file != '.' && $file != '..')
				{
					if(is_dir($dir.'/'.$file))
					{
						$size += $this->get_folder_size($dir.'/'.$file);
					}
					else
					{
						$size += filesize($dir.'/'.$file);
					}
				}
			}
		}
		return $size;
	}

	function sort_files($files)
	{
		// sort folders on top
		if($this->cfg['separateFolders'])
		{
			$sortedFolders = $this->order_by_column($files['folders'], '2');

			$sortedFiles = $this->order_by_column($files['files'], '1');

			// sort files depending on sort order
			if($this->cfg['sortOrder'] == 'A')
			{
				ksort($sortedFolders);
				ksort($sortedFiles);
				$result = array_merge($sortedFolders, $sortedFiles);
			}
			else
			{
				krsort($sortedFolders);
				krsort($sortedFiles);
				$result = array_merge($sortedFiles, $sortedFolders);
			}
		}
		else
		// sort folders and files together
		{
			$files = array_merge($files['folders'], $files['files']);
			$result = $this->order_by_column($files,'1');

			// sort files depending on sort order
			$this->cfg['sortOrder'] == 'A' ? ksort($result):krsort($result);
		}
		return $result;
	}

	function order_by_column($input, $type)
	{
		$column = $this->cfg['sortMode'];

		$result = array();

		// available sort columns
		$columnList = array('N'=>'name',
							'S'=>'size',
							'T'=>'type',
							'M'=>'mtime');

		// row count
		// each array key gets $rowcount and $type
		// concatinated to account for duplicate array keys
		$rowcount = 0;

		// create new array with sort mode as the key
		foreach($input as $key=>$value)
		{
			// natural sort - make array keys lowercase
			if($this->cfg['naturalSort'])
			{
				$col = $value[$columnList[$column]];
				$res = strtolower($col).'.'.$rowcount.$type;
				$result[$res] = $value;
			}
			// regular sort - uppercase values get sorted on top
			else
			{
				$res = $value[$columnList[$column]].'.'.$rowcount.$type;
				$result[$res] = $value;
			}
			$rowcount++;
		}
		return $result;
	}

	function file_list($dir, $files)
	{
    $ret  = '<div id="'.$this->cfg['id'].'">';
    $ret .= '<table class="filelist" cellspacing="0" border="0">';
		$ret .= ($this->cfg['sort']) ? $this->row('sort', $dir) : '';
		$ret .= ($this->get_parent_dir($dir)) ? $this->row('parent', $dir) : '';

		// total number of files
		$rowcount  = 1;
		// total byte size of the current tree
		$totalsize = 0;

		// rows of files
		foreach($files as $file)
		{
			$ret .= $this->row($file['rowType'], $dir, $rowcount, $file);
			$rowcount++;
			$totalsize += $file['size'];
		}

		$this->cfg['totalSize'] = $this->format_size($totalsize);

		$ret .= ($this->cfg['footer']) ? $this->row('footer') : '';

		$ret .= '</table>';
		$ret .= '</div>';
		
		return $ret;
	}

	function row($type, $dir=null, $rowcount=null, $file=null)
	{
		// alternating row styles
		$rnum = $rowcount ? ($rowcount%2 == 0 ? ' r2' : ' r1') : null;

		// start row string variable to be returned
		$row = "\n".'<tr class="'.$type.$rnum.'">'."\n";

		switch($type)
		{
			// file / folder row
			case 'fl' :
			case 'fr' :
				// line number
				$row .= $this->cfg['lineNumbers'] ?
				        '<td class="ln">'.$rowcount.'</td>' : '';

				// filename
				$row .= '<td class="nm"><a href="';
				$row .= $type == 'fr' ? '?p='.$dir.$file['name'].'/' : '?p='.$dir.$file['name'];
				$row .= '">'.$file['name'].'</a></td>';

				// file size
				$row .= $this->cfg['showFileSize'] ?
				        '<td class="sz">'.$this->format_size($file['size']).'
				         </td>' : '';

				// file type
				$row .= $this->cfg['showFileType'] ?
				        '<td class="tp">'.$file['type'].'</td>' : '';

				// date
				$row .= $this->cfg['showFileModDate'] ?
				        '<td class="dt">
				        '.date($this->cfg['dateFormat'], $file['mtime']).'
				         </td>' : '';
				break;

			// sorting header
			case 'sort' :
				// sort order. Setting ascending or descending for sorting links
				$N = ($this->cfg['sortMode'] == 'N') ?
					 ($this->cfg['sortOrder'] == 'A' ? 'D' : 'A') : 'A';

				$S = ($this->cfg['sortMode'] == 'S') ?
					 ($this->cfg['sortOrder'] == 'A' ? 'D' : 'A') : 'A';

				$T = ($this->cfg['sortMode'] == 'T') ?
					 ($this->cfg['sortOrder'] == 'A' ? 'D' : 'A') : 'A';

				$M = ($this->cfg['sortMode'] == 'M') ?
					 ($this->cfg['sortOrder'] == 'A' ? 'D' : 'A') : 'A';

				$row .= $this->cfg['lineNumbers'] ?
				        '<td class="ln">&nbsp;</td>' : '';
				$row .= '<td><a href="?N='.$N.'&amp;p='.$dir.'">Name</a></td>';
				$row .= $this->cfg['showFileSize'] ?
					    '<td class="sz">
						 <a href="?S='.$S.'&amp;p='.$dir.'">Size</a>
						 </td>' : '';
				$row .= $this->cfg['showFileType'] ?
				        '<td class="tp">
				         <a href="?T='.$T.'&amp;p='.$dir.'">Type</a>
				         </td>' : '';
				$row .= $this->cfg['showFileModDate'] ?
					    '<td class="dt">
					     <a href="?M='.$M.'&amp;p='.$dir.'">Last Modified</a>
					     </td>' : '';
				break;

			// parent directory row
			case 'parent' :
				$row .= $this->cfg['lineNumbers'] ?
				        '<td class="ln">&laquo;</td>' : '';
				$row .= '<td class="nm">
				         <a href="?p='.$this->get_parent_dir($dir).'">';
				$row .= 'Parent Directory';
				$row .= '</a></td>';
				$row .= $this->cfg['showFileSize'] ?
				        '<td class="sz">&nbsp;</td>' : '';
				$row .= $this->cfg['showFileType'] ?
				        '<td class="tp">&nbsp;</td>' : '';
				$row .= $this->cfg['showFileModDate'] ?
				        '<td class="dt">&nbsp;</td>' : '';
				break;

			// footer row
			case 'footer' :
				$row .= $this->cfg['lineNumbers'] ?
				        '<td class="ln">&nbsp;</td>' : '';
				$row .= '<td class="nm">&nbsp;</td>';
				$row .= $this->cfg['showFileSize'] ?
				        '<td class="sz">'.$this->cfg['totalSize'].'
				         </td>' : '';
				$row .= $this->cfg['showFileType'] ?
				        '<td class="tp">&nbsp;</td>' : '';
				$row .= $this->cfg['showFileModDate'] ?
				        '<td class="dt">&nbsp;</td>' : '';
				break;
		}

		$row .= '</tr>';
		return $row;
	}

  function address_bar($path)
	{
    $ret .= <<<EOD

  <form method="get" action="?">
    <div id="addrBar">
      <tr>
        <input name="p" value="{$path}" type="text">
      </tr>
    </div>
  </form>

EOD;
    return $ret;
  }

	function command_bar()
	{
    $ret = <<<EOD

  <form method="get" onSubmit="onSubmit();" onReset="onReset();">
    <div id="cmdBar">
      <tr>
        <input type="submit" value="Ok">
        <input type="reset" value="Cancel">
      </tr>
    </div>
  </form>

EOD;
    return $ret;
  }
}
?>
<html>
  <head>
<style type="text/css">
#fileBrowser { }
/* File Browser Table */
#fileBrowser table { width:100%; }
/* rows */
#fileBrowser table tr td { padding:1px; font-size:12px; }
#fileBrowser a { text-decoration:none; }
#fileBrowser a:hover { text-decoration:underline; }
/* rows */
#fileBrowser table tr.fr td,
#fileBrowser table tr.fl td { border-top:1px solid #fff; border-bottom:1px solid #ddd; }
/* folder row */
#fileBrowser table tr.fr td.nm { font-weight:bold; }
/* parent row */
#fileBrowser table tr.parent { font-weight:bold; }
#fileBrowser table tr.parent td { border-bottom:1px solid #eee; background:#efefd3; }
/* sorting row */
#fileBrowser tr.sort td {  }
/* Columns */
/* line number */
#fileBrowser table tr td.ln { border-left:1px solid #eee; font-weight:normal; text-align:right; padding:0 10px 0 10px; width:10px; color: #999; }
/* date  */
#fileBrowser table tr td.dt { border-right:1px solid #eee; }
/* footer row */
#fileBrowser table tr.footer td { border:0; font-weight:bold; }
/* sort row */
#fileBrowser table tr.sort td { border:0; border-bottom:1px solid #eee; }
/* alternating Row Colors */
/* folders */
tr.fr.r1 { background-color:#eee; }
tr.fr.r2 { }
/* files */
tr.r1 { background-color:#eee; }
tr.r2 { }
#cmdBar { padding:2px 3px; text-align:right; border-left:1px solid #eee; border-right:1px solid #eee; border-bottom:1px solid #eee; border-top:1px solid #fff; background-color:#eee; border-spacing:0; }
#addrBar { padding:2px 3px; text-align:right; border-left:1px solid #eee; border-right:1px solid #eee; border-bottom:1px solid #eee; border-top:1px solid #fff; background-color:#eee; border-spacing:0; }
#addrbar input { width:100%; }
</style>
<script class="javascript">
function onSubmit()
{
  opener.ifield.value = document.forms[0].p.value;
  close();
}
function onReset()
{
  close();
}
</script>
  </head>
  <body>
<?
	new FileChooser();
?>
  </body>
</html>
