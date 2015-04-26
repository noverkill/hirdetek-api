
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
      postcode: $rootScope.filter.postcode,
      distance: $rootScope.filter.distance,
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
