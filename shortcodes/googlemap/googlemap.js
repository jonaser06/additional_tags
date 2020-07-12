/**
 * Shortcode Google map
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

(function() {

	"use strict";

	var googlemap = {
		'inited': false,
		'count': 0,
		'geocoder': null,
		'maps': []
	};
	var timer = null;
	
	jQuery(document).ready(function(e){
		var container = jQuery('body');
		var gmap = container.find('.sc_googlemap:not(.inited)');
		if (gmap.length > 0) {
			if (timer !== null) clearTimeout(timer);
			// Init Google map after all other elements (i.e. slider)
			timer = setTimeout(function() {
					themerex_sc_googlemap_init(e, container);
					}, gmap.parents('.elementor-element-editable').length > 0 ? 500 : 0);
		}
	});
	
	function themerex_sc_googlemap_init(e, container) {
		
		if (typeof google.maps === 'undefined') {
			if (timer !== null) clearTimeout(timer);
			timer = setTimeout(function() {
				themerex_sc_googlemap_init(e, container);
			}, 100);
			return;
		}

		if (container === undefined) container = jQuery('body');
		
		var gmap = container.find('.sc_googlemap:not(.inited)');
		if (gmap.length > 0) {
			gmap.each(function () {
				if (jQuery(this).parents('div:hidden,article:hidden').length > 0) return;
				var map 		= jQuery(this).addClass('inited');
				var map_id		= map.attr('id');
				var map_zoom	= map.data('zoom');
				var map_style	= map.data('style');
				var map_center  = map.data('center');
				var map_cluster_icon = map.data('cluster-icon');
				var map_markers = [];
				map.find('.sc_googlemap_marker').each(function() {
					var marker = jQuery(this);
					map_markers.push({
						icon:			marker.data('icon'),
						icon_retina:	marker.data('icon_retina'),
						icon_width:		marker.data('icon_width'),
						icon_height:	marker.data('icon_height'),
						address:		marker.data('address'),
						latlng:			marker.data('latlng'),
						animation:		marker.data('animation') == 'drop'
											? google.maps.Animation.DROP
											: (marker.data('animation') == 'bounce'
												? google.maps.Animation.BOUNCE
												: false
												),
						description:	marker.data('description'),
						title:			marker.data('title')
					});
				});
				themerex_sc_googlemap_create( map, {
					style: map_style,
					zoom: map_zoom,
					center: map_center,
					cluster_icon: map_cluster_icon,
					markers: map_markers
					}
				);
			});
		}
	}
	
	
	function themerex_sc_googlemap_create(map, coords) {
		if (!googlemap.inited) themerex_sc_googlemap_init_styles();
//		try {
			var id = map.attr('id');
			googlemap.count++;
			// Change id if already exists on this page
			if (typeof googlemap.maps[id] !== 'undefined') {
				id += '_copy' + googlemap.count;
				map.attr('id', id);
			}
			var center = null;
			if (coords.center) {
				center = (''+coords.center).split(',');
				center = center.length == 2 ? new google.maps.LatLng(center[0], center[1]) : null;
			}
			googlemap.maps[id] = {
				dom: map.get(0),
				markers: coords.markers,
				geocoder_request: false,
				cluster: null,
				clusterIcon: coords.cluster_icon,
				opt: {
					center: center,
					scrollwheel: false,
					scaleControl: false,
					disableDefaultUI: false,
					zoom: coords.zoom,
					zoomControl: true,
					panControl: true,
					mapTypeControl: false,
					streetViewControl: false,
					overviewMapControl: false,
					styles: THEMEREX_GLOBALS['googlemap_styles'][coords.style ? coords.style : 'default'],
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			};
			themerex_sc_googlemap_build(id);
//		} catch (e) {
//			console.log(THEMEREX_GLOBALS['msg_sc_googlemap_not_avail']);
//		};
	}
	
	function themerex_sc_googlemap_refresh() {
		for (var id in googlemap.maps) {
			themerex_sc_googlemap_build(id);
		}
	}
	
	function themerex_sc_googlemap_build(id) {
		// Create map
		googlemap.maps[id].map = new google.maps.Map(googlemap.maps[id].dom, googlemap.maps[id].opt);

		// Prepare maps bounds
		if (googlemap.maps[id].opt['zoom'] < 1)
			googlemap.maps[id].bounds = new google.maps.LatLngBounds();
		else
			googlemap.maps[id].map.setZoom(googlemap.maps[id].opt['zoom']);
			
		// Add markers
		for (var i=0; i < googlemap.maps[id].markers.length; i++)
			googlemap.maps[id].markers[i].inited = false;
		themerex_sc_googlemap_add_markers(id);

		// Add resize listener
		jQuery(document).on('action.resize_trx_addons', function() {
			if (googlemap.maps[id].map)
				googlemap.maps[id].map.setCenter(googlemap.maps[id].opt['center']);
		});
	}
	
	function themerex_sc_googlemap_add_markers(id) {
		
		var inited = 0;
		
		for (var i=0; i < googlemap.maps[id].markers.length; i++) {
			
			if (googlemap.maps[id].markers[i].inited) {
				inited++;
				continue;
			}
			
			if (googlemap.maps[id].markers[i].latlng == '') {
				
				if (googlemap.maps[id].geocoder_request!==false) continue;
				
				if (!googlemap.geocoder) googlemap.geocoder = new google.maps.Geocoder();
				googlemap.maps[id].geocoder_request = i;
				googlemap.geocoder.geocode({address: googlemap.maps[id].markers[i].address}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						try {
							var idx = googlemap.maps[id].geocoder_request;
							if (results[0].geometry.location.lat && results[0].geometry.location.lng)
								googlemap.maps[id].markers[idx].latlng = '' + results[0].geometry.location.lat()
																		+ ',' + results[0].geometry.location.lng();
							else
								googlemap.maps[id].markers[idx].latlng = results[0].geometry.location.toString().replace(/\(\)/g, '');
							googlemap.maps[id].geocoder_request = false;
							setTimeout(function() { 
								themerex_sc_googlemap_add_markers(id);
								}, 200);
						} catch(e) {
							// Do nothing
						}
					} else
						dcl(THEMEREX_GLOBALS['msg_sc_googlemap_geocoder_error'] + ' ' + status);
				});
			
			} else {
				
				// Prepare marker object
				var latlngStr = googlemap.maps[id].markers[i].latlng.split(',');
				var markerInit = {
					map: googlemap.maps[id].map,
					position: new google.maps.LatLng(latlngStr[0], latlngStr[1]),
					clickable: googlemap.maps[id].markers[i].description!=''
				};
				if (googlemap.maps[id].markers[i].icon) {
					markerInit.icon = googlemap.maps[id].markers[i].icon_width > 0 && googlemap.maps[id].markers[i].icon_height > 0
										? new google.maps.MarkerImage(googlemap.maps[id].markers[i].icon, null, null, null, new google.maps.Size(googlemap.maps[id].markers[i].icon_width, googlemap.maps[id].markers[i].icon_height))
										: googlemap.maps[id].markers[i].icon;
				}
				if (googlemap.maps[id].markers[i].title) {
					markerInit.title = googlemap.maps[id].markers[i].title;
				}
				if (googlemap.maps[id].markers[i].animation) {
					markerInit.animation = googlemap.maps[id].markers[i].animation;
				}
				googlemap.maps[id].markers[i].marker = new google.maps.Marker(markerInit);
				
				// Set Map center
				if (googlemap.maps[id].opt['center'] == null 
						&& (googlemap.maps[id].markers.length == 1 || googlemap.maps[id].opt['zoom'] > 0)
				) {
					googlemap.maps[id].opt['center'] = markerInit.position;
					googlemap.maps[id].map.setCenter(googlemap.maps[id].opt['center']);				
				}
				
				// Add description window
				if (googlemap.maps[id].markers[i].description!='') {
					// Create info window for the marker
					googlemap.maps[id].markers[i].infowindow = new google.maps.InfoWindow({
						content: '<div class="sc_googlemap_info_window">' + googlemap.maps[id].markers[i].description + '</div>'
					});
					// Open info window on click on the marker
					google.maps.event.addListener(googlemap.maps[id].markers[i].marker, "click", function(e) {
						var latlng = e.latLng.toString().replace("(", '').replace(")", "").replace(" ", "");
						for (var j=0; j < googlemap.maps[id].markers.length; j++) {
							if (themerex_googlemap_compare_latlng(latlng, googlemap.maps[id].markers[j].latlng)) {
								googlemap.maps[id].opened_marker = googlemap.maps[id].markers[j];
								// Zoom map and center to the marker
								googlemap.maps[id].old_zoom = googlemap.maps[id].map.getZoom();
								googlemap.maps[id].map.setZoom(Math.max(16, googlemap.maps[id].old_zoom + 2));
								googlemap.maps[id].old_center = googlemap.maps[id].map.getCenter();
								var center = googlemap.maps[id].markers[j].latlng.split(',');
								googlemap.maps[id].map.setCenter(new google.maps.LatLng(center[0], center[1]));
								// Stop animation
								if (googlemap.maps[id].markers[j].animation) {
									googlemap.maps[id].markers[j].marker.setAnimation(null);
								}
								// Open info window
								googlemap.maps[id].markers[j].infowindow.open(
									googlemap.maps[id].map,
									googlemap.maps[id].markers[j].marker
								);
								break;
							}
						}
					});
					// Resume animation and restore zoom after the info window is closed
					google.maps.event.addListener(googlemap.maps[id].markers[i].infowindow, 'closeclick', function(){
						// Restore default zoom
						googlemap.maps[id].map.setZoom(googlemap.maps[id].old_zoom);
						googlemap.maps[id].map.setCenter(googlemap.maps[id].old_center);
						// Resume animation
						if (googlemap.maps[id].opened_marker.animation) {
							googlemap.maps[id].opened_marker.marker.setAnimation(googlemap.maps[id].opened_marker.animation);
						}
					});
				}
				
				googlemap.maps[id].markers[i].inited = true;
				inited++;

				if (googlemap.maps[id].opt['zoom'] < 1) {
					googlemap.maps[id].bounds.extend(markerInit.position);
				}
			}
		}
		
		// If all markers inited
		if (inited > 1 && inited == googlemap.maps[id].markers.length) {
			var markers = [];
			for (i = 0; i < googlemap.maps[id].markers.length; i++)
				markers.push(googlemap.maps[id].markers[i].marker);
			// Make Cluster
			googlemap.maps[id].cluster = new MarkerClusterer(googlemap.maps[id].map, markers, {
				maxZoom: 18,
				gridSize: 60,
				styles: [
					{
					url: googlemap.maps[id].clusterIcon,
					width: 48,
					height: 48,
					textColor: "#fff"
					}
				]
			});
			// Fit Bounds
			if (googlemap.maps[id].opt['zoom'] < 1) {
				googlemap.maps[id].map.fitBounds(googlemap.maps[id].bounds);
				googlemap.maps[id].map.panToBounds(googlemap.maps[id].bounds);
			}
		}
	}
	
	// Compare two latlng strings
	function themerex_googlemap_compare_latlng(l1, l2) {
		var l1 = l1.replace(/\s/g, '', l1).split(',');
		var l2 = l2.replace(/\s/g, '', l2).split(',');
		var m0 = Math.min(l1[0].length, l2[0].length);
		l1[0] = Number(l1[0]).toFixed(m0);
		l2[0] = Number(l2[0]).toFixed(m0);
		var m1 = Math.min(l1[1].length, l2[1].length);
		l1[1] = Number(l1[1]).toFixed(m1);
		l2[1] = Number(l2[1]).toFixed(m1);
		return l1[0]==l2[0] && l1[1]==l2[1];
	}
	
	
	// Add styles for Google map
	function themerex_sc_googlemap_init_styles() {
		THEMEREX_GLOBALS['googlemap_styles'] = {
			'default': [],
			'invert': [ { "stylers": [ { "invert_lightness": true }, { "visibility": "on" } ] } ],
			'dark': [{"featureType":"landscape","stylers":[{ "invert_lightness": true },{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","stylers":[{"saturation":-100},{"lightness":51},{"visibility":"simplified"}]},{"featureType":"road.highway","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"road.arterial","stylers":[{"saturation":-100},{"lightness":30},{"visibility":"on"}]},{"featureType":"road.local","stylers":[{"saturation":-100},{"lightness":40},{"visibility":"on"}]},{"featureType":"transit","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"administrative.province","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":-25},{"saturation":-100}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]}],
			'simple': [
				{
					stylers: [
						{ hue: "#00ffe6" },
						{ saturation: -20 }
					]
				},{
					featureType: "road",
					elementType: "geometry",
					stylers: [
						{ lightness: 100 },
						{ visibility: "simplified" }
					]
				},{
					featureType: "road",
					elementType: "labels",
					stylers: [
						{ visibility: "off" }
					]
				}
			],
			'greyscale': [
				{
					"stylers": [
						{ "saturation": -100 }
					]
				}
			],
			'greyscale2': [
				{
					"featureType": "landscape",
					"stylers": [
						{ "hue": "#FF0300" },
						{ "saturation": -100 },
						{ "lightness": 20.4705882352941 },
						{ "gamma": 1 }
					]
				},
				{
					"featureType": "road.highway",
					"stylers": [
						{ "hue": "#FF0300" },
						{ "saturation": -100 },
						{ "lightness": 25.59999999999998 },
						{ "gamma": 1 }
					]
				},
				{
					"featureType": "road.arterial",
					"stylers": [
						{ "hue": "#FF0300" },
						{ "saturation": -100 },
						{ "lightness": -22 },
						{ "gamma": 1 }
					]
				},
				{
					"featureType": "road.local",
					"stylers": [
						{ "hue": "#FF0300" },
						{ "saturation": -100 },
						{ "lightness": 21.411764705882348 },
						{ "gamma": 1 }
					]
				},
				{
					"featureType": "water",
					"stylers": [
						{ "hue": "#FF0300" },
						{ "saturation": -100 },
						{ "lightness": 21.411764705882348 },
						{ "gamma": 1 }
					]
				},
				{
					"featureType": "poi",
					"stylers": [
						{ "hue": "#FF0300" },
						{ "saturation": -100 },
						{ "lightness": 4.941176470588232 },
						{ "gamma": 1 }
					]
				}
			],
			'style1': [{
				"featureType": "landscape",
				"stylers": [
					{ "hue": "#FF0300"	},
					{ "saturation": -100 },
					{ "lightness": 20.4705882352941 },
					{ "gamma": 1 }
				]
			},
				{
					"featureType": "road.highway",
					"stylers": [
						{ "hue": "#FF0300" },
						{ "saturation": -100 },
						{ "lightness": 25.59999999999998 },
						{ "gamma": 1 }
					]
				},
				{
					"featureType": "road.arterial",
					"stylers": [
						{ "hue": "#FF0300" },
						{ "saturation": -100 },
						{ "lightness": -22 },
						{ "gamma": 1 }
					]
				},
				{
					"featureType": "road.local",
					"stylers": [
						{ "hue": "#FF0300" },
						{ "saturation": -100 },
						{ "lightness": 21.411764705882348 },
						{ "gamma": 1 }
					]
				},
				{
					"featureType": "water",
					"stylers": [
						{ "hue": "#FF0300" },
						{ "saturation": -100 },
						{ "lightness": 21.411764705882348 },
						{ "gamma": 1 }
					]
				},
				{
					"featureType": "poi",
					"stylers": [
						{ "hue": "#FF0300" },
						{ "saturation": -100 },
						{ "lightness": 4.941176470588232 },
						{ "gamma": 1 }
					]
				}
			],
			'style2': [
				{
					"featureType": "landscape",
					"stylers": [
						{
							"hue": "#007FFF"
						},
						{
							"saturation": 100
						},
						{
							"lightness": 156
						},
						{
							"gamma": 1
						}
					]
				},
				{
					"featureType": "road.highway",
					"stylers": [
						{
							"hue": "#FF7000"
						},
						{
							"saturation": -83.6
						},
						{
							"lightness": 48.80000000000001
						},
						{
							"gamma": 1
						}
					]
				},
				{
					"featureType": "road.arterial",
					"stylers": [
						{
							"hue": "#FF7000"
						},
						{
							"saturation": -81.08108108108107
						},
						{
							"lightness": -6.8392156862745
						},
						{
							"gamma": 1
						}
					]
				},
				{
					"featureType": "road.local",
					"stylers": [
						{
							"hue": "#FF9A00"
						},
						{
							"saturation": 7.692307692307736
						},
						{
							"lightness": 21.411764705882348
						},
						{
							"gamma": 1
						}
					]
				},
				{
					"featureType": "water",
					"stylers": [
						{
							"hue": "#0093FF"
						},
						{
							"saturation": 16.39999999999999
						},
						{
							"lightness": -6.400000000000006
						},
						{
							"gamma": 1
						}
					]
				},
				{
					"featureType": "poi",
					"stylers": [
						{
							"hue": "#00FF60"
						},
						{
							"saturation": 17
						},
						{
							"lightness": 44.599999999999994
						},
						{
							"gamma": 1
						}
					]
				}
			],
			'style3':  [
				{
					"featureType": "landscape",
					"stylers": [
						{
							"hue": "#FFA800"
						},
						{
							"saturation": 17.799999999999997
						},
						{
							"lightness": 152.20000000000002
						},
						{
							"gamma": 1
						}
					]
				},
				{
					"featureType": "road.highway",
					"stylers": [
						{
							"hue": "#007FFF"
						},
						{
							"saturation": -77.41935483870967
						},
						{
							"lightness": 47.19999999999999
						},
						{
							"gamma": 1
						}
					]
				},
				{
					"featureType": "road.arterial",
					"stylers": [
						{
							"hue": "#FBFF00"
						},
						{
							"saturation": -78
						},
						{
							"lightness": 39.19999999999999
						},
						{
							"gamma": 1
						}
					]
				},
				{
					"featureType": "road.local",
					"stylers": [
						{
							"hue": "#00FFFD"
						},
						{
							"saturation": 0
						},
						{
							"lightness": 0
						},
						{
							"gamma": 1
						}
					]
				},
				{
					"featureType": "water",
					"stylers": [
						{
							"hue": "#007FFF"
						},
						{
							"saturation": -77.41935483870967
						},
						{
							"lightness": -14.599999999999994
						},
						{
							"gamma": 1
						}
					]
				},
				{
					"featureType": "poi",
					"stylers": [
						{
							"hue": "#007FFF"
						},
						{
							"saturation": -77.41935483870967
						},
						{
							"lightness": 42.79999999999998
						},
						{
							"gamma": 1
						}
					]
				}
			]
		};
		jQuery(document).trigger('action.add_googlemap_styles');
		googlemap.inited = true;
	}

})();