arsipti.controller('EmployeeController', function(dataFactory,$scope,$http, SweetAlert, ngProgress){
  ngProgress.color('#169f85');
  $scope.data = [];
  $scope.libraryTemp = {};
  $scope.totalItemsTemp = {};
  $scope.totalItems = 0;

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };
  
  getResultsPage(1);
  getPosisi();
  getGroup();
  getShift();
  getBlock();
  getSlice();
  getDivisi();

  function getResultsPage(pageNumber) {
      if(! $.isEmptyObject($scope.libraryTemp)){
          dataFactory.httpRequest('/employee?cari='+$scope.searchText+'&page='+pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.current_page = (data.current_page - 1) * 10;
            $scope.totalItems = data.total;
          });
      }else{
        angular.element('#mydiv').show();
        dataFactory.httpRequest('/employee?page='+pageNumber).then(function(data) {
        ngProgress.start();
          $scope.data = data.data;
          $scope.current_page = (data.current_page - 1) * 10;
          $scope.totalItems = data.total;
        ngProgress.complete();
        angular.element('#mydiv').hide();
        });
      }
  }

  function getPosisi(){
      dataFactory.httpRequest('/getPosisi').then(function(data) {
        $scope.getPosisi = data;
      });
  }

  function getGroup(){
      dataFactory.httpRequest('/getGroup').then(function(data) {
        $scope.getGroup = data;
      });
  }

  function getShift(){
      dataFactory.httpRequest('/getShift').then(function(data) {
        $scope.getShift = data;
      });
  }

  function getBlock(){
      dataFactory.httpRequest('/getBlock').then(function(data) {
        $scope.getBlock = data;
      });
  }

  function getSlice(){
      dataFactory.httpRequest('/getSlice').then(function(data) {
        $scope.getSlice = data;
      });
  }

  function getDivisi(){
      dataFactory.httpRequest('/getDivisi').then(function(data) {
        $scope.getDivisi = data;
      });
  }

  function getGrade(block,shift,grade='')
  {
    dataFactory.httpRequest('/getGrade?value='+block+'-'+shift).then(function(data) {
      $scope.getGrade = data;
      if (grade!='') {          
        $scope.form.EMPLOYEE_GRADE = grade;
      }
    });
  }

  $scope.tambah_employee = function()
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
    dataFactory.httpRequest('employee','POST',{},$scope.form).then(function(data) {
      if (data.success == true) {
        $scope.data.push(data.data);
        $(".modal").modal("hide");
        SweetAlert.swal("Success!", "Data Employees Saved!", "success");
      }else{
        SweetAlert.swal("Oopss!", "Data Employees Not Saved!", "error");
      };
    });
  }

  $scope.edit = function(id){
    dataFactory.httpRequest('employee/'+id+'/edit').then(function(data) {    	
        // getGrade(data['EMPLOYEE_BLOCK'],data['EMPLOYEE_SHIFT'],data['EMPLOYEE_GRADE']);
      	$scope.form = data;
        // $scope.checkSlice(data['EMPLOYEE_GRADE'],data['EMPLOYEE_BLOCK'],data['EMPLOYEE_SHIFT']);


    });
  }

  $scope.saveEdit = function(){    
    dataFactory.httpRequest('employee/'+$scope.form.EMPLOYEE_NIK,'PUT',{},$scope.form).then(function(data) {
      if (data.success == true) {
        $(".modal").modal("hide");
        $scope.data = apiModifyTableEmployee($scope.data,data.data.EMPLOYEE_NIK,data.data);
        SweetAlert.swal("Success!", "Data Employees Updated!", "success");
      }else{
        SweetAlert.swal("Oopss!", "Data Employees Not Updated!", "error");
      };
    });
  }

  $scope.remove = function(item,index){
    SweetAlert.swal({
       title: "Are you sure?",
       text: "Your will not be able to recover this imaginary Employees data!",
       type: "warning",
       showCancelButton: true,
       confirmButtonColor: "#DD6B55",confirmButtonText: "Yes, delete it!",
       cancelButtonText: "No, cancel plx!",
       closeOnConfirm: false,
       closeOnCancel: false }, 
    function(isConfirm){ 
       if (isConfirm) {
          dataFactory.httpRequest('employee/'+item.EMPLOYEE_NIK,'DELETE').then(function(data) {
              $scope.data.splice(index,1);
          });
          SweetAlert.swal("Deleted!", "Your Employees Data has been deleted.", "success");
       } else {
          SweetAlert.swal("Cancelled", "Your Employees Data file is safe :)", "error");
       }
    });
  }

  $scope.showGrade = function(block,shift){
    getGrade(block,shift);
  }

  $scope.checkSlice = function(grade,block,shift)
  {
    dataFactory.httpRequest('/checkSlice?value='+grade+'-'+block+'-'+shift).then(function(data) {      
      $scope.form.EMPLOYEE_SLICE = data['SLICE_ID'];
      $scope.form.EMPLOYEE_SLICE_KETERANGAN = data['SLICE_ID']+' - '+data['SLICE_NAME'];
    });
  }
});