<?php 
ob_end_clean ();
require_once '../../Config/public.php';
require_once '../../public/mobcentDatabase.php';
require_once '../../../source/class/class_core.php';
C::app ()->init();
@session_start();
unset($_SESSION['renxing']);
echo "<script>location.href='login.php';</script>";

?>


