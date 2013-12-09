<?php
class table_common_block{
	public static function selAllBlackData($start_limit,$limit)
	{
		return DB::query(" SELECT * FROM ".DB::table('common_block_item')." where bid in (34,31,20,21,22) ORDER BY displayorder, itemtype DESC limit $start_limit,$limit");
	
	}
	public static function selAllBlackItemData($classItem,$start_limit,$limit)
	{
		$query =DB::query(" SELECT * FROM ".DB::table('common_block_item')." where bid in ($classItem) ORDER BY displayorder, itemtype DESC limit $start_limit,$limit");
		while($Item = DB::fetch($query))
		{
			$ItemDta []=$Item;
		}
		return $ItemDta;
	}
	
	public static function selAllSlidebid()
	{
		return DB::result_first(" SELECT * FROM ".DB::table('common_block')." where styleid =110");
	
	}
	
	public static function selAllBlackDataNum($classItem)
	{
		return DB::result_first(" SELECT count(*) num  FROM ".DB::table('common_block_item')." where bid in ($classItem)");
	}
	
	public static function selAllBlackPicData($bid,$strat,$piclimit)
	{
		$bid = empty($bid)?-1:$bid;
		$query = DB::query(" SELECT * FROM ".DB::table('common_block_item')." where bid = $bid ORDER BY displayorder, itemtype DESC limit $strat,$piclimit");
		while($Item = DB::fetch($query))
		{
			$ItemDta []=$Item;
		}
		return $ItemDta;
	}
}
?>