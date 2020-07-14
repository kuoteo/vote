
window.onload=function(){
	var input_name=document.getElementById('input_name');
	var content=document.getElementById('content');
	var input_name1=document.getElementById('input_name1');
	var input_name2=document.getElementById('input_name2');
	var input_name3=document.getElementById('input_name3');
	var input_name4=document.getElementById('input_name4');
	var input_name5=document.getElementById('input_name5');
	var URL = localStorage.getItem('url');

	var orgAjax = '星投票'; //组织名称

	document.title = orgAjax;


	var submitBtn = document.getElementById('submitBtn');
	
	var arr = [];
	var  arr_id = [];
	htmlTemp= '';

	var options = {
		duration: 3000,
		type: 'danger'
	};

	$.ajax({
        type: "GET",

		url:   URL + "show",

        dataType: "json",

		async:false,
		
		timeout: 5000,
        
        cache:false,

        success: function (data) {
        	if (data.status_code == 200) {
        		arr = data.message;
				for(var i = 0; i < arr.length; i++) {
					num = i < 10 ? ('0' + i) : i;
					htmlTemp += '<div id=' + arr[i].id +' class="candidate" isGet = 0>' + arr[i].vote_id + '</br>' + arr[i].name + '</div>';
					arr_id.push(arr[i].id);
				}
        	} else if (data.status_code == 203){
				//投票过期
				createToast(data.message, options);
			}else {
        		message = '获取列表失败, 请刷新页面';
				createToast(message, options);
        	}
        },
        beforeSend: function(xhr) {
            xhr.setRequestHeader("Authorization", localStorage.getItem('_token'));
	    },
	    error: function (error) {
	    	if (error.responseJSON.status_code == 401) {
				localStorage.clear('_token');
				window.location.href = './index.html';
	    	}
	    }

    });
	var message = '';

	var num; 

	var reNum = 4; //推荐人选人数
	var reHtml = ''; //推荐HTml
	//if(arr_id.length % 4 === 0)reNum = 4;
	//else if(arr_id.length %4 === 1)reNum = 6;
	//else if(arr_id.length %4 === 2)reNum = 5;
	//else if(arr_id.length %4 === 3)reNum = 4;
	//console.log(reNum);
	for(var i = 1;i <= reNum; i++){
		reHtml +='<div class="last"><input id=\'input_name' +i +'\'class=\'input_name\'style=\'outline: none;border:0px;\'value=\'\' type=\'text\'placeholder=\"另选人\"></div>'
	}
	$('#recommend').html(reHtml)
    content.innerHTML = htmlTemp;

	var maxSelect = 31, minSelect = 8;
	var selectArr = ['1','2','3','4','5','6','7','8','9','10','11','12','13','14',
    '15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34',
    '35','36','37','38','39']
	Array.prototype.indexOf = function(val) { for (var i = 0; i < this.length; i++) { if (this[i] == val) return i; } return -1; };

	Array.prototype.remove = function(val) { var index = this.indexOf(val); if (index > -1) { this.splice(index, 1); } };

	var all_name=[];
	var all_id = [];

	//点击时每个content时发生的事件，颜色改变，selectArr的元素删减
	addEvent(content, 'click', selectPeople);

	function selectPeople(e){
		console.log(arr_id);
		curItem = e.target;
		curItemId = e.target.id;
		curItemIs = e.target.getAttribute('isGet');
		if(curItemIs ==0){
			curItem.style.backgroundColor='red';
			curItem.setAttribute('isGet',1)		
			arr_id.remove(curItemId);
		}
		else if(curItemIs ==1){
			curItem.style.backgroundColor='#40afff';
			curItem.setAttribute('isGet',0);
			arr_id.push(curItemId);
		}
	}

	addEvent(submitBtn, 'click', function(){
		//禁用提交按钮
		$("#submitBtn").attr('disabled',true);
		if ((arr.length - arr_id.length)<5) {
			message = '投反对票需要5票或5票以上';
			createToast(message, options);
			//解除禁用提交按钮
			$("#submitBtn").attr('disabled',false);

		}else{


			$(".input_name").each(function(){
				if(this.value.length>24){
					message = '请输入正确的姓名格式';
					createToast(message, options);
				}else if(this.value.length!==0){
					// var goodName = {
					// 	math: 2,
					// 	name: this.value
					// }
					all_name.push(this.value);
				}
			});
	
			$.ajax({
				url: URL + "vote",

				type: 'POST',

				async: false,
				
				timeout: 5000,
				
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
				
				cache: false,
				
				data: {
					name:arr_id,
					other_name:all_name,
				},
				success: function(data) {
					if(data.status_code == 200){
						localStorage.clear('_token');
						localStorage.clear('name');
						localStorage.clear('vote_model_id');
						//all_name.splice(0,all_name.length);
						createToast(message, options);
						window.location.href = './page3.html';

					} else if (data.status_code == 203){
						//投票过期
						createToast(data.message, options);
					}else if (data.status_code == 206) {
						$("#submitBtn").attr('disabled',true);
		        		createToast(data.message, options);

		        	} else if (data.status_code == 207) {
		        		localStorage.clear('_token');
						//all_name.splice(0,all_name.length);
						createToast(message, options);
						window.location.href = './page3.html'
		        	}
	        	},
		        beforeSend: function(xhr) {
		            xhr.setRequestHeader("Authorization", localStorage.getItem('_token'));
			    },
			    error: function (error) {
		    	if (error.responseJSON.status_code == 401) {
					localStorage.clear('_token');
					window.location.href = './index.html';
		    	}
		    }

			})
	    }
})


} 



function createToast(message, options) {
	$.toast.config.align = 'right';
	$.toast.config.width = window.innerWidth - 10;
	$.toast(message, options);
}

function addEvent(obj, event, fn) {
	if (obj.addEventListener) {
		obj.addEventListener('click', fn, false)
	} else if (obj.attachEvent) {
		obj.attachEvent('on' + event, fn)
	} else {
		obj.event = fn;
	}
}