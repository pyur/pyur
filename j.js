
  // -------- v2.60 ---------------------------------------------------------------- //

var pyur = {

    // ---- get/set DOM element by ID ---- //
  id: function(k,v) {
    return  document.getElementById(k);
    },

    // ---- get DOM array of elements by CSS ---- //
  css: function(k) {
    //return  document.getElementById(v);
    },

    // ---- get inner HTML ---- //
  gh: function(k) {
    return  document.getElementById(k).innerHTML;
    },

    // ---- put inner HTML ---- //
  ph: function(k,v) {
    document.getElementById(k).innerHTML = v;
    },

    // ---- get/set cookie ---- //
  cookie: function(k,v) {
    //document.getElementById(v);
    },




    // -------------------------------- event ------------------------------------------------------------------------------------------------ //

  event: function(id,e,f) {
    var o;
    if (typeof(id) === 'string')  o = $.id(id);
    else if (typeof(id) === 'object')  o = id;
    else  return;
    o.addEventListener(e, f, false);
    },




    // -------------------------------- non-click hide ---------------------------------------------------------------------------------------- //

  nc_hide: function(id) {
    var o;
    o = $.id(id);
    //console.log('1) '+o);

    var nch = function(e) {
      e = e.target;
      //console.log('3) '+o);
      //console.log('e) '+e);

      while(e) {
        //console.log(e);
        if (e.id == id)  return;  // console.log('found!'); 
        e = e.parentNode;
        }

      //console.log('NOT found!');
      o.style.display = 'none';
      window.removeEventListener('mousedown', nch);
      };

    window.addEventListener('mousedown', nch, false);
    },




    // -------------------------------- delay ------------------------------------------------------------------------------------------------ //

  Delay: function (func, delay) {
    var timeout;

    this.retry = function () {
      clearTimeout(timeout);
      timeout = setTimeout(func, delay*1000);
      }

    },

  delay: function (func, delay) {
    var d = new this.Delay(func, delay);
    return  d.retry;
    },




    // -------------------------------- ajax v2 ------------------------------------------------------------------------------------------------ //

  ajax: function(u,r,p) {
    if (p === undefined)  p = {};
    if (p.async === undefined)  p.async = true;

    p.type = 'GET';
    if (p.post !== undefined) {
      p.type = 'POST';
      }

    var ar = new XMLHttpRequest();
    var arf = null;

    ar.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        if (r)  r(this.responseText);
        }
      if (this.readyState == 4 && this.status != 200) {
        if (p.fail)  p.fail(this);
        }
      };

    ar.open(p.type, u, p.async);
    if (p.post !== undefined) {
      if (p.post.tagName === "FORM") {
        var arf = new FormData(p.post);
        }
      else {
        var arf = new FormData();
        for (var i in p.post)  arf.append(i, p.post[i]);
        }
      }

    ar.send(arf);
    },




    // -------------------------------- file api v1 ------------------------------------------------------------------------------------------------ //

  file_area: function(id,url) {
    var dz = $.id(id);
    dz.addEventListener('dragover', function(de) {
      de.stopPropagation();
      de.preventDefault();
      de.dataTransfer.dropEffect = 'copy';
      }, false);
    dz.addEventListener('drop',

    (
    function(theURL) {
    return  function(de) {
      de.stopPropagation();
      de.preventDefault();

      $.note('file(s) dropped');

      var files = de.dataTransfer.files;

      for (var i = 0; i < files.length; i++) {
        var f = files[i];
        //$.note(f.name + ' - ' + f.size + ' bytes, (' + f.type + ')');

        f.durl = url;
        $.file_queue.push(f);
        }

      $.file_queue_send();
      };
      })(url)

      , false);


    var cz = document.createElement('INPUT');  // new File();
    cz.type = 'file';
    cz.style.width = dz.offsetWidth + 'px';
    cz.style.height = dz.offsetHeight + 'px';
    cz.style.opacity = '0';
    cz.style.position = 'absolute';
    cz.style.cursor = 'default';
    cz.multiple = true;
    dz.insertBefore(cz, dz.firstChild);

    cz.addEventListener('change', function(ce) {
      //console.log(url);
      $.note('file(s) choosed');

      var files = ce.target.files;

      for (var i = 0; i < files.length; i++) {
        var f = files[i];
        //$.note(f.name + ' - ' + f.size + ' bytes, (' + f.type + ')');

        f.durl = url;
        $.file_queue.push(f);
        }

      $.file_queue_send();
      }, false);
    },


  file_queue: [],
  file_queue_busy: false,

  file_queue_send: function() {
    if (!this.file_queue_busy && this.file_queue.length) {
      this.file_queue_busy = true;

      $.note('file_queue_send started');

      var f = this.file_queue.shift();

      var ar = new XMLHttpRequest();

      ar.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          $.note(this.responseText);
          $.file_queue_busy = false;
          $.file_queue_send();
          }
        };

      ar.open('POST', f.durl, true);
      //ar.setRequestHeader('Content-Type', 'application/octet-stream');
      //ar.withCredentials = true; // allow cookies to be sent
      var arfd = new FormData();
      arfd.append('data', f);
      ar.send(arfd);
      }
    },




    // -------------------------------- context menu ------------------------------------------------------------------------------------------------ //

  context_menu: false,
  context_menus: [],
  context_bubble: false,


  context: function(id,p) {
    if (p === undefined)  p = {};
    new this.Context(id,p);
    },


  context_hidem: function() {
    for (m in $.context_menus)  $.context_menus[m].style.display = 'none';
    $.context_menu = false;
    },
  context_hideb: function() {
    if ($.context_bubble)  $.context_bubble.style.display = 'none';
    },


  Context: function(id,p) {
    var col_id = [];
    var menu = {};
    var bubble;
    var mover_t;

    var mdown = function(e) {
      e.stopPropagation();
      e.preventDefault();

      $.context_hidem();

      var t = e.target;

      while (t.tagName != 'TD') {
        if (t.parentNode === undefined)  return;
        t = t.parentNode;
        }


        // ---- right mouse button ---- //
      if (e.button == 2) {

          // ---- calculate column number ---- //
        var child = t.previousSibling;
        col_num = 0;
        while (child) {
          child = child.previousSibling;
          col_num++;
          }

          // ---- calculate row id ---- //
        var child = t.parentNode;
        var row_id = child.firstChild.id;


        if (p.menu[col_id[col_num]]) {
          var cm = menu[col_id[col_num]];
          var cc = p.menu[col_id[col_num]];


          p.par = {row: row_id, col: col_id[col_num]};
          p.parq = '&row=' + row_id + '&col=' + col_id[col_num];

          for (var i in cc) {
            if (cc[i].href) {
              cc[i].hrefl.href = cc[i].href + p.parq;
              }
            }

            // ---- display hidden elements ---- //
          cm.style.display = 'block';
          cm.style.left = e.pageX - 30 + 'px';
          cm.style.top = e.pageY + 2 + 'px';

          $.context_menu = true;
          $.context_hideb();
          }

        }  // end: right mouse button


      }

    var mover = function(e) {
      var t = e.target;
      if ($.context_bubble && !$.context_menu) {
        clearTimeout(mover_t);
        mover_t = setTimeout(function() {moverd(e)}, 500);
        }
      }

    var mout = function(e) {
      clearTimeout(mover_t);
      $.context_hideb();
      }

      // -------- bubble -------- //
    var moverd = function(e) {
      if ($.context_menu)  return;

      var t = e.target;

      while (t.tagName != 'TD') {
        if (t.parentNode === undefined)  return;
        t = t.parentNode;
        }

        // ---- calculate column number ---- //
      var child = t.previousSibling;
      col_num = 0;
      while (child) {
        child = child.previousSibling;
        col_num++;
        }

        // ---- calculate row id ---- //
      var child = t.parentNode;
      var row_id = child.firstChild.id;


      if (p.bubble[col_id[col_num]]) {
        var cc = p.bubble[col_id[col_num]];

        var maxwidth = (bubble.maxwidth ? bubble.maxwidth : 0);

        bubble.style.left = e.pageX + cc.offsetx - maxwidth + 'px';
        bubble.style.top  = e.pageY + cc.offsety + 'px';

        $.ajax(cc.link + '&row=' + row_id + '&col=' + col_id[col_num], function(r){if(r){bubble.innerHTML = r;  bubble.style.display = 'block';}});
          // if (bubble.maxwidth)  pb.style.left = parseInt(pb.style.left) - pb.offsetWidth + bubble.maxwidth + 'px';
        }

      }

    var mdownw = function(e) {
      var t = e.target;
      if (t.tagName == 'A')  {
        $.delay($.context_hidem, 0.2)();
        return;
        }
      if (t.idt != 'menu')  $.context_hidem();
      }




      // ---------------- init ---------------- //

      // ---- read columns' ids ---- //
    var child = $.id(id).firstChild.firstChild.firstChild;
    while (child) {
      col_id.push(child.id);
      child = child.nextSibling;
      }


      // ---- create menu(s) ---- //
    if (p.menu !== undefined) {

      for (m in p.menu) {
        var dm = document.createElement('DIV');
        dm.idt = 'menu';
        dm.style.display = 'none';
        dm.className = 'cmenu';

        var table = document.createElement('TABLE');

        for (i in p.menu[m]) {
          var ci = p.menu[m][i];

          var tr = table.insertRow(-1);

          var td = tr.insertCell(-1);
          td.className = 'cmenu';

          var but = document.createElement('DIV');
          but.idt = 'menu';
          but.className = 'cmenub';
          but.unselectable = 'on';

          if (ci.href) {
            var anch = document.createElement('A');
            anch.className = 'k';
            //anch.unselectable = 'on';
            if (ci.blank)  anch.target = '_blank';
            ci.hrefl = anch;
            anch.appendChild(document.createTextNode(ci.desc));
            but.appendChild(anch);
            }

          else if (ci.ajax) {
            if (ci.call === undefined)  ci.call = function() {};
            but.ajax = ci.ajax;
            but.call = ci.call;
            but.onclick = function() {$.context_hidem(); $.ajax(this.ajax + p.parq, this.call)};
            but.appendChild(document.createTextNode(ci.desc));
            }

          else if (ci.code) {
            but.code = ci.code;
            but.onclick = function() {this.code(p.par)};
            but.appendChild(document.createTextNode(ci.desc));
            }

          td.appendChild(but);
          }

        dm.appendChild(table);
        document.body.appendChild(dm);

        menu[m] = dm;
        $.context_menus.push(dm);
        }
      }


      // ---- create bubble(s) ---- //
    if (p.bubble !== undefined) {

      bubble = document.createElement('DIV');
      bubble.style.display = 'none';
      bubble.className = 'cbubble';

      document.body.appendChild(bubble);

      $.context_bubble = bubble;
      }



      // ---- attach events ---- //
    $.event(id, 'contextmenu', mdown);
    $.event(window, 'mousedown', mdownw);
    $.event(id, 'mouseover', mover);
    $.event(id, 'mouseout', mout);
    },




    // -------------------------------- notification v2 ------------------------------------------------------------------------------------------------ //

  note_div: false,

    // ---- note object ---- //
  Note: function(txt, cwait, color) {

    var fps = 1000/30;  // 30 fps
    var anim_t;
    var frame = 0;
    var stat = 0;
    var nt;
    var ntd;
    var div_height;
    var wait = 90;
    var trigger = false;
    var fade = 30;
    var collapse = 15;

    if (!$.note_div) {
      $.note_div = document.createElement('DIV');
      $.note_div.style.position = 'fixed';
      $.note_div.style.fontSize = '10pt';
      $.note_div.style.left = '8px';
      $.note_div.style.top = '8px';
      $.note_div.style.width = '200px';
  
      document.body.appendChild($.note_div);
      }


    if (cwait !== undefined) {
      if (typeof(cwait) == 'number') {
        wait = cwait;
        if (wait > 0)  wait *= 1000/fps;
        }
      }


    var animation = function() {
      frame++;

      switch (stat) {

        case 0:
          if (frame > wait) {
            if (wait == -1)  {frame--;  break;}
            frame = 0;
            stat = 1;
            }
          break;

        case 1:
          if (frame > fade) {
            frame = 0;
            stat = 2;
            div_height = ntd.offsetHeight;
            break;
            }

          //ntd.style.opacity = Math.round((1-(frame / fade)) *100) /100;  // linear
          //var f = Math.round((1-Math.sin((frame / fade) * (Math.PI/2))) *100) /100;
          var f = Math.round((1-Math.sqrt(Math.sin((frame / fade) * (Math.PI/2)))) *100) /100;
          //console.log(frame + ' - ' + f);
          ntd.style.opacity = f;

          break;

        case 2:
          if (frame > collapse) {
            frame = 0;
            stat = 99;
            break;
            }

          var f = Math.round((1 - Math.sin((frame / collapse) * (Math.PI/2))) *div_height) + 'px';
          //console.log(f);
          ntd.style.height = f;

          break;

        default:
          // destroy
          clearTimeout(anim_t);
          $.note_div.removeChild(ntd);
          break;

        }  // switch

      }  // function a()

    ntd = document.createElement('DIV');
    ntd.style.overflow = 'hidden';
    nt = document.createElement('DIV');
    nt.style.border = '1px solid black';
    nt.style.borderRadius = '6px';
    nt.style.backgroundColor = color || '#eee';
    nt.style.padding = '2px 4px';
    nt.style.margin = '0 0 6px 0';

    nt.innerHTML = txt;

    ntd.appendChild(nt);
    $.note_div.appendChild(ntd);

    if (wait == -1) {
      $.event(ntd, 'click', function() {wait = 0;});  // remove_event_listener
      }

    anim_t = setInterval(animation, fps);  //fps
    },  // Note


    // ---- invoke note ---- //
  note: function(txt, cwait, color) {
    new this.Note(txt, cwait, color);
    },


  end: 0
  };




  // ---------------- Global vars ---------------- //

var gpage;




var $ = pyur;

