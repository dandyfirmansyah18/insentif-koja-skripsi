// arsipti.controller('AdminController', function($scope,$http){
//   $scope.pools = [];
// });

arsipti.controller('UserController', function(dataFactory,$scope,$http, SweetAlert, ngProgress){
  ngProgress.color('#169f85');
  $scope.data = [];
  $scope.libraryTemp = {};
  $scope.totalItemsTemp = {};
  $scope.totalItems = 0;

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };
  getResultsPage(1);
  getPrivileges();
  getUserStatus();

  function getPrivileges(){
      dataFactory.httpRequest('/getPrivileges').then(function(data) {
        $scope.getPrivileges = data;
      });
  }

  function getUserStatus(){
      dataFactory.httpRequest('/getUserStatus').then(function(data) {
        $scope.getUserStatus = data;
      });
  }

  function getResultsPage(pageNumber) {
      if(! $.isEmptyObject($scope.libraryTemp)){
          dataFactory.httpRequest('/user?cari='+$scope.searchText+'&page='+pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.current_page = (data.current_page - 1) * 10;
            $scope.totalItems = data.total;
          });
      }else{
        angular.element('#mydiv').show();
        ngProgress.start();
        dataFactory.httpRequest('/user?page='+pageNumber).then(function(data) {
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
    dataFactory.httpRequest('user','POST',{},$scope.form).then(function(data) {
      if (data.success == true) {
        $scope.data.push(data.data);
        $(".modal").modal("hide");
        SweetAlert.swal("Success!", "Data User Saved!", "success");
      }else{
        SweetAlert.swal("Oopss!", "Data User Not Saved!", "error");
      };
    });
  }

  $scope.edit = function(id){
    dataFactory.httpRequest('user/'+id+'/edit').then(function(data) {    	
      	$scope.form = data;
    });
  }

  $scope.saveEdit = function(){
    dataFactory.httpRequest('user/'+$scope.form.id,'PUT',{},$scope.form).then(function(data) {
      if (data.success == true) {
        $(".modal").modal("hide");
        $scope.data = apiModifyTableUser($scope.data,data.data.id,data.data);
        SweetAlert.swal("Success!", "Data User Updated!", "success");
      }else{
        SweetAlert.swal("Oopss!", "Data User Not Updated!", "error");
      };
    });
  }

  $scope.resetpassword = function(id){
    $scope.form = {id_user:id};
  }

  $scope.saveResetPassword = function(){
    if ($scope.form.password_user != $scope.form.confirm_password_user) {
      SweetAlert.swal("Oopss!", "Password and Confirm Password must be same.", "error");
      return false;
    }
    
    dataFactory.httpRequest('resetPassword','POST',{},$scope.form).then(function(data) {
      if (data.success == true) {
        $(".modal").modal("hide");
        SweetAlert.swal("Success!", data.message, "success");
      }else{
        SweetAlert.swal("Oopss!", data.message, "error");
      };
    });
  }

  $scope.remove = function(item,index){
    SweetAlert.swal({
       title: "Are you sure?",
       text: "Your will not be able to recover this imaginary User data!",
       type: "warning",
       showCancelButton: true,
       confirmButtonColor: "#DD6B55",confirmButtonText: "Yes, delete it!",
       cancelButtonText: "No, cancel plx!",
       closeOnConfirm: false,
       closeOnCancel: false }, 
    function(isConfirm){ 
       if (isConfirm) {
          dataFactory.httpRequest('user/'+item.id,'DELETE').then(function(data) {
              $scope.data.splice(index,1);
          });
          SweetAlert.swal("Deleted!", "Your User Data has been deleted.", "success");
       } else {
          SweetAlert.swal("Cancelled", "Your User Data file is safe :)", "error");
       }
    });
  }

  $scope.tambah_user_baru = function()
  {
    // alert('aa')
    $scope.form = '';
  }
});