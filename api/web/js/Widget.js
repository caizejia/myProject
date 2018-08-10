define(['jquery'],function($){
	
    var widget = {
    	timeSet:function(years,months,days, hours){		// 计时器 //divnames:倒计时的id，lang:地区语言
    		var year = years,month = months,day = days,hours=hours;
    		function ShowCountDown(){
    			var now = new Date();
		        var endDate = new Date(year,month-1, day, now.getHours() + hours);
		        var leftTime = endDate.getTime()-now.getTime();
		        var leftsecond = parseInt(leftTime/1000);
		        var day1= Math.floor(leftsecond/(60*60*24));
		        var hour=Math.floor((leftsecond-day1*24*60*60)/3600);
		        var minute=Math.floor((leftsecond-day1*24*60*60-hour*3600)/60);
		        var second=Math.floor(leftsecond-day1*24*60*60-hour*3600-minute*60);
		        //if (day   <= 9) day = "0" + day;
	            if (hour   <= 9) hour = "0" + hour;
	            if (minute <= 9) minute = "0" + minute;
	            if (second <= 9) second = "0" + second;
	            //document.getElementById("d").innerHTML = day;
		        document.getElementById("h").innerHTML = hour;
		        document.getElementById("m").innerHTML = minute;
		        document.getElementById("s").innerHTML = second;
		        //cc.innerHTML = hour+"時"+minute+"分"+second+"秒";
    		}
	        window.setInterval(function(){
	        	ShowCountDown();
	        }, 1000);

    	},
    	percent:function(soldNum,percentNum,progress){	//销售百分比
		        var curhour= $('.percentBar').attr('data-value');
		        var base=0,range=0;
		        var percent   = document.getElementById("percentNum");
		        var progress  = document.getElementById("progress");
		        if(curhour<=10000){
		            base=70;range=5;
		        }else
		        if(curhour<=20000){
		            base=70;range=10;
		        }else
		        if(curhour<=40000){
		            base=70;range=15;
		        }else
		        if(curhour<=80000){
		            base=70;range=20;
		        }else
		        if(curhour<=130000){
		            base=70;range=25;
		        }else
		        if(curhour<200000){
		            base=70;range=28;
		        }
		        var opercent=Math.floor(range+base);
		        try{
		        	progress.style.width = percent.innerHTML = opercent+"%";
		        }catch(ex){
		        	
		        }

    	},
    	
    	fmoney:function(s, n){//金额格式化
    		n = n > 0 && n <= 20 ? n : 2;   
	       s = parseFloat((s + "").replace(/[^\d\.-]/g, "")).toFixed(n) + "";   
	       var l = s.split(".")[0].split("").reverse(),      
	       t = "";   
	       for(i = 0; i < l.length; i ++ )   
	       {   
	          t += l[i] + ((i + 1) % 3 == 0 && (i + 1) != l.length ? "," : "");   
	       }   
	       return t.split("").reverse().join("");  
    	}
    }
    window.widget = widget;

});