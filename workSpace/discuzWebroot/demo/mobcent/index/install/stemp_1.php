<?php   
ob_end_clean ();
require_once '../../Config/public.php';
require_once '../../public/mobcentDatabase.php';
require_once '../../../source/class/class_core.php';
require_once '../../../config/config_ucenter.php';
require 'include/install_var.php';
require_once '../tools.php';
C::app ()->init(); 

$mobcentPath = dirname(__FILE__) . "/../";
$ucserverPath = dirname(__FILE__) . "/../../uc_server";
$dirfile_items = array
(
        'mobcent_data' => array('type' => 'dir', 'path' => $mobcentPath,'name' => 'mobcent'),
        'uc_server' => array('type' => 'dir', 'path' => $ucserverPath,'name' => 'uc_server'),
);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <title></title>
    <link rel="stylesheet" href="images/css.css" />
</head>
<body style="margin:0px;padding:0px;">
    <div id="check" style="margin-left:auto;margin-right:auto;">
        <div class="check_top">
            <div class="discur_xzaz">
               <span><?php echo Common::get_web_unicode_charset('\u6469\u8baf\u0044\u0069\u0073\u0063\u0075\u007a\u0021\u4e0b\u8f7d\u5b89\u88c5');?></span>
               <a href=""><img src="images/close.png" alt="" /></a>
            </div>
            <div class="check_top_jc">
                <div><?php echo Common::get_web_unicode_charset('\u68c0\u67e5\u6570\u636e\u5e93\u662f\u5426\u8fde\u63a5\u6210\u529f');?></div>
                <div><?php echo Common::get_web_unicode_charset('\u68c0\u67e5\u6587\u4ef6\u5939\u662f\u5426\u6709\u6743\u9650');?></div>
                <div><?php echo Common::get_web_unicode_charset('\u5b89\u88c5');?></div>
            </div>
            <div class="check_top_img">
                <div class="jiantou"><img src="images/blue_jiantou.png" alt="" /></div>
                <div class="jiantou"><img src="images/green_jiantou.png" alt="" /></div>
                <div class="wancheng"><img src="images/grey_jiantou.png" alt="" /></div>
            </div>
        </div>
        <?php
              if(file_exists('../../install.log')){
                    echo "<script>window.location = 'index.php'</script>";
              }else{?>
        <div class="check_middle">
            <span class="checkspan"><?php echo Common::get_web_unicode_charset('\u6570\u636e\u5e93\u8fde\u63a5\u68c0\u67e5');?></span>
            <table class="checktable">
                <tr class="tablehead">
                    <td style="width:60%;"><?php echo Common::get_web_unicode_charset('\u51fd\u6570\u540d\u79f0');?></td>
                    <td style="width:30%;"><?php echo Common::get_web_unicode_charset('\u68c0\u67e5\u7ed3\u679c');?></td>
                    <td style="width:10%;"><?php echo Common::get_web_unicode_charset('\u5efa\u8bae');?></td>
                </tr>
                <tr class="tableother">
                <td>./config/config_global.php</td>
                    <?php 
                        $open =opendir('data/');
                        $result = mysql_query('SHOW TABLES');
                        while($rows = mysql_fetch_array($result)){
                            $arr[]=$rows[0];
                        }
                        
                        while($file = readdir($open))
                        {
                            if ($file == '.' || $file == '..' || $file =='.svn') continue;
                            $sql = file_get_contents('data/'.$file);
                            $sql = str_replace('{replaceStr}', DB::table(), $sql);
                            $sql = str_replace("\r\n", "\n", $sql);
                             
                            $type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
                            $type = in_array($type, array('MYISAM', 'HEAP', 'MEMORY')) ? $type : 'MYISAM';
                            $sql = preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
                            (mysql_get_server_info() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=".UC_DBCHARSET : " TYPE=$type");
                            $prename=str_replace('.sql', '', $file);
                            //print_r(DB::table($prename)); 
                            if(in_array(DB::table($prename), $arr) && DB::table($prename) == DB::table('add_portal_module')){
                                $columnsQuery =DB::query('SELECT * FROM '.DB::table('add_portal_module'));
                                $columnsArr = mysql_field_name($columnsQuery,10);
                                if($columnsArr != 'essence')
                                {
                                    DB::query('DROP  TABLE '.DB::table('add_portal_module'));
                                    $create_table = DB::query($sql);
                                    if($create_table == 1)
                                    {
                                        $issubmit = true;
                                        $istable = true;
                                    }
                                }
                            
                            }
                            if(in_array(DB::table($prename), $arr) &&  DB::table($prename) == DB::table('add_module'))
                            {
                                $columnsQuery =DB::query('SELECT * FROM '.DB::table('add_module'));
                                $columnsArr = mysql_field_name($columnsQuery,3);
                                if($columnsArr != 'content'){
                                    mysql_query('DROP TABLE '.DB::table('add_module'));
                                    $create_table = DB::query($sql);
                                    if($create_table == 1)
                                    {
                                        $issubmit = true;
                                        $istable = true;
                                    }
                                }
                            } 
                            
                            if(in_array(DB::table($prename), $arr)){
                                $issubmit = true;
                                $istable = true;
                            }else { 
                                $create_table = DB::query($sql);
                                if($create_table == 1)
                                {
                                    $issubmit = true;
                                    $istable = true;
                                }
                                else {
                                    $issubmit = false;
                                    $istable = false;
                                }
                            }
                        } 
                        DB::query("DELETE FROM ".DB::table('add_admin')); /*qing kong biao zhong yi you shu ju*/
                         
                    ?>
            <td class="lj"><?php if($istable == true){ echo Common::get_web_unicode_charset('\u8fde\u63a5\u6210\u529f');}else{ echo '<font color=red>'.Common::get_web_unicode_charset('\u8fde\u63a5\u5931\u8d25').'</font>';}?></td>
                    <td><?php echo Common::get_web_unicode_charset('\u5199\u5165');?></td>
                </tr>
            </table>
        </div> 
        <div class="check_middle">
            <span class="checkspan"><?php echo Common::get_web_unicode_charset('\u6587\u4ef6\u5939\u6743\u9650\u68c0\u67e5');?></span>
            <table class="checktable">
                <tr class="tablehead">
                    <td style="width:60%;"><?php echo Common::get_web_unicode_charset('\u76ee\u6807\u6587\u4ef6');?></td>
                    <td style="width:30%;"><?php echo Common::get_web_unicode_charset('\u68c0\u67e5\u7ed3\u679c');?></td>
                    <td style="width:10%;"><?php echo Common::get_web_unicode_charset('\u5efa\u8bae');?></td>
                </tr> 
                <?php 
                    $dirfile_items = dirfile_check($dirfile_items); 
                    foreach ($dirfile_items as $key=>$val)
                    {
                        echo '<tr class="tableother">';
                        echo "<td>".$val['name']."</td>";
                        
                        if(checkFile($val['current']))
                        {
                            echo '<td class="lj">'.Common::get_web_unicode_charset('\u53ef\u5199').'</td>';
                        }else{
                            $issubmit = false;
                            echo "<td class='lj' style='color:red'>".Common::get_web_unicode_charset('\u53ea\u8bfb')."</td>";
                        }
                        echo '<td >'.Common::get_web_unicode_charset('\u53ef\u5199').'</td>';
                        echo "</tr>";
                    }
                ?>

            </table>
        </div>
        
    <form action="stemp_2.php" class="check_bottom"  name="check_bottom" method ="post">
    
     <div style="padding-bottom:10px">
    <a href="../../requirements/" target="_blank" style="font-size:12px;padding-left:5px;color:red">
    <?php echo Common::get_web_unicode_charset('\u70b9\u51fb\u67e5\u770b\u914d\u7f6e\u9700\u6c42');?></a> 
    </div>
        <div class="password">
            <span class="setspan"><?php echo Common::get_web_unicode_charset('\u8bbe\u7f6e\u767b\u5f55\u5e10\u53f7\uff1a');?></span>
            <input type="text" name ='uname' id ='uname'/>
        </div>
        <div class="password">
            <span class="setspan"><?php echo Common::get_web_unicode_charset('\u8bbe\u7f6e\u767b\u5f55\u5bc6\u7801\uff1a');?></span>
            <input type="password" name ='pwd' id ='pwd'/>
        </div>

        <div class="password">
             <span class="setspan"><?php echo Common::get_web_unicode_charset('\u786e\u8ba4\u767b\u5f55\u5bc6\u7801\uff1a');?></span>
            <div class="import">
               <input type="password" name ='repwd' id ='repwd'/>
                <span class="passwords" style="color:red"><?php echo Common::get_web_unicode_charset('\uff08\u6b64\u5904\u5bc6\u7801\u7528\u4f5c\u5b89\u7c73\u7f51\u63d2\u4ef6\u8f6c\u6362\uff09');?></span>
            </div>
        </div>
        
        
        
        <div class="password">
            <span class="setspan"><?php echo Common::get_web_unicode_charset('\u8bbe\u7f6e\u5bc6\u4fdd\u95ee\u9898\uff1a ');?></span>
            <input type="text" name ='question' id ='question'/>
        </div>
        <div class="password">
            <span class="setspan"><?php echo Common::get_web_unicode_charset('\u8bbe\u7f6e\u5bc6\u4fdd\u7b54\u6848\uff1a ');?></span>
            <div class="import">
                <input type="text" name ='answer' id ='answer'/>
                <span class="passwords"><?php echo Common::get_web_unicode_charset('\uff08\u5bc6\u4fdd\u95ee\u9898\u548c\u7b54\u6848\u7528\u4e8e\u627e\u56de\u5bc6\u7801\uff09 ');?></span>
            </div>
        </div>
        
        <input type="reset" value="<?php echo Common::get_web_unicode_charset('\u53d6\u6d88');?>" class="qx" />
            <?php
        if($issubmit =='true')
        {
            echo ' <input type="button" value='.Common::get_web_unicode_charset('\u4e0b\u4e00\u6b65').' class="xyb" style="cursor:pointer" />';
        }
        else
        {
            echo ' <input type="button" value='.Common::get_web_unicode_charset('\u4e0b\u4e00\u6b65').' class="xyb" style="cursor:pointer" disabled/>';
        }
        ?>

        </form>
        <?php }?>
    </div>
    <script src="images/jquery-1.3.1.js" type="text/javascript"></script>
    <script type="text/javascript">
    $(function(){
        $('.xyb').click(function(){
            $.ajax({
                type:"GET",
                url:"../../requirements/index.php?ajax=1",
                success:function(data){ 
                    //var data="{rs:1}";
                    var objs = eval("["+data+"]");
                    var res = objs[0].rs; 
                    if(res=='0'){
                        alert(decodeURI('%E7%8E%AF%E5%A2%83%E9%85%8D%E7%BD%AE%E4%B8%8D%E6%BB%A1%E8%B6%B3%E8%A6%81%E6%B1%82%EF%BC%8C%E8%AF%B7%E5%85%88%E9%85%8D%E7%BD%AE%E7%8E%AF%E5%A2%83%EF%BC%81'));
                        location.href="../../requirements/index.php";
                    }else{
                        if(res=='-1'){
                            alert(decodeURI('%E8%AD%A6%E5%91%8A%EF%BC%9A%E6%82%A8%E7%9A%84%E7%8E%AF%E5%A2%83%E8%BF%98%E5%8F%AF%E4%BB%A5%E4%BC%98%E5%8C%96%E7%9A%84%E6%9B%B4%E5%A5%BD%EF%BC%81'));
                        }
                        var uname =document.getElementById('uname').value;
                        var pwd =document.getElementById('pwd').value;
                        var repwd =document.getElementById('repwd').value;
                        var question =document.getElementById('question').value;
                        var answer =document.getElementById('answer').value;
                        if(uname.length < 3){
                            alert(decodeURI('%E7%99%BB%E5%BD%95%E5%B8%90%E5%8F%B7%E9%95%BF%E5%BA%A6%E4%B8%8D%E8%83%BD%E4%BD%8E%E4%BA%8E3%E4%BD%8D'));
                            document.getElementById('uname').select();
                            return false;
                        }
                        if(pwd.length < 3){
                            alert(decodeURI('%E7%99%BB%E5%BD%95%E5%AF%86%E7%A0%81%E9%95%BF%E5%BA%A6%E4%B8%8D%E8%83%BD%E4%BD%8E%E4%BA%8E3%E4%BD%8D'));
                            document.getElementById('pwd').select();
                            return false;
                        }
                        if(repwd != pwd){
                            alert(decodeURI('%E4%B8%A4%E6%AC%A1%E8%BE%93%E5%85%A5%E7%9A%84%E5%AF%86%E7%A0%81%E4%B8%8D%E4%B8%80%E8%87%B4'));
                            document.getElementById('pwd').select();
                            return false;
                        }
                        if(question == ""){
                            alert(decodeURI('%E5%AF%86%E4%BF%9D%E9%97%AE%E9%A2%98%E4%B8%8D%E8%83%BD%E4%B8%BA%E7%A9%BA'));
                            document.getElementById('question').focus();
                            return false;
                        }
                        if(answer == ""){
                            alert(decodeURI('%E5%AF%86%E4%BF%9D%E7%AD%94%E6%A1%88%E4%B8%8D%E8%83%BD%E4%B8%BA%E7%A9%BA'));
                            document.getElementById('answer').focus();
                            return false;
                        }
                        location.href="stemp_2.php?uname="+uname+"&pwd="+pwd+"&repwd="+repwd+"&question="+question+"&answer="+answer;
                    }
                }
            });
        });
    })
    
    function checkpwd(){
        
        
    }
    </script>
</body>
</html>
