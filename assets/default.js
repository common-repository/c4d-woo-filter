var c4dWooFilter = {};
(function($){
	"use strict";
	//// START FUNCTIONS  //////
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	// define filters
	c4dWooFilter.options = {
		container: '.c4d_woo_filter_products_wrap .products',
		noproduct: 'woocommerce-info',
		countNumber: '.woocommerce-result-count',
		pagination: '.woocommerce-pagination',
		loadStyle: false, // false/loadmore/scroll
		loadingIcon: '<div class="c4d-woo-filter-loading-icon"><span class="ball"></span></div>',
		loadMore: {
			running: false,
			text: 'Load More',
			icon: '',
			end: 'All Products Loaded'
		},
		beforeActions: [],
		afterActions: []
	};

	c4dWooFilter.filters = {
		'.woocommerce-ordering': {
			type: 'select',
			action: 'submit',
			dataIndex: '.woocommerce-ordering select.c4d-woo-filter-status-active',
			term: 'orderby',
			bf: function(self, els) {
				$(self).find('select').addClass('c4d-woo-filter-status-active');
			},
			buildData: function() {},
			af: function(self, els) {
				$(self).find('select').removeClass('c4d-woo-filter-status-active');
			},
		},
		'.c4d-woo-cpp-form': {
			type: 'select',
			action: 'submit',
			dataIndex: '.c4d-woo-cpp-form select.c4d-woo-filter-status-active',
			term: 'product_perpage',
			bf: function(self, els) {
				$(self).find('select').addClass('c4d-woo-filter-status-active');
			},
			buildData: function() {},
			af: function(self, els) {
				$(self).find('select').removeClass('c4d-woo-filter-status-active');
			},
		},
		'.widget_product_categories li a': {
			type: 'a',
			action: 'click',
			dataIndex: '.widget_product_categories li a.c4d-woo-filter-status-active',
			term: '',
			bf: function(self, els) {
				var filterClass = $(self).hasClass('c4d-woo-filter-status-active');
				$(self).parents('.widget_product_categories').find('a').removeClass('c4d-woo-filter-status-active');
				if (filterClass) {
					$(self).addClass('c4d-woo-filter-status-active');
				} else {
					$(self).removeClass('c4d-woo-filter-status-active');
				}
			},
			buildData: function() {},
			af: function() {},
		},
		'.widget_price_filter .price_slider_wrapper .price_slider': {
			type: 'form',
			action: 'mouseup',
			dataIndex: '.widget_price_filter form.c4d-woo-filter-status-active',
			term: '',
			bf: function(self, els) {
				$(self).parents('.widget_price_filter').find('form').addClass('c4d-woo-filter-status-active');
			},
			buildData: function() {},
			af: function() {},
		},
		'.widget_layered_nav a': {
			type: 'a',
			action: 'click',
			dataIndex: '.widget_layered_nav a.c4d-woo-filter-status-active',
			term: '',
			bf: function(self, els) {

			},
			buildData: function(self, els) {

			},
			af: function(self, els) {}
		},
		'.widget_rating_filter a': {
			type: 'a',
			action: 'click',
			dataIndex: '.widget_rating_filter a.c4d-woo-filter-status-active',
			term: '',
			bf: function(self, els) {

			},
			buildData: function(self, els) {

			},
			af: function(self, els) {}
		},
		'.woocommerce-widget-layered-nav-dropdown a': {
			type: 'form',
			action: 'click',
			dataIndex: '.widget_rating_filter a.c4d-woo-filter-status-active',
			term: '',
			bf: function(self, els) {

			},
			buildData: function(self, els) {

			},
			af: function(self, els) {}
		},
		'.woocommerce-pagination a': {
			type: 'a',
			action: 'click',
			dataIndex: '.woocommerce-pagination a.c4d-woo-filter-status-active',
			term: '',
			bf: function(self, els) {

			},
			buildData: function(self, els) {

			},
			af: function(self, els) {}
		},
		'[class*="c4d-woo-filter-template"] a': {
			type: 'a',
			action: 'click',
			dataIndex: '[class*="c4d-woo-filter-template"] a.c4d-woo-filter-status-active',
			term: '',
			bf: function(self, els) {

			},
			buildData: function(self, els) {

			},
			af: function(self, els) {
				if ($(self).parents('.c4d-woo-filter-template-sort').length > 0) {
					if (typeof c4dWooFilter.datas.orderby != 'undefined') {
						delete c4dWooFilter.datas.orderby;
					}
				}
			}
		},
		'.c4d-woo-category a': {
			type: 'a',
			action: 'click',
			dataIndex: '.c4d-woo-category a.c4d-woo-filter-status-active',
			term: '',
			bf: function(self, els) {
				var filterClass = $(self).hasClass('c4d-woo-filter-status-active');
				$(self).parents('.c4d-woo-category').find('a').removeClass('c4d-woo-filter-status-active');
				if (filterClass) {
					$(self).addClass('c4d-woo-filter-status-active');
				} else {
					$(self).removeClass('c4d-woo-filter-status-active');
				}
			},
			buildData: function(self, els) {

			},
			af: function(self, els) {}
		}
	};

	c4dWooFilter.currentUrl = false;
	// list filtered
	c4dWooFilter.filtered = {}; // store filterd values

	c4dWooFilter.datas = {};

	c4dWooFilter.checkData = function(options){
		if (options.type == 'form') {
			var datas = $(options.dataIndex).serializeArray();
			$.each(datas, function(index, data){
				c4dWooFilter.datas[data.name] = data.value;
			});
		}
		if (options.type == 'a') {
			$(options.dataIndex).each(function(index, filter){
				var name = $(filter).data('filter-name'),
				minPrice = $(filter).data('min-price'),
				maxPrice = $(filter).data('max-price'),
				value = $(filter).data('filter-value');
				if (typeof name != 'undefined') {
					c4dWooFilter.datas[name] = value;
				}
				if (typeof minPrice != 'undefined') {
					c4dWooFilter.datas['min_price'] = minPrice;
				}
				if (typeof maxPrice != 'undefined') {
					c4dWooFilter.datas['max_price'] = maxPrice;
				}
			});
		}
		if (options.type == 'input') {
			if($(options.dataIndex).length > 0) {
				c4dWooFilter.datas[options.term] = $(options.dataIndex).html();
			}
		}
		if (options.type == 'select') {
			if($(options.dataIndex).length > 0) {
				c4dWooFilter.datas[options.term] = $(options.dataIndex).val();
			}
		}
	};

	c4dWooFilter.requestResponse = function(res) {
		c4dWooFilter.updateHTML(res);
	};

	c4dWooFilter.updateHTML = function(res) {
		var newItems = $(res).find(c4dWooFilter.options.container).html(),
		countNumber = $(res).find(c4dWooFilter.options.countNumber),
		pagination = $(res).find(c4dWooFilter.options.pagination).html();
		pagination =  pagination == undefined ? '' : pagination;

		newItems = newItems == undefined ? '<div class="'+ c4dWooFilter.options.noproduct +'">' + $(res).find('.'+c4dWooFilter.options.noproduct).html() + '</div>' : newItems;
		$(c4dWooFilter.options.countNumber).html(countNumber);
		// replace list product
		if (c4dWooFilter.options.loadMore.running) { // scroll or load more style
			$(c4dWooFilter.options.container).append(newItems);
			$(c4dWooFilter.options.pagination).html(pagination);
			c4dWooFilter.options.loadMore.running = false;
		} else { // replace
			$(c4dWooFilter.options.container).html(newItems);
			$(c4dWooFilter.options.pagination).html(pagination);
		}
	};

	c4dWooFilter.toggleLoadingIcon = function() {
		var container = $(c4dWooFilter.options.container),
		loadingIcon = $('.c4d-woo-filter-loading-icon');
		container.css('position', 'relative');
		if (loadingIcon.length < 1) {
			container.after(c4dWooFilter.options.loadingIcon);
		}
		$('body').toggleClass('c4d-woo-filter-loading-active');
	}

	c4dWooFilter.loadMore = function() {
		if (!$('body').hasClass('c4d-woo-filter-load-more-active')) return;
		var pag = $(c4dWooFilter.options.pagination),
		html = '';
		html = '<div class="c4d-woo-filter-load-more">';
		html += '<div class="text">'+c4dWooFilter.options.loadMore.text+'</div>';
		html += '<div class="icon">'+c4dWooFilter.options.loadMore.icon+'</div>';
		html += '<div class="end">'+c4dWooFilter.options.loadMore.end+'</div>';
		html += '</div>';

		pag.after(html);

		$('body').on('click', '.c4d-woo-filter-load-more', function(){
			c4dWooFilter.options.loadMore.running = true;
			var pages = pag.find('li .page-numbers');
			$.each(pages, function(index, page){
				if ($(page).hasClass('current')) {
					$(pages[index + 1]).trigger('click');
					if ($(pages[index + 3]).length < 1) {
						$('.c4d-woo-filter-load-more').addClass('end-page');
					}
					return false;
				}
			});
		});

		if ($('body').hasClass('c4d-woo-filter-load-more-scroll')) {
			var startScroll = 0,
			scrollButton = $('.c4d-woo-filter-load-more');
			if (scrollButton.length > 0) {
				$(window).scroll(function(event) {
					if (c4dWooFilter.options.loadMore.running == true) return false;
					var end = scrollButton.offset().top,
					scroll = $(this).scrollTop(),
					viewEnd = scroll + $(window).height(),
					distance = end - viewEnd;
					if (scroll > startScroll && distance < -100) {
						scrollButton.trigger('click');
					}
					startScroll = scroll;
				});
			}
		}

		c4dWooFilter.options.afterActions.push('loadMoreButtonStatus');
	}

	c4dWooFilter.loadMoreButtonStatus = function() {
		var loadMoreButton = $('.c4d-woo-filter-load-more'),
		pagination = $(c4dWooFilter.options.pagination),
		lastPage = pagination.find('li:last-child .page-numbers');

		if (!lastPage.hasClass('current')) {
			loadMoreButton.removeClass('end-page');
		}
		if (pagination.find('li').length < 1) {
			$(loadMoreButton).addClass('no-products');
		} else {
			$(loadMoreButton).removeClass('no-products');
		}
	}

	c4dWooFilter.initFilter = function() {
		$.each(c4dWooFilter.filters, function(filter, els){
			// on event
			$('body').on(els.action, filter, function(event){
				var self = $(this);
				// stop event

				c4dWooFilter.datas = {};

				event.preventDefault();
				// status active
				if (els.type == 'a') {
					self.toggleClass('c4d-woo-filter-status-active');
					self.siblings().removeClass('c4d-woo-filter-status-active');
					self.parent().siblings().find('a').removeClass('c4d-woo-filter-status-active');
				}
				// before action
				$.each(c4dWooFilter.options.beforeActions, function(index, functionName){
					c4dWooFilter[functionName](this);
				});
				if (typeof els.bf != 'undefined' ) {
					els.bf(this, els);
				}
				// loading icon
				c4dWooFilter.toggleLoadingIcon();

				// grab filter datas
				$.each(c4dWooFilter.filters, function(selector, options){
					c4dWooFilter.checkData(options);
				});

				// custom data of each filter
				if (typeof els.data != 'undefined' ) {
					els.buildData(this, els);
				}

				// after data
				if (typeof els.af != 'undefined' ) {
					els.af(this, els);
				}

				// check if element has link
				var ajaxUrl = window.location.href,
				herf = $(this).attr('href');

				if (herf && herf != '') {
					if (self.hasClass('c4d-woo-filter-status-active')) {
						c4dWooFilter.currentUrl = herf;
					} else {
						c4dWooFilter.currentUrl = ajaxUrl;
					}
				}

				if (c4dWooFilter.currentUrl) {
					ajaxUrl = c4dWooFilter.currentUrl;
				}

				// ajax start
				var filterRequest = $.ajax({
					url: ajaxUrl,
					method: 'GET',
					data: c4dWooFilter.datas,
					dataType: 'html',
					beforeSend: function(res) {
						//c4dWooFilter.requestResponse(res);
					},
					fail: function(res) {
						c4dWooFilter.requestResponse(res);
						c4dWooFilter.toggleLoadingIcon();
					}
				}).done(function(res){
					c4dWooFilter.requestResponse(res);
					c4dWooFilter.toggleLoadingIcon();
					$.each(c4dWooFilter.options.afterActions, function(index, functionName){
						c4dWooFilter[functionName](self);
					});
					if (!$('body').hasClass('c4d-woo-filter-load-more-active')) {
						$([document.documentElement, document.body]).animate({
        			scrollTop: $(c4dWooFilter.options.container).offset().top - 100
    				}, 400);
					}
				});
				return false;
			});
		});
	}
	//// END FUNCTIONS  //////
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//// START SCRIPT //////

	$(document).ready(function(){
		// detect events
		c4dWooFilter.initFilter();

		// load more & croll load feature
		c4dWooFilter.loadMore();
	});
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//// END SCRIPT //////
})(jQuery);