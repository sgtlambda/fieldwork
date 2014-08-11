var _JannieForms = {};
(function($){
    var JannieForms = {
        AJAXCALLBACK: 0,
        SUBMITCALLBACK: 1,
        i: {
            callbacks: []
        },
        validators: {
            regex: function(value, validator){
                var match = /\/(.*)\/([a-z]*)/.exec(validator.pattern);
                return value.match(new RegExp(match[1], match[2]));
            }
        },
        registerCallback: function(slug, on, fn) {
            this.i.callbacks.push({
                slug: slug,
                on: on,
                fn: fn
            });
            return this;
        },
        retrieveCallback: function(slug, on) {
            return _.findWhere(this.i.callbacks, {
                slug: slug,
                on: on
            });
        },
        performCallback: function(slug, on, form, data, e) {
            var callback = this.retrieveCallback(slug, on);
            if( callback !== undefined )
                callback.fn (form, data, e);
        },
        ajaxSubmitForm: function(form) {
            $.ajax({
                url: '/ajax/' + form.ajaxMethod + '/',
                data: form.getValues(),
                dataType: 'json',
                type: 'POST',
                success: function(data){
                    _JannieForms.performCallback(
                        form.ajaxMethod, 
                        JannieForms.AJAXCALLBACK, 
                        form, 
                        data, {
                            type: JannieForms.AJAXCALLBACK,
                            instant: false
                        }
                    );
                },
                error: function(){
                    if(console)
                        console.log('Jannieforms Ajax encountered an error');
                }
            });
        }
    };
    _JannieForms = JannieForms;
    $(document).trigger("jannieforms-loaded", _JannieForms);
    function JannieForm($form, formData){
        this.slug = formData.slug;
        this.submitCallback = formData.submitCallback;
        this.hiddenFieldName = formData.hiddenFieldName;
        this.fields = [];
        this.dataFields = formData.dataFields;
        this.element = $form;
        this.ajaxEnabled = formData.ajax.submitEnabled;
        var fieldsData = formData.fields;
        for(var field in fieldsData)
            this.fields.push(new JannieField(this, fieldsData[field]));
        var form = this;
        if(formData.ajax.method !== ""){
            this.ajaxMethod = formData.ajax.method;
            if(formData.ajax.results !== null)
                _JannieForms.performCallback(
                    this.ajaxMethod, 
                    JannieForms.AJAXCALLBACK,
                    this, 
                    formData.ajax.results, {
                        type: JannieForms.AJAXCALLBACK,
                        instant: true
                    }
                );
        }
        
        if($.fn.inputActionOnEnter)
            $form.find('[data-input-action-on-enter]').inputActionOnEnter();
        
        $form.on({submit: function(e){   
            form.submit(e);
        }});
        _JannieForms.processForms();
    }
    $.extend(JannieForm.prototype, {
        validate: function() {
            for(var n in this.fields)
                this.fields[n].validate();
        },
        isValid: function() {
            var valid = true;
            for(var n in this.fields)
                valid = this.fields[n].isValid() ? valid : false;
            return valid;
        },
        submit: function(e){
            this.validate();
            if(!this.isValid()){
                e.preventDefault();
                for(var n in this.fields)
                    this.fields[n].cancelSubmit();
                for(n in this.fields)
                    if(!this.fields[n].isValid()){
                        this.fields[n].element.focus().jtShow();
                        break;
                    }
            }else if(this.submitCallback !== ""){
                var fn = window[this.submitCallback];
                if(typeof fn === 'function')
                    fn(e, this);
            }
            _JannieForms.performCallback(this.slug, _JannieForms.SUBMITCALLBACK, this, null, e);
            if(this.ajaxEnabled){
                e.preventDefault();
                _JannieForms.ajaxSubmitForm(this);
            }
        },
        getValues: function(){
            var values = {};
            values[this.hiddenFieldName] = "yes"; //force submit
            for(var n in this.fields)
                if(this.fields[n].hasValue())
                    values[this.fields[n].getName()] = this.fields[n].getValue();
            return values;
        },
        getFieldByName: function(name) {
            for(var n in this.fields)
                if(this.fields[n].getName() === name)
                    return this.fields[n];
            return false;
        }
    });
    function JannieField(form, fieldData){
        this.form = form;
        this.element = $("#"+fieldData.id);
        this.touched = false;
        this.valid = false;
        this.validators = fieldData.validators;
        this.isButton = fieldData.isButton === true;
        this.clicked = false;
        var field = this;
        this.element.on({
            blur: function(){
                field.blur();
            },
            focus: function(){
                field.touched = true;
            },
            click: function(){
                field.clicked = true;
            }
        });
    }
    $.extend(JannieField.prototype, {
        blur: function(){
            if(!this.touched) return;
            this.validate();
        }, 
        cancelSubmit: function(){
            this.clicked = false;
        },
        validate: function(){
            var val = this.getValue();
            var valid = true;
            for(var v in this.validators)
                if((JannieForms.validators[this.validators[v].method])){
                    if(this.element.attr("placeholder") == val) val = "";
                    if (! ( (JannieForms.validators[this.validators[v].method])(val, this.validators[v]) ) ){
                        this.setInvalid(this.validators[v].error);
                        valid = false;
                        break;
                    }
                }
            if(valid)
                this.setValid();
        }, 
        getName: function(){
            return this.element.attr('name');
        },
        hasValue: function(){
            return (!this.isButton || this.clicked);
        },
        getValue: function(){
            return this.element.val();
        }, 
        setInvalid: function(error){
            this.element.removeClass("valid").addClass("invalid");
            this.element.jtLink(error, ['focus'], []);
            this.valid = false;
        }, 
        setValid: function(){
            this.element.removeClass("invalid")
            if(this.validators.length)
                this.element.addClass("valid");
            this.element.jtUnlink();
            this.valid = true;
        }, 
        isValid: function(){
            return this.valid;
        }
    });
    $.fn.jannieform = function(formData){
        new JannieForm($(this), formData);
    };
    $.fn.recaptchaUnitRefresh = function(){
        var $this = this;
        var table = $this.data('recaptcha-elmt');
        var imgSrc = table.find('#recaptcha_image img').attr('src');
        var challenge = table.find('input[name="recaptcha_challenge_field"]').val();
        $this.find('.recaptcha-img').attr('src', imgSrc);
        $this.find('.rc-challenge-field').val(challenge);
    }
    $(function(){
        
        var recaptchaReload = function(){
            Recaptcha.reload();
            var unit = $(this).parents(".recaptcha-unit");
            var refresh = function(){unit.recaptchaUnitRefresh();}
            setTimeout(refresh, 100);
            setTimeout(refresh, 500);
        };
        var recaptchaSwitchType = function(){
            var unit = $(this).parents('.recaptcha-unit');
            var image = unit.hasClass("type-image");
            var newType = image?'audio':'image';
            Recaptcha.switch_type(newType);
            if(newType=='audio'){
                unit.find('.rc-response-field').attr('placeholder', 'Type de nummers die u hoort');
                if(window['wpTheme'] != undefined)
                    wpTheme.applyPlaceholders();
                var updateLink = function(){unit.find('.recaptcha-audio-link').attr('href', unit.data('recaptcha-elmt').find('#recaptcha_audio_download').attr('href'));}
                setTimeout(updateLink, 100);
                setTimeout(updateLink, 500);
            }else{
                unit.find('.rc-response-field').attr('placeholder', 'Type de bovenstaande woorden');
                if(window['wpTheme'] != undefined)
                    wpTheme.applyPlaceholders();
                unit.find('.recaptcha-img').attr('src', '');
            }
            unit.removeClass("type-image type-audio").addClass("type-" + newType);
            var refresh = function(){unit.recaptchaUnitRefresh();}
            setTimeout(refresh, 100);
            setTimeout(refresh, 500);
        };
        var processForms = function(){
            $(".jannierecaptcha:not(.processed)").each(function(){
                var $this = $(this);
                var img = $this.find('#recaptcha_image img');
                if(img.length){
                    var responseFieldName = $this.attr('data-recaptcha-response-field-name');
                    var captchaIsInvalid = $this.hasClass('invalid');
                    var validClassName = (captchaIsInvalid?'invalid':'');
                    var replacementCaptchaUnit = $('<div class="recaptcha-unit type-image"><div class="recaptcha-img-wrap"><img class="recaptcha-img"><a target="_blank" class="recaptcha-audio-link">Download geluid (.mp3)</a><ul class="recaptcha-img-actions">' + 
                        '<li><a title="Een nieuwe verificatie proberen" class="rca-reload"></a></li>'+
                        '<li><a title="Verander type verificatie: audio/afbeelding" class="rca-switch-type current-type-image"></a></li></ul></div>'+
                        '<input type="hidden" class="rc-challenge-field" name="recaptcha_challenge_field"><input type="text" name="' + responseFieldName + '" value="" id="'+$this.attr('field-id')+'" class="janniefield jannieinputfield recaptchafield textfield rc-response-field ' + validClassName + '" placeholder="Type de bovenstaande woorden"></div>');
                    replacementCaptchaUnit.data('recaptcha-elmt', $this);
                    replacementCaptchaUnit.find('.rca-reload').click(recaptchaReload);
                    replacementCaptchaUnit.find('.rca-switch-type').click(recaptchaSwitchType);
                    var responseField = $(replacementCaptchaUnit.find('input[name="' + responseFieldName + '"]')[0]);
                    $this.after(replacementCaptchaUnit);
                    $this.addClass("processed").hide();
                    if(captchaIsInvalid){
                        responseField.jtLink('Probeer het nog eens.', ['focus'], []);
                        responseField.jtShow();
                    }
                }
            });
            $('.recaptcha-unit').each(function(){
                $(this).recaptchaUnitRefresh();
            });
            $(".invisible-target-button:not(.processed)").each(function(){
                var $this = $(this);
                $this.addClass('processed');
                $("#target-" + $this.attr('id')).on({click: function(){
                    $this.click();
                }});
            });
        };
        $('[data-input-mask]').each(function(){
            var $this = $(this);
            $this.mask($this.data('input-mask'));
        });
        processForms();
        _JannieForms.processForms = processForms;
    });
})(jQuery);