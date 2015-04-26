
hirdetekApp.controller('UserCreateController', function($rootScope, $scope, $state, $stateParams, $anchorScroll, UserService) {

  if($rootScope.user.isLogged()) {
    $state.go('hirdetesek');
  }

  $scope.registered = 0;

  $scope.user = new UserService();

  $scope.error = 0;

  $scope.addUser = function(formValid) {

    if(! formValid) return;

    $scope.userBusy = $scope.user.$save(function(response) {
      $scope.response = response;
      if(response.success) {
        $scope.error = 0;
        $scope.user = {};
        $scope.registered = 1;
        $anchorScroll();
      } else {
        //console.log("not response.success");
        $scope.error = 1;
        //$scope.user = response.data;
        $anchorScroll();
      }
      //$state.go('users');
    });
  };
});