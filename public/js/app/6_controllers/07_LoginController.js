
hirdetekApp.controller('LoginController', function ($scope, $rootScope, $state, $http) {

  if($rootScope.user.isLogged()) {
    $state.go('hirdetesek');
  }

  if($rootScope.user.hasCredentials()) {
    $scope.credentials = $rootScope.user.getCredentials();
  }

});
