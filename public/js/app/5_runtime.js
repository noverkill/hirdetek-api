
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

   $rootScope.rovatok.splice(0, 0, {'id': 0, 'nev': 'All categories'});

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
   $rootScope.regio = {id: 0, nev: 'Regions', order: 1}
   $rootScope.foregio = {id: 0, nev: 'Regions', order: 1};
  if($('#regionsModal').is(":visible")) {
    $('#regionsModal').modal('hide');
  }
 };

 $rootScope.setRegio  = function (foregio, regio) {
  $rootScope.foregio = foregio;
  $rootScope.regio = regio || {id: 0, nev: 'Regions', order: 1};
  if($('#regionsModal').is(":visible")) {
    $('#regionsModal').modal('hide');
  }
 };

 $rootScope.resetRovat = function() {
   $rootScope.rovat = {id: 0, nev: 'All categories'}
   $rootScope.forovat = {id: 0, nev: 'All categories'};
 };

 $rootScope.setRovat  = function (forovat, rovat) {
   $rootScope.forovat = forovat;
   $rootScope.rovat = rovat || {id: 0, nev: 'All categories'};
 };

  $rootScope.filter = {
    text: '',
    minar: null,
    maxar: null,
    postcode: null,
    distance: 0
  };

 $rootScope.resetFilter = function() {
    $rootScope.filter.minar = null;
    $rootScope.filter.maxar = null;
    $rootScope.filter.postcode = null;
    $rootScope.filter.distance = 0;
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
