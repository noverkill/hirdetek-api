
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

  }).state('szabalyzat', {

    url: '/szabalyzat',
    templateUrl: 'partials/szabalyzat.html',
    //controller: 'TermsController',
    data: {requireLogin: false}

  });

// this is changed from angular 1.2 to 1.3 see below   
//http://brewhouse.io/blog/2014/12/09/authentication-made-simple-in-single-page-angularjs-applications.html
  //$httpProvider.responseInterceptors.push(
    //function($q, $injector /*, $timeout*/) {

      //// $timeout(function () {
      ////   console.log('timeout');
      ////   $injector.get('$rootScope').user.logout();
      //// });

      //return function (promise) {
        //return promise.then(
          //function (response) {
            //return response;
          //},
          //function (response) {
            //$injector.get('$rootScope').user.logout();
            //$injector.get('$state').go('login');
            //return $q.reject(response);
         // }
        //);
     // }
   // });

// to upgrade to angular 1.3 or 1.4 (to use the form filed ng-touched class) see page below
//docs.angularjs.org/api/ng/service/$http
$httpProvider.interceptors.push(function($q, $injector) {
  return {
   'request': function(config) {
       return config;
    },
    'response': function(response) {
       return response;
    },
    'requestError': function(rejection) {
    	$injector.get('$rootScope').user.logout();
        $injector.get('$state').go('login');
    	return $q.reject;
     },
     'responseError': function(rejection) {
        $injector.get('$rootScope').user.logout();
        $injector.get('$state').go('login');
        return $q.reject(rejection);
      }
    }
});

});
