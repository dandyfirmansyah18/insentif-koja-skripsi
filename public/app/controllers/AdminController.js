arsipti.controller('AdminController', function(dataFactory,$scope,$http){
// getCountEmployee();
  	angular.element('#mydiv').show()
	function getCountEmployee(){
	  dataFactory.httpRequest('/getCountEmployee').then(function(data) {
	    $scope.aaa = data;
	  });
	}

	angular.element('#mydiv').hide()

});