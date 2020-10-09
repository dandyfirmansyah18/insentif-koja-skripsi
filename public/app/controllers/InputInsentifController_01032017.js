arsipti.controller('InputInsentifController', function(dataFactory,$scope,$http){

	$scope.form_step1 = 'show';
	$scope.form_step2 = 'hide';
	$scope.form_step3 = 'hide';
	$scope.form_step4 = 'hide';
	$scope.form_step5 = 'hide';
	$scope.form_step6 = 'hide';

	$scope.param = '1.8';

	$scope.form_style1 = { 'background' : "#34495E" };
	$scope.form_style2 = { 'background' : "#34495E" };
	$scope.form_style3 = { 'background' : "#34495E" };
	$scope.form_style4 = { 'background' : "#34495E" };
	$scope.form_style5 = { 'background' : "#34495E" };
	$scope.form_style6 = { 'background' : "#34495E" };
	
	getCountBlock1();
	getCountBlock2();
	getCountBlock3();

	$scope.form = {
					incentiveparam:"",getCountBlock1:"",getCountBlock3:"",getCountBlock3:"",upperBlock1:"",upperBlock2:"",upperBlock3:"",bobot1:"",bobot2:"",bobot3:"",
					persenBlock1:"",persenBlock2:"",persenBlock3:"",persenBlockKar1:"",persenBlockKar2:"",persenBlockKar3:"",kueBlock1:"",kueBlock2:"",kueBlock3:"",
					avgBlock1:"",avgBlock2:"",avgBlock3:"",diffNomBlock1:"",diffNomBlock2:"",diffNomBlock3:"",diffPersenBlock1:"",diffPersenBlock2:"",diffPersenBlock3:"",
					BlockPerCom1:"",BlockPerCom2:"",BlockPerCom3:""
				  };

	$scope.upperBlockKey = function(upperBlock1,diffBlock3,diffBlock2,countBlock1,countBlock2,countBlock3){
		// countBlock1 = 118;
		// countBlock2 = 321;
		// countBlock3 = 17;
		var diffBlock3 = diffBlock3/100;
		var diffBlock2 = diffBlock2/100;
		var upperBlock3 = upperBlock1*(1-diffBlock3);
		var upperBlock2 = upperBlock3*(1-diffBlock2);
		var bobot1 = upperBlock1 * countBlock1;
		var bobot2 = upperBlock2 * countBlock2;
		var bobot3 = upperBlock3 * countBlock3;
		var sumbobot = bobot1 + bobot2 + bobot3;
		var persenBlock1 = bobot1/sumbobot * 100;
		var persenBlock2 = bobot2/sumbobot * 100;
		var persenBlock3 = bobot3/sumbobot * 100;
		var persenBlockKar1 = persenBlock1/countBlock1;
		var persenBlockKar2 = persenBlock2/countBlock2;
		var persenBlockKar3 = persenBlock3/countBlock3;
		var kueBlock1 = persenBlock1*1/100;
		var kueBlock2 = persenBlock2*1/100;
		var kueBlock3 = persenBlock3*1/100;
		var avgBlock1 = kueBlock1/countBlock1;
		var avgBlock2 = kueBlock2/countBlock2;
		var avgBlock3 = kueBlock3/countBlock3;
		var diffNomBlock1 = 0;
		var diffNomBlock2 = avgBlock3 - avgBlock2;
		var diffNomBlock3 = avgBlock1 - avgBlock3;
		var diffPersenBlock1 = 0;
		var diffPersenBlock2 = diffNomBlock2/avgBlock3*100;
		var diffPersenBlock3 = diffNomBlock3/avgBlock1*100;
		var sumkueperblock = kueBlock1 + kueBlock2 + kueBlock3;
		var BlockPerCom1 = kueBlock1/sumkueperblock*100;
		var BlockPerCom2 = kueBlock2/sumkueperblock*100;
		var BlockPerCom3 = kueBlock3/sumkueperblock*100;

		$scope.form.upperBlock3 = upperBlock3.toFixed(4);
		$scope.form.upperBlock2 = upperBlock2.toFixed(4);
		$scope.form.bobot1 = bobot1.toFixed(4);
		$scope.form.bobot2 = bobot2.toFixed(4);
		$scope.form.bobot3 = bobot3.toFixed(4);
		$scope.form.persenBlock1 = persenBlock1.toFixed(2);
		$scope.form.persenBlock2 = persenBlock2.toFixed(2);
		$scope.form.persenBlock3 = persenBlock3.toFixed(2);
		$scope.form.persenBlockKar1 = persenBlockKar1.toFixed(4);
		$scope.form.persenBlockKar2 = persenBlockKar2.toFixed(4);
		$scope.form.persenBlockKar3 = persenBlockKar3.toFixed(4);
		$scope.form.kueBlock1 = kueBlock1.toFixed(4);
		$scope.form.kueBlock2 = kueBlock2.toFixed(4);
		$scope.form.kueBlock3 = kueBlock3.toFixed(4);
		$scope.form.avgBlock1 = avgBlock1.toFixed(5);
		$scope.form.avgBlock2 = avgBlock2.toFixed(5);
		$scope.form.avgBlock3 = avgBlock3.toFixed(5);
		$scope.form.diffNomBlock1 = 0;
		$scope.form.diffNomBlock2 = diffNomBlock2.toFixed(5);
		$scope.form.diffNomBlock3 = diffNomBlock3.toFixed(5);
		$scope.form.diffPersenBlock1 = 0;
		$scope.form.diffPersenBlock2 = diffPersenBlock2.toFixed(2);
		$scope.form.diffPersenBlock3 = diffPersenBlock3.toFixed(2);
		$scope.form.BlockPerCom1 = BlockPerCom1.toFixed(2);
		$scope.form.BlockPerCom2 = BlockPerCom2.toFixed(2);
		$scope.form.BlockPerCom3 = BlockPerCom3.toFixed(2);

	}

	$scope.step1 = function(){
		$scope.form_step1 = 'show';
		$scope.form_step2 = 'hide';
		$scope.form_step3 = 'hide';
		$scope.form_step4 = 'hide';
		$scope.form_step5 = 'hide';
		$scope.form_step6 = 'hide';
	}

	$scope.step2 = function(){
		$scope.form_step1 = 'hide';
		$scope.form_step2 = 'show';
		$scope.form_step3 = 'hide';
		$scope.form_step4 = 'hide';
		$scope.form_step5 = 'hide';
		$scope.form_step6 = 'hide';

	}

	$scope.step3 = function(){
		$scope.form_step1 = 'hide';
		$scope.form_step2 = 'hide';
		$scope.form_step3 = 'show';
		$scope.form_step4 = 'hide';
		$scope.form_step5 = 'hide';
		$scope.form_step6 = 'hide';

	}

	$scope.step4 = function(){
		$scope.form_step1 = 'hide';
		$scope.form_step2 = 'hide';
		$scope.form_step3 = 'hide';
		$scope.form_step4 = 'show';
		$scope.form_step5 = 'hide';
		$scope.form_step6 = 'hide';

	}

	$scope.step5 = function(){
		$scope.form_step1 = 'hide';
		$scope.form_step2 = 'hide';
		$scope.form_step3 = 'hide';
		$scope.form_step4 = 'hide';
		$scope.form_step5 = 'show';
		$scope.form_step6 = 'hide';

	}

	$scope.step6 = function(){
		$scope.form_step1 = 'hide';
		$scope.form_step2 = 'hide';
		$scope.form_step3 = 'hide';
		$scope.form_step4 = 'hide';
		$scope.form_step5 = 'hide';
		$scope.form_step6 = 'show';

	}

	$scope.saveStep1 = function(form,param){		
	    dataFactory.httpRequest('saveStep1/'+param,'PUT',{},form).then(function(data) {
	    	if (data.success == true) {
				alert('success');
	    		$scope.form_style1 = { 'background' : "#1ABB9C" };
	    		$scope.form.incentiveparam = data.lastid;
	    		$scope.form_step1 = 'hide';
				$scope.form_step2 = 'show';
	    	}else{
	    		alert('gagal');
	    	};
	    });	    

	}

	$scope.saveStep2 = function(form){		
	    dataFactory.httpRequest('saveStep2','POST',{},form).then(function(data) {
	    	if (data.success == true) {
				alert('success');
	    		$scope.form_style2 = { 'background' : "#1ABB9C" };
	    		// $scope.form.incentiveparam = data.lastid;	    		
				$scope.form_step2 = 'hide';
				$scope.form_step3 = 'show';
	    	}else{
	    		alert('gagal');
	    	};
	    });	    

	}

	function getCountBlock1(){
      dataFactory.httpRequest('/getCountBlock1').then(function(data) {
        $scope.form.getCountBlock1 = data.count;        
      });
 	}

 	function getCountBlock2(){
      dataFactory.httpRequest('/getCountBlock2').then(function(data) {
        $scope.form.getCountBlock2 = data.count;        
      });
 	}

 	function getCountBlock3(){
      dataFactory.httpRequest('/getCountBlock3').then(function(data) {
        $scope.form.getCountBlock3 = data.count;        
      });
 	}

});