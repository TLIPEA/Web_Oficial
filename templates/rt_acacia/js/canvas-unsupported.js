((function(){
    var Canvas = {
        init: function(){
            var elements = document.getElements('canvas');
            elements.forEach(function(element){
                if (!element.getParent('[data-canvas-graph]')){
                    new Element('div.canvas-not-supported', {html: '<span>This Canvas HTML element is unsupported in IE8</span>'}).inject(element, 'before');
                    element.dispose();
                }
            });
        }
    };

    window.addEvent('domready', Canvas.init);
})());
