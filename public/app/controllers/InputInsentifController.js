arsipti.controller('InputInsentifController', function(dataFactory,$scope,$http,SweetAlert,ngProgress,Upload,$timeout){
	ngProgress.color('#169f85');
	$scope.form_step1 = 'show';
	$scope.form_step2 = 'hide';
	$scope.form_step3 = 'hide';
	$scope.form_step4 = 'hide';
	$scope.form_step5 = 'hide';


	

	$scope.form_style1 = { 'background' : "#34495E" };
	$scope.form_style2 = { 'background' : "#34495E" };
	$scope.form_style3 = { 'background' : "#34495E" };
	$scope.form_style4 = { 'background' : "#34495E" };
	$scope.form_style5 = { 'background' : "#34495E" };


	$scope.getQccGroupA = {};
	$scope.getQccGroupB = {};
	$scope.getQccGroupC = {};
	$scope.getQccGroupD = {};

	$scope.getPotonganKar = {};

	// getCountBlock1();
	// getCountBlock2();
	// getCountBlock3();
	cekSetting();
	cekStep();

	getqccrtgmin();
	// getQccGroupA();
	// getQccGroupB();
	// getQccGroupC();
	// getQccGroupD();
	// getRtgGroupA();
	// getRtgGroupB();
	// getRtgGroupC();
	// getRtgGroupD();

	$scope.form = {
					incentiveparam:"",upperBlock1:"",upperBlock2:"",upperBlock3:"",bobot1:"",bobot2:"",bobot3:"",
					persenBlock1:"",persenBlock2:"",persenBlock3:"",persenBlockKar1:"",persenBlockKar2:"",persenBlockKar3:"",kueBlock1:"",kueBlock2:"",kueBlock3:"",
					avgBlock1:"",avgBlock2:"",avgBlock3:"",diffNomBlock1:"",diffNomBlock2:"",diffNomBlock3:"",diffPersenBlock1:"",diffPersenBlock2:"",diffPersenBlock3:"",
					BlockPerCom1:"",BlockPerCom2:"",BlockPerCom3:"", 
					BoxPerGroupA:0,BoxPerOrangA:0, OverBoxA:0, InsentifPerUnitA:0,InsentifPerBoxA:0,
					BoxPerGroupB:0,BoxPerOrangB:0, OverBoxB:0, InsentifPerUnitB:0,InsentifPerBoxB:0,
					BoxPerGroupC:0,BoxPerOrangC:0, OverBoxC:0, InsentifPerUnitC:0,InsentifPerBoxC:0,
					BoxPerGroupD:0,BoxPerOrangD:0, OverBoxD:0, InsentifPerUnitD:0,InsentifPerBoxD:0,
					minboxrtg:0,kueinsrtg:0,
					minboxqcc:0,kueinsqcc:0
				  };

	$scope.value = {
						BoxPerGroupA:0
					};

	$scope.calculate_total_insentive = function(param_percent,income)
	{
		var income = income;
		income = income.substring(4);

		var array = income.split(".");
		var incomefix = parseFloat(array.join(""));

		var param_percent = parseFloat(param_percent);
		// var incentive_val = Math.round(param_percent / 100 * incomefix);
		var incentive_val = param_percent / 100 * incomefix;
		
		$scope.form.total_insentif = incentive_val;

	}

	function getqccrtgmin()
	{
		dataFactory.httpRequest('/getqccrtgmin').then(function(getqccrtgmin) {
			$scope.AP_COST_BOX_QCC = getqccrtgmin['AP_COST_BOX_QCC'];
			$scope.AP_COST_BOX_RTG = getqccrtgmin['AP_COST_BOX_RTG']; 
		});
	}

	function cekSetting(){
		dataFactory.httpRequest('/cekSetting').then(function(cekSetting) {
			// alert(cekSetting.success)
			if (cekSetting.success == 'salah') {
				SweetAlert.swal('Tidak Dapat Input Insentif, Cek Settingan Anda!');
				window.location = site_url+"/#/adjustparam";
			}else{
				$scope.param = cekSetting.param;
			};
		});
	}

	function cekStep(){
		dataFactory.httpRequest('/cekStep').then(function(cekstep) {

			
	    	if (cekstep.status_last == 1) {
	    		$scope.form_style1 = { 'background' : "#1ABB9C" };
	    		$scope.form_step1 = 'show';	    		
	    		dataFactory.httpRequest('showStep1/'+cekstep.incentiveparam,'PUT',{},'').then(function(step1){	   
	    		ngProgress.start();
	    		angular.element('#mydiv').show();

	    			getCountBlock1();
	    			getCountBlock2();
	    			getCountBlock3();

	    			$scope.form = step1;
	    			$scope.param = step1.param;
	    			// $scope.form.total_insentif = step1.INCENTIVE_PARAM_VALUE;
	    		
	    		angular.element('#mydiv').hide();
	    		ngProgress.complete();
	    		});


	    	}else if (cekstep.status_last == 2) {
	    		$scope.form_style1 = { 'background' : "#1ABB9C" };
	    		$scope.form_style2 = { 'background' : "#1ABB9C" };
	    		$scope.form_step1 = 'hide';
	    		$scope.form_step2 = 'show';
	    		$scope.form_step3 = 'hide';
	    		dataFactory.httpRequest('showStep1/'+cekstep.incentiveparam,'PUT',{},'').then(function(step1){	  
	    		ngProgress.start();
	    		angular.element('#mydiv').show();
	    		  			
	    			getCountBlock1();
	    			getCountBlock2();
	    			getCountBlock3();

	    			$scope.form = step1;
	    			$scope.param = step1.param;
	    			// $scope.form.total_insentif = step1.INCENTIVE_PARAM_VALUE;

	    	
	    		});

	    		dataFactory.httpRequest('showStep2/'+cekstep.incentiveparam,'PUT',{},'').then(function(step2){	

	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCA = step2[0].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCB = step2[1].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCC = step2[2].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCD = step2[3].BOX_ADJUSMENT_GROUP_ID;

	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGA = step2[4].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGB = step2[5].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGC = step2[6].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGD = step2[7].BOX_ADJUSMENT_GROUP_ID;

	    			//

	    			$scope.form.QccboxpergroupA = step2[0].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.QccboxpergroupB = step2[1].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.QccboxpergroupC = step2[2].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.QccboxpergroupD = step2[3].BOX_ADJUSMENT_GROUP_VALUE;

	    			$scope.form.RtgboxpergroupA = step2[4].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgboxpergroupB = step2[5].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgboxpergroupC = step2[6].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgboxpergroupD = step2[7].BOX_ADJUSMENT_GROUP_VALUE;

	    			angular.element('#mydiv').hide();
					ngProgress.complete();
	    		});

	    	}else if (cekstep.status_last == 3) {

	    		$scope.form_style1 = { 'background' : "#1ABB9C" };
	    		$scope.form_style2 = { 'background' : "#1ABB9C" };
	    		$scope.form_style3 = { 'background' : "#1ABB9C" };
	    		$scope.form_step1 = 'hide';
	    		$scope.form_step2 = 'hide';
	    		$scope.form_step3 = 'show';

	    		dataFactory.httpRequest('showStep1/'+cekstep.incentiveparam,'PUT',{},'').then(function(step1){	  
	    		ngProgress.start();
	    		angular.element('#mydiv').show();
	    		  			
	    			getCountBlock1();
	    			getCountBlock2();
	    			getCountBlock3();

	    			$scope.form = step1;
	    			$scope.param = step1.param;
	    			// $scope.form.total_insentif = step1.INCENTIVE_PARAM_VALUE;

	    	
	    		});

	    		dataFactory.httpRequest('showStep2/'+cekstep.incentiveparam,'PUT',{},'').then(function(step2){	



	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCA = step2[0].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCB = step2[1].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCC = step2[2].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCD = step2[3].BOX_ADJUSMENT_GROUP_ID;

	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGA = step2[4].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGB = step2[5].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGC = step2[6].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGD = step2[7].BOX_ADJUSMENT_GROUP_ID;

	    			//

	    			$scope.form.QccboxpergroupA = step2[0].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.QccboxpergroupB = step2[1].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.QccboxpergroupC = step2[2].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.QccboxpergroupD = step2[3].BOX_ADJUSMENT_GROUP_VALUE;

	    			$scope.form.RtgboxpergroupA = step2[4].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgboxpergroupB = step2[5].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgboxpergroupC = step2[6].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgboxpergroupD = step2[7].BOX_ADJUSMENT_GROUP_VALUE;

	    			$scope.form.BoxPerGroupA = step2[0].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.BoxPerGroupB = step2[1].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.BoxPerGroupC = step2[2].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.BoxPerGroupD = step2[3].BOX_ADJUSMENT_GROUP_VALUE;

	    			$scope.form.RtgBoxPerGroupA = step2[4].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgBoxPerGroupB = step2[5].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgBoxPerGroupC = step2[6].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgBoxPerGroupD = step2[7].BOX_ADJUSMENT_GROUP_VALUE;

	    		});

				getQccGroupA(cekstep.incentiveparam);
				getQccGroupB(cekstep.incentiveparam);
				getQccGroupC(cekstep.incentiveparam);
				getQccGroupD(cekstep.incentiveparam);
				getRtgGroupA(cekstep.incentiveparam);
				getRtgGroupB(cekstep.incentiveparam);
				getRtgGroupC(cekstep.incentiveparam);
				getRtgGroupD(cekstep.incentiveparam);

				dataFactory.httpRequest('showStep3/'+cekstep.incentiveparam,'PUT',{},'').then(function(step3){					

					$scope.form.BoxPerOrangA = step3.BoxPerOrangA;
					$scope.form.BoxPerOrangB = step3.BoxPerOrangB;
					$scope.form.BoxPerOrangC = step3.BoxPerOrangC;
					$scope.form.BoxPerOrangD = step3.BoxPerOrangD;

					$scope.form.RtgBoxPerOrangA = step3.RtgBoxPerOrangA;
					$scope.form.RtgBoxPerOrangB = step3.RtgBoxPerOrangB;
					$scope.form.RtgBoxPerOrangC = step3.RtgBoxPerOrangC;
					$scope.form.RtgBoxPerOrangD = step3.RtgBoxPerOrangD;

					$scope.form.InsentifPerUnitA = step3.InsentifPerUnitA
					$scope.form.InsentifPerUnitB = step3.InsentifPerUnitB
					$scope.form.InsentifPerUnitC = step3.InsentifPerUnitC
					$scope.form.InsentifPerUnitD = step3.InsentifPerUnitD

					$scope.form.RtgInsentifPerUnitA = step3.RtgInsentifPerUnitA
					$scope.form.RtgInsentifPerUnitB = step3.RtgInsentifPerUnitB
					$scope.form.RtgInsentifPerUnitC = step3.RtgInsentifPerUnitC
					$scope.form.RtgInsentifPerUnitD = step3.RtgInsentifPerUnitD

					$scope.form.InsentifPerBoxA = step3.InsentifPerBoxA
					$scope.form.InsentifPerBoxB = step3.InsentifPerBoxB
					$scope.form.InsentifPerBoxC = step3.InsentifPerBoxC
					$scope.form.InsentifPerBoxD = step3.InsentifPerBoxD

					$scope.form.RtgInsentifPerBoxA = step3.RtgInsentifPerBoxA
					$scope.form.RtgInsentifPerBoxB = step3.RtgInsentifPerBoxB
					$scope.form.RtgInsentifPerBoxC = step3.RtgInsentifPerBoxC
					$scope.form.RtgInsentifPerBoxD = step3.RtgInsentifPerBoxD

					$scope.form.OverBoxA = step3.OverBoxA;
					$scope.form.OverBoxB = step3.OverBoxB;
					$scope.form.OverBoxC = step3.OverBoxC;
					$scope.form.OverBoxD = step3.OverBoxD;

					$scope.form.RtgOverBoxA = step3.RtgOverBoxA;
					$scope.form.RtgOverBoxB = step3.RtgOverBoxB;
					$scope.form.RtgOverBoxC = step3.RtgOverBoxC;
					$scope.form.RtgOverBoxD = step3.RtgOverBoxD;

					$scope.form.minboxqcc = step3.minboxqcc;
					$scope.form.kueinsqcc = step3.kueinsqcc;
					$scope.form.minboxrtg = step3.minboxrtg;
					$scope.form.kueinsrtg = step3.kueinsrtg;

				angular.element('#mydiv').hide();
				ngProgress.complete();
				});

				

	    	}else if (cekstep.status_last == 4) {

	    		$scope.form_style1 = { 'background' : "#1ABB9C" };
	    		$scope.form_style2 = { 'background' : "#1ABB9C" };
	    		$scope.form_style3 = { 'background' : "#1ABB9C" };
	    		$scope.form_style4 = { 'background' : "#1ABB9C" };
	    		$scope.form_step1 = 'hide';
	    		$scope.form_step2 = 'hide';
	    		$scope.form_step3 = 'hide';
	    		$scope.form_step4 = 'show';

	    		dataFactory.httpRequest('showStep1/'+cekstep.incentiveparam,'PUT',{},'').then(function(step1){	 

	    		ngProgress.start();
	    		angular.element('#mydiv').show();
	    		  			
	    			getCountBlock1();
	    			getCountBlock2();
	    			getCountBlock3();

	    			$scope.form = step1;
	    			$scope.param = step1.param;
	    			// $scope.form.total_insentif = step1.INCENTIVE_PARAM_VALUE;

	    	
	    		});

	    		dataFactory.httpRequest('showStep2/'+cekstep.incentiveparam,'PUT',{},'').then(function(step2){	

	    			console.log(step2);

	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCA = step2[0].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCB = step2[1].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCC = step2[2].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCD = step2[3].BOX_ADJUSMENT_GROUP_ID;

	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGA = step2[4].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGB = step2[5].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGC = step2[6].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGD = step2[7].BOX_ADJUSMENT_GROUP_ID;

	    			//

	    			$scope.form.QccboxpergroupA = step2[0].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.QccboxpergroupB = step2[1].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.QccboxpergroupC = step2[2].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.QccboxpergroupD = step2[3].BOX_ADJUSMENT_GROUP_VALUE;

	    			$scope.form.RtgboxpergroupA = step2[4].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgboxpergroupB = step2[5].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgboxpergroupC = step2[6].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgboxpergroupD = step2[7].BOX_ADJUSMENT_GROUP_VALUE;

	    			$scope.form.BoxPerGroupA = step2[0].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.BoxPerGroupB = step2[1].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.BoxPerGroupC = step2[2].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.BoxPerGroupD = step2[3].BOX_ADJUSMENT_GROUP_VALUE;

	    			$scope.form.RtgBoxPerGroupA = step2[4].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgBoxPerGroupB = step2[5].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgBoxPerGroupC = step2[6].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgBoxPerGroupD = step2[7].BOX_ADJUSMENT_GROUP_VALUE;

	    		});

				getQccGroupA(cekstep.incentiveparam);
				getQccGroupB(cekstep.incentiveparam);
				getQccGroupC(cekstep.incentiveparam);
				getQccGroupD(cekstep.incentiveparam);
				getRtgGroupA(cekstep.incentiveparam);
				getRtgGroupB(cekstep.incentiveparam);
				getRtgGroupC(cekstep.incentiveparam);
				getRtgGroupD(cekstep.incentiveparam);

				getPotonganKar(cekstep.incentiveparam);

				dataFactory.httpRequest('showStep3/'+cekstep.incentiveparam,'PUT',{},'').then(function(step3){					

					$scope.form.BoxPerOrangA = step3.BoxPerOrangA;
					$scope.form.BoxPerOrangB = step3.BoxPerOrangB;
					$scope.form.BoxPerOrangC = step3.BoxPerOrangC;
					$scope.form.BoxPerOrangD = step3.BoxPerOrangD;

					$scope.form.RtgBoxPerOrangA = step3.RtgBoxPerOrangA;
					$scope.form.RtgBoxPerOrangB = step3.RtgBoxPerOrangB;
					$scope.form.RtgBoxPerOrangC = step3.RtgBoxPerOrangC;
					$scope.form.RtgBoxPerOrangD = step3.RtgBoxPerOrangD;

					$scope.form.InsentifPerUnitA = step3.InsentifPerUnitA
					$scope.form.InsentifPerUnitB = step3.InsentifPerUnitB
					$scope.form.InsentifPerUnitC = step3.InsentifPerUnitC
					$scope.form.InsentifPerUnitD = step3.InsentifPerUnitD

					$scope.form.RtgInsentifPerUnitA = step3.RtgInsentifPerUnitA
					$scope.form.RtgInsentifPerUnitB = step3.RtgInsentifPerUnitB
					$scope.form.RtgInsentifPerUnitC = step3.RtgInsentifPerUnitC
					$scope.form.RtgInsentifPerUnitD = step3.RtgInsentifPerUnitD

					$scope.form.InsentifPerBoxA = step3.InsentifPerBoxA
					$scope.form.InsentifPerBoxB = step3.InsentifPerBoxB
					$scope.form.InsentifPerBoxC = step3.InsentifPerBoxC
					$scope.form.InsentifPerBoxD = step3.InsentifPerBoxD

					$scope.form.RtgInsentifPerBoxA = step3.RtgInsentifPerBoxA
					$scope.form.RtgInsentifPerBoxB = step3.RtgInsentifPerBoxB
					$scope.form.RtgInsentifPerBoxC = step3.RtgInsentifPerBoxC
					$scope.form.RtgInsentifPerBoxD = step3.RtgInsentifPerBoxD

					$scope.form.OverBoxA = step3.OverBoxA;
					$scope.form.OverBoxB = step3.OverBoxB;
					$scope.form.OverBoxC = step3.OverBoxC;
					$scope.form.OverBoxD = step3.OverBoxD;

					$scope.form.RtgOverBoxA = step3.RtgOverBoxA;
					$scope.form.RtgOverBoxB = step3.RtgOverBoxB;
					$scope.form.RtgOverBoxC = step3.RtgOverBoxC;
					$scope.form.RtgOverBoxD = step3.RtgOverBoxD;

					$scope.form.minboxqcc = step3.minboxqcc;
					$scope.form.kueinsqcc = step3.kueinsqcc;
					$scope.form.minboxrtg = step3.minboxrtg;
					$scope.form.kueinsrtg = step3.kueinsrtg;


					angular.element('#mydiv').hide();
					ngProgress.complete();
				});

				

	    	}else if (cekstep.status_last == 5) {
	    		$scope.form_style1 = { 'background' : "#1ABB9C" };
	    		$scope.form_style2 = { 'background' : "#1ABB9C" };
	    		$scope.form_style3 = { 'background' : "#1ABB9C" };
	    		$scope.form_style4 = { 'background' : "#1ABB9C" };
	    		$scope.form_style5 = { 'background' : "#1ABB9C" };
	    		$scope.form_step1 = 'hide';
	    		$scope.form_step2 = 'hide';
	    		$scope.form_step3 = 'hide';
	    		$scope.form_step4 = 'hide';
	    		$scope.form_step5 = 'show';

	    		dataFactory.httpRequest('showStep1/'+cekstep.incentiveparam,'PUT',{},'').then(function(step1){	  
	    		ngProgress.start();
	    		angular.element('#mydiv').show();
	    		  			
	    			getCountBlock1();
	    			getCountBlock2();
	    			getCountBlock3();

	    			$scope.form = step1;
	    			$scope.param = step1.param;
	    			// $scope.form.total_insentif = step1.INCENTIVE_PARAM_VALUE;

	    	
	    		});

	    		dataFactory.httpRequest('showStep2/'+cekstep.incentiveparam,'PUT',{},'').then(function(step2){	

	    			console.log(step2);

	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCA = step2[0].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCB = step2[1].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCC = step2[2].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCD = step2[3].BOX_ADJUSMENT_GROUP_ID;

	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGA = step2[4].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGB = step2[5].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGC = step2[6].BOX_ADJUSMENT_GROUP_ID;
	    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGD = step2[7].BOX_ADJUSMENT_GROUP_ID;

	    			//

	    			$scope.form.QccboxpergroupA = step2[0].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.QccboxpergroupB = step2[1].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.QccboxpergroupC = step2[2].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.QccboxpergroupD = step2[3].BOX_ADJUSMENT_GROUP_VALUE;

	    			$scope.form.RtgboxpergroupA = step2[4].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgboxpergroupB = step2[5].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgboxpergroupC = step2[6].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgboxpergroupD = step2[7].BOX_ADJUSMENT_GROUP_VALUE;

	    			$scope.form.BoxPerGroupA = step2[0].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.BoxPerGroupB = step2[1].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.BoxPerGroupC = step2[2].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.BoxPerGroupD = step2[3].BOX_ADJUSMENT_GROUP_VALUE;

	    			$scope.form.RtgBoxPerGroupA = step2[4].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgBoxPerGroupB = step2[5].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgBoxPerGroupC = step2[6].BOX_ADJUSMENT_GROUP_VALUE;
	    			$scope.form.RtgBoxPerGroupD = step2[7].BOX_ADJUSMENT_GROUP_VALUE;

	    		});

				getQccGroupA(cekstep.incentiveparam);
				getQccGroupB(cekstep.incentiveparam);
				getQccGroupC(cekstep.incentiveparam);
				getQccGroupD(cekstep.incentiveparam);
				getRtgGroupA(cekstep.incentiveparam);
				getRtgGroupB(cekstep.incentiveparam);
				getRtgGroupC(cekstep.incentiveparam);
				getRtgGroupD(cekstep.incentiveparam);

				getPotonganKar(cekstep.incentiveparam);

				previewInsentif(cekstep.incentiveparam);

				dataFactory.httpRequest('showStep3/'+cekstep.incentiveparam,'PUT',{},'').then(function(step3){					

					$scope.form.BoxPerOrangA = step3.BoxPerOrangA;
					$scope.form.BoxPerOrangB = step3.BoxPerOrangB;
					$scope.form.BoxPerOrangC = step3.BoxPerOrangC;
					$scope.form.BoxPerOrangD = step3.BoxPerOrangD;

					$scope.form.RtgBoxPerOrangA = step3.RtgBoxPerOrangA;
					$scope.form.RtgBoxPerOrangB = step3.RtgBoxPerOrangB;
					$scope.form.RtgBoxPerOrangC = step3.RtgBoxPerOrangC;
					$scope.form.RtgBoxPerOrangD = step3.RtgBoxPerOrangD;

					$scope.form.InsentifPerUnitA = step3.InsentifPerUnitA
					$scope.form.InsentifPerUnitB = step3.InsentifPerUnitB
					$scope.form.InsentifPerUnitC = step3.InsentifPerUnitC
					$scope.form.InsentifPerUnitD = step3.InsentifPerUnitD

					$scope.form.RtgInsentifPerUnitA = step3.RtgInsentifPerUnitA
					$scope.form.RtgInsentifPerUnitB = step3.RtgInsentifPerUnitB
					$scope.form.RtgInsentifPerUnitC = step3.RtgInsentifPerUnitC
					$scope.form.RtgInsentifPerUnitD = step3.RtgInsentifPerUnitD

					$scope.form.InsentifPerBoxA = step3.InsentifPerBoxA
					$scope.form.InsentifPerBoxB = step3.InsentifPerBoxB
					$scope.form.InsentifPerBoxC = step3.InsentifPerBoxC
					$scope.form.InsentifPerBoxD = step3.InsentifPerBoxD

					$scope.form.RtgInsentifPerBoxA = step3.RtgInsentifPerBoxA
					$scope.form.RtgInsentifPerBoxB = step3.RtgInsentifPerBoxB
					$scope.form.RtgInsentifPerBoxC = step3.RtgInsentifPerBoxC
					$scope.form.RtgInsentifPerBoxD = step3.RtgInsentifPerBoxD

					$scope.form.OverBoxA = step3.OverBoxA;
					$scope.form.OverBoxB = step3.OverBoxB;
					$scope.form.OverBoxC = step3.OverBoxC;
					$scope.form.OverBoxD = step3.OverBoxD;

					$scope.form.RtgOverBoxA = step3.RtgOverBoxA;
					$scope.form.RtgOverBoxB = step3.RtgOverBoxB;
					$scope.form.RtgOverBoxC = step3.RtgOverBoxC;
					$scope.form.RtgOverBoxD = step3.RtgOverBoxD;

					$scope.form.minboxqcc = step3.minboxqcc;
					$scope.form.kueinsqcc = step3.kueinsqcc;
					$scope.form.minboxrtg = step3.minboxrtg;
					$scope.form.kueinsrtg = step3.kueinsrtg;


				angular.element('#mydiv').hide();
				ngProgress.complete();
				});



	    	}else{
	    		ngProgress.start();
	    		getCountBlock1();
    			getCountBlock2();
    			getCountBlock3();
    			angular.element('#mydiv').hide();
    			ngProgress.complete();
	    	};
	    });	   
	}

	$scope.step1 = function(){
		$scope.form_step1 = 'show';
		$scope.form_step2 = 'hide';
		$scope.form_step3 = 'hide';
		$scope.form_step4 = 'hide';
		$scope.form_step5 = 'hide';
	}

	$scope.step2 = function(){
		$scope.form_step1 = 'hide';
		$scope.form_step2 = 'show';
		$scope.form_step3 = 'hide';
		$scope.form_step4 = 'hide';
		$scope.form_step5 = 'hide';

	}

	$scope.step3 = function(){
		$scope.form_step1 = 'hide';
		$scope.form_step2 = 'hide';
		$scope.form_step3 = 'show';
		$scope.form_step4 = 'hide';
		$scope.form_step5 = 'hide';

	}

	$scope.step4 = function(){
		$scope.form_step1 = 'hide';
		$scope.form_step2 = 'hide';
		$scope.form_step3 = 'hide';
		$scope.form_step4 = 'show';
		$scope.form_step5 = 'hide';

	}

	$scope.step5 = function(){
		$scope.form_step1 = 'hide';
		$scope.form_step2 = 'hide';
		$scope.form_step3 = 'hide';
		$scope.form_step4 = 'hide';
		$scope.form_step5 = 'show';

	}

	$scope.saveStep1 = function(form,param){	

		if (!$scope.form.tahun || !$scope.form.penghasilan || !$scope.form.bulan) {
			 SweetAlert.swal("Oops!", "Isi Data dengan Lengkap!", "error");
		}else{
		    ngProgress.start();
		    angular.element('#mydiv').show()
		    dataFactory.httpRequest('saveStep1/'+param,'PUT',{},form).then(function(data) {
		    	if (data.success == true) {
					// alert('success');
		    		$scope.form_style1 = { 'background' : "#1ABB9C" };
		    		$scope.form_style2 = { 'background' : "#1ABB9C" };
		    		$scope.form.incentiveparam = data.lastid;
		    		$scope.form_step1 = 'hide';
					$scope.form_step2 = 'show';

					//cek jika step 2 sudah terisi
					dataFactory.httpRequest('checkStep2/'+data.lastid,'PUT',{},'').then(function(checkStep2){	
						if (checkStep2 > 0) {							
							dataFactory.httpRequest('showStep2/'+data.lastid,'PUT',{},'').then(function(step2){	

				    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCA = step2[0].BOX_ADJUSMENT_GROUP_ID;
				    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCB = step2[1].BOX_ADJUSMENT_GROUP_ID;
				    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCC = step2[2].BOX_ADJUSMENT_GROUP_ID;
				    			$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCD = step2[3].BOX_ADJUSMENT_GROUP_ID;

				    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGA = step2[4].BOX_ADJUSMENT_GROUP_ID;
				    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGB = step2[5].BOX_ADJUSMENT_GROUP_ID;
				    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGC = step2[6].BOX_ADJUSMENT_GROUP_ID;
				    			$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGD = step2[7].BOX_ADJUSMENT_GROUP_ID;		    		

				    			$scope.form.QccboxpergroupA = step2[0].BOX_ADJUSMENT_GROUP_VALUE;
				    			$scope.form.QccboxpergroupB = step2[1].BOX_ADJUSMENT_GROUP_VALUE;
				    			$scope.form.QccboxpergroupC = step2[2].BOX_ADJUSMENT_GROUP_VALUE;
				    			$scope.form.QccboxpergroupD = step2[3].BOX_ADJUSMENT_GROUP_VALUE;

				    			$scope.form.RtgboxpergroupA = step2[4].BOX_ADJUSMENT_GROUP_VALUE;
				    			$scope.form.RtgboxpergroupB = step2[5].BOX_ADJUSMENT_GROUP_VALUE;
				    			$scope.form.RtgboxpergroupC = step2[6].BOX_ADJUSMENT_GROUP_VALUE;
				    			$scope.form.RtgboxpergroupD = step2[7].BOX_ADJUSMENT_GROUP_VALUE;

								angular.element('#mydiv').hide();
								ngProgress.complete();
				    		});
						}
					});					

				angular.element('#mydiv').hide();
				ngProgress.complete();

		    	}else{		    		
		    	angular.element('#mydiv').hide();
		    	SweetAlert.swal("Oops!", "Insentif dengan Bulan Tahun ini Sudah Ada!", "error");
		    	ngProgress.complete();
		    	};
		    });	    
		};


	}

	$scope.saveStep2 = function(form){	

		if (!$scope.form.QccboxpergroupA || !$scope.form.QccboxpergroupB || !$scope.form.QccboxpergroupC || !$scope.form.QccboxpergroupD || 
			!$scope.form.RtgboxpergroupA || !$scope.form.RtgboxpergroupB || !$scope.form.RtgboxpergroupC || !$scope.form.RtgboxpergroupD) {
			SweetAlert.swal("Oops!", "Isi Data dengan Lengkap!", "error");
		}else if($scope.form.QccboxpergroupA == 0 || $scope.form.QccboxpergroupB == 0 || $scope.form.QccboxpergroupC == 0 || $scope.form.QccboxpergroupD == 0 || 
			$scope.form.RtgboxpergroupA == 0 || $scope.form.RtgboxpergroupB == 0 || $scope.form.RtgboxpergroupC == 0 || $scope.form.RtgboxpergroupD == 0){
			SweetAlert.swal("Oops!", "Inputan tidak boleh 0!", "error");
		}else{
			ngProgress.start();
			angular.element('#mydiv').show();
			dataFactory.httpRequest('/saveStep2','POST',{},form).then(function(data) {
		    	if (data.success == true) {
		    		// alert('success');
		    		$scope.form_style2 = { 'background' : "#1ABB9C" };
		    		$scope.form_style3 = { 'background' : "#1ABB9C" };
		    		$scope.form_step2 = 'hide';
					$scope.form_step3 = 'show';

					$scope.form.BoxPerGroupA = data.BoxPerGroupA;
					$scope.form.InsentifPerUnitA = data.InsentifPerUnitA;
					$scope.form.InsentifPerBoxA = data.InsentifPerBoxA;
					$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCA = data.BOX_ADJUSMENT_GROUP_ID_QCCA;
					$scope.form.BoxPerOrangA = data.BoxPerOrangA;

					$scope.form.BoxPerGroupB = data.BoxPerGroupB;
					$scope.form.InsentifPerUnitB = data.InsentifPerUnitB;
					$scope.form.InsentifPerBoxB = data.InsentifPerBoxB;
					$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCB = data.BOX_ADJUSMENT_GROUP_ID_QCCB;
					$scope.form.BoxPerOrangB = data.BoxPerOrangB;

					$scope.form.BoxPerGroupC = data.BoxPerGroupC;
					$scope.form.InsentifPerUnitC = data.InsentifPerUnitC;
					$scope.form.InsentifPerBoxC = data.InsentifPerBoxC;
					$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCC = data.BOX_ADJUSMENT_GROUP_ID_QCCC;
					$scope.form.BoxPerOrangC = data.BoxPerOrangC;

					$scope.form.BoxPerGroupD = data.BoxPerGroupD;
					$scope.form.InsentifPerUnitD = data.InsentifPerUnitD;
					$scope.form.InsentifPerBoxD = data.InsentifPerBoxD;
					$scope.form.BOX_ADJUSMENT_GROUP_ID_QCCD = data.BOX_ADJUSMENT_GROUP_ID_QCCD;
					$scope.form.BoxPerOrangD = data.BoxPerOrangD;

					$scope.form.RtgBoxPerGroupA = data.RtgBoxPerGroupA;
					$scope.form.RtgInsentifPerUnitA = data.RtgInsentifPerUnitA;
					$scope.form.RtgInsentifPerBoxA = data.RtgInsentifPerBoxA;
					$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGA = data.BOX_ADJUSMENT_GROUP_ID_RTGA;
					$scope.form.RtgBoxPerOrangA = data.RtgBoxPerOrangA;

					$scope.form.RtgBoxPerGroupB = data.RtgBoxPerGroupB;
					$scope.form.RtgInsentifPerUnitB = data.RtgInsentifPerUnitB;
					$scope.form.RtgInsentifPerBoxB = data.RtgInsentifPerBoxB;
					$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGB = data.BOX_ADJUSMENT_GROUP_ID_RTGB;
					$scope.form.RtgBoxPerOrangB = data.RtgBoxPerOrangB;

					$scope.form.RtgBoxPerGroupC = data.RtgBoxPerGroupC;
					$scope.form.RtgInsentifPerUnitC = data.RtgInsentifPerUnitC;
					$scope.form.RtgInsentifPerBoxC = data.RtgInsentifPerBoxC;
					$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGC = data.BOX_ADJUSMENT_GROUP_ID_RTGC;
					$scope.form.RtgBoxPerOrangC = data.RtgBoxPerOrangC;

					$scope.form.RtgBoxPerGroupD = data.RtgBoxPerGroupD;
					$scope.form.RtgInsentifPerUnitD = data.RtgInsentifPerUnitD;
					$scope.form.RtgInsentifPerBoxD = data.RtgInsentifPerBoxD;
					$scope.form.BOX_ADJUSMENT_GROUP_ID_RTGD = data.BOX_ADJUSMENT_GROUP_ID_RTGD;
					$scope.form.RtgBoxPerOrangD = data.RtgBoxPerOrangD;

					$scope.form.OverBoxA = data.OverBoxA;
					$scope.form.OverBoxB = data.OverBoxB;
					$scope.form.OverBoxC = data.OverBoxC;
					$scope.form.OverBoxD = data.OverBoxD;

					$scope.form.RtgOverBoxA = data.RtgOverBoxA;
					$scope.form.RtgOverBoxB = data.RtgOverBoxB;
					$scope.form.RtgOverBoxC = data.RtgOverBoxC;
					$scope.form.RtgOverBoxD = data.RtgOverBoxD;

					$scope.form.minboxqcc = data.minboxqcc;
					$scope.form.kueinsqcc = data.kueinsqcc;
					$scope.form.minboxrtg = data.minboxrtg;
					$scope.form.kueinsrtg = data.kueinsrtg;

					var param = data.incentiveparam

					getQccGroupA(param);
					getQccGroupB(param);
					getQccGroupC(param);
					getQccGroupD(param);
					getRtgGroupA(param);
					getRtgGroupB(param);
					getRtgGroupC(param);
					getRtgGroupD(param);

					angular.element('#mydiv').hide();
					ngProgress.complete();
					
		    	} else {
		    		// alert('gagal');
		    		angular.element('#mydiv').hide();
		    		ngProgress.complete();
		    	};
		    });	
		};
	        

	}

	$scope.saveStep3 = function(form){
		ngProgress.start();
		angular.element('#mydiv').show();
		dataFactory.httpRequest('/saveStep3','POST',{},form).then(function(data) {
	    	if (data.success == true) {
				// alert('success');


				getPotonganKar(data.incentiveparam);

	    		$scope.form_style3 = { 'background' : "#1ABB9C" };
	    		$scope.form_style4 = { 'background' : "#1ABB9C" };
	    		$scope.form_step3 = 'hide';
				$scope.form_step4 = 'show';

				angular.element('#mydiv').hide();
				ngProgress.complete();

	    	}else{
	    		// alert('gagal');
	    	};
	    });	 
	}

	$scope.saveStep4 = function(form){

		ngProgress.start();
		angular.element('#mydiv').show();
		dataFactory.httpRequest('/saveStep4','POST',{},form).then(function(data) {
	    	if (data.success == true) {
			
				previewInsentif(form.incentiveparam);

				$scope.form_style4 = { 'background' : "#1ABB9C" };
	    		$scope.form_style5 = { 'background' : "#1ABB9C" };
	    		$scope.form_step4 = 'hide';
				$scope.form_step5 = 'show';
				
				angular.element('#mydiv').hide();
				ngProgress.complete();

	    	}else{
	    		// alert('gagal');
	    	};
	    });	 
	}

	$scope.saveStep5 = function(incentiveparam){
		SweetAlert.swal({
		  title: "Apakah Anda Yakin?",
		  text: "Jika anda sudah click Posting, maka tidak bisa kembali ke Step sebelumnya lagi!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "Ya, Posting!",
		  cancelButtonText: "Tidak, Cancel!",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm){
		  if (isConfirm) {
		    ngProgress.start();
		    angular.element('#mydiv').show();
			dataFactory.httpRequest('saveStep5/'+incentiveparam,'PUT',{},'').then(function(data) {
		    	if (data.success == true) {
		    		angular.element('#mydiv').hide();
		    		SweetAlert.swal("Success!", "Posting Successful!", "success");
		    		window.location = site_url+"/#/postedinsentif";
		    		ngProgress.complete();
		    	}else{		    		
		    		angular.element('#mydiv').hide();
		    		SweetAlert.swal("Oops!", "Posting UnSuccessful!", "error");
		    		ngProgress.complete();
		    	};
		    });
		  } else {		  	
		    SweetAlert.swal("Cancelled", "Canceled !", "error");
		  }
		});
		
	}

	function previewInsentif(incentiveparam){
		dataFactory.httpRequest('previewInsentif/'+incentiveparam,'PUT',{},'').then(function(preview) {
        	$scope.bulan = preview.bulan;
        	$scope.tahun = preview.tahun;
        	$scope.previewInsentif = preview.loop;   
        	$scope.showPortionOfBlock = preview.showPortionOfBlock;  
        	$scope.showPortionOfSlice = preview.showPortionOfSlice;     
      	});
	}

	$scope.searchInsentif = function(incentiveparam, text){
		// console.log(text);
		dataFactory.httpRequest('previewInsentifsearch/'+incentiveparam+'&'+text,'PUT',{},'').then(function(preview) {
        	$scope.previewInsentif = preview.loop; 
        	$scope.showPortionOfBlock = preview.showPortionOfBlock;  
        	$scope.showPortionOfSlice = preview.showPortionOfSlice;          
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

 	function getQccGroupA(param){
 		dataFactory.httpRequest('getQccGroupA/'+param,'PUT',{},'').then(function(data) {
        	$scope.getQccGroupA = data;        
      	});
 	}

 	function getQccGroupB(param){
 		dataFactory.httpRequest('getQccGroupB/'+param,'PUT',{},'').then(function(data) {
        	$scope.getQccGroupB = data;        
      	});
 	}

 	function getQccGroupC(param){
 		dataFactory.httpRequest('getQccGroupC/'+param,'PUT',{},'').then(function(data) {
        	$scope.getQccGroupC = data;        
      	});
 	}

 	function getQccGroupD(param){
 		dataFactory.httpRequest('getQccGroupD/'+param,'PUT',{},'').then(function(data) {
        	$scope.getQccGroupD = data;        
      	});
 	}

 	function getRtgGroupA(param){
 		dataFactory.httpRequest('getRtgGroupA/'+param,'PUT',{},'').then(function(data) {
        	$scope.getRtgGroupA = data;        
      	});
 	}

 	function getRtgGroupB(param){
 		dataFactory.httpRequest('getRtgGroupB/'+param,'PUT',{},'').then(function(data) {
        	$scope.getRtgGroupB = data;        
      	});
 	}

 	function getRtgGroupC(param){
 		dataFactory.httpRequest('getRtgGroupC/'+param,'PUT',{},'').then(function(data) {
        	$scope.getRtgGroupC = data;        
      	});
 	}

 	function getRtgGroupD(param){
 		dataFactory.httpRequest('getRtgGroupD/'+param,'PUT',{},'').then(function(data) {
        	$scope.getRtgGroupD = data;        
      	});
 	}

 	function getPotonganKar(param){
 		dataFactory.httpRequest('getPotonganKar/'+param,'PUT',{},'').then(function(data) {
        	$scope.getPotonganKar = data;        
      	});
 	}

 	$scope.searchPotongan = function(incentiveparam, text){
		// console.log(text);
		dataFactory.httpRequest('potonganKarSearch/'+incentiveparam+'&'+text,'PUT',{},'').then(function(preview) {
        	$scope.getPotonganKar = preview;        
      	});
	}


 	// $scope.BoxPerGroupAKey = function(BoxPerGroupA,BoxPerOrangA){
 		
 	// 	var overbox = BoxPerGroupA - BoxPerOrangA;
 	// 	$scope.form.OverBoxA = overbox;
 	// }

 	$scope.boxOrngAkeyUp = function(incentiveparam, getQccGroupA, BoxPerGroupA, index, BOX_ADJUSMENT_GROUP_ID_QCCA, InsentifPerUnitA){ //entar tambahin incentiveparam di parameternya dan ngirim dari viewnya
 		var incentiveparam = incentiveparam;
 		var total=0;
 		var a = 0;
      	angular.forEach(getQccGroupA, function(uhuy){
      		if (a == index) {
      			dataFactory.httpRequest('saveBoxGroupA/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_QCCA+'&'+BoxPerGroupA+'&'+InsentifPerUnitA,'PUT',{},getQccGroupA[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
	        total+= parseInt(uhuy.EMPLOYEE_BOX_TEMP);
	        a++;
    	 });

	    $scope.form.BoxPerOrangA = total;
	    var overbox = BoxPerGroupA - total;
 		$scope.form.OverBoxA = overbox;

 	}

 	$scope.boxOrngBkeyUp = function(incentiveparam, getQccGroupB, BoxPerGroupB, index, BOX_ADJUSMENT_GROUP_ID_QCCB, InsentifPerUnitB){ //entar tambahin incentiveparam di parameternya dan ngirim dari viewnya
 		var incentiveparam = incentiveparam;
 		var total=0;
 		var a = 0;
      	angular.forEach(getQccGroupB, function(uhuy){
      		if (a == index) {
      			dataFactory.httpRequest('saveBoxGroupB/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_QCCB+'&'+BoxPerGroupB+'&'+InsentifPerUnitB,'PUT',{},getQccGroupB[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
	        total+= parseInt(uhuy.EMPLOYEE_BOX_TEMP);
	        a++;
    	 });

	    $scope.form.BoxPerOrangB = total;
	    var overbox = BoxPerGroupB - total;
 		$scope.form.OverBoxB = overbox;

 	}

 	$scope.boxOrngCkeyUp = function(incentiveparam, getQccGroupC, BoxPerGroupC, index, BOX_ADJUSMENT_GROUP_ID_QCCC, InsentifPerUnitC){ //entar tambahin incentiveparam di parameternya dan ngirim dari viewnya
 		var incentiveparam = incentiveparam;
 		var total=0;
 		var a = 0;
      	angular.forEach(getQccGroupC, function(uhuy){
      		if (a == index) {
      			dataFactory.httpRequest('saveBoxGroupC/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_QCCC+'&'+BoxPerGroupC+'&'+InsentifPerUnitC,'PUT',{},getQccGroupC[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
	        total+= parseInt(uhuy.EMPLOYEE_BOX_TEMP);
	        a++;
    	 });

	    $scope.form.BoxPerOrangC = total;
	    var overbox = BoxPerGroupC - total;
 		$scope.form.OverBoxC = overbox;

 	}

 	$scope.boxOrngDkeyUp = function(incentiveparam, getQccGroupD, BoxPerGroupD, index, BOX_ADJUSMENT_GROUP_ID_QCCD, InsentifPerUnitD){ //entar tambahin incentiveparam di parameternya dan ngirim dari viewnya
 		var incentiveparam = incentiveparam;
 		var total=0;
 		var a = 0;
      	angular.forEach(getQccGroupD, function(uhuy){
      		if (a == index) {
      			dataFactory.httpRequest('saveBoxGroupD/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_QCCD+'&'+BoxPerGroupD+'&'+InsentifPerUnitD,'PUT',{},getQccGroupD[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
	        total+= parseInt(uhuy.EMPLOYEE_BOX_TEMP);
	        a++;
    	 });

	    $scope.form.BoxPerOrangD = total;
	    var overbox = BoxPerGroupD - total;
 		$scope.form.OverBoxD = overbox;

 	}

 	$scope.RtgboxOrngAkeyUp = function(incentiveparam, getRtgGroupA, RtgBoxPerGroupA, index, BOX_ADJUSMENT_GROUP_ID_RTGA, RtgInsentifPerUnitA){ //entar tambahin incentiveparam di parameternya dan ngirim dari viewnya
 		var incentiveparam = incentiveparam;
 		var total=0;
 		var a = 0;
      	angular.forEach(getRtgGroupA, function(uhuy){
      		if (a == index) {
      			dataFactory.httpRequest('saveBoxGroupRtgA/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_RTGA+'&'+RtgBoxPerGroupA+'&'+RtgInsentifPerUnitA,'PUT',{},getRtgGroupA[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
	        total+= parseInt(uhuy.EMPLOYEE_BOX_TEMP);
	        a++;
    	 });

	    $scope.form.RtgBoxPerOrangA = total;
	    var overbox = RtgBoxPerGroupA - total;
 		$scope.form.RtgOverBoxA = overbox;

 	}

 	$scope.RtgboxOrngBkeyUp = function(incentiveparam, getRtgGroupB, RtgBoxPerGroupB, index, BOX_ADJUSMENT_GROUP_ID_RTGB, RtgInsentifPerUnitB){ //entar tambahin incentiveparam di parameternya dan ngirim dari viewnya
 		var incentiveparam = incentiveparam;
 		var total=0;
 		var a = 0;
      	angular.forEach(getRtgGroupB, function(uhuy){
      		if (a == index) {
      			dataFactory.httpRequest('saveBoxGroupRtgB/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_RTGB+'&'+RtgBoxPerGroupB+'&'+RtgInsentifPerUnitB,'PUT',{},getRtgGroupB[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
	        total+= parseInt(uhuy.EMPLOYEE_BOX_TEMP);
	        a++;
    	 });

	    $scope.form.RtgBoxPerOrangB = total;
	    var overbox = RtgBoxPerGroupB - total;
 		$scope.form.RtgOverBoxB = overbox;

 	}

 	$scope.RtgboxOrngCkeyUp = function(incentiveparam, getRtgGroupC, RtgBoxPerGroupC, index, BOX_ADJUSMENT_GROUP_ID_RTGC, RtgInsentifPerUnitC){ //entar tambahin incentiveparam di parameternya dan ngirim dari viewnya
 		var incentiveparam = incentiveparam;
 		var total=0;
 		var a = 0;
      	angular.forEach(getRtgGroupC, function(uhuy){
      		if (a == index) {
      			dataFactory.httpRequest('saveBoxGroupRtgC/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_RTGC+'&'+RtgBoxPerGroupC+'&'+RtgInsentifPerUnitC,'PUT',{},getRtgGroupC[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
	        total+= parseInt(uhuy.EMPLOYEE_BOX_TEMP);
	        a++;
    	 });

	    $scope.form.RtgBoxPerOrangC = total;
	    var overbox = RtgBoxPerGroupC - total;
 		$scope.form.RtgOverBoxC = overbox;

 	}

 	$scope.RtgboxOrngDkeyUp = function(incentiveparam, getRtgGroupD, RtgBoxPerGroupD, index, BOX_ADJUSMENT_GROUP_ID_RTGD, RtgInsentifPerUnitD){ //entar tambahin incentiveparam di parameternya dan ngirim dari viewnya
 		var incentiveparam = incentiveparam;
 		var total=0;
 		var a = 0;
      	angular.forEach(getRtgGroupD, function(uhuy){
      		if (a == index) {
      			dataFactory.httpRequest('saveBoxGroupRtgD/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_RTGD+'&'+RtgBoxPerGroupD+'&'+RtgInsentifPerUnitD,'PUT',{},getRtgGroupD[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
	        total+= parseInt(uhuy.EMPLOYEE_BOX_TEMP);
	        a++;
    	 });

	    $scope.form.RtgBoxPerOrangD = total;
	    var overbox = RtgBoxPerGroupD - total;
 		$scope.form.RtgOverBoxD = overbox;

 	}

 	$scope.PotonganKeyUp = function(incentiveparam, getPotonganKar, index){ //entar tambahin incentiveparam di parameternya dan ngirim dari viewnya

 		var incentiveparam = incentiveparam;
 		var total=0;
 		var a = 0; 		
 		var persen_potong = getPotonganKar[index]['TEMP_INCENTIVE_PERCENT_CUT'];
 		var insentif_bersih = getPotonganKar[index]['TEMP_INCENTIVE_TOTAL_BERSIH'];

 		var hasil_potong = persen_potong * insentif_bersih / 100;		 		
      	angular.forEach(getPotonganKar, function(uhuy){
      		if (a == index) {
      			dataFactory.httpRequest('savePotonganKar/'+incentiveparam,'PUT',{},getPotonganKar[index]).then(function(data){
      				if (data.success == true) {
      					
      				};
      			});

      		};
	        a++;
    	 });      	

      	$scope.getPotonganKar[index].TEMP_INCENTIVE_COST_CUT_ROUND = Math.round(hasil_potong);
 	}

 	$scope.boxOjekQccA = function(incentiveparam, BOX_ADJUSMENT_GROUP_ID_QCCA, getQccGroupA, RtgInsentifPerboxA, index){
 		var incentiveparam = incentiveparam;
 		var a = 0;
 		angular.forEach(getQccGroupA, function(uhuy){
 			if (a == index) {
      			dataFactory.httpRequest('saveBoxOjekQccA/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_QCCA+'&'+RtgInsentifPerboxA,'PUT',{},getQccGroupA[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
 			a++;
 		})

 	}


 	$scope.boxOjekQccB = function(incentiveparam, BOX_ADJUSMENT_GROUP_ID_QCCB, getQccGroupB, RtgInsentifPerboxB, index){
 		var incentiveparam = incentiveparam;
 		var a = 0;
 		angular.forEach(getQccGroupB, function(uhuy){
 			if (a == index) {
      			dataFactory.httpRequest('saveBoxOjekQccB/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_QCCB+'&'+RtgInsentifPerboxB,'PUT',{},getQccGroupB[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
 			a++;
 		})

 	}

 	$scope.boxOjekQccC = function(incentiveparam, BOX_ADJUSMENT_GROUP_ID_QCCC, getQccGroupC, RtgInsentifPerboxC, index){
 		var incentiveparam = incentiveparam;
 		var a = 0;
 		angular.forEach(getQccGroupC, function(uhuy){
 			if (a == index) {
      			dataFactory.httpRequest('saveBoxOjekQccC/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_QCCC+'&'+RtgInsentifPerboxC,'PUT',{},getQccGroupC[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
 			a++;
 		})

 	}

 	$scope.boxOjekQccD = function(incentiveparam, BOX_ADJUSMENT_GROUP_ID_QCCD, getQccGroupD, RtgInsentifPerboxD, index){
 		var incentiveparam = incentiveparam;
 		var a = 0;
 		angular.forEach(getQccGroupD, function(uhuy){
 			if (a == index) {
      			dataFactory.httpRequest('saveBoxOjekQccD/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_QCCD+'&'+RtgInsentifPerboxD,'PUT',{},getQccGroupD[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
 			a++;
 		})

 	}

 	$scope.boxOjekRtgA = function(incentiveparam, BOX_ADJUSMENT_GROUP_ID_RTGA, getRtgGroupA, InsentifPerBoxA, index){
 		var incentiveparam = incentiveparam;
 		var a = 0;
 		angular.forEach(getRtgGroupA, function(uhuy){
 			if (a == index) {
      			dataFactory.httpRequest('saveBoxOjekRtgA/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_RTGA+'&'+InsentifPerBoxA,'PUT',{},getRtgGroupA[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
 			a++;
 		})

 	}

 	$scope.boxOjekRtgB = function(incentiveparam, BOX_ADJUSMENT_GROUP_ID_RTGB, getRtgGroupB, InsentifPerBoxB, index){
 		var incentiveparam = incentiveparam;
 		var a = 0;
 		angular.forEach(getRtgGroupB, function(uhuy){
 			if (a == index) {
      			dataFactory.httpRequest('saveBoxOjekRtgB/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_RTGB+'&'+InsentifPerBoxB,'PUT',{},getRtgGroupB[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
 			a++;
 		})

 	}

 	$scope.boxOjekRtgC = function(incentiveparam, BOX_ADJUSMENT_GROUP_ID_RTGC, getRtgGroupC, InsentifPerBoxC, index){
 		var incentiveparam = incentiveparam;
 		var a = 0;
 		angular.forEach(getRtgGroupC, function(uhuy){
 			if (a == index) {
      			dataFactory.httpRequest('saveBoxOjekRtgC/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_RTGC+'&'+InsentifPerBoxC,'PUT',{},getRtgGroupC[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
 			a++;
 		})

 	}

 	$scope.boxOjekRtgD = function(incentiveparam, BOX_ADJUSMENT_GROUP_ID_RTGD, getRtgGroupD, InsentifPerBoxD, index){
 		var incentiveparam = incentiveparam;
 		var a = 0;
 		angular.forEach(getRtgGroupD, function(uhuy){
 			if (a == index) {
      			dataFactory.httpRequest('saveBoxOjekRtgD/'+incentiveparam+'&'+BOX_ADJUSMENT_GROUP_ID_RTGD+'&'+InsentifPerBoxD,'PUT',{},getRtgGroupD[index]).then(function(data){
      				if (data.success == true) {
      					// console.log('')
      				};
      			});

      		};
 			a++;
 		})

 	}

 	$scope.upload_potongan = function(incentiveparam, file)
 	{ 		 		
 		if (file == undefined) {
 			SweetAlert.swal("Oops!", "Masukkan File Terlebih Dahulu!", "error");
 			return false;
 		}
 		ngProgress.start();
 		angular.element('#mydiv').show();
 		file.upload = Upload.upload({
            url: '/upload_potongan',
            data: {file: file, incentiveparam:incentiveparam},
        });
        file.upload.then(function (response,status) {
            $timeout(function () {
                file.result = response.data;
                SweetAlert.swal("Success!", "Upload Potongan Successful!", "success");
                getPotonganKar(incentiveparam);
                $scope.potonganFile = null;
                var fileElement = angular.element('#file_upload');
				angular.element(fileElement).val(null);
				angular.element('#mydiv').hide();
                ngProgress.complete();
            });
        }, function (response) {               	
           	SweetAlert.swal("Success!", "Upload Potongan Successful!", "success"); 
           	getPotonganKar(incentiveparam);
           	$scope.potonganFile = null;
           	var fileElement = angular.element('#file_upload');
			angular.element(fileElement).val(null);
			angular.element('#mydiv').hide();
           	ngProgress.complete();
        }, function (evt) {
            
        });  
 	} 	

});