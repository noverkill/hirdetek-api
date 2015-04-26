
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
