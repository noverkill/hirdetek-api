
hirdetekApp.controller('LogoutController', function ($scope, $rootScope, $state) {
  //console.log('logoutController');
  $rootScope.user.logout();
  $state.go('login');
});
