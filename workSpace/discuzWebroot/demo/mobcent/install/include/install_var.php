<?php
//define('CONFIG', '../../config/config_global.php');


function checkUrl($action)
{
	$submit_url = array('checkFile','checkDatabase','finish');
	foreach ($submit_url as $key=>$v)
	{
		if($v == $action)
		{
			$step =$key;
			if($step ==0)
			{
				$step = $step+1;
				$url = "<td>上一步</td><td><a href='index.php?action=".$submit_url[$step]."'>下一步</a></td>";
			}
			else if($step == count($submit_url) - 1)
			{
				$url = "<td><a href='index.php?action=".$submit_url[$step-1]."'>上一步</a></td><td>下一步</td>";
			}
			else
			{
				$url = "<td><a href='index.php?action=".$submit_url[$step-1]."'>上一步</a></td><td><a href='index.php?action=".$submit_url[$step+1]."'>下一步</a></td>";
			}
		}

	}
	return $url;
}
function dir_writeable($dir)
{
	
	$writeable = 0;
	if(!is_dir($dir))
	{
		@mkdir($dir,0777);
	}
	if($dh = @fopen($dir.'/text.txt', 'w'))
	{

		fclose($dh);
		unlink($dir.'/text.txt');
		$writeable = 1;
	}
	else
	{
		$writeable = 0;
	}
	return $writeable;
}
function dirfile_check(&$dirfile_items)
{
	foreach($dirfile_items as $key=>$path)
	{
		$item_path = $path['path'];
		if($path['type'] == 'dir')
		{
			if(!dir_writeable($item_path))
			{
				if(is_dir($item_path))
				{
					$dirfile_items[$key]['status'] = '0';
					$dirfile_items[$key]['current'] = '+r';
				}
				else
				{ 
					$dirfile_items[$key]['status'] = '-1';
					$dirfile_items[$key]['current'] = 'nofile';
				}

			}
			else
			{
				$dirfile_items[$key]['status'] = '1';
				$dirfile_items[$key]['current'] = '+r+w';
			}
		}
		else
		{
			if(file_exists($item_path))
			{
				if(is_writable($item_path))
				{
					$dirfile_items[$key]['status'] = '1';
					$dirfile_items[$key]['current'] = '+r+w';
				}
				else
				{
					$dirfile_items[$key]['status'] = '0';
					$dirfile_items[$key]['current'] = '+r';
				}
			}
			else
			{
				if(dir_writeable(dirname($item_path))) {
					$dirfile_items[$key]['status'] = 1;
					$dirfile_items[$key]['current'] = '+r+w';
				} else {  
					$dirfile_items[$key]['status'] = -1;
					$dirfile_items[$key]['current'] = 'nofile';
				}
			}
		}
	}
	return $dirfile_items;
}

function checkFile($file)
{
	$arr = explode('+',$file);
	$arr = array_filter($arr);
	$arr = array_values($arr);
	if(count($arr) > 1)
	{
		return true;
	}
	else if(count($arr) == 1 && $arr[0] ='r')
	{
		return false;
	}
}