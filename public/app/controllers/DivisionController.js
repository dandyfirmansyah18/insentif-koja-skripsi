arsipti.controller('DivisionController', function(dataFactory,$scope,$http, SweetAlert, ngProgress){
  ngProgress.color('#169f85');
  $scope.data = [];
  $scope.libraryTemp = {};
  $scope.totalItemsTemp = {};
  $scope.totalItems = 0;

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };
  getResultsPage(1);

  $scope.tambah_divisi = function()
  {
    $scope.form = '';
  }

  function getResultsPage(pageNumber) {
      if(! $.isEmptyObject($scope.libraryTemp)){
          dataFactory.httpRequest('/division?cari='+$scope.searchText+'&page='+pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.current_page = (data.current_page - 1) * 10;
            $scope.totalItems = data.total;
          });
      }else{
        angular.element('#mydiv').show();
        dataFactory.httpRequest('/division?page='+pageNumber).then(function(data) {
        ngProgress.start();
          $scope.data = data.data;
          $scope.current_page = (data.current_page - 1) * 10;
          $scope.totalItems = data.total;
        ngProgress.complete();
        angular.element('#mydiv').hide();
        });
      }
  }

  $scope.searchDB = function(){
      if($scope.searchText.length >= 1){
          if($.isEmptyObject($scope.libraryTemp)){
              $scope.libraryTemp = $scope.data;
              $scope.totalItemsTemp = $scope.totalItems;
              $scope.data = {};
          }
          getResultsPage(1);
      }else{
          if(! $.isEmptyObject($scope.libraryTemp)){
              $scope.data = $scope.libraryTemp ;
              $scope.totalItems = $scope.totalItemsTemp;
              $scope.libraryTemp = {};
          }
      }
  }

  $scope.saveAdd = function(){
    dataFactory.httpRequest('division','POST',{},$scope.form).then(function(data) {
      if (data.success == true) {
        $scope.data.push(data.data);
        $(".modal").modal("hide");
        SweetAlert.swal("Success!", "Data Divison Saved!", "success");
      }else{
        SweetAlert.swal("Oopss!", "Data Divison Not Saved!", "error");
      };
    });
  }

  $scope.edit = function(id){
    dataFactory.httpRequest('division/'+id+'/edit').then(function(data) {
    	console.log(data);
      	$scope.form = data;
    });
  }

  $scope.saveEdit = function(){
    dataFactory.httpRequest('division/'+$scope.form.DIVISION_ID,'PUT',{},$scope.form).then(function(data) {
      if (data.success == true) {
        $(".modal").modal("hide");
        $scope.data = apiModifyTableDivision($scope.data,data.data.DIVISION_ID,data.data);
        SweetAlert.swal("Success!", "Data division Updated!", "success");
      }else{
        SweetAlert.swal("Oopss!", "Data division Not Updated!", "error");
      };
    });
  }

  $scope.remove = function(item,index){
    SweetAlert.swal({
       title: "Are you sure?",
       text: "Your will not be able to recover this imaginary Division data!",
       type: "warning",
       showCancelButton: true,
       confirmButtonColor: "#DD6B55",confirmButtonText: "Yes, delete it!",
       cancelButtonText: "No, cancel plx!",
       closeOnConfirm: false,
       closeOnCancel: false }, 
    function(isConfirm){ 
       if (isConfirm) {
          dataFactory.httpRequest('division/'+item.DIVISION_ID,'DELETE').then(function(data) {
              $scope.data.splice(index,1);
          });
          SweetAlert.swal("Deleted!", "Your Division Data has been deleted.", "success");
       } else {
          SweetAlert.swal("Cancelled", "Your Division Data file is safe :)", "error");
       }
    });
  }
});