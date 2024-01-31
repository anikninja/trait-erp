/** noinspection ES6ConvertVarToLetConst */
( function ( $, window, document, site, cart, lang, restoreHome, sys_alerts, filters, viewProduct ) {
    "use strict";
    /**
     * Countdown timer.
     *
     * @param {Object} $timerEl
     *      jQuery object for the countdown timer optional
     *      if not set, it will search DOM for element with `live_countdown` id
     * Data attributes
     *      @type {object} countdown
     *          for server side time.
     *          {'start':'M d, Y H:i:s', 'now':'M d, Y H:i:s'}
     *      @type {string} format
     *          output format (optional).
     *          default: "%d : %h : %m : %s"
     *          available placeholders
     *              %d => days
     *              %h => hours
     *              %m => minutes
     *              %s => seconds
     *      @type {boolean} leading_zero
     *          Optional, default: false
     *      @type {string} expired
     *          Message to show after countdown stopped. Default: 'EXPIRED'
     *      @type {boolean} paused
     * @return {countDownTimer}
     */
    function countDownTimer( $timerEl ) {
        var self = this;
        self.$timerEl = $timerEl;
        self.opts = { stop: 0, now: new Date().getTime() };
        self.deltas = { days: 0, hours: 0, minutes: 0, seconds: 0 };
        self.timer = 0;
        self.format = '%d : %h : %m : %s';
        self.setLeadingZeros = false;
        self.expired = "EXPIRED";
        var paused = false;
        /**
         * Initialize the Countdown Timer
         * @return {countDownTimer}
         */
        self.init = function(){
            if( self.$timerEl.attr('data-countdown') !== undefined ) {
                var options =JSON.parse( self.$timerEl.attr('data-countdown').replace( /'/g, '"' ) );
                if( options.stop.length === 0 ) showExpired();
                self.opts.stop = new Date( options.stop ).getTime();
                if( options.now && options.now.length > 0 ) self.opts.now = new Date( options.now ).getTime();
            }
            
            if( self.$timerEl.attr('data-format') !== undefined ) {
                self.format = self.$timerEl.attr('data-format');
            }
            if( self.$timerEl.attr('data-leading_zero') !== undefined && self.$timerEl.attr('data-leading_zero') === 'true' ) {
                self.setLeadingZeros = true;
            }
            if( self.$timerEl.attr('data-expired') !== undefined ) {
                self.expired = self.$timerEl.attr('data-expired');
            }
            if( options.paused && options.paused === true ) paused = true;
            updateDelta();
            calculate();
            self.$timerEl.trigger( 'timer.init', [ self.delta, self.deltas ] );
            if( ! paused ) {
                // start the timer
                self.startTimer();
            }
            
            return self;
        };
        /**
         * Start Countdown Loop
         * @return {countDownTimer}
         */
        self.startTimer = function() {
            if( paused === true ) {
                updateDelta();
                paused = false;
            }
            if( self.delta ) {
                countDown();
                self.$timerEl.trigger( 'timer.start', [ self.delta, self.deltas ] );
                self.timer = setInterval( countDown, 1000 );
            }
            return self;
        };
        /**
         * Stop Countdown Loop
         * @return {countDownTimer}
         */
        self.stopTimer = function() {
            paused = true;
            clearInterval( self.timer );
            self.$timerEl.trigger( 'timer.stop', [ self.delta, self.deltas ] );
            return self;
        };
        
        var updateDelta = function () {
            // Round off milliseconds
            self.delta = ( Math.round( self.opts.stop / 1000 ) - Math.round( self.opts.now / 1000 ) );
            // Seconds to milliseconds conversion
            self.delta *= 1000;
        };
        /**
         * Calculate
         * @return {void}
         */
        var calculate = function() {
            // Calculate Deltas
            self.deltas.days = Math.floor( self.delta / ( 1000 * 60 * 60 * 24 ) );
            self.deltas.hours = Math.floor( ( self.delta % ( 1000 * 60 * 60 * 24) ) / ( 1000 * 60 * 60 ) );
            self.deltas.minutes = Math.floor( ( self.delta % ( 1000 * 60 * 60) ) / ( 1000 * 60 ) );
            self.deltas.seconds = Math.floor( ( self.delta % ( 1000 * 60 ) ) / 1000 );
        };
        /**
         * The Countdown Loop
         * @return {void}
         */
        var countDown = function() {
            self.delta -= 1000;
            calculate();
            if( self.delta > 0 ) {
                updateTimerEl( self.deltas );
            } else {
                clearInterval( self.timer );
                showExpired();
            }
        };
        /**
         * Execute expire message and execute the callback
         * @return {void}
         */
        var showExpired = function() {
            self.$timerEl.html( self.expired );
            self.$timerEl.trigger( 'timer.expired', [ self.delta, self.deltas ] );
        };
        /**
         * Update Timer El
         * @param {Object} counts
         * @return {void}
         */
        var updateTimerEl = function( counts ) {
            if( self.setLeadingZeros ) for( var k in counts ) if( counts[k] < 10 ) counts[k] = '0' + counts[k];
            // update display
            self.$timerEl.html(
                self.format
                .replace( '%d', counts.days )
                .replace( '%h', counts.hours )
                .replace( '%m', counts.minutes )
                .replace( '%s', counts.seconds )
            );
        };
        return self.init();
    }
    /**
     * Parse String to JSON from data attribute
     * @param {jQuery} el
     * @param {string} data
     * @return {Object}
     */
    function parseData( el, data ) {
        data = el.data( data );
        if( data && 'object' !== typeof data ) {
            try {
                return JSON.parse( data )
            } catch ( e ) {
                return null;
            }
        }
        return data;
    }
    // jq helpers.
    $.extend( $.fn, {
        GS_Countdown: function() {
            $(this).each( function(){
                var timer = new countDownTimer( $(this) );
                $(this).data( 'GS_Countdown', timer )
            });
        },
        ac: function( value ) {
            return $(this).addClass( value );
        },
        rc: function( value ) {
            return $(this).removeClass( value );
        },
        parseData: function( data ) {
            return parseData( $(this), data );
        },
        /**
         * Check if el is flagged as processing
         * @param {boolean} [start=false] Optional. Set the flag as inProgress if it's not.
         * @return {boolean}
         */
        inProgress: function( start ) {
            start = start || false;
            var status = $(this).hasClass( processing );
            if ( ! status && start ) {
                $(this).setProgressing( true );
            }
            return status;
        },
        /**
         * Set processing css class flag.
         * @param {boolean} [start=true] Set or remove inProgress Flag.
         *
         * @return {jQuery}
         */
        setProgressing: function( start ) {
            start = false !== start;
            if ( start ) {
                $(this).ac( processing );
            } else {
                $(this).rc( processing );
            }
            return this;
        },
    } );
    
    var __CSRF__ = {},
        __CACHES__ = [];
    // csrf
    __CSRF__[site.csrf_token] = site.csrf_token_value;
    __CACHES__['prodModal'] = [];
    
    var addToWishList = 'add-to-wishlist',
        removeFromWishList = 'remove-wishlist',
        processing = 'processing',
        site_url = site.site_url,
        shop_url = site.shop_url,
        prod_url = site_url + 'product/',
        autoAjax = false,
        ajaxify = false,
        ajaxify2 = false,
        scrollTopShowOffset = ( $( document ).height() * .125 ),
        pageNow = m + '/' + v,
        StickyHeader = {
            isFixed: false,
            element: $('.cs-header'),
            fixedEl: $('.header-main'),
            fixedDistanceTop: 0,
            triggerY: 0,
            parentElement: $('body'),
            parentFixedClassName: 'scrollParent',
            fixedClassName: 'main-fixed',
            _currentTop: 0,
            init: function() {
                var self = this;
                this._getTriggerY();
                self._scrollForHome();
                $(window).on('scroll', function() {
                    self._scrollForHome();
                } );
                // window.getX = this._getTriggerY.bind(this);
            },
            _getTriggerY: function() {
                this.triggerY = this.element.offset().top + this.element.height();
                return this.triggerY;
            },
            _scrollForHome: function() {
                if ($(window).scrollTop() < 0 + this.triggerY ) {
                    this._cancelFixed();
                } else {
                    if ( ! this.isFixed) {
                        if (this.triggerY !== this._getTriggerY()) {
                            return;
                        }
                    }
                    this._setScrollFixed();
                }
            },
            _setScrollFixed: function() {
                if ( ! this.isFixed) {
                    this.isFixed = true;
                    this.parentElement.ac(this.parentFixedClassName);
                    this.fixedEl.css({
                        position: 'fixed',
                        top: '-60px'
                    });
                    this.fixedEl.animate( { top: 0 }, 550 );
                    this.element.ac(this.fixedClassName);
                }
            },
            _cancelFixed: function() {
                if (this.isFixed) {
                    this.isFixed = false;
                    this.parentElement.rc(this.parentFixedClassName);
                    this.fixedEl.css({
                        position: '',
                        top: '-60px'
                    });
                    this.element.rc(this.fixedClassName);
                }
            },
        },
        toFixed = function( input, precision ) {
            precision = precision || 2;
            return parseFloat( input ).toFixed( precision );
        },
        __debounce = function(func, wait, immediate) {
            var timeout, result;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) result = func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) result = func.apply(context, args);
                return result;
            };
        },
        lsGet = function( name ) {
            return localStorage.getItem( name );
        },
        lsSet = function( name, value ) {
            localStorage.setItem( name, value );
            return true;
        },
        lsDel = function( name ) {
            localStorage.removeItem( name );
        },
        winLoc = function() { return window.location.href; },
        
        lastSearch = {
            hash: '',
            data: []
        },
        AjaxErrorHandler = function ( jqXHR, textStatus ) {
            if( 'abort' === textStatus ) {
                return;
            } else {
                sa_alert('Error!', 'Ajax call failed, please try again or contact site owner.', 'error', true);
            }
        },
        validateEmail = function (email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        email_form = function () {
            var __HTML__ = `
            <div>
                <span class="text-bold padding-bottom-md">${lang.fill_form}</span>
                <hr class="swal2-spacer padding-bottom-xs" style="display: block;">
                <form action="${shop_url}send_message" id="message-form" class="padding-bottom-md">
                    <input type="hidden" name="${site.csrf_token}" value="${site.csrf_token_value}">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label for="form-name" class="sr-only">${lang.full_name}</label>
                            <input type="text" name="name" id="form-name" value="" class="form-control" placeholder="${lang.full_name}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label for="form-email" class="sr-only">${lang.email}</label>
                            <input type="email" name="email" id="form-email" value="" class="form-control" placeholder="${lang.email}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label for="form-subject" class="sr-only">${lang.subject}</label>
                            <input type="text" name="subject" id="form-subject" value="" class="form-control" placeholder="${lang.subject}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label for="form-message" class="sr-only">${lang.message}</label>
                            <textarea name="message" id="form-message" class="form-control" placeholder="${lang.message}" style="height:100px;"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            `;
        
            swal({
                title: lang.send_email_title,
                html: __HTML__,
                showCancelButton: true,
                allowOutsideClick: false,
                cancelButtonText: lang.cancel,
                confirmButtonText: lang.submit,
                preConfirm: function () {
                    return new Promise(function (resolve, reject) {
                        var email = $('#form-email').val();
                        if ( ! $( '#form-name' ).val() ) {
                            reject( lang.name + ' ' + lang.is_required );
                        }
                        if ( ! email ) {
                            reject( lang.email + ' ' + lang.is_required );
                        }
                        if ( ! $( '#form-subject' ).val() ) {
                            reject( lang.subject + ' ' + lang.is_required );
                        }
                        if ( ! $( '#form-message' ).val() ) {
                            reject( lang.message + ' ' + lang.is_required );
                        }
                        if ( ! validateEmail( email ) ) {
                            reject( lang.email_is_invalid );
                        }
                        resolve();
                    });
                },
                onOpen: function () {
                    $('#form-name').focus();
                },
            })
            .then(function () {
                $.ajax({
                    url: `${shop_url}send_message`,
                    type: 'POST',
                    data: $('#message-form').serialize(),
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return false;
                        } else {
                            sa_alert(data.status, data.message, data.level, true);
                        }
                    },
                    error: function () {
                        sa_alert('Error!', 'Ajax call failed, please try again or contact site owner.', 'error', true);
                    },
                });
            })
            .catch(swal.noop);
        },
        subscription_form = function ( e ) {
            e.preventDefault();
            var email = $(this).find( '#subs_email' ).val();
            if ( ! validateEmail( email ) ) {
                sa_alert( lang.error, lang.email_is_invalid, 'error' );
                return;
            }
        
            $.ajax({
                url: shop_url + 'subscribe',
                type: 'POST',
                data: $(this).serialize(),
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                        return false;
                    }
                    sa_alert(data.status, data.message, data.level, true);
                },
                error: function () {
                    sa_alert('Error!', 'Ajax call failed, please try again or contact site owner.', 'error', true);
                },
            });
        },
        /**
         * Show SweetAlert with message.
         *
         * @param {string} title
         * @param {string} message
         * @param {string} level
         * @param {boolean} overlay
         */
        sa_alert = function ( title, message, level, overlay) {
            level = level || 'success';
            overlay = overlay || false;
            swal({
                title: title,
                html: message,
                type: level,
                timer: overlay ? 60000 : 2000,
                confirmButtonText: 'Okay',
            }).catch(swal.noop);
        },
        saa_alert = function ( action, message, method, form_data, callback ) {
            method = method || lang.delete;
            message = message || lang.x_reverted_back;
            form_data = form_data || {};
            form_data._method = method;
            form_data[site.csrf_token] = site.csrf_token_value;
            swal({
                title: lang.r_u_sure,
                html: message,
                type: 'question',
                showCancelButton: true,
                allowOutsideClick: false,
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    return new Promise(function () {
                        $.ajax({
                            url: action,
                            type: 'POST',
                            data: form_data,
                            success: function (data) {
                                if (data.cart) {
                                    cart = null;
                                    cart = data.cart;
                                    $(document).triggerHandler( 'cart.update', data.cart );
                                }
                                if ( callback && 'function' === typeof callback ) {
                                    callback( data );
                                }
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                    return false;
                                } else {
                                    sa_alert(data.status, data.message);
                                }
                            },
                            error: function () {
                                sa_alert('Error!', 'Ajax call failed, please try again or contact site owner.', 'error', true);
                            },
                        });
                    });
                },
            }).catch(swal.noop);
        },
        sa_img = function (title, msg) {
            swal({
                title: title,
                html: msg,
                type: 'success',
                confirmButtonText: lang.okay,
            }).catch(swal.noop);
        },
        prompt= function ( title, message, form_data ) {
            title = title || 'Reset Password';
            message = message || 'Please type your email address';
            form_data = form_data || {};
            form_data[site.csrf_token] = site.csrf_token_value;
            
            swal( {
                title: title,
                html: message,
                input: 'email',
                showCancelButton: true,
                allowOutsideClick: false,
                showLoaderOnConfirm: true,
                cancelButtonText: lang.cancel,
                confirmButtonText: lang.submit,
                preConfirm: function (email) {
                    form_data['email'] = email;
                    return new Promise(function (resolve, reject) {
                        $.ajax({
                            url: site.base_url + 'forgot_password',
                            type: 'POST',
                            data: form_data,
                            success: function (data) {
                                if (data.status) {
                                    resolve(data);
                                } else {
                                    reject(data);
                                }
                            },
                            error: function () {
                                sa_alert('Error!', 'Ajax call failed, please try again or contact site owner.', 'error', true);
                            },
                        });
                    });
                },
            } ).then(function (data) {
                sa_alert( data.status, data.message );
            } );
        },
        formatSA = function ( x ) {
            x = x.toString();
            var afterPoint = '';
            if (x.indexOf('.') > 0) afterPoint = x.substring(x.indexOf('.'), x.length);
            x = Math.floor(x);
            x = x.toString();
            var lastThree = x.substring(x.length - 3);
            var otherNumbers = x.substring(0, x.length - 3);
            if ( otherNumbers !== '' ) lastThree = ',' + lastThree;
            return otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + lastThree + afterPoint;
        },
        /**
         * Format Money - for products price
         * @param {number} x
         * @param {string} symbol
         * @return {string}
         */
        formatMoney = function (x, symbol) {
            if (!symbol) {
                symbol = site.settings.symbol;
            }
            if (site.settings.sac == 1) {
                return (
                    (site.settings.display_symbol == 1 ? symbol : '') +
                    '' +
                    formatSA(parseFloat(x).toFixed(site.settings.decimals)) +
                    (site.settings.display_symbol == 2 ? symbol : '')
                );
            }
            return accounting.formatMoney(
                x,
                symbol,
                site.settings.decimals,
                site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep,
                site.settings.decimals_sep,
                ( 2 == site.settings.display_symbol ? '%v%s' : '%s%v' )
            );
        },
        /**
         * Strict search if item is in array.
         * @param {string|number|*} needle
         * @param {Array} haystack
         * @return {boolean}
         */
        in_array = function ( needle, haystack ) {
            return -1 !== haystack.indexOf( needle );
        },
        cartHandler = function () {
            var theCart = $('.cart-sidebar-content');
            var toggleCart = function ( e ) {
                e.preventDefault();
                $( '.cart-sidebar-wrapper' ).toggleClass( 'cart-sidebar-open' );
            }
            var updateCart = function ( event, cartData ) {
                cartData = cartData || cart;
                var total_items   = cartData.total_items,
                    countTxt      = total_items > 1 ? lang.items : lang.item,
                    countSubTotal = total_items > 1 ? cartData.subtotal: formatMoney( cartData.subtotal );
                $('.cart-total-items').attr( 'aria-label', total_items > 0 ? total_items + ' ' + countTxt : 'Cart is empty' ).text(total_items);
                $('.total-cart-item').html( `${total_items} <span class="sr-only">${countTxt}</span>` );
                $('.cart-total-item-count').html( `${total_items} ${countTxt}` );
                $('.total-price-basket .value,.total-cart-amount,.checkout-amount').text( countSubTotal );
                
                if ( total_items && total_items > 0 ) {
                    theCart.rc( 'cart-empty' ).empty();
                    var i = 1;
                    $.each( cartData.contents, function () {
                        var item = this;
                        var optHtml = '';
                        if ( item.options && item.option ) {
                            var selected = item.options.filter( function( opt ) {
                                return opt.id == item.option;
                            } );
                            if ( selected.length ) {
                                optHtml = selected[0].name.split('|').join( ', ').split( ':').join( ': ');
                                // optHtml += '<ul>';
                                // $.each( selected[0].name.split('|'), function() {
                                //     var o = this.split(':');
                                //     optHtml += `<li><span>${o[0]}</span><span>${o[1]}</span></li>`;
                                // } );
                                // optHtml += '</ul>';
                            }
                            // optHtml += `<select name="${i}[option]" class="selectpicker cart-item-option" data-width="100%" data-style="btn-default">`;
                            // $.each(item.options, function () {
                            //     optHtml += `<option value="${item.id}" ${item.id == item.option ? 'selected' : ''}>${item.name} ${ parseFloat(item.price) != 0 ? '(+' + item.price + ')' : '' }</option>`;
                            // });
                            // optHtml += `</select>`;
                        }
                        var cartItem = `
                        <div class="product product-${item.product_id} single-cart-item">
                        <input type="hidden" name="${i}[rowid]" value="${item.rowid}">
                        <div class="cart-qty-input">
                            <button class="cart-qty qty-inc cart-item-increase-btn"><i class="fa fa-angle-up" aria-hidden="true"></i></button>
                            <span class="cart-qty-count cart-item-qty-input">${item.qty}</span>
                            <button class="cart-qty qty-desc cart-item-decrease-btn"><i class="fa fa-angle-down" aria-hidden="true"></i></button>
                        </div>
                        <div class="cart-item-photo">
                            <a class="view-product" href="${site.site_url}product/${item.slug}">
                                <img src="${item.image}" alt="${item.name}">
                            </a>
                        </div>
                        <div class="cart-item-details">
                            <div class="cart-item-title">
                                <a class="view-product" href="${site.site_url}product/${item.slug}">${item.name}</a>
                            </div>
                            <div class="cart-item-price">${item.price}</div>
                            <div class="cart-item-qty-with-item-unit">
                                <p><span class="cart-qty-count cart-item-qty">${item.qty}</span> X <span class="cart-item-unit">${item.unit_name}</span></p>
                            </div>
                            <div class="cart-item-options">${optHtml}</div>
                        </div>
                        <div class="cart-item-amount"><p>${item.subtotal}</p><p class="regular-price">${item.reg_price}</p></div>
                        <a class="cart-item-remove-btn remove-item" data-rowid="${item.rowid}">
                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                        </a>
                    </div>`;
                        i++;
                        cartItem = $(cartItem);
                        cartItem.data( 'product_id', item.product_id );
                        cartItem.data( 'product', {
                            id: item.product_id,
                            rowId: item.rowid,
                            slug: item.slug,
                            cartQty: item.qty,
                            cartItem: true,
                        } );
                        cartItem.data( 'row_id', item.rowid );
                        cartItem.data( 'item', item );
                        cartItem.appendTo( theCart );
                    });
                } else {
                    theCart.ac( 'cart-empty' ).empty();
                }
            }
            /**
             *
             * @param string
             * @return {number}
             */
            var parsePrice = function( string ) {
                return parseFloat( string.replace( /[^\d.]+/, '' ) );
            };
            /**
             *
             * @param {int} prodId
             * @param {Object} [data={cartQty:false,rowId:false}]
             */
            var updateProdData = function ( prodId, data ) {
                var product = $('.product-' + prodId );
                if ( ! product.length ) {
                    return;
                }
                
                data = $.extend( {}, { cartQty: false, rowId: false }, product.data( 'product' ), data );
                product.data( 'product', data );
                
                if ( false !== data.cartQty ) {
                    product.find( '.cart-qty-count' ).text( data.cartQty || 1 );
                }
                if( ! data.cartQty ) {
                    product.rc( 'added-in-cart');
                    product.ac( 'not-added-in-cart');
                } else {
                    product.rc( 'not-added-in-cart');
                    product.ac( 'added-in-cart');
                }
            }
            /**
             *
             * @param {int} [qty=1]
             * @param {int} prodId
             * @param {string} rowId
             */
            var updateCartQty = function( qty, prodId, rowId ) {
                qty = Math.abs( qty || 1 );
                var cartItem = cart.contents[rowId];
                var price = parsePrice( cartItem.price );
                // new qty.
                cartItem.qty = parseInt( qty );
                price *= cartItem.qty;
                cartItem.subtotal = cartItem.subtotal.replace( /[\d.]+/, price.toFixed(2) );
                cart.contents[rowId] = cartItem;
                updateProdData( prodId, { cartQty: cartItem.qty} );
                updateCart();
            };
            /**
             *
             * @param {Object} data
             * @param {boolean} [successAlert=false]
             */
            var updateCartItem = function ( data, successAlert ) {
                if ( ajaxify2 ) {
                    ajaxify2.abort();
                    ajaxify2 = false;
                    return;
                }
                successAlert  = successAlert || false;
                data = $.extend( {}, { prodId: false, rowid: false, qty: false, option: false }, data );
                if ( ! data.rowid ) {
                    return;
                }
                if ( false === data.qty && false === data.option ) {
                    return;
                }
                if ( false === data.option ) {
                    delete data.option;
                }
                ajaxify2 = $.ajax( {
                    url: site_url + 'cart/update',
                    type: 'POST',
                    data: $.extend( {}, __CSRF__, data ),
                    success: function ( res ) {
                        ajaxify2 = false;
                        if ( res.hasOwnProperty( 'cart' ) ) {
                            cart = res.cart;
                        }
                        if ( res.error ) {
                            sa_alert('Error!', res.message, 'error', true );
                            updateCartQty( res.cart.currentItem.qty, data.prodId, res.cart.currentItem.rowId);
                        } else {
                            updateProdData( data.prodId, { cartQty: data.qty } );
                            updateCart();
                            if ( successAlert ) {
                                sa_alert( res.status, res.message );
                            }
                        }
                    },
                    error: AjaxErrorHandler,
                } );
            }
            /**
             *
             * @param {string} rowid
             * @param {string} prodId
             */
            var removeCartItemAlert = function( rowid, prodId ) {
                saa_alert(
                    site_url + 'cart/remove',
                    'You want to remove this item from cart?',
                    'post', { rowid: rowid }, function() {
                    var prod = $( `.product-${prodId}` );
                    updateCart();
                } );
            };
            /**
             *
             * @param {jQuery} elToTrag
             * @param {jQuery} toEl
             * @param {Object} [animateFrom]
             * @param {Object} [animateTo]
             * @param {number} [speed=400]
             */
            var dragElAnimation = function ( elToTrag, toEl, animateFrom, animateTo, speed ) {
                animateFrom = $.extend( {}, { offsetTop: 10, offsetLeft: 10, width: 50, height: 50 }, animateFrom );
                animateTo = $.extend( {}, { width: 50, height: 50 }, animateTo );
                speed = speed || 400;
                if ( elToTrag && toEl ) {
                    var elClone = elToTrag.clone()
                    .offset( { top: elToTrag.offset().top, left: elToTrag.offset().left } )
                    .css( {
                        opacity: '0.34',
                        position: 'absolute',
                        height: '130px',
                        width: '130px',
                        'z-index': '1000',
                    } )
                    .appendTo( $( 'body' ) )
                    .animate( {
                        top: toEl.offset().top + animateFrom.offsetTop,
                        left: toEl.offset().left + animateFrom.offsetLeft,
                        width: animateFrom.width + 'px',
                        height: animateFrom.height + 'px',
                    }, speed );
                    elClone.animate( { width: animateTo.width + 'px', height: animateTo.height + 'px' }, function () {
                        $( this ).detach();
                    } );
                }
            }
            /**
             *
             * @param {int} prodId
             * @param {int} qty
             * @param {int} option
             */
            var ajaxAddToCart = function( prodId, qty, option ) {
                if ( ajaxify ) {
                    ajaxify.abort();
                    ajaxify = false;
                }
                if ( ! prodId || ! qty ) {
                    console.warn( 'Invalid add to cart request' );
                    return;
                }
                option = ( '' !== option ? parseInt( option ) : '' );
                
                ajaxify = $.ajax( {
                    url: site_url + 'cart/add/' + prodId,
                    type: 'POST',
                    dataType: 'json',
                    data: $.extend( { quantity: qty, option: option }, __CSRF__ ),
                } ).done(function ( data ) {
                    ajaxify = false;
                    if (data.error) {
                        sa_alert('Error!', data.message, 'error', true);
                    } else {
                        cart = data;
                        updateProdData( prodId, { cartQty: data.newItem.qty, rowId: data.newItem.rowId } );
                        $(document).triggerHandler( 'cart.update' );
                    }
                });
            };
            var addToCartAnim = function( product, prodId, qty, option ) {
                qty = qty || 1;
                option = option || '';
                dragElAnimation( product.find( 'img' ).eq( 0 ) , $('.cart-open-btn .icon') );
                ajaxAddToCart( prodId, qty, option );
            }
            
            updateCart();
            
            $( document )
            .on('click', '.add-to-cart', function( e ) {
                e.preventDefault();
                var self = $(this);
                if ( self.hasClass( 'disabled' ) ) {
                    return false;
                }
                var id = self.attr('data-id'),
                    product = self.closest( '.product' ),
                    prodData = product.parseData( 'product' ),
                    optionName = [],
                    option = '';
                // if ( prodData.rowId ) {
                //     var qty = cart.contents[prodData.rowId].qty + 1;
                //     updateCartQty( qty, id, prodData.rowId );
                //     updateCartItem( { prodId: id, rowid: prodData.rowId, qty: qty }, false );
                // } else {
                // }
                // build option name.
                if ( prodData.isVariable ) {
                    product.find( '.options [type="radio"]:checked').each( function() {
                        var self = $(this);
                        optionName.push( self.attr('name') + ':' + self.val() );
                    } );
                    optionName = optionName.join('|');
                    if ( prodData.attributes.dataMap.hasOwnProperty( optionName ) ) {
                        option = prodData.attributes.dataMap[ optionName ].id;
                    }
                }
                addToCartAnim( product, id, parseInt( product.find( '.cart-qty-count:eq(0)' ).text() ), option );
            } )
            .on( 'click', '.cart-qty', function( e ) {
                e.preventDefault();
                var self = $( this ),
                    prod = self.closest( '.product' ),
                    rowId = '',
                    prodId = '',
                    qtyEl = prod.find( '.cart-qty-count:eq(0)' ),
                    qty = parseInt( qtyEl.text() );
        
                // Increase.
                if ( self.hasClass( 'qty-inc' ) ) qty += 1;
                // Decrease & can set to zero.
                if ( self.hasClass( 'qty-desc' ) && qty > 0 ) qty -= 1;
                
                if ( qty <= 0 ) {
                    return;
                }
                
                // update dom.
                qtyEl.text( qty );
                // handle cart item
                if ( ! prod.hasClass( 'single-cart-item' ) ) {
                    return;
                }
        
                rowId = prod.data( 'row_id' );
                prodId = prod.data( 'product_id' );
                
                if ( ! rowId || ! prodId ) {
                    return;
                }
                
                updateCartQty( qty, prodId, rowId );
                updateCartItem( { prodId: prodId, rowid: rowId, qty: qty } );
                
                // if ( prod.hasClass( 'single-cart-item' ) ) {
                //     rowId = prod.data( 'row_id' );
                //     prodId = prod.data( 'product_id' );
                // } else {
                    // rowId = prodData.rowId;
                    // prodId = prodData.id;
                // }
                // if ( ! rowId ) {
                //     if ( prodId && ! prod.hasClass( 'single-cart-item' ) ) {
                //         addToCartAnim( prod, prodId, 1 );
                //     }
                //     return;
                // }
                //
                // if ( qty > 0 ) {
                //     updateCartQty( qty, prodId, rowId );
                //     updateCartItem( { prodId: prodId, rowid: rowId, qty: qty } );
                // } else {
                //     // remove removeCartItem
                //     removeCartItemAlert( rowId, prodId );
                // }
            } )
            .on('change', '.cart-item-option, .cart-item-qty', function (e) {
                e.preventDefault();
                
                var row = $(this).closest('tr');
                
                updateCartItem( {
                    rowid: row.attr('id'),
                    qty: row.find('.cart-item-qty').val(),
                    option: row.find('.cart-item-option').children('option:selected').val(),
                } );
            } )
            .on('click', '.remove-item', function (e) {
                e.preventDefault();
                var self = $(this);
                removeCartItemAlert( self.attr('data-rowid'), self.closest( '.product' ).data( 'product_id' ) );
            } )
            .on( 'click', '.empty-cart', function (e) {
                e.preventDefault();
                saa_alert( site_url + 'cart/destroy' );
                var prod = $('.product');
                prod.ac( 'not-added-in-cart').rc( 'added-in-cart');
                prod.data( 'product' ).rowId = '';
                prod.data( 'product' ).cartQty = 0;
                prod.find( '.cart-qty-count').text( 1 );
            } )
            // .on( 'shown.bs.select', '.cart-item-option', function(){
            //     if ($(this).children('option:selected').val()) {
            //         $po = $(this).children('option:selected').val();
            //     }
            // } )
            .on( 'cart.update', __debounce( updateCart, 500 ) )
            .on( 'click', '.cart-open-btn, .cart-close-btn', toggleCart );
        },
        addressEditor = function () {
            var setActive = function ( addressId ) {
                addressId = addressId || false;
                var addEl, phoneEl;
                if ( addressId ) {
                    addEl = $( `.address-${addressId}` );
                    phoneEl = $( `.phone-${addressId}` );
                } else {
                    addEl = $( '.address:eq(0)' );
                    phoneEl = $( '.phone:eq(0)' );
                }
                if ( addEl.length ) {
                    addEl.find('input[type="radio"]').prop('checked',true).trigger('change' );
                    addEl.ac( 'active' );
                    phoneEl.ac( 'active' );
                }
            };
            
            function editAddress( address ) {
                address = $.fn.extend( {}, {
                    id: '',
                    title: '',
                    line1: '',
                    line2: '',
                    city: '',
                    state: '',
                    postal_code: '',
                    country: '',
                    phone: '',
                }, address );
                var __FORM__ = '',
                    keys = Object.keys( address ),
                    placeholders = {
                        title: 'Home/Office',
                        line1: '222/b Lake view, Gulshan',
                        line2: 'Apt. T/10',
                        city: 'Dhaka',
                        state: 'Dhaka',
                        postal_code: '1222',
                        country: 'Bangladesh',
                        phone: '+8801xxxxxxxxx',
                    };
                // The form.
                __FORM__ += `
        <span class="text-bold padding-bottom-md">${lang.fill_form}</span>
        <hr class="swal2-spacer padding-bottom-xs" style="display: block;">
        <form action="${shop_url}/address/${address.id}" id="address-form" class="padding-bottom-md">
        <input type="hidden" name="${site.csrf_token}" value="${site.csrf_token_value}">
        `;
                for( var k of keys ) {
                    if ( in_array( k, [ 'id', 'company_id', 'updated_at' ] ) ) {
                        continue;
                    }
                    __FORM__ +=`<div class="${ 'city' === k || 'state' === k ? 'col-sm-6' : 'col-sm-12' } form-group">
        <label class="sr-only" for="address-${k}">${lang[k]}</label>
        <input type="text" class="form-control" id="address-${k}" name="${k}" value="${address[k]}" placeholder="${placeholders[k]}" required>
        </div>`
                }
            
                __FORM__ += `</form>`;
            
                swal({
                    title: address.id ? lang.update_address : lang.add_address,
                    html: __FORM__,
                    showCancelButton: true,
                    allowOutsideClick: false,
                    cancelButtonText: lang.cancel,
                    confirmButtonText: lang.submit,
                    preConfirm: function () {
                        return new Promise( function ( resolve, reject ) {
                            var errors = false;
                            for( k of keys ) {
                                if ( in_array( k, [ 'id', 'company_id', 'updated_at' ] ) ) {
                                    continue;
                                }
                                var field = $( `#address-${k}`);
                                if ( ! field.val() ) {
                                    field.ac( 'has-error' );
                                    field.after( `<label for="address-${k}" class="error">${ lang[k] } ${ lang.is_required }</label>` );
                                    errors = true;
                                }
                            }
                            if ( errors ) {
                                reject();
                            }
                            resolve();
                        } );
                    },
                    onOpen: function () {
                        for( k of keys ) {
                            $( `#address-${k}`).on( 'change', function () {
                                if ( ! $(this).val() ) {
                                    $(this).ac( 'has-error' )
                                } else {
                                    $(this).rc( 'has-error' )
                                }
                            } );
                        }
                    },
                }).then( function () {
                    var $form = $('#address-form');
                
                    $.ajax({
                        url: `${shop_url}/address/${address.id}`,
                        type: 'POST',
                        data: $form.serialize(),
                        success: function ( data ) {
                            sa_alert( data.title, data.message, data.status );
                            if ( data.address ) {
                                renderAddress(data.address);
                            }
                        },
                        error: function () {
                            sa_alert('Error!', 'Ajax call failed, please try again or contact site owner.', 'error', true );
                        },
                    });
                }).catch( swal.noop );
            }
            
            /**
             *
             * @param {Object} address
             */
            function renderAddress( address ) {
                if ( ! address ) return;
                var __HTML__ = $( `
                    <div class="address col-sm-6 col-md-4 address-${address.id}">
                        <label>
                            <input type="radio" name="address" value="${address.id}">
                            <div class="checkout-page-content">
                                <div class="content-header">${address.title}</div>
                                    <p class="content"></p>
                                <div class="checkout-step-content-edit">
                                    <a class="edit" href="#"><img src="${site.assetss_url}images/edit-icon.png"></a>
                                    <a class="remove" href="#"><img src="${site.assetss_url}images/times.png"></a>
                                </div>
                            </div>
                        </label>
                    </div>
                ` ),
                    __PHONE__ = $( `
                        <div class="phone col-sm-6 col-md-4 phone-${address.id}">
                            <label>
                                <input type="radio" disabled name="phone" value="${address.id}">
                                <div class="checkout-page-content">
                                    <div class="content-header">${address.title}</div>
                                    <p class="content">${address.phone}</p>
                                </div>
                            </label>
                        </div>
                    ` ),
                    addressID = address.id;
                __HTML__.data( 'address', address );
                
                var addressStr = '';
                // format address
                if ( address.line1 ) addressStr += address.line1 + '<br>';
                if ( address.line2 ) addressStr += address.line2 + '<br>';
                if ( address.city ) addressStr += address.city + ',';
                if ( address.city && address.state ) addressStr += ' ';
                if ( address.state ) addressStr += address.state + '';
                if ( address.state && address.postal_code ) addressStr += ' – ';
                if ( address.postal_code ) addressStr += address.postal_code + '<br>';
                if ( address.country ) addressStr += address.country;
                
                __HTML__.find('.content').html( addressStr );
                jQuery( '.address-wrap' ).find( 'input[type="radio"]' ).removeAttr( 'checked' );
                lsSet( 'addressPref', addressID );
                var existing = $('.address-wrap').find('.address-' + addressID );
                if ( existing.length ) {
                    existing.replaceWith( __HTML__ );
                } else {
                    __HTML__.appendTo('.address-wrap');
                }
                existing = $('.phone-wrap').find('.phone-' + addressID );
                if ( existing.length ) {
                    existing.replaceWith( __PHONE__ );
                } else {
                    __PHONE__.appendTo('.phone-wrap');
                }
        
                setActive( addressID );
            }
            
            // Set on load.
            setActive( lsGet( 'addressPref' ) );
            
            $(document)
            .on( 'click', '.address .edit, .update-address', function( e ) {
                e.preventDefault();
                editAddress( $(this).closest( '.address' ).parseData( 'address' ) );
            } )
            .on('click', '.address .remove', function (e){
                e.preventDefault();
                var address = $(this).closest( '.address' ),
                    address_id = address.parseData('address').id;
                $.ajax({
                    url: `${shop_url}/delete_address`,
                    type: 'POST',
                    data: $.extend( {}, __CSRF__, { address_id } ),
                    success: function ( data ) {
                        if ( data.status.toLocaleString() === 'success' ) {
                            lsDel( address_id );
                            var reset = address.hasClass( 'active' );
                            $(`.phone-${address_id}`).remove();
                            address.remove();
                            if ( reset ) {
                                setActive();
                            }
                        }
                        sa_alert( data.title, data.message, data.status );
                    },
                    error: function () {
                        sa_alert('Error!', 'Ajax call failed, please try again or contact site owner.', 'error', true );
                    },
                });
            })
            .on( 'change', '.address input[type="radio"]', function() {
                var address = $(this).closest( '.address' ),
                    addressId = address.parseData( 'address' ).id;
                // remove classes.
                $('.address-wrap .address, .phone-wrap .phone').rc( 'active' );
                // set active class.
                address.ac( 'active' );
                $('.phone-' + addressId ).ac( 'active' );
                lsSet( 'addressPref', addressId )
            } );
        },
        loader = {
            el: '#gsLoading',
            show: function( speed, easing, complete ) {
                speed = speed || 400;
                easing = easing || 'swing';
                if ( 'string' === typeof this.el ) {
                    this.el = $( this.el );
                }
                if ( ! this.el.hasClass( 'active') ) {
                    this.el.ac( 'active' );
                    $('html').ac( 'loading' )
                    this.el.show( speed, easing, function () {
                        if ( 'function' === typeof complete ) {
                            complete()
                        }
                        $(document).triggerHandler( 'loader.shown' );
                    } );
                }
            },
            hide: function( speed, easing, complete ) {
                speed = speed || 400;
                easing = easing || 'swing';
                if ( 'string' === typeof this.el ) {
                    this.el = $( this.el );
                }
                if ( this.el.hasClass( 'active') ) {
                    this.el.rc( 'active' );
                    $('html').rc( 'loading' )
                    this.el.hide( speed, easing, function () {
                        if ( 'function' === typeof complete ) {
                            complete()
                        }
                        $(document).triggerHandler( 'loader.hidden' );
                    } );
                }
            }
        },
        // Share buttons.
        __RRSSB__ = {
            buttons: [
                {
                    class: 'facebook',
                    label: 'Facebook',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 29 29"><path d="M26.4 0H2.6C1.714 0 0 1.715 0 2.6v23.8c0 .884 1.715 2.6 2.6 2.6h12.393V17.988h-3.996v-3.98h3.997v-3.062c0-3.746 2.835-5.97 6.177-5.97 1.6 0 2.444.173 2.845.226v3.792H21.18c-1.817 0-2.156.9-2.156 2.168v2.847h5.045l-.66 3.978h-4.386V29H26.4c.884 0 2.6-1.716 2.6-2.6V2.6c0-.885-1.716-2.6-2.6-2.6z"/></svg>'
                },{
                    class: 'twitter',
                    label: 'Twitter',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28"><path d="M24.253 8.756C24.69 17.08 18.297 24.182 9.97 24.62a15.093 15.093 0 0 1-8.86-2.32c2.702.18 5.375-.648 7.507-2.32a5.417 5.417 0 0 1-4.49-3.64c.802.13 1.62.077 2.4-.154a5.416 5.416 0 0 1-4.412-5.11 5.43 5.43 0 0 0 2.168.387A5.416 5.416 0 0 1 2.89 4.498a15.09 15.09 0 0 0 10.913 5.573 5.185 5.185 0 0 1 3.434-6.48 5.18 5.18 0 0 1 5.546 1.682 9.076 9.076 0 0 0 3.33-1.317 5.038 5.038 0 0 1-2.4 2.942 9.068 9.068 0 0 0 3.02-.85 5.05 5.05 0 0 1-2.48 2.71z"/></svg>'
                },{
                    class: 'pinterest',
                    label: 'Pinterest',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28"><path d="M14.02 1.57c-7.06 0-12.784 5.723-12.784 12.785S6.96 27.14 14.02 27.14c7.062 0 12.786-5.725 12.786-12.785 0-7.06-5.724-12.785-12.785-12.785zm1.24 17.085c-1.16-.09-1.648-.666-2.558-1.22-.5 2.627-1.113 5.146-2.925 6.46-.56-3.972.822-6.952 1.462-10.117-1.094-1.84.13-5.545 2.437-4.632 2.837 1.123-2.458 6.842 1.1 7.557 3.71.744 5.226-6.44 2.924-8.775-3.324-3.374-9.677-.077-8.896 4.754.19 1.178 1.408 1.538.49 3.168-2.13-.472-2.764-2.15-2.683-4.388.132-3.662 3.292-6.227 6.46-6.582 4.008-.448 7.772 1.474 8.29 5.24.58 4.254-1.815 8.864-6.1 8.532v.003z"/></svg>'
                },{
                    class: 'tumblr',
                    label: 'Tumblr',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28"><path d="M18.02 21.842c-2.03.052-2.422-1.396-2.44-2.446v-7.294h4.73V7.874H15.6V1.592h-3.714s-.167.053-.182.186c-.218 1.935-1.144 5.33-4.988 6.688v3.637h2.927v7.677c0 2.8 1.7 6.7 7.3 6.6 1.863-.03 3.934-.795 4.392-1.453l-1.22-3.54c-.52.213-1.415.413-2.115.455z"/></svg>'
                },{
                    class: 'linkedin',
                    label: 'LinkedIn',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28"><path d="M25.424 15.887v8.447h-4.896v-7.882c0-1.98-.71-3.33-2.48-3.33-1.354 0-2.158.91-2.514 1.802-.13.315-.162.753-.162 1.194v8.216h-4.9s.067-13.35 0-14.73h4.9v2.087c-.01.017-.023.033-.033.05h.032v-.05c.65-1.002 1.812-2.435 4.414-2.435 3.222 0 5.638 2.106 5.638 6.632zM5.348 2.5c-1.676 0-2.772 1.093-2.772 2.54 0 1.42 1.066 2.538 2.717 2.546h.032c1.71 0 2.77-1.132 2.77-2.546C8.056 3.593 7.02 2.5 5.344 2.5h.005zm-2.48 21.834h4.896V9.604H2.867v14.73z"/></svg>'
                },{
                    class: 'whatsapp',
                    action: 'share/whatsapp/share',
                    label: 'WhatsApp',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="29" height="29" viewBox="0 0 600 600"><g><path d="M304.8,35C161.9,35,46,150.9,46,293.8c0,70.9,37.6,134.3,37.6,134.3L38.4,565.9l141.8-45.3c0,0,52.6,32,124.7,32 c142.9,0,258.8-115.9,258.8-258.8C563.6,150.9,447.7,35,304.8,35z M304.8,511.2c-66.5,0-119.8-36-119.8-36l-81.7,26.7l26.5-79.1 c0,0-42.4-58.6-42.4-129c0-120.1,97.3-217.4,217.4-217.4c120.1,0,217.4,97.3,217.4,217.4C522.2,413.8,424.9,511.2,304.8,511.2z"/><path d="M206.3,181.1c0,0,7-4.4,10.6-4.4c3.6,0,20.4,0,20.4,0s5.5,1,8,6.6c2.5,5.7,19.7,46.1,21,49.2s4.6,10.8-0.7,17.7 c-5.4,6.9-16.5,19.8-16.5,19.8s-4.4,4-0.6,10.3c3.8,6.4,17.3,27.3,35,43.1c17.7,15.8,39.3,27.5,50.1,31c10.8,3.5,13.1-1.2,17.3-6.5  c4.1-5.3,17-21.6,17-21.6s4.4-6.5,13.1-2.5c8.7,4,51.1,24.5,51.1,24.5s5.2,0.9,5.5,6.6c0.3,5.8,3.8,23-11.5,39.6 c-15.4,16.6-48,24.3-63.5,19.9c-15.5-4.3-66.6-17.7-100-48.8S202,302,191,277.8c-11.1-24.2-10.4-38.5-9.8-45.2 C181.8,225.8,185.3,194.5,206.3,181.1z"/></g></svg>',
                }
            ],
            template: '',
            init: function() {
                this.template = '';
                for ( var button of this.buttons ) {
                    this.template += `<li class="rrssb-${button.class}"><a ${button.hasOwnProperty( 'action' ) ? `data-action="${button.action}"` : 'class="popup"'} aria-label="Share On ${button.label}"><span class="rrssb-icon" aria-hidden="true">${button.icon}</span><span class="rrssb-text" aria-hidden="true">${button.label}</span></a></li>`;
                }
                this.template = `<div class="row">
                    <div class="col-xs-12">
                        <p style="font-size:1.1em;font-weight:bold;margin: 15px 0 10px 0;">${lang.share_message}</p>
                        <ul class="rrssb-buttons clearfix">${this.template}</ul>
                    </div>
                </div>`;
            },
            getTemplate: function() {
                if ( ! this.template ) {
                    this.init();
                }
                return this.template;
            }
        },
        // Product Viewer.
        productViewer = {
            modal: '#productViewer',
            current: null,
            urlRestore: site_url, // Initial Site URL
            showing: false,
            color: '',
            doc: function( set ) {
                set = $.extend( {}, {
                    title: '',
                    description: '',
                    url: '/',
                    image: '',
                }, restoreHome || {}, set );
                document.title = set.title;
                $('head meta[name="description"]').attr( 'content', set.description );
                $( '[property="og:image"]' ).attr( 'content', set.image );
                window.history.pushState( '', set.title, set.url );
            },
            init: function() {
                var self = this;
                if ( 'string' === typeof self.modal ) {
                    this.modal = $( self.modal );
                }
                // cleanup.
                self.modal.on( 'hide.bs.modal', function( e ) {
                    self.doc( { url: self.urlRestore } )
                } );
                $(window).on( 'popstate', function ( e ) {
                    var loc = winLoc();
                    if ( loc.includes( 'product/' ) ) {
                        self.show( loc.replace( prod_url, '' ) );
                    } else {
                        self.close();
                    }
                } );
                if ( ! winLoc().includes( 'product/' ) || self.urlRestore !== site_url ) {
                    self.urlRestore = winLoc();
                }
            },
            fetch: function( data ) {
                if ( this.current && this.current.slug === data ) {
                    this.modal.modal( 'show' );
                    return;
                }
                var productUrl = data;
                if ( data.indexOf( prod_url ) === -1 ) {
                    productUrl = prod_url + data
                } else {
                    data = data.replace( prod_url, '' );
                }
            
                if ( __CACHES__['prodModal'][data] ) {
                    this.show( __CACHES__['prodModal'][data] );
                    return;
                }
                loader.show( 100 );
            
                $.get( productUrl, function ( data ) {
                    loader.hide( 100 );
                    if ( data.error ) {
                        if ( data.message ) {
                            sa_alert( 'Error!', data.message, 'error' );
                        } else {
                            window.location.reload();
                        }
                    }
                    productViewer.show( data );
                } ).fail(function( e ) {
                    var message;
                    if ( e.status === 404 ) {
                        message = 'Product Not Found';
                    } else if ( e.status === 500 ) {
                        message = 'Error Processing the request.';
                    } else {
                        message = e.statusText;
                    }
                    loader.hide( 10 );
                    sa_alert( 'Error!', message, 'error' );
                });
            },
            show: function( data, fetchData, color ) {
                var self = this;
                fetchData  = ! ! fetchData;
                self.color = color || '';
                if ( ! data ) {
                    return;
                }
                if ( 'object' === typeof data ) {
                    data = $.extend( {}, { slug: '', }, data );
                    if ( ! data.slug ) {
                        return;
                    }
                }
                if ( fetchData ) {
                    self.fetch( data.slug || data );
                    return;
                }
            
                if ( self.current && self.current.id === data.id ) {
                    var product = self.current;
                    self.modal.modal( 'show' );
                    self.doc( {
                        title: product.meta_title,
                        description: product.meta_description,
                        image: product.meta_image,
                        url: product.link,
                    } );
                    if ( product.isVariable && self.color ) {
                        $('.product_options .options [type="radio"][value="'+self.color+'"]').prop( 'checked', true );
                        $('.product_options .options [type="radio"]').trigger( 'change' );
                    }
                    return;
                }
            
                self.current = data;
                self.prepare();
            },
            prepare: function() {
                // if ( ! __CACHES__['prodModal'][this.current.id] ) {
                //     __CACHES__['prodModal'][this.current.id]
                // }
                var self = this,
                    product = self.current,
                    __HTML__ = '',
                    __GALLERY__ = '',
                    __OPTIONS__ = '';
                if ( product.gallery.length ) {
                    __GALLERY__ += '<div class="product-gallery">';
                    __GALLERY__ += `<div class="item"><a href="#" class="gallery-preview" data-src="${product.image}"><img src="${product.thumb||product.image}"></a></div>`;
                    $.each( product.gallery, function () {
                        __GALLERY__ += `<div class="item"><a href="#" class="gallery-preview" data-src="${this.photo}"><img src="${this.thumb||this.photo}"></a></div>`;
                    } );
                    __GALLERY__ += '</div>';
                }
                if ( product.isVariable ) {
                    __OPTIONS__ += `<div class="available-options"><h4>${lang.available_options}</h4><div class="product_options">`;
                    for( var i in product.attributes.opts ) {
                        var opts = product.attributes.opts[i],
                            lower = i.toLowerCase();
                        __OPTIONS__ += `<div id="option_${lower}" class="options option-${lower}"><label class="control-label">${ lang.hasOwnProperty( `option_${lower}` ) ? lang[`option_${lower}`]: i }</label><ul>`;
                        for ( var opt of opts ) {
                            var optLower = opt.toLowerCase().replace( /\s/g, '-');
                            __OPTIONS__ += `<li class="option-${lower}-${optLower}"><label><input type="radio" name="${i}" value="${opt}"${opt === self.color ? 'checked' : ''}><span class="swatch" style="background-color:${optLower}" title="${opt}">${ 'color' === lower ? '': opt.toUpperCase() }</span></label></li>`;
                        }
                        __OPTIONS__ += '</ul></div>';
                    }
                    __OPTIONS__ +=`</div></div>`;
                }
            
                __HTML__ = `
                        <div class="products">
                        <div class="product product-${product.id}">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 gallery-holder">
                                    <div class="product-preview">
                                        <img src="${product.image}" alt="${product.name}">
                                    </div><!-- /.product-preview -->
                                    ${ __GALLERY__ }
                                    ${__RRSSB__.getTemplate()}
                                </div><!-- /.gallery-holder -->
                                <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 product-info-block">
                                    <div class="product-info">
                                        <h1 class="name" id="productName">${product.name}</h1>
                                        <div class="product-price">${'' !== product.sale_price ? `<span class="price">${product.sale_price}</span><span class="price-before-discount">${product.regular_price}</span>` : `<span class="price">${product.regular_price}</span>`}</div>
                                        <div class="overview">
                                            <h4>${lang.overview}</h4>
                                            <div class="ov-details">${product.details}</div>
                                        </div>
                                        ${ __OPTIONS__ }
                                        <div class="row cart-btn-wrap">
                                            <div class="col-xs-7 col-sm-8">
                                                <div class="add-to-cart-count-content">
                                                    <button class="cart-qty qty-desc cart-item-decrease-btn-from-product">-</button>
                                                    <span class="cart-qty-count cart-item-qty-input-from-product">1</span>
                                                    <button class="cart-qty qty-inc cart-item-increase-btn-from-product">+</button>
                                                </div>
                                            </div>
                                            <div class="col-xs-5 col-sm-4">
                                                <div class="buy-now">
                                                    <a class="add-to-cart${ product.isVariable ? ' disabled' : '' }" data-id="${product.id}" href="${product.add_to_cart}" data-tooltip="${lang.select_option}">${lang.buy_now}</a>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <hr>
                                        <div class="description-container m-t-20">${product.details}</div> --><!-- /.description-container -->
                                    </div><!-- /.product-info -->
                                </div><!-- /.col-sm-7 -->
                            </div><!-- /.row -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="product-full-desc">
                                        <aside class="product-specification">
                                            <ul>
                                                <li>
                                                    <strong>Hair Texture:</strong>
                                                    <span>Body Wave</span>
                                                </li>
                                                <li>
                                                    <strong>Hair Color:</strong>
                                                    <span>Natural Deep Brown</span>
                                                </li>
                                                <li>
                                                    <strong>Hair Length:</strong>
                                                    <span>Available from 10’’- 24’’ inches</span>
                                                </li>
                                                <li>
                                                    <strong>Hair Luster:</strong>
                                                    <span>Medium</span>
                                                </li>
                                                <li>
                                                    <strong>Weight:</strong>
                                                    <span>3.3oz</span>
                                                </li>
                                            </ul>
                                        </aside>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab accusamus, accusantium aperiam commodi cum exercitationem in iste labore magnam maiores molestias nam obcaecati odio omnis, placeat quisquam sed similique temporibus unde voluptates! Aspernatur autem distinctio, esse facilis praesentium repellendus! Aut consectetur consequatur culpa cupiditate delectus dolor ex excepturi expedita hic iure odit, officia porro provident quaerat repudiandae! Accusantium adipisci aliquid aspernatur commodi corporis culpa debitis dolor dolorem doloremque, eius eos illum, ipsam laudantium natus necessitatibus nihil nisi officiis pariatur quisquam quod, recusandae rem repellendus similique tempora velit veniam voluptas. A aliquam autem commodi consequatur earum et eum ex expedita fugiat fugit ipsa ipsum magnam magni minima neque nesciunt non nostrum placeat porro, provident quae quam quidem quo quos recusandae, repellat suscipit voluptatum. Aliquam asperiores autem corporis dolore dolorem facere id ipsam necessitatibus nostrum numquam provident quam quia reiciendis, similique sint veritatis, voluptate! Alias asperiores autem laboriosam, libero quidem ratione sed?</p>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet culpa dolor sit tempore tenetur. Atque impedit libero neque odio possimus quibusdam voluptates. Alias architecto beatae consequatur consequuntur corporis cum deleniti dicta dignissimos dolor dolore eaque eum facilis fuga iste laboriosam laudantium molestias mollitia nesciunt, odit pariatur perferendis quos, reiciendis repellat reprehenderit voluptates. Aliquid amet corporis cupiditate debitis delectus esse fugit hic in ipsam maiores minima minus nam nostrum numquam officia optio pariatur provident quae, quis quisquam ullam, vel voluptates! Assumenda beatae blanditiis, debitis dolores expedita explicabo id odio porro. A aliquam assumenda at delectus, distinctio ducimus eos error est id incidunt itaque molestiae molestias nam nemo nobis nulla obcaecati odit pariatur perferendis placeat possimus provident quam, quidem recusandae repellendus, repudiandae saepe soluta sunt suscipit tenetur totam ullam vel voluptatum. Aspernatur cumque distinctio doloribus minus nemo placeat, reiciendis voluptate? Ab aliquam aliquid corporis delectus dignissimos dolor dolore doloremque earum, eligendi est exercitationem fuga illum laboriosam magni modi molestiae odit officiis pariatur possimus, rem repudiandae similique veritatis vero! A aperiam debitis dolore ea impedit incidunt minus, molestiae neque nihil, officia perferendis quod quos, recusandae vel veniam voluptas voluptatibus? Accusamus animi cum facere iure molestias odio placeat quisquam tenetur ut? Culpa, dolor maiores.</p>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Doloremque dolores enim, eos facilis fugiat illum inventore laudantium magnam molestias nisi non numquam odio quae quis totam ullam voluptatem. A aspernatur dolores ex quibusdam, repudiandae voluptates! Accusantium animi at autem doloremque id, in incidunt quas quia quibusdam, sapiente tempore veritatis voluptatem?</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.product -->
                        <div class="youtube-embed">
                            <iframe width="800" height="450" src="https://www.youtube.com/embed/bluL_iD1FBI?modestbranding=1&rel=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                    <!-- /.products -->
                    <div class="gradiant-area-prod-desc">
                        <div class="row align-center">
                            <div class="col-xs-offset-4 col-xs-4 col-sm-offset-0 col-sm-3">
                                <img src="${site.assetss_url}images/logo-head-black.svg">
                            </div>
                            <div class="col-xs-12 col-sm-9">
                                <h4>The Her Hair Difference</h4>
                                <p>Not all hair bundles are equal. We've worked hard to consistently achieve the ultimate balance value and quality.</p>
                                <p>All Her Hair is guaranteed to be pure virgin hair: un-dyed, untreated, whole, healthy, and beautiful.</p>
                                <div class="additional-features">
                                    <p><strong>And all Her Hair bundles share the following features:</strong></p>
                                    <ul>
                                        <li data-icon="">100% virgin human hair</li>
                                        <li data-icon="">Cuticles are intact and flow in the same direction</li>
                                        <li data-icon="">Minimal shedding</li>
                                        <li data-icon="">Machine weft for secure installs</li>
                                        <li data-icon="">Bleach/Dye-friendly</li>
                                        <li data-icon="">Undergoes Her Hair quality inspection process</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="faq-area-prod-desc">
                        <h4 class="text-center faq-header-title">Frequently Asked Questions</h4>
                        <button class="custom-accordion">How long will bundles last?</button>
                        <div class="accordion-panel">
                            <div class="accordion-inner">
                                <p>Bundles typically last for 6-8 months, if cared for properly.</p>
                            </div>
                        </div>
                        <button class="custom-accordion">How many bundles should I purchase?</button>
                        <div class="accordion-panel">
                            <div class="accordion-inner">
                                <p>For a full weave install for styles 18 inches or shorter, we recommend three bundles. However for styles longer than 18" inches, we recommend using 4-5 bundles to achieve a full look.</p>
                            </div>
                        </div>
                        <button class="custom-accordion">What length should I purchase?</button>
                        <div class="accordion-panel">
                            <div class="accordion-inner">
                                <p>All bundles must be measured to the stretch. With that said, please keep in mind that you will need to accommodate for its curl pattern when selecting your desired length. The Wavy hair has an average shrinkage of up to 2 inches; Curly hair has an average shrinkage of 2 inches; the Kinky Curly has an average shrinkage of 2-4 inches.</p>
                            </div>
                        </div>
                        <button class="custom-accordion">Returns &amp; Exchanges</button>
                        <div class="accordion-panel">
                            <div class="accordion-inner">
                                <p>If you are not completely satisfied with your purchase for any reason, you may exchange an eligible item for a different item, excluding the original shipping charges and taxes, within 3 days of the shipment received date, provided you follow the proper return procedure and eligibility guidelines.</p>
                                <p><em>*Note: Due to Federal health regulations, all product received in our Returns and Exchanges department must be in its original condition. We can not accept any hair that has been unraveled or manipulated in any shape or form.</em></p>
                            </div>
                        </div>
                    </div>
                    `;
                __HTML__ = $( __HTML__ );
                __HTML__.find( '.product' ).data( 'product', product );
                self.modal.find( '.main-content' ).empty();
                __HTML__.appendTo( this.modal.find( '.main-content' ) );
                self.doc( {
                    title: product.meta_title,
                    description: product.meta_description,
                    image: product.meta_image,
                    url: product.link,
                } );
                self.showing = true;
                self.modal.modal( 'show' );
                $('.rrssb-buttons').rrssb( {
                    title: product.code + ' - ' + product.name,
                    url: product.link,
                    image: product.image,
                    description: product.meta_description,
                    // emailSubject: '',
                    // emailBody: '',
                } )
                if ( product.gallery.length > 3 ) {
                    setTimeout( function() {
                        $(".product-gallery").owlCarousel2({
                            items :4,
                            loop: true,
                            dots: false,
                            nav : true,
                            navText: ['', ''],
                            responsive: {
                                0: 	  { items: 4 },
                                480:  { items: 4 },
                                768:  { items: 4 },
                                992:  { items: 4 },
                                1200: { items: 4 }
                            },
                        });
                    }, 500 );
                }
                if ( product.isVariable && self.color ) {
                    $('.product_options .options [type="radio"]').trigger( 'change' );
                }
            },
            close: function () {
                var self = this;
                if ( 'string' === typeof self.modal ) {
                    return;
                }
                if ( ! self.showing ) {
                    return;
                }
                self.showing = false;
                self.modal.modal( 'hide' );
            },
        },
        get_filters = function () {
        filters.category = $('#product-category').val() ? $('#product-category').val() : filters.category;
        filters.min_price = $('#min-price').val();
        filters.max_price = $('#max-price').val();
        filters.in_stock = $('#in-stock').is(':checked') ? 1 : 0;
        filters.promo = $('#promotions').is(':checked') ? 'yes' : 0;
        filters.featured = $('#featured').is(':checked') ? 'yes' : 0;
        filters.sorting = lsGet('sorting');
        return filters;
    },
        gen_html = function (products) {
            var html = '',
                results = $('#results');
            console.log( { products } );
            if (!products) {
                html += `
                <div class="col-sm-12">
                    <div class="alert alert-warning text-center padding-xl margin-top-lg">
                        <h4 class="margin-bottom-no">${lang.x_product}</h4>
                    </div>
                </div>
                `;
                results.html( html );
                return;
            }
            // if (site.settings.products_page == 1) {
            //     $('#results').empty();
            // }
    
            results.empty();
            var prods = $('<div class="products"></div>');
            $(prods).appendTo(results);
            $.each(products, function(index, product) {
                var colorsOpts = '';
                if ( product.isVariable && product.attributes.opts.hasOwnProperty( 'Color' ) ) {
                    var i = 0;
                    for ( var opt of product.attributes.opts.Color ) {
                        var optLower = opt.toLowerCase().replace( /\s/g, '-');
                        colorsOpts += `<a class="view-product" href="${product.link}" data-color="${opt}" style="--delay: ${i};"><span style="background: ${optLower};" title="${opt}"></span></a>`;
                        i++;
                    }
                    if ( colorsOpts.length ) {
                        colorsOpts = `<div class="product-color-btn">${colorsOpts}</div>`;
                    }
                }
                var prodHtml = `
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-5c">
                <div class="product product-${product.id}">
                    <div class="product-info-wrap product-single">
                        <div class="product-image">
                            <div class="product-all-btn-wrap">
                                <div class="product-btn-wrap">
                                    <a class="add-to-cart add-to-cart-btn" href="${product.add_to_cart}" data-id="${product.id}"><i class="fa fa-shopping-basket"></i><span>Add to Cart</span></a>
                                    ${ product.inWishList ? `<a class="remove-wishlist" data-id="${product.id}" href="${site.site_url + 'cart/remove_wishlist/' + product.id}"><i class="fa fa-heart"></i><span>Remove From Wishlist</span></a>` : `<a class="add-to-wishlist" data-id="${product.id}" href="${site.site_url + 'cart/add_wishlist/' + product.id}"><i class="fa fa-heart-o"></i><span>Add To Wishlist</span></a>`}
                                    <a class="view-product" href="${product.link}"><i class="fa fa-eye"></i><span>Quick View</span></a>
                                </div>
                                ${colorsOpts}
                            </div>
                            <div class="image">
                                <a class="view-product" href="${product.link}">
                                    <img src="${product.image}" alt="${product.name}">
                                </a>
                            </div>
                            <!-- /.image -->
                        </div>
                        <!-- /.product-image -->
                        <div class="product-info text-center">
                            <h3 class="name">
                                <a class="view-product" href="${product.link}">${product.name}</a>
                            </h3>
                            <div class="product-price">
                                ${'' !== product.sale_price ? `<span class="price">${product.sale_price}</span><span class="price-before-discount">${product.regular_price}</span>` : `<span class="price">${product.regular_price}</span>`}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `;
                prodHtml = $(prodHtml);
                prodHtml.find( '.product' ).data('product', product);
                prodHtml.appendTo(prods);
            });
        },
        searchProducts = function ( link ) {
            if (history.pushState) {
                var newurl = window.location.origin + window.location.pathname + '?page=' + filters.page;
                // var newurl = window.location.protocol + '//' + window.location.host + window.location.pathname + '?page=' + filters.page;
                window.history.pushState({ path: newurl, filters: filters }, '', newurl);
            }
            loader.show();
            var data = {};
            data[site.csrf_token] = site.csrf_token_value;
            data['filters'] = get_filters();
            data['format'] = 'json';
            setTimeout(function () {
                var ScrollToEl = $('.page-contents');
                if ( window.pageYOffset > ( ScrollToEl.offset().top + 10 ) ) {
                    $('html, body').animate( {
                        scrollTop: 0, //( ScrollToEl.offset().top - parseInt( ScrollToEl.css( 'margin-top' ) ) )
                    } );
                }
            }, 100);
            $.ajax({ url: site.shop_url + 'search?page=' + filters.page, type: 'POST', data: data, dataType: 'json' })
            .done(function (data) {
                var products = data.products;
                $('.page-info').empty();
                $('.page-pagination').empty();
                if (data.products) {
                    if (data.pagination) {
                        $('.page-pagination').html(data.pagination);
                    }
                    if (data.info) {
                        $('.page-info').text(lang.page_info.replace('_page_', data.info.page).replace('_total_', data.info.total));
                    }
                }
                gen_html(products);
            })
            .always(function () {
                loader.hide();
            });
            if ( location.href.includes('products') ) {
                if ( link ) {
                    window.history.pushState({ link: link, filters: filters }, '', link);
                    window.onpopstate = function (e) {
                        if (e.state && e.state.filters) {
                            filters = e.state.filters;
                            searchProducts();
                        } else {
                            filters.page = 1;
                            searchProducts();
                        }
                    };
                }
            }
        }
    // ~_~
    // $( document ).on( 'ready', function () {
        window.history.pushState( '', null, window.location.href );
        StickyHeader.init();
        var sorting = lsGet( 'sorting' );
        if ( sorting ) {
            $('#sorting').val( sorting );
        } else {
            lsSet('sorting', 'name-asc');
        }
    $( '.chosen-select' ).chosen( {
            width: '100%',
        } );
        if ( 'shop/products' !== pageNow ) {
            var __AutoSearch__ = $('#product-search');
            __AutoSearch__.autocomplete( {
                preserveInput: true,
                lookup: function( query, done ) {
                    if ( autoAjax ) {
                        autoAjax.abort();
                        autoAjax = false;
                    }
                    var cat = $('.select_category option:selected').val() || '';
                    var catFilters = { category: {}, subcategory: {} };
                    if ( cat ) {
                        cat = cat.split( '/' );
                        catFilters.category['id'] = cat[0];
                        if ( 2 === cat.length ) {
                            catFilters.subcategory['id'] = cat[1];
                        }
                    }
                    var filters =  $.extend( {}, { query: query }, catFilters );
                    if ( lastSearch.hash === JSON.stringify( filters ) && lastSearch.data.length ) {
                        done( { suggestions: lastSearch.data } );
                        return;
                    }
                    lastSearch.hash = JSON.stringify( filters );
                    autoAjax = $.ajax({
                        url: shop_url + 'search/',
                        type: 'POST',
                        data: $.extend( {}, __CSRF__, { filters: filters } ),
                        success: function ( resp ) {
                            if ( resp.products ) {
                                var suggestions = [];
                                for ( var i in resp.products ) {
                                    var product = resp.products[i];
                                
                                    suggestions.push( { value: product.name, product: product, data: product.id } );
                                }
                                lastSearch.data = suggestions;
                                done( { suggestions: suggestions } );
                            }
                        },
                        error: AjaxErrorHandler,
                    });
                
                },
                formatResult: function( suggestion, currentValue ) {
                    if ( ! currentValue ) {
                        return suggestion.value;
                    }
                    var pattern = '(' + $.Autocomplete.utils.escapeRegExChars( currentValue ) + ')'
                    var prod = suggestion.product;
                    var name = suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/&lt;(\/?strong)&gt;/g, '<$1>');
                    return `<div class="product" data-product='${ JSON.stringify( prod ) }'>
                    <a class="view-product" href="${prod.link}">
                        <div class="thumb"><img src="${prod.image}" alt="${prod.name}"></div>
                        <div class="prod-content">
                            <div class="title">${prod.category_name ? prod.category_name + ' > ' : ''}${prod.subcategory_name ? prod.subcategory_name + ' > ' : ''}${name}</div>
                            <div class="price">${prod.current_price}</div>
                        </div>
                    </a>
                </div>`;
                },
                onSelect: function( selection ) {
                    __AutoSearch__.blur();
                    productViewer.show( selection.product, true );
                    return false;
                }
            } );
        }
        productViewer.init();
        if ( viewProduct ) {
            productViewer.show( viewProduct );
        }
        loader.hide( 100 );
        if ( sys_alerts.length ) {
            for( var a of sys_alerts ) {
                sa_alert( a.t, a.m, a.l, a.o );
            }
        }
        $('.timing-wrapper').GS_Countdown();
        cartHandler( cart );
        addressEditor();
        /* OWL CAROUSEL */
        $(function () {
            // var dragging = true;
            // var owlElementID = "#owl-main";
        
            // function fadeInReset() {
            //     if (!dragging) {
            //         jQuery(owlElementID + " .caption .fadeIn-1, " + owlElementID + " .caption .fadeIn-2, " + owlElementID + " .caption .fadeIn-3").stop().delay(800).animate({ opacity: 0 }, { duration: 400, easing: "easeInCubic" });
            //     }
            //     else {
            //         jQuery(owlElementID + " .caption .fadeIn-1, " + owlElementID + " .caption .fadeIn-2, " + owlElementID + " .caption .fadeIn-3").css({ opacity: 0 });
            //     }
            // }
            //
            // function fadeInDownReset() {
            //     if (!dragging) {
            //         jQuery(owlElementID + " .caption .fadeInDown-1, " + owlElementID + " .caption .fadeInDown-2, " + owlElementID + " .caption .fadeInDown-3").stop().delay(800).animate({ opacity: 0, top: "-15px" }, { duration: 400, easing: "easeInCubic" });
            //     }
            //     else {
            //         jQuery(owlElementID + " .caption .fadeInDown-1, " + owlElementID + " .caption .fadeInDown-2, " + owlElementID + " .caption .fadeInDown-3").css({ opacity: 0, top: "-15px" });
            //     }
            // }
            //
            // function fadeInUpReset() {
            //     if (!dragging) {
            //         jQuery(owlElementID + " .caption .fadeInUp-1, " + owlElementID + " .caption .fadeInUp-2, " + owlElementID + " .caption .fadeInUp-3").stop().delay(800).animate({ opacity: 0, top: "15px" }, { duration: 400, easing: "easeInCubic" });
            //     }
            //     else {
            //         $(owlElementID + " .caption .fadeInUp-1, " + owlElementID + " .caption .fadeInUp-2, " + owlElementID + " .caption .fadeInUp-3").css({ opacity: 0, top: "15px" });
            //     }
            // }
            //
            // function fadeInLeftReset() {
            //     if (!dragging) {
            //         jQuery(owlElementID + " .caption .fadeInLeft-1, " + owlElementID + " .caption .fadeInLeft-2, " + owlElementID + " .caption .fadeInLeft-3").stop().delay(800).animate({ opacity: 0, left: "15px" }, { duration: 400, easing: "easeInCubic" });
            //     }
            //     else {
            //         jQuery(owlElementID + " .caption .fadeInLeft-1, " + owlElementID + " .caption .fadeInLeft-2, " + owlElementID + " .caption .fadeInLeft-3").css({ opacity: 0, left: "15px" });
            //     }
            // }
            //
            // function fadeInRightReset() {
            //     if (!dragging) {
            //         jQuery(owlElementID + " .caption .fadeInRight-1, " + owlElementID + " .caption .fadeInRight-2, " + owlElementID + " .caption .fadeInRight-3").stop().delay(800).animate({ opacity: 0, left: "-15px" }, { duration: 400, easing: "easeInCubic" });
            //     }
            //     else {
            //         jQuery(owlElementID + " .caption .fadeInRight-1, " + owlElementID + " .caption .fadeInRight-2, " + owlElementID + " .caption .fadeInRight-3").css({ opacity: 0, left: "-15px" });
            //     }
            // }
            //
            // function fadeIn() {
            //     jQuery(owlElementID + " .active .caption .fadeIn-1").stop().delay(500).animate({ opacity: 1 }, { duration: 800, easing: "easeOutCubic" });
            //     jQuery(owlElementID + " .active .caption .fadeIn-2").stop().delay(700).animate({ opacity: 1 }, { duration: 800, easing: "easeOutCubic" });
            //     jQuery(owlElementID + " .active .caption .fadeIn-3").stop().delay(1000).animate({ opacity: 1 }, { duration: 800, easing: "easeOutCubic" });
            // }
            //
            // function fadeInDown() {
            //     jQuery(owlElementID + " .active .caption .fadeInDown-1").stop().delay(500).animate({ opacity: 1, top: "0" }, { duration: 800, easing: "easeOutCubic" });
            //     jQuery(owlElementID + " .active .caption .fadeInDown-2").stop().delay(700).animate({ opacity: 1, top: "0" }, { duration: 800, easing: "easeOutCubic" });
            //     jQuery(owlElementID + " .active .caption .fadeInDown-3").stop().delay(1000).animate({ opacity: 1, top: "0" }, { duration: 800, easing: "easeOutCubic" });
            // }
            //
            // function fadeInUp() {
            //     jQuery(owlElementID + " .active .caption .fadeInUp-1").stop().delay(500).animate({ opacity: 1, top: "0" }, { duration: 800, easing: "easeOutCubic" });
            //     jQuery(owlElementID + " .active .caption .fadeInUp-2").stop().delay(700).animate({ opacity: 1, top: "0" }, { duration: 800, easing: "easeOutCubic" });
            //     jQuery(owlElementID + " .active .caption .fadeInUp-3").stop().delay(1000).animate({ opacity: 1, top: "0" }, { duration: 800, easing: "easeOutCubic" });
            // }
            //
            // function fadeInLeft() {
            //     jQuery(owlElementID + " .active .caption .fadeInLeft-1").stop().delay(500).animate({ opacity: 1, left: "0" }, { duration: 800, easing: "easeOutCubic" });
            //     jQuery(owlElementID + " .active .caption .fadeInLeft-2").stop().delay(700).animate({ opacity: 1, left: "0" }, { duration: 800, easing: "easeOutCubic" });
            //     jQuery(owlElementID + " .active .caption .fadeInLeft-3").stop().delay(1000).animate({ opacity: 1, left: "0" }, { duration: 800, easing: "easeOutCubic" });
            // }
            //
            // function fadeInRight() {
            //     jQuery(owlElementID + " .active .caption .fadeInRight-1").stop().delay(500).animate({ opacity: 1, left: "0" }, { duration: 800, easing: "easeOutCubic" });
            //     jQuery(owlElementID + " .active .caption .fadeInRight-2").stop().delay(700).animate({ opacity: 1, left: "0" }, { duration: 800, easing: "easeOutCubic" });
            //     jQuery(owlElementID + " .active .caption .fadeInRight-3").stop().delay(1000).animate({ opacity: 1, left: "0" }, { duration: 800, easing: "easeOutCubic" });
            // }
        
            // $(owlElementID).owlCarousel({
            //
            //     autoPlay: 5000,
            //     stopOnHover: true,
            //     navigation: true,
            //     pagination: true,
            //     singleItem: true,
            //     addClassActive: true,
            //     transitionStyle: "fade",
            //     navigationText: ["<i class='icon fa fa-angle-left'></i>", "<i class='icon fa fa-angle-right'></i>"],
            //
            //     afterInit: function() {
            //         fadeIn();
            //         fadeInDown();
            //         fadeInUp();
            //         fadeInLeft();
            //         fadeInRight();
            //     },
            //
            //     afterMove: function() {
            //         fadeIn();
            //         fadeInDown();
            //         fadeInUp();
            //         fadeInLeft();
            //         fadeInRight();
            //     },
            //
            //     afterUpdate: function() {
            //         fadeIn();
            //         fadeInDown();
            //         fadeInUp();
            //         fadeInLeft();
            //         fadeInRight();
            //     },
            //
            //     startDragging: function() {
            //         dragging = true;
            //     },
            //
            //     afterAction: function() {
            //         fadeInReset();
            //         fadeInDownReset();
            //         fadeInUpReset();
            //         fadeInLeftReset();
            //         fadeInRightReset();
            //         dragging = false;
            //     }
            //
            // });
            //
            // if ($(owlElementID).hasClass("owl-one-item")) {
            //     jQuery(owlElementID + ".owl-one-item").data('owlCarousel').destroy();
            // }
            //
            // $(owlElementID + ".owl-one-item").owlCarousel({
            //     singleItem: true,
            //     navigation: false,
            //     pagination: false
            // });
        
            $('.home-owl-carousel').each(function(){
            
                var owl = $(this);
                var  itemPerLine = owl.data('item');
                if(!itemPerLine){
                    itemPerLine = 8;
                }
                // owl.owlCarousel({
                //     items : itemPerLine,
                //     itemsDesktop : [1199,3],
                //     itemsTablet:[991,2],
                //     navigation : true,
                //     pagination : false,
                //     navigationText: ["", ""]
                // });
                owl.owlCarousel2({
                    rtl: false,
                    margin: 30,
                    slideBy: 1,
                    autoplay: 0,
                    autoplayHoverPause: 0,
                    autoplayTimeout: 0 ,
                    autoplaySpeed: 1000 ,
                    startPosition: 0 ,
                    mouseDrag: 1,
                    touchDrag: 1 ,
                    autoWidth: false,
                    responsive: {
                        0: 	{ items: 1 } ,
                        480: { items: 2 },
                        768: { items: 2 },
                        992: { items: 3 },
                        1200: {items: itemPerLine }
                    },
                    dotClass: "owl2-dot",
                    dotsClass: "owl2-dots",
                    dotsSpeed:500 ,
                    nav: true ,
                    loop: false ,
                    navSpeed: 500 ,
                    navClass: ["owl-prev", "owl-next"],
                    navText: ["", ""]
                });
            
            });
        
            $('.homepage-owl-carousel').each(function(){
            
                var owl = $(this);
                var  itemPerLine = owl.data('item');
                if(!itemPerLine){
                    itemPerLine = 4;
                }
                owl.owlCarousel({
                    items : itemPerLine,
                    itemsTablet:[991,2],
                    itemsDesktop : [1199,3],
                    navigation : true,
                    pagination : false,
                
                    navigationText: ["", ""]
                });
            });
        
            // $(".blog-slider").owlCarousel({
            //     items : 3,
            //     itemsDesktopSmall :[979,2],
            //     itemsDesktop : [1199,3],
            //     navigation : true,
            //     slideSpeed : 300,
            //     pagination: false,
            //     navigationText: ["", ""]
            // });
            //
            // $(".best-seller").owlCarousel({
            //     items : 3,
            //     navigation : true,
            //     itemsDesktopSmall :[979,2],
            //     itemsDesktop : [1199,2],
            //     slideSpeed : 300,
            //     pagination: false,
            //     paginationSpeed : 400,
            //     navigationText: ["", ""]
            // });
            // $(".sidebar-carousel").owlCarousel({
            //     items : 1,
            //     itemsTablet:[978,1],
            //     itemsDesktopSmall :[979,2],
            //     itemsDesktop : [1199,1],
            //     navigation : true,
            //     slideSpeed : 300,
            //     pagination: false,
            //     paginationSpeed : 400,
            //     navigationText: ["", ""]
            // });
            // $(".brand-slider").owlCarousel({
            //     items :6,
            //     navigation : true,
            //     slideSpeed : 300,
            //     pagination: false,
            //     paginationSpeed : 400,
            //     navigationText: ["", ""]
            // });
            // $("#advertisement").owlCarousel({
            //     items : 1,
            //     itemsTablet:[978,1],
            //     itemsDesktopSmall :[979,1],
            //     itemsDesktop : [1199,1],
            //     navigation : true,
            //     slideSpeed : 300,
            //     pagination: true,
            //     paginationSpeed : 400,
            //     navigationText: ["", ""]
            // });
        });
        /* LAZY LOAD IMAGES USING ECHO */
        echo.init({
            offset: 100,
            throttle: 250,
            unload: false
        });
        /* RATING */
        // $('.rating').rateit({max: 5, step: 1, value : 4, resetable : false , readonly : true});
        /* PRICE SLIDER */
        $('.price-slider').each( function(){
            var self = $(this);
            self.slider({
                min: parseInt( self.data('min') || 0 ),
                max: parseInt( self.data('max') || 1000 ),
                step: self.data('step') || 5,
                value: self.data('value') || [ parseInt( self.data('min') || 5 ), parseInt( (self.data('max') ? self.data('max') /2 : 500 ) ) ],
                handle: 'square',
                formatter: function( val ) {
                    return val instanceof Array ? val.map( function( x ) {
                        return formatMoney( x );
                    } ).join( ', ' ) : formatMoney( val );
                },
            });
        } );
        /* SINGLE PRODUCT GALLERY */
        $('#owl-single-product').owlCarousel({
            items:1,
            itemsTablet:[768,3],
            itemsDesktop : [1199,1],
            itemsDesktopSmall : [768,3]
        
        });
        $('#owl-single-product-thumbnails').owlCarousel({
            items: 4,
            pagination: true,
            rewindNav: true,
            itemsTablet : [992,4],
            itemsDesktopSmall :[768,4],
            itemsDesktop : [992,1]
        });
        $('#owl-single-product2-thumbnails').owlCarousel({
            items: 6,
            pagination: true,
            rewindNav: true,
            itemsTablet : [768, 4],
            itemsDesktop : [1199,3]
        });
        $('.single-product-slider').owlCarousel({
            stopOnHover: true,
            rewindNav: true,
            singleItem: true,
            pagination: true
        });
        /* WOW */
        // new WOW().init();
        /* TOOLTIP */
        $("[data-toggle='tooltip']").tooltip();
        $('.tip').tooltip({ container: 'body' }); // Tooltip
    
        // sys.
        $(document)
        .on( 'click', '.view-product', function( e ) {
            e.preventDefault();
            var product = $(this).closest( '.product' ).data( 'product' );
            if ( ! product ) {
                sa_alert( 'Error!', 'Invalid Request', 'error', false );
            } else {
                productViewer.show( product, true, $(this).data('color') );
            }
        } )
        .on( 'click', '.contact-us, .email-modal', function(e){
            e.preventDefault();
            email_form();
        } )
        .on( 'submit', '#subscribe', subscription_form )
        .on( 'click', `.${addToWishList}, .${removeFromWishList}`, function( e ) {
            e.preventDefault();
            var el = $(this),
                add = el.hasClass( addToWishList ),
                id  = el.attr('data-id'),
                currentProdButtons = $( '.product-' + id ).find( `.${addToWishList}, .${removeFromWishList}` );
            if( el.inProgress( true ) ) {
                return false;
            }
            // Request.
            $.ajax( {
                url: site_url + (add ? 'cart/add_wishlist/' : 'cart/remove_wishlist/') + id,
                type: 'GET',
                dataType: 'json'
            } ).done( function( res ) {
                if ( res.total ) {
                    $( '#total-wishlist' ).attr( 'title', lang.wishlist_count.replace( /\%[sdf]/ig, res.total ) );
                } else if ( res.redirect ) {
                    window.location.href = res.redirect;
                    return false;
                }
                if ( in_array( res.level, [ 'error', 'warning' ] ) ) {
                    sa_alert( res.status, res.message, res.level, 'warning' === res.level );
                } else {
                    if ( 'shop/wishlist' === pageNow ) {
                        if ( ! add ) {
                            el.closest('tr' ).remove();
                        } else {
                            // add from sidebar or slider on the wishlist page.
                            var prodData = el.closest( '.product' ).parseData( 'product' );
                            if ( prodData && prodData.hasOwnProperty( 'id' ) ) {
                                var __HTML__ = `
                                    <tr class="product product-${prodData.id}">
                                        <td class="col-xs-2">
                                            <a class="view-product" href="${prodData.link}">
                                                <img src="${prodData.image}" alt="${prodData.name}" class="img-responsive">
                                            </a>
                                        </td>
                                        <td>
                                            <a class="view-product" href="${prodData.link}">${prodData.name}</a>
                                            <p>${prodData.meta_description}</p>
                                        </td>
                                        <td>
                                            ${ prodData.onSale ? `<del class="text-red">${prodData.sale_price}</del><br>${prodData.regular_price}` : prodData.regular_price }
                                        </td>
                                        <td>${ prodData.quantity ? lang.yes : lang.no }</td>
                                        <td class="col-xs-2">
                                            <div class="btn-group btn-group-justified" role="group">
                                                <div class="btn-group" role="group">
                                                    <a href="${prodData.add_to_cart}" class="tip btn btn-sm btn-theme add-to-cart" data-id="${prodData.id}" title="${lang.add_to_cart}"><i class="fa fa-shopping-cart"></i></a>
                                                </div>
                                                <div class="btn-group" role="group">
                                                    <a href="${site_url}cart/remove_wishlist/${prodData.id}" class="tip btn btn-sm btn-danger remove-wishlist" data-id="${prodData.id}" title="${lang.remove_from_wishlist}"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    `;
                                __HTML__ = $(__HTML__);
                                __HTML__.data( 'product', prodData );
                                __HTML__.appendTo( $('.wishlist-table tbody') );
                            }
                        }
                    }
                    currentProdButtons.attr( 'href', '#' );
                    currentProdButtons.rc( add ? addToWishList : removeFromWishList ).ac( add ? removeFromWishList : addToWishList );
                    currentProdButtons.find( '.fa' ).ac( add ? 'fa-heart' : 'fa-heart-o' ).rc( add ? 'fa-heart-o' : 'fa-heart' );
                    currentProdButtons.find('span').text( add ? lang.remove_from_wishlist : lang.add_to_wishlist );
                }
                currentProdButtons.setProgressing( false );
            } );
        } )
        .on( 'click', '.icon-search', function( e ) {
            e.preventDefault();
            var self = $(this), body = $('body'), search = $('.site-search .search');
            if ( self.hasClass( 'active' ) ) {
                self.rc( 'active' );
                body.rc( 'has-popup open-search' );
            } else {
                search.css( 'left', ( ( body.width() - 300 ) / 2 ) );
                self.ac( 'active' );
                body.ac( 'has-popup open-search' );
            }
        } )
        .on( 'click mouseover', '.gallery-preview', function ( e ) {
            e.preventDefault();
            $( this ).closest( '.gallery-holder' ).find( '.product-preview img' ).attr( 'src', $( this ).data( 'src' ) );
        } )
        .on( 'change', '.product_options [type="radio"]', function() {
            var self = $(this),
                optGroup = self.closest( '.product_options' ),
                options = optGroup.find('.options' ),
                addToCart = self.closest( '.product' ).find( '.add-to-cart' ),
                optionsChecked = 0;
        
            options.each( function () {
                optionsChecked += $(this).find( '[type="radio"]:checked' ).length;
            } );
            if ( optionsChecked === options.length ) {
                addToCart.rc( 'disabled' );
            } else {
                addToCart.ac( 'disabled' );
            }
        } )
        .on( 'change', '.product_options .options:eq(0) [type="radio"]', function() {
            // show/hide dependent attribute selector.
            var masterOpt = $(this),
                optGroup = masterOpt.closest( '.product_options' ),
                product = masterOpt.closest('.product').parseData( 'product' ),
                optMap = product.attributes.optMap;
            if ( ! masterOpt.is( ':checked' ) ) {
                return;
            }
            if ( masterOpt.length && masterOpt.val() ) {
                var n = masterOpt.attr('name'),
                    v = masterOpt.val();
                if ( optMap.hasOwnProperty( n) && optMap[n].hasOwnProperty( v ) ) {
                    for ( var subOpt in optMap[n][v] ) {
                        var subOptVals = optMap[n][v][subOpt];
                        var list = optGroup.find( '.option-' + subOpt.toLowerCase() + ' li' );
                        if ( subOptVals.length ) {
                            list.hide();
                            list.find( '[type="radio"]' ).prop( 'checked', false )
                            $(subOptVals.map( function( opt ) {
                                return `.option-${subOpt.toLowerCase()}-${opt.toLowerCase()}`;
                            }).join(',')).show();
                        }
                        list.find( '[type="radio"]' ).trigger( 'change' );
                    }
                }
            }
        } )
        .on( 'mouseenter', '.cart-btn-wrap', function() {
            var self = $(this).find( '.add-to-cart' ),
                options = self.closest( '.product-info' ).find( '.product_options .options' ),
                optionNeeded = false, optionsChecked = 0;
            if ( options.length ) {
                options.each( function() {
                    optionsChecked += $(this).find( '[type="radio"]:checked' ).length;
                } );
                optionNeeded = options.length !== optionsChecked;
            }
            if ( optionNeeded && ! self.data('bs.tooltip') ) {
                self.tooltip( {
                    container: 'body',
                    html: true,
                    trigger: 'manual',
                    title: self.data('tooltip'),
                } );
            }
            if ( optionNeeded ) {
                self.tooltip( 'show' );
            }
        } )
        .on( 'mouseleave', '.cart-btn-wrap', function() {
            $(this).find( '.add-to-cart' ).tooltip( 'hide' );
        } )
        .on( 'click', '.back-to-top', function () {
            $( 'body,html' ).animate( { scrollTop: 0 }, 500 )
        } )
        .on('click', '.forgot-password', function (e) {
            e.preventDefault();
            prompt( lang.reset_pw, lang.type_email )
        })
        .on('click', '.reload-captcha', function (e) {
            e.preventDefault();
            let link = $(this).attr('href');
            $.ajax({ url: link + '?width=210&height=34', type: 'GET' }).done(function (data) {
                if (data) {
                    $('.captcha-image').html(data);
                } else {
                    sa_alert('Error!', 'Something went wrong.', 'error', true);
                }
            });
        })
        .on('click', '.guest-checkout', function (e) {
            e.preventDefault();
            $('.nav-tabs a:last').tab('show');
            return false;
        });
        if ( in_array( v, [ 'products', 'brand', 'category' ] ) ) {
            $( document )
            .on( 'change', '#sorting', function ( e ) {
                lsSet( 'sorting', $( this ).val() );
                searchProducts();
                return false;
            } )
            .on( 'keyup', '#product-search', __debounce( function ( e ) {
                e.preventDefault();
                filters.query = $( this ).val();
                filters.page = 1;
                searchProducts();
                return false;
            }, 250 ) )
            .on( 'click', '.reset_filters_brand', function ( e ) {
                filters.brand = null;
                filters.page = 1;
                searchProducts();
                $( this ).closest( 'li' ).lsDel();
            } )
            .on( 'click', '.reset_filters_category', function ( e ) {
                filters.category = null;
                filters.page = 1;
                searchProducts();
                $( this ).closest( 'li' ).lsDel();
            } )
            .on( 'change', '#in-stock, #promotions, #featured', function () {
                filters.page = 1;
                searchProducts();
            } )
            .on( 'click', '.page-pagination a', function ( e ) {
                e.preventDefault();
                var link = $( this ).attr( 'href' );
                if ( ! link || '#' === link) {
                    return;
                }
                var p = link.split( 'page=' );
                if (p[1]) {
                    var pp = p[1].split( '&' );
                    filters.page = pp[0];
                } else {
                    filters.page = 1;
                }
                searchProducts( link );
                return false;
            } );
            $('#price-range').on( 'slideStop', function(){
                filters.page = 1;
                searchProducts();
            });
            // Top search on products page - dont load page but recall ajax
            $( '#product-search-form' ).submit( function ( e ) {
                e.preventDefault();
                filters.query = $( '#product-search' ).val();
                filters.page = 1;
                searchProducts();
                // $('#product-search').val('');
                return false;
            } );
            if (filters.query) {
                $('#product-search').val(filters.query);
            }
            
            searchProducts();
        }
        // Form validation
        $('.validate').formValidation({
            framework: 'bootstrap',
            // icon: {
            //     valid: 'fa fa-ok',
            //     invalid: 'fa fa-remove',
            //     validating: 'fa fa-refresh'
            // },
            message: lang.required_invalid,
        });
        // @XXX shipping address!
        $('#same_as_billing').change(function (e) {
            if ($(this).is(':checked')) {
                $('#shipping_line1').val($('#billing_line1').val()).change();
                $('#shipping_line2').val($('#billing_line2').val()).change();
                $('#shipping_city').val($('#billing_city').val()).change();
                $('#shipping_state').val($('#billing_state').val()).change();
                $('#shipping_postal_code').val($('#billing_postal_code').val()).change();
                $('#shipping_country').val($('#billing_country').val()).change();
                $('#shipping_phone').val($('#phone').val()).change();
                $('#guest-checkout').data('formValidation').resetForm();
            }
        });
        /**
         * sticky order details
         */
        $('#nav-tabContent').scrollToFixed({
            minWidth: 1000,
            limit: function() {
                return ($('#footer').offset().top - $('#nav-tabContent').outerHeight(true));
            },
            zIndex: false
        });
        $('.checkout-progress-sidebar').scrollToFixed({
            minWidth: 1000,
            limit: function() {
                return ( $('#footer').offset().top - $('.checkout-progress-sidebar ').outerHeight(true) );
            },
            zIndex: false
        });
    // } );
    
    $( window )
    .on( 'resize', function() {
        var body    = $('body'),
            win     = $(window),
            mainCat = $('.categories-main'),
            mcTitle = mainCat.find('.categories-content-title'),
            search  = $('.site-search .search');
        if ( body.hasClass( 'open-search' ) ) {
            search.css( 'left', ( ( body.width() - 300 ) / 2 ) );
        }
        if ( win.width() <= 1230 ) {
            mainCat.rc( 'showing' );
            mcTitle.attr('aria-expanded', false );
        } else {
            mainCat.ac( 'showing' );
            mcTitle.attr('aria-expanded', true );
        }
    } ).trigger( 'resize' )
    .on( 'scroll', function () {
        if ( $( this ).scrollTop() > scrollTopShowOffset ) {
            $( '.back-to-top' ).fadeIn();
        } else {
            $( '.back-to-top' ).fadeOut();
        }
    } );
    
    $('.contentslider').each(function () {
            var $slider = $(this),
                $panels = $slider.children(),
                data = $slider.data(),
                $totalItem = $panels.length;
            // Apply Owl Carousel
            $slider.on("initialized.owl.carousel2", function () {
                setTimeout(function() {
                    $slider.parent().find('.loading-placeholder').hide();
                }, 1000);

            });
            // Apply Owl Carousel
            $slider.owlCarousel2({
                responsiveClass: true,
                mouseDrag: true,
                video:true,
                autoWidth: (data.autowidth == 'yes') ? true : false,
                animateIn: data.transitionin,
                animateOut: data.transitionout,
                lazyLoad: (data.lazyload == 'yes') ? true : false,
                autoplay: (data.autoplay == 'yes') ? true : false,
                autoHeight: (data.autoheight == 'yes') ? true : false,
                autoplayTimeout: data.delay * 1000,
                smartSpeed: data.speed * 1000,
                autoplayHoverPause: (data.hoverpause == 'yes') ? true : false,
                center: (data.center == 'yes') ? true : false,
                loop: (data.loop == 'yes') ? true : false,
                dots: (data.pagination == 'yes') ? true : false,
                rtl: (data.rtl == 'yes') ? true : false,
                nav: true,
                dotClass: "owl2-dot",
                dotsClass: "owl2-dots",
                margin: data.margin,
                navText: ['',''],
                navClass: ["owl2-prev", "owl2-next"],
                responsive: {
                    0: {
                        items   : data.items_column4,
                        nav     : ($totalItem > data.items_column4 && data.arrows == 'yes') ? true : false
                    },
                    480: {
                        items   : data.items_column3,
                        nav     : ($totalItem > data.items_column3 && data.arrows == 'yes') ? true : false
                    },
                    768: {
                        items   : data.items_column2,
                        nav     : ($totalItem > data.items_column2 && data.arrows == 'yes') ? true : false
                    },
                    992: {
                        items   : data.items_column1,
                        nav     : ($totalItem > data.items_column1 && data.arrows == 'yes') ? true : false
                    },
                    1200: {
                        items   : data.items_column0,
                        nav     : ($totalItem > data.items_column0 && data.arrows == 'yes') ? true : false
                    }
                }
            });
        });
    
    var total_item = $(".ui-banner-slider .item").length;
    $(".ui-banner-slider").owlCarousel2({
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        autoplay: true,
        autoplayTimeout: 5000,
        autoplaySpeed:  1000,
        smartSpeed: 500,
        autoplayHoverPause: true,
        startPosition: 0,
        mouseDrag:  true,
        touchDrag: true,
        dots: true,
        autoWidth: false,
        dotClass: "owl2-dot",
        dotsClass: "owl2-dots",
        loop: true,
        navText: ["Next", "Prev"],
        navClass: ["owl2-prev", "owl2-next"],
        autoHeight: false,
        responsive: {
            0:{ items: 1,
                nav: total_item <= 1 ? false : ((false ) ? true: false),
            },
            480:{ items: 1,
                nav: total_item <= 1 ? false : ((false ) ? true: false),
            },
            768:{ items: 1,
                nav: total_item <= 1 ? false : ((false ) ? true: false),
            },
            992:{ items: 1,
                nav: total_item <= 1 ? false : ((false ) ? true: false),
            },
            1200:{ items: 1,
                nav: total_item <= 1 ? false : ((false ) ? true: false),
            }
        },
    });
    $(".sidebar-slider").owlCarousel2({
        rtl: false,
        margin: 0,
        slideBy: 1,
        autoplay: 0,
        autoplayHoverPause: 0,
        autoplayTimeout: 0 ,
        autoplaySpeed: 1000 ,
        startPosition: 0 ,
        mouseDrag: 1,
        touchDrag: 1 ,
        autoWidth: false,
        responsive: {
            0: 	{ items: 1 } ,
            480: { items: 1 },
            768: { items: 1 },
            992: { items: 1 },
            1200: {items: 1}
        },
        dotClass: "owl2-dot",
        dotsClass: "owl2-dots",
        dots: true ,
        dotsSpeed:500 ,
        nav: false ,
        loop: true ,
        navSpeed: 500 ,
        navText: ["&#171 ", "&#187 "],
        navClass: ["owl2-prev", "owl2-next"]
    });
    $('.extraslider-inner').owlCarousel2({
        rtl: false,
        margin: 30,
        slideBy: 1,
        autoplay: false,
        autoplayHoverPause: 0,
        autoplayTimeout: 5000,
        autoplaySpeed: 1000,
        startPosition: 0,
        mouseDrag: true,
        touchDrag: true,
        autoWidth: false,
        responsive: {
            0: 	{ items: 1 } ,
            480: { items: 1 },
            768: { items: 1 },
            992: { items: 1 },
            1200: {items: 2 }
        },
        dotClass: 'owl2-dot',
        dotsClass: 'owl2-dots',
        dots: true,
        dotsSpeed: 500,
        nav: true,
        loop: false,
        navSpeed: 500,
        navText: ['&#171;', '&#187;'],
        navClass: ['owl2-prev', 'owl2-next']
    });
    $('.most-viewed-carousel').owlCarousel2({
        rtl: false,
        margin: 30,
        slideBy: 1,
        autoplay: 0,
        autoplayHoverPause: 0,
        autoplayTimeout: 0 ,
        autoplaySpeed: 1000 ,
        startPosition: 0 ,
        mouseDrag: 1,
        touchDrag: 1 ,
        autoWidth: false,
        responsive: {
            0: 	{ items: 1 } ,
            480: { items: 1 },
            768: { items: 2 },
            992: { items: 3 },
            1200: {items: 4}
        },
        dotClass: "owl2-dot",
        dotsClass: "owl2-dots",
        dots: false ,
        dotsSpeed:500 ,
        nav: true ,
        loop: true ,
        navSpeed: 500 ,
        navText: ["&#171 ", "&#187 "],
        navClass: ["owl2-prev", "owl2-next"]

    });
    
    var inputs = document.querySelectorAll('.file');
    var submit_btn = document.querySelector('#submit-container');
    if (submit_btn) {
        submit_btn.style.display = 'none';
    }
    Array.prototype.forEach.call(inputs, function (input) {
        var label = input.nextElementSibling,
            labelVal = label.innerHTML;
        
        input.addEventListener('change', function (e) {
            var fileName = '';
            if (this.files && this.files.length > 1) {
                fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                if (submit_btn) {
                    submit_btn.style.display = 'inline-block';
                }
            } else {
                fileName = e.target.value.split('\\').pop();
                if (submit_btn) {
                    submit_btn.style.display = 'none';
                }
            }
            
            if (fileName) {
                label.querySelector('span').innerHTML = fileName;
                if (submit_btn) {
                    submit_btn.style.display = 'inline-block';
                }
            } else {
                label.innerHTML = labelVal;
                if (submit_btn) {
                    submit_btn.style.display = 'none';
                }
            }
        });
    });
    
})( jQuery, window, document, site, cart, lang, restoreHome, sys_alerts, filters, viewProduct );
