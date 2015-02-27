var stripe;
stripe = document.createElement('IMG');  // or var stripe = new Image();
stripe.src = '/is.php?lenta_img_t,' + th_id;

stripe.onload = function() {
  var divs = document.getElementsByTagName('DIV');
  var n = 0;
  for (var d in divs) {
    if (divs[d].className == 'image' && !divs[d].alreadyderived) {
      divs[d].alreadyderived = true;
      var canvas = document.createElement('CANVAS');
      canvas.width = 128;
      canvas.height = 96;
      canvas.style.width = divs[d].style.width;
      canvas.style.height = divs[d].style.height;
      var cn = canvas.getContext('2d');
      //                 src_x,y - src_w,h - dst_x,y - dst_w,h
      cn.drawImage(stripe, 0,(96*n++), 128,96, 0,0, 128,96);

      if (!divs[d].alreadyloading) {
        divs[d].appendChild(canvas);
        }
      }
    }
  }




var store_id = $.delay(function() {$.ajax('/'+mod+'/upi/?id='+curr_id, function() {$.note('Position saved', false, '#ecc')} )
                                   }, 2);




    // ---- check img is visible ---- //

function checkVisible() {
  var divs = document.getElementsByTagName('DIV');
  var bound;
  for (var d in divs) {
    if (divs[d].className == 'image') {
      bound = divs[d].getBoundingClientRect();
      //console.log('id: ' + pid + ', top: ' + bound.top + ', right: ' + bound.right + ', bottom: ' + bound.bottom + ', left: ' + bound.left + ', height: ' + bound.height + ', width: ' + bound.width);
      if ((bound.top > 0 && bound.top < innerHeight) || (bound.bottom > 0 && bound.bottom < innerHeight)) {
        if (!divs[d].alreadyloading && (!divs[d].firstChild || divs[d].firstChild.tagName == 'CANVAS') ) {
          divs[d].alreadyloading = true;

          var img = document.createElement('IMG');
          img.src = '/i/lenta_img,' + divs[d].id.substr(1);

          img.onload = (function (parent) {
            return  function () {
              if (parent.firstChild && parent.firstChild.tagName == 'CANVAS') {
                parent.removeChild(parent.firstChild);
                }

              parent.appendChild(img);
              }
            })(divs[d]);


          }

        }

      }
    }
  }  // checkVisible



function checkForStore() {
  var divs = document.getElementsByTagName('DIV');
  for (var d in divs) {
    if (divs[d].className == 'container') {
      if (window.pageYOffset < divs[d].offsetTop + divs[d].offsetHeight) {
        if (divs[d].id != curr_id) {
          curr_id = divs[d].id;
          store_id();
          }
        break;
        }
      }
    }

  checkVisible();
  }  // checkForStore




window.onload = function () {

    // ---- try scroll to stored position ---- //
  var divs = document.getElementsByTagName('DIV');
  for (var d in divs) {
    if (divs[d].className == 'container') {
      if (divs[d].id == curr_id) {
        window.scrollTo(0,divs[d].offsetTop-4);
        break;
        }
      }
    }


  window.onscroll = checkForStore;

  checkVisible();
  }  // onload
