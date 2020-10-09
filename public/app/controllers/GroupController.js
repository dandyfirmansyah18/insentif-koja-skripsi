arsipti.controller('GroupController', function(dataFactory,$scope,$http, SweetAlert, ngProgress){
  ngProgress.color('#169f85');
  $scope.data = [];
  $scope.libraryTemp = {};
  $scope.totalItemsTemp = {};
  $scope.totalItems = 0;

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };
  getResultsPage(1);
  function getResultsPage(pageNumber) {
      if(! $.isEmptyObject($scope.libraryTemp)){
          dataFactory.httpRequest('/group?cari='+$scope.searchText+'&page='+pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.current_page = (data.current_page - 1) * 10;
            $scope.totalItems = data.total;
          });
      }else{
        angular.element('#mydiv').show();
        dataFactory.httpRequest('/group?page='+pageNumber).then(function(data) {
        ngProgress.start();
          $scope.data = data.data;
          $scope.current_page = (data.current_page - 1) * 10;
          $scope.totalItems = data.total;
        ngProgress.complete();
        angular.element('#mydiv').hide();
        });
      }
  }

  $scope.tambah_group = function()
  {
    $scope.form = '';
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
    dataFactory.httpRequest('group','POST',{},$scope.form).then(function(data) {
      if (data.success == true) {
        $scope.data.push(data.data);
        $(".modal").modal("hide");
        SweetAlert.swal("Success!", "Data Group Saved!", "success");
      }else{
        SweetAlert.swal("Oopss!", "Data Group Not Saved!", "error");
      };
    });
  }

  $scope.edit = function(id){
    dataFactory.httpRequest('group/'+id+'/edit').then(function(data) {
    	console.log(data);
      	$scope.form = data;
    });
  }

  $scope.saveEdit = function(){
    dataFactory.httpRequest('group/'+$scope.form.GROUP_ID,'PUT',{},$scope.form).then(function(data) {
      if (data.success == true) {
      	$(".modal").modal("hide");
        $scope.data = apiModifyTableGroup($scope.data,data.data.GROUP_ID,data.data);
        SweetAlert.swal("Success!", "Data Group Updated!", "success");
      }else{
        SweetAlert.swal("Oopss!", "Data Group Not Updated!", "error");
      };
    });
  }

  $scope.remove = function(item,index){
    SweetAlert.swal({
       title: "Are you sure?",
       text: "Your will not be able to recover this imaginary Group data!",
       type: "warning",
       showCancelButton: true,
       confirmButtonColor: "#DD6B55",confirmButtonText: "Yes, delete it!",
       cancelButtonText: "No, cancel plx!",
       closeOnConfirm: false,
       closeOnCancel: false }, 
    function(isConfirm){ 
       if (isConfirm) {
          dataFactory.httpRequest('group/'+item.GROUP_ID,'DELETE').then(function(data) {
              $scope.data.splice(index,1);
          });
          SweetAlert.swal("Deleted!", "Your Group Data has been deleted.", "success");
       } else {
          SweetAlert.swal("Cancelled", "Your Group Data file is safe :)", "error");
       }
    });
  }
});