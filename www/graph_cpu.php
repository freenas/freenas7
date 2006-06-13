#!/usr/local/bin/php -f
<?php
/*
	graph_cpu.php
	part of m0n0wall (http://m0n0.ch/wall)
	
	Copyright (C) 2004-2005 T. Lechat <dev@lechat.org> and Manuel Kasper <mk@neon1.net>.
	All rights reserved.
	
	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:
	
	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.
	
	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.
	
	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/

/********* Other conf *******/
$nb_plot=120;			//NB plot in graph

$fetch_link = "stats.cgi?cpu";

//Style
$style['bg']="fill:white;stroke:none;stroke-width:0;opacity:1;";
$style['axis']="fill:black;stroke:black;stroke-width:1;";
$style['cpu']="fill:#435370; font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:7;";
$style['graph_cpu']="fill:none;stroke:#435370;stroke-width:1;opacity:0.8;";
$style['legend']="fill:black; font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:4;";
$style['grid_txt']="fill:gray; font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:6;";
$style['grid']="stroke:gray;stroke-width:1;opacity:0.5;";
$style['error']="fill:blue; font-family:Arial; font-size:4;";
$style['collect_initial']="fill:gray; font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:4;";

$error_text = "Cannot get CPU load";

$height=100;		//SVG internal height : do not modify
$width=200;		//SVG internal width : do not modify

/********* Graph DATA **************/
header("Content-type: image/svg+xml");
print('<?xml version="1.0" encoding="iso-8859-1"?>' . "\n");?><svg width="100%" height="100%" viewBox="0 0 <?=$width?> <?=$height?>" preserveAspectRatio="none" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" onload="init(evt)">
<g id="graph" style="visibility:visible">
	<rect id="bg" x1="0" y1="0" x2="<?=$width?>" y2="<?=$height?>" style="<?=$style['bg']?>"/>
	<line id="axis_x" x1="0" y1="0" x2="0" y2="<?=$height?>" style="<?=$style['axis']?>"/>
	<line id="axis_y" x1="0" y1="<?=$height?>" x2="<?=$width?>" y2="<?=$height?>" style="<?=$style['axis']?>"/>
	<path id="graph_cpu"  d="M0 <?=$height?> L 0 <?=$height?>" style="<?=$style['graph_cpu']?>"/>
	<path id="grid"  d="M0 <?=$height/4*1?> L <?=$width?> <?=$height/4*1?> M0 <?=$height/4*2?> L <?=$width?> <?=$height/4*2?> M0 <?=$height/4*3?> L <?=$width?> <?=$height/4*3?>" style="<?=$style[grid]?>"/>
	<text id="grid_txt1" x="<?=$width?>" y="<?=$height/4*1?>" style="<?=$style['grid_txt']?> text-anchor:end">75%</text>
	<text id="grid_txt2" x="<?=$width?>" y="<?=$height/4*2?>" style="<?=$style['grid_txt']?> text-anchor:end">50%</text>
	<text id="grid_txt3" x="<?=$width?>" y="<?=$height/4*3?>" style="<?=$style['grid_txt']?> text-anchor:end">25%</text>
	<text id="graph_cpu_txt" x="4" y="8" style="<?=$style['cpu']?>"> </text>
	<polygon id="axis_arrow_x" style="<?=$style['axis']?>" points="<?=($width) . "," . ($height)?> <?=($width-2) . "," . ($height-2)?> <?=($width-2) . "," . $height?>"/>
	<text id="error" x="<?=$width*0.5?>" y="<?=$height*0.5?>"  style="visibility:hidden;<?=$style['error']?> text-anchor:middle"><?=$error_text?></text>
	<text id="collect_initial" x="<?=$width*0.5?>" y="<?=$height*0.5?>"  style="visibility:hidden;<?=$style['collect_initial']?> text-anchor:middle">Collecting initial data, please wait...</text>
</g>

<script type="text/ecmascript"><![CDATA[
var SVGDoc;
var last_cpu_total=0;
var last_cpu_idle=0;
var diff_cpu_total=0;
var diff_cpu_idle=0;
plot_cpu = new Array();

var isfirst=1;
var index_plot=0;
var step = <?=$width?> / <?=$nb_plot?> ;

function init(evt) {
	SVGDoc = evt.getTarget().getOwnerDocument();
	go();
}

function go() {
	getURL('<?=$fetch_link?>',urlcallback);
}

function urlcallback(obj) {
	var error = 0;

	//shift plot to left if nb_plot is already completed
	var i=0;
	if(index_plot > <?=$nb_plot?>)
	{
		while (i <= <?=$nb_plot?>)
		{
			var a=i+1;
			plot_cpu[i]=plot_cpu[a];
			i=i+1;
		}
		index_plot = <?=$nb_plot?>;
		plot_cpu[index_plot]=0;
	}

	//if Geturl returns something
	if (obj.success){
		var cpu = parseInt(obj.content);
		var scale;

		if(!isNumber(cpu)) {
			goerror();
			return;
		} else {
			SVGDoc.getElementById("error").getStyle().setProperty ('visibility', 'hidden');
		}

		if(isfirst) {
			SVGDoc.getElementById("collect_initial").getStyle().setProperty ('visibility', 'visible');
			go();
			isfirst=0;
			return;
		} else SVGDoc.getElementById("collect_initial").getStyle().setProperty ('visibility', 'hidden');

		plot_cpu[index_plot] = cpu;

		SVGDoc.getElementById('graph_cpu_txt').getFirstChild().setData(plot_cpu[index_plot] + '%');
		
		scale = <?=$height?> / 100;
		
		i = 0;
		
		while (i <= index_plot)
		{
			var x = step * i;
			var y_cpu= <?=$height?> - (plot_cpu[i] * scale);
			if(i==0) {
				var path_cpu = "M" + x + " " + y_cpu;
			}
			else
			{
				var path_cpu = path_cpu + " L" + x + " " + y_cpu;
			}
			i = i + 1;
		}

		index_plot = index_plot+1;
		SVGDoc.getElementById('graph_cpu').setAttribute("d", path_cpu);

		go();
	}
	else
	{ //In case of Geturl fails
		goerror();
	}
}

function goerror() {
	SVGDoc.getElementById("error").getStyle().setProperty ('visibility', 'visible');
	go();
}

function isNumber(a) {
    return typeof a == 'number' && isFinite(a);
}

function LZ(x) {
	return (x < 0 || x > 9 ? "" : "0") + x
}
]]></script>
</svg>
