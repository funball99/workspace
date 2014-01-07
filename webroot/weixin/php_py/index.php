<?php
require 'socketapi.php';
header("Content-type: text/html; charset=utf-8");
$s = new server('192.168.1.8', 1990);
$code = <<<EOT
/*   美化：格式化代码，使之容易阅读			*/
/*   净化：去掉代码中多余的注释、换行、空格等	*/
/*   压缩：将代码压缩为更小体积，便于传输		*/
/*   解压：将压缩后的代码转换为人可以阅读的格式	*/

/*   如果有用，请别忘了推荐给你的朋友：		*/
/*   javascript在线美化、净化、压缩、解压：http://box.inote.cc/js   */

/*   以下是演示代码				*/

    var getPositionLite = function(el) {        var x = 0,        y = 0;        while (el) {            x += el.offsetLeft || 0;            y += el.offsetTop || 0;            el = el.offsetParent        }        return {            x: x,            y: y        }    };
/*   更新记录：					*/
    var history = {
    	'v1.0':	['2011-01-18','javascript工具上线']
    };
EOT;
$res = $s->obj('Todo')->test($code);
echo '<pre>'.$res.'</pre>';

/*//php并发
$read = $write = $sockets;
$n = socket_select($read, $write, $except = null, 0);
if ($n > 0) {
	foreach($read as $r) {
		$id = array_search($r, $sockets);
		
		$res = socket_read($r, 1024, PHP_BINARY_READ);
		$data = substr($res, 8);
		$len = intval(substr($res, 0, 8));
 
		while(true){
			if($len != strlen($data)){
				$data .= socket_read($socket, 1024, PHP_BINARY_READ);
			}else{
				break;
			}
		}
		echo $data;
	}
}
*/