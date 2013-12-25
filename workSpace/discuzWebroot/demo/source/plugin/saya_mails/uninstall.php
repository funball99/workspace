<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
ALTER TABLE pre_common_member DROP saya_pmvoice;
ALTER TABLE pre_common_member DROP saya_hitvoice;
DROP TABLE pre_saya_mails;
EOF;


                runquery($sql);

$finish = true;
?>