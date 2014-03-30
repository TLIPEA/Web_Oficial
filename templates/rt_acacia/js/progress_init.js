((function(loader){
    if (!RTLoaderCanvas) throw new Error('Unable to find RTLoaderCanvas library.');

    var Progress = {
        init: function(){
            var elements = document.getElements('[data-canvas-graph]'),
                data, canvas, middle, size, icon;

            elements.forEach(function(element){
                data = element.get('data-canvas-graph');
                try {
                    data = JSON.decode(data);
                }
                catch(e){
                    data = null;
                    element.html('<small>Error: Unable to parse options: <br /><code>' + data + '</code></small>');
                }

                if (data){
                    if (!data.color) data.color = element.getStyle('color');

                    element.setStyles({
                        width: (data.radius * 2),
                        height: (data.radius * 2)
                    });

                    canvas = new loader(element, {fgColor: data.color, strokeWidth: data.size, start: 0});
                    element.store('canvas', {canvas: canvas, data: data});
                    canvas = canvas.element;

                    middle = new Element('div.canvas-middle').inject(canvas);
                    size   = ((data.radius * 2) - (data.size * 2));
                    middle.setStyles({
                        width: size, height: size, borderRadius: size + 'px',
                        marginLeft: -(size / 2), marginTop: -(size / 2)
                    });

                    if (data.icon){
                        icon = new Element('i.' + data.icon).inject(middle);
                        icon.setStyles({
                            color: (data.iconColor ? data.iconColor : data.color),
                            fontSize: data.iconSize,
                            lineHeight: size
                        });
                    }

                    if (data.text){
                        text = new Element('span.canvas-pc').inject(middle);
                        text.setStyles({
                            color: (data.iconColor ? data.iconColor : data.color)
                        });
                    }
                }
            });
        },

        animate: function(){
            var elements = document.getElements('[data-canvas-graph]'),
                previous = [], pc;

            elements.forEach(function(element, i){
                if (!previous[i]) previous[i] = 0;
                var memory = element.retrieve('canvas'),
                    fx = moofx(function(time){
                        time = time.toInt();
                        if ((!time || !previous[i]) || (time / previous[i]) < 1.2){
                            pc = element.getElement('.canvas-pc');
                            memory.canvas.update(time);
                            if (pc) pc.set('text', time.toInt() + '%');
                        }

                        previous[i] = time;
                    });

                fx.start.delay(Number.random(0, 400), fx, [0, memory.data.start, {
                    duration: '400ms',
                    equation: 'ease-out'
                }]);
            });
        }
    };

    window.addEvent('domready', Progress.init);
    window.addEvent('load', Progress.animate);

})(RTLoaderCanvas));
