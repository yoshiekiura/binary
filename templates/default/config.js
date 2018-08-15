'use strict';

module.exports = {
	css: [ // mengambil semua file css yang akan dicompress menjadi file dengan nama yang ada di `.dest.path`+'css/'+`.dest.css`
		'html/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
		'html/vendor/revolution/settings-imported.css',
		'html/vendor/revolution/layers.css',
		'html/fonts/icon/font/flaticon-imported.css',
		'html/vendor/revolution/navigation.css',
		'html/vendor/fancy-box/jquery.fancybox-imported.css',
		'html/vendor/menu/dist/css/slimmenu.css',
		'html/vendor/owl-carousel/owl.carousel.css',
		'html/vendor/owl-carousel/owl.theme.css',
		'html/vendor/WOW-master/css/libs/animate-imported.css',
		'html/vendor/hover.css',
		'html/vendor/sanzzy-map/dist/snazzy-info-window.min.css',
		'html/css/style-imported.css',
		'html/css/responsive.css',
	],
	js: [ // mengambil semua file js yang akan dicompress menjadi file dengan nama yang ada di `.dest.path`+'js/'+`.dest.js`
		'html/vendor/revolution/jquery.themepunch.tools.min.js',
		'html/vendor/revolution/jquery.themepunch.revolution.min.js',
		'html/vendor/revolution/revolution.extension.slideanims.min.js',
		'html/vendor/revolution/revolution.extension.layeranimation.min.js',
		'html/vendor/revolution/revolution.extension.navigation.min.js',
		'html/vendor/revolution/revolution.extension.kenburn.min.js',

		'html/vendor/menu/src/js/jquery.slimmenu.js',
		'html/vendor/jquery.easing.1.3.js',
		'html/vendor/bootstrap-select/dist/js/bootstrap-select.js',
		'html/vendor/fancy-box/jquery.fancybox.pack.js',
		'html/vendor/jquery.appear.js',
		'html/vendor/jquery.countTo.js',
		'html/vendor/WOW-master/dist/wow.min.js',
		'html/vendor/owl-carousel/owl.carousel.min.js',

		// 'html/vendor/sanzzy-map/dist/snazzy-info-window.min.js',
		'html/js/theme.js',
		// 'js/map-script.js',

	],
	source: __dirname+"/", // menentukan doc_root yang akan di compress jika dinamis isikan saja __dirname+"/"
	dest: {
		path: __dirname + "/", // menentukan path tujuan
		css: "custom.css", // menentukan nama hasil compress dari semua css dan scss
		js: "application.js" // menentukan nama hasil compress dari semua file js
	},
	jscompress : 2, // 1=uglify, 2=packer
	watch : 1 // 1=recompile when changes, 0=compile only
}