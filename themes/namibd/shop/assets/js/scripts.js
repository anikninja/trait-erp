/** noinspection ES6ConvertVarToLetConst */
( function ( $, pageNow ) {
    "use strict";
    var countCountry;
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
        validatePhone = function( phone ) {
            return /^(018|017|016|013|015|019)\d{8}/g.test( phone );
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
                preConfirm: function (identity) {
                    form_data['identity'] = identity;
                    return new Promise(function (resolve, reject) {
                        $.ajax({
                            url: site.base_url + 'forgot_password/id1',
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
                    country: shipping.defaults.country || '',
                    state: shipping.defaults.state || '',
                    city: shipping.defaults.city || '',
                    postal_code: shipping.defaults.zip || '',
                    area: '',
                    zone: shipping.defaults.zone,
                    phone: '',
                }, address );
                var __FORM__ = '',
                    keys = Object.keys( address ),
                    ph   = {
                        title: 'Title (Home/Office)',
                        address: 'Address',
                        line1: 'Address Line 1',
                        line2: 'Address Line 2',
                        country: 'Country',
                        state: 'District',
                        city: 'City',
                        postal_code: 'Postal Code',
                        area: 'Area',
                        phone: '01xxxxxxxxx',
                    };
                var countries = shipping.data.countries;
                var cc = Object.keys( countries );
                var states = shipping.data.states;
                var sc = Object.keys( states );
                var cities = shipping.data.cities;
                var areas = shipping.data.areas;
                
                if ( address.city && ! cities.length ) {
                    cities.push( address.city );
                }
                
                if ( areas.length >= 1 && ! address.area ) {
                    address.area = areas[0].id;
                }
    
                var exclude_fields = [ 'id', 'company_id', 'updated_at', 'area_name', 'line2' ];
                
                // The form.
                __FORM__ += `
    <span style="font-weight: 300;letter-spacing: 1px;">${lang.fill_form}</span>
    <hr class="swal2-spacer" style="display: block;">
    <form action="${site.shop_url}address/${address.id}" id="address-form" class="padding-bottom-md">
        <input type="hidden" name="${site.csrf_token}" value="${site.csrf_token_value}">
        <div class="col-sm-12 form-group">
            <label class="form-label" for="address-title">${lang.title}</label>
            <input type="text" class="form-control" id="address-title" name="title" value="${address.title}" placeholder="${ph.title}" required>
        </div>
        <div class="col-sm-12 form-group">
            <label class="form-label" for="address-line1">${lang.address}</label>
            <!-- input type="text" class="form-control" id="address-line1" name="line1" value="${address.line1}" placeholder="${ph.line1}" required -->
            <textarea class="form-control" id="address-line1" name="line1" placeholder="${ph.address}">${address.line1}</textarea>
        </div>
        <!-- div class="col-sm-12 form-group">
            <label class="form-label" for="address-line2">${lang.line2}</label>
            <input type="text" class="form-control" id="address-line2" name="line2" value="${address.line2}" placeholder="${ph.line2}" required>
        </div -->
    `;
                
                __FORM__ += `<input type="hidden" name="zone" id="address-zone" value="${address.zone}">`;
                
                if ( shipping.count.zone > 0 ) {
                    
                    __FORM__ += `<div class="col-sm-6 form-group${cc.length <= 1 ? ' hidden' : ''}">
                        <label class="form-label" for="address-country">${ lang.country }</label>
                        <select class="form-control" name="country" id="address-country" data-selected="${address.country}"${cc.length <= 1 ? ' disabled' : ''}>
                            <option value="">${lang.select_x.replace( '%s', lang.country ) }</option>
                            ${ cc.map( x => `<option value="${x}"${ address.country == x ? ' selected' : '' }>${countries[x]}</option>` ).join( '' ) }
                        </select>`;
                    __FORM__ += `</div>`;
                    
                    __FORM__ += `<div class="col-sm-6 form-group${sc.length <= 1 ? ' hidden' : ''}">
                    <label class="form-label" for="address-state">${ lang.state }</label>
                    <select class="form-control" name="state" id="address-state" data-selected="${address.state}"${sc.length <= 1 ? ' disabled' : ''}>
                        <option value="">${lang.select_x.replace( '%s', lang.state ) }</option>
                        ${ sc.map( x => `<option value="${x}"${ address.state == x ? ' selected' : '' }>${states[x]}</option>` ).join( '' ) }
                    </select>`;
                    __FORM__ += `</div>`;
                    
                    __FORM__ += `<div class="col-sm-6 form-group">
                        <label class="form-label" for="address-city">${ lang.city }</label>
                        <select class="form-control" name="city" id="address-city" data-selected="${address.city}"${cities.length <= 1 ? ' disabled' : ''}>
                            <option value="">${lang.select_x.replace( '%s', lang.city ) }</option>
                            ${ cities.map( x => `<option value="${x}"${ address.city == x ? ' selected' : '' }${cities.length <= 1 ? ' disabled' : ''}>${x}</option>` ).join( '' ) }
                        </select>`;
                    __FORM__ += `</div>`;
                    
                    if ( areas.length < 1 ) {
                        __FORM__ += `<div class="col-sm-6 form-group">
                            <label class="form-label" for="address-postal_code">${lang.postal_code}</label>
                            <input class="form-control" type="text" class="form-control" id="address-postal_code" name="postal_code" value="${address.postal_code}" placeholder="${ph.postal_code}" required>
                        </div>`;
                    }
                    
                    var c = 0;
                    c += cc.length <= 1 ? 1 : 0;
                    c += sc.length <= 1 ? 1 : 0;
                    c = c == 1 ? 'col-sm-12' : 'col-sm-6';
                    __FORM__ += `<div class="${c} form-group${areas.length <= 1 ? ' hidden' : ''}">
                        <label class="form-label" for="address-area">${ lang.area }</label>
                        <select class="form-control" name="area" id="address-area" data-selected="${address.area}">
                            <option value="">${lang.select_x.replace( '%s', lang.area ) }</option>
                            ${ areas.map( x => `<option value="${x.id}"${x.id == address.area ? ' selected' : ''}>${x.name}</option>` ).join( '' ) }
                        </select>`;
                    __FORM__ += `</div>`;
                    
                } else {
                    __FORM__ += `<input type="hidden" name="country" id="address-country" value="${address.country}">`;
                    __FORM__ += `<input type="hidden" name="state" id="address-state" value="${address.state}">`;
                    __FORM__ += `<input type="hidden" name="city" id="address-city" value="${address.city}">`;
                    __FORM__ += `<input type="hidden" name="area" id="address-area" value="${address.area}">`;
                    exclude_fields.push( 'country', 'state', 'city', 'area', 'zone' );
                }
    
                if ( cc.length <= 1 ) {
                    exclude_fields.push( 'country' );
                    __FORM__ += `<input type="hidden" name="country" value="${address.country}">`;
                }
                if ( sc.length <= 1 ) {
                    exclude_fields.push( 'state' );
                    __FORM__ += `<input type="hidden" name="state" value="${address.state}">`;
                }
                if ( cities.length <= 1 ) {
                    exclude_fields.push( 'city' );
                    __FORM__ += `<input type="hidden" name="city" value="${address.city}">`;
                }
                if ( areas.length <= 1 ) {
                    exclude_fields.push( 'area' );
                    __FORM__ += `<input type="hidden" name="area" value="${address.area}">`;
                }
                if ( areas.length ) {
                    exclude_fields.push( 'postal_code' );
                    __FORM__ += `<input type="hidden" name="postal_code" value="${address.postal_code}">`;
                }
    
                __FORM__ += `<div class="col-sm-12 form-group">
                    <label class="form-label" for="address-phone">${lang.phone}</label>
                    <div class="input-group">
                        <span class="input-group-addon">+88</span>
                        <input class="form-control" type="tel" id="address-phone" name="phone" value="${address.phone.replace( /^\+/g, '' ).replace( /^\88/g, '' )}" placeholder="${ph.phone}" required>
                    </div>
                </div>`;
                
                __FORM__ += `</form>`;
                __FORM__ = `<div class="edit-address clearfix pull-left">${__FORM__}</div>`;
                
                swal({
                    title: address.id ? lang.update_address : lang.add_address,
                    html: __FORM__,
                    /*customClass: '',*/
                    showCancelButton: true,
                    allowOutsideClick: false,
                    cancelButtonText: lang.cancel,
                    confirmButtonText: lang.submit,
                    preConfirm: function () {
                        return new Promise( function ( resolve, reject ) {
                            var errors = false;
                            for( let k of keys ) {
                                if ( in_array( k, exclude_fields ) ) {
                                    continue;
                                }
                                var field = $( `#address-${k}`),
                                    group = field.closest('.form-group'),
                                    val = field.val(),
                                    _error = false;
                                
                                if ( 'phone' == k ) {
                                    var val = val.replace( /^\+/g, '' ).replace( /^\88/g, '' );
                                    field.val( val );
                                }
                                
                                switch ( k ) {
                                    case 'phone':
                                        if ( ! val ) {
                                            field.closest('.input-group').addClass( 'has-error' );
                                            group.append( `<label for="address-${k}" class="error">${ lang.x_is_required.replace( '%s', line.phone_number ) }</label>` );
                                            _error = errors = true;
                                        } else if ( ! validatePhone( val ) ) {
                                            field.closest('.input-group').addClass( 'has-error' );
                                            group.append( `<label for="address-${k}" class="error">${ lang.x_is_invalid.replace( '%s', line.phone_number ) }</label>` );
                                            _error = errors = true;
                                        }
                                        break;
                                    default:
                                        if ( ! val ) {
                                            field.addClass( 'has-error' );
                                            group.append( `<label for="address-${k}" class="error">${ lang.x_is_required.replace( '%s', 'line1' == k ? lang.address : lang[k] ) }</label>` );
                                            _error = errors = true;
                                        }
                                        break;
                                }
                                
                                if ( ! _error ) {
                                    field.closest('.input-group').removeClass( 'has-error' );
                                    field.removeClass( 'has-error' );
                                    group.find('label.error').remove();
                                }
                            }
                            if ( errors ) {
                                reject();
                            }
                            resolve();
                        } );
                    },
                    onOpen: function () {
                        for( let k of keys ) {
                            $( `#address-${k}`).on( 'change', function () {
                                if ( ! $(this).val() ) {
                                    $(this).addClass( 'has-error' )
                                } else {
                                    $(this).removeClass( 'has-error' )
                                }
                            } );
                        }
                
                        $('#address-form .form-group select').select2(select2Options);
                        setTimeout( function() {
                            $( '#address-country' ).trigger( 'change' );
                            if (countCountry == 1) {
                                $('#address-country').next().hide();
                            }
                        }, 50 )
                        var stateLoading = new Select2Cascade($('#address-country'), $('#address-state'), stateUrl, select2Options);
                        var cityLoading = new Select2Cascade($('#address-state'), $('#address-city'), cityUrl, select2Options);
                        var areaLoading = new Select2Cascade($('#address-city'), $('#address-area'), areaUrl, select2Options);
                    },
                    onClose: function(){
                        $('#address-form .form-group select').select2('destroy');
                    }
                }).then( function () {
                    var $form = $('#address-form');
                    $.ajax({
                        url: `${site.shop_url}/address/${address.id}`,
                        type: 'POST',
                        data: $form.serialize(),
                        success: function ( data ) {
                            sa_alert( data.title, data.message, data.status );
                            if ( data.address ) {
                                renderAddress(data.address);
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
                if ( address.phone ) addressStr += '<br>' + address.phone;

                __HTML__.find('.content').html( addressStr );
                $( '.address-wrap' ).find( 'input[type="radio"]' ).removeAttr( 'checked' );
                lsSet( 'addressPref', addressID );
                var existing = $('.address-wrap').find('.address-' + addressID );
                if ( existing.length ) {
                    existing.replaceWith( __HTML__ );
                } else {
                    __HTML__.appendTo('.address-wrap');
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
                speed  =  ! isNaN( speed ) || '' === speed || null === speed || undefined === speed ? 400 : parseFloat( speed );
                speed  = speed <= 0 ? false : speed;
                easing = easing || 'swing';
                var self = this;
                if ( ! self.el.hasClass( 'active') ) {
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
                speed  =  ! isNaN( speed ) || '' === speed || null === speed || undefined === speed ? 400 : parseFloat( speed );
                speed  = speed <= 0 ? false : speed;
                easing = easing || 'swing';
                var self = this;
                if ( self.el.hasClass( 'active') ) {
                    self.el.rc( 'active' );
                    $('html').rc( 'loading' );
                    this.el.rc('preloader');
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
            modal: '#productDetailsModal',
            current: null,
            urlRestore: site.site_url, // Initial Site URL
            showing: false,
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
                        // setTimeout( self.onOpen.bind( self ), 150 );
                        loader.hide( 100 );
                    } );
                // global event
                $(window).on( 'popstate', function ( e ) {
                    var loc = winLoc();
                    if ( loc.includes( 'product/' ) ) {
                        self.show( loc.replace( prod_url, '' ), true );
                    } else {
                        self.close();
                    }
                } );
                if ( ! winLoc().includes( 'product/' ) || self.urlRestore !== site.site_url ) {
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
                    productUrl = site.site_url + 'product/' + data
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
            show: function( data, viewCount ) {
                viewCount = ! ! viewCount;
                if ( 'object' === typeof data ) {
                    data = $.extend( {}, {
                        id: 0,
                        slug: '',
                        name: '',
                        image: '',
                        details: '',
                        price: '',
                        unit: '',
                        in_stock: '',
                        cartItem: false,
                    }, data );
                    if ( true === data.cartItem ) {
                        this.fetch( data.slug );
                        return;
                    }
                } else {
                    this.fetch( data );
                    return;
                }
        
                if ( ! data.id ) {
                    return;
                }
        
                if ( this.current && this.current.id === data.id ) {
                    this.modal.modal( 'show' );
                    return;
                }
                if (viewCount) {
                    $.post(site.shop_url + 'track_view/', $.extend({
                        slug: data.slug
                    }, __CSRF__ ));
                }
                this.current = data;
                this.prepare();
            },
            prepare: function() {
                // if ( ! __CACHES__['prodModal'][this.current.id] ) {
                //     __CACHES__['prodModal'][this.current.id]
                // }
                var __HTML__ = `
                <div class="row">
                    <div class="products">
                        <div class="product product-${this.current.id}">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 gallery-holder">
                            <div class="product-item-thumbnail">
                                <img src="${this.current.image}" alt="${this.current.name}">
                            </div><!-- /.single-product-gallery -->
                            </div><!-- /.gallery-holder -->
                            <div class="col-sm-12 col-md-6 col-lg-6 product-info-block">
                                <div class="product-info">
                                    <h1 class="name" id="productName">${this.current.name}</h1>
                                    <p class="unit">${this.current.unit_name}</p>
                                    <div class="product-price">
                                    ${'' !== this.current.sale_price ? `<span class="price">${this.current.sale_price}</span><span class="price-before-discount">${this.current.regular_price}</span>` : `<span class="price">${this.current.regular_price}</span>`}
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="add-to-cart-count-content">
                                                <button class="cart-qty qty-desc cart-item-decrease-btn-from-product">-</button>
                                                <span class="cart-qty-count cart-item-qty-input-from-product">${this.current.cartQty ? this.current.cartQty : 1}</span>
                                                <button class="cart-qty qty-inc cart-item-increase-btn-from-product">+</button>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="buy-now">
                                                <a class="add-to-cart" data-id="${this.current.id}" href="${this.current.add_to_cart}">Buy Now</a>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="description-container m-t-20">${this.current.details}</div><!-- /.description-container -->
                                </div><!-- /.product-info -->
                            </div><!-- /.col-sm-7 -->
                        </div>
                        <!-- /.product -->
                    </div>
                    <!-- /.products -->
                </div><!-- /.row -->
                `;
                __HTML__ = $( __HTML__ );
                __HTML__.find( '.product' ).data( 'product', this.current );
                this.modal.find( '.main-content' ).empty();
                __HTML__.appendTo( this.modal.find( '.main-content' ) );
        
                this.doc( {
                    title: this.current.meta_title,
                    description: this.current.meta_description,
                    image: this.current.meta_image,
                    url: this.current.link,
                } );
                this.showing = true;
                this.modal.modal( 'show' );
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
            },
            onClose: function() {
                this.doc( { url: this.urlRestore } );
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
                e.preventDefault();
                $( '.cart-sidebar-wrapper' ).toggleClass( 'cart-sidebar-open' );
            }
            var updateCart = function ( event, cartData ) {
                cartData = cartData || cart;
                var min_order    = $('.cart-min-order'),
                    checkoutBtn  = $('.cart-checkout a'),
                    freeShipping = $('.free_shipping'),
                    countTxt     = cartData.total_items > 1 ? lang.items : lang.item;
                updateCheckoutDetails( cartData );
                $('.cart-total-items').attr( 'aria-label', cartData.total_items > 0 ? cartData.total_items + ' ' + countTxt : 'Cart is empty' ).text(cartData.total_items);
                $('.total-cart-item').html( `${cartData.total_items} <span class="sr-only">${countTxt}</span>` );
                $('.cart-total-item-count').html( `${cartData.total_items} ${countTxt}` );
                $('.total-price-basket .value,.total-cart-amount,.checkout-amount').text( cartData.total );
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
                if ( cartData.total_items && cartData.total_items > 0 ) {
                    theCart.removeClass( 'cart-empty' ).empty();
                    var i = 1;
                    $.each( cartData.contents, function () {
                        var item = this;
                        var optHtml = '';
                        // if (item.options) {
                        //     optHtml += `<select name="${i}[option]" class="selectpicker cart-item-option" data-width="100%" data-style="btn-default">`;
                        //     $.each(item.options, function () {
                        //         optHtml += `<option value="${item.id}" ${item.id == item.option ? 'selected' : ''}>${item.name} ${ parseFloat(item.price) != 0 ? '(+' + item.price + ')' : '' }</option>`;
                        //     });
                        //     optHtml += `</select>`;
                        // }
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
                        <div class="cart-item-amount">
                            <p>${item.subtotal}</p>
                            <!-- <p class="regular-price">${item.subtotal}</p> -->
                        </div>
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
                    // $('.cart-item-option').selectpicker('refresh');
                } else {
                    theCart.addClass( 'cart-empty' ).empty();
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
                    product.removeClass( 'added-in-cart');
                    product.addClass( 'not-added-in-cart');
                } else {
                    product.removeClass( 'not-added-in-cart');
                    product.addClass( 'added-in-cart');
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
                    url: site.site_url + 'cart/update',
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
                    site.site_url + 'cart/remove',
                    'You want to remove this item from cart?',
                    'post', { rowid: rowid }, function() {
                        var prod = $( `.product-${prodId}` );
                        prod.addClass( 'not-added-in-cart').removeClass( 'added-in-cart');
                        updateProdData( prodId, { inCart: false, cartQty: 0, rowId: false } );
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
             */
            var ajaxAddToCart = function( prodId, qty ) {
                if ( ajaxify ) {
                    ajaxify.abort();
                    ajaxify = false;
                }
                if ( ! prodId || ! qty ) {
                    console.warn( 'Invalid add to cart request' );
                    return;
                }
                ajaxify = $.ajax( {
                    url: site.site_url + 'cart/add/' + prodId,
                    type: 'GET',
                    dataType: 'json',
                    data: { qty: qty },
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
            var addToCartAnim = function( product, prodId, qty ) {
                qty = qty || 1;
                dragElAnimation( product.find( 'img' ).eq( 0 ) , $('.cart-open-btn .icon') );
                ajaxAddToCart( prodId, qty );
            }
    
            updateCart();
    
            $( document )
            .on('click', '.add-to-cart', function( e ) {
                e.preventDefault();
                var self = $(this),
                    id = self.attr('data-id'),
                    product = self.closest( '.product' ),
                    prodData = product.data( 'product' );
                if ( prodData.rowId ) {
                    var qty = cart.contents[prodData.rowId].qty + 1;
                    updateCartQty( qty, id, prodData.rowId );
                    updateCartItem( { prodId: id, rowid: prodData.rowId, qty: qty }, false );
                } else {
                    addToCartAnim( product, id, 1 );
                }
            } )
            .on( 'click', '.cart-qty', function( e ) {
                e.preventDefault();
                var self = $( this ),
                    prod = self.closest( '.product' ),
                    prodData = prod.data( 'product' ),
                    rowId = '',
                    prodId = '',
                    qtyEl = '.cart-qty-count',
                    qty = parseInt( prod.find( qtyEl + ':eq(0)' ).text() );
        
                if ( prod.hasClass( 'single-cart-item' ) ) {
                    rowId = prod.data( 'row_id' );
                    prodId = prod.data( 'product_id' );
                } else {
                    rowId = prodData.rowId;
                    prodId = prodData.id;
                }
                if ( ! rowId ) {
                    if ( prodId && ! prod.hasClass( 'single-cart-item' ) ) {
                        addToCartAnim( prod, prodId, 1 );
                    }
                    return;
                }
        
                // Increase.
                if ( self.hasClass( 'qty-inc' ) ) qty += 1;
                // Decrease & can set to zero.
                if ( self.hasClass( 'qty-desc' ) && qty > 0 ) qty -= 1;
                if ( qty > 0 ) {
                    updateCartQty( qty, prodId, rowId );
                    updateCartItem( { prodId: prodId, rowid: rowId, qty: qty } );
                } else {
                    // remove removeCartItem
                    removeCartItemAlert( rowId, prodId );
                }
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
                    saa_alert( site.site_url + 'cart/destroy' );
                    var prod = $('.product');
                    prod.addClass( 'not-added-in-cart').removeClass( 'added-in-cart');
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
        get_filters = function () {
            filters.category = $('#product-category').val() ? $('#product-category').val() : filters.category;
            // filters.min_price = $('#min-price').val();
            // filters.max_price = $('#max-price').val();
            var range = $('#price-range').val().split( ',' );
            if ( range.length ) {
                filters.min_price = range[0];
                filters.max_price = range[1];
            }
            filters.in_stock = $('#in-stock').is(':checked') ? 1 : 0;
            filters.promo    = $('#promotions').is(':checked') ? 'yes' : 0;
            filters.cashback = $('#cashbacks').is(':checked') ? 'yes' : 0;
            filters.featured = $('#featured').is(':checked') ? 'yes' : 0;
            filters.sorting  = lsGet('sorting');
            return filters;
        },
        get_width = function() {
            return $(window).width();
        },
        gen_html = function (products) {
            var self = this;
            var html = '';
            if (get_width() > 992) {
                var shop_grid = lsGet('shop_grid');
                var cols = shop_grid == '.three-col' ? 3 : 2;
            } else {
                var shop_grid = '.two-col';
                var cols = 2;
            }
            var pr_con = shop_grid && shop_grid == '.three-col' ? 'col-sm-6 col-md-4' : 'col-md-6';
            var pr_c = shop_grid && shop_grid == '.three-col' ? 'alt' : '';
    
            if (!products) {
                html += `
                <div class="col-sm-12">
                    <div class="alert alert-warning text-center padding-xl margin-top-lg">
                        <h4 class="margin-bottom-no">${lang.x_product}</h4>
                    </div>
                </div>
                `;
                $('#results').html( html );
                return;
            }
    
            if (site.settings.products_page == 1) {
                $('#results').empty();
            }
            html += ``;
            var prods = $('<div class="products"></div>');
            $.each(products, function(index, product) {
                var prodHtml = `
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-5c">
            <div class="product product-${product.id} ${ product.inCart ? 'added-in-cart' : 'not-added-in-cart' }">
            <div class="product-info-wrap">
                <div class="product-image">
                    <div class="image">
                        <a class="view-product" href="${product.link}">
                            <img src="${product.image}" alt="${product.name}">
                        </a>
                    </div>
                    <!-- /.image -->
                    ${ product.cash_back ? `<div class="cash-back">
                        <span class="cash-back-amount">${ product.cash_back_amount }</span>
                        <span class="cash-back-text">${ lang.cash_back }</span>
                    </div>` : '' }
                </div>
                <div class="product-info text-center">
                    <h3 class="name"><a class="view-product" href="${product.link}">${product.name}</a></h3>
                    <div class="unit">${product.unit_name}</div>
                    <div class="product-price">
                    ${'' !== product.sale_price ? `<span class="price">${product.sale_price}</span><span class="price-before-discount">${product.regular_price}</span>` : `<span class="price">${product.regular_price}</span>`}
                    </div>
                </div>
                <div class="overlay-area">
                    <div class="cart-details">
                        <div class="cart-qty qty-inc cart-item-increase-btn-from-product"></div>
                        <a class="add-to-cart add-to-cart-btn" href="${product.add_to_cart}" data-id="${product.id}">Add to Cart</a>
                        <div class="cart-item-and-total-price">
                            <div class="cart-qty qty-inc cart-item-increase-btn-from-product"></div>
                            <div class="cart-total-price">
                                <p><span>${product.current_price}</span></p>
                            </div>
                            <div class="overlay-cart-count">
                                <button class="cart-qty qty-desc cart-item-decrease-btn-from-product">-</button>
                                <span class="cart-qty-count cart-item-qty-input-from-product">${product.cartQty}</span>
                                <button class="cart-qty qty-inc cart-item-increase-btn-from-product">+</button>
                            </div>
                            <p>in Cart</p>
                        </div>
                    </div>
                    <div class="view-product-btn">
                        <a class="view-product" href="${product.link}">Details &gt;&gt;</a>
                    </div>
                </div>
            </div>
            <div class="add-to-cart-wrap">
                <div class="add-to-cart-count-content">
                    <button class="cart-qty qty-desc cart-item-decrease-btn-from-product">-</button>
                    <span class="cart-qty-count cart-item-qty-input-from-product">${product.cartQty}</span>
                    <button class="cart-qty qty-inc cart-item-increase-btn-from-product">+</button>
                </div>
                <a class="add-to-cart add-to-cart-btn" href="${product.add_to_cart}" data-id="${product.id}">Add to Cart</a>
            </div>
        </div>
        </div>
        `;
                prodHtml = $(prodHtml);
                prodHtml.find( '.product' ).data('product', product);
                prodHtml.appendTo(prods);
            });
    
            $('#results').empty();
            $(prods).appendTo($('#results'));
        },
        searchProducts = function ( filters, link ) {
            filters = filters || get_filters();
            if ( history.pushState ) {
                var newurl = window.location.origin + window.location.pathname + '?page=' + filters.page;
                // var newurl = window.location.protocol + '//' + window.location.host + window.location.pathname + '?page=' + filters.page;
                window.history.pushState( { path: newurl, filters: filters }, '', newurl );
            }
            $('#gsLoading').show();
            var data = {};
            data[site.csrf_token] = site.csrf_token_value;
            data['filters'] = filters;
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
                $('#gsLoading').hide();
            });
            if ( location.href.includes('products') ) {
                if (link) {
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
    var sorting = lsGet( 'sorting' );
    if ( sorting ) {
        $('#sorting').val( sorting );
    } else {
        lsSet('sorting', 'name-asc');
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
        loader.show( 0, 'none', function(){
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
    $('.timing-wrapper').GS_Countdown();
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
    $('.price-slider').each( function () {
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
        var product = $(this).closest( '.product' ).parseData( 'product' );
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
            var q = $( this ).val();
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
        .on( 'change', '#in-stock, #promotions, #featured, #cashbacks', function () {
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
            searchProducts( false, link );
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
        
        searchProducts( filters );
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
    if ( $('[name="payment_method"][value="authorize"]').length ) {
        $(document).on( 'change', '[name="payment_method"]', function() {
            $( '#authorize_extra input' ).prop( 'required', ( 'authorize' === $(this).val() ) );
        } );
    }
    // if ( $('[name="payment_method"][value="authorize"]').length ) {
    //     var paymentMethod = $('[name="payment_method"]');
    //     validatorOptions = $.extend( {}, validatorOptions, { fields: {
    //             cc_no: {
    //                 validators: {
    //                     notEmpty: {
    //                         message: 'Card number is required'
    //                     },
    //                     callback: {
    //                         message: 'Card number is not valid',
    //                         callback: function(input) {
    //                             if (input === '' || 'authorize' !== paymentMethod.val() ) {
    //                                 return true;
    //                             }
    //                             // Check if the input is valid credit card number
    //                             const result = FormValidation.Validator.creditCard.validate( FormValidation.Base.prototype, $('#card_number'), 'creditCard' );
    //
    //                             if ( ! result || ! result.valid) {
    //                                 return false;
    //                             } else {
    //                                 let typeValid;
    //                                 // result.meta.type can be one of
    //                                 // AMERICAN_EXPRESS, DINERS_CLUB, DINERS_CLUB_US, DISCOVER, JCB, LASER,
    //                                 // MAESTRO, MASTERCARD, SOLO, UNIONPAY, VISA
    //                                 console.log( result.type );
    //                                 switch (result.type) {
    //                                     case 'AMERICAN_EXPRESS':
    //                                     case 'MASTERCARD':
    //                                     case 'VISA':
    //                                     case 'DISCOVER':
    //                                     case 'DINERS_CLUB_US':
    //                                         typeValid = true;
    //                                         break;
    //                                     default:
    //                                         typeValid = false;
    //                                         break;
    //                                 }
    //                                 if ( ! typeValid ) {
    //                                     return {
    //                                         valid: false,
    //                                         message: 'Unsupported Card Type.'
    //                                     }
    //                                 }
    //                             }
    //                             return true;
    //                         }
    //                     },
    //                 },
    //             },
    //             card_expiry: {
    //                 validators: {
    //                     notEmpty: {
    //                         message: 'Card expiration date required'
    //                     },
    //                     callback: {
    //                         message: 'Card expiration date is not valid',
    //                         callback: function ( input ) {
    //                             if ( input === '' || 'authorize' !== paymentMethod.val() ) {
    //                                 return true;
    //                             }
    //                             if ( ! /^(0?[1-9]|1[012])\/(\d{2})$/g.test( input ) ) {
    //                                 return {
    //                                     valid: false,
    //                                     message: 'Invalid expiration date.'
    //                                 };
    //                             }
    //                             if ( input.split('/')[1] < ( new Date().getFullYear()+'' ).substring( 2 ) ) {
    //                                 return {
    //                                     valid: false,
    //                                     message: 'Invalid Year.'
    //                                 };
    //                             }
    //
    //                             return true;
    //                         },
    //                     },
    //                 },
    //             },
    //             security: {
    //                 validators: {
    //                     notEmpty: {
    //                         message: 'CVV2 is required'
    //                     },
    //                     callback: {
    //                         message: 'The CVV2 number is not valid',
    //                         callback: function ( input ) {
    //                             if ( input === '' || 'authorize' !== paymentMethod.val() ) {
    //                                 return true;
    //                             }
    //                             if ( ! $('#card_number').val() ) {
    //                                 return {
    //                                     valid: false,
    //                                     message: 'Card number required'
    //                                 };
    //                             }
    //                             if ( ! /^[0-9]{3,4}$/.test( input ) ) {
    //                                 return {
    //                                     valid: false,
    //                                     message: 'The CVV2 number is not valid',
    //                                 };
    //                             }
    //                             const result = FormValidation.Validator.creditCard.validate( FormValidation.Base.prototype, $('#card_number'), 'creditCard' );
    //                             if ( ! result || ! result.type ) {
    //                                 return {
    //                                     valid: false,
    //                                     message: 'Please Check Card Number'
    //                                 };
    //                             } else {
    //                                 return (
    //                                     (
    //                                         'AMERICAN_EXPRESS' === result.type
    //                                     ) ? (
    //                                         input.length === 4
    //                                     ) : (
    //                                         input.length === 3
    //                                     )
    //                                 );
    //                             }
    //                         },
    //                     }
    //                 }
    //             }
    //         } } );
    // }
    // $('.checkout').formValidation( validatorOptions );
    
    // @XXX shipping address!
    $(document).on( 'change', '#same_as_billing', function (e) {
        $('.guest-shipping-address').slideToggle();
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
    
    var loginModal = $('#loginModal');
    
    $(document).on('click', '.join-btn,[data-modal]', function (e) {
        e.preventDefault();
        var m = $( this ).data( 'modal' ),
            c = m ? '.' + m + '-form' : false,
            b = loginModal.find( c );
        if ( b.length ) {
            b.show().siblings().hide();
        }
        if ( ! loginModal.data( 'modal-visible' ) ) {
            loginModal.modal('show');
        }
    });
    
    $('#loginModal').on('show.bs.modal', function (e) {
        loginModal.data( 'modal-visible', true );
    });
    
    $('#loginModal').on('hidden.bs.modal', function (e) {
        $('#loginModal .sign-in-form').show().siblings().hide();
        loginModal.data( 'modal-visible', false );
    });
    
    var firstLoad = true,
        isFetchingSlots = false,
        slotsWrap = $('.delivery-slot-container'),
        slotsTmpl = $( '#delivery-slots-tmpl' ).text().trim(),
        slotTmpl = $( '#delivery-slot-tmpl' ).text().trim(),
        slotForm = $( '.deliver-slot-form' ),
        hasChecked = false;
    function getSlots( area, date ) {
        if ( isFetchingSlots ) {
            isFetchingSlots.abort();
            isFetchingSlots = false;
        }
        firstLoad = false;
        if ( area && date ) {
            slotForm.slideDown();
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
            if (!area) {
                slotsWrap.html('<div class="col-sm-12"><div class="alert alert-warning">Update Your Address</div></div>');
                slotForm.slideUp();
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
        console.log( { area } );
        if ( area ) {
            getSlots( area, $('#delivery-slot-date').val() );
        }
    }, 500 ) );
    //
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
    })
    .on('click','.next-button, .prev-button', function (e) {
        e.preventDefault();

        var $this = $(this),
            type = $this.val(),
            wrapper = $this.closest('fieldset'),
            nextWrap = wrapper.next(),
            prevWrap = wrapper.prev(),
            ActiveBar = $('.progress-step li.current'),
            nextBar = ActiveBar.next(),
            prevBar = ActiveBar.prev();

        wrapper.hide();
        ActiveBar.removeClass('current');

        switch (type) {
            case 'Next':
                nextWrap.show();
                nextBar.addClass('active current');
                break;
            case 'Back':
                prevWrap.show();
                prevBar.addClass('current');
                ActiveBar.removeClass('active');
                break;
        }
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
            
            if ( parent.prop('disabled') ) {
                return this;
            }
            
            // Register functions to be called after cascading data loading done
            this.then = function(callback) {
                afterActions.push(callback);
                return this;
            };
            
            parent.select2(select2Options).on("change", function (e) {
                
                child.prop("disabled", true);
                
                var _this = this;
                var __URL__ = typeof( url ) === 'string' ? (
                    url.replace( ':cc:', $( '#address-country' ).val() ).
                        replace( ':sc:', $( '#address-state' ).val() ).
                        replace( ':city:', $( '#address-city' ).val() ).
                        replace( ':zip:', $( '#address-postal_code').val() )
                ) : ( 'function' === typeof url ? url() : false );
                if ( ! __URL__ ) {
                    return;
                }
                $.getJSON( __URL__, function(items) {
                    if ( items.hasOwnProperty( 'zone' ) ) {
                        $( '#address-zone, #billing_zone, #shipping_zone' ).val( items.zone );
                        items = items.area;
                    }
                    var newOptions = '<option value=""> '+ ( child.attr( 'placeholder' ) || '-- Select --' ) +' </option>';
                    for ( var id in items ) {
                        var value = items[id];
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
        
    })( window, jQuery);
    
    
    var select2Options = { width: '100%' };
    var stateUrl =  site.base_url + 'cart/getShippingStates/?cc=:cc:';
    var cityUrl =  site.base_url + 'cart/getShippingCities/?cc=:cc:&sc=:sc:';
    var areaUrl =  site.base_url + 'cart/getShippingAreas/?cc=:cc:&sc=:sc:&city=:city:&zip=:zip:';
    
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
    $(document).on( 'change', '[name="address"],#billing_city, #shipping_city, [name="delivery_slot"]', __debounce( function () {
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
    
    var firstChangeShippingMethod = true,
        isSettingShippingMethod = false;
    $(document).on( 'change', '[name="shipping_method"]', __debounce(function(){
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
        
    }, 500));
    
    if ( $('.guest-checkout').length ) {
        
        $( '.guest-checkout .form-group select' ).select2( select2Options );
        
        new Select2Cascade($('#billing_country'), $('#billing_state'), function() {
            return stateUrl.replace( ':cc:', $( '#billing_country' ).val() );
        }, select2Options);
        new Select2Cascade($('#billing_state'), $('#billing_city'), function() {
            return cityUrl.replace( ':cc:', $( '#billing_country' ).val() ).
                replace( ':sc:', $( '#billing_state' ).val() );
        }, select2Options);
        var billingArea = new Select2Cascade($('#billing_city'), $('#billing_area'), function() {
            return areaUrl.replace( ':cc:', $( '#billing_country' ).val() ).
                replace( ':sc:', $( '#billing_state' ).val() ).
                replace( ':city:', $( '#billing_city' ).val() ).
                replace( ':zip:', $( '#billing_postal_code').val() );
        }, select2Options);
        
        new Select2Cascade($('#shipping_country'), $('#shipping_state'), function() {
            return stateUrl.replace( ':cc:', $( '#shipping_country' ).val() );
        }, select2Options);
        new Select2Cascade($('#shipping_state'), $('#shipping_city'), function() {
            return cityUrl.replace( ':cc:', $( '#shipping_country' ).val() ).
                replace( ':sc:', $( '#shipping_state' ).val() );
        }, select2Options);
        var shippingArea = new Select2Cascade($('#shipping_city'), $('#shipping_area'), function() {
            return areaUrl.replace( ':cc:', $( '#shipping_country' ).val() ).
                replace( ':sc:', $( '#shipping_state' ).val() ).
                replace( ':city:', $( '#shipping_city' ).val() ).
                replace( ':zip:', $( '#shipping_postal_code').val() );
        }, select2Options);
        
        function handleShippingArea ( parent, child, items ) {
            child.prop( 'disabled', ! items.length );
            if ( ! items.length ) {
                $('.delivery-slot-wrap').slideUp();
            } else {
                $('.delivery-slot-wrap').slideDown();
            }
        }
        
        billingArea.then( handleShippingArea );
        shippingArea.then( handleShippingArea );
    }
    
} )( jQuery, pageNow )
