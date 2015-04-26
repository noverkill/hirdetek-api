
hirdetekApp.controller('ContactController', function($rootScope, $scope, $state, $stateParams, $anchorScroll, UserService) {

  $anchorScroll();

  $scope.contact = {contact: 1};

  if($rootScope.user.isLogged()) {
    $scope.contact.userid = $rootScope.user.getUser().id;
    $scope.contact.nev = $rootScope.user.getUser().details.nev;
    $scope.contact.email = $rootScope.user.getUser().details.email;
  } else {
    $scope.contact.userid = 0;
  }

  $scope.sendContact = function(form) {

    $anchorScroll();

    $scope.success = 0;

    if(! form.$valid) return;

    $scope.contactBusy = UserService.save($scope.contact,
      function (response) {
        $scope.success = 1;
    }).$promise
  }

});