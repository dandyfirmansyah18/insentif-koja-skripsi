arsipti.controller('AdjustParamController', function(dataFactory,$scope,$http, SweetAlert, ngProgress){
  ngProgress.color('#169f85');
  show();

  $scope.form = {
          AP_PERCENTASE_INCENTIVE:"",AP_POB_DIFF_BLOCK_I:"",AP_POB_DIFF_BLOCK_II:"",AP_POB_DIFF_BLOCK_III:"",AP_POB_CUV_BLOCK_I:"",AP_POS_DIST_FLAT_BLOCK_I:"",AP_POS_DIFF_SLICE_BLOCK_I:"",
          AP_POS_DIFF_SLICE_BLOCK_IIA:"",AP_POS_DIFF_SLICE_BLOCK_IIB:"",AP_POS_DIFF_SLICE_BLOCK_III:"",
          AP_POS_CUV_BLOCK_I:"",AP_POS_CUV_BLOCK_II:"",AP_POS_CUV_BLOCK_III:"",AP_PENALTY:"",AP_BA_MIN_BOX_QCC:"",AP_BA_MIN_BOX_RTG:"",AP_COST_BOX_QCC:"",AP_COST_BOX_RTG:""
          };

  function show(){  
    ngProgress.start();
    angular.element('#mydiv').show();
    dataFactory.httpRequest('getAdjustParam').then(function(data) {
        $scope.form.AP_PERCENTASE_INCENTIVE = data.AP_PERCENTASE_INCENTIVE;
        $scope.form.AP_POB_DIFF_BLOCK_I = data.AP_POB_DIFF_BLOCK_I;
        $scope.form.AP_POB_DIFF_BLOCK_II = data.AP_POB_DIFF_BLOCK_II;
        $scope.form.AP_POB_DIFF_BLOCK_III = data.AP_POB_DIFF_BLOCK_III;
        $scope.form.AP_POB_CUV_BLOCK_I = data.AP_POB_CUV_BLOCK_I;
        $scope.form.AP_POS_DIST_FLAT_BLOCK_I = data.AP_POS_DIST_FLAT_BLOCK_I;
        $scope.form.AP_POS_DIFF_SLICE_BLOCK_I = data.AP_POS_DIFF_SLICE_BLOCK_I;
        $scope.form.AP_POS_DIFF_SLICE_BLOCK_IIA = data.AP_POS_DIFF_SLICE_BLOCK_IIA;
        $scope.form.AP_POS_DIFF_SLICE_BLOCK_IIB = data.AP_POS_DIFF_SLICE_BLOCK_IIB;
        $scope.form.AP_POS_DIFF_SLICE_BLOCK_III = data.AP_POS_DIFF_SLICE_BLOCK_III;
        $scope.form.AP_POS_CUV_BLOCK_I = data.AP_POS_CUV_BLOCK_I;
        $scope.form.AP_POS_CUV_BLOCK_II = data.AP_POS_CUV_BLOCK_II;
        $scope.form.AP_POS_CUV_BLOCK_III = data.AP_POS_CUV_BLOCK_III;
        $scope.form.AP_PENALTY = data.AP_PENALTY;
        $scope.form.AP_BA_MIN_BOX_QCC = data.AP_BA_MIN_BOX_QCC;
        $scope.form.AP_BA_MIN_BOX_RTG = data.AP_BA_MIN_BOX_RTG;
        $scope.form.AP_COST_BOX_QCC = data.AP_COST_BOX_QCC;
        $scope.form.AP_COST_BOX_RTG = data.AP_COST_BOX_RTG;
      ngProgress.complete();
      angular.element('#mydiv').hide();
    });
  }

  $scope.saveEdit = function(form){    
      var id = 1;
      dataFactory.httpRequest('saveEdit/'+id,'PUT',{},form).then(function(data) {
        if (data.success == true) {
          SweetAlert.swal("Success!", "Setting Saved!", "success");
          show();
        }else{
          SweetAlert.swal("Oops!", "Setting Not Saved!", "error");
        };
      });     

  }

  $scope.changePenalty = function(penalty){
    if (penalty == 1) {
      SweetAlert.swal('Penalty 1 : Hanya Mendapat INCENTIVE BOX');
    }else{
      SweetAlert.swal('Penalty 2 : Hanya Mendapat INCENTIVE FLAT');
    };
  }

});