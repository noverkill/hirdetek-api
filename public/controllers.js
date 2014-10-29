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

hirdetekApp.service( 'RovatService', [ '$resource', function( $resource ) {
  return $resource( 'http://localhost:8080/rovatok/:id', { id: '@id'}, {
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

hirdetekApp.service( 'RegioService', [ '$resource', function( $resource ) {
  return $resource( 'http://localhost:8080/regio/:id', { id: '@id'}, {
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

  $stateProvider.state('mainpage', {

    url: '/',
    templateUrl: 'partials/mainpage.html',
    controller: 'MainpageCtrl'

  }).state('hirdetesek', {

    url: '/hirdetesek',
    templateUrl: 'partials/hirdetesek.html',
    controller: 'HirdetesListCtrl'

  }).state('viewHirdetes', {

    url: '/hirdetes/:id/view',
    templateUrl: 'partials/reszletek.html',
    controller: 'HirdetesViewController'

  }).state('editHirdetes', {

    url: '/hirdetes/:id/edit',
    templateUrl: 'partials/hirdetes-edit.html',
    controller: 'HirdetesEditController'

  }).state('newHirdetes', {

    url: '/hirdetes/new',
    templateUrl: 'partials/hirdetes-add.html',
    controller: 'HirdetesCreateController'

  }).state('login', {

    url: '/login',
    templateUrl: 'partials/login.html',
    controller: 'LoginController'

  }).state('logout', {

    url: '/logout',
    templateUrl: 'partials/logout.html',
    controller: 'LogoutController'

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

hirdetekApp.run(['$http', '$state', '$injector', '$rootScope', '$cookieStore', 'RovatService', 'RegioService', function($http, $state, $injector, $rootScope, $cookieStore, RovatService, RegioService) {

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

  $rootScope.goHome = function() {
    $state.go('mainpage');
  }

  RovatService.query({ps: 1000}, function(response) {

    $rootScope.rovatok = response._embedded.rovatok;

    $rootScope.forovatok = [];
    $rootScope.alrovatok = [];

    for(var i = 0; i < $rootScope.rovatok.length; i++) {
      if($rootScope.rovatok[i].parent == 0) {
        $rootScope.forovatok.push($rootScope.rovatok[i]);
      } else {
        $rootScope.alrovatok.push($rootScope.rovatok[i]);
      }
    }

    //$rootScope.rovatok.splice(0, 0, {'id': 0, 'nev': 'Mindegy'});

  });

  RegioService.query({ps: 1000}, function(response) {

    $rootScope.regiok = response._embedded.regio;

    $rootScope.foregiok = [];
    $rootScope.alregiok = [];
    $rootScope._alregiok = [];

    for(var i = 0; i < $rootScope.regiok.length; i++) {
      if($rootScope.regiok[i].parent == 0) {
        $rootScope.foregiok.push($rootScope.regiok[i]);
      } else {
        $rootScope.alregiok.push($rootScope.regiok[i]);

        if(angular.isUndefined($rootScope._alregiok[$rootScope.regiok[i].parent])) {
            $rootScope._alregiok[$rootScope.regiok[i].parent] = [];
        }

        $rootScope._alregiok[$rootScope.regiok[i].parent].push($rootScope.regiok[i]);
      }
    }

    //$rootScope.regiok.splice(0, 0, {'id': 0, 'nev': 'Mindegy'});
  });

  $rootScope.resetRegio = function() {
    $rootScope.regio = {id: 0, nev: 'Regio'}
    $rootScope.foregio = {id: 0, nev: 'Regio'};
  };

  $rootScope.setRegio  = function (foregio, regio) {
    $rootScope.foregio = foregio;
    $rootScope.regio = regio || {id: 0, nev: 'Regio'};
  };

  $rootScope.resetRegio();

  $rootScope.resetRovat = function() {
    $rootScope.rovat = {id: 0, nev: 'Mindem rovatban'}
    $rootScope.forovat = {id: 0, nev: 'Minden rovatban'};
  };

  $rootScope.setRovat  = function (forovat, rovat) {
    $rootScope.forovat = forovat;
    $rootScope.rovat = rovat || {id: 0, nev: 'Minden rovat'};
  };

  $rootScope.resetRovat();

  $rootScope.filter = {
    text: '',
    minar: null,
    maxar: null
  };

  $rootScope.listing = {
    currentPage: 1,
    itemsPerPage: 25,
    maxSize: 5,
    ord: 'feladas',
    ordir: 'DESC'
  }

  $rootScope.setPage = function (pageNo) {
    $rootScope.listing.currentPage = pageNo;
  };

  $rootScope.setOrd = function (ord) {
    $rootScope.listing.ord = ord;
    $rootScope.listing.ordir = $rootScope.listing.ordir == 'DESC' ? 'ASC' : 'DESC';
  };

  $rootScope.date = new Date();

  $state.go('mainpage');

}]);

hirdetekApp.controller('MainpageCtrl', [ '$scope', '$rootScope', '$state', function ($scope, $rootScope, $state) {

  $scope.doSearch = function() {
     $state.go('hirdetesek');
  };

}]);

hirdetekApp.controller('HirdetesListCtrl', [ '$scope', '$rootScope', '$state', 'HirdetesService', function ($scope, $rootScope, $state, HirdetesService) {

  $( "#regionsBtn" ).bind( "click", function() {
      $('#myTab a:eq(0)').tab('show');
      $('#regionsModal').modal({
          keyboard: false
      });
      return false;
  });

  $scope.pageChanged = function() {
    HirdetesService.query({
          page:   $rootScope.listing.currentPage,
          rovat:  $rootScope.rovat.id || $rootScope.forovat.id,
          regio:  $rootScope.regio.id || $rootScope.foregio.id,
          search: $rootScope.filter.text,
          minar:  $rootScope.filter.minar,
          maxar:  $rootScope.filter.maxar,
          ord:    $rootScope.listing.ord,
          ordir:  $rootScope.listing.ordir
      }, function(response) {
        $scope.hirdetesek = response._embedded.hirdetes;
        $scope.totalItems = response.total_items;
    });
  };

  $scope.doSearch = function() {
     $rootScope.setPage(1);
     $scope.pageChanged();
  };

   $scope.pageChanged();

}]);

hirdetekApp.controller('HirdetesViewController', function($scope, $state, $stateParams, HirdetesService) {

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