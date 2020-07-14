function GetQueryString(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
} 

$(function() {
	var vid = GetQueryString('vid');
	//var vote_name = decodeURI(GetQueryString('name'));
	var URL = decodeURI(GetQueryString('u'));

	// 验证un
	var options = {
		duration: 2000,
		
		type: 'danger'
	};
	var message = '';

	//if (!vid || !vote_name || !URL) {
	if (!vid || !URL) {
	 	message = '未允许操作，请联系工作人员';
	 	createToast(message, options);
	 	$(".login").attr('disabled',true);
	}

	localStorage.setItem('vote_model_id', vid);
	//localStorage.setItem('name', vote_name);
	localStorage.setItem('url', URL);

	if( localStorage.getItem('_token')){
		window.location.href = './page2.html';
	}
	var stuName = $('#stuName');
	var stuNum = $('#stuNum');
	var login= $('.login');

	var orgAjax = '星投票'; //组织名称

	$("title").html(orgAjax);
	//$('#organization').html(vote_name + orgAjax);
	//$('#organization').html(orgAjax);
	login.click(function(){
		var loginNum = $('#stuNum').val();
		var loginName = $('#stuName').val();

		if(loginName.length == 0){
			message = '姓名不能为空';
			createToast(message, options)
		} else if(loginNum.length == 0){
			message = '学号不能为空';
			createToast(message, options)
		}else if(stuNum.val().length!='12'){
         	message = '请输入正确的学号';
			createToast(message, options)
		}else{
			//console.log(loginName);
			$.ajax({
				type: 'POST',
				url: URL + 'login',
				async:false,
				timeout: 1000,
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
				data:{
					student_id: loginNum,
					name: loginName,
					vote_model_id: vid
				}, 
				cache:false, 
				dataType:'json',
				success: function( data) {

					if(data.status_code == 200){
						//签到成功
						localStorage.setItem('_token', data.ps + data._token);
						createToast(data.message, options);
						window.location.href = './page2.html';
					} else if (data.status_code == 203){
						//投票过期
						createToast(data.message, options);
					} else if (data.status_code == 206){
						//代表信息错误
						createToast(data.message, options);
					} else if (data.status_code == 207) {
						//已投票
						window.location.href = './page3.html';
					} else {
						message = '签到失败';
						createToast(message, options);
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					message = '登陆失败';
					createToast(message, options)
                    // 状态码
                    console.log(XMLHttpRequest.status);
                    // 状态
                    //console.log(XMLHttpRequest.readyState);
                    // 错误信息   
                    //console.log(textStatus);
                }
			});
		}

	})

}) 

function createToast(message, options) {
	$.toast.config.align = 'right';
	$.toast.config.width = window.innerWidth - 10;
	$.toast(message, options);
}