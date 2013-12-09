<?php
 class SubPages{   
      
   private  $each_disNums; 
  private  $nums; 
  private  $current_page;   
  private  $sub_pages; 
  private  $pageNums; 
  private  $page_array = array(); 
  private  $subPage_link; 
  private  $subPage_type; 
  function __construct($each_disNums,$nums,$current_page,$sub_pages,$subPage_link,$subPage_type){   
   $this->each_disNums=intval($each_disNums);   
   $this->nums=intval($nums);   
    if(!$current_page){   
    $this->current_page=1;   
    }else{   
    $this->current_page=intval($current_page);   
    }   
   $this->sub_pages=intval($sub_pages);   
   $this->pageNums=ceil($nums/$each_disNums);   
   $this->subPage_link=$subPage_link;    
   $this->show_SubPages($subPage_type);    
  }   
      
      
  function __destruct(){   
    unset($each_disNums);   
    unset($nums);   
    unset($current_page);   
    unset($sub_pages);   
    unset($pageNums);   
    unset($page_array);   
    unset($subPage_link);   
    unset($subPage_type);   
   }   
  function show_SubPages($subPage_type){   
    if($subPage_type == 1){   
    $this->subPageCss1();   
    }elseif ($subPage_type == 2){   
    $this->subPageCss2();   
    }   
   }   
      
      
  function initArray(){   
    for($i=0;$i<$this->sub_pages;$i++){   
    $this->page_array[$i]=$i;   
    }   
    return $this->page_array;   
   }   
      
  function construct_num_Page(){   
    if($this->pageNums < $this->sub_pages){   
    $current_array=array();   
     for($i=0;$i<$this->pageNums;$i++){    
     $current_array[$i]=$i+1;   
     }   
    }else{   
    $current_array=$this->initArray();   
     if($this->current_page <= 3){   
      for($i=0;$i<count($current_array);$i++){   
      $current_array[$i]=$i+1;   
      }   
     }elseif ($this->current_page <= $this->pageNums && $this->current_page > $this->pageNums - $this->sub_pages + 1 ){   
      for($i=0;$i<count($current_array);$i++){   
      $current_array[$i]=($this->pageNums)-($this->sub_pages)+1+$i;   
      }   
     }else{   
      for($i=0;$i<count($current_array);$i++){   
      $current_array[$i]=$this->current_page-2+$i;   
      }   
     }   
    }   
       
    return $current_array;   
   }   
      
  function subPageCss1(){   
   $subPageCss1Str="";   
   $subPageCss1Str.=Common::get_web_unicode_charset('\u5171').$this->nums.Common::get_web_unicode_charset('\u6761\u8bb0\u5f55\uff0c');   
   $subPageCss1Str.=Common::get_web_unicode_charset('\u6bcf\u9875\u663e\u793a').$this->each_disNums.Common::get_web_unicode_charset('\u6761\uff0c');   
   $subPageCss1Str.=Common::get_web_unicode_charset('\u5f53\u524d\u7b2c').$this->current_page."/".$this->pageNums.Common::get_web_unicode_charset('\u9875');   
    if($this->current_page > 1){   
    $firstPageUrl=$this->subPage_link."1";   
    $prewPageUrl=$this->subPage_link.($this->current_page-1);   
    $subPageCss1Str.="[<a href='$firstPageUrl'>".Common::get_web_unicode_charset('\u9996\u9875')."</a>] ";   
    $subPageCss1Str.="[<a href='$prewPageUrl'>".Common::get_web_unicode_charset('\u4e0a\u4e00\u9875')."</a>] ";   
    }else {   
    $subPageCss1Str.=Common::get_web_unicode_charset('\u005b\u9996\u9875\u005d');   
    $subPageCss1Str.=Common::get_web_unicode_charset('\u005b\u4e0a\u4e00\u9875\u005d');   
    }   
       
    if($this->current_page < $this->pageNums){   
    $lastPageUrl=$this->subPage_link.$this->pageNums;   
    $nextPageUrl=$this->subPage_link.($this->current_page+1);   
    $subPageCss1Str.=" [<a href='$nextPageUrl'>".Common::get_web_unicode_charset('\u4e0b\u4e00\u9875')."</a>] ";   
    $subPageCss1Str.="[<a href='$lastPageUrl'>".Common::get_web_unicode_charset('\u5c3e\u9875')."</a>] ";   
    }else {   
    $subPageCss1Str.=Common::get_web_unicode_charset('\u005b\u4e0b\u4e00\u9875\u005d');   
    $subPageCss1Str.=Common::get_web_unicode_charset('\u005b\u5c3e\u9875\u005d');   
    }   
       
    echo $subPageCss1Str;   
       
   }   
      
  function subPageCss2(){   
   $subPageCss2Str="";   
   $subPageCss2Str.=Common::get_web_unicode_charset('\u5f53\u524d\u7b2c').$this->current_page."/".$this->pageNums.Common::get_web_unicode_charset('\u9875');   
       
       
    if($this->current_page > 1){   
    $firstPageUrl=$this->subPage_link."1";   
    $prewPageUrl=$this->subPage_link.($this->current_page-1);   
    $subPageCss2Str.="[<a href='$firstPageUrl'>".Common::get_web_unicode_charset('\u9996\u9875')."</a>] ";   
    $subPageCss2Str.="[<a href='$prewPageUrl'>".Common::get_web_unicode_charset('\u4e0a\u4e00\u9875')."</a>] ";   
    }else {   
    $subPageCss2Str.=Common::get_web_unicode_charset('\u005b\u9996\u9875\u005d');   
    $subPageCss2Str.=Common::get_web_unicode_charset('\u005b\u4e0a\u4e00\u9875\u005d');   
    }   
       
   $a=$this->construct_num_Page();   
    for($i=0;$i<count($a);$i++){   
    $s=$a[$i];   
     if($s == $this->current_page ){   
     $subPageCss2Str.="[<span style='color:red;font-weight:bold;'>".$s."</span>]";   
     }else{   
     $url=$this->subPage_link.$s;   
     $subPageCss2Str.="[<a href='$url'>".$s."</a>]";   
     }   
    }   
       
    if($this->current_page < $this->pageNums){   
    $lastPageUrl=$this->subPage_link.$this->pageNums;   
    $nextPageUrl=$this->subPage_link.($this->current_page+1);   
    $subPageCss2Str.=" [<a href='$nextPageUrl'>".Common::get_web_unicode_charset('\u005b\u4e0b\u4e00\u9875\u005d')."</a>] ";   
    $subPageCss2Str.="[<a href='$lastPageUrl'>".Common::get_web_unicode_charset('\u5c3e\u9875')."</a>] ";   
    }else {   
    $subPageCss2Str.=Common::get_web_unicode_charset('\u005b\u4e0b\u4e00\u9875\u005d');   
    $subPageCss2Str.=Common::get_web_unicode_charset('\u005b\u5c3e\u9875\u005d');   
    }   
    echo $subPageCss2Str;   
   }   
}   
?>