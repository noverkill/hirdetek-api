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
