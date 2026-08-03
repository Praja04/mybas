/**
 * FauziLineChart — Custom Line Chart tanpa library eksternal (Canvas-based)
 * Fitur: multi-series, data labels dgn collision detection, legend, tooltip, year bands, HiDPI
 */
(function () {
    'use strict';

    var YEAR_BAND_COLORS = [
        'rgba(220, 232, 255, 0.50)',
        'rgba(255, 243, 205, 0.50)',
        'rgba(212, 237, 218, 0.50)',
        'rgba(248, 215, 218, 0.50)',
    ];

    var MARGIN = { top: 35, right: 20, bottom: 75, left: 70 };

    function FauziLineChart(options) {
        var self = this;
        if (!options || !options.container) return;

        var container = typeof options.container === 'string'
            ? document.querySelector(options.container)
            : options.container;
        if (!container) return;

        self._opts = options;
        self._container = container;
        self._canvas = null;
        self._ctx = null;
        self._dpr = window.devicePixelRatio || 1;
        self._tooltipEl = null;
        self._resizeObserver = null;
        self._hoveredPoint = null;
        self._dataLabelPositions = [];

        self._init();
    }

    FauziLineChart.prototype._init = function () {
        var self = this;
        var container = self._container;

        container.innerHTML = '';
        container.style.position = 'relative';

        var canvas = document.createElement('canvas');
        canvas.style.display = 'block';
        canvas.style.width = '100%';
        canvas.style.height = (self._opts.height || 320) + 'px';
        canvas.style.cursor = 'default';
        container.appendChild(canvas);

        self._canvas = canvas;
        self._ctx = canvas.getContext('2d');

        var tooltip = document.createElement('div');
        tooltip.className = 'flc-tooltip';
        tooltip.style.cssText = 'position:absolute;display:none;pointer-events:none;z-index:100;'
            + 'background:rgba(0,0,0,0.80);color:#fff;padding:6px 10px;border-radius:4px;'
            + 'font-size:16px;line-height:1.4;white-space:nowrap;';
        container.appendChild(tooltip);
        self._tooltipEl = tooltip;

        self._bindEvents();
        self._setupResize();
        self.draw();
    };

    FauziLineChart.prototype._setupResize = function () {
        var self = this;
        if (typeof ResizeObserver !== 'undefined') {
            self._resizeObserver = new ResizeObserver(function () {
                self.draw();
            });
            self._resizeObserver.observe(self._container);
        } else {
            var timer = null;
            var onResize = function () {
                if (timer) clearTimeout(timer);
                timer = setTimeout(function () { self.draw(); }, 150);
            };
            window.addEventListener('resize', onResize);
            self._resizeCleanup = function () {
                window.removeEventListener('resize', onResize);
            };
        }
    };

    FauziLineChart.prototype._bindEvents = function () {
        var self = this;
        var canvas = self._canvas;

        canvas.addEventListener('mousemove', function (e) {
            self._handleMouseMove(e);
        });

        canvas.addEventListener('mouseleave', function () {
            self._hideTooltip();
            self._hoveredPoint = null;
            self.draw();
        });
    };

    FauziLineChart.prototype._handleMouseMove = function (e) {
        var self = this;
        var rect = self._canvas.getBoundingClientRect();
        var scaleX = self._canvas.width / (self._dpr * rect.width);
        var scaleY = self._canvas.height / (self._dpr * rect.height);
        var mouseX = (e.clientX - rect.left) * scaleX;
        var mouseY = (e.clientY - rect.top) * scaleY;

        var layout = self._layout;
        if (!layout) return;

        var nearest = self._findNearestPoint(mouseX, mouseY, layout);
        if (nearest) {
            if (self._hoveredPoint !== nearest) {
                self._hoveredPoint = nearest;
                self._showTooltip(nearest, e);
                self.draw();
            }
        } else {
            if (self._hoveredPoint !== null) {
                self._hideTooltip();
                self._hoveredPoint = null;
                self.draw();
            }
        }
    };

    FauziLineChart.prototype._findNearestPoint = function (mx, my, layout) {
        var self = this;
        var threshold = 16;
        var nearest = null;
        var minDist = Infinity;

        var seriesArr = self._opts.series || [];
        var xLabels = self._opts.xLabels || [];
        var n = self._pointCount;
        var yScale = layout.yScale;

        for (var si = 0; si < seriesArr.length; si++) {
            var data = seriesArr[si].data || [];
            for (var i = 0; i < n && i < data.length; i++) {
                var px = layout.plotLeft + layout.stepX * i + layout.stepX / 2;
                var py = layout.plotTop + (1 - data[i] / yScale) * layout.plotHeight;
                var dx = mx - px;
                var dy = my - py;
                var dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < minDist && dist < threshold) {
                    minDist = dist;
                    nearest = {
                        seriesIdx: si,
                        pointIdx: i,
                        seriesName: seriesArr[si].name,
                        seriesColor: seriesArr[si].color,
                        value: data[i],
                        px: px,
                        py: py,
                    };
                }
            }
        }
        return nearest;
    };

    FauziLineChart.prototype._showTooltip = function (point, e) {
        var self = this;
        var rect = self._canvas.getBoundingClientRect();
        var el = self._tooltipEl;

        var monthStr = (self._opts.xLabels || [])[point.pointIdx] || '';
        var unit = self._opts.valueUnit || '';
        var html = '<b>' + monthStr + '</b><br/>'
            + '<span style="color:' + point.seriesColor + '">●</span> '
            + '<b>' + point.seriesName + '</b>: ' + point.value + ' ' + unit;

        el.innerHTML = html;
        el.style.display = 'block';

        var left = e.clientX - rect.left + 12;
        var top = e.clientY - rect.top - 10;

        if (left + 160 > rect.width) left = e.clientX - rect.left - 160;
        if (top < 0) top = e.clientY - rect.top + 20;

        el.style.left = left + 'px';
        el.style.top = top + 'px';
    };

    FauziLineChart.prototype._hideTooltip = function () {
        if (this._tooltipEl) {
            this._tooltipEl.style.display = 'none';
        }
    };

    FauziLineChart.prototype._computeLayout = function () {
        var self = this;
        var canvas = self._canvas;
        var dpr = self._dpr;
        var w = canvas.clientWidth;
        var h = self._opts.height || 320;

        canvas.width = w * dpr;
        canvas.height = h * dpr;

        var plotLeft = MARGIN.left;
        var plotTop = MARGIN.top;
        var plotWidth = w - MARGIN.left - MARGIN.right;
        var plotHeight = h - MARGIN.top - MARGIN.bottom;
        var plotBottom = plotTop + plotHeight;

        var xLabels = self._opts.xLabels || [];
        var n = xLabels.length;
        var stepX = n > 1 ? plotWidth / (n - 1) : plotWidth;

        var seriesArr = self._opts.series || [];
        var maxVal = 0;
        for (var si = 0; si < seriesArr.length; si++) {
            var data = seriesArr[si].data || [];
            for (var i = 0; i < data.length; i++) {
                if (data[i] > maxVal) maxVal = data[i];
            }
        }
        if (maxVal === 0) maxVal = 10;
        maxVal = maxVal * 1.15;

        var niceMax = self._niceY(maxVal);
        var yTickCount = Math.min(6, Math.max(3, Math.floor(niceMax / 5)));
        var yTickStep = Math.ceil(niceMax / yTickCount);
        niceMax = yTickStep * yTickCount;

        self._layout = {
            w: w, h: h,
            plotLeft: plotLeft,
            plotTop: plotTop,
            plotWidth: plotWidth,
            plotHeight: plotHeight,
            plotBottom: plotBottom,
            stepX: stepX,
            yScale: niceMax,
            yTickStep: yTickStep,
            yTickCount: yTickCount,
        };
        self._pointCount = n;
    };

    FauziLineChart.prototype._niceY = function (val) {
        if (val <= 0) return 10;
        var mag = Math.pow(10, Math.floor(Math.log10(val)));
        var norm = val / mag;
        if (norm <= 1.5) return Math.ceil(val / (mag * 0.5)) * mag * 0.5;
        if (norm <= 3) return Math.ceil(val / mag) * mag;
        if (norm <= 7) return Math.ceil(val / (mag * 2)) * mag * 2;
        return Math.ceil(val / (mag * 5)) * mag * 5;
    };

    FauziLineChart.prototype._toPlotX = function (i) {
        var layout = this._layout;
        return layout.plotLeft + (i / Math.max(1, this._pointCount - 1)) * layout.plotWidth;
    };

    FauziLineChart.prototype._toPlotY = function (val) {
        var layout = this._layout;
        return layout.plotTop + (1 - val / layout.yScale) * layout.plotHeight;
    };

    FauziLineChart.prototype.draw = function () {
        var self = this;
        var ctx = self._ctx;
        var dpr = self._dpr;

        self._computeLayout();
        var layout = self._layout;
        if (!layout) return;

        ctx.save();
        ctx.scale(dpr, dpr);

        // Background
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, layout.w, layout.h);

        self._drawYearBands(ctx, layout);
        self._drawGridlines(ctx, layout);
        self._drawAxes(ctx, layout);
        self._drawSeries(ctx, layout);
        self._drawMarkers(ctx, layout);
        self._drawDataLabels(ctx, layout);
        self._drawLegend(ctx, layout);

        ctx.restore();
    };

    FauziLineChart.prototype._drawYearBands = function (ctx, layout) {
        var self = this;
        var months = self._opts.months || self._opts.xLabels || [];
        if (months.length === 0) return;

        var bounds = self._computeYearBounds(months);
        if (bounds.length === 0) return;

        // Precompute midpoints between years (pixel X between last month of year A and first of year B)
        var midpoints = [];
        for (var j = 1; j < bounds.length; j++) {
            var lastOfPrev = bounds[j - 1].toIdx - 1;
            var firstOfNext = bounds[j].fromIdx;
            var midX = (self._toPlotX(lastOfPrev) + self._toPlotX(firstOfNext)) / 2;
            midpoints.push(midX);
        }

        for (var i = 0; i < bounds.length; i++) {
            var band = bounds[i];
            var x1, x2;

            if (i === 0) {
                x1 = self._toPlotX(band.fromIdx);
            } else {
                x1 = midpoints[i - 1];
            }

            if (i < bounds.length - 1) {
                x2 = midpoints[i];
            } else {
                x2 = band.toIdx < self._pointCount
                    ? self._toPlotX(band.toIdx)
                    : layout.plotLeft + layout.plotWidth;
            }

            var w = x2 - x1;

            ctx.fillStyle = YEAR_BAND_COLORS[i % YEAR_BAND_COLORS.length];
            ctx.fillRect(x1, layout.plotTop, w, layout.plotHeight);

            ctx.fillStyle = '#333';
            ctx.font = 'bold 16px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            ctx.fillText(String(band.year), x1 + w / 2, layout.plotBottom + 40);
        }

        for (var k = 0; k < midpoints.length; k++) {
            var mx = midpoints[k];
            ctx.strokeStyle = '#999';
            ctx.lineWidth = 1;
            ctx.setLineDash([4, 4]);
            ctx.beginPath();
            ctx.moveTo(mx, layout.plotTop);
            ctx.lineTo(mx, layout.plotBottom);
            ctx.stroke();
            ctx.setLineDash([]);
        }
    };

    FauziLineChart.prototype._computeYearBounds = function (months) {
        var self = this;
        var n = self._pointCount;

        var yearSet = {};
        for (var i = 0; i < months.length; i++) {
            var m = String(months[i]);
            var y = parseInt(m.substring(0, 4), 10);
            if (!isNaN(y)) yearSet[y] = true;
        }
        var years = Object.keys(yearSet).map(Number).sort(function (a, b) { return a - b; });
        if (years.length === 0) return [];

        var bounds = [];
        for (var yi = 0; yi < years.length; yi++) {
            var year = years[yi];
            var fromIdx = -1;
            var toIdx = -1;
            for (var i = 0; i < n; i++) {
                var s = String(months[i]);
                if (parseInt(s.substring(0, 4), 10) === year) {
                    if (fromIdx < 0) fromIdx = i;
                    toIdx = i + 1;
                }
            }
            if (fromIdx >= 0 && toIdx > fromIdx) {
                bounds.push({ year: year, fromIdx: fromIdx, toIdx: toIdx });
            }
        }
        return bounds;
    };

    FauziLineChart.prototype._drawGridlines = function (ctx, layout) {
        ctx.strokeStyle = '#e0e0e0';
        ctx.lineWidth = 0.5;

        for (var t = 0; t <= layout.yTickCount; t++) {
            var y = layout.plotTop + (t / layout.yTickCount) * layout.plotHeight;
            ctx.beginPath();
            ctx.moveTo(layout.plotLeft, y);
            ctx.lineTo(layout.plotLeft + layout.plotWidth, y);
            ctx.stroke();
        }
    };

    FauziLineChart.prototype._drawAxes = function (ctx, layout) {
        var self = this;
        // Y axis
        var yTickVal;
        ctx.fillStyle = '#666';
        ctx.font = '16px sans-serif';
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';

        for (var t = 0; t <= layout.yTickCount; t++) {
            yTickVal = t * layout.yTickStep;
            var y = layout.plotTop + (1 - yTickVal / layout.yScale) * layout.plotHeight;
            ctx.fillText(String(Math.round(yTickVal)), layout.plotLeft - 8, y);
        }

        // Y axis title
        ctx.save();
        ctx.translate(12, layout.plotTop + layout.plotHeight / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.fillStyle = '#333';
        ctx.font = '16px sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(self._opts.yAxisTitle || '', 0, 0);
        ctx.restore();

        // X axis labels
        var xLabels = self._opts.xLabels || [];
        ctx.fillStyle = '#666';
        ctx.font = '16px sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';

        for (var i = 0; i < xLabels.length; i++) {
            var x = self._toPlotX(i);
            ctx.fillText(xLabels[i], x, layout.plotBottom + 5);
        }

        // Axis lines
        ctx.strokeStyle = '#ccc';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(layout.plotLeft, layout.plotTop);
        ctx.lineTo(layout.plotLeft, layout.plotBottom);
        ctx.lineTo(layout.plotLeft + layout.plotWidth, layout.plotBottom);
        ctx.stroke();
    };

    FauziLineChart.prototype._drawSeries = function (ctx, layout) {
        var self = this;
        var seriesArr = self._opts.series || [];
        var n = self._pointCount;

        for (var si = 0; si < seriesArr.length; si++) {
            var s = seriesArr[si];
            var data = s.data || [];
            if (data.length === 0) continue;

            ctx.strokeStyle = s.color;
            ctx.lineWidth = 2.5;
            ctx.lineJoin = 'round';
            ctx.lineCap = 'round';
            ctx.beginPath();

            var started = false;
            for (var i = 0; i < n && i < data.length; i++) {
                var px = self._toPlotX(i);
                var py = self._toPlotY(data[i]);
                if (started) {
                    ctx.lineTo(px, py);
                } else {
                    ctx.moveTo(px, py);
                    started = true;
                }
            }
            ctx.stroke();
        }
    };

    FauziLineChart.prototype._drawMarkers = function (ctx, layout) {
        var self = this;
        var seriesArr = self._opts.series || [];
        var n = self._pointCount;
        var r = 4;

        for (var si = 0; si < seriesArr.length; si++) {
            var s = seriesArr[si];
            var data = s.data || [];

            for (var i = 0; i < n && i < data.length; i++) {
                var px = self._toPlotX(i);
                var py = self._toPlotY(data[i]);

                // Hover highlight
                if (self._hoveredPoint && self._hoveredPoint.seriesIdx === si && self._hoveredPoint.pointIdx === i) {
                    ctx.beginPath();
                    ctx.arc(px, py, r + 3, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(0,0,0,0.12)';
                    ctx.fill();
                }

                // White border
                ctx.beginPath();
                ctx.arc(px, py, r + 2, 0, Math.PI * 2);
                ctx.fillStyle = '#fff';
                ctx.fill();

                // Series color fill
                ctx.beginPath();
                ctx.arc(px, py, r, 0, Math.PI * 2);
                ctx.fillStyle = s.color;
                ctx.fill();
            }
        }
    };

    FauziLineChart.prototype._drawDataLabels = function (ctx, layout) {
        var self = this;
        var seriesArr = self._opts.series || [];
        var n = self._pointCount;

        var allLabels = [];

        for (var i = 0; i < n; i++) {
            var labelsAtX = [];

            for (var si = 0; si < seriesArr.length; si++) {
                var s = seriesArr[si];
                var data = s.data || [];
                if (i >= data.length) continue;

                var val = Math.round(data[i]);
                var text = String(val);
                var px = self._toPlotX(i);
                var py = self._toPlotY(data[i]);

                var fontSize = 16;
                ctx.font = '600 ' + fontSize + 'px sans-serif';
                var textWidth = ctx.measureText(text).width;
                var textHeight = fontSize + 4;

                var labelY = py - 8 - textHeight;

                labelsAtX.push({
                    seriesIdx: si,
                    seriesName: s.name,
                    color: s.color,
                    text: text,
                    value: data[i],
                    px: px,
                    py: py,
                    width: textWidth,
                    height: textHeight,
                    x: px - textWidth / 2,
                    y: labelY,
                });
            }

            self._resolveLabelCollisions(labelsAtX);

            for (var li = 0; li < labelsAtX.length; li++) {
                var lbl = labelsAtX[li];
                allLabels.push(lbl);

                // White background for readability
                var pad = 2;
                ctx.fillStyle = 'rgba(255,255,255,0.85)';
                ctx.fillRect(
                    lbl.x - pad,
                    lbl.y - pad,
                    lbl.width + pad * 2,
                    lbl.height + pad * 2
                );

                // Text outline
                ctx.fillStyle = lbl.color;
                ctx.font = '600 16px sans-serif';
                ctx.textAlign = 'left';
                ctx.textBaseline = 'top';
                ctx.shadowColor = '#fff';
                ctx.shadowBlur = 2;
                ctx.fillText(lbl.text, lbl.x, lbl.y);
                ctx.shadowBlur = 0;
            }
        }

        self._dataLabelPositions = allLabels;
    };

    FauziLineChart.prototype._resolveLabelCollisions = function (labels) {
        if (labels.length < 2) return;

        var n = labels.length;
        var adj = new Array(n);
        for (var i = 0; i < n; i++) adj[i] = [];

        for (var i = 0; i < n; i++) {
            for (var j = i + 1; j < n; j++) {
                var a = labels[i];
                var b = labels[j];
                var overlapY = Math.abs((a.y + a.height / 2) - (b.y + b.height / 2))
                    < (a.height + b.height) / 2;
                var overlapX = Math.abs(a.px - b.px) < Math.max(a.width, b.width);
                if (overlapY) {
                    adj[i].push(j);
                    adj[j].push(i);
                }
            }
        }

        var visited = new Array(n);
        for (var i = 0; i < n; i++) visited[i] = false;
        var groups = [];

        for (var i = 0; i < n; i++) {
            if (visited[i]) continue;
            var group = [];
            var stack = [i];
            visited[i] = true;
            while (stack.length > 0) {
                var curr = stack.pop();
                group.push(labels[curr]);
                for (var ni = 0; ni < adj[curr].length; ni++) {
                    var nb = adj[curr][ni];
                    if (!visited[nb]) {
                        visited[nb] = true;
                        stack.push(nb);
                    }
                }
            }
            groups.push(group);
        }

        var MIN_GAP = 4;

        for (var g = 0; g < groups.length; g++) {
            var group = groups[g];
            if (group.length < 2) continue;

            group.sort(function (a, b) { return a.value - b.value; });

            for (var k = 1; k < group.length; k++) {
                var prev = group[k - 1];
                var curr = group[k];
                var neededY = prev.y - prev.height - MIN_GAP;

                if (curr.y > neededY) {
                    var offset = neededY - curr.y;
                    curr.y += offset;
                }
            }
        }
    };

    FauziLineChart.prototype._drawLegend = function (ctx, layout) {
        var self = this;
        var seriesArr = self._opts.series || [];
        if (seriesArr.length === 0) return;

        ctx.font = '600 16px sans-serif';
        ctx.textBaseline = 'middle';
        ctx.textAlign = 'left';

        var totalWidth = 0;
        var items = [];

        for (var si = 0; si < seriesArr.length; si++) {
            var s = seriesArr[si];
            var tw = ctx.measureText(s.name).width;
            items.push({ color: s.color, name: s.name, width: tw });
            totalWidth += tw + 28;
        }
        totalWidth += 8 * (items.length - 1);

        var startX = layout.plotLeft + (layout.plotWidth - totalWidth) / 2;
        if (startX < layout.plotLeft) startX = layout.plotLeft;
        var legendY = 8;

        ctx.fillStyle = 'rgba(255,255,255,0.9)';
        ctx.fillRect(startX - 10, legendY - 4, totalWidth + 20, 22);

        var x = startX;
        for (var si = 0; si < items.length; si++) {
            var item = items[si];

            // Colored dot
            ctx.beginPath();
            ctx.arc(x + 5, legendY + 7, 5, 0, Math.PI * 2);
            ctx.fillStyle = item.color;
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 1.5;
            ctx.stroke();

            // Text
            ctx.fillStyle = '#333';
            ctx.fillText(item.name, x + 14, legendY + 7);

            x += item.width + 28 + 8;
        }
    };

    FauziLineChart.prototype.destroy = function () {
        var self = this;
        if (self._resizeObserver) {
            self._resizeObserver.disconnect();
            self._resizeObserver = null;
        }
        if (self._resizeCleanup) {
            self._resizeCleanup();
            self._resizeCleanup = null;
        }
        if (self._tooltipEl && self._tooltipEl.parentNode) {
            self._tooltipEl.parentNode.removeChild(self._tooltipEl);
            self._tooltipEl = null;
        }
        self._container.innerHTML = '';
        self._canvas = null;
        self._ctx = null;
    };

    window.FauziLineChart = FauziLineChart;
})();
