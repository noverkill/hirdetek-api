//var oldTime = 0;
//showTime();

function showTime() {

    var date = new Date();
    var newTime = date.getTime();

    console.log(newTime);
    console.log('oldTime: ' + oldTime);

    if(oldTime>0) {
      var elapsed = newTime - oldTime;
      console.log('Elapsed: ' + elapsed);
    }

    oldTime = newTime;
}

var hirdetekApp = angular.module('hirdetekApp', ['ngResource', 'ui.bootstrap', 'ui.router', 'ngCookies', 'cgBusy']);

// this is shit !!! but this is the best i found so far!!!
// so stupiu and typical !!!! these little shits can't programm !!!
// i need to find or write a better one!!!
hirdetekApp.directive('validPasswordC', function () {
    return {
        require: 'ngModel',
        link: function (scope, elm, attrs, ctrl) {
            ctrl.$parsers.unshift(function (viewValue, $scope) {
                var noMatch = viewValue != scope.regForm.password.$viewValue
                ctrl.$setValidity('noMatch', !noMatch)
            })
        }
    }
})

var EMAIL_REGEXP = /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;

hirdetekApp.directive('validEmail', function() {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      //viewValue = angular.lowercase(viewValue);
      ctrl.$parsers.unshift(function(viewValue) {
        viewValue = angular.lowercase(viewValue);
	if (EMAIL_REGEXP.test(viewValue)) {
          // it is valid
          ctrl.$setValidity('validEmail', true);
          return viewValue;
        } else {
          // it is invalid, return undefined (no model update)
          ctrl.$setValidity('validEmail', false);
          return undefined;
        }
      });
    }
  };
});

hirdetekApp.directive('uniqueEmail', ["UserService", function (UserService) {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      scope.emailChecking = false;
      ctrl.$parsers.push(function (viewValue) {
        if (viewValue) {
          scope.emailChecking = true;
          ctrl.$setValidity('uniqueEmail', true);
          UserService.get({'email':viewValue}, function (users) {
            if (users.total_items > 0) {
              ctrl.$setValidity('uniqueEmail', false);
            } else {
              ctrl.$setValidity('uniqueEmail', true);
            }
          }).$promise.then(function(){
            scope.emailChecking = false;
          });
          return viewValue;
        }
      });
    }
  };
}]);
/*
hirdetekApp.directive('uniqueEmail', ["Users", function (Users) {
  return {
    require:'ngModel',
    restrict:'A',
    link:function (scope, el, attrs, ctrl) {

      console.log('fuck');

      //TODO: We need to check that the value is different to the original

      //using push() here to run it as the last parser, after we are sure that other validators were run
      ctrl.$parsers.push(function (viewValue) {

        if (viewValue) {
          Users.query({email:viewValue}, function (users) {
            if (users.length === 0) {
              ctrl.$setValidity('uniqueEmail', true);
            } else {
              ctrl.$setValidity('uniqueEmail', false);
            }
          });
          return viewValue;
        }
      });
    }
  };
}]);
*/

//https://robots.thoughtbot.com/preload-resource-data-into-angularjs
hirdetekApp.directive("preloadResource", function() {
    return {
      link: function(scope, element, attrs) {
        scope.preloadResource = JSON.parse(attrs.preloadResource);
        element.remove();
      }
    };
  });

hirdetekApp.value('cgBusyDefaults',{
    message:'Betöltés...'
});

hirdetekApp.service('popupService',function($window){
    this.showPopup=function(message){
        return $window.confirm(message);
    }
});

hirdetekApp.service( 'HirdetesService', [ '$resource', function( $resource ) {
  return $resource( siteurl + '/hirdetes/:id', { id: '@id'}, {
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
  return $resource( siteurl + '/rovatok/:id', { id: '@id'}, {
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

hirdetekApp.service( 'KepService', [ '$resource', function( $resource ) {
  return $resource( siteurl + '/kep/:id', { id: '@id'}, {
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
  return $resource( siteurl + '/regio/:id', { id: '@id'}, {
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
  return $resource( siteurl + '/user/:id', { id: '@id'}, {
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

hirdetekApp.service( 'MegosztasService', [ '$resource', function( $resource ) {
  return $resource( siteurl + '/megosztas/:id', { id: '@id'}, {
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

hirdetekApp.config(function($httpProvider, $stateProvider) {

  $stateProvider.state('mainpage', {

    url: '',
    templateUrl: 'partials/hirdetesek.html',
    controller: 'HirdetesListCtrl',
    data: {requireLogin: false}
    // templateUrl: 'partials/mainpage.html',
    // controller: 'MainpageCtrl'

  }).state('hirdetesek', {

    url: '/hirdetesek',
    templateUrl: 'partials/hirdetesek.html',
    controller: 'HirdetesListCtrl',
    data: {requireLogin: false}

  }).state('detailHirdetes', {

    url: '/hirdetes/:id/detail',
    templateUrl: 'partials/hirdetes-detail.html',
    controller: 'HirdetesDetailController',
    data: {requireLogin: false}

  }).state('editHirdetes', {

    url: '/hirdetes/:id/edit',
    //templateUrl: 'partials/hirdetes-edit.html',
    templateUrl: 'partials/hirdetes-szerkesztes.html',
    controller: 'HirdetesEditController',
    data: {requireLogin: true}

  })/*.state('newHirdetes', {

    url: '/hirdetes/new',
    templateUrl: 'partials/hirdetes-add.html',
    controller: 'HirdetesCreateController',
    data: {requireLogin: false}

  })*/.state('hirdetes-feladas', {

    url: '/hirdetes/feladas',
    templateUrl: 'partials/hirdetes-feladas.html',
    controller: 'HirdetesCreateController',
    data: {requireLogin: false}

  }).state('hirdetes-feladva', {

    url: '/hirdetes/:id/feladas',
    templateUrl: 'partials/hirdetes-feladva.html',
    controller: 'HirdetesFeladvaController',
    data: {requireLogin: false}

  }).state('hirdeteseim', {

    url: '/hirdeteseim/:id',
    templateUrl: 'partials/account-ads.html',
    controller: 'HirdeteseimCtrl',
    data: {requireLogin: true}

  }).state('login', {

    url: '/login',
    templateUrl: 'partials/login.html',
    controller: 'LoginController',
    data: {requireLogin: false}

  }).state('logout', {

    url: '/logout',
    templateUrl: 'partials/login.html',
    controller: 'LogoutController',
    data: {requireLogin: false}

  }).state('users', {

    url: '/',
    templateUrl: 'partials/users.html',
    controller: 'UserListCtrl',
    data: {requireLogin: true}

  })/*.state('viewUser', {
    url: '/user/:id/view',
    templateUrl: 'partials/user-view.html',
    controller: 'UserViewController',
    data: {requireLogin: true}

  }).state('editUser', {

    url: '/user/:id/edit',
    templateUrl: 'partials/user-edit.html',
    controller: 'UserEditController',
    data: {requireLogin: true}

  })*/.state('profile', {

    url: '/profile',
    templateUrl: 'partials/profile.html',
    controller: 'UserEditController',
    data: {requireLogin: true}

  })/*.state('newUser', {

    url: '/user/new',
    templateUrl: 'partials/user-add.html',
    controller: 'UserCreateController',
    data: {requireLogin: true}

  })*/.state('register', {

    url: '/register',
    templateUrl: 'partials/register.html',
    controller: 'UserCreateController',
    data: {requireLogin: false}

  }).state('jelszo', {

    url: '/jelszo',
    templateUrl: 'partials/password.html',
    controller: 'PasswordController',
    data: {requireLogin: false}

  }).state('kapcsolat', {

    url: '/kapcsolat',
    templateUrl: 'partials/kapcsolat.html',
    controller: 'ContactController',
    data: {requireLogin: false}

  });

  //http://brewhouse.io/blog/2014/12/09/authentication-made-simple-in-single-page-angularjs-applications.html
  $httpProvider.responseInterceptors.push(
    function($q, $injector /*, $timeout*/) {

      /*
      $timeout(function () {
        console.log('timeout');
        $injector.get('$rootScope').user.logout();
      });
      */

      return function (promise) {
          return promise.then(function (response) {
              return response;
          },
          function (response) {
              $injector.get('$rootScope').user.logout();
              $injector.get('$state').go('login');
              return $q.reject(response);
          });
      }
    });

});

hirdetekApp.run(['$http', '$state', '$injector', '$rootScope', '$cookieStore', '$anchorScroll', 'UserService', 'RovatService', 'RegioService', function($http, $state, $injector, $rootScope, $cookieStore, $anchorScroll, UserService, RovatService, RegioService) {

  //showTime();

    /*
    $rootScope
        .$on('$stateChangeStart',
            function(event, toState, toParams, fromState, fromParams){
                //console.log('stateChangeStart');
                $("#ui-view").html("");
                //$(".page-loading").removeClass("hidden");
        });
    $rootScope
        .$on('$stateChangeSuccess',
            function(event, toState, toParams, fromState, fromParams){
                //$(".page-loading").addClass("hidden");
        });
    */

  $injector.get("$http").defaults.transformRequest = function(data, headersGetter) {

      //curl -i siteurl + "/oauth" --user testclient:testpass -X POST -d "grant_type=client_credentials"
      //headersGetter()['Authorization'] = "Bearer ef9475306488cfee6187de0ea483d3e357cebddb";

      if($rootScope.user.isLogged()) {
        headersGetter()['Authorization'] = "Bearer " + $rootScope.user.getTk();
      }

      if (data) {
          return angular.toJson(data);
      }
  };

  //http://brewhouse.io/blog/2014/12/09/authentication-made-simple-in-single-page-angularjs-applications.html
  $rootScope.$on('$stateChangeStart', function (event, toState, toParams) {

    var requireLogin = toState.data.requireLogin;

    if (requireLogin && (! $rootScope.user.isLogged())) {
      event.preventDefault();
      $state.go('login');
    }

    //console.log(toState);
    //console.log(toParams);

    if(toState.controller == "HirdetesFeladvaController") {
      if ($rootScope.feladasId != toParams.id) {
        event.preventDefault();
        $state.go('login');
      }
      $rootScope.feladasId = 0;
    }

  });

  $rootScope.user = {

        id: 0,
        credentials: {
          nev: '',
          email: '',
          jelszo: '',
          jelszo_megegyszer: '',
          remember: 1,
          grant_type: 'password',
          client_id: 'testclient'
        },
        details: {},
        logged: 0,
        login: function (credentials, formValid) {

          $anchorScroll();

          if(! formValid) return;

          this.credentials.username = credentials.username;
          this.credentials.password = credentials.password;
          this.credentials.remember = credentials.remember;

          if(this.credentials.remember) {
            $rootScope.user.remember(credentials);
          } else {
            $rootScope.user.forget(credentials);
          }

          $rootScope.userBusy = $http.post('/oauth', this.credentials).
            success(function(data, status, headers, config) {
              $cookieStore.put('tk', data.access_token);
              $rootScope.userBusy = UserService.get({ id: 100 }, function(response) {
                //$rootScope.user.id = response.id;
                //$rootScope.user.details = response;
                $cookieStore.put('user', {'id': response.id, details: response});
                $rootScope.login_error = 0;
                //$rootScope.user.logged = 1;
                $state.go('hirdetesek');
              }).$promise;
            }).
            error(function(data, status, headers, config) {
              //console.log('login error');
              //console.log(data, status, headers, config);
              $rootScope.login_error = 1;
            });

        },
        logout: function () {
            $cookieStore.remove('tk');
            $cookieStore.remove('user');
            $rootScope.user.logged = 0;
        },
        getTk: function() {
            return $cookieStore.get('tk');
        },
        getUser: function() {
            return $cookieStore.get('user');
        },
        setUser: function(id, details) {
            $cookieStore.put('user', {'id': id, 'details': details});
        },
        isLogged: function() {
            return ($rootScope.user.logged = angular.isDefined($cookieStore.get('tk')));
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

  $rootScope.goHome = function() {
    $state.go('mainpage');
  }

  $rootScope.loadRovatok = function () {

    if($rootScope.rovatokLoaded) return;

   $rootScope.rovatok = $rootScope.preloadResource.rovatok._embedded.rovatok;

   $rootScope.forovatok = [];
   $rootScope.alrovatok = [];

   for(var i = 0; i < $rootScope.rovatok.length; i++) {
     if($rootScope.rovatok[i].parent == 0) {
       $rootScope.forovatok.push($rootScope.rovatok[i]);
     } else {
       $rootScope.alrovatok.push($rootScope.rovatok[i]);
     }
   }

   $rootScope.rovatok.splice(0, 0, {'id': 0, 'nev': 'Mindegy'});

    $rootScope.rovatokLoaded = 1;
  }

  $rootScope.loadRegiok = function () {

    if($rootScope.regiokLoaded) return;

    $rootScope.regiok = $rootScope.preloadResource.regiok._embedded.regio;

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

    $rootScope.regiokLoaded = 1;
  }

/* for the regio chooser control */
 $rootScope.setRegio2  = function (foregio) {
   $rootScope.foregio2 = foregio;
 };
/**************************************/

$rootScope.resetRegio = function() {
   $rootScope.regio = {id: 0, nev: 'Régió', order: 1}
   $rootScope.foregio = {id: 0, nev: 'Régió', order: 1};
  if($('#regionsModal').is(":visible")) {
    $('#regionsModal').modal('hide');
  }
 };

 $rootScope.setRegio  = function (foregio, regio) {
  $rootScope.foregio = foregio;
  $rootScope.regio = regio || {id: 0, nev: 'Régió', order: 1};
  if($('#regionsModal').is(":visible")) {
    $('#regionsModal').modal('hide');
  }
 };

 $rootScope.resetRovat = function() {
   $rootScope.rovat = {id: 0, nev: 'Mindem rovatban'}
   $rootScope.forovat = {id: 0, nev: 'Minden rovatban'};
 };

 $rootScope.setRovat  = function (forovat, rovat) {
   $rootScope.forovat = forovat;
   $rootScope.rovat = rovat || {id: 0, nev: 'Minden rovat'};
 };

 $rootScope.resetFilter = function() {
    $rootScope.filter = {
      text: '',
      minar: null,
      maxar: null
    };
 };

 $rootScope.resetPriceFilter = function() {
    $rootScope.filter.minar = null;
    $rootScope.filter.maxar = null;
 };

  $rootScope.listing = {
    server: {
      hirdetesek: [],
      itemsPerPage: 50,
      totalPages: 0,
      currentPage: 0,
      direction: 1, //upward
      preloadPage: 5,
      setCurrentPage: function(p) {
        $rootScope.listing.server.currentPage = p;
      }
    },
    currentPage: 0,
    itemsPerPage: 10,    //server.itemsPerpage should be divisible by this (server.itemsPerpage-nek oszthatonak kell lenni ezzel az ertekkel)
    totalItems: 0,
    totalPages: 0,
    maxPagerSize: 4,
    pagerPages: [],
    ord: 'feladas',
    ordir: 'DESC',
    setCurrentPage: function(p) {
      $rootScope.listing.direction = ($rootScope.listing.currentPage <= p) ? 1 : -1;
      $rootScope.listing.currentPage = p;
    }
  }

  $rootScope.setOrd = function (ord) {
    $rootScope.listing.ord = ord;
    $rootScope.listing.ordir = $rootScope.listing.ordir == 'DESC' ? 'ASC' : 'DESC';
  };

  //modal window's setup functions

  $rootScope.login = function() {
     $rootScope.mustLoginMessage = 0;
  };

  $rootScope.shareReset = function(id) {
    $rootScope.share = {
      id: 0,
      sender: {
        name: '',
        email: ''
      },
      recipient: {
        name: '',
        email: ''
      }
    }
  };

  $rootScope.shareHirdetesClick = function(id) {
     $rootScope.share.id = id;
     $rootScope.share.success = 0;
  };

  $rootScope.shareHirdetes = function() {

    $rootScope.share.success = 0;

    $rootScope.megosztasWait = $http({
        url: siteurl + '/megosztas',
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        data: $rootScope.share
    }).success(function (data, status, headers, config) {
        $rootScope.shareReset();
        $rootScope.share.success = 1;
    });

  };

  $rootScope.saveHirdetesClick = function(id) {

    $rootScope.saveSuccess = 0;
    $rootScope.mustLoginMessage = 1;

    $rootScope.saveWait = $http({
      url: siteurl + '/kedvencek',
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      data: {'id': id}
    }).success(function (data, status, headers, config) {
      $rootScope.saveSuccess = 1;
    }).error(function (data, status, headers, config) {
      $rootScope.saveSuccess = 2;
    });

  };

  $rootScope.createPath = function (dt, name) {
    if(dt == null || name == null) return;

    // handle cross browser issue
    dt = dt.replace(/-/g,"/");

    //console.log('createPath');
    //console.log(dt);
    //console.log(name);

    var date = new Date(dt);
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    if(month < 10) month = '0' + month;
    var day = date.getDate();
    if(day < 10) day = '0' + day;
    var path = uploadDir + '/' + year + '/' +  month + '/' + day + '/' + (kepDir != '' ? (kepDir + '/') : '') + name;
    //console.log(path);
    return path;
  }

  $rootScope.date = new Date();

  $rootScope.rovatokLoaded = 0;
  $rootScope.regiokLoaded = 0;

  $rootScope.resetRegio();
  $rootScope.resetRovat();

  $rootScope.resetFilter();

  //$rootScope.shareReset();

  // 0 = the hirdetes details will not be refreshed from the server
  // if it is already loaded with the page of hirdetes list
  // it can set to 1 to make sure that the details always reloads from the server
  // it is useful for example when the user modified his ad, so he would like to see
  // the effect when he goes back to the ad's detail page
  $rootScope.detailRefresh = 0;

  $rootScope.HirdetesekLoaded = 0;

}]);

/*
hirdetekApp.controller('MainpageCtrl', [ '$scope', '$rootScope', '$state', function ($scope, $rootScope, $state) {

  $scope.doSearch = function() {
     $state.go('hirdetesek');
  };

}]);
*/

hirdetekApp.controller('HirdetesListCtrl', [ '$scope', '$rootScope', '$state', '$anchorScroll', 'HirdetesService', function ($scope, $rootScope, $state, $anchorScroll, HirdetesService) {

  $anchorScroll();

  $scope.loadHirdetesek = function() {
    return HirdetesService.query({
      page:   $rootScope.listing.server.currentPage,
      rovat:  $rootScope.rovat.id || $rootScope.forovat.id,
      regio:  $rootScope.regio.id || $rootScope.foregio.id,
      search: $rootScope.filter.text,
      minar:  $rootScope.filter.minar,
      maxar:  $rootScope.filter.maxar,
      ord:    $rootScope.listing.ord,
      ordir:  $rootScope.listing.ordir
      }, function(response) {
        $rootScope.listing.server.hirdetesek[$rootScope.listing.server.currentPage] = response._embedded.hirdetes;
        $rootScope.listing.totalItems = response.total_items;
        $rootScope.listing.totalPages = Math.ceil($rootScope.listing.totalItems / $rootScope.listing.itemsPerPage);
        $rootScope.listing.server.totalPages = Math.ceil($rootScope.listing.totalItems / $rootScope.listing.server.itemsPerPage);
        //showTime();
      }, function(error) {

      }
    ).$promise;
  }

  $scope.gotoPage = function(p) {

    $anchorScroll();

    if(p < 1 || p > $rootScope.listing.totalPages) {
      $rootScope.hirdetesek = [];
      return;
    }

    $rootScope.listing.setCurrentPage(p);
    $rootScope.listing.server.setCurrentPage(Math.ceil((p * $rootScope.listing.itemsPerPage) / $rootScope.listing.server.itemsPerPage));

    if(angular.isUndefined($rootScope.listing.server.hirdetesek[$rootScope.listing.server.currentPage])){
      //post-load hirdetesek
      $scope.hirdetesBusy = $scope.loadHirdetesek().then(function(){
        $scope.sliceHirdetesek(p);
      });
    } else {
      $scope.sliceHirdetesek(p);
    }
  }

  $scope.sliceHirdetesek = function(p) {

    var startIndex = (((p - 1) * $rootScope.listing.itemsPerPage) % ($rootScope.listing.server.itemsPerPage));
    var endIndex = startIndex + $rootScope.listing.itemsPerPage;

    $rootScope.hirdetesek = $rootScope.listing.server.hirdetesek[$rootScope.listing.server.currentPage].slice(startIndex, endIndex);

    $rootScope.listing.pagerPages = [];
    for(var page = p - $rootScope.listing.maxPagerSize; page < p - 1 + $rootScope.listing.maxPagerSize; page++)
      $rootScope.listing.pagerPages.push(page);

    //$scope.preloadPages();
  }

  //pre-load hirdetesek
  $scope.preloadPages = function() {

    var preloadNeedUpToPage = $rootScope.listing.server.currentPage + ($rootScope.listing.direction * $rootScope.listing.server.preloadPage);

    if(! angular.isDefined($rootScope.listing.server.hirdetesek[preloadNeedUpToPage])) {

      var i = 1;

      while(i < preloadNeedUpToPage) {

        var preloadPage = $rootScope.listing.server.currentPage + ($rootScope.listing.direction * i);

        if(preloadPage < 1 || preloadPage > $rootScope.listing.server.totalPages) return;

        if(! angular.isDefined($rootScope.listing.server.hirdetesek[preloadPage])) {
          $rootScope.listing.server.setCurrentPage(preloadPage);
          $scope.loadHirdetesek();
          break;
        }

        i++;
      }
    } else {
        //console.log('preloaded');
    }

    /*
    if($rootScope.listing.server.hirdetesek.length < (endIndex + $rootScope.listing.server.preloadPage * $rootScope.listing.itemsPerPage)) {
      console.log('pre-load');
      if($rootScope.listing.server.currentPage + 1 < $rootScope.listing.server.totalPages) {
        $rootScope.listing.server.currentPage++;
        $scope.loadHirdetesek();
      }
    }
    */
  }

  if(! $rootScope.HirdetesekLoaded) {

    /*
    $rootScope.listing.server.hirdetesek = [];

    $scope.hirdetesBusy = $scope.loadHirdetesek().then(function(){
      $rootScope.HirdetesekLoaded = 1;
      $scope.gotoPage(1);
    })
    */

    $anchorScroll();

    var response = $rootScope.preloadResource.hirdetesek;

    $rootScope.listing.server.setCurrentPage(1);
    $rootScope.listing.server.hirdetesek[1] = response._embedded.hirdetes;
    $rootScope.listing.totalItems = response.total_items;
    $rootScope.listing.totalPages = Math.ceil($rootScope.listing.totalItems / $rootScope.listing.itemsPerPage);
    $rootScope.listing.server.totalPages = Math.ceil($rootScope.listing.totalItems / $rootScope.listing.server.itemsPerPage);

    $scope.gotoPage(1);

    $rootScope.loadRovatok();
    $rootScope.loadRegiok();

    /* default for the regio chooser control */
    $rootScope.foregio2 = $rootScope.foregiok[0];

    $rootScope.HirdetesekLoaded = 1;
  }

  $scope.doSearch = function() {

    $anchorScroll();

    $rootScope.listing.server.setCurrentPage(1);
    $rootScope.listing.server.hirdetesek = [];
    $scope.hirdetesBusy = $scope.loadHirdetesek().then(function(){
      $scope.gotoPage(1);
    })
  };

  $( "#regionsBtn" ).bind( "click", function() {
      if($('#regionsModal').is(":visible")) {
        $('#regionsModal').modal('hide');
      } else {
        $('#myTab a:eq(' + ($rootScope.foregio.order - 1) + ')').tab('show');
        $('#regionsModal').modal({
            keyboard: false
        });
      }
      return false;
  });

  $(document).click(function(event) {
      if($(event.target).parents().index($('#regionsModal')) == -1) {
          if($('#regionsModal').is(":visible")) {
              $('#regionsModal').modal('hide');
          }
      }
  });
}]);

hirdetekApp.controller('HirdetesDetailController', function($scope, $rootScope, $state, $stateParams, $filter, $anchorScroll, HirdetesService, MegosztasService) {

  /*
  $scope.hirdetesService = HirdetesService.get({
    id: $stateParams.id
  }, function(response) {
    $scope.hirdetes = response;
    //console.log(response);
  }).$promise;
  */

   $anchorScroll();

  $scope.message = {};

  if($rootScope.user.isLogged()) $scope.message.email = $rootScope.user.getUser().details.email;

  $scope.submitted = 0;
  $scope.success = 0;
  $scope.hideTelNum = 1;

   var found = $filter('filter')($rootScope.hirdetesek, {id: $stateParams.id}, true);

   if (angular.isDefined(found) && found.length && ! $rootScope.detailRefresh) {
      $scope.hirdetes = found[0];
      $scope.telNum = $scope.hirdetes.telefon.substring(1,3);
      if (! angular.isDefined($scope.hirdetes.images)) {
        $scope.imagesBusy = HirdetesService.get({
          id: $stateParams.id
        }, function(response) {
          $scope.hirdetes.images = response.images;
        }).$promise;
      }
   } else {
      $scope.hirdetesService = HirdetesService.get({
        id: $stateParams.id
      }, function(response) {
        $scope.hirdetes = response;
        $scope.telNum = $scope.hirdetes.telefon.substring(1,3);
      }).$promise;
   }

  $scope.doSearch = function() {
     $state.go('hirdetesek');
  }

  $scope.sendMessage = function(form) {

      $scope.submitted = 1;

      if(! form.$valid) return;

      $scope.megosztas = new MegosztasService();
      $scope.megosztas.adid = $stateParams.id;
      $scope.megosztas.func = "reply";
      $scope.megosztas.email = $scope.message.email;
      $scope.megosztas.text = $scope.message.text;

      $scope.hirdetesBusy = $scope.megosztas.$save(function(response) {
          console.log(response);
          $scope.response = response;
          if(response.success) {
            $scope.megosztas = {};
            $scope.success = 1;
          } else {
            //console.log("not response.success");
            $scope.error = 1;
            //$scope.hirdetes = response.data;
            $anchorScroll();
          }
        });
  };

  $scope.showTelNum = function() {
      $scope.telNum = $scope.hirdetes.telefon;
      $scope.hideTelNum = 0;
  };

});

hirdetekApp.controller('HirdeteseimCtrl', function ($scope, $rootScope, $state, $stateParams, HirdetesService, popupService) {

  $scope.hirdetesek = {};

  $scope.pageChanged = function() {
      $scope.hirdetesBusy = HirdetesService.query({
        userid:   $stateParams.id,
      }, function(response) {
        $scope.hirdetesek = response._embedded.hirdetes;
        //console.log($scope.hirdetesek);
        $rootScope.listing.totalItems = response.total_items;
    }, function(error) {
        $scope.hirdetesek = [];
        $rootScope.listing.totalItems = 0;
    }).$promise;
  };

  $scope.pageChanged();

  $scope.extendHirdetes = function(hirdetes) {
    hirdetes.lejarat = 30;
    //console.log(hirdetes);
    $scope.hirdetesBusy = HirdetesService.update({id: hirdetes.id}, hirdetes, function(response) {
        //console.log(response);
        $scope.response = response;
        $scope.hirdetes = response;
        //console.log($scope.hirdetes);
        //if(response.success) {
          $("#extend-ad-" + hirdetes.id).notify(
            "Meghosszabítva 30 nappal (" + $scope.hirdetes.lejarat + ")",
            "success",
            {clickToHide: false, autoHide: true, autoHideDelay: 1}
          );
        //} else {}
    }).$promise;
  };

  $scope.deleteHirdetes = function(id) { // Delete a movie. Issues a DELETE to /api/movies/:id
    if (popupService.showPopup('Biztosan törli?')) {
    $scope.hirdetesBusy = HirdetesService.delete({id: id}, function(response) {
        //console.log(response);
        $('#ad-' + id).remove();
          $.notify(
            "Hirdetés sikeresen törölve!",
            "info",
            {position: 'top center', clickToHide: false, autoHide: true, autoHideDelay: 1}
          );
      }).$promise;
    }
  };

});

hirdetekApp.controller('HirdetesEditController', function($scope, $rootScope, $http, $state, $stateParams, $anchorScroll, HirdetesService, KepService) {

  $rootScope.loadRovatok();
  $rootScope.loadRegiok();

  $anchorScroll();

  $scope.updateHirdetes = function() {
    // set to 1 here, so from now on the hirdetes' details will be always refreshed from the server
    // this is to make sure that the user will see the effect of his modifica\tion when he goes back to
    // the hirdetes detail's page
    $rootScope.detailRefresh = 1;
    $scope.error = 0;
    $anchorScroll();
    $scope.hirdetesService = $scope.hirdetes.$update(function(response) {
        //console.log(response);
        $scope.response = response;

        if(response.success) {
          //$state.go('hirdetes-feladva',{id:response.id});
          //$state.go('hirdeteseim', {id: $rootScope.user.getUser().id});
          //$scope.hirdetes = {};
          $.notify(
              "Sikeres hirdetés módosítás!",
              "success",
              {clickToHide: false, autoHide: true, autoHideDelay: 1}
            );
        } else {
          //console.log("not response.success");
          $scope.error = 1;
        }
        //$state.go('hirdetesek');
      });
    //console.log($scope.hirdetes);
  };

/*
  $scope.loadHirdetes = function() { //Issues a GET request to /api/movies/:id to get a movie to update
    //$scope.hirdetes = HirdetesService.get({ id: $stateParams.id });
    //console.log($scope.hirdetes);

    $scope.hirdetesService = HirdetesService.get({
      id: $stateParams.id
    }, function(response) {
      $scope.hirdetes = response;
      //console.log(response);
    }).$promise;
  };

  $scope.loadHirdetes(); // Load a movie which can be edited on UI
*/

  var myDropzone = new Dropzone("div#myDropzone", {
    url: "/hirdetes?id=" + $stateParams.id,
    maxFiles: 6,
    acceptedFiles: 'image/jpeg, image/gif, image/png',
    dictRemoveFile: '',
    dictMaxFilesExceeded: 'max 6 kép tölthető fel!',
    //headers: {'Authorization': 'Bearer ' + $rootScope.user.getTk()},
    dictRemoveFileConfirmation: "Biztosan törli?",
    init: function() {

      this.element.querySelector(".dz-message").remove();

      thisDropzone = this;

      thisDropzone.on("complete", function(file) {
          if(! file.xhr) return;
          file.image_id = angular.fromJson(file.xhr.response).image_id;
          $(file.previewElement).data('id', file.image_id); // updates the data object
          $(file.previewElement).attr('data-id', file.image_id); // updates the attribute

      });

      thisDropzone.on("addedfile", function(file) {

        // Create the remove button
        var removeButton = Dropzone.createElement("<button class='btn btn-dropzone'>Kép törlés</button>");

        // Capture the Dropzone instance as closure.
        var _this = thisDropzone;
        var _file = file;

        //console.log('addedfile');
        //console.log(_file);
        //console.log(_file.previewElement);

        if( _file.image_id) {
          $(_file.previewElement).data('id', _file.image_id); // updates the data object
          $(_file.previewElement).attr('data-id', _file.image_id); // updates the attribute
        }

        // Listen to the click event
        removeButton.addEventListener("click", function(e) {
          // Make sure the button click doesn't submit the form:
          e.preventDefault();
          e.stopPropagation();

          //console.log(_file);

          if(! _file.image_id && ! _file.accepted) {
            return _this.removeFile(file);
          }

          if(confirm("Biztosan törli?")) {

            var image_id = _file.image_id || angular.fromJson(_file.xhr.response).image_id;

            //console.log(image_id);

            KepService.delete({id: image_id}, function(response) {
                //console.log(response);
                //$scope.response = response;
                // Remove the file preview.
                thisDropzone.removeFile(file);
            });
          }
        });
        file.previewElement.appendChild(removeButton);
      });

      $scope.hirdetesService = HirdetesService.get({
        id: $stateParams.id
      }, function(response) {
        //console.log(response);

        response.ar = parseFloat(response.ar);

        $scope.hirdetes = response;

        var images = response.images;
        if(response.image_id) {
          images.unshift({'id': response.image_id, 'ad_id': $stateParams.id, 'user_id': '???', 'created': response.image_created, 'name': response.image_name, 'sorrend': 1});
        }
        //console.log(images);
        $.each(images, function(key,value){
            var mockFile = { name: value.name, size: value.size, image_id: value.id, accepted: 1, upload: {bytesSent: 123} };
            //thisDropzone.options.addedfile.call(thisDropzone, mockFile);
            thisDropzone.files.push(mockFile);
            mockFile.status = Dropzone.ADDED;
            thisDropzone.emit("addedfile", mockFile);
            thisDropzone.options.thumbnail.call(thisDropzone, mockFile,  $rootScope.createPath(value.created, value.name));
        });
      }).$promise;
    }
  });

  $("div#myDropzone").sortable({
      items:'.dz-preview',
      cursor: 'move',
      opacity: 0.5,
      containment: '#myDropzone',
      distance: 20,
      tolerance: 'pointer',
      update: function(event, ui) {
        var data = $(this).sortable('toArray', {attribute: 'data-id'});//.toString();
        for(var i = 0; i < data.length; i++) {
          //console.log(data[i]);
          if(data[i] == "") {
            alert("Amennyiben új képet töltött fel akkor kérjük az átrendezés elött várja meg amíg a kép teljesen feltöltődik (zöld pipa) vagy amennyiben nem töltődött fel (piros X) akkor kérjük távolítsa el!");
            return false;
          }
        }
        $scope.hirdetesService = $http({
          method: 'POST',
          url: "/kep",
          data: data,
        });
      }
  });

});

hirdetekApp.controller('HirdetesFeladvaController', function($scope, $rootScope, $state, $stateParams, HirdetesService, KepService) {

  //console.log('HirdetesFeladvaController');
  //console.log($stateParams.id);

  $scope.hirdetes = {id: $stateParams.id};

  var myDropzone = new Dropzone("div#myDropzone", {
    url: "/hirdetes?id=" + $stateParams.id + "&kod=" + $rootScope.kod,
    maxFiles: 6,
    acceptedFiles: 'image/jpeg, image/gif, image/png',
    dictRemoveFile: '',
    dictMaxFilesExceeded: 'max 6 kép tölthető fel!',
    //headers: {'Authorization': 'Bearer ' + $rootScope.user.getTk()},
    init: function() {
      this.on("addedfile", function(file) {
        // Create the remove button
        var removeButton = Dropzone.createElement("<button class='btn btn-dropzone'>Kép törlés</button>");

        // Capture the Dropzone instance as closure.
        var _this = this;
        var _file = file;

        // Listen to the click event
        removeButton.addEventListener("click", function(e) {
          // Make sure the button click doesn't submit the form:
          e.preventDefault();
          e.stopPropagation();

          var image_id = angular.fromJson(_file.xhr.response).image_id;

          //console.log(image_id);

          KepService.delete({id: image_id}, function(response) {
              //console.log(response);
              //$scope.response = response;

              // Remove the file preview.
              _this.removeFile(file);
          });
        });
        file.previewElement.appendChild(removeButton);
      });
    }
  });
});

hirdetekApp.controller('HirdetesCreateController', function($rootScope, $scope, $state, $stateParams, $anchorScroll, HirdetesService) {

  $rootScope.loadRovatok();
  $rootScope.loadRegiok();

  $scope.error = 0;

  $scope.hirdetes = new HirdetesService();
  $scope.hirdetes.lejarat = 365;

  if($rootScope.user.isLogged()) {
    var user = $rootScope.user.getUser().details;
    $scope.hirdetes.nev = user.nev;
    $scope.hirdetes.email = user.email;
  }

  $scope.createHirdetes = function(formValid) {

    if(! formValid) return;

    $anchorScroll();

    $scope.hirdetesBusy = $scope.hirdetes.$save(function(response) {
        //console.log(response);
        $scope.response = response;
        if(response.success) {
          $rootScope.feladasId = response.id;
          $rootScope.kod = response.kod;
          $state.go('hirdetes-feladva',{id:response.id});
          //$scope.hirdetes = {};
        } else {
          //console.log("not response.success");
          $scope.error = 1;
          //$scope.hirdetes = response.data;
          $anchorScroll();
        }
        //$state.go('hirdetesek');
      });
  };
});

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
