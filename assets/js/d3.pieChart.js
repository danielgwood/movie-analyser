d3.pieChart = {
    init: function(container, width, height, dataset)
    {
        var radius = Math.min(width, height) / 2;
        var color = d3.scale.category20();
        var labelPad = 3;

        var vis = d3.select(container)
            .append("svg:svg")
            .data([dataset])
            .attr("width", width)
            .attr("height", height)
            .append("svg:g")
            .attr("transform", "translate(" + (width / 2) + "," + radius + ")");

        var arc = d3.svg.arc()
            .outerRadius(radius)
            .innerRadius(radius/2);

        var pie = d3.layout.pie()
            .value(function(d) { return d.value; });

        var arcs = vis.selectAll("g.slice")
            .data(pie)
            .enter()
                .append("svg:g")
                .attr("class", "slice");

        arcs.append("svg:path")
                .attr("fill", function(d, i) { return color(i); } )
                .attr("d", arc);

        arcs.append("svg:text")
            .attr("transform", function(d) {
                d.innerRadius = radius/2;
                d.outerRadius = radius;
                return "translate(" + arc.centroid(d) + ")";
            })
            .attr('class', 'labeltext')
            .attr("text-anchor", "middle")
            .text(function(d, i) { return dataset[i].label; })
            .attr("fill", "white")
            .attr("font-size", "12px")

        arcs.insert("rect", ".labeltext")
                .attr("width", function() { return d3.select(this.parentNode).select("text").node().getBBox().width+(labelPad*2); })
                .attr("height", function() { return d3.select(this.parentNode).select("text").node().getBBox().height+(labelPad*2); })
                .attr("x", function(d, i) {
                    return arc.centroid(d)[0] - this.getAttribute('width')/2;
                })
                .attr("y", function(d) {
                    return arc.centroid(d)[1] - this.getAttribute('height')/1.5;
                })
                .attr("fill", function(d) {
                    return "rgba(20, 20, 20, 0.8)";
                });
    }
};