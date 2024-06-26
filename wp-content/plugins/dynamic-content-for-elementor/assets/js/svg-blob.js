(function ($) {
    var WidgetElements_SvgBlobHandler = function ($scope, $) {
        var elementSettings = dceGetElementSettings($scope);
        var id_scope = $scope.attr('data-id');
        var numPoints = elementSettings.numPoints.size;
        var minRadius = elementSettings.minmaxRadius.sizes.start;
        var maxRadius = elementSettings.minmaxRadius.sizes.end;
        var minDuration = elementSettings.minmaxDuration.sizes.start;
        var maxDuration = elementSettings.minmaxDuration.sizes.end;
        var is_showPoints = Boolean( elementSettings.show_points ) || false;

        var blob1 = createBlob({
          element: document.querySelector("#path1-"+id_scope),
          numPoints: numPoints,
          centerX: (elementSettings.viewbox_width/2) || 300,
          centerY: (elementSettings.viewbox_height/2) || 300,
          minRadius: minRadius,
          maxRadius: maxRadius,
          minDuration: minDuration,
          maxDuration: maxDuration
        });
        var tensionPoints = elementSettings.tensionPoints.size;

        // To show the points
        if(is_showPoints){
          createDots([blob1]);
        }
        function createBlob(options) {

			var points = [];
			var path = options.element;
			var slice = (Math.PI * 2) / options.numPoints;
			var startAngle = random(Math.PI * 2);

			var tl = gsap.timeline({
				onUpdate: update
			});			

			for (var i = 0; i < options.numPoints; i++) {

			var angle = startAngle + i * slice;
			var duration = random(options.minDuration, options.maxDuration);

			var point = {
				x: options.centerX + Math.cos(angle) * options.minRadius,
				y: options.centerY + Math.sin(angle) * options.minRadius
			};

			var tween = gsap.to(point, {
				duration: duration,
				x: options.centerX + Math.cos(angle) * options.maxRadius,
				y: options.centerY + Math.sin(angle) * options.maxRadius,
				repeat: -1,
				yoyo: true,
				ease: Sine.easeInOut
			});

			tl.add(tween, -random(duration));
			points.push(point);
			}

			options.tl = tl;
			options.points = points;

			function update() {
			path.setAttribute("d", cardinal(points, true, tensionPoints));
			}

			return options;
        }

        // Cardinal spline - a uniform Catmull-Rom spline with a tension option
        function cardinal(data, closed, tension) {

			if (data.length < 1) return "M0 0";
			if (tension == null) tension = 1;

			var size = data.length - (closed ? 0 : 1);
			var path = "M" + data[0].x + " " + data[0].y + " C";

			for (var i = 0; i < size; i++) {

				var p0, p1, p2, p3;

				if (closed) {
					p0 = data[(i - 1 + size) % size];
					p1 = data[i];
					p2 = data[(i + 1) % size];
					p3 = data[(i + 2) % size];

				} else {
					p0 = i == 0 ? data[0] : data[i - 1];
					p1 = data[i];
					p2 = data[i + 1];
					p3 = i == size - 1 ? p2 : data[i + 2];
				}

				var x1 = p1.x + (p2.x - p0.x) / 6 * tension;
				var y1 = p1.y + (p2.y - p0.y) / 6 * tension;

				var x2 = p2.x - (p3.x - p1.x) / 6 * tension;
				var y2 = p2.y - (p3.y - p1.y) / 6 * tension;

				path += " " + x1 + " " + y1 + " " + x2 + " " + y2 + " " + p2.x + " " + p2.y;
			}

			return closed ? path + "z" : path;
        }

        function random(min, max) {
			if (max == null) { max = min; min = 0; }
			if (min > max) { var tmp = min; min = max; max = tmp; }
			return min + (max - min) * Math.random();
        }

        function createDots(blobs) {

          var dotContainer = $scope.find("#dot-container")[0];
          var showPoints = true;
          var points = [];
          var dots = [];

          blobs.forEach(function(blob) {
            blob.points.forEach(function(point) {

              var dot = document.createElementNS("http://www.w3.org/2000/svg", "circle");
              dot.setAttribute("r", elementSettings.dot_r.size);
              dot.setAttribute("fill", elementSettings.dot_color);
              dot.setAttribute("class", "dot");
              dotContainer.appendChild(dot);
              dots.push(dot);
              points.push(point);
            });
          });

          if(is_showPoints){
			onChange();
			gsap.ticker.add(update);
          }
          function onChange() {
            if(is_showPoints){
              showPoints = true;
            }
            else{
              showPoints = false;
            }
            dotContainer.style.setProperty("visibility", showPoints ? "visible" : "hidden");
          }

          function update() {

            if (!showPoints) {
              return;
            }

            for (var i = 0; i < points.length; i++) {
              var p = points[i];
              dots[i].setAttribute("transform", "translate(" + p.x + "," + p.y + ")");
            }
          }
        }

    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/dyncontel-svgblob.default', WidgetElements_SvgBlobHandler);
    });
})(jQuery);
