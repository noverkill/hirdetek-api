var hirdetekApp = angular.module('hirdetekApp', ['ngResource', 'ui.bootstrap', 'ui.router', 'ngCookies']);
 
hirdetekApp.service('popupService',function($window){

    this.showPopup=function(message){
        return $window.confirm(message);
    }

}); 

hirdetekApp.service( 'HirdetesService', [ '$resource', function( $resource ) {
  return $resource( 'http://localhost:8080/hirdetes/:id', { id: '@id'}, {
      'query': { 
        method: 'GET', 
        isArray: false 
      },    
      'update': {
        method: 'PUT'
      }
    }
  );
}]);

hirdetekApp.service( 'UserService', [ '$resource', function( $resource ) {
  return $resource( 'http://localhost:8080/user/:id', { id: '@id'}, {
      'query': { 
        method: 'GET', 
        isArray: false 
      },    
      'update': {
        method: 'PUT'
      }
    }
  );
}]);

hirdetekApp.config(function($stateProvider) {

  $stateProvider.state('login', {
    url: '/login',
    templateUrl: 'partials/login.html',
    controller: 'LoginController'
  }).state('logout', { 
    url: '/logout',
    templateUrl: 'partials/logout.html',    
    controller: 'LogoutController'  
  }).state('hirdetesek', { 
    url: '/',
    templateUrl: 'partials/hirdetesek.html',
    controller: 'HirdetesListCtrl'
  }).state('viewHirdetes', { 
    url: '/hirdetes/:id/view',
    templateUrl: 'partials/hirdetes-view.html',
    controller: 'HirdetesViewController'    
  }).state('editHirdetes', {
    url: '/hirdetes/:id/edit',
    templateUrl: 'partials/hirdetes-edit.html',
    controller: 'HirdetesEditController'
  }).state('newHirdetes', { 
    url: '/hirdetes/new',
    templateUrl: 'partials/hirdetes-add.html',
    controller: 'HirdetesCreateController'
  }).state('users', {
    url: '/',
    templateUrl: 'partials/users.html',
    controller: 'UserListCtrl'
  }).state('viewUser', {
    url: '/user/:id/view',
    templateUrl: 'partials/user-view.html',
    controller: 'UserViewController'    
  }).state('editUser', { 
    url: '/user/:id/edit',
    templateUrl: 'partials/user-edit.html',
    controller: 'UserEditController'
  }).state('newUser', {
    url: '/user/new',
    templateUrl: 'partials/user-add.html',
    controller: 'UserCreateController'
  });  

});

hirdetekApp.run(['$http', '$state', '$injector', '$rootScope', '$cookieStore', function($http, $state, $injector, $rootScope, $cookieStore) {

  $rootScope.user = {

        credentials: {
          username: '',
          password: '',
          grant_type: 'password',
          client_id: 'testclient2'
        },
        login: function (credentials) {

          this.credentials.username = credentials.username;
          this.credentials.password = credentials.password;

          $http.post('/oauth', this.credentials)
            .then(function (res) {
            
              $cookieStore.put('tk', res.data.access_token);          
          });
        },       
        logout: function () {
            $cookieStore.remove('tk');
        },
        getTk: function() {
            return $cookieStore.get('tk'); 
        },
        isLogged: function() {   
            return angular.isDefined($cookieStore.get('tk'));
        },
        remember: function (credentials) {
          $cookieStore.put('credentials', credentials); 
        },
        forget: function () {
          $cookieStore.remove('credentials'); 
        },
        getCredentials: function() {
          return $cookieStore.get('credentials');  
        },
        hasCredentials: function() {
          return angular.isDefined($cookieStore.get('credentials'));  
        }
    }

  $injector.get("$http").defaults.transformRequest = function(data, headersGetter) {

      if($rootScope.user.isLogged()) {
        headersGetter()['Authorization'] = "Bearer " + $rootScope.user.getTk();
      }

      if (data) {
          return angular.toJson(data);
      }
  };

  $state.go('login');   

}]);

hirdetekApp.controller('LoginController', function ($scope, $rootScope, $state, $http) {
  
  if($rootScope.user.hasCredentials()) {
    $scope.credentials = $rootScope.user.getCredentials();
  }

  $scope.login = function (credentials) {

    if(credentials.remember) {
      $rootScope.user.remember(credentials);
    } else {
      $rootScope.user.forget(credentials); 
    }

    $rootScope.user.login(credentials);

    $state.go('hirdetesek');
  }
});

hirdetekApp.controller('LogoutController', function ($scope, $rootScope, $state) {

  $rootScope.user.logout();

  //$state.go('hirdetesek');

});

hirdetekApp.controller('HirdetesListCtrl', [ '$scope', 'HirdetesService', function ($scope, HirdetesService) {

	$scope.maxSize = 5;
	$scope.itemsPerPage = 25;
	$scope.currentPage = 1;
	
  $scope.setPage = function (pageNo) {
    $scope.currentPage = pageNo;
  };

  $scope.pageChanged = function() {
  	HirdetesService.query({page: $scope.currentPage}, function(response) { 	
  		$scope.hirdetesek = response._embedded.hirdetes;
  		$scope.totalItems = response.total_items; 	
  	});    
  };

  $scope.pageChanged();

}]);

hirdetekApp.controller('HirdetesViewController', function($scope, $stateParams, HirdetesService) {

	$scope.hirdetes = HirdetesService.get({ id: $stateParams.id }); 

});

hirdetekApp.controller('HirdetesEditController', function($scope, $state, $stateParams, HirdetesService, popupService) {
  
  $scope.updateHirdetes = function() { //Update the edited movie. Issues a PUT to /api/movies/:id
    $scope.hirdetes.$update(function() {
      $state.go('hirdetesek'); // on success go back to home i.e. movies state.
    });
  };

  $scope.deleteHirdetes = function() { // Delete a movie. Issues a DELETE to /api/movies/:id
    if (popupService.showPopup('Really delete this?')) {
      $scope.hirdetes.$delete(function() {
        $state.go('hirdetesek'); // on success go back to home i.e. movies state.
      });
    }
  };

  $scope.loadHirdetes = function() { //Issues a GET request to /api/movies/:id to get a movie to update
    $scope.hirdetes = HirdetesService.get({ id: $stateParams.id });
  };
 
  $scope.loadHirdetes(); // Load a movie which can be edited on UI
});


hirdetekApp.controller('HirdetesCreateController', function($scope, $state, $stateParams, HirdetesService) {
  
  $scope.hirdetes = new HirdetesService();  //create new movie instance. Properties will be set via ng-model on UI
 
  $scope.addHirdetes = function() { //create a new movie. Issues a POST to /api/movies
    $scope.hirdetes.$save(function() {
      $state.go('hirdetesek'); // on success go back to home i.e. movies state.
    });
  };

});

hirdetekApp.controller('UserListCtrl', [ '$scope', 'UserService', function ($scope, UserService) {

  $scope.maxSize = 5;
  $scope.itemsPerPage = 25;
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

hirdetekApp.controller('UserViewController', function($scope, $stateParams, UserService) {

  $scope.user = UserService.get({ id: $stateParams.id }); 

});

hirdetekApp.controller('UserEditController', function($scope, $state, $stateParams, UserService, popupService) {
  
  $scope.updateUser = function() { //Update the edited movie. Issues a PUT to /api/movies/:id
    $scope.user.$update(function() {
      $state.go('users'); // on success go back to home i.e. movies state.
    });
  };

  $scope.deleteUser = function() { // Delete a movie. Issues a DELETE to /api/movies/:id
    if (popupService.showPopup('Really delete this?')) {
      $scope.user.$delete(function() {
        $state.go('users'); // on success go back to home i.e. movies state.
      });
    }
  };

  $scope.loadUser = function() { //Issues a GET request to /api/movies/:id to get a movie to update
    $scope.user = UserService.get({ id: $stateParams.id });
  };
 
  $scope.loadUser(); // Load a movie which can be edited on UI
});


hirdetekApp.controller('UserCreateController', function($scope, $state, $stateParams, UserService) {
  
  $scope.user = new UserService();  //create new movie instance. Properties will be set via ng-model on UI
 
  $scope.addUser = function() { //create a new movie. Issues a POST to /api/movies
    $scope.user.$save(function() {
      $state.go('users'); // on success go back to home i.e. movies state.
    });
  };

});