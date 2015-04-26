
hirdetekApp.controller('LoginController', function ($scope, $rootScope, $state, $http) {

  if($rootScope.user.isLogged()) {
    $state.go('hirdetesek');
  }

  if($rootScope.user.hasCredentials()) {
    $scope.credentials = $rootScope.user.getCredentials();
  }

});

hirdetekApp.controller('LogoutController', function ($scope, $rootScope, $state) {
  //console.log('logoutController');
  $rootScope.user.logout();
  $state.go('login');
});

/*
hirdetekApp.controller('UserListCtrl', [ '$scope', 'UserService', function ($scope, UserService) {

  $scope.maxSize = 10;
  $scope.itemsPerPage = 50;
  $scope.currentPage = 1;

  $scope.setPage = function (pageNo) {
    $scope.currentPage = pageNo;
  };

  $scope.pageChanged = function() {
    UserService.query({page: $scope.currentPage}, function(response) {
      $scope.users = response._embedded.users;
      $scope.totalItems = response.total_items;
    });
  };

  $scope.pageChanged();

}]);
*/

/*
hirdetekApp.controller('UserViewController', function($scope, $stateParams, UserService) {

  $scope.user = UserService.get({ id: $stateParams.id });

});
*/

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

  /*
  $scope.deleteUser = function() {
    if (popupService.showPopup('Really delete this?')) {
      $scope.user.$delete(function() {
        $state.go('users');
      });
    }
  };
  */

  /*
  $scope.loadUser = function() {
    $scope.userBusy = UserService.get({ id: 100 }, function(response) {
        $scope.user = response;
    }).$promise;
  };

  $scope.loadUser();
  */

  $scope.user = $rootScope.user.getUser().details;

});

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