(function(){
	var $ = function(elm){
			return document.getElementById(elm)
		},
		qqdomain = false,
		followbtn = $("followbtn"),
		followarea = $("followarea"),
		weibolink = $("weibo_url"),
		getcookie = function(name) {
		    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
	    	if(arr != null) 
	    	{return unescape(arr[2]);}
	    	else
	    	{return null;}
		}, 
		ajax = {
			"request":function(url,opt){
				var async = opt.async !== false,
					fn = function(){},
					method = (opt.method || 'GET').toUpperCase(),
					data = opt.data || null,
					success = opt.success || fn,
					failure = opt.error || fn,
					_onStateChange = function(xhr,success,failure){
						if(xhr.readyState == 4){
							var s = xhr.status;
							if(s>= 200 && s < 300){
								/*for(var i in xhr){
									alert(i+":"+xhr[i]);
								}
								alert(xhr.responseText);*/
								success(xhr.responseText||xhr.response);
							}else{
								failure(xhr.response);
							}
						}else{}
					};
				
				if (method == 'GET' && data) {
					url += (url.indexOf('?') == -1 ? '?': '&') + data;
					data = null;
				}
				var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
				xhr.onreadystatechange = function() {
					_onStateChange(xhr, success, failure);
				};
				xhr.open(method, url, async);
				if (method == 'POST') {
					xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded;');
				}
				xhr.send(data);
				return xhr;
			}
		},
		showLogin =function(){
				var w = 420,h = 250 ,t = (screen.height - h)/2,l = (screen.width - w)/2,url = 'http://ui.ptlogin2.qq.com/cgi-bin/login?appid=46000101'
			    +'&s_url=http%3A%2F%2Ffollow.v.t.qq.com%2F%3Fc%3Dfollow%26a%3Djump%26rcode%3D1%26token%3D'+encodeURIComponent(getcookie("__hash__"))
			    +'&target=self&style=0&link_target=blank&hide_title_bar=1&dummy=1&bgcolor=ffffff';
		window.open(url,'_blank',"width="+w+",height="+h+",top="+t+",left="+l+",toolbar=no,menubar=no,scrolbars=no,resizeable=no,status=no");
		},
		changeEvent = function(){
	    	weibolink.className = "weibo_url";
		},
		clickhighjacking = function(){
			!qqdomain && alert("收听腾讯微博用户<"+uinfo.nick+">成功");	
		},
		postFollow = function(){
			ajax.request(app_url+"/index.php?c=follow&a=listen",{
				"data":"name="+$('name').value+"&hash="+getcookie('__hash__')+"&time="+new Date().getTime(),
				"method":"post",
				"async":false,
				"success":function(response){//alert(response);
					 var d = (function(s){
					 	if (window.JSON && window.JSON.parse){
					 		return window.JSON.parse(s);
					 	}else{
					 		return eval("("+s+")");
					 	}
					 })(response);
					 
		             if (d.ret == 0) {
		                 if (false) {
		                 } else if (s==1) {
		                     changeEvent();
		                     followarea.innerHTML = '<span class="bg action followed">&nbsp;</span>';
		                     clickhighjacking();
		                 } else if(s==2) {
		                     changeEvent();
		                     followbtn.innerHTML = '已收听';
		                     clickhighjacking();
		                 } else if(s==3) {
		                     changeEvent();
		                     alert("收听成功");
		                 } else if(s==4) {
		                     changeEvent();
		                     followbtn.innerHTML = '<span class="bg toleft noaction"></span>';
		                     clickhighjacking();
		                 } else if(s==5) {
		                     changeEvent();
		                     followbtn.innerHTML = '<span class="bg toleft noaction"></span>';
		                     clickhighjacking();
		                 } else {
		                     changeEvent();
		                     $('#followbtn').html('已收听');
		                     $('#followbtn').unbind('click');
		                     clickhighjacking();
		                 }
		            } else {
		               if(d.errcode==6){
		                   window.open('http://t.qq.com/'+$('name').value,'_blank');
		               }else{
		                   alert(d.msg);
		               }
		            }
				},
				"error":function(){
					alert("网络链接失败！");
				}
			});	
		},
		initFollowEvent = function(){
			try{
				(parent.location);
			}catch(e){
				qqdomain = false;
			}
			if (followbtn){
				followbtn.onclick = function(){
					if(unlogin){
						showLogin();
					}else{
						postFollow();		
					}
				}
			}
			if (uinfo.ismyidol){
				changeEvent();
			}
		};
		initFollowEvent();
		window.setLoginInfo = function(uinfo){
			window.unlogin = false;
			postFollow();
		}
})();/*  |xGv00|6862da8dc73ad148518954c208fda635 */