/*global document:false, videolength:false */

var slider = {
    dragger: null,
    progress: null,
    init: function () {
        "use strict";
        slider.dragger = document.getElementById("drag");
        slider.dragger.style.display = "block";
        slider.progress = document.getElementById("progress");
        
        slider.dragger.onmousedown = slider.startDrag;
        document.body.onmouseup = slider.endDrag;
        document.body.onmousemove = slider.drag;
        
        slider.dragger.parentElement.onclick = slider.click;
    },
    dragging: false,
    percentage: 0,
    
    startDrag: function (e) {
        "use strict";
        slider.dragging = true;
        
        return false;
    },
    drag: function (e) {
        "use strict";
        var p = slider.dragger.parentElement,
            s = p.getBoundingClientRect(),
            w = p.offsetWidth,
            o = null;
        
        if (slider.dragging) {
            o = e.pageX - s.left;
            slider.percentage = o / w * 100;
            if (-1 < o && slider.percentage < (w - 5) * 100 / w) {
                slider.dragger.style.left = slider.progress.style.width = slider.percentage + "%";
                return false;
            }
        }
    },
    endDrag: function (e) {
        "use strict";
        if (slider.dragging) {
            slider.dragging = false;
            slider.callback();
            return false;
        }
    },
    click: function (e) {
        "use strict";
        slider.startDrag(null);
        slider.drag(e);
        slider.endDrag(null);
    },
    
    callback: function () {
        "use strict";
        slider.ajax();
    },
    ajax: function () {
        "use strict";
        var x = new XMLHttpRequest();
        x.onreadystatechange = function () {
            var s = x.readyState === 4 && x.status === 200;
        };
        x.open("GET", "setpos.php?p=" + (slider.percentage * videolength / 100) + "&cache=" + Math.random(), true);
        x.send();
    }
};

(function () {
    "use strict";
    slider.init();
})();