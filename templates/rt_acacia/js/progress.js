(function(){

    //convert degree to radian
    var _degreeToRadian = function _degreeToRadian(deg){
        return Math.PI * deg / 180;
    };

    //convert radian to degree
    var _radianToDegree = function _radianToDegree(rad){
        return (180 * (rad)) / Math.PI;
    };

    var Loader = new Class({
        Implements: [Options, Events],
        options: {
            fgColor:    '#ec5840',//'#f00',
            bgColor:    'transparent',
            strokeWidth: 8,
            min:         0,
            max:         100,
            start:       0
        },

        initialize: function(element, options){
            this.setOptions(options);
            this.element = document.id(element) || document.getElement(element);

            this.size    = this.element.offsetWidth || 70;
            this.radius  = this.size / 2;
            this.ctx     = new Element('canvas').inject(this.element, 'top').set('width', this.size + 'px').set('height', this.size + 'px');
            this.center  = {};
            this.origin  = {};

            this.init();

            //return this.element;
        },

        init: function(){
            this.setup();
            this.drawBackground();
            this.startView();
        },

        setup: function(){
            this.ctx = (this.ctx.getContext) ? this.ctx.getContext('2d') : null;

            this.origin = {
                x: this.radius,
                y: this.radius
            };
        },

        drawArc: function(endingAngle, clockwise, color){
            if (!this.ctx) return;
            this.ctx.beginPath();
            this.ctx.arc(
                this.radius,
                this.radius,
                (this.radius - (this.options.strokeWidth / 2)),
                (Math.PI / 180) * (-90),
                endingAngle,
                clockwise
            );
            this.ctx.strokeStyle = color;
            this.ctx.lineWidth = this.options.strokeWidth;
            this.ctx.stroke();
        },

        moveArc: function(percent, clockwise, color){
            if (!this.ctx) return;
            this.ctx.beginPath();
            this.ctx.arc(
                this.radius,
                this.radius,
                (this.radius - (this.options.strokeWidth / 2)),
                _degreeToRadian(percent),
                _degreeToRadian((percent + 60)),
                clockwise
            );
            this.ctx.strokeStyle = color;
            this.ctx.lineWidth = this.options.strokeWidth;
            this.ctx.stroke();
        },

        drawBackground: function(){
            var rad         = _degreeToRadian(90),
                endingAngle = ((Math.PI / 180) * 360) - rad;

            this.drawArc(endingAngle, 1, this.options.bgColor);
        },

        updateData: function(angle){
            var deg, percent, currentVal, data = this.options, total = data.max - data.min;

            deg = _radianToDegree(angle);

            if (deg < 0) deg = 180 + (180 - Math.abs(deg));

            percent = Math.round(deg * 100 / 360);
            currentVal = percent * total / 100;

            this.fireEvent('change', currentVal);
        },

        drawProgress: function(coords){
            if (!this.ctx) return;
            var x, y, signX, signY, rad = _degreeToRadian(90), endAngle = coords;

            this.ctx.clearRect(0, 0, this.size, this.size);
            this.drawBackground();

            if (typeof coords == 'object'){
                signX = (coords.x < this.origin.x) ? -1 : 1;
                signY = (coords.y < this.origin.y) ? 1 : -1;

                x = Math.abs(this.origin.x - coords.x) * signX;
                y = Math.abs(this.origin.y - coords.y) * signY;

                endAngle = Math.atan2(x, y);
            }

            this.drawArc(endAngle - rad, 0, this.options.fgColor);
            this.updateData(endAngle);
        },

        moveProgress: function(coords){
            if (!this.ctx) return;
            var x, y, signX, signY, rad = _degreeToRadian(90), endAngle = coords;

            this.ctx.clearRect(0, 0, this.size, this.size);
            this.drawBackground();

            if (typeof coords == 'object'){
                signX = (coords.x < this.origin.x) ? -1 : 1;
                signY = (coords.y < this.origin.y) ? 1 : -1;

                x = Math.abs(this.origin.x - coords.x) * signX;
                y = Math.abs(this.origin.y - coords.y) * signY;

                endAngle = Math.atan2(x, y);
            }

            this.moveArc(_radianToDegree(endAngle - rad), 0, this.options.fgColor);
            this.updateData(endAngle);
        },

        getAngle: function(value){
            var currentPercent = value * 100 / (this.options.max - this.options.min),
                currentAngle   = _degreeToRadian(currentPercent * 360 / 100);

            return currentAngle;
        },

        startView: function(){
            this.drawProgress(this.getAngle(this.options.start));
        },

        update: function(percent){
            this.drawProgress(this.getAngle(percent));
        }

    });

    this.RTLoaderCanvas = Loader;

}());
