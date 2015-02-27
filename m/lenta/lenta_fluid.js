var curr_items = [];
//var main_div;
var loader_active = false;
var enable_remove = true;



var loadFirst = function() {
  $.ajax('/'+mod+'/fli/?itm='+curr_id, loadItem2);
  }

var loadItem2 = function(val) {
  try {
    val = JSON.parse(val);

    var div = document.createElement('DIV');
    div.className = 'container';
    div.innerHTML = val.div;
    //div.innerHTML = val;
    div.new_id = val.new_id;
    document.body.appendChild(div);
    curr_items.push(div);

    loadNext();
    //$.note('loader active false');
    loader_active = false;
    }
  catch(error) {
    //val.div = val;
    //val.new_id = 0;
    //$.note('catch');
    loader_active = false;
    }

  }



var loadNext = function() {
  if (loader_active)  return;
  //$.note('loader active true');
  loader_active = true;

  var view_top = window.pageYOffset;
  var view_height = window.innerHeight;

  var total_height = 0;
  for (var i in curr_items) {
    var bound = curr_items[i].getBoundingClientRect();
    total_height += bound.height;
    }

  var tale_size = total_height - view_height - view_top;

  if ((tale_size / view_height) < 3) {
    var load_id = curr_items[curr_items.length-1].new_id;
    if (load_id) {
      //$.note('load next ('+load_id+')');
      $.ajax('/'+mod+'/fli/?itn='+load_id, loadItem2);
      }
    }

  else {
    loader_active = false;
    }

  }


var store_new_pos = $.delay(function() {$.ajax('/'+mod+'/upi/?id='+curr_id, function() {$.note('Position saved', false, '#ecc')} )
                                        }, 2);

var checkPosition = function() {
  var view_top = window.pageYOffset;

  var total_height = 0;
  for (var i in curr_items) {
    var bound = curr_items[i].getBoundingClientRect();
    total_height += bound.height;

    if (total_height > view_top) {
      //$.note('position ' + curr_items[i].new_id);
      if (curr_items[i].new_id != curr_id) {
        //console.log(curr_items[i].new_id);
        //console.log(curr_id);
        //$.note('store_new_pos');
        curr_id = curr_items[i].new_id;
        store_new_pos();
        }
      break;
      }
    }

  //var tale_size = total_height - view_height - view_top;

  }



var checkAndRemove = function() {
  if (!enable_remove)  return;

  while(true) {
    var first = curr_items[0].getBoundingClientRect();

    var view_height = window.innerHeight;


    var tale_size = window.pageYOffset - first.height;

    if ((tale_size / view_height) > 3) {
      //window.pageYOffset -= first.height;
      scrollBy(0,-first.height);
      document.body.removeChild(curr_items[0]);
      curr_items.shift();

      $.note('removed');
      }

    else break;
    }

  }





window.onload = function() {
  //main_div = document.createElement('DIV');
  //main_div.style.border = '1px solid black';
  //main_div.style.height = '560px';
  //main_div.style.overflowY = 'auto';
  //document.body.appendChild(main_div);
  //
  //main_div.tabIndex = '-1';
  //main_div.focus();

  //for_focus = document.createElement('INPUT');
  //for_focus.type = 'radio';
  //for_focus.appendChild(document.createTextNode('link'));

  //main_div.appendChild(for_focus);
  //for_focus.focus();
  //main_div.removeChild(for_focus);



  //checkAndLoad();


  //var isOperaMini = Object.prototype.toString.call(window.operamini) === "[object OperaMini]";
  //$.note(isOperaMini, 10);

  //var isOperaMobile = Object.prototype.toString.call(window.operamobile) === "[object OperaMobile]";
  //$.note(isOperaMobile, 10);

  if (innerWidth < 800) {
    enable_remove = false;
    //$.note('removing disabled', 10);
    }

  window.onscroll = function() {
    //$.note(main_div.style.height);
    //$.note('loader active = ' + loader_active);
    checkPosition();
    loadNext();
    checkAndRemove();
    }


  loadFirst();
  }
