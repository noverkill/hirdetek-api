
hirdetekApp.controller('PasswordController', function($rootScope, $scope, $state, $stateParams, $anchorScroll, UserService) {

  $anchorScroll();

  $scope.remind = function(email, formValid) {

    $anchorScroll();

    $scope.success = 0;

    if(! formValid) return;

    $scope.userBusy = UserService.get({'email': email, 'remind': "1"},
      function (response) {
        $scope.success = 1;
    }).$promise
  }

});
