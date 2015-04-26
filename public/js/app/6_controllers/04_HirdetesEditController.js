
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
