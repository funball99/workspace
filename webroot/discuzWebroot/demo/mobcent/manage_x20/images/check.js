
	 var GB2312UnicodeConverter = {
	            ToUnicode: function (str) {
	                return escape(str).toLocaleLowerCase().replace(/%u/gi, '\\u');
	            },
	            ToGB2312: function (str) {
	                return unescape(str.replace(/\\u/gi, '%u'));
	            }

	        };
		function selectAll(checkbox) { 
			$('input.check[type=checkbox]').attr('checked', $(checkbox).attr('checked')); 
		} 
		function selectClassAll(checkbox)
		{
			$('input.classcheck[type=checkbox]').attr('checked', $(checkbox).attr('checked')); 
		}
		function show(obj){ 
		    if(obj.value=='1'){
			    document.getElementById('zhucecishu').style.display = "block"; 			  
		    }else{
		    	document.getElementById('zhucecishu').style.display = "none"; 			
		    }
		} 

		function checksub(form){	
			var inputs = document.getElementsByName("classfid[]");			 
			var checked_counts = 0;
			for(var i=0;i<inputs.length;i++){
				if(inputs[i].checked){
				checked_counts++;
				}
			}
			
			var fid=document.getElementsByName("fid[]"); 
			var groupTypeId = new Array();
		    for(var i=0;i<fid.length;i++){
		         if(fid[i].checked){		 
		        	 groupTypeId[i]=fid[i].value;
		         } 
		    } 
		    if(checked_counts == 0){
		    	var IdLength = GB2312UnicodeConverter.ToGB2312('\u5bf9\u4e0d\u8d77\uff0c\u8bf7\u9009\u62e9\u95e8\u6237\u6a21\u5757');
				alert(IdLength);
				return false;
			}  
		    if(checked_counts > 6){
		    	var IdLength = GB2312UnicodeConverter.ToGB2312('\u5bf9\u4e0d\u8d77\uff0c\u95e8\u6237\u5bfc\u822a\u6a21\u5757\u6570\u4e0d\u80fd\u5927\u4e8e\u0036');
				alert(IdLength);
				return false;
			}   
		    if(groupTypeId.length==0){
		    	var IdLength = GB2312UnicodeConverter.ToGB2312('\u5bf9\u4e0d\u8d77\uff0c\u8bf7\u9009\u62e9\u677f\u5757\uff01');
				alert(IdLength);
				return false;
			}

			if(form.login_count.value==""){
				var IdLength = GB2312UnicodeConverter.ToGB2312('\u5bf9\u4e0d\u8d77\uff0c\u8bf7\u8f93\u5165\u767b\u9646\u6b21\u6570\uff01');
				alert(IdLength);
				form.login_count.focus();
				return false;
			}else if(form.login_count.value>9999){
				var IdLength = GB2312UnicodeConverter.ToGB2312('\u5bf9\u4e0d\u8d77\uff0c\u767b\u5f55\u6b21\u6570\u4e0d\u80fd\u5927\u4e8e\u0039\u0039\u0039\u0039\uff01');
				alert(IdLength);
				form.login_count.select();
				return false;
			}else if(form.login_count.value==0){  
				var IdLength = GB2312UnicodeConverter.ToGB2312('\u767b\u9646\u6b21\u6570\u4e0d\u80fd\u4e3a\u0030');
				alert(IdLength);
				form.login_count.select();
				return false;
			}
 

			var temp=document.getElementsByName("isreg"); 
			for (i=0;i<temp.length;i++){ 
				if(temp[i].checked){
					 
					if(temp[i].value=="1" && form.register_count.value==""){
						var IdLength = GB2312UnicodeConverter.ToGB2312('\u6ce8\u518c\u6b21\u6570\u4e0d\u80fd\u4e3a\u7a7a');
						alert(IdLength);
						form.register_count.focus();
						return false;
					}else if(temp[i].value=="1" && form.register_count.value>9999){
						var IdLength = GB2312UnicodeConverter.ToGB2312('\u6ce8\u518c\u6b21\u6570\u4e0d\u80fd\u5927\u4e8e\u0039\u0039\u0039\u0039');
						alert(IdLength);
						form.register_count.select();
						return false;
					}else if(temp[i].value=="1" && form.register_count.value==0){ 
						var IdLength = GB2312UnicodeConverter.ToGB2312('\u6ce8\u518c\u6b21\u6570\u4e0d\u80fd\u4e3a\u0030');
						alert(IdLength);
						form.register_count.select();
						return false;
					}						 
				} 
			} 
		}
		