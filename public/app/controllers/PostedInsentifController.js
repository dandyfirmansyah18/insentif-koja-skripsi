arsipti.controller('PostedInsentifController', function(dataFactory,$scope,$http,SweetAlert, ngProgress){
  ngProgress.color('#169f85');
  var search = '';

  $scope.form = {bulan:"", tahun:"", incentiveparam:""}

  daftarKaryawan();

  function daftarKaryawan(){
    angular.element('#mydiv').show();
    ngProgress.start();
    dataFactory.httpRequest('daftarKaryawan').then(function(data){   
      // console.log(data); 
      $scope.showPosting = data.showPosting;
      $scope.showPortionOfSlice = data.showPortionOfSlice;
      $scope.showPortionOfBlock = data.showPortionOfBlock;
      $scope.form.bulan = data.month;
      $scope.form.tahun = data.tahun;
      $scope.form.incentiveparam = data.param;
      $scope.bulan = data.bulan;
      $scope.tahun = data.tahun;

      ngProgress.complete();
      angular.element('#mydiv').hide();
    });
  }

  $scope.searchInsentif = function(search,bulan,tahun){
    if (search == '' || search == null) {
      search = 'uhuy';
    };
    dataFactory.httpRequest('searchNameInsentif/'+search+'&'+bulan+'&'+tahun,'PUT',{},'').then(function(view) {
      $scope.showPosting = view.showPosting;  
      $scope.showPortionOfSlice = view.showPortionOfSlice;  
      $scope.showPortionOfBlock = view.showPortionOfBlock;  
      $scope.bulan = view.bulan;  
      $scope.tahun = view.tahun;        
    });
  }

  $scope.SearchBulanTahun = function(bulan, tahun){
    angular.element('#mydiv').show();
    ngProgress.start();
    dataFactory.httpRequest('SearchBulanTahun/'+bulan+'&'+tahun,'PUT',{},'').then(function(data){   
      if (data.success == true) {
        $scope.showPosting = data.showPosting;
        $scope.showPortionOfSlice = data.showPortionOfSlice;
        $scope.showPortionOfBlock = data.showPortionOfBlock;
        $scope.form.bulan = data.month;
        $scope.form.tahun = data.tahun;
        $scope.form.incentiveparam = data.param;
        $scope.bulan = data.bulan;
        $scope.tahun = data.tahun;
        angular.element('#mydiv').hide();
        ngProgress.complete();
      }else{
        angular.element('#mydiv').hide();
        SweetAlert.swal("Oops!", "Data Tidak Ada!", "error");
        ngProgress.complete();
      };    
    });
  }

  $scope.downloadSlip = function(value){
    var url = site_url+'/exportSlip/'+value.POSTING_INCENTIVE_ID;
    // window.location.href = url;
    window.open(
        url,
        '_blank' // <- This is what makes it open in a new window.
      );
  }
});