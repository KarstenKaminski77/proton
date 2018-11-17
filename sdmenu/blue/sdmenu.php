<style>
div.sdmenu {
	width: 200px;
	font-family: Arial, sans-serif;
	font-size: 12px;
	padding-bottom: 10px;
	color: <?php echo $row_rs_fonts['RGB1']; ?>;
	background-image: url(http://<?php echo $_SERVER['HTTP_HOST']; ?>/kwd_inv/sdmenu/blue/bottom.gif);
	background-repeat: no-repeat;
	background-position: right bottom;
}
div.sdmenu div {
	overflow: hidden;
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #E0E0E0;
	border-top-style: none;
	border-right-style: solid;
	border-left-style: solid;
	border-right-color: #CDCDCD;
	border-left-color: #CDCDCD;
	border-right-width: 1px;
	border-left-width: 1px;
	background-color: #f3f3f3;
	background-image: url(hr.jpg);
	background-repeat: repeat-x;
	background-position: center bottom;
}
div.sdmenu div:first-child {
	background: url(http://<?php echo $_SERVER['HTTP_HOST']; ?>/kwd_inv/sdmenu/blue/toptitle.gif) no-repeat;
}
div.sdmenu div.collapsed {
	height: 25px;
}
div.sdmenu div span {
	display: block;
	padding: 5px 25px;
	font-weight: bold;
	color: <?php echo $row_rs_fonts['RGB1']; ?>;
	background: url(http://<?php echo $_SERVER['HTTP_HOST']; ?>/kwd_inv/sdmenu/blue/expanded.gif) no-repeat 10px center;
	cursor: pointer;
	border-bottom: 1px solid #ddd;
}
div.sdmenu div.collapsed span {
	background-image: url(http:/<?php echo $_SERVER['HTTP_HOST']; ?>/kwd_inv/sdmenu/blue/collapsed.gif);
}
div.sdmenu div a {
	display: block;
	border-bottom: 1px solid #ddd;
	color: <?php echo $row_rs_fonts['RGB1']; ?>;
	text-decoration: none;
	padding-top: 5px;
	padding-right: 10px;
	padding-bottom: 5px;
	padding-left: 25px;
	background-color: #E4E4E4;
}
div.sdmenu div a.current {
	text-decoration: none;
	color:<?php echo $row_rs_fonts['RGB1']; ?>;
	background-color: <?php echo $row_rs_fonts['RGB2']; ?>;
}
div.sdmenu div a:hover {
	background : <?php echo $row_rs_fonts['RGB1']; ?> url(http<?php echo $_SERVER['HTTP_HOST']; ?>/kwd_inv/sdmenu/blue/linkarrow.gif) no-repeat right center;
	color: #fff;
	text-decoration: none;
}
.first-line {
	background-image:url(sdmenu/blue/hr.jpg) !important;
	height:1px;
	width:200px;
	display:block !important;
	background-repeat: repeat-x;
	background-position: center bottom;
}
</style>