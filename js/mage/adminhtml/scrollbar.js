/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
/**************************************************
 * dom-drag.js
 * 09.25.2001
 * www.youngpup.net
 **************************************************
 * 10.28.2001 - fixed minor bug where events
 * sometimes fired off the handle, not the root.
 **************************************************/

var Drag = {

  obj : null,

  init : function(o, oRoot, minX, maxX, minY, maxY, bSwapHorzRef, bSwapVertRef, fXMapper, fYMapper)
  {
    o.onmousedown  = Drag.start;

    o.hmode      = bSwapHorzRef ? false : true ;
    o.vmode      = bSwapVertRef ? false : true ;

    o.root = oRoot && oRoot != null ? oRoot : o ;

    if (o.hmode  && isNaN(parseInt(o.root.style.left  ))) o.root.style.left   = "0px";
    if (o.vmode  && isNaN(parseInt(o.root.style.top   ))) o.root.style.top    = "0px";
    if (!o.hmode && isNaN(parseInt(o.root.style.right ))) o.root.style.right  = "0px";
    if (!o.vmode && isNaN(parseInt(o.root.style.bottom))) o.root.style.bottom = "0px";

    o.minX  = typeof minX != 'undefined' ? minX : null;
    o.minY  = typeof minY != 'undefined' ? minY : null;
    o.maxX  = typeof maxX != 'undefined' ? maxX : null;
    o.maxY  = typeof maxY != 'undefined' ? maxY : null;

    o.xMapper = fXMapper ? fXMapper : null;
    o.yMapper = fYMapper ? fYMapper : null;

    o.root.onDragStart  = new Function();
    o.root.onDragEnd  = new Function();
    o.root.onDrag  = new Function();
  },

  start : function(e)
  {
    var o = Drag.obj = this;
    e = Drag.fixE(e);
    var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
    var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
    o.root.onDragStart(x, y);

    o.lastMouseX  = e.clientX;
    o.lastMouseY  = e.clientY;

    if (o.hmode) {
      if (o.minX != null)  o.minMouseX  = e.clientX - x + o.minX;
      if (o.maxX != null)  o.maxMouseX  = o.minMouseX + o.maxX - o.minX;
    } else {
      if (o.minX != null) o.maxMouseX = -o.minX + e.clientX + x;
      if (o.maxX != null) o.minMouseX = -o.maxX + e.clientX + x;
    }

    if (o.vmode) {
      if (o.minY != null)  o.minMouseY  = e.clientY - y + o.minY;
      if (o.maxY != null)  o.maxMouseY  = o.minMouseY + o.maxY - o.minY;
    } else {
      if (o.minY != null) o.maxMouseY = -o.minY + e.clientY + y;
      if (o.maxY != null) o.minMouseY = -o.maxY + e.clientY + y;
    }

    document.onmousemove  = Drag.drag;
    document.onmouseup  = Drag.end;

    return false;
  },

  drag : function(e)
  {
    e = Drag.fixE(e);
    var o = Drag.obj;

    var ey  = e.clientY;
    var ex  = e.clientX;
    var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
    var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
    var nx, ny;

    if (o.minX != null) ex = o.hmode ? Math.max(ex, o.minMouseX) : Math.min(ex, o.maxMouseX);
    if (o.maxX != null) ex = o.hmode ? Math.min(ex, o.maxMouseX) : Math.max(ex, o.minMouseX);
    if (o.minY != null) ey = o.vmode ? Math.max(ey, o.minMouseY) : Math.min(ey, o.maxMouseY);
    if (o.maxY != null) ey = o.vmode ? Math.min(ey, o.maxMouseY) : Math.max(ey, o.minMouseY);

    nx = x + ((ex - o.lastMouseX) * (o.hmode ? 1 : -1));
    ny = y + ((ey - o.lastMouseY) * (o.vmode ? 1 : -1));

    if (o.xMapper)    nx = o.xMapper(y);
    else if (o.yMapper)  ny = o.yMapper(x);

    Drag.obj.root.style[o.hmode ? "left" : "right"] = nx + "px";
    Drag.obj.root.style[o.vmode ? "top" : "bottom"] = ny + "px";
    Drag.obj.lastMouseX  = ex;
    Drag.obj.lastMouseY  = ey;

    Drag.obj.root.onDrag(nx, ny);
    return false;
  },

  end : function()
  {
    document.onmousemove = null;
    document.onmouseup   = null;
    Drag.obj.root.onDragEnd(  parseInt(Drag.obj.root.style[Drag.obj.hmode ? "left" : "right"]), 
                  parseInt(Drag.obj.root.style[Drag.obj.vmode ? "top" : "bottom"]));
    Drag.obj = null;
  },

  fixE : function(e)
  {
    if (typeof e == 'undefined') e = window.event;
    if (typeof e.layerX == 'undefined') e.layerX = e.offsetX;
    if (typeof e.layerY == 'undefined') e.layerY = e.offsetY;
    return e;
  }
};

/* =======================================================
* ypSimpleScroll
* 3/11/2001
* 
* http://www.youngpup.net/
* ======================================================= */

// Modified by Sergi Meseguer (www.zigotica.com) 04/2004
// Now it works with dragger and can use multiple instances in a page



ypSimpleScroll.prototype.scrollNorth = function(count) {
    this.startScroll(90, count);
};
ypSimpleScroll.prototype.scrollSouth = function(count) {
    this.startScroll(270, count);
};
ypSimpleScroll.prototype.scrollWest = function(count) {
    this.startScroll(180, count);
};
ypSimpleScroll.prototype.scrollEast = function(count) {
    this.startScroll(0, count);
};

ypSimpleScroll.prototype.startScroll = function(deg, count) {
  if (this.loaded){
    if (this.aniTimer) window.clearTimeout(this.aniTimer);
    this.overrideScrollAngle(deg);
    this.speed = this.origSpeed;
    this.lastTime = (new Date()).getTime() - this.y.minRes;
    this.aniTimer = window.setTimeout(this.gRef + ".scroll('"+deg+"','"+count+"')", this.y.minRes);
  }
};

ypSimpleScroll.prototype.endScroll = function() {
  if (this.loaded){
    window.clearTimeout(this.aniTimer);
    this.aniTimer = 0;
    this.speed = this.origSpeed;
  }
};

ypSimpleScroll.prototype.overrideScrollAngle = function(deg) {
  if (this.loaded){
    deg = deg % 360;
    if (deg % 90 == 0) {
      var cos = deg == 0 ? 1 : deg == 180 ? -1 : 0;
      var sin = deg == 90 ? -1 : deg == 270 ? 1 : 0;
    } 
    else {
      var angle = deg * Math.PI / 180;
      var cos = Math.cos(angle);
      var sin = Math.sin(angle);
      sin = -sin;
    }
    this.fx = cos / (Math.abs(cos) + Math.abs(sin));
    this.fy = sin / (Math.abs(cos) + Math.abs(sin));
    this.stopH = deg == 90 || deg == 270 ? this.scrollLeft : deg < 90 || deg > 270 ? this.scrollW : 0;
    this.stopV = deg == 0 || deg == 180 ? this.scrollTop : deg < 180 ? 0 : this.scrollH;
  }
};

ypSimpleScroll.prototype.overrideScrollSpeed = function(speed) {
  if (this.loaded) this.speed = speed;
};


ypSimpleScroll.prototype.scrollTo = function(stopH, stopV, aniLen) {
  if (this.loaded){
    if (stopH != this.scrollLeft || stopV != this.scrollTop) {
      if (this.aniTimer) window.clearTimeout(this.aniTimer);
      this.lastTime = (new Date()).getTime();
      var dx = Math.abs(stopH - this.scrollLeft);
      var dy = Math.abs(stopV - this.scrollTop);
      var d = Math.sqrt(Math.pow(dx,2) + Math.pow(dy,2));
      this.fx = (stopH - this.scrollLeft) / (dx + dy);
      this.fy = (stopV - this.scrollTop) / (dx + dy);
      this.stopH = stopH;
      this.stopV = stopV;
      this.speed = d / aniLen * 1000;
      window.setTimeout(this.gRef + ".scroll()", this.y.minRes);
    }
  }
};

ypSimpleScroll.prototype.jumpTo = function(nx, ny) { 
  if (this.loaded){
    nx = Math.min(Math.max(nx, 0), this.scrollW);
    ny = Math.min(Math.max(ny, 0), this.scrollH);
    this.scrollLeft = nx;
    this.scrollTop = ny;
    if (this.y.ns4)this.content.moveTo(-nx, -ny);
    else {
      this.content.style.left = -nx + "px";
      this.content.style.top = -ny + "px";
    }
  }
};

ypSimpleScroll.minRes = 10;
ypSimpleScroll.ie = document.all ? 1 : 0;
ypSimpleScroll.ns4 = document.layers ? 1 : 0;
ypSimpleScroll.dom = document.getElementById ? 1 : 0;
ypSimpleScroll.mac = navigator.platform == "MacPPC";
ypSimpleScroll.mo5 = document.getElementById && !document.all ? 1 : 0;

ypSimpleScroll.prototype.scroll = function(deg,count) {
  this.aniTimer = window.setTimeout(this.gRef + ".scroll('"+deg+"','"+count+"')", this.y.minRes);
  var nt = (new Date()).getTime();
  var d = Math.round((nt - this.lastTime) / 1000 * this.speed);
  if (d > 0){
    var nx = d * this.fx + this.scrollLeft;
    var ny = d * this.fy + this.scrollTop;
    var xOut = (nx >= this.scrollLeft && nx >= this.stopH) || (nx <= this.scrollLeft && nx <= this.stopH);
    var yOut = (ny >= this.scrollTop && ny >= this.stopV) || (ny <= this.scrollTop && ny <= this.stopV);
    if (nt - this.lastTime != 0 && 
      ((this.fx == 0 && this.fy == 0) || 
      (this.fy == 0 && xOut) || 
      (this.fx == 0 && yOut) || 
      (this.fx != 0 && this.fy != 0 && 
      xOut && yOut))) {
      this.jumpTo(this.stopH, this.stopV);
      this.endScroll();
    }
    else {
      this.jumpTo(nx, ny);
      this.lastTime = nt;
    }
  // (zgtc) now we also update dragger position:
  if(deg=='270')  theThumb[count].style.top = parseInt(((theThumb[count].maxY-theThumb[count].minY)*this.scrollTop/this.stopV)+theThumb[count].minY) + "px"; //ok nomes down
  if(deg=='90')  theThumb[count].style.top = parseInt(((theThumb[count].maxY-theThumb[count].minY)*this.scrollTop/this.scrollH)+theThumb[count].minY) + "px"; //ok nomes down
  }
};

function ypSimpleScroll(id, left, top, width, height, speed) {
  width -= 2;
  var y = this.y = ypSimpleScroll;
  if (document.layers && !y.ns4) history.go(0);
  if (y.ie || y.ns4 || y.dom) {
    this.loaded = false;
    this.id = id;
    this.origSpeed = speed;
    this.aniTimer = false;
    this.op = "";
    this.lastTime = 0;
    this.clipH = height;
    this.clipW = width;
    this.scrollTop = 0;
    this.scrollLeft = 0;
    this.gRef = "ypSimpleScroll_"+id;
    eval(this.gRef+"=this");
    var d = document;
    d.write('<style type="text/css">');
    d.write('#' + this.id + 'Container { left:0px; top:' + top + 'px; width:' + (width+15) + 'px; height:' + (height+12) + 'px; clip:rect(0 ' + (width+15) + ' ' + (height+12) + ' 0); overflow:hidden; }');
    d.write('#' + this.id + 'Container, #' + this.id + 'Content { position:absolute; }');
    d.write('#' + this.id + 'Content { left:' + (-this.scrollLeft) + 'px; top:' + (-this.scrollTop) + 'px; width:' + width + 'px; }');
    // (zgtc) fix to overwrite p/div/ul width (would be clipped if wider than scroller in css):
    // d.write('#' + this.id + 'Container p, #' + this.id + 'Container div {width:' + parseInt(width-10) + 'px; }')
    d.write('</style>');
  }
}

ypSimpleScroll.prototype.load = function() {
  var d, lyrId1, lyrId2;
  d = document;
  lyrId1 = this.id + "Container";
  lyrId2 = this.id + "Content";
  this.container = this.y.dom ? d.getElementById(lyrId1) : this.y.ie ? d.all[lyrId1] : d.layers[lyrId1];
  this.content = obj2 = this.y.ns4 ? this.container.layers[lyrId2] : this.y.ie ? d.all[lyrId2] : d.getElementById(lyrId2);
  this.docH = Math.max(this.y.ns4 ? this.content.document.height : this.content.offsetHeight, this.clipH);
  this.docW = Math.max(this.y.ns4 ? this.content.document.width : this.content.offsetWidth, this.clipW);
  this.scrollH = this.docH - this.clipH;
  this.scrollW = this.docW - this.clipW;
  this.loaded = true;
  this.scrollLeft = Math.max(Math.min(this.scrollLeft, this.scrollW),0);
  this.scrollTop = Math.max(Math.min(this.scrollTop, this.scrollH),0);
  this.jumpTo(this.scrollLeft, this.scrollTop);
};

// ==============================================================
// HANDLES SCROLLER/S
// Modified from Aaron Boodman http://webapp.youngpup.net/?request=/components/ypSimpleScroll.xml
// mixed ypSimpleScroll with dom-drag script and allowed multiple scrolelrs through array instances
// (c)2004 Sergi Meseguer (http://zigotica.com/), 04/2004:
// ==============================================================
var theHandle = []; var theRoot = []; var theThumb = []; var theScroll = []; var thumbTravel = []; var ratio = [];

function instantiateScroller(count, id, left, top, width, height, speed){
  if(document.getElementById) {
    theScroll[count] = new ypSimpleScroll(id, left, top, width, height, speed);
  }
}

function createDragger(count, handler, root, thumb, minX, maxX, minY, maxY){
    var buttons = '<div class="scroll-cont"><div class="up" id="up'+count+'">'+
                  '<a href="#" onmousedown="theScroll['+count+'].scrollNorth(\''+count+'\')" '+
                  'onmouseout="theScroll['+count+'].endScroll()" onmouseup="theScroll['+count+'].endScroll()" onclick="return false;">'+
                  '<img src="'+db_but_top+'" width="16" height="16"></a></div>'+
                  '<div class="dn"  id="dn'+count+'"">'+
                  '<a href="#" onmousedown="theScroll['+count+'].scrollSouth(\''+count+'\')" '+
                  'onmouseout="theScroll['+count+'].endScroll()" onmouseup="theScroll['+count+'].endScroll()" onclick="return false;">'+
                  '<img src="'+db_but_bot+'" width="16" height="15"></a></div>'+
                  '</div><div class="thumb" id="'+thumb+'">'+
                  '<img src="'+db_but_rol+'" width="16" height="40"></div>';    
    
    document.getElementById(root).innerHTML = buttons + document.getElementById(root).innerHTML;

    theRoot[count]   = document.getElementById(root);
    theThumb[count]  = document.getElementById(thumb);
    var thisup = document.getElementById("up"+count);
    var thisdn = document.getElementById("dn"+count);
    theThumb[count].style.left = parseInt(minX+15) + "px";
    thisup.style.left = parseInt(minX+15) + "px";
    thisdn.style.left = parseInt(minX+15) + "px";
    theThumb[count].style.border =0;
    theThumb[count].style.top = parseInt(minY) + "px";
    thisup.style.top = 0 + "px";
    thisdn.style.top = parseInt(minY+maxY) + "px";
    //thisdn.style.top = 15 + "px";

    theScroll[count].load();

    //Drag.init(theHandle[count], theRoot[count]); //not draggable on screen
    Drag.init(theThumb[count], null, minX+15, maxX+15, minY, maxY);
    
    // the number of pixels the thumb can travel vertically (max - min)
    thumbTravel[count] = theThumb[count].maxY - theThumb[count].minY;

    // the ratio between scroller movement and thumbMovement
    ratio[count] = theScroll[count].scrollH / thumbTravel[count];

    theThumb[count].onDrag = function(x, y) {
      theScroll[count].jumpTo(null, Math.round((y - theThumb[count].minY) * ratio[count]));
    };
}  

// INITIALIZER:
// ==============================================================
// ala Simon Willison http://simon.incutio.com/archive/2004/05/26/addLoadEvent
function addLoadEvent(fn) {
      var old = window.onload;
      if (typeof window.onload != 'function') {
         window.onload = fn;
      }
      else {
         window.onload = function() {
         old();
         fn();
         };
      }
   }
addLoadEvent(function(){
    if(theScroll.length>0) {
    for(var i=0;i<theScroll.length;i++){
      createDragger(i, "handle"+i, "root"+i, "thumb"+i, theScroll[i].clipW, theScroll[i].clipW, 15, theScroll[i].clipH-30);
    }
  }
});
