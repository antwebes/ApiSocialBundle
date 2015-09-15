module.exports = function(grunt) {
	//s"use strict";
	var folder = 'v0.6';
	grunt.initConfig({
		// Run your source code through JSHint's defaults.
		jshint: {
			file: ["app/apps/**/*.js"],
			options: {
				ignores: ["app/apps/**/test/*.js"],
				unused:true,
				undef:true,
				globals: {
					window: true,
					document: true,
					_: true,
					$: true,
					jQuery: true,
					Backbone: true,
					Handlebars: true,
					_: true,
					App: true,
					define: true,
					require: true,
					requirejs: true,
					console:true
				}
			}
		},

		jslint: {
			client: {
				src: ["app/apps/**/*.js"],
				exclude: ["app/apps/**/test/*.js"],
				directives: {
					browser: true,
					predef: [
						'window',
						'document',
						'_',
						'$',
						'jQuery',
						'Backbone',
						'Handlebars',
						'_',
						'App',
						'define',
						'require',
						'requirejs',
						'console'
					]
				}
			}
		},

		uglify: {
			build: {
				files: {
					'vendor/bower/requirejs/require.min.js': ['vendor/bower/requirejs/require.js']
				}
			}
		},

		//start grunt s3
		// aws: grunt.file.readJSON('~/grunt-aws.json'),
		s3: {
			options: {
				key: 'AKIAI2N4IWHL7JGG4ONQ',
				secret: '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',
				bucket: 'social-dinamic',
				access: 'public-read',
				// upload existing files if and only if they are newer than the versions of those same files on the server
				// verify: true,
				headers: {
					// Two Year cache policy (1000 * 60 * 60 * 24 * 730)
					"Cache-Control": "max-age=630720000, public",
					"Expires": new Date(Date.now() + 63072000000).toUTCString(),
					"Vary": "Accept-Encoding",
				}
			},
			dev: {
				// These options override the defaults
				options: {
					encodePaths: false,
					//max number of concurrent transfers - if not specified or set to 0, will be unlimited
					maxOperations: 20
				},
				// Files to be uploaded.
				upload: [
					{
						src: 'www-built/**/*',
						dest: "/"+folder+"/",
						rel: '/var/www/social-chatsfree/',
						options: { gzip: true }
					},
					{
						src: 'vendor/bower/requirejs/require.min.js',
						dest: '/'+folder+'/vendor/bower/requirejs/require.min.js',
						options: { gzip: true }
					},
					{
						src: 'vendor/bower/backbone-forms/distribution.amd/backbone-forms.js',
						dest: ''+folder+'/vendor/bower/backbone-forms/distribution.amd/backbone-forms.js',
						options: { gzip: true }
					},
					{
						src: 'vendor/bower/backbone-forms/distribution.amd/templates/bootstrap3.js',
						dest: ''+folder+'/vendor/bower/backbone-forms/distribution.amd/templates/bootstrap3.js',
						options: { gzip: true }
					},
					{
						src: 'vendor/bower/bootstrap/js/modal.js',
						dest: ''+folder+'/vendor/bower/bootstrap/js/modal.js',
						options: { gzip: true }
					},
					{
						src: 'vendor/bower/backbone.bootstrap-modal/src/backbone.bootstrap-modal.js',
						dest: ''+folder+'/vendor/bower/backbone.bootstrap-modal/src/backbone.bootstrap-modal.js',
						options: { gzip: true }
					},
					{
						src: 'vendor/bower/jquery.lazyload/jquery.lazyload.min.js',
						dest: ''+folder+'/vendor/bower/jquery.lazyload/jquery.lazyload.min.js',
						options: { gzip: true }
					},
					{
						src: 'vendor/bower/zeroclipboard/ZeroClipboard.min.js',
						dest: ''+folder+'/vendor/bower/zeroclipboard/ZeroClipboard.min.js'
					},
					{
						src: 'vendor/bower/zeroclipboard/ZeroClipboard.swf',
						dest: ''+folder+'/vendor/bower/zeroclipboard/ZeroClipboard.swf'
					},
					{
						src: 'vendor/bower/jasny-bootstrap/js/fileinput.js',
						dest: ''+folder+'/vendor/bower/jasny-bootstrap/js/fileinput.js'
					},
					{
						src: 'vendor/bower/jquery.fileapi/FileAPI/FileAPI.min.js',
						dest: ''+folder+'/vendor/bower/jquery.fileapi/FileAPI/FileAPI.min.js'
					},
					{
						src: 'vendor/bower/jquery.fileapi/jcrop/jquery.Jcrop.min.js',
						dest: ''+folder+'/vendor/bower/jquery.fileapi/jcrop/jquery.Jcrop.min.js'
					},
					{
						src: 'vendor/bower/jquery.fileapi/statics/jquery.modal.js',
						dest: ''+folder+'/vendor/bower/jquery.fileapi/statics/jquery.modal.js'
					},
					{
						src: 'vendor/bower/jquery.fileapi/FileAPI/FileAPI.exif.js',
						dest: ''+folder+'/vendor/bower/jquery.fileapi/FileAPI/FileAPI.exif.js'
					},
					{
						src: 'vendor/bower/jquery.fileapi/jquery.fileapi.min.js',
						dest: ''+folder+'/vendor/bower/jquery.fileapi/jquery.fileapi.min.js'
					},
					{
						src: 'vendor/bower/toastr/toastr.min.js',
						dest: ''+folder+'/vendor/bower/toastr/toastr.min.js'
					},
					{
						src: 'vendor/bower/toastr/toastr.min.css',
						dest: ''+folder+'/vendor/bower/toastr/toastr.min.css'
					},
					{
						src: 'vendor/bower/backbone-autocomplete/src/backbone.autocomplete.js',
						dest: ''+folder+'/vendor/bower/backbone-autocomplete/src/backbone.autocomplete.js'
					},
					{
						src: 'vendor/bower/backbone-autocomplete/src/backbone.autocomplete-min.css',
						dest: ''+folder+'/vendor/bower/backbone-autocomplete/src/backbone.autocomplete-min.css'
					}					
				],
			}

		}
		//finish grunts3

	});

	// Grunt contribution tasks.
	grunt.loadNpmTasks("grunt-contrib-jshint");
	grunt.loadNpmTasks('grunt-s3');
	grunt.loadNpmTasks("grunt-contrib-uglify");
	grunt.loadNpmTasks('grunt-jslint');
        

	// When running the default Grunt command, just lint the code.
	grunt.registerTask("default", [
		"uglify",
	]);
	grunt.registerTask("ie", [
		"jshint",
	]);
};
