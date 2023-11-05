<?php
	class Chart
	{
		function __construct($data)
		{
			$this->margin_left=40;
			$this->margin_right=20;
			$this->margin_top=20;
			$this->margin_bottom=40;
			$this->x_ticks=[];
			$this->width=600;
			$this->height=600;
			$this->data = $data;
			
			
		}
		function drawGrid()
		{
			echo '<line x1="'.$this->margin_left.'" y1="'.$this->margin_top.'" x2="'.$this->margin_left.'" y2="'.($this->height-$this->margin_bottom).'" />';
		//	echo '<line x1="'.$this->margin.'" y1="'.($this->height-$this->margin).'" x2="'.$this->width.'" y2="'.($this->height-$this->margin).'" />';
			
			$this->cell_height = ($this->height - ($this->margin_top+$this->margin_bottom))/ sizeof($this->data);
			
			echo '<g class="graph-grad">';
			$keys = array_keys($this->data);
			for($i = 1; $i<=sizeof($this->data); $i++)
			{
				echo '<text x="'.($this->margin_left/2).'" y="'.($this->cell_height*$i-(.5*$this->cell_height)+$this->margin_top).'">'.$keys[$i-1].'</text>';
				echo '<line x1="'.$this->margin_left.'" y1="'.($this->margin_top+$this->cell_height*$i).'" x2="'.$this->width-($this->margin_right).'" y2="'.($this->margin_top+ $this->cell_height*$i).'" />';
			}
			
			echo '</g>';
			
			
		}
		function drawGraph()
		{
			
			$max = max($this->data);
			$min_width = 40;
			$unit_width = ($this->width -$min_width - ($this->margin_left+$this->margin_right))/$max;
			$values = array_values($this->data);
			
			for($i = 0; $i<sizeof($this->data); $i++)
			{
				echo '<rect x="'.$this->margin_left.'" y="'.($this->margin_top + $i*$this->cell_height+($this->cell_height*.15)).'" width="'.(($values[$i])*$unit_width+$min_width).'" height="'.($this->cell_height*.7).'">
				
				
				</rect><text class="label" x="'.(($values[$i])*$unit_width+$min_width+$this->margin_left-6).'" y="'.($this->margin_top + $i*$this->cell_height+10+.5*$this->cell_height).'">'.$values[$i].' </text>';
			}
		}
		function draw()
		{
			echo "<svg viewbox=\"0 0 ".$this->width." ".$this->height."\" width=\"100%\">\n";
			$this->drawGrid();
			$this->drawGraph();
			echo "</svg>";
		}
	}