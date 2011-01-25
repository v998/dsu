<?php
$tablename = DB::table('czw_threadfield');

DB::insert('czw_threadfield', array('tid'=>1), false, false, true);
$info = DB::fetch_first("SELECT * FROM $tablename WHERE 1 LIMIT 1");
$sql = '';
if(count($info) > 9){
	$sql = <<<EOF
		ALTER TABLE $tablename DROP `threadmood1`;
		ALTER TABLE $tablename DROP `threadmood2`;
		ALTER TABLE $tablename DROP `threadmood3`;
		ALTER TABLE $tablename DROP `threadmood4`;
		ALTER TABLE $tablename DROP `threadmood5`;
		ALTER TABLE $tablename DROP `threadmood6`;
		ALTER TABLE $tablename DROP `threadmood7`;
		ALTER TABLE $tablename DROP `threadmood8`;
EOF;
}else{
	$sql = "DROP TABLE $tablename;";
}
runquery($sql);

DB::delete('home_click',array('idtype'=>'czw_threadmood'));

$finish = TRUE;