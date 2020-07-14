window.onload=function(){
	var content=document.getElementById('content');
	var arr1 = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10' ,'11', '12', 
	'13', '14', '15', '16' ,'17','18', '19', '20', '21', '22', '23', '24' ,'25','26', '27', '28', '29', '30'
	,'31','32', '33', '34', '35', '36', '37', '38', '39'];
	var data=['580', '580', '580', '580', '580', '580', '580', '580', '580', '580' ,'580', '580', 
	'580', '580', '580', '580' ,'580','580', '580', '580', '580', '580', '580', '580' ,'580','580', '580', '580', '580', '580'
	,'580','580', '580', '580', '580', '580', '580', '580', '580'];

	var dataArr = [
		// {
		// 	num: '01',
		// 	voteNum: 580
		// }
	];

	var arr = [];

	var orgAjax = '星投票'; //组织名称

	document.title = orgAjax;
//插入节点
	// function initDataArr () {
	// 	dataArr = [];
	// 	for(var j = 0; j <arr.length; j++) {
			
	// 		dataArr.push({
	// 			num: arr[j].num,
	// 			voteNum: arr[j].voteNum
	// 		})
	// 	}
	// }
	// initDataArr()
	function initDataArr(){
		//var URL = localStorage.getItem('url');

		$.ajax({
			
        type: "GET",

		url: './api/show/vote_num',

        dataType: "json",

		async:false,
		
		timeout: 5000,
        
        cache: false,

        data: {
        	//vote_model_id: localStorage.getItem('vote_model_id')
        	vote_model_id: 8
        },

        success: function (data) {
        	//console.log(data);
        	if (data.status_code == 200) {
        		arr = data.message;
        		for (var i=0; i < arr.length; i++) {
        			dataArr.push(arr[i]);
        		}
        		i = 0;
        	} else if (data.status_code == 203){
				//投票过期
				createToast(data.message, options);
			}else {
        		message = '获取列表失败, 请刷新页面';
				createToast(message, options);
        	}
        }

    });
	}

	var showDataArr = []
	function initUpdata(){
		var classArr = ['red', 'blue'];
		var htmlTemp= '';
		for(i = 0; i <dataArr.length; i++) {
			// htmlTemp +='<div id="candidate_modle" class="ca580ndidate">' + i < 10 ? ('0' + i) : i + '</br>'+	 arr[i] + '</div>'
			var id = dataArr[i].id < 10 ? '0' + dataArr[i].id : dataArr[i].id
			htmlTemp += '<div  class="group">'
				+ '<div class="data ">'+'<div class="showdata ' + classArr[i%2] +'" id=' + i +'></div>'+'</div>'  
				+ '<div class="circle ' + classArr[i%2] + '">'+ dataArr[i].vote_id+'</div>' 
				+ '<div>' +dataArr[i].vote_num+'</div>'

				+ '</div>';
		}
		content.innerHTML = htmlTemp;

		showDataArr.splice(0,showDataArr.length);
		for(var j = 0; j < dataArr.length; j++) {
			showDataArr.push(document.getElementById(j))
		}
	}



//模拟后台数据
// function genderate() {
// 		for(var j = 0; j < 39; j++) {
// 			dataArr.push({
// 				num: j,
// 				voteNum: Math.floor(Math.random() *1000 )   
// 			})
// 		}
// 	}

	// function genderateUp(dataArr){
	// 	for(var j = 0; j < 39; j++) {
	// 		//debugger
	// 		dataArr[j].voteNum  = dataArr[j].voteNum + 1;
	// 	}
	// }

//实时刷新



//设置高度


function renderShowDataHeight(dataArr) {
	var max = 0
	for(var j = 0; j < dataArr.length; j++) {
		if (max < dataArr[j].vote_num) {
			max = dataArr[j].vote_num
		}
	}
	for(var j = 0; j < dataArr.length; j++) {
        //计算高度
		var height = dataArr[j].vote_num / 200 * 6
		showDataArr[j].style.height = height + 'rem';

		if (showDataArr[j].parentNode) {
			showDataArr[j].parentNode.parentNode.lastChild.innerHTML = dataArr[j].vote_num+"票"
		} else {
			showDataArr[j].parentElement.parentElement.lastChild.innerHTML = dataArr[j].vote_num+"票"
		}
	}
}
  setInterval(function(){
	initDataArr();
	initUpdata();
	renderShowDataHeight(dataArr)
	dataArr = [];
	// update()
  }, 1000);   //刷新时间间隔 
//设置每一个投票条的高度

// function rowHeight(max,height){
// 	// itemheight / maxHeight = h / max;
// 	// 所以 itemheight = h / max * maxHeight;


// 	var maxHeight =200;
// 	var i = 1;

// 	var TopNode = new Array();
// 	var Top = 0;
	
// 	for(var h in height){
		
// 		i++;
// 		var itemheigth = height[h] / max * maxHeight;
// 		var node = document.getElementById(i);	
// 		node.style.height = (itemheigth ) + 'px';
// 		node.style.background = "";

// 		if(Top < height[h]){
// 			Top = height[h];
// 			TopNode = new Array();
// 			TopNode.push(node);
// 		}else if(Top == height[h]){
// 			TopNode.push(node);
// 		}
// 	}

// 	for(var i in TopNode){
// 		TopNode[i].style.background =  "#DB4545";
// 	}

// }

  
}