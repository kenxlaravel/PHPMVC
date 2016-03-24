jQuery.fn.equalCols = function(){
	//Array Sorter
	var sortNumber = function(a,b){return b - a;};
	var heights = [];
	//Push each height into an array
	$(this).each(function(){
		heights.push($(this).height());
	});
	heights.sort(sortNumber);
	var maxHeight = heights[0];
	return this.each(function(){
		//Set each column to the max height
		$(this).css({'height': maxHeight});
	});
};

