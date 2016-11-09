
//angular.module( "voteApp", ['ngAnimate', 'ngSanitize', 'mgcrea.ngStrap'])
angular.module( "voteApp",[])
/*
.config(function($modalProvider) {
  angular.extend($modalProvider.defaults, {
    html: true
  });
})*/
.controller('voteController', ['$scope', '$http', function($scope, $http) {

  $scope.authFail = 0;
  $scope.authSucc = 0;
  $scope.showGraduateList = false;
  
  $http.get('panel/controller.php?p=panel').success(function(data) {
	  if (data['code'] == 'fail'){
		$scope.authFail = 1;
		$scope.authSucc = 0;
		$scope.authMsg = data['msg'];
		window.location.replace('/rc/panel/login.html');
		
	  }else{
		$scope.showGraduateList = true;
	  }
	});
}]);
