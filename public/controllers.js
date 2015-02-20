var hirdetekApp = angular.module('hirdetekApp', ['ngResource', 'ui.bootstrap', 'ui.router', 'ngCookies', 'cgBusy']);

hirdetekApp.value('cgBusyDefaults',{
    message:'Kis türelmet...'
});

hirdetekApp.service('popupService',function($window){
    this.showPopup=function(message){
        return $window.confirm(message);
    }
});

hirdetekApp.service( 'HirdetesService', [ '$resource', function( $resource ) {
  return $resource( 'http://localhost:8888/hirdetes/:id', { id: '@id'}, {
      'query': {
        method: 'GET',
        isArray: false
      },
      'update': {
        method: 'PUT'
      },
      /*
      save: {
       method: 'POST',
       transformRequest: function(data) {

        console.log('hirdetes save transformRequest');
        console.log(data);

        var fd = new FormData();

        angular.forEach(data, function(value, key) {
          if (value instanceof FileList) {
            if (value.length == 1) {
              fd.append(key, value[0]);
            } else {
              angular.forEach(value, function(file, index) {
                fd.append(key + '_' + index, file);
              });
            }
          } else {
            fd.append(key, value);
          }
        });

        return fd;

       },
       header: 'undefined'
      }
      */
    }
  );
}]);

hirdetekApp.service( 'RovatService', [ '$resource', function( $resource ) {
  return $resource( 'http://localhost:8888/rovatok/:id', { id: '@id'}, {
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
  return $resource( 'http://localhost:8888/kep/:id', { id: '@id'}, {
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
  return $resource( 'http://localhost:8888/regio/:id', { id: '@id'}, {
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
  return $resource( 'http://localhost:8888/user/:id', { id: '@id'}, {
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
    templateUrl: 'partials/hirdetesek.html',
    controller: 'HirdetesListCtrl'
    // templateUrl: 'partials/mainpage.html',
    // controller: 'MainpageCtrl'

  }).state('hirdetesek', {

    url: '/hirdetesek',
    templateUrl: 'partials/hirdetesek.html',
    controller: 'HirdetesListCtrl'

  }).state('detailHirdetes', {

    url: '/hirdetes/:id/detail',
    templateUrl: 'partials/hirdetes-detail.html',
    controller: 'HirdetesDetailController'

  }).state('editHirdetes', {

    url: '/hirdetes/:id/edit',
    //templateUrl: 'partials/hirdetes-edit.html',
    templateUrl: 'partials/hirdetes-szerkesztes.html',
    controller: 'HirdetesEditController'

  }).state('newHirdetes', {

    url: '/hirdetes/new',
    templateUrl: 'partials/hirdetes-add.html',
    controller: 'HirdetesCreateController'

  }).state('hirdetes-feladas', {

    url: '/hirdetes/feladas',
    templateUrl: 'partials/hirdetes-feladas.html',
    controller: 'HirdetesCreateController'

  }).state('hirdetes-feladva', {

    url: '/hirdetes/:id/feladas',
    templateUrl: 'partials/hirdetes-feladva.html',
    controller: 'HirdetesFeladvaController'

  }).state('hirdeteseim', {

    url: '/hirdeteseim/:id',
    templateUrl: 'partials/account-ads.html',
    controller: 'HirdeteseimCtrl'

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

  }).state('profile', {

    url: '/profile/:id',
    templateUrl: 'partials/profile.html',
    controller: 'UserEditController'

  })/*.state('newUser', {

    url: '/user/new',
    templateUrl: 'partials/user-add.html',
    controller: 'UserCreateController'

  })*/.state('register', {

    url: '/register',
    templateUrl: 'partials/register.html',
    controller: 'UserCreateController'

  });

});

hirdetekApp.run(['$http', '$state', '$injector', '$rootScope', '$cookieStore', 'RovatService', 'RegioService', function($http, $state, $injector, $rootScope, $cookieStore, RovatService, RegioService) {

  $rootScope.user = {

        credentials: {
          nev: '',
          email: '',
          jelszo: '',
          remember: 1,
          grant_type: 'password',
          client_id: 'testclient'
        },
        logged: 0,
        login: function (credentials) {

          this.credentials.username = credentials.username;
          this.credentials.password = credentials.password;
          this.credentials.remember = credentials.remember;

          if(this.credentials.remember) {
            $rootScope.user.remember(credentials);
          } else {
            $rootScope.user.forget(credentials);
          }

          $http.post('/oauth', this.credentials).
            success(function(data, status, headers, config) {
              $rootScope.user.logged = 1;
              $cookieStore.put('tk', data.access_token);
              $state.go('hirdetesek');
            }).
            error(function(data, status, headers, config) {
              console.log('login error');
              console.log(data, status, headers, config);
            });

        },
        logout: function () {
            $cookieStore.remove('tk');
            $rootScope.user.logged = 0;
        },
        getTk: function() {
            return $cookieStore.get('tk');
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

  $injector.get("$http").defaults.transformRequest = function(data, headersGetter) {

      //curl -i "http://localhost:8888/oauth" --user testclient:testpass -X POST -d "grant_type=client_credentials"
      //headersGetter()['Authorization'] = "Bearer ef9475306488cfee6187de0ea483d3e357cebddb";

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

   $rootScope.viewBusy = RovatService.query({ps: 1000}, function(response) {

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

     $rootScope.rovatok.splice(0, 0, {'id': 0, 'nev': 'Mindegy'});

     // console.log($rootScope.forovatok);

     // console.log($rootScope.alrovatok);

   }).$promise;

 $rootScope.viewBusy = RegioService.query({ps: 1000}, function(response) {

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

  $rootScope.regiok.splice(0, 0, {'id': 0, 'nev': 'Mindegy'});
 }).$promise;

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

  //$state.go('mainpage');

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
        url: 'http://localhost:8888/megosztas',
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        data: $rootScope.share
    }).success(function (data, status, headers, config) {
        $rootScope.shareReset();
        $rootScope.share.success = 1;
    });

  };

  $rootScope.shareReset();

  $rootScope.saveHirdetesClick = function(id) {

    $rootScope.saveSuccess = 0;
    $rootScope.mustLoginMessage = 1;

    $rootScope.saveWait = $http({
      url: 'http://localhost:8888/kedvencek',
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
    //console.log('createPath');
    //console.log(dt);
    //console.log(name);
    var uploadDir = '/upload';
    var date = new Date(dt);
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    if(month < 10) month = '0' + month;
    var day = date.getDate();
    if(day < 10) day = '0' + day;
    var path = uploadDir + '/' + year + '/' +  month + '/' + day + '/' + name;
    //console.log(path);
    return path;
  }

  $state.go('hirdetesek');

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
      $scope.hirdetesBusy = HirdetesService.query({
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
    }, function(error) {
        $scope.hirdetesek = [];
        $scope.totalItems = 0;
    }).$promise;
  };

  $scope.doSearch = function() {
     $rootScope.setPage(1);
     $scope.pageChanged();
  };

  $scope.pageChanged();

}]);

hirdetekApp.controller('HirdeteseimCtrl', function ($scope, $rootScope, $state, $stateParams, HirdetesService, popupService) {

  $scope.pageChanged = function() {
      $scope.hirdetesBusy = HirdetesService.query({
        userid:   $stateParams.id,
      }, function(response) {
        $scope.hirdetesek = response._embedded.hirdetes;
        //console.log($scope.hirdetesek);
        $scope.totalItems = response.total_items;
    }, function(error) {
        $scope.hirdetesek = [];
        $scope.totalItems = 0;
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

hirdetekApp.controller('HirdetesDetailController', function($scope, $state, $stateParams, HirdetesService) {

	$scope.hirdetesService = HirdetesService.get({
    id: $stateParams.id
  }, function(response) {
    $scope.hirdetes = response;
    //console.log(response);
  }).$promise;

  $scope.doSearch = function() {
     $state.go('hirdetesek');
  };

});

hirdetekApp.controller('HirdetesEditController', function($scope, $rootScope, $http, $state, $stateParams, HirdetesService, KepService) {

  $scope.updateHirdetes = function() {
    //console.log($scope.hirdetes);
    $scope.hirdetesService = $scope.hirdetes.$update(function(response) {
        //console.log(response);
        $scope.response = response;
        if(response.success) {
          //$state.go('hirdetes-feladva',{id:response.id});
          $state.go('hirdeteseim');
          //$scope.hirdetes = {};
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
    headers: {'Authorization': 'Bearer ' + $rootScope.user.getTk()},
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
    url: "/hirdetes?id=" + $stateParams.id,
    maxFiles: 6,
    acceptedFiles: 'image/jpeg, image/gif, image/png',
    dictRemoveFile: '',
    dictMaxFilesExceeded: 'max 6 kép tölthető fel!',
    headers: {'Authorization': 'Bearer ' + $rootScope.user.getTk()},
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

hirdetekApp.controller('HirdetesCreateController', function($scope, $state, $stateParams, HirdetesService) {

  $scope.error = 0;

  $scope.hirdetes = new HirdetesService();
  $scope.hirdetes.lejarat = 365;

  $scope.createHirdetes = function() {
    $scope.hirdetesBusy = $scope.hirdetes.$save(function(response) {
        //console.log(response);
        $scope.response = response;
        if(response.success) {
          $state.go('hirdetes-feladva',{id:response.id});
          //$scope.hirdetes = {};
        } else {
          //console.log("not response.success");
          $scope.error = 1;
        }
        //$state.go('hirdetesek');
      });
  };
});

hirdetekApp.controller('LoginController', function ($scope, $rootScope, $state, $http) {

  if($rootScope.user.hasCredentials()) {
    $scope.credentials = $rootScope.user.getCredentials();
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
    $scope.userBusy = $scope.user.$update(function() {
      //$state.go('users'); // on success go back to home i.e. movies state.
    });
  };

  $scope.deleteUser = function() { // Delete a movie. Issues a DELETE to /api/movies/:id
    if (popupService.showPopup('Really delete this?')) {
      $scope.user.$delete(function() {
        $state.go('users'); // on success go back to home i.e. movies state.
      });
    }
  };

  $scope.loadUser = function() {
    $scope.userBusy = UserService.get({ id: $stateParams.id }, function(response) {
        $scope.user = response;
    }).$promise;
  };

  $scope.loadUser();
});


hirdetekApp.controller('UserCreateController', function($scope, $state, $stateParams, UserService) {

  $scope.registered = 0;

  $scope.user = new UserService();

  $scope.addUser = function() {
     $scope.userBusy = $scope.user.$save(function() {
      $scope.user = {};
      $scope.registered = 1;
      //$state.go('users');
    });
  };

});