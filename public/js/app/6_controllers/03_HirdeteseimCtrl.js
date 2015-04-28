
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
            "Extended by 30 days (" + $scope.hirdetes.lejarat + ")",
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
            "Ad has been deleted",
            "info",
            {position: 'top center', clickToHide: false, autoHide: true, autoHideDelay: 1}
          );
      }).$promise;
    }
  };

});
