d3.yearChart = {
    init: function(container, width, height, dataset)
    {
        var barPadding = 1;
        var bottomMargin = 40;
        var scale = 20;
        var svg = d3.select(container)
                    .append("svg")
                    .attr("width", width)
                    .attr("height", height);

        svg.selectAll("rect")
            .data(dataset)
            .enter()
            .append("rect")
            .attr("x", function(d, i) {
                return i * (width / dataset.length);
            })
            .attr("y", function(d) {
                return (height - bottomMargin) - (d.value * scale);
            })
            .attr("width", width / dataset.length - barPadding)
            .attr("height", function(d) {
                return d.value * scale;
            })
            .attr("fill", function(d) {
                return "rgb(167, 34, 46)";
            });

        svg.selectAll("text")
            .data(dataset)
            .enter()
            .append("text")
            .text(function(d) {
                return d.label;
            })
            .attr("text-anchor", "middle")
            .attr('transform', 'rotate(-90)')
            .attr("x", function(d, i) {
                return -(height-(bottomMargin/2));
            })
            .attr("y", function(d, i) {
                return i * (width / dataset.length) + (width / dataset.length - barPadding) / 1.4;
            })
            .attr("font-family", "sans-serif")
            .attr("font-size", "10px")
            .attr("fill", "white");

        svg.append("g");
        svg.select("g")
            .selectAll("text")
            .data(dataset)
            .enter()
            .append("text")
            .text(function(d) {
                return (d.value > 0) ? d.value : '';
            })
            .attr("text-anchor", "middle")
            .attr("x", function(d, i) {
                return i * (width / dataset.length) + (width / dataset.length - barPadding) / 2.2;
            })
            .attr("y", function(d, i) {
                return (height-(d.value * scale))-27;
            })
            .attr("font-family", "sans-serif")
            .attr("font-size", "11px")
            .attr("font-weight", "bold")
            .attr("fill", "rgb(25, 25, 25)");
    }
};