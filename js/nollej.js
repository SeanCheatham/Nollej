//var sys = arbor.ParticleSystem(1000, 100, 0.2);

var sys = arbor.ParticleSystem({friction:.2, stiffness:1000, repulsion:0, precision:0})
function getLinks(q,n){
    if(n>8) return;
    if(n>=0) {
        var c = "rgba("+Math.floor((Math.random() * 255) + 1)+","+Math.floor((Math.random() * 255) + 1)+","+Math.floor((Math.random() * 255) + 1)+",1)";

        $.get('../getLinksJSON/' + encodeURIComponent(q), function (data) {
            if (data.links.length > 0) {
                var x;
                if(!(x=sys.getNode(q))){

                    x = sys.addNode(q,{'myColor':c});
                    x.fillStyle = c;
                }
                data.links.forEach(function (entry) {
                    var y;
                    if(!(y=sys.getNode(entry))){

                        y = sys.addNode(entry,{'myColor':"white"});
                        y.fillStyle = c;
                    }
                    var edge = sys.addEdge(x, y);
                    if (edge) edge.strokeStyle = c;
                    getLinks(entry, n - 1);
                });
            }
            else alert('Invalid Page');
        });
    }
}

(function($){

    var Renderer = function(canvas){
        var canvas = $(canvas).get(0);
        var ctx = canvas.getContext("2d");
        var particleSystem;

        var that = {
            init:function(system){
                //
                // the particle system will call the init function once, right before the
                // first frame is to be drawn. it's a good place to set up the canvas and
                // to pass the canvas size to the particle system
                //
                // save a reference to the particle system for use in the .redraw() loop
                particleSystem = system;

                // inform the system of the screen dimensions so it can map coords for us.
                // if the canvas is ever resized, screenSize should be called again with
                // the new dimensions
                particleSystem.screenSize(canvas.width, canvas.height)
                particleSystem.screenPadding(80) // leave an extra 80px of whitespace per side

                // set up some event handlers to allow for node-dragging
                that.initMouseHandling()
            },

            redraw:function(){
                //
                // redraw will be called repeatedly during the run whenever the node positions
                // change. the new positions for the nodes can be accessed by looking at the
                // .p attribute of a given node. however the p.x & p.y values are in the coordinates
                // of the particle system rather than the screen. you can either map them to
                // the screen yourself, or use the convenience iterators .eachNode (and .eachEdge)
                // which allow you to step through the actual node objects but also pass an
                // x,y point in the screen's coordinate system
                //
                ctx.fillStyle = "#293040"
                //ctx.fillStyle = "#111";
                ctx.fillRect(0,0, canvas.width, canvas.height)

                particleSystem.eachEdge(function(edge, pt1, pt2){
                    // edge: {source:Node, target:Node, length:#, data:{}}
                    // pt1:  {x:#, y:#}  source position in screen coords
                    // pt2:  {x:#, y:#}  target position in screen coords

                    // draw a line from pt1 to pt2
                    //ctx.strokeStyle = "rgba(255,255,255, .333)";
                    ctx.strokeStyle = edge.strokeStyle;
                    ctx.lineWidth = 1;
                    ctx.beginPath ();
                    ctx.moveTo (pt1.x, pt1.y);
                    ctx.lineTo (pt2.x, pt2.y);
                    ctx.stroke ();
                })

                particleSystem.eachNode(function(node, pt){
                    // node: {mass:#, p:{x,y}, name:"", data:{}}
                    // pt:   {x:#, y:#}  node position in screen coords

                    // draw a rectangle centered at pt
                    var w = (node.fillStyle) ? 14 : 6;
                    ctx.fillStyle = (node.fillStyle) ? node.fillStyle : "black";
                    ctx.fillRect (pt.x-w/2, pt.y-w/2, w,w);
                    ctx.fillStyle = (node.data.myColor) ? node.data.myColor : "black";
                    ctx.font = '13px sans-serif';
                    ctx.fillText (node.name, pt.x+8, pt.y+8);
                })
            },

            initMouseHandling:function(){
                // no-nonsense drag and drop (thanks springy.js)
                var dragged = null;
                var handler = {
                    clicked:function(e){
                        var pos = $(canvas).offset();
                        _mouseP = arbor.Point(e.pageX-pos.left, e.pageY-pos.top)
                        dragged = particleSystem.nearest(_mouseP);
                        $(canvas).bind('mouseup', handler.doubleclicked)

                        return false
                    },
                    doubleclicked:function(e){
                        if (dragged===null || dragged.node===undefined) return
                        if (dragged.node !== null){

                            dragged.node.fixed = false

                            var id=dragged.node.name;
                            getLinks(id,0);
                        }

                        selected = null
                        $(canvas).unbind('click', handler.doubleclicked)
                        _mouseP = null
                        return false
                    }
                }

                $(canvas).mousedown(handler.clicked);

            }

        }
        return that;
    }

    $(document).ready(function(){
        sys.renderer = Renderer("#viewport")
        var canvas = document.getElementById('viewport'),
            context = canvas.getContext('2d');
        window.addEventListener('resize', resizeCanvas, false);

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight-160;
            canvas.marginTop = 80;
            sys.renderer.redraw();
        }
        resizeCanvas();

    })

})(this.jQuery)