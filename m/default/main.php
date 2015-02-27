<?php

/************************************************************************/
/*  Страница по умолчанию, заглавная v1.2o                              */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');



    // ------------------------ output ------------------------ //

  b();
  b();

  //b('<p class="h1"><span style="background: url(m/'.$mod.'/tg.png) repeat-x; position: absolute; background-position: 0 3px; display: block; width: 100%; height: 34px;"></span>Программа</p>');

  b('<p class="h1"><span style="background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAAfCAQAAABn99TqAAAAcklEQVQI1y3KoQ5BYRgA0PP/vkLC7SZ7Am+heVmRJrNrimCzW4zNTMCnOPmUTMJDVj33cKAa6sKaqnEJK6q+LmwJH7dwIry8Sw4oiTAlLJzDxKTkSVsd1aqnLblUwt7znxufMCbMdWFkVHJjV33NSr5cf+xYJTUrTywVAAAAAElFTkSuQmCC) repeat; position: absolute; background-position: 0 5px; display: block; width: 97%; height: 34px;"></span>'.$auth['org_desc'].'</p>');

  //b('<div style="text-align: center;  padding: 1mm 2mm;  font-size: 20pt;  font-weight: bold;">');
  //b('<div style="position: absolute;  background-position: 0 5px;  width: 97%;  height: 34px;  background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAAfCAQAAABn99TqAAAAcklEQVQI1y3KoQ5BYRgA0PP/vkLC7SZ7Am+heVmRJrNrimCzW4zNTMCnOPmUTMJDVj33cKAa6sKaqnEJK6q+LmwJH7dwIry8Sw4oiTAlLJzDxKTkSVsd1aqnLblUwt7znxufMCbMdWFkVHJjV33NSr5cf+xYJTUrTywVAAAAAElFTkSuQmCC) repeat-x;"></div>');
  //b($auth['org_desc']);
  //b('</div>');



?>