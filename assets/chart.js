document.addEventListener("DOMContentLoaded", function() {
const margin = {top:20, right:20, left:20, bottom:20};

const svg = d3.select('svg.myChart');

data.forEach(d => {
	d.count=+d.count;
	d.score = String(d.score)
});

svgProps = document.querySelector('svg.myChart').getBoundingClientRect()
const innerWidth = Math.floor(svgProps.width) - margin.left - margin.right;
const innerHeight = Math.floor(svgProps.height) - margin.top - margin.bottom;

svg.attr("viewBox", `0, 0, ${svgProps.width}, ${svgProps.height}`);

const render = data => {
	
	const xValue = d => d.count;
	const yValue = d => d.score;
	
	const xScale = d3.scaleLinear()
	.domain([0, d3.max(data, xValue)])
	.range([0, innerWidth]);
	
	const yScale = d3.scaleBand()
	.domain(data.map( yValue))
	.range([0, innerHeight])
	.padding(0.3)
	
	
	const yAxis = d3.axisLeft(yScale);
	const xAxis = d3.axisBottom(xScale);
	
	const g = svg.append('g')
	.attr('transform',`translate(${margin.left}, ${margin.top})`);
	

	g.append('g').call(yAxis);
/*	g.append('g').call(xAxis)
	.attr('transform',`translate(0,${innerHeight})`);
*/
	
	g.selectAll('rect').data(data)
	.enter().append('rect')
// 	.attr('x', margin.left)
	.attr('y', d => yScale(yValue(d)))
	.attr('width', d => xScale(xValue(d)))
	.attr('height',yScale.bandwidth());	
	
	
	const label_height = Math.floor(.4*yScale.bandwidth());
	
const labels = g.selectAll(".label")
  .data(data)
  .enter()
  .append("text")
  .attr("class","label")
  .text(d => d.count)
  .attr("x", d => xScale(xValue(d)) > 25 ?  xScale(xValue(d))-5 : xScale(xValue(d))+5)
  .attr("y", d => yScale(yValue(d)) + (yScale.bandwidth()+label_height) / 2) // Adjust the y-position
  .attr("font-size", label_height+"px")
  .attr("text-anchor", d => xScale(xValue(d)) > 25 ? "end" : "start" );
  
  
/*
const gridLinePositions = data.map((d, i) => yScale(yValue(d)));


// Append the grid lines to your SVG
const gridLines = g.selectAll(".grid-line")
  .data(gridLinePositions)
  .enter()
  .append("line")
  .attr("class", "grid-line")
  .attr("x1", 0) // Start at the left edge of the chart
  .attr("x2", innerWidth) // Extend to the right edge of the chart
  .attr("y1", d => d)
  .attr("y2", d => d)
  .attr("stroke", "lightgray")
  .attr("stroke-opacity", 0.7);*/
};


render(data);
});