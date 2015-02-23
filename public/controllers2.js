var hirdetekApp = angular.module('hirdetekApp', ['ngResource', 'ui.bootstrap', 'ui.router', 'ngCookies', 'cgBusy']);

hirdetekApp.value('cgBusyDefaults',{
    message:'Kis türelmet...'
});

hirdetekApp.service( 'HirdetesService', [ '$resource', function( $resource ) {
  return $resource( 'http://localhost:8888/hirdetes/:id', { id: '@id'}, {
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

  $stateProvider.state('hirdetesek', {

    url: '/hirdetesek',
    templateUrl: 'partials/hirdetesek2.html',
    controller: 'HirdetesListCtrl'

  });

});

hirdetekApp.controller('MainpageCtrl', [ '$scope', '$rootScope', '$state', function ($scope, $rootScope, $state) {

  $scope.doSearch = function() {
     $state.go('hirdetesek');
  };

}]);

hirdetekApp.controller('HirdetesListCtrl', [ '$scope', '$rootScope', '$state', 'HirdetesService', function ($scope, $rootScope, $state, HirdetesService) {

  $scope.pageChanged = function(p) {
      $scope.hirdetesBusy = HirdetesService.query({
          page:   p,
          rovat:  0,
          regio:  0,
          search: '',
          //minar:  $rootScope.filter.minar,
          //maxar:  $rootScope.filter.maxar,
          ord:    'feladas',
          ordir:  'DESC'
      }, function(response) {
        $scope.hirdetesek = response._embedded.hirdetes;
        $scope.totalItems = response.total_items;
    }, function(error) {
        $scope.hirdetesek = [];
        $scope.totalItems = 0;
    }).$promise;
  };

  $scope.pageChanged(1);

}]);

