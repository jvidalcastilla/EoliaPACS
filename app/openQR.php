<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$aStudy=filter_var($_GET['id'],FILTER_SANITIZE_STRING);
//$aStudy = '1317ae50-2539e436-e791c09b-5dc6f37a-6a49cf53';
$pac_name = "";
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="libro.png" sizes="16x16">
    <title>Diagn&oacutestico por Im&aacutegenes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>        
    <link rel="stylesheet" href="../css/medisur.css">
</head>

<body translate="no" style="user-select: none;">

    <style>
        body { background: #000; margin:1em; text-align:center; }
        canvas { display:block; 
                 margin:1em auto; 
                 background:#000; 
                 border:1px solid darkgoldenrod;}
        overlay {
            z-index: 9;
            margin: 30px;

        }
        @media print {
            canvas {
                width: 1000px;
                margin-left: 10px;
            }
            canv_container    {
                width: 100%;
            }
            
            
        }
        
     
    </style>
    <div class="container-fluid">    
        <div class="row no-gutters d-flex flex-nowrap">    

            <div class="col-4 d-print-none">    

                <div class="row d-print-none">
                    <div class="col-1">
                        <label for='slider_brillo' style="color:white">Brillo:</label>
                    </div>
                    <div class="col-sm-1 m-auto">
                    <input id="slider_brillo" type="range" min="0" max="2" step="0.1" oninput="var_contraste()" value="1" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-1">
                    <label for='slider_contraste' style="color:white">Contraste:</label>
                    </div>
                    <div class="col-sm-1 m-auto">
                    <input id="slider_contraste" type="range" min="0" max="3" step="0.1" oninput="var_contraste()" value="1" />
                    </div>
                </div>
                <?php
                include './PACSHelper.php';
                $series = getStudyDetails($aStudy);
                //echo var_dump($series);
                $i = 0;
                echo "<div class='container d-print-none' style='border:1px solid darkgoldenrod;'>";
                
                foreach ($series as $unaSerie) {
                    $justClose=false;
                    $pac_name = $unaSerie->patientName;
                    $pac_id = $unaSerie->patientID;
                    $st_date=$unaSerie->studyDate;
                    if (($i % 3) == 0) {
                        echo "<div class='row d-print-none'>";
                    }
                    echo "<div class='col-sm-4 d-print-none p-0 m-0'>";
                    echo "<img src='data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=' data-src='" . $unaSerie->fileName . "' id='imagen_" . $i . "' onclick='imageSelected(" . $i++ . ")' alt='" . $unaSerie->bodyPart . "' height='96' width='96'>";
                    echo "</div>";
                    if (($i % 3) == 0) {
                        $justClose=true;
                        echo "</div>";
                    }
                }
                if (!$justClose){
                        echo "</div>";
                }
                ?>
            </div>
            </div>
            <div class="col-8 p-0 m-0 canv_container">    
                <div class ="d-flex pl-3 pt-0 pr-4">
                    <div class="mr-auto p-2" style="color:goldenrod"><?php echo "ID:" .$pac_id."  ". $pac_name." FECHA:".$st_date; ?></div>
                    <div class="p-2" style="color:goldenrod" id="study_desc"></div>
                </div>
                <div class ="container-fluid" >
                
                <canvas width="760" height="540"></canvas>
                  
                </div>
            </div>    
        </div>    


    <script>
    
  
    
    function imageSelected(i) {
            //alert ("imagen " + i);
            var canvas = document.getElementsByTagName('canvas')[0];
            var ctx = canvas.getContext('2d');
            var img = document.getElementById("imagen_" + i);
            gkhead = img;
            var hRatio = canvas.width / img.naturalWidth;
            var vRatio = canvas.height / img.naturalHeight;
            var ratio = Math.min(hRatio, vRatio);
            //alert ("w:"+canvas.width + " img:" + img.naturalwidth + " ratio " + hRatio);
            //tenia 0.25
            // Los ultimos dos valores son desplaz x, y 
            var hPos = img.naturalWidth * ratio;
            hPos=canvas.width- hPos;
            if (hPos>0){ hPos=hPos/2;}

            ctx.setTransform(ratio, 0, 0, ratio, hPos, 0);
            ctx.filter = "";
            cargarImagen();
            
            var studyDesc= document.getElementById("study_desc");
            studyDesc.innerHTML=img.alt;
            
            // ctx.restore();


        }
        function var_contraste() {
            var contraste = document.getElementById("slider_contraste").value;
            var brillo = document.getElementById("slider_brillo").value;
            var canvas = document.getElementsByTagName('canvas')[0];
            var ctx = canvas.getContext('2d');
            var filtro = "contrast(" + contraste + ") brightness(" + brillo + ")";
            ctx.filter = filtro;
            //app.filters.contrast(contraste*100);
            
            ctx.drawImage(gkhead, 0, 0);
        }


    </script>

    <script id="rendered-js">
        var init = 0;
        var canvas = document.getElementsByTagName('canvas')[0];
        var gkhead = new Image();


        function redraw() {

            // Clear the entire canvas
            var ctx = canvas.getContext('2d');
            var p1 = ctx.transformedPoint(0, 0);
            var p2 = ctx.transformedPoint(canvas.width, canvas.height);
            ctx.clearRect(p1.x, p1.y, p2.x - p1.x, p2.y - p1.y);

            ctx.save();
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            //var contraste = document.getElementById("slider_contraste").value;
            
            
            ctx.restore();

            ctx.drawImage(gkhead, 0, 0);
            //app.filters.contrast(contraste*100);
            
        }

        function  cargarImagen() {

            var ctx = canvas.getContext('2d');
            trackTransforms(ctx);

            redraw();


            var lastX = canvas.width / 2, lastY = canvas.height / 2;
            //var lastX = 0, lastY = 0;
            var dragStart, dragged;

           

            var scaleFactor = 1.1;
            


            if (init == 0) {
                
                var zoom = function (clicks) {
                    var pt = ctx.transformedPoint(lastX, lastY);
                    ctx.translate(pt.x, pt.y);
                    var factor = Math.pow(scaleFactor, clicks);
                    ctx.scale(factor, factor);
                    ctx.translate(-pt.x, -pt.y);
                    redraw();
                };

                var handleScroll = function (evt) {
                    var delta = evt.wheelDelta ? evt.wheelDelta / 40 : evt.detail ? -evt.detail : 0;
                    if (delta)
                        zoom(delta);
                    return evt.preventDefault() && false;
                };
                 canvas.addEventListener('mousedown', function (evt) {
                   document.body.style.mozUserSelect = document.body.style.webkitUserSelect = document.body.style.userSelect = 'none';
                   lastX = evt.offsetX || evt.pageX - canvas.offsetLeft;
                   lastY = evt.offsetY || evt.pageY - canvas.offsetTop;
                   dragStart = ctx.transformedPoint(lastX, lastY);
                   dragged = false;
               }, false);

                canvas.addEventListener('mousemove', function (evt) {
                    lastX = evt.offsetX || evt.pageX - canvas.offsetLeft;
                    lastY = evt.offsetY || evt.pageY - canvas.offsetTop;
                    dragged = true;
                    if (dragStart) {
                        var pt = ctx.transformedPoint(lastX, lastY);
                        ctx.translate(pt.x - dragStart.x, pt.y - dragStart.y);
                        redraw();
                    }
                }, false);

                canvas.addEventListener('mouseup', function (evt) {
                    dragStart = null;
                    if (!dragged)
                        zoom(evt.shiftKey ? -1 : 1);
                }, false);

                
                canvas.addEventListener('DOMMouseScroll', handleScroll, false);
                canvas.addEventListener('mousewheel', handleScroll, false);
                init = 1;
            }
        }
        ;



//gkhead.src = $_GET['file'];

//initImage();

// Adds ctx.getTransform() - returns an SVGMatrix
// Adds ctx.transformedPoint(x,y) - returns an SVGPoint
        function trackTransforms(ctx) {
            var svg = document.createElementNS("http://www.w3.org/2000/svg", 'svg');
            var xform = svg.createSVGMatrix();
            ctx.getTransform = function () {
                return xform;
            };

            var savedTransforms = [];
            var save = ctx.save;
            ctx.save = function () {
                savedTransforms.push(xform.translate(0, 0));
                return save.call(ctx);
            };

            var restore = ctx.restore;
            ctx.restore = function () {
                xform = savedTransforms.pop();
                return restore.call(ctx);
            };

            var scale = ctx.scale;
            ctx.scale = function (sx, sy) {
                xform = xform.scaleNonUniform(sx, sy);
                return scale.call(ctx, sx, sy);
            };

            var rotate = ctx.rotate;
            ctx.rotate = function (radians) {
                xform = xform.rotate(radians * 180 / Math.PI);
                return rotate.call(ctx, radians);
            };

            var translate = ctx.translate;
            ctx.translate = function (dx, dy) {
                xform = xform.translate(dx, dy);
                return translate.call(ctx, dx, dy);
            };

            var transform = ctx.transform;
            ctx.transform = function (a, b, c, d, e, f) {
                var m2 = svg.createSVGMatrix();
                m2.a = a;
                m2.b = b;
                m2.c = c;
                m2.d = d;
                m2.e = e;
                m2.f = f;
                xform = xform.multiply(m2);
                return transform.call(ctx, a, b, c, d, e, f);
            };

            var setTransform = ctx.setTransform;
            ctx.setTransform = function (a, b, c, d, e, f) {
                xform.a = a;
                xform.b = b;
                xform.c = c;
                xform.d = d;
                xform.e = e;
                xform.f = f;
                return setTransform.call(ctx, a, b, c, d, e, f);
            };

            var pt = svg.createSVGPoint();
            ctx.transformedPoint = function (x, y) {
                pt.x = x;
                pt.y = y;
                return pt.matrixTransform(xform.inverse());
            };
        }
//# sourceURL=pen.js


    </script>

<script>
function initImages() {
    var imgDefer = document.getElementsByTagName('img');
    for (var i=0; i<imgDefer.length; i++) {
    if(imgDefer[i].getAttribute('data-src')) {
    imgDefer[i].setAttribute('src',imgDefer[i].getAttribute('data-src'));
    } } }
    window.onload = initImages;
</script>


 <script>
            var app = ( function () {
 
                 var canvas = document.getElementsByTagName('canvas')[0],
                    context = canvas.getContext( '2d' ),
 
                    // API
                    public = {};
 
                // Public methods goes here...
                public.loadPicture = function () {
                    var imageObj = new Image();
                    imageObj.src = 'entropy.jpg';
 
                    imageObj.onload = function () {
                        context.drawImage( imageObj, 0, 0 );
                    }
                };
 
                public.getImgData = function () {
                    return context.getImageData( 0, 0, canvas.width, canvas.height );
                };
 
 
                // Filters
                public.filters = {};
 
                public.filters.bw = function () {
                    var imageData = app.getImgData(),
                        pixels = imageData.data,
                        numPixels = imageData.width * imageData.height;
 
                    for ( var i = 0; i < numPixels; i++ ) {
                        var r = pixels[ i * 4 ];
                        var g = pixels[ i * 4 + 1 ];
                        var b = pixels[ i * 4 + 2 ];
 
                        var grey = ( r + g + b ) / 3;
 
                        pixels[ i * 4 ] = grey;
                        pixels[ i * 4 + 1 ] = grey;
                        pixels[ i * 4 + 2 ] = grey;
                    }
 
                    context.putImageData( imageData, 0, 0 );
                };
 
 
                public.filters.invert = function () {
                    var imageData = app.getImgData(),
                        pixels = imageData.data,
                        numPixels = imageData.width * imageData.height;
 
                    for ( var i = 0; i < numPixels; i++ ) {
                        var r = pixels[ i * 4 ];
                        var g = pixels[ i * 4 + 1 ];
                        var b = pixels[ i * 4 + 2 ];
 
                        pixels[ i * 4 ] = 255 - r;
                        pixels[ i * 4 + 1 ] = 255 - g;
                        pixels[ i * 4 + 2 ] = 255 - b;
                    }
 
                    context.putImageData( imageData, 0, 0 );
                };
 
                public.filters.sepia = function () {
                    var imageData = app.getImgData(),
                        pixels = imageData.data,
                        numPixels = imageData.width * imageData.height;
 
                    for ( var i = 0; i < numPixels; i++ ) {
                        var r = pixels[ i * 4 ];
                        var g = pixels[ i * 4 + 1 ];
                        var b = pixels[ i * 4 + 2 ];
 
                        pixels[ i * 4 ] = 255 - r;
                        pixels[ i * 4 + 1 ] = 255 - g;
                        pixels[ i * 4 + 2 ] = 255 - b;
 
                        pixels[ i * 4 ] = ( r * .393 ) + ( g *.769 ) + ( b * .189 );
                        pixels[ i * 4 + 1 ] = ( r * .349 ) + ( g *.686 ) + ( b * .168 );
                        pixels[ i * 4 + 2 ] = ( r * .272 ) + ( g *.534 ) + ( b * .131 );
                    }
 
                    context.putImageData( imageData, 0, 0 );
                };
 
 
                public.filters.contrast = function ( contrast ) {
                    var imageData = app.getImgData(),
                        pixels = imageData.data,
                        numPixels = imageData.width * imageData.height,
                        factor;
 
                    contrast || ( contrast = 100 ); // Default value
 
                    factor = ( 259 * ( contrast + 255 ) ) / ( 255 * ( 259 - contrast ) );
 
                    for ( var i = 0; i < numPixels; i++ ) {
                        var r = pixels[ i * 4 ];
                        var g = pixels[ i * 4 + 1 ];
                        var b = pixels[ i * 4 + 2 ];
 
                        pixels[ i * 4 ] = factor * ( r - 128 ) + 128;
                        pixels[ i * 4 + 1 ] = factor * ( g - 128 ) + 128;
                        pixels[ i * 4 + 2 ] = factor * ( b - 128 ) + 128;
                    }
 
                    context.putImageData( imageData, 0, 0 );
                };
 
                public.save = function () {
                    var link = window.document.createElement( 'a' ),
                        url = canvas.toDataURL(),
                        filename = 'screenshot.jpg';
 
                    link.setAttribute( 'href', url );
                    link.setAttribute( 'download', filename );
                    link.style.visibility = 'hidden';
                    window.document.body.appendChild( link );
                    link.click();
                    window.document.body.removeChild( link );
                };
 
                return public;
            } () );
        </script>

    
</body>