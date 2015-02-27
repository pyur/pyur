var $chart = function(div) {
  var w = 800;
  var h = 200;
  div = $.id(div);

  var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  //svg.style.width = div.offsetWidth+'px';
  //svg.style.height = div.offsetHeight+'px';
  //svg.width.baseVal.value = w;
  //svg.height.baseVal.value = h;
  svg.style.width = '800px';
  svg.style.height = '200px';
  svg.width.baseVal.value = 800;
  svg.height.baseVal.value = 200;

  div.appendChild(svg);

  var ns = svg.namespaceURI;



  var rect = function (x,y,w,h,p) {
    if (p === undefined)  p = {};

    var rect = document.createElementNS(ns, 'rect');
    rect.x.baseVal.value = x;
    rect.y.baseVal.value = y;
    rect.width.baseVal.value = w;
    rect.height.baseVal.value = h;

    if (p.s !== undefined)  rect.style.stroke = p.s;
    if (p.w !== undefined)  rect.style.strokeWidth = p.w;
    if (p.f !== undefined)  rect.style.fill = p.f;  else  rect.style.fill = 'none';

    svg.appendChild(rect);
    }



  var line = function (x1,y1,x2,y2,p) {
    if (p === undefined)  p = {};

    var line = document.createElementNS(ns, 'line');
    line.x1.baseVal.value = x1;
    line.y1.baseVal.value = y1;
    line.x2.baseVal.value = x2;
    line.y2.baseVal.value = y2;

    if (p.s !== undefined)  line.style.stroke = p.s;  else  line.style.stroke = '#000';
    if (p.w !== undefined)  line.style.strokeWidth = p.w + 'px';
    //if (p.f !== undefined)  line.style.fill = p.f;
    svg.appendChild(line);
    }




  var createMarker = function () {
    var defs = document.createElementNS(ns, 'defs');
    var marker = document.createElementNS(ns, 'marker');
    marker.id = 'mymarker';
    marker.setAttribute("markerWidth", 8);
    marker.setAttribute("markerHeight", 8);
    marker.setAttribute("refX", 5);
    marker.setAttribute("refY", 5);

    var circle = document.createElementNS(ns, 'circle');
    circle.setAttribute("cx", 5);
    circle.setAttribute("cy", 5);
    circle.setAttribute("r", 2.3);
    circle.setAttribute("style", "stroke: #fff;  stroke-width: 0.8px;  fill:#000000;");

    marker.appendChild(circle);
    defs.appendChild(marker);

    var marker = document.createElementNS(ns, 'marker');
    marker.id = 'mymarkerb';
    marker.setAttribute("markerWidth", 8);
    marker.setAttribute("markerHeight", 8);
    marker.setAttribute("refX", 5);
    marker.setAttribute("refY", 5);

    var circle = document.createElementNS(ns, 'circle');
    circle.setAttribute("cx", 5);
    circle.setAttribute("cy", 5);
    circle.setAttribute("r", 2.3);
    circle.setAttribute("style", "stroke: #fff;  stroke-width: 0.8px;  fill:#888800;");

    marker.appendChild(circle);
    defs.appendChild(marker);

    svg.appendChild(defs);
    }




  var polyline = function (p) {
    if (p === undefined)  p = {};

    var polyline = document.createElementNS(ns, 'polyline');

    if (p.s !== undefined)  polyline.style.stroke = p.s;  else  polyline.style.stroke = '#000';
    if (p.w !== undefined)  polyline.style.strokeWidth = p.w;
    if (p.f !== undefined)  polyline.style.fill = p.f;  else  polyline.style.fill = 'none';
    polyline.style.marker = 'url(#mymarker)';

    svg.appendChild(polyline);

    this.add = function(x,y) {
      var point = svg.createSVGPoint();
      point.x = x;
      point.y = y;
      //console.log(point);
      //point.style.marker = 'url(#mymarkerb)';
      polyline.points.appendItem(point);
      }

    return  this;
    }



    // ---------------- electricity ---------------- //

  this.elec = function(cdata) {
    var i;

    rect(0.5,0.5, w-1,h-1, {s:'#000',w:1});


      // ---- chart ---- //

    line(20,h-20, 20,20, {s:'#008',w:2});
    line(20,h-20, w-20,h-20, {s:'#008',w:2});


    for (i = 1; i <= 7; i++) {
      line(16,(h-20) - i*20, 24,(h-20) - i*20, {s:'#008',w:2});
      line(24,(h-19.5) - i*20, w-20,(h-19.5) - i*20, {s:'#ccf'});
      }


    createMarker();

    var pl = polyline({s:'#080',w:2});

    for (i in cdata) {
      pl.add(30 + i*25, (h-20) - cdata[i]*20);
      }
    }



    // ---------------- water ---------------- //

  this.water = function(cdata) {
    var i;

    rect(0.5,0.5, w-1,h-1, {s:'#000',w:1});


      // ---- chart ---- //

    line(20,(h-20), 20,20, {s:'#008',w:2});
    line(20,(h-20), (w-20),(h-20), {s:'#008',w:2});

    var step = 2;
    var step_grid = step*10;
    for (i = (h-20); i >= 21; i-=step_grid) {
      if (i == (h-20))  continue;
      line(16,i, 24,i, {s:'#008',w:2});
      line(24,i-0.5, (w-20),i-0.5, {s:'#ccf'});
      }


    createMarker();


    var pl = polyline({s:'#08f',w:2});

    for (i in cdata[0]) {
      pl.add(30 + i*25, (h-20) - cdata[0][i]*step);
      }


    var pl = polyline({s:'#f00',w:2});

    for (i in cdata[1]) {
      pl.add(30 + i*25, (h-20) - cdata[1][i]*step);
      }
    }

  }

