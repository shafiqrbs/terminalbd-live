/*
	inputFileZone.js plugin works with jQuery
	Transform input[type="file"] to dropzone !
	by aZerato
*/
(function($) {
		
		var defaults = {
			message: 'Drop file ...',
			messageFile: 'Ready to upload', // if preview is impossible due browser
			zIndex: 1,
			previewImages: true
		};

		/*
			Plugin declaration
		*/
		$.inputFileZone = function(elem, options) {

			var plugin = this;

			plugin.options = options;

			plugin.input = $(elem);
			plugin.closestDiv = null;
			plugin.spanEl = null;

			plugin.browser = null;

			/*
				publics
			*/
			plugin.init = function() {
				plugin.start();
			};

			plugin.start = function() {
				StylingDropZone();
				EventsDropZone();
			};
			
			/* 
				Privates
			*/
			function StylingDropZone() {
				plugin.input.wrap('<div class="closest-dropzone"></div>');

				plugin.closestDiv = plugin.input.closest('.closest-dropzone');
				
				plugin.closestDiv.css({
					'width': '100%',
					'height': '100%',
					'position': 'relative',
					'background-size': 'cover',
					'text-align': 'center',
					'-webkit-transition': 'all 0.3s ease-out',
					'-moz-transition': 'all 0.3s ease-out',
					'-ms-transition': 'all 0.3s ease-out',
					'-o-transition': 'all 0.3s ease-out',
					'transition': 'all 0.3s ease-out'
				});
					
				plugin.closestDiv.append('<span>' + plugin.options.message + '</span>');

				plugin.input.css({
					'z-index': plugin.options.zIndex + 1,
					'top': 0,
					'left': 0,
					'cursor': 'pointer !important',
					'height': '100%',
					'width': '100%',
					'position': 'absolute',
					'opacity': 0,
					'filter': 'alpha(opacity=0)'
				});

				plugin.spanEl = plugin.closestDiv.find('span');

				plugin.spanEl.css({
					'z-index': plugin.options.zIndex,
					'top': ( plugin.closestDiv.height() - plugin.spanEl.height() ) / 2 ,
					'left': 0,
					'width': '100%',
					'position': 'absolute',
					'word-wrap': 'break-word'
				});
			}

			function EventsDropZone() {
				plugin.input.on({
					dragover: dragOver,
					dragenter: dragEnter,
					dragleave: dragExit,
					dragexit: dragExit
				});

				// IE Legacy need to add events ! :(
				Browser();
				if (plugin.browser.msie && parseInt(plugin.browser.version, 10) < 10) {
					plugin.input.on({
						drop: drop
					});
				} else {
					plugin.input.on({
						change: dropOrChange,
						drop: dropOrChange
					});
				}
			}

			function dropOrChange(event) {
				killHandler(event);

				var file = event.target.files[0];

				//IE 10!
				if(!file) {
					file = event.originalEvent.dataTransfer.files[0];
				}

				plugin.closestDiv.addClass('dropzone-upload')
					.find('span')
					.css({'color': 'white'})
					.html(file.name);

				if(plugin.options.previewImages) {
					// Create Thumbnail if type === image
					if(file.type.match('image.*')) {
						var reader = new FileReader();
						reader.onload = function(event){
							plugin.closestDiv.data('previouscss', plugin.closestDiv.css('background-image')+ ';'+ plugin.closestDiv.css('background-color'));

							plugin.closestDiv.css({
								'background-image': 'url(' + event.target.result + ')'
							});
						};
						reader.readAsDataURL(file);
					} else {
						var previousCss = plugin.closestDiv.data('previouscss').split(';');
						if(previousCss) {
							plugin.closestDiv.css({
								'background-image': previousCss[0],
								'background-color': previousCss[1]
							});
						}
					}
				}
			}

			function drop(event) {
				killHandler(event);

				plugin.closestDiv.addClass('dropzone-upload')
					.find('span')
					.css({'color': 'white'})
					.html(plugin.options.messageFile);
			}

			function dragEnter(event) {
				killHandler(event);
			}

			function dragOver(event) {
				killHandler(event);

				plugin.closestDiv.addClass('dropzone-upload');
			}
			
			function dragExit(event) {
				killHandler(event);

				plugin.closestDiv.removeClass('dropzone-upload');
			}

			function killHandler(event) {
				event.stopPropagation();
				event.preventDefault();
			}

			// Check browser 
			function Browser() {
				var browser = {
					msie : false,
					version : 0
				};

				if(navigator.userAgent.match(/MSIE ([0-9]+)\./)){
					browser.msie = true;
					browser.version = RegExp.$1;
				}

				plugin.browser = browser;
			}

			/*
				initialize
			*/
			plugin.init();
		};

		// Add to jquery functions library
		$.fn.extend({
			inputFileZone: function(options, arg) {
				if (options && typeof(options) == 'object') {
					options = $.extend({}, defaults, options );
				}

				if(!options) options = defaults;

				this.each(function() {
					new $.inputFileZone(this, options);
				});
				return;
			}
		});
})(jQuery);