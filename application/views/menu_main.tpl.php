<style>

/*------меню2-------*/
.cl-effect-17
	{
	float:right;	
	margin-bottom: 15px;
	margin-top:20px;
	}
.cl-effect-17 a
	{
	float:left;
	text-transform:uppercase;
	margin-right: 18px;
	font-size: 17px;
	}
.cl-effect-17  a.last
	{
	margin-right:0;
	}
nav a {
	position: relative;
	display: inline-block;
	outline: none;
	text-decoration: none;
	text-transform: uppercase;
	letter-spacing: 1px;
	font-weight: 400;
	text-shadow: 0 0 1px rgba(138,138,138,1);
	font-size: 1.35em;
	}

nav a:hover,
nav a:focus {
	outline: none;
}
/* Effect 17: move up fade out, push border */
.cl-effect-17 a {
	color: #8a8a8a;
	text-shadow: none;
	padding-left: 2px;
padding-right: 2px;
}

.cl-effect-17 a::before {
	color: #000;
	text-shadow: 0 0 1px rgba(138,138,138,1);
	content: attr(data-hover);
	position: absolute;
	-webkit-transition: -webkit-transform 0.3s, opacity 0.3s;
	-moz-transition: -moz-transform 0.3s, opacity 0.3s;
	transition: transform 0.3s, opacity 0.3s;
	pointer-events: none;
}

.cl-effect-17 a::after {
	content: '';
	position: absolute;
	left: 0;
	bottom: 0;
	width: 100%;
	height: 2px;
	background-color:#8a8a8a;
	opacity: 0;
	-webkit-transform: translateY(5px);
	-moz-transform: translateY(5px);
	transform: translateY(5px);
	-webkit-transition: -webkit-transform 0.3s, opacity 0.3s;
	-moz-transition: -moz-transform 0.3s, opacity 0.3s;
	transition: transform 0.3s, opacity 0.3s;
	pointer-events: none;
}

.cl-effect-17 a:hover::before,
.cl-effect-17 a:focus::before {
	opacity: 0;
	-webkit-transform: translateY(-2px);
	-moz-transform: translateY(-2px);
	transform: translateY(-2px);
}

.cl-effect-17 a:hover::after,
.cl-effect-17 a:focus::after {
	opacity: 1;
	-webkit-transform: translateY(0px);
	-moz-transform: translateY(0px);
	transform: translateY(0px);
}
.cl-effect-17 a.active
	{
	color: white;
	background: black;
	}
.cl-effect-17 a.active::before
	{
	color:#fff;
	}
</style>

<?php //showTopMenu2(); ?><br />
