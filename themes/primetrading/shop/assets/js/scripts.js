/** noinspection ES6ConvertVarToLetConst */
( function ( $, window, document, site, cart, lang, restoreHome, sys_alerts, filters, viewProduct, pageNow ) {
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
     * @param {Object} [options={}]
     * @return {countDownTimer}
     */
    function countDownTimer( $timerEl, options ) {
        var self = this,
            defaults = {
                opts: { stop: '', now: new Date().getTime() },
                deltas: { days: 0, hours: 0, minutes: 0, seconds: 0 },
                timer: 0,
                format: '%d : %h : %m : %s',
                setLeadingZeros: false,
                expired: 'EXPIRED',
                paused: false,
            };
        options = $.extend( {}, defaults, options );
        
        self.$timerEl = $timerEl;
        self.opts = options.opts;
        self.deltas = options.deltas;
        self.timer = options.timer;
        self.format = options.format;
        self.setLeadingZeros = options.setLeadingZeros;
        self.expired = options.expired;
        self.paused = options.paused;
        self.delta = 0;
        /**
         * Initialize the Countdown Timer
         * @return {countDownTimer}
         */
        self.init = function() {
            if( self.$timerEl.attr('data-countdown') !== undefined ) {
                var countdown = JSON.parse( self.$timerEl.attr('data-countdown').replace( /'/g, '"' ) );
                self.$timerEl.removeAttr( 'data-countdown' );
                if( countdown.stop.length > 0 ) {
                    self.opts.stop = countdown.stop;
                }
                if( countdown.now && countdown.now.length > 0 ) {
                    self.opts.now = countdown.now;
                }
                if( countdown.paused && countdown.paused === true ) {
                    self.paused = true;
                }
            }
            if ( self.opts.stop.length === 0 ) {
                showExpired();
            }
            self.opts.stop = getTime( self.opts.stop );
            self.opts.now  = getTime( self.opts.now );
            if( self.$timerEl.attr('data-format') !== undefined ) {
                self.format = self.$timerEl.attr('data-format');
                self.$timerEl.removeAttr( 'data-format' );
            }
            if( self.$timerEl.attr('data-leading_zero') !== undefined && self.$timerEl.attr('data-leading_zero') === true ) {
                self.$timerEl.removeAttr( 'data-leading_zero' );
                self.setLeadingZeros = true;
            }
            if( self.$timerEl.attr('data-expired') !== undefined ) {
                self.expired = self.$timerEl.attr('data-expired');
                self.$timerEl.removeAttr( 'data-expired' );
            }
            updateDelta();
            calculate();
            self.$timerEl.addClass( 'timer-loaded' );
            self.$timerEl.trigger( 'timer.init', [ self.delta, self.deltas ] );
            if( ! self.paused ) {
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
            if( self.paused === true ) {
                updateDelta();
                self.paused = false;
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
            self.paused = true;
            clearInterval( self.timer );
            self.$timerEl.trigger( 'timer.stop', [ self.delta, self.deltas ] );
            return self;
        };
        var getTime = function( time ) {
            if ( /^\d+$/g.test( time ) ) {
                return time;
            } else {
                time = new Date( time );
                // noinspection EqualityComparisonWithCoercionJS
                if ( time == 'Invalid Date' ) {
                    return '';
                }
                return time.getTime();
            }
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
    function dismissalAlert( el, content, options ) {
        if ( ! content || ! el ) {
            return el;
        }
        options = $.extend( {}, { type: 'success', duration: 4000, easing: 'linear' }, options);
        var __HTML__ = `
            <div class="alert alert-${options.type} has-dismissal-progress" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                ${content}
                <div class="progress dismissal-progress"><div class="progress-bar dismissal-bar" style=""></div></div>
            </div>
        `;
        __HTML__ = $( __HTML__ );
        __HTML__.appendTo( el );
        __HTML__.find( '.dismissal-bar' ).animate( {
            width: 0
        }, options.duration, options.easing, function() {
            el.triggerHandler( 'alert.dismissed' );
            __HTML__.remove();
        } );
        return el;
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
        dismissalAlert: function( content, options ) {
            return dismissalAlert( $(this), content, options );
        },
        GS_Countdown: function( options ) {
            $(this).not('.timer-loaded').each( function(){
                $(this).data( 'GS_Countdown', new countDownTimer( $(this), options || {} ) )
            });
        },
        ac: function( value ) {
            return $(this).addClass( value );
        },
        rc: function( value ) {
            return $(this).removeClass( value );
        },
        hc: function( value ) {
            return $(this).hasClass( value );
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
        countdownGlobalSettings = {
            setLeadingZeros: true,
            format: `<div class="box-wrapper"><div class="date box"><span class="key">%d</span> <span class="value">${lang._days_}</span></div></div><div class="box-wrapper"><div class="date box"><span class="key">%h</span> <span class="value">${lang._hours_}</span></div></div><div class="box-wrapper"><div class="date box"><span class="key">%m</span> <span class="value">${lang._mins_}</span></div></div><div class="box-wrapper"><div class="date box"><span class="key">%s</span> <span class="value">${lang._secs_}</span></div></div>`,
        },
        __CACHES__ = [];
    // csrf
    __CSRF__[site.csrf_token] = site.csrf_token_value;
    __CACHES__['prodModal'] = [];
    
    var addToWishList = 'add-to-wishlist',
        removeFromWishList = 'remove-wishlist',
        processing = 'processing',
        out_of_stock = 'out_of_stock',
        site_url = site.site_url,
        shop_url = site.shop_url,
        prod_url = site_url + 'product/',
        autoAjax = false, ajaxify = false, ajaxify2 = false,
        scrollTopShowOffset = ( $( document ).height() * .125 ),
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
        AjaxErrorHandler = function (jqXHR, textStatus) {
            loader.hide();
            if ('abort' === textStatus) {
                return;
            } else {
                sa_alert('Error!', lang.ajax_call_failed, 'error', true);
            }
        },
        winLoc = function() { return window.location.href; },
        lastSearch = {
            hash: '',
            data: []
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
                    error: AjaxErrorHandler,
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
                error: AjaxErrorHandler,
            });
        },
        /**
         * Show SweetAlert with message.
         *
         * @param {string} title
         * @param {string} message
         * @param {string} [level="success"]
         * @param {boolean} [overlay=false]
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
        /**
         *
         * @param action
         * @param message
         * @param method
         * @param form_data
         * @param {Function} callback
         */
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
                            success: function ( data ) {
                                if ( data.hasOwnProperty( 'cart' ) ) {
                                    cart = data.cart;
                                    $(document).triggerHandler( 'cart.update', data.cart );
                                }
                                if ( callback && 'function' === typeof callback ) callback( data );
                                if ( data.redirect ) {
                                    window.location.href = data.redirect;
                                    return false;
                                } else sa_alert( data.status, data.message );
                            },
                            error: AjaxErrorHandler,
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
                            error: AjaxErrorHandler,
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
                    var addIn = addEl.find( 'input[type="radio"]' );
                    addIn.prop( 'checked', true );
                    setTimeout( function() {
                        addIn.trigger( 'change' );
                    }, 50 );
                    addEl.addClass( 'active' );
                    phoneEl.addClass( 'active' );
                }
            };
            function editAddress( address ) {
                address = $.fn.extend( {}, {
                    id: '',
                    title: '',
                    line1: '',
                    line2: '',
                    country: '',
                    state: '',
                    city: '',
                    postal_code: '',
                    area: '',
                    zone: '',
                    phone: '',
                }, address );
                var __FORM__ = '',
                    keys = Object.keys( address ),
                    placeholders = {
                        title: 'Title (Home/Office)',
                        line1: 'Address Line 1',
                        line2: 'Address Line 2',
                        country: 'Country',
                        state: 'State',
                        city: 'City',
                        postal_code: 'Zip Code',
                        //area: 'Area',
                        phone: '+1 (000) 000–0000',
                    }, masks = [];
                // The form.
                __FORM__ += `<span class="text-bold padding-bottom-md">${lang.fill_form}</span>
                <hr class="swal2-spacer padding-bottom-xs" style="display: block;">
                <form action="${site.shop_url}address/${address.id}" id="address-form" class="padding-bottom-md">
                    <input type="hidden" name="${site.csrf_token}" value="${site.csrf_token_value}">
                `;
                for( var k of keys ) {
                    if ( in_array( k, [ 'id', 'company_id', 'updated_at', 'area_name' ] ) ) {
                        continue;
                    }
                    if ( 'country' === k ) {
                        __FORM__ += `<div class="col-sm-6 form-group">
                            <label class="sr-only" for="address-${k}">${lang[k]}</label>
                            <select name="${k}" id="address-${k}" placeholder="${lang.select_x.replace( '%s', lang[k] )}">
                                <option value="">${ lang.select_x.replace( '%s', lang[k] ) }</option>`;
                        var countries = JSON.parse( site.countries );
                        for ( var key in countries ) {
                            var selected_c = address.hasOwnProperty( k ) && address[k] === key ? ' selected' : '';
                            if ( Object.keys(countries).length === 1 ) {
                                selected_c = ' selected';
                            }
                            if ( countries.hasOwnProperty( key ) ) {
                                __FORM__ +=`<option value="${key}"${selected_c}>${countries[key]}</option>`;
                            }
                        }
                        __FORM__ +=`</select>
                        </div>`;
                    }
                    // else if ( 'state' === k || 'city' === k ) {
                    else if ( 'state' === k ) {
                        __FORM__ += `<div class="col-sm-6 form-group">
                            <label class="sr-only" for="address-${k}">${lang[k]}</label>
                            <select name="${k}" id="address-${k}" placeholder="${lang.select_x.replace( '%s', lang[k] )}" data-selected="${address[k]}" disabled={(!!$[k].is(':selected'))}>
                                <option value="">${lang.select_x.replace( '%s', lang[k] )}</option>
                            </select>
                        </div>`;
                    }
                    else if ( 'zone' === k || 'area' === k ) {
                        __FORM__ += `<input type="hidden" name="${k}" id="address-${k}" value="${ address[k] }">`;
                    }
                    // else if ( 'area' === k ) {
                    //     __FORM__ += `<div class="col-sm-12 form-group">
                    //         <label class="sr-only" for="address-${k}">${lang[k]}</label>
                    //         <select name="${k}" id="address-${k}" placeholder="${lang.select_x.replace( '%s', lang[k] )}" data-selected="${address[k]}" disabled={(!!$[k].is(':selected'))}>
                    //             <option value="">${lang.select_x.replace( '%s', lang[k] )}</option>
                    //         </select>
                    //     </div>`;
                    // }
                    else if( 'postal_code' === k ) {
                        __FORM__ += `<div class="col-sm-12 form-group">
                            <label class="sr-only" for="address-${k}">${lang[k]}</label>
                            <input type="text" class="form-control" id="address-${k}" name="${k}" value="${address[k]}" placeholder="${placeholders[k]}" required>
                        </div>`;
                    }
                    else {
                        __FORM__ += `<div class="col-sm-12 form-group">
                            <label class="sr-only" for="address-${k}">${lang[k]}</label>
                            <input type="text" class="form-control" id="address-${k}" name="${k}" value="${address[k]}" placeholder="${placeholders[k]}" required>
                        </div>`;
                    }
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
                                if ( in_array( k, [ 'id', 'company_id', 'updated_at', 'area', 'zone', 'line2' ] ) ) {
                                    continue;
                                }
                                var field = $( `#address-${k}`);
                                if ( ! field.val() ) {
                                    field.addClass( 'has-error' );
                                    field.after( `<label for="address-${k}" class="error">${ lang.x_is_required.replace( '%s', lang[k] ) }</label>` );
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
                                var self = $(this);
                                if ( in_array( self.attr( 'name' ), [ 'zone', 'line2' ] ) ) {
                                    return;
                                }
                                if ( ! self.val() ) {
                                    self.addClass( 'has-error' )
                                } else {
                                    self.removeClass( 'has-error' )
                                }
                            } );
                        }
                        $('#address-form .form-group select').select2(select2Options);
                        setTimeout( function() {
                            $( '#address-country' ).trigger( 'change' );
                            masks.push( new IMask( $('#address-phone').get(0), { mask: '+1 (000) 000–0000' } ) );
                        }, 80 )
                        var stateLoading = new Select2Cascade($('#address-country'), $('#address-state'), stateUrl, select2Options);
                        // var cityLoading = new Select2Cascade($('#address-state'), $('#address-city'), cityUrl, select2Options);
                        // var areaLoading = new Select2Cascade($('#address-city'), $('#address-area'), areaUrl, select2Options);
                    },
                    onClose: function(){
                        $('#address-form .form-group select').select2('destroy');
                        if ( masks.length ) {
                            masks.map( function( mask ) {
                                if ( mask.hasOwnProperty( 'destroy' ) ) {
                                    mask.destroy()
                                }
                            } );
                        }
                    }
                }).then( function () {
                    var $form = $('#address-form');
                    $.ajax({
                        url: `${site.shop_url}/address/${address.id}`,
                        type: 'POST',
                        data: $form.serialize(),
                        success: function ( data ) {
                            sa_alert( data.title, data.message, data.status );
                            if ( data.hasOwnProperty( 'address' ) && data.address ) {
                                renderAddress( data.address );
                            }
                        },
                        error: AjaxErrorHandler,
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
                        ${pageNow !== 'shop/addresses' ? `<input type="radio" name="address" value="${address.id}">` : ''}
                        <div class="checkout-page-content">
                            <div class="content-header">${address.title}</div>
                                <p class="content"></p>
                            <div class="checkout-step-content-edit">
                                <a class="edit" href="#"><img src="${site.assets_url}images/edit-icon.png"></a>
                                <a class="remove" href="#"><img src="${site.assets_url}images/times.png"></a>
                            </div>
                        </div>
                    </label>
                </div>
            ` ),
                    __PHONE__ = $( `
                    <div class="phone col-sm-6 col-md-4 phone-${address.id}">
                        <label>
                            ${pageNow !== 'shop/addresses' ? `<input type="radio" name="phone" disabled value="${address.id}">` : ''}
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
                if ( address.area_name ) addressStr += address.area_name + '<br>';
                if ( address.city ) addressStr += address.city + ',';
                if ( address.city && address.state ) addressStr += ' ';
                if ( address.state ) addressStr += address.state + '';
                if ( address.state && address.postal_code ) addressStr += ' – ';
                if ( address.postal_code ) addressStr += address.postal_code + '<br>';
                if ( address.country ) addressStr += address.country;
            
                __HTML__.find('.content').html( addressStr );
                $( '.address-wrap' ).find( 'input[type="radio"]' ).removeAttr( 'checked' );
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
            // set on load.
            setActive( lsGet( 'addressPref' ) );
        
            $(document)
            .on( 'click', 'a.disabled', function( e ) {
                e.preventDefault();
                if ( $( this ).data( 'reason' ) ) {
                    alert( $( this ).data( 'reason' ) )
                }
            } )
            .on( 'click', '.address .edit, .update-address', function( e ) {
                e.preventDefault();
                editAddress( $(this).closest( '.address' ).parseData( 'address' ) );
            } )
            .on('click', '.address .remove', function (e){
                e.preventDefault();
                var address = $(this).closest( '.address' ),
                    address_id = address.parseData('address').id;
                $.ajax({
                    url: `${site.shop_url}delete_address`,
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
                    error: AjaxErrorHandler,
                });
            })
            .on( 'change', '.address input[type="radio"]', function() {
                var address = $(this).closest( '.address' ),
                    addressId = address.parseData( 'address' ).id;
                // remove classes.
                $('.address-wrap .address, .phone-wrap .phone').removeClass( 'active' );
                // set active class.
                address.addClass( 'active' );
                $('.phone-' + addressId ).addClass( 'active' );
                lsSet( 'addressPref', addressId )
            } );
        },
        loader = {
            el: '#gsLoading',
            init: function() {
                var self = this;
                if ( 'string' === typeof self.el ) {
                    self.el = $( self.el );
                }
            },
            cb: function( complete, ev ) {
                ev = ev || 'shown';
                if ( 'function' === typeof complete ) {
                    complete()
                }
                $(document).triggerHandler( 'loader.' + ev );
            },
            show: function( speed, easing, complete ) {
                var self = this;
                if ( ! self.el.hasClass( 'active') ) {
                    speed  =  ! isNaN( speed ) || '' === speed || null === speed || undefined === speed ? 400 : parseFloat( speed );
                    speed  = speed <= 0 ? false : speed;
                    easing = easing || 'swing';
                    self.el.ac( 'active' );
                    $('html').ac( 'loading' );
                    if ( ! speed ) {
                        self.el.css( 'display', 'block' );
                        self.cb( complete );
                    } else {
                        self.el.show( speed, easing, function () {
                            self.cb( complete );
                        } );
                    }
                }
            },
            hide: function( speed, easing, complete ) {
                var self = this;
                if ( self.el.hasClass( 'active') ) {
                    speed  =  ! isNaN( speed ) || '' === speed || null === speed || undefined === speed ? 400 : parseFloat( speed );
                    speed  = speed <= 0 ? false : speed;
                    easing = easing || 'swing';
                    self.el.rc( 'active' );
                    $('html').rc( 'loading' );
                    self.el.rc('preloader');
                    if ( ! speed ) {
                        self.el.css( 'display', 'none' );
                        self.cb( complete );
                    } else {
                        self.el.hide( speed, easing, function () {
                            self.cb( complete, 'hidden' );
                        } );
                    }
                }
            }
        },
        // Share buttons.
        __RRSSB__ = {
            buttons: [
                {
                    class: 'facebook',
                    label: 'Facebook',
                    icon: 'facebook'
                },{
                    class: 'twitter',
                    label: 'Twitter',
                    icon: 'twitter'
                },{
                    class: 'pinterest',
                    label: 'Pinterest',
                    icon: 'pinterest-p'
                },{
                    class: 'tumblr',
                    label: 'Tumblr',
                    icon: 'tumblr'
                },{
                    class: 'linkedin',
                    label: 'LinkedIn',
                    icon: 'linkedin'
                },{
                    class: 'whatsapp',
                    action: 'share/whatsapp/share',
                    label: 'WhatsApp',
                    icon: 'whatsapp',
                }
            ],
            template: '',
            init: function() {
                var template = '';
                for ( var button of this.buttons ) {
                    var icon = button.icon.indexOf( 'svg' ) > 0 ? button.icon : `<i class="fa fa-${button.icon}" aria-hidden="true"></i>`;
                    template += `<li class="rrssb-${button.class}">
                        <a ${button.hasOwnProperty( 'action' ) ? `data-action="${button.action}"` : 'class="popup"'}>
                            <span class="rrssb-icon" aria-hidden="true">${icon}</span>
                            <span class="rrssb-text sr-only">Share On  ${button.label}</span>
                        </a>
                    </li>`;
                }
                this.template = `<div class="row">
                    <div class="col-xs-12">
                        <p style="font-size:1.1em;font-weight:bold;margin: 15px 0 0;text-align: center;">${lang.share_message}</p>
                        <ul class="rrssb-buttons clearfix">${template}</ul>
                    </div>
                </div>`;
            },
            getTemplate: function() {
                var self = this;
                if ( ! self.template ) {
                    self.init();
                }
                return self.template;
            }
        },
        // Product Viewer.
        productViewer = {
            modal: '#productViewer',
            current: null,
            urlRestore: site_url, // Initial Site URL
            showing: false,
            color: '',
            price: '',
            __HTML__: null,
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
                // modal event.
                self.modal
                .on( 'hide.bs.modal', function( e ) {
                    self.showing = false;
                    self.onClose();
                } )
                // .on( 'hidden.bs.modal', function( e ) {
                // } )
                .on( 'show.bs.modal', function() {
                    self.showing = true;
                    self.beforeOpen();
                } )
                .on( 'shown.bs.modal', function() {
                    var product = self.current;
                    if ( product.promo ) {
                        $('.gs_countdown-single').GS_Countdown( $.extend( { opts: { stop: product.promo_ends, now: product.promo_starts } }, countdownGlobalSettings ) );
                    }
                    setTimeout( self.onOpen.bind( self ), 150 );
                    loader.hide( 100 );
                } );
                // global event
                $(document)
                .on( 'click mouseover', '.gallery-preview', function ( e ) {
                    e.preventDefault();
                    var el = $(this),
                        img  = el.closest( '.gallery-holder' ).find( '.product-preview img' );
    
                    img.attr( 'src', el.data( 'src' ) ).data( 'zoom-image', el.data( 'zoom-image' ) );
                    // elevateZoom bind click event on gallery>anchor>current-item.
                    // el.click();
                    var zp = $('#zoom-preview');
                    $.removeData( zp, 'elevateZoom' );
                    $('.zoomContainer').remove();
                    zp.elevateZoom({
                        // gallery: product.gallery.length ? 'product-gallery' : '',
                        // galleryActiveClass: 'current-item',
                        zoomType: "inner",
                        scrollZoom: true,
                        cursor: "crosshair",
                        zoomWindowFadeIn: 500,
                        zoomWindowFadeOut: 750
                    });
                } )
                .on( 'change', '.product_options [type="radio"]', function() {
                    var el = $(this),
                        optGroup = el.closest( '.product_options' ),
                        options = optGroup.find('.options' ),
                        dataMap = self.current.attributes.dataMap,
                        addToCart = el.closest( '.product' ).find( '.add-to-cart' ),
                        optionsCheckedLength = 0,
                        optionsChecked = [];
                    // check options.
                    options.each( function () {
                        var el = $( this ), opt = el.find( '[type="radio"]:checked' );
                        if ( opt.length ) {
                            optionsCheckedLength += 1;
                            el.find( '.opt-label').text( opt.val() );
                            optionsChecked.push( opt.attr('name') + ':' + opt.val() );
                        } else {
                            el.find( '.opt-label').text( '' );
                        }
                    } );
                    optionsChecked = optionsChecked.join( '|' );
                    if ( optionsCheckedLength === options.length && dataMap.hasOwnProperty( optionsChecked ) ) {
                        // necessary options are chosen.
                        var variation = dataMap[ optionsChecked ];
                        if ( variation ) {
                            self.setPrice( variation );
                            self.setStockStatus( variation.stock_status );
                            if ( out_of_stock !== variation.stock_status ) {
                                addToCart.removeAttr( 'data-alert', undefined );
                                addToCart.data( 'alert', '' );
                                addToCart.removeData( 'bs.tooltip' );
                                addToCart.rc( 'disabled' );
                                return;
                            } else {
                                addToCart.data( 'alert', lang[out_of_stock] );
                            }
                        } else {
                            addToCart.data( 'alert', lang.select_option );
                        }
                    }
                    
                    addToCart.ac( 'disabled' );
                } )
                .on( 'change', '.product_options .options:eq(0) [type="radio"]', function() {
                    // show/hide dependent attribute selector.
                    var masterOpt = $(this),
                        optGroup = masterOpt.closest( '.product_options' ),
                        optMap = self.current.attributes.optMap;
                    if ( ! masterOpt.is( ':checked' ) ) {
                        return;
                    }
                    if ( masterOpt.length && masterOpt.val() ) {
                        var n = masterOpt.attr('name'),
                            v = masterOpt.val();
                        if ( optMap.hasOwnProperty( n) && optMap[n].hasOwnProperty( v ) ) {
                            for ( var subOpt in optMap[n][v] ) {
                                // noinspection JSUnfilteredForInLoop
                                var subOptVals = optMap[n][v][subOpt];
                                // noinspection JSUnfilteredForInLoop
                                var list = optGroup.find( '.option-' + subOpt.toLowerCase() + ' li' );
                                if ( subOptVals.length ) {
                                    list.hide();
                                    list.find( '[type="radio"]' ).prop( 'checked', false )
                                    $(subOptVals.map( function( opt ) {
                                        // noinspection JSUnfilteredForInLoop
                                        return `.option-${subOpt.toLowerCase()}-${opt.toLowerCase()}`;
                                    }).join(',')).show();
                                    // only update if there's more then 1 attribute set.
                                    self.setPrice();
                                }
                                list.find( '[type="radio"]' ).trigger( 'change' );
                            }
                        }
                    }
                } )
                .on( 'mouseenter', '.cart-btn-wrap .add-to-cart', function() {
                    var el = $(this),
                        tooltip = el.data( 'alert' );
                    if ( el.data( 'bs.tooltip' ) ) {
                        el.removeData( 'bs.tooltip' );
                    }
                    
                    if ( tooltip ) {
                        el.tooltip( {
                            container: 'body',
                            html: true,
                            trigger: 'manual',
                            title: tooltip,
                        } );
                        el.tooltip( 'show' );
                    }
                } )
                .on( 'mouseleave', '.cart-btn-wrap .add-to-cart', function() {
                    $(this).tooltip( 'hide' );
                } )
                .on( 'cart.update', function( event, data ) {
                    self.showCartAlert( data );
                } );
                
                // popstate event.
                $(window).on( 'popstate', function ( e ) {
                    var loc = winLoc();
                    if ( loc.includes( 'product/' ) ) {
                        self.show( loc.replace( prod_url, '' ), true );
                    } else {
                        self.close();
                    }
                } );
                
                if ( ! winLoc().includes( 'product/' ) || self.urlRestore !== site_url ) {
                    self.urlRestore = winLoc();
                }
            },
            showCartAlert: function( data ) {
                if ( this.showing && data.message ) {
                    $('.modal-header').dismissalAlert( `<p>${data.message} <a href="#" class="cart-open-btn">${lang.view_cart}</a></p>` );
                    // todo scroll to top now working properly
                    $( '.modal-dialog' ).animate( { scrollTop: 0 }, 100 );
                }
            },
            fetch: function( data ) {
                if ( this.current && this.current.slug === data ) {
                    this.modal.modal( 'show' );
                    return;
                }
                loader.show( 100 );
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
                
                $.get( productUrl, function ( data ) {
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
                $( '.cart-sidebar-wrapper' ).rc( 'cart-sidebar-open' );
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
    
                if ( ! data.id ) {
                    return;
                }
                
                if ( self.current && self.current.id === data.id ) {
                    self.modal.modal( 'show' );
                    return;
                }
                // Reset & Initialize data.
                self.price = '';
                self.current = data;
                self.prepare();
            },
            prepare: function() {
                loader.show( 100 );
                // if ( ! __CACHES__['prodModal'][this.current.id] ) {
                //     __CACHES__['prodModal'][this.current.id]
                // }
                var self = this,
                    product = self.current,
                    __HTML__ = '';
                var custom = product.custom_data;
                __HTML__ = `
                    <div class="products container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="product single-product product-page product-${product.id}">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 gallery-holder">
                                            ${ self.getGalleryContent() }
                                            ${__RRSSB__.getTemplate()}
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 product-info-block">
                                            <div class="product-info">
                                                <div class="sku stock-info">
                                                    <span>${lang.sku_}</span>
                                                    <span>${product.code}</span>
                                                </div>
                                                <div class="stock-status stock-info">
                                                    <span>${lang.availability_}</span>
                                                    <span></span>
                                                </div>
                                                <h1 class="name" id="modalLabel">${product.name}</h1>
                                                <div id="product-price" class="product-price"></div>
                                                ${ product.promo ? `<h3 class="promo-inds-in">${lang.promo_ends_in}</h3><div class="gs_countdown-single timing-wrapper"></div>` : '' }
                                                ${ self.getOverview() }
                                                ${ self.getVariationOptions() }
                                                <div class="row cart-btn-wrap">
                                                    <div class="col-xs-7 col-sm-4">
                                                        <div class="add-to-cart-count-content">
                                                            <button class="cart-qty qty-desc cart-item-decrease-btn-from-product">-</button>
                                                            <!-- <span class="cart-qty-count cart-item-qty-input-from-product">1</span>-->
                                                            <input type="number" class="cart-qty-count cart-item-qty-input-from-product" value="1" step="1" min="1" max="${product.quantity}">
                                                            <button class="cart-qty qty-inc cart-item-increase-btn-from-product">+</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-5 col-sm-4">
                                                        <div class="buy-now">
                                                            <a class="${ product.inWishList ? 'remove' : 'add-to' }-wishlist" data-id="${ product.id }" href="${ site.site_url }cart/${ product.inWishList ? 'remove' : 'add' }_wishlist/${ product.id }"><i class="fa fa-heart${ product.inWishList ? '' : '-o' }"></i></a>
                                                            <a class="add-to-cart${ product.isVariable || out_of_stock === product.stock_status ? ' disabled' : '' }" data-alert="${ product.isVariable ? lang.select_option : ( out_of_stock === product.stock_status ? lang.out_of_stock : '' ) }" data-id="${ product.id }" href="${ product.add_to_cart }">${ lang.buy_now }</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- /.product-info -->
                                        </div>
                                    </div><!-- /.row -->
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="product-full-desc">
                                                ${ self.getSpecifications() }
                                                ${ product.product_details }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.product -->
                                ${ custom.video_url && custom.video_url.src ? `<div class="youtube-embed"><iframe width="800" height="450" src="${ custom.video_url.src }" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>` : '' }
                            </div>
                        </div>
                    </div>
                    <!-- /.products -->
                    ${ self.getAdditionalInfo() }
                    ${ self.getFAQ() }
                    `;
                self.__HTML__ = $( __HTML__ );
                self.__HTML__.find( '.product' ).data( 'product', product );
                self.modal.find( '.main-content' ).empty();
                self.setStockStatus( product.stock_status );
                self.setPrice();
                self.__HTML__.appendTo( this.modal.find( '.main-content' ) );
                self.doc( {
                    title: product.meta_title,
                    description: product.meta_description,
                    image: product.meta_image,
                    url: product.link,
                } );
                self.showing = true;
                self.modal.modal( 'show' );
            },
            /**
             *
             * @param {string|jQuery|HTMLElement} selector
             * @param {string|jQuery|HTMLElement}content
             * @return {boolean|jQuery|HTMLElement}
             */
            replaceContent: function( selector, content ) {
                var root = this.__HTML__;
                if ( ! root ) return false;
                selector = selector || false;
                content = content || false;
                return content ? (
                    ( selector ? root.find( selector ) : root ).replaceWith( content )
                ) : root;
            },
            /**
             *
             * @param {string} stock_status
             */
            setStockStatus: function( stock_status ) {
                this.replaceContent( '.stock-status span:eq(1)', `<span>${lang.hasOwnProperty( stock_status ) ? lang[stock_status] : stock_status }</span>` );
            },
            /**
             *
             * @param {string} input
             * @return {string|boolean}
             */
            mapColor: function( input ) {
                if ( color_mappings ) {
                    input = input.toLowerCase().replace( /[^a-z0-9]/g, '' );
                    if ( color_mappings.hasOwnProperty( input ) ) {
                        return color_mappings[input]
                    } else {
                        return input.replace( /\d/, '' );
                    }
                }
                return false;
            },
            /**
             *
             * @param {Object} [price=false]
             */
            setPrice: function( price ) {
                var self = this,
                    product = self.current,
                    showPrice = '';
                price = price || false;
                if ( ! self.price ) {
                    if ( product.isVariable ) {
                        var minMaxReg = product.max_min_regular,
                            minMaxSale = product.max_min_sale;
                        if ( '' !== product.sale_price ) {
                            //self.price = `<span class="price">${ minMaxSale.min }${ minMaxSale.max ? ' - ' + minMaxSale.max : '' }</span><span class="price-before-discount">${ minMaxReg.min }${ minMaxReg.max ? ' - ' + minMaxReg.max : '' }</span>${ product.saved ? `<span class="saved-amount">${ product.saved }%</span>` : '' }`;
                            self.price = `<span class="price">${ minMaxSale.min }</span><span class="price-before-discount">${ minMaxReg.min }</span>${ product.saved ? `<span class="saved-amount">${ product.saved }%</span>` : '' }`;
                        } else {
                            //self.price = `<span class="price">${ minMaxReg.min } - ${ minMaxReg.max }</span>`;
                            self.price = `<span class="price">${ minMaxReg.min }</span>`;
                        }
                    } else {
                        if ( '' !== product.sale_price ) {
                            self.price = `<span class="price">${ product.sale_price }</span><span class="price-before-discount">${ product.regular_price }</span>${ product.saved ? `<span class="saved-amount">${ product.saved }%</span>` : '' }`;
                        } else {
                            self.price = `<span class="price">${ product.regular_price }</span>`;
                        }
                    }
                }
                if ( price ) {
                    price = $.extend( { sale_price: '', regular_price: '', saved: '', }, price );
                    if ( '' !== price.sale_price ) {
                        showPrice = `<span class="price">${ price.sale_price }</span><span class="price-before-discount">${ price.regular_price }</span>${ price.saved ? `<span class="saved-amount">${ price.saved }%</span>` : '' }`;
                    } else {
                        showPrice = `<span class="price">${ price.regular_price }</span>`;
                    }
                }
                self.replaceContent( '#product-price.product-price', `<div id="product-price" class="product-price">${ price ? showPrice : self.price }</div>` );
            },
            getGalleryContent: function() {
                var product = this.current, __GALLERY__ = '';
                if ( product.gallery.length ) {
                    __GALLERY__ += '<div id="product-gallery" class="product-gallery">';
                    __GALLERY__ += `<div class="item"><a href="#" class="gallery-preview current-item" data-src="${product.image}" data-zoom-image="${product.image}"><img src="${product.thumb||product.image}"></a></div>`;
                    $.each( product.gallery, function () {
                        // noinspection JSUnresolvedVariable
                        __GALLERY__ += `<div class="item"><a href="#" class="gallery-preview" data-src="${this.thumb}" data-zoom-image="${this.photo}"><img src="${this.thumb}"></a></div>`;
                    } );
                    __GALLERY__ += '</div>';
                }
                return `<div class="product-preview">
                    <img id="zoom-preview" src="${product.image}" data-zoom-image="${product.image}" alt="${product.name}">
                </div><!-- /.product-preview -->
                ${ __GALLERY__ }
                `;
            },
            getOverview: function() {
                var custom = this.current.custom_data;
                return custom.short_description ? `<div class="overview"><h4>${lang.overview}</h4><div class="ov-details">${custom.short_description}</div></div>` : '';
            },
            getVariationOptions: function() {
                var self = this,
                    product = self.current, __OPTIONS__ = '';
                if ( product.isVariable ) {
                    __OPTIONS__ += `<div class="available-options"><h4>${lang.available_options}</h4><div class="product_options">`;
                    for( var i in product.attributes.opts ) {
                        var opts = product.attributes.opts[i],
                            lower = i.toLowerCase();
                        __OPTIONS__ += `<div id="option_${lower}" class="options option-${lower}">
                            <label class="control-label"><span class="opt-name">${ lang.hasOwnProperty( `option_${lower}` ) ? lang[`option_${lower}`] : i }</span><span class="opt-label"></span></label>
                            <ul>`;
                        for ( var opt of opts ) {
                            var optLower = opt.toLowerCase().replace( /\s/g, '-');
                            var color   = self.mapColor( opt );
                            __OPTIONS__ += `<li class="option-${lower}-${optLower}">
                                <label>
                                    <input type="radio" name="${i}" value="${opt}">
                                    <span class="swatch" style="${'color' === lower && color.code ? 'background-color:' + color.code : '' }" title="${opt}">${ 'color' === lower ? ( color.swatch ? `<img src="${color.swatch}" alt="${opt}">` : '' ) + '<span></span>' : opt }</span>
                                </label>
                            </li>`;
                        }
                        __OPTIONS__ += '</ul></div>';
                    }
                    __OPTIONS__ +=`</div></div>`;
                }
                return __OPTIONS__;
            },
            getSpecifications: function() {
                var specifications = '';
                $.each(this.current.custom_data.specification, function( idx, content ){
                    if ( content.label && content.value ) {
                        specifications += `<li><strong>${content.label}:</strong><span>${content.value}</span></li>`;
                    }
                });
                return specifications ? `<aside class="product-specification"><ul>${specifications}</ul></aside>` : '';
            },
            /**
             *
             * @return {string}
             */
            getAdditionalInfo: function() {
                var custom = this.current.custom_data;
                if ( custom.additional_info ) {
                    var addInfo = custom.additional_info;
                    if ( addInfo.additional_summery && addInfo.additional_title ) {
                        var list = '',
                            listDesc = addInfo.additional_features.feature_desc,
                            bg = site.assets_url + 'images/girl-image-black.png';
                        if ( addInfo.additional_features.feature ) {
                            $.each( addInfo.additional_features.feature, function( idx, content ) {
                                if ( content.value ) {
                                    list += `<li data-icon="">${content.value}</li>`;
                                }
                            } );
                        }
                        return `
                        <div class="gradiant-area-prod-desc">
                            <div class="container">
                                <div class="row">
                                    <div class="girl_image" style="background-image: url('${bg}');"></div>
                                    <div class="col-sm-offset-4">
                                    </div>
                                    <div class="col-xs-12 col-sm-6 content--area">
                                        <h4>${addInfo.additional_title}</h4>
                                        ${addInfo.additional_summery}
                                        <div class="additional-features">
                                            ${ listDesc ? `<p><strong>${listDesc}</strong></p>` : '' }
                                            ${ list ? `<ul>${list}</ul>` : '' }
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;
                    }
                }
                return '';
            },
            getFAQ: function() {
                var custom = this.current.custom_data;
                var faq = '';
                if ( custom.faq ) {
                    $.each( custom.faq, function( idx, content ) {
                        if ( content.question && content.answer ) {
                            faq += `<button class="custom-accordion">${content.question}</button><div class="accordion-panel"><div class="accordion-inner">${content.answer}</div></div>`;
                        }
                    } );
                }
                return faq ? `<div class="faq-area-prod-desc"><h4 class="text-center faq-header-title">${lang.faq_full}</h4>${faq}</div>` : '';
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
            beforeOpen: function() {
                var product = this.current;
                this.doc( {
                    title: product.meta_title,
                    description: product.meta_description,
                    image: product.meta_image,
                    url: product.link,
                } );
            },
            onOpen: function() {
                var product = this.current;
                $('.rrssb-buttons').rrssb( {
                    title: product.code + ' - ' + product.name,
                    url: product.link,
                    image: product.image,
                    description: product.meta_description,
                    // emailSubject: '',
                    // emailBody: '',
                } );
                if ( product.gallery.length > 3 ) {
                    $("#product-gallery").owlCarousel2({
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
                }
                $('#zoom-preview').elevateZoom({
                    // gallery: product.gallery.length ? 'product-gallery' : '',
                    // galleryActiveClass: 'current-item',
                    zoomType: "inner",
                    scrollZoom: true,
                    cursor: "crosshair",
                    zoomWindowFadeIn: 500,
                    zoomWindowFadeOut: 750
                });
                // select first option of variation.
                if ( product.isVariable ) {
                    if ( ! this.color ) {
                        var optKeys = Object.keys( product.attributes.opts );
                        if ( optKeys.length ) {
                            var firstOpt = product.attributes.opts[optKeys[0]];
                            if ( firstOpt[0] ) {
                                this.color = firstOpt[0];
                            }
                        }
                    }
                    var opts = '.product_options .options [type="radio"]';
                    $( opts + '[value="'+this.color+'"]').prop( 'checked', true );
                    $( opts ).trigger( 'change' );
                }
            },
            onClose: function() {
                this.doc( { url: this.urlRestore } );
                this.color = '';
                var gallery = $("#product-gallery");
                gallery.trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
                gallery.find('.owl-stage-outer').children().unwrap();
                // elevate zoom glitch.
                var zp = $('#zoom-preview');
                $.removeData( zp, 'elevateZoom' );
                $('.zoomContainer').remove();
            },
        },
        checkoutDetailsTable,
        updateCheckoutDetails = function ( _cart ) {
            if ( 'cart_ajax/checkout' !== pageNow || ! $('body').hasClass('user-logged-in') ) {
                return;
            }
            if ( ! checkoutDetailsTable ) {
                checkoutDetailsTable = $( '.order-details' );
            }
            checkoutDetailsTable.find('.subtotal').text( _cart.subtotal );
            checkoutDetailsTable.find('.total_item_tax').text( _cart.total_item_tax );
            checkoutDetailsTable.find('.cart_total').text( _cart.total );
            checkoutDetailsTable.find('.order_tax').text( _cart.order_tax );
            checkoutDetailsTable.find('.shipping_cost').text( _cart.shipping.cost);
            checkoutDetailsTable.find('.grand_total').text( _cart.grand_total );
            checkoutDetailsTable.find('.shipping_label').text( checkoutDetailsTable.find('.shipping_label').data('label').replace( '%s', _cart.shipping.name ) );
        },
        cartHandler = function () {
            var theCart = $('.cart-sidebar-content');
            var toggleCart = function ( e ) {
                e && e.preventDefault();
                productViewer.close();
                $( '.cart-sidebar-wrapper' ).toggleClass( 'cart-sidebar-open' );
            }
            if ( winLoc().includes( 'cart' ) && ! winLoc().includes( 'checkout' ) ) {
                toggleCart();
                document.title = restoreHome.title;
                $('head meta[name="description"]').attr( 'content', restoreHome.description );
                $( '[property="og:image"]' ).attr( 'content', restoreHome.image );
                window.history.pushState( '', restoreHome.title, restoreHome.url );
            }
            var updateCart = function ( event, cartData ) {
                cartData = cartData || cart;
                var min_order = $('.cart-min-order'),
                    checkoutBtn  = $('.cart-checkout a'),
                    total_items   = cartData.total_items,
                    countTxt      = total_items > 1 ? lang.items : lang.item,
                    freeShipping = $('.free_shipping'),
                    countSubTotal = total_items > 1 ? cartData.subtotal: formatMoney( cartData.subtotal );
                $('.cart-total-items').attr( 'aria-label', total_items > 0 ? total_items + ' ' + countTxt : 'Cart is empty' ).text(total_items);
                $('.total-cart-item').html( `${total_items} <span class="sr-only">${countTxt}</span>` );
                $('.cart-total-item-count').html( `${total_items} ${countTxt}` );
                $('.total-price-basket .value,.total-cart-amount,.checkout-amount').text( countSubTotal );
                if ( freeShipping.length ) {
                    var limit      = freeShipping.data( 'free_shipping' ),
                        remaining  = limit - cartData._total,
                        progress   = 0,
                        bar        = freeShipping.find( '.progress' ),
                        status     = freeShipping.find( '.status' ),
                        statusFree = freeShipping.find( '.free' );

                    if ( remaining < 0 ) {
                        remaining = 0;
                    }
                    if ( remaining > 0 ) {
                        progress = ( 100 - ( ( remaining / limit ) * 100 ) );
                    } else {
                        progress = 100;
                    }

                    bar.stop( true, true ).animate( { width: progress + '%' } );
                    status.find('span').text(formatMoney(remaining));
                    if ( progress < 100 ) {
                        status.removeClass( 'hide' ).addClass('show');
                        statusFree.addClass( 'hide' ).removeClass('show');
                    } else {
                        statusFree.removeClass( 'hide' ).addClass('show');
                        status.addClass( 'hide' ).removeClass('show');
                    }
                }
                if ( min_order.length ) {
                    var min_order_amount = parseInt( min_order.data( 'min_order' ) );
                    if ( min_order_amount > 0 ) {
                        if ( cartData._total >= min_order_amount ) {
                            min_order.hide();
                            checkoutBtn.removeClass( 'disabled' );
                            min_order.closest('.cart-sidebar-wrapper').addClass('hide-min-amount');
                        } else {
                            if ( 'cart_ajax/checkout' === pageNow ) {
                                window.location.reload();
                                return;
                            }
                            checkoutBtn.addClass( 'disabled' );
                            min_order.show();
                            min_order.closest('.cart-sidebar-wrapper').removeClass('hide-min-amount');
                        }
                    }
                }
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
                            <!-- <span class="cart-qty-count cart-item-qty-input">${item.qty}</span> -->
                            <input class="cart-qty-count cart-item-qty-input" type="number" value="${item.qty}" min="0" step="1">
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
                    product.find( '.cart-qty-count' ).val( data.cartQty || 1 );
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
                        prod.ac( 'not-added-in-cart').rc( 'added-in-cart');
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
                        $(document).triggerHandler( 'cart.update', data );
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
                    dataMap = prodData.isVariable ? prodData.attributes.dataMap : {},
                    optionName = [],
                    option = '',
                    stock_status = prodData.stock_status;
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
                
                    if ( dataMap.hasOwnProperty( optionName ) ) {
                        option = dataMap[ optionName ].id;
                        stock_status = dataMap[ optionName ].stock_status;
                    }
                }
                if ( out_of_stock !== stock_status ) {
                    addToCartAnim( product, id, parseInt( product.find( '.cart-qty-count' ).val() ), option );
                }
            } )
            .on( 'click', '.cart-qty', function( e ) {
                e.preventDefault();
                var self = $( this ),
                    prod = self.closest( '.product' ),
                    rowId = '',
                    prodId = '',
                    qtyEl = prod.find( '.cart-qty-count' ),
                    qty = parseInt( qtyEl.val() );
            
                // Increase.
                if ( self.hasClass( 'qty-inc' ) ) qty += 1;
                // Decrease & can set to zero.
                if ( self.hasClass( 'qty-desc' ) && qty > 0 ) qty -= 1;
            
                if ( qty <= 0 ) {
                    return;
                }
            
                // update dom.
                qtyEl.val( qty );
                // handle cart item
                if ( ! prod.hasClass( 'single-cart-item' ) ) {
                    return;
                }
                var item = prod.data( 'item' );
                var option = false;
                if ( item.option ) {
                    option = parseInt( item.option );
                }
                rowId = prod.data( 'row_id' );
                prodId = prod.data( 'product_id' );

                if ( ! rowId || ! prodId ) {
                    return;
                }
                updateCartQty( qty, prodId, rowId );
                updateCartItem( { prodId: prodId, rowid: rowId, qty: qty, option: option } );
            
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
                prod.find( '.cart-qty-count').val( 1 );
            } )
            .on( 'cart.update', __debounce( updateCart, 500 ) )
            .on( 'click', '.cart-open-btn, .cart-close-btn', toggleCart );
        },
        get_filters = function () {
            filters.category = $('#product-category').val() ? $('#product-category').val() : filters.category;
            var range = $('#price-range').val().split( ',' );
            if ( range.length ) {
                filters.min_price = range[0];
                filters.max_price = range[1];
            }
            filters.in_stock = $('#in-stock').is(':checked') ? 1 : 0;
            filters.promo = $('#promotions').is(':checked') ? 'yes' : 0;
            filters.featured = $('#featured').is(':checked') ? 'yes' : 0;
            filters.sorting = lsGet('sorting');
            return filters;
        },
        gen_html = function (products) {
            var html = '',
                results = $('#results');
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
                // var colorsOpts = '';
                // if ( product.isVariable && product.attributes.opts.hasOwnProperty( 'Color' ) ) {
                //     var i = 0;
                //     for ( var opt of product.attributes.opts.Color ) {
                //         var optLower = opt.toLowerCase().replace( /\s/g, '-');
                //         colorsOpts += `<a class="view-product" href="${product.link}" data-color="${opt}" style="--delay: ${i};"><span style="background: ${optLower};" title="${opt}"></span></a>`;
                //         i++;
                //     }
                //     if ( colorsOpts.length ) {
                //         colorsOpts = `<div class="product-color-btn">${colorsOpts}</div>`;
                //     }
                // }
                var prodHtml = `
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-5c">
                <div class="product product-${product.id}">
                    <div class="product-info-wrap product-single">
                        <div class="product-image">
                            <div class="image">
                                <a class="view-product" href="${product.link}">
                                    <img src="${product.image}" alt="${product.name}">
                                </a>
                                ${product.stock_status === out_of_stock ? `<div class="sold-out">${lang.sold_out}</div>` : ``}
                                ${product.onSale ? `<div class="on-sale">Sale ${product.saved}%</div>` : ``}
                            </div>
                            <!-- /.image -->
                        </div>
                        <!-- /.product-image -->
                        <div class="product-info text-center">
                            <h3 class="name">
                                <a class="view-product" href="${product.link}">${product.name}</a>
                            </h3>
                            <div class="product-price">
                                ${'' !== product.sale_price ? `<span class="price">${product.isVariable ? product.max_min_sale.min :  product.sale_price}</span><span class="price-before-discount">${product.isVariable ? product.max_min_regular.min :  product.regular_price}</span>` : `<span class="price">${product.isVariable ? product.max_min_regular.min :  product.regular_price}</span>`}
                            </div>
                        </div>
                        <div class="button-group">
                            <a class="${ product.inWishList ? 'remove' : 'add-to' }-wishlist" data-id="${ product.id }" href="${ site.site_url }cart/${ product.inWishList ? 'remove' : 'add' }_wishlist/${ product.id }"><i class="fa fa-heart${ product.inWishList ? '' : '-o' }"></i></a>
                            <a class="buynow-btn ${product.stock_status === out_of_stock || product.isVariable ? `view-product` : `add-to-cart add-to-cart-btn`}" href="${ product.stock_status === out_of_stock || product.isVariable ? product.link : product.add_to_cart}" data-id="${product.id}"><span>Buy Now</span></a>
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
        };
    // ~_~
    loader.init();
    window.history.pushState( '', null, window.location.href );
    //StickyHeader.init();
    var sorting = lsGet( 'sorting' );
    if ( sorting ) {
        $('#sorting').val( sorting );
    } else {
        lsSet('sorting', 'name-asc');
    }
    $( '.chosen-select' ).chosen( { width: '100%', } );
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
                    <div class="thumb"><img src="${prod.thumb}" alt="${prod.name}"></div>
                    <div class="prod-content">
                        <div class="title">${prod.category_name ? prod.category_name + ' > ' : ''}${prod.subcategory_name ? prod.subcategory_name + ' > ' : ''}${name}</div>
                        <div class="price">${'' !== prod.sale_price ? `${prod.isVariable ? prod.max_min_sale.min :  prod.sale_price}` : `${prod.isVariable ? prod.max_min_regular.min :  prod.regular_price}`}</div>
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
        productViewer.show( viewProduct, false );
    } else {
        loader.hide( 100 );
    }
    var exitIntent = false;
    $(window).on('beforeunload', function(){
        exitIntent = true;
        loader.show( 0, 'linear', function(){
            loader.el.addClass( 'preloader' );
        } );
    });
    $(document).on( 'keydown', function( e ) {
        if ( exitIntent && 'Escape' === e.key ) {
            exitIntent = false;
            loader.hide(0);
        }
    } );
    if ( sys_alerts.length ) {
        for( var a of sys_alerts ) {
            sa_alert( a.t, a.m, a.l, a.o );
        }
    }
    $('.gs_countdown').GS_Countdown( countdownGlobalSettings );
    cartHandler( cart );
    addressEditor();
    /* LAZY LOAD IMAGES USING ECHO */
    echo.init({
        offset: 1000,
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
    $("[data-toggle='tooltip']").tooltip();
    $('.tip').tooltip({ container: 'body' }); // Tooltip

    // sys.
    $(document)
    .on( 'click', '.view-product', function ( e ) {
        e.preventDefault();
        var product = $(this).closest( '.product' ).data( 'product' );
        if ( ! product ) {
            sa_alert( 'Error!', 'Invalid Request', 'error', false );
        } else {
            productViewer.show( product, true, $(this).data('color') );
        }
    } )
    .on( 'click', '.contact-us, .email-modal', function ( e ) {
        e.preventDefault();
        email_form();
    } )
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
    .on( 'click', '.icon-search', function ( e ) {
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
    .on( 'click', '.back-to-top', function () {
        $( 'body,html' ).animate( { scrollTop: 0 }, 500 )
    } )
    .on( 'click', '.forgot-password', function ( e ) {
        e.preventDefault();
        prompt( lang.reset_pw, lang.type_email )
    } )
    .on( 'click', '.reload-captcha', function ( e ) {
        e.preventDefault();
        let link = $(this).attr('href');
        $.ajax({ url: link + '?width=210&height=34', type: 'GET' }).done(function (data) {
            if (data) {
                $('.captcha-image').html(data);
            } else {
                sa_alert('Error!', 'Something went wrong.', 'error', true);
            }
        });
    } )
    .on( 'click', '.custom-accordion', function ( e ) {
        e.preventDefault();
        $(this).toggleClass('active').next().slideToggle();
    } )
    .on( 'click', '.modal-backdrop', function ( e ) {
        e.preventDefault();
        productViewer.close();
        $(this).remove();
    } )
    .on( 'click', '.mobile-filter-trigger ul li a', function( e ) {
        e.preventDefault();
        var $this = $(this),
            triggerID = $this.data('id');
        $('.mobile-filter-trigger ul li a').not($this).removeClass('active')
        $this.toggleClass('active');
        $('.single-filter-body').not(triggerID).stop().slideUp();
        $(triggerID).stop().slideToggle();
    } )
    .on('click', 'ul.mobile-sub-category-list li span', function(e){
        e.preventDefault();
        $(this).toggleClass('active').next('ul.subs').slideToggle();
    })
    .on( 'submit', '.subscribe-form', subscription_form )
    .on( 'change', '[name=payment_method]', function () {
        var el           = $(this),
            gateWay      = el.val(),
            extras       = $( '.gateway-extra' ),
            gateWayExtra = $( '#' + gateWay + '_extra' );
        if ( ! gateWayExtra.is( ':visible' )) {
            extras.hide();
            gateWayExtra.show();
        }
    } );
    
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
    var validatorOptions = {
        framework: 'bootstrap',
        // icon: {
        //     valid: 'fa fa-check',
        //     invalid: 'fa fa-remove',
        //     validating: 'fa fa-refresh'
        // },
        message: lang.required_invalid,
    };
    
    $('.validate').formValidation( validatorOptions );
    
    if ( $('[name="payment_method"]').length === 1 ) {
        $('[name="payment_method"]').prop( 'checked', true ).trigger('change');
    }
    if ( $('[name="payment_method"][value="authorize"]').length ) {
        $(document).on( 'change', '[name="payment_method"]', function() {
            $( '#authorize_extra input' ).prop( 'required', ( 'authorize' === $(this).val() ) );
        } );
    }
    
    // @XXX shipping address!
    var shippingFields = $('#shipping_line1,#shipping_country,#shipping_state,#shipping_city,#shipping_phone');
    $(document).on( 'change', '#same_as_billing', function (e) {
        $('.guest-shipping-address').slideToggle();
        if( ! $(this).is( ':checked' ) ) {
            shippingFields.prop('required', true );
        } else {
            shippingFields.prop('required', false );
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
    
    // files...
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
    /**
     * Smart Menu Options
     */
    $('#main-menu').smartmenus({
        mainMenuSubOffsetX: -1,
        mainMenuSubOffsetY: 4,
        subMenusSubOffsetX: 6,
        subMenusSubOffsetY: -6
    });
    // SmartMenus mobile menu toggle button
    var $mainMenuState = $('#main-menu-state');
    if ( $mainMenuState.length ) {
        // animate mobile menu
        $mainMenuState.change(function(e) {
            var $menu = $('#main-menu');
            if (this.checked) {
                $menu.hide().slideDown(250, function() { $menu.css('display', ''); });
            } else {
                $menu.show().slideUp(250, function() { $menu.css('display', ''); });
            }
        });
        // hide mobile menu beforeunload
        $(window).bind('beforeunload unload', function() {
            if ($mainMenuState[0].checked) {
                $mainMenuState[0].click();
            }
        });
    }
    // var mcPop = $(".subscribe-me");
    // if ( mcPop.length ) {
    //     mcPop.subscribeBetter({
    //         autoClose: false,
    //         delay: 1000,
    //         showOnce: true,
    //     });
    // }
    
    /**
     * Date Picker for delivery slot
     */
    var minDate  = new Date(),
        maxDate  = new Date();
    maxDate.setDate( minDate.getDate() + 7 );
    minDate.setDate( minDate.getDate() - 1 );

    $("#delivery-slot-date").datepicker({
        format: 'yyyy-mm-dd',
        autoHide: true,
        filter: function(date) {
            date = date.getTime();
            return ( ! ( date < minDate.getTime() || date > maxDate.getTime() ) );
        }
    });

    $(document)
    .on('click', 'a.join-btn', function (e) {
        e.preventDefault();

        if( $(this).data('modal') === 'sign-up' ) {
            $('#loginModal .sign-up-form').show().siblings().hide();
        }

        $('#loginModal').modal('show');
    })
    .on('click', '[data-modal="sign-up"]', function (e) {
        e.preventDefault();
        $('#loginModal .sign-up-form').show().siblings().hide();
    })
    .on('click', '[data-modal="forgot-password"]', function (e) {
        e.preventDefault();
        $('#loginModal .forgot-password-form').show().siblings().hide();
    })
    .on('click', '[data-modal="sign-in"]', function (e) {
        e.preventDefault();
        $('#loginModal .sign-in-form').show().siblings().hide();
    });

    $('#loginModal').on('hidden.bs.modal', function (e) {
        $('#loginModal .sign-in-form').show().siblings().hide();
    });

    var firstLoad = true,
        isFetchingSlots = false,
        slotsWrap = $('.delivery-slot-container'),
        slotsTmpl = $( '#delivery-slots-tmpl' ).text().trim(),
        slotTmpl = $( '#delivery-slot-tmpl' ).text().trim(),
        slotWrap = $( '.delivery-slot-wrap' ),
        hasChecked = false;
    
    function getSlots( area, date ) {
        if ( isFetchingSlots ) {
            isFetchingSlots.abort();
            isFetchingSlots = false;
        }
        firstLoad = false;
        if ( area && date ) {
            slotWrap.slideDown();
            if ( ! firstLoad ) {
                loader.show();
            }
        
            $('#delivery_area').val( area );
            isFetchingSlots = $.ajax( {
                url: site.site_url + 'cart/getAvailableShippingSlots',
                type: 'POST',
                data: $.extend( {}, __CSRF__, { area, date } ),
                success: function ( res ) {
                    isFetchingSlots = false;
                    loader.hide();
                    if ( res.success ) {
                        var date;
                        slotsWrap.empty();
                        $.each( res.data, function() {
                            var slots = $( slotsTmpl );
                            date = this.date;
                            slots.find( '.date-label' ).text( lang.slots_for_x.replace( '%s', this.label ) );
                            slots.appendTo( slotsWrap );
                            $.each( this.slots, function() {
                                var slot = $( slotTmpl ),
                                    input = slot.find( '[name="delivery_slot"]' ),
                                    _date = slot.find( '[name="delivery_date"]' );
                                input.val( this.id );
                                _date.val( date );
                                // noinspection EqualityComparisonWithCoercionJS
                                if ( this.isAvailable == 0 ) {
                                    input.prop( 'disabled', true );
                                    _date.prop( 'disabled', true );
                                } else {
                                    if ( false === hasChecked ) {
                                        hasChecked = input;
                                        input.prop( 'checked', true );
                                    }
                                }
                                slot.find( '.content-header' ).html( '<i class="fa fa-calendar"></i> ' + date );
                                slot.find( '.content' ).html( '<i class="fa fa-clock-o"></i> ' +  this.start_at + ' - ' + this.end_at );
                                slot.appendTo( slots.find( '.slots' ) );
                            } );
                        } );
                    
                        if ( false !== hasChecked ) {
                            setTimeout( function() {
                                hasChecked.trigger( 'change' );
                                hasChecked = false;
                            }, 200 );
                        }
                    } else {
                        sa_alert( 'Error!', res.message, 'error', true );
                    }
                },
                error: AjaxErrorHandler,
            } );
        } else {
            if ( ! area ) {
                slotsWrap.html('<div class="col-sm-12"><div class="alert alert-warning">Update Your Address</div></div>');
                slotWrap.slideUp();
            }
        }
    }
    
    $(document).on( 'change', '[name="address"]', __debounce( function () {
        var area = $('[name="address"]:checked').closest( '.address' ).parseData( 'address' ).area;
        getSlots( area, $('#delivery-slot-date').val() );
    }, 500 ) );
    $(document).on( 'change', '#delivery-slot-date, #billing_area, #shipping_area', __debounce( function () {
        var area;
        if ( $('[name="address"]:checked').length ) {
            area = $('[name="address"]:checked').closest( '.address' ).parseData( 'address' ).area;
        } else {
            area = $('#billing_area' ).val();
            if ( ! $('#same_as_billing').is(':checked') && $('#shipping_area').val() ) {
                area = $('#shipping_area').val();
            }
        }
        
        if ( area ) {
            getSlots( area, $('#delivery-slot-date').val() );
        }
    }, 500 ) );
    $( document ).on('change', '.delivery-slot input', function (){
        $('[name="delivery_date"]').prop('checked', false);
        // Selected label
        let parent = $('.delivery-slot input:checked').parent();
        // selected label content
        let date = parent.find('.content-header').text(),
            time = parent.find('.content').text();
        // display content
        $('.selected-slot').find( 'span' ).html( '<i class="fa fa-calendar"></i> ' + date +' / <i class="fa fa-clock-o"></i> ' + time);
        parent.find('[name="delivery_date"]').prop('checked', true);
    });
    
    /**
     * A Javascript module to loadeding/refreshing options of a select2 list box using ajax based on selection of another select2 list box.
     *
     * @url : https://gist.github.com/ajaxray/187e7c9a00666a7ffff52a8a69b8bf31
     * @auther : Anis Uddin Ahmad <anis.programmer@gmail.com>
     *
     * Live demo - https://codepen.io/ajaxray/full/oBPbQe/
     * w: http://ajaxray.com | t: @ajaxray
     */
    var Select2Cascade = ( function(window, $) {

        function Select2Cascade ( parent, child, url, select2Options ) {
            var afterActions = [];
            var options = select2Options || {};

            // Register functions to be called after cascading data loading done
            this.then = function(callback) {
                afterActions.push(callback);
                return this;
            };
            var prep_url = function ( url ) {
                return 'string' === typeof( url ) ? (
                    url.replace( ':cc:', $( '#address-country' ).val() ).
                        replace( ':sc:', $( '#address-state' ).val() ).
                        replace( ':city:', $( '#address-city' ).val() ).
                        replace( ':zip:', $( '#address-postal_code').val() )
                ) : ( 'function' === typeof url ? url() : false );
            };
            parent.select2(select2Options).on("change", function (e) {
                var _this = this,
                    placeholder = '<option value=""> '+ ( child.attr( 'placeholder' ) || '-- Select --' ) +' </option>',
                    __URL__ = prep_url( url );
                child.prop("disabled", true);
                child.html( placeholder );
                if ( ! __URL__ ) {
                    return;
                }
                $.getJSON( __URL__, function(items) {
                    if ( items.hasOwnProperty( 'zone' ) ) {
                        $( '#address-zone, #billing_zone, #shipping_zone' ).val( items.zone );
                        if ( in_array( 'getShippingStates', __URL__.split('/') ) ) {
                            items = items.states;
                        }
                        if ( in_array( 'getShippingCities', __URL__.split('/') ) ) {
                            items = items.cities;
                        }
                        if ( in_array( 'getShippingAreas', __URL__.split('/') ) ) {
                            items = items.area;
                        }
                    }
                    var newOptions = placeholder;
                    for ( var id in items ) {
                        var value = items[id] || '';
                        var optVal, optTxt;
                        if ( 'string' === typeof( value ) ) {
                            optVal = optTxt = value
                        } else {
                            optVal = value.id
                            optTxt = value.name
                        }
                        newOptions += '<option value="'+ optVal +'">'+ optTxt +'</option>';
                    }
                    child.select2( 'destroy' ).html( newOptions );
                    if ( child.data( 'selected' ) ) {
                        child.find( 'option[value="' + child.data( 'selected' ) + '"]' ).prop( 'selected', true );
                        child.trigger( 'change' );
                    }
                    child.prop( 'disabled', false ).select2( options );

                    afterActions.forEach(function (callback) {
                        callback(parent, child, items);
                    });
                });
            });
        }

        return Select2Cascade;

    } )( window, jQuery );
    var select2Options = { width: '100%' };
    var stateUrl       =  site.base_url + 'cart/getShippingStates/?all=true&get_zone=true&cc=:cc:';
    var cityUrl        =  site.base_url + 'cart/getShippingCities/?cc=:cc:&sc=:sc:';
    var areaUrl        =  site.base_url + 'cart/getShippingAreas/?cc=:cc:&sc=:sc:&city=:city:&zip=:zip:';

    /**
     * Shipping zone
     */
    var firstLoadShippingMethod = true,
        isFetchingShippingMethod = false,
        shippingMethodWrap = $('.shipping-method-wrap .shipping-method'),
        shippingMethodTmpl = $( '#shipping_method-tmpl' ).text().trim();
    
    function getShippingMethods( zone, area, slot ) {
        if ( isFetchingShippingMethod ) {
            isFetchingShippingMethod.abort();
            isFetchingShippingMethod = false;
        }
        if ( zone || area || slot ) {
            if ( ! firstLoadShippingMethod) {
                loader.show();
            }
            firstLoadShippingMethod = false;
            isFetchingShippingMethod = $.ajax({
                url: site.site_url + 'cart/getAvailableShippingMethods',
                type: 'POST',
                data: $.extend({}, __CSRF__, {zone:zone, area:area, slot:slot}),
                success: function (res) {
                    isFetchingShippingMethod = false;
                    loader.hide();
                    shippingMethodWrap.empty();
                    if (res.success) {
                        $.each( res.data, function () {
                            var method = $( shippingMethodTmpl ),
                                input = method.find( 'input' );
                            method.find( '.content' ).html( this.name + ' ' + this.cost )
                            method.data( 'desc', this.desc );
                            input.val( this.id );
                            if ( this.checked ) {
                                input.prop( 'checked', true );
                                setTimeout( function() {
                                    input.trigger( 'change' );
                                }, 80 );
                            }
                            method.appendTo( shippingMethodWrap );
                        
                        } );
                    } else {
                        sa_alert('Error!', res.message, 'error', true);
                    }
                },
                error: AjaxErrorHandler,
            });
        }
    }
    
    $(document).on( 'change', '[name="address"],#billing_country, #billing_country, [name="delivery_slot"]', __debounce( function () {
        var zone, area;
        if ( $('[name="address"]:checked').length ) {
            var address = $('[name="address"]:checked').closest( '.address' ).parseData( 'address' );
            zone = address.zone;
            area = address.area;
        } else {
            area = $('#billing_area' ).val();
            zone = $('#billing_zone' ).val();
            if ( ! $('#same_as_billing').is(':checked') && $('#shipping_area').val() ) {
                area = $('#shipping_area').val();
                zone = $('#shipping_zone').val();
            }
        }
        
        getShippingMethods( zone, area, $('[name="delivery_slot"]:checked').val() );
    }, 500) );

    var firstChangeShippingMethod = true, isSettingShippingMethod = false;
    
    $( document ).on( 'change', '[name="shipping_method"]', __debounce( function() {
        if ( isSettingShippingMethod ) {
            isSettingShippingMethod.abort();
            isSettingShippingMethod = false;
        }
        if ( ! firstChangeShippingMethod ) {
            loader.show();
        }
        firstChangeShippingMethod = false;
        var zone, area,
            shipping_method = $(this).val(),
            slot = $('[name="delivery_slot"]:checked').val();
        if ( $('[name="address"]:checked').length ) {
            var address = $('[name="address"]:checked').closest( '.address' ).parseData( 'address' );
            zone = address.zone;
            area = address.area;
        } else {
            area = $('#billing_area' ).val();
            zone = $('#billing_zone' ).val();
            if ( ! $('#same_as_billing').is(':checked') && $('#shipping_area').val() ) {
                area = $('#shipping_area').val();
                zone = $('#shipping_zone').val();
            }
        }
        isSettingShippingMethod = $.ajax({
            url: site.site_url + 'cart/shipping',
            type: 'POST',
            data: $.extend({}, __CSRF__, {zone:zone, area:area, slot, shipping_method}),
            success: function (res) {
                loader.hide();
                if (res.success) {
                    updateCheckoutDetails( res.data );
                } else {
                    sa_alert('Error!', res.message, 'error', true);
                }
            },
            error: AjaxErrorHandler,
        });

    }, 500 ) );
    
    if ( $('.guest-checkout').length ) {
        
        $( '.guest-checkout .form-group select' ).select2( select2Options );
        
        new Select2Cascade($('#billing_country'), $('#billing_state'), function() {
            return stateUrl.replace( ':cc:', $( '#billing_country' ).val() );
        }, select2Options);
        
        // new Select2Cascade($('#billing_state'), $('#billing_city'), function() {
        //     return cityUrl.replace( ':cc:', $( '#billing_country' ).val() ).
        //         replace( ':sc:', $( '#billing_state' ).val() );
        // }, select2Options);
        // var billingArea = new Select2Cascade($('#billing_city'), $('#billing_area'), function() {
        //     return areaUrl.replace( ':cc:', $( '#billing_country' ).val() ).
        //         replace( ':sc:', $( '#billing_state' ).val() ).
        //         replace( ':city:', $( '#billing_city' ).val() ).
        //         replace( ':zip:', $( '#billing_postal_code').val() );
        // }, select2Options);
    
        new Select2Cascade($('#shipping_country'), $('#shipping_state'), function() {
            return stateUrl.replace( ':cc:', $( '#shipping_country' ).val() );
        }, select2Options);
        
        // new Select2Cascade($('#shipping_state'), $('#shipping_city'), function() {
        //     return cityUrl.replace( ':cc:', $( '#shipping_country' ).val() ).
        //         replace( ':sc:', $( '#shipping_state' ).val() );
        // }, select2Options);
        // var shippingArea = new Select2Cascade($('#shipping_city'), $('#shipping_area'), function() {
        //     return areaUrl.replace( ':cc:', $( '#shipping_country' ).val() ).
        //         replace( ':sc:', $( '#shipping_state' ).val() ).
        //         replace( ':city:', $( '#shipping_city' ).val() ).
        //         replace( ':zip:', $( '#shipping_postal_code').val() );
        // }, select2Options);
        //
        
        // function handleShippingArea ( parent, child, items ) {
        //     child.prop( 'disabled', ! items.length );
        //     if ( ! items.length ) {
        //         slotWrap.slideUp();
        //     } else {
        //         slotWrap.slideDown();
        //     }
        // }
    
        // billingArea.then( handleShippingArea );
        // shippingArea.then( handleShippingArea );
    }

})( jQuery, window, document, site, cart, lang, restoreHome, sys_alerts, filters, viewProduct, pageNow );
