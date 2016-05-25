joint.dia.Paper.prototype.toSVG = function (opt) {
    opt = opt || {
        };
    var viewportTransform = V(this.viewport).attr('transform');
    V(this.viewport).attr('transform', '');
    var viewportBbox = this.getContentBBox();
    var svgClone = this.svg.cloneNode(true);
    V(this.viewport).attr('transform', viewportTransform || '');
    svgClone.removeAttribute('style');
    if (opt.preserveDimensions) {
        V(svgClone).attr({
            width: viewportBbox.width,
            height: viewportBbox.height
        })
    } else {
        V(svgClone).attr({
            width: '100%',
            height: '100%'
        })
    }
    V(svgClone).attr('viewBox', viewportBbox.x + ' ' + viewportBbox.y + ' ' + viewportBbox.width + ' ' + viewportBbox.height);
    var styleSheetsCount = document.styleSheets.length;
    var styleSheetsCopy = [
    ];
    for (var i = styleSheetsCount - 1; i >= 0; i--) {
        styleSheetsCopy[i] = document.styleSheets[i];
        document.styleSheets[i].disabled = true
    }
    var defaultComputedStyles = {
    };
    $(this.svg).find('*').each(function (idx) {
        var computedStyle = window.getComputedStyle(this, null);
        var defaultComputedStyle = {
        };
        _.each(computedStyle, function (property) {
            defaultComputedStyle[property] = computedStyle.getPropertyValue(property)
        });
        defaultComputedStyles[idx] = defaultComputedStyle
    });
    if (styleSheetsCount != document.styleSheets.length) {
        _.each(styleSheetsCopy, function (copy, i) {
            document.styleSheets[i] = copy
        })
    }
    for (var i = 0; i < styleSheetsCount; i++) {
        document.styleSheets[i].disabled = false
    }
    var customStyles = {
    };
    $(this.svg).find('*').each(function (idx) {
        var computedStyle = window.getComputedStyle(this, null);
        var defaultComputedStyle = defaultComputedStyles[idx];
        var customStyle = {
        };
        _.each(computedStyle, function (property) {
            if (computedStyle.getPropertyValue(property) !== defaultComputedStyle[property]) {
                customStyle[property] = computedStyle.getPropertyValue(property)
            }
        });
        customStyles[idx] = customStyle
    });
    $(svgClone).find('*').each(function (idx) {
        $(this).css(customStyles[idx])
    });
    $(svgClone).find('.connection-wrap, .marker-vertices, .link-tools, .marker-arrowheads').remove();
    var svgString;
    try {
        var serializer = new XMLSerializer;
        svgString = serializer.serializeToString(svgClone)
    } catch (err) {
        console.error('Error serializing paper to SVG:', err)
    }
    var isChrome = !!window.chrome && !window.opera;
    var isIE = navigator.appName == 'Microsoft Internet Explorer';
    var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
    if (isChrome) {
    }
    if (isIE) {
        var xmlns = 'xmlns="' + this.svg.namespaceURI + '"';
        var matches = svgString.match(new RegExp(xmlns, 'g'));
        if (matches && matches.length >= 2) svgString = svgString.replace(new RegExp(xmlns), '')
    }
    if (isSafari) {
        svgString = svgString.replace('xlink="', 'xmlns:xlink="');
        svgString = svgString.replace(/href="/g, 'xlink:href="')
    }
    return svgString
};

joint.dia.Paper.prototype.openAsSVG = function () {
    var svg = this.toSVG();
    var windowFeatures = 'menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes';
    var windowName = _.uniqueId('svg_output');
    var dataImageUri = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svg)));
    var imageWindow = window.open('', windowName, windowFeatures);
    imageWindow.document.write('<img src="' + dataImageUri + '" style="max-height:100%" />')
};

joint.dia.Paper.prototype.toDataURL = function (callback, options) {
    if (typeof this.toSVG !== 'function') throw new Error('The joint.format.svg.js plugin must be loaded.');
    options = options || {
        };
    var imageWidth,
        imageHeight,
        contentHeight,
        contentWidth,
        padding = options.padding || 0;
    if (!options.width || !options.height) {
        var clientRect = this.viewport.getBoundingClientRect();
        contentWidth = clientRect.width || 1;
        contentHeight = clientRect.height || 1;
        imageWidth = contentWidth + 2 * padding;
        imageHeight = contentHeight + 2 * padding;
    } else {
        imageWidth = options.width;
        imageHeight = options.height;
        padding = Math.min(padding, imageWidth / 2 - 1, imageHeight / 2 - 1);
        contentWidth = imageWidth - 2 * padding;
        contentHeight = imageHeight - 2 * padding;
    }
    var img = new Image;
    img.onload = function () {
        var dataURL,
            context,
            canvas;
        function createCanvas() {
            canvas = document.createElement('canvas');
            canvas.width = imageWidth;
            canvas.height = imageHeight;
            context = canvas.getContext('2d');
            context.fillStyle = options.backgroundColor || 'white';
            context.fillRect(0, 0, imageWidth, imageHeight);
        }
        createCanvas();
        context.drawImage(img, padding, padding, contentWidth, contentHeight);
        try {
            dataURL = canvas.toDataURL(options.type, options.quality)
        } catch (e) {
            if (typeof canvg === 'undefined') {
                console.error('Canvas tainted. Canvg library required.');
                return
            }
            createCanvas();
            canvg(canvas, svg, {
                ignoreDimensions: true,
                ignoreClear: true,
                offsetX: padding,
                offsetY: padding,
                renderCallback: function () {
                    dataURL = canvas.toDataURL(options.type, options.quality);
                    callback(dataURL)
                }
            });
            return
        }
        callback(dataURL)
    };
    var svg = this.toSVG();
    svg = svg.replace('width="100%"', 'width="' + contentWidth + '"').replace('height="100%"', 'height="' + contentHeight + '"');
    img.src = 'data:image/svg+xml;base64,' + btoa(svg);
};
