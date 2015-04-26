
hirdetekApp.controller('UserEditController', function($rootScope, $scope, $state, $stateParams, $anchorScroll, UserService, popupService) {

  $scope.updateUser = function() { //Update the edited movie. Issues a PUT to /api/movies/:id
    $anchorScroll();
    //$scope.userBusy = $rootScope.user.$update(function() {
    $scope.userBusy = UserService.update($scope.user, function(response) {
      $scope.response = response;
      if(! response.errors) {
        $rootScope.user.setUser(response.id, response);
        $scope.user = response;
        $.notify(
            "Sikeres adatmódosítás!",
            "success",
            {clickToHide: false, autoHide: true, autoHideDelay: 1}
          );
      }
    }).$promise;
  };
});