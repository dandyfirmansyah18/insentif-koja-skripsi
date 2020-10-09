var arsipti =  angular.module('insentif_koja',['ngRoute','angularUtils.directives.dirPagination','oitozero.ngSweetAlert','ngProgress','ngFileUpload']);
arsipti.run(function(){ });
arsipti.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
            when('/', {
                templateUrl: 'templates/home.html',
                controller: 'AdminController'
            }).
            when('/grouplist', {
                templateUrl: 'templates/groups.html',
                controller: 'GroupController'
            }).
            when('/employeelist', {
                templateUrl: 'templates/employees.html',
                controller: 'EmployeeController'
            }).
            when('/userlist', {
                templateUrl: 'templates/users.html',
                controller: 'UserController'
            }).
            when('/divisionlist', {
                templateUrl: 'templates/divisions.html',
                controller: 'DivisionController'
            }).
            when('/positionlist', {
                templateUrl: 'templates/positions.html',
                controller: 'PositionController'
            }).
            when('/inputinsentif', {
                templateUrl: 'templates/inputinsentif.html',
                controller: 'InputInsentifController'
            }).
            when('/adjustparam', {
                templateUrl: 'templates/adjustparam.html',
                controller: 'AdjustParamController'
            }).
            when('/postedinsentif', {
                templateUrl: 'templates/postedinsentif.html',
                controller: 'PostedInsentifController'
            })
            .otherwise({ redirectTo: '/' });;
}]);

arsipti.directive('format', ['$filter', function ($filter) {
    return {
        require: '?ngModel',
        link: function (scope, elem, attrs, ctrl) {
            if (!ctrl) return;


            ctrl.$formatters.unshift(function (a) {
                return $filter(attrs.format)(ctrl.$modelValue)
            });


            ctrl.$parsers.unshift(function (viewValue) {
                var plainNumber = viewValue.replace(/[^\d|\-+|\.+]/g, '');
                elem.val($filter(attrs.format)(plainNumber));
                return plainNumber;
            });
        }
    };
}]);

arsipti.filter('range', function() {
  return function(input, total) {
    total = parseInt(total);

    for (var i=1970; i<total; i++) {
      input.push(i);
    }

    return input;
  };
});