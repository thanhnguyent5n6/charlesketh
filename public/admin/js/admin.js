var Admin = function(){

    var handleCkeditors = function(){
        $('.ck-editor').each(function(index, el) {
            var id = $(this).find('textarea').attr('id');
            CKEDITOR.replace( id, {
                on: {
                    instanceReady: function() {
                        // Show textarea for dev purposes.
                        // this.element.show();
                    },
                    change: function() {
                        // Sync textarea.
                        this.updateElement();    
                        
                        // Fire keyup on <textarea> here?
                    }
                },
                height : 400,
                entities: false,
                basicEntities: false,
                entities_greek: false,
                entities_latin: false,
                filebrowserBrowseUrl : Laravel.baseUrl+'/public/packages/ckeditor/ckfinder/ckfinder.html',
                filebrowserImageBrowseUrl : Laravel.baseUrl+'/public/packages/ckeditor/ckfinder/ckfinder.html?type=Images',
                filebrowserFlashBrowseUrl : Laravel.baseUrl+'/public/packages/ckeditor/ckfinder/ckfinder.html?type=Flash',
                filebrowserUploadUrl : Laravel.baseUrl+'/public/packages/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
                filebrowserImageUploadUrl : Laravel.baseUrl+'/public/packages/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
                filebrowserFlashUploadUrl : Laravel.baseUrl+'/public/packages/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
                allowedContent:
                    'h1 h2 h3 p blockquote strong em;' +
                    'a[!href];' +
                    'img(left,right)[!src,alt,width,height];' +
                    'table tr th td caption;' +
                    'span{!font-family};' +
                    'span{!color};' +
                    'span(!marker);' +
                    'del ins'
            });
        });
    }

    var handleSideBarActive = function(){
        $('ul.page-sidebar-menu .nav-item .nav-link[data-route="'+Laravel.routeName+'"]').parents('.nav-item').addClass('active open');
    }

	var handleGroupCheckable = function(){
		$('body').on('click', 'input.group-checkable', function(){
            $(this).parents('table').find('input.checkable').prop('checked',$(this).is(':checked'));
            $('.count-checkbox').html( $('input.checkable:checked').length );
        });
        $('body').on('click', 'input.group-checkable-role', function(){
			$(this).parents('tr').find('input.checkable').prop('checked',$(this).is(':checked'));
			$('.count-checkbox').html( $('input.checkable:checked').length );
		});
	}

	var handleStripUnicode = function(str){
		str = str.toLowerCase();
        str = str.replace(/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/g, 'a');
        str = str.replace(/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/g, 'e');
        str = str.replace(/(ì|í|ị|ỉ|ĩ)/g, 'i');
        str = str.replace(/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/g, 'o');
        str = str.replace(/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/g, 'u');
        str = str.replace(/(ỳ|ý|ỵ|ỷ|ỹ)/g, 'y');
        str = str.replace(/(đ)/g, 'd');
        str = str.replace(/([^0-9a-z-\s])/g, '');
        str = str.replace(/(\s+)/g, '-');
        str = str.replace(/^-+/g, '');
        str = str.replace(/--+$/g, '-');
        return str;
	}

	var handleStrToSlug = function(){
		$('input.title').on('keyup blur', function(){
            var str = handleStripUnicode($(this).val());
            $('input.slug').val(str);
        });
        $('input.slug').on('keyup', function(event){
        	if( event.keyCode>=37 && event.keyCode<=40 ) return;
            var str = handleStripUnicode($(this).val());
            $(this).val(str);
        });
	}

    var handleComment = function(){
        var targetComment = localStorage.getItem("targetComment");

        $('.nav-list-item-comment').on('click', 'a', function(e){
            e.preventDefault();
            var btn = $(this);
            var li = btn.parent('li');
            var result = $('#portlet-load-ajax');
            if(li.hasClass('active')) return false;
            $('.nav-list-item-comment li').removeClass('active');
            li.addClass('active');
            localStorage.setItem("targetComment",btn.attr('href'));
            var dataAjax = btn.attr('data-ajax').replace(/\|/g,'&')+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/admin/comments/ajax',
                data: dataAjax,
                beforeSend: function(){
                    result.html('Loading...');
                }
            }).done(function(response){
                result.html(response.data);
            });
        })

        if(targetComment && $('.nav-list-item-comment a[href="'+targetComment+'"]').length > 0){
            $('.nav-list-item-comment a[href="'+targetComment+'"]').trigger('click');
        }else{
            $('.nav-list-item-comment a:first').trigger('click');
        }

        $('body').on('click', '.btn-comment-unapproved', function(e){
            $('.timeline-item:not(.disabled)').slideUp();
        });

        $('body').on('click', '.btn-comment-approved', function(e){
            $('.timeline-item:hidden').slideDown();
        });

        $('body').on('click', '.btn-comment-reply', function(e){
            e.preventDefault();
            var btn = $(this);
            var wrap = btn.closest('.timeline-wrap');
            if( wrap.find(' > .comment-form').length > 0 ) return false;
            var parentID = parseInt(btn.attr('data-parent'));
            var categoryID = parseInt(btn.attr('data-category'));
            var productID = parseInt(btn.attr('data-product'));
            var postID = parseInt(btn.attr('data-post'));
            $('.timeline .comment-form').slideUp(function(){
                $(this).remove();
            });
            var form = $('<form action="#" method="post" class="comment-form display-hide">'+
                    '<input type="hidden" name="parent" value="'+parentID+'">'+
                    '<input type="hidden" name="category_id" value="'+categoryID+'">'+
                    '<input type="hidden" name="product_id" value="'+productID+'">'+
                    '<input type="hidden" name="post_id" value="'+postID+'">'+
                    '<div class="form-group"><textarea name="description" class="form-control" rows="6"></textarea></div>'+
                    '<div class="form-group"><button type="submit" class="btn btn-info btn-comment-submit" data-ajax="type=default"> Trả lời </button></div>'+
                '</form>');
            form.appendTo(wrap).slideDown(function(){
                App.scrollTo(form);
            });

        });

        $('body').on('click', '.btn-comment-status', function(e){
            e.preventDefault();
            var btn = $(this);
            if( typeof btn.attr('data-ajax') === 'undefined' ) return;
            var wrap = btn.closest('.timeline-item');
            var dataAjax = btn.attr('data-ajax').replace(/\|/g,'&')+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/admin/ajax',
                data: dataAjax,
                beforeSend: function(){
                    btn.button('loading');
                }
            }).fail(function(response) {
                alert( response.statusText );
                btn.button('reset');
            }).done(function(response){
                btn.button('reset');
                wrap.toggleClass('disabled');
            });
        });

        $('body').on('click', '.btn-comment-expand', function(e){
            e.preventDefault();
            var btn = $(this);
            var wrap = btn.closest('.timeline-wrap');
            wrap.find('.timeline').toggle('slow');
            btn.toggleClass('active');
        });

        $('body').on('click', '.btn-comment-submit', function(e){
            e.preventDefault();
            var btn = $(this);
            var frm = btn.parents('form');
            var wrap = btn.closest('.timeline-wrap');
            var dataAjax = frm.serialize()+'&'+btn.data('ajax').replace(/\|/g,'&')+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/admin/comments',
                data: dataAjax,
                beforeSend: function(){
                    btn.button('loading');
                }
            }).done(function(response){
                btn.button('reset');
                if(response.type == 'success'){
                    var data = response.data.replace('timeline','timeline display-hide');
                    wrap.append(data);
                    frm.slideUp(function(){
                        $(this).remove();
                        wrap.find('.timeline').slideDown();
                    });
                }else{
                    App.alert({
                        container: frm, // alerts parent container(by default placed after the page breadcrumbs)
                        place: 'prepend', // "append" or "prepend" in container 
                        type: response.type, // alert's type
                        message: response.message, // alert's message
                        close: true, // make alert closable
                        reset: true, // close all previouse alerts first
                        focus: true, // auto scroll to the alert after shown
                        closeInSeconds: 5, // auto close after defined seconds
                        icon: response.icon // put icon before the message
                    });
                }
            });
        });

        $('body').on('click', '.btn-comment-delete', function(e){
            e.preventDefault();
            var btn = $(this);
            var id = btn.attr('data-id');
            var listID = btn.attr('data-id');
            var wrap = btn.closest('.timeline-item');
            wrap.find('.btn-comment-delete').each(function(){
                listID = listID+","+$(this).attr('data-id');
            });
            swal({
                title: 'Bạn muốn xóa dữ liệu này?',
                text: '',
                type: '',
                allowOutsideClick: true,
                showConfirmButton: true,
                showCancelButton: true,
                showLoaderOnConfirm: true,
                confirmButtonClass: 'btn-primary',
                cancelButtonClass: 'btn-default',
                closeOnConfirm: false,
                closeOnCancel: true,
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Bỏ qua',
            },function(isConfirm){
                if (isConfirm){
                    $.ajax({
                        type: 'POST',
                        url : Laravel.baseUrl+'/admin/ajax',
                        data: {'act':'delete_record', 'table':'comments', 'id':listID, '_token':Laravel.csrfToken}
                    }).fail(function(response) {
                        alert( response.statusText );
                    }).done(function(response){
                        $('#record-'+id).slideUp(function(){
                            $(this).remove();
                        });
                        swal('Đã thực thi', '', "success");
                    });
                }
            });
        });
        
        $('body').on('click', '#btn-comment-delete-all', function(e){
            e.preventDefault();
            var btn = $(this);
            var listID = '';
            var wrap = btn.closest('.profile-content');
            wrap.find('.btn-comment-delete').each(function(){
                listID = listID+","+$(this).attr('data-id');
            });
            swal({
                title: 'Bạn muốn xóa dữ liệu này?',
                text: '',
                type: '',
                allowOutsideClick: true,
                showConfirmButton: true,
                showCancelButton: true,
                showLoaderOnConfirm: true,
                confirmButtonClass: 'btn-primary',
                cancelButtonClass: 'btn-default',
                closeOnConfirm: false,
                closeOnCancel: true,
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Bỏ qua',
            },function(isConfirm){
                if (isConfirm){
                    $.ajax({
                        type: 'POST',
                        url : Laravel.baseUrl+'/admin/ajax',
                        data: {'act':'delete_record', 'table':'comments', 'id':listID, '_token':Laravel.csrfToken}
                    }).fail(function(response) {
                        alert( response.statusText );
                    }).done(function(response){
                        wrap.find('.portlet-body').slideUp(function(){
                            $(this).remove();
                        });
                        swal('Đã thực thi', '', "success");
                    });
                }
            });
        });
    }

    var handleAjax = function(){
        // Change Status
        $('.btn-status:enabled').on('click', function(e){
            e.preventDefault();
            var btn = $(this);
            if( typeof btn.data('ajax') === 'undefined' ) return;
            var dataAjax = btn.data('ajax').replace(/\|/g,'&')+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/admin/ajax',
                data: dataAjax,
                beforeSend: function(){
                    btn.button('loading');
                }
            }).fail(function(response) {
                alert( response.statusText );
                btn.button('reset');
            }).done(function(response){
                btn.button('reset').toggleClass('blue');
            });
        });

        // Change Priority
        $('.input-priority').on('blur', function(e){
            e.preventDefault();
            var input = $(this);
            if( typeof input.data('ajax') === 'undefined' ) return;
            var dataAjax = input.data('ajax').replace(/\|/g,'&')+'&val='+input.val()+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/admin/ajax',
                data: dataAjax,
                beforeSend: function(){
                    input.prop('disabled',true);
                }
            }).fail(function(response) {
                alert( response.statusText );
                input.prop('disabled',false);
            }).done(function(response){
                input.prop('disabled',false);
            });
        });

        $('.btn-delete').on('click', function(e){
            e.preventDefault();
            var btn = $(this);
            var frm = btn.parents('form');
            swal({
                title: 'Bạn muốn xóa dữ liệu này?',
                text: '',
                type: '',
                allowOutsideClick: true,
                showConfirmButton: true,
                showCancelButton: true,
                showLoaderOnConfirm: true,
                confirmButtonClass: 'btn-primary',
                cancelButtonClass: 'btn-default',
                closeOnConfirm: false,
                closeOnCancel: true,
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Bỏ qua',
            },function(isConfirm){
                if (isConfirm){
                    frm.submit();
                }
            });
        });

        $('.btn-cancel').on('click', function(e){
            var btn = $(this);
            $('#cancel-modal form').attr('action',btn.data('url'));
        });

        $('body').on('click', '.btn-delete-file', function(e){
            e.preventDefault();
            var btn = $(this);
            if( typeof btn.data('ajax') === 'undefined' ) return;
            var dataAjax = btn.data('ajax').replace(/\|/g,'&')+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/admin/ajax',
                data: dataAjax,
                beforeSend: function(){
                }
            }).fail(function(response) {
                alert( response.statusText );
            }).done(function(response){
            });
        });

        $('.btn-action').on('click', function(e){
            e.preventDefault();
            var btn = $(this);
            if( typeof btn.data('ajax') === 'undefined' ) return;
            var dataType = btn.data('type');
            var dataAjax = btn.data('ajax').replace(/\|/g,'&')+'&_token='+Laravel.csrfToken;

            var listID = '';
            var sa_title = 'Bạn muốn thực hiện hành động này?';
            var sa_message = '';
            var sa_type = 'warning';
            var sa_allowOutsideClick = true;
            var sa_showConfirmButton = true;
            var sa_showCancelButton = true;
            var sa_showLoaderOnConfirm = true;
            var sa_closeOnConfirm = false;
            var sa_closeOnCancel = true;
            var sa_confirmButtonText = 'Đồng ý';
            var sa_cancelButtonText = 'Bỏ qua';
            var sa_popupTitleSuccess = 'Đã thực thi';
            var sa_popupMessageSuccess = '';
            var sa_popupTitleCancel = 'Đã hủy';
            var sa_popupMessageCancel = 'You have disagreed to our Terms and Conditions';
            var sa_confirmButtonClass = 'btn-primary';
            var sa_cancelButtonClass = 'btn-default';

            $('input.checkable:checked').each(function(){
                listID = listID+","+$(this).val();
            });

            listID=listID.substr(1);
            if (listID == '') {
                App.alert({
                    container: '#alert-container', // alerts parent container(by default placed after the page breadcrumbs)
                    place: 'append', // "append" or "prepend" in container 
                    type: 'danger', // alert's type
                    message: 'Không có bản ghi nào được chọn', // alert's message
                    close: true, // make alert closable
                    reset: true, // close all previouse alerts first
                    focus: true, // auto scroll to the alert after shown
                    closeInSeconds: 5, // auto close after defined seconds
                    icon: 'warning' // put icon before the message
                });
                return false;
            }
            swal({
                title: sa_title,
                text: sa_message,
                type: sa_type,
                allowOutsideClick: sa_allowOutsideClick,
                showConfirmButton: sa_showConfirmButton,
                showCancelButton: sa_showCancelButton,
                showLoaderOnConfirm: sa_showLoaderOnConfirm,
                confirmButtonClass: sa_confirmButtonClass,
                cancelButtonClass: sa_cancelButtonClass,
                closeOnConfirm: sa_closeOnConfirm,
                closeOnCancel: sa_closeOnCancel,
                confirmButtonText: sa_confirmButtonText,
                cancelButtonText: sa_cancelButtonText,
            },function(isConfirm){
                if (isConfirm){
                    $.ajax({
                        type: 'POST',
                        url : Laravel.baseUrl+'/admin/ajax',
                        data: dataAjax+'&id='+listID
                    }).fail(function(response) {
                        alert( response.statusText );
                    }).done(function(response){
                        if( dataType === 'delete' ){
                            $.each(listID.split(','), function(i,v){
                                $('#record-'+v).slideUp(function(){
                                    $(this).remove();
                                });
                            });
                        }else{
                            $.each(listID.split(','), function(i,v){
                                $('.btn-status-'+dataType+'-'+v).toggleClass('blue');
                            });
                        }
                        swal(sa_popupTitleSuccess, sa_popupMessageSuccess, "success");
                    });
                    
                } else {
                    // swal(sa_popupTitleCancel, sa_popupMessageCancel, "error");
                }
            });
        });

        $('.btn-quick-add').on('click', function(e){
            e.preventDefault();
            var btn = $(this);
            var frm = btn.parents('form');
            if( typeof btn.data('url') === 'undefined' ) return;
            var IDs = $('select[name="'+btn.data('ajax')+'"').val();
            var dataAjax = frm.serialize()+'&ids='+IDs+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : btn.data('url'),
                data: dataAjax,
                beforeSend: function(){
                    btn.button('loading');
                }
            }).done(function(response){
                btn.button('reset').toggleClass('blue');
                if(response.type == 'success'){
                    $('select[name="'+btn.data('ajax')+'"').html(response.newData).selectpicker('refresh');
                    frm.find('.input-rs').val('');
                }
                App.alert({
                    container: frm.find('.modal-body'), // alerts parent container(by default placed after the page breadcrumbs)
                    place: 'prepend', // "append" or "prepend" in container 
                    type: response.type, // alert's type
                    message: response.message, // alert's message
                    close: true, // make alert closable
                    reset: true, // close all previouse alerts first
                    focus: false, // auto scroll to the alert after shown
                    closeInSeconds: 5, // auto close after defined seconds
                    icon: response.icon // put icon before the message
                });
            });
        });

        $('#refresh-analysis').on('click', function(e){
            e.preventDefault();
            $('#yoast-seo').css('display','block');
            $('#content').val($('.tab-pane.active .content').val());
            $('#focusKeyword').val($('.tab-pane.active .meta-keywords').val());
            $('#snippet-editor-title').val($('.tab-pane.active .meta-title').val());
            $('#snippet-editor-slug').val($('.slug').val());
            $('#snippet-editor-meta-description').val($('.tab-pane.active .meta-description').val());
            $('#snippet-editor-title').focus();
        });
        
    }

    var handleFileuploader = function() {
        $('input[name="files"]').fileuploader({
            addMore: true,
            enableApi: true,
            editor: {
                cropper: {
                    ratio: '',
                    minWidth: 100,
                    minHeight: 100,
                    showGrid: true
                }
            },
            thumbnails: {
                box: '<div class="table-responsive">\
                    <table class="table table-bordered table-hover">\
                        <thead>\
                            <tr role="row" class="heading">\
                                <th width="2%"> Thứ tự </th>\
                                <th width="8%"> Hình ảnh </th>\
                                <th width="25%"> Tiêu đề </th>\
                                <th width="10%"> </th>\
                            </tr>\
                        </thead>\
                        <tbody class="fileuploader-items-list"></tbody>\
                    </table>\
                </div>',
                boxAppendTo: $('.fileuploader-items'),
                item: '<tr class="fileuploader-item columns">\
                    <td align="center" class="column-order">\
                        <input type="hidden" name="attachment[name][]" value="${name}">\
                        <input type="text" class="form-control input-mini input-orderby" name="attachment[priority][]" value="1"> </td>\
                    <td align="center" class="column-thumbnail">\
                        ${image} <span class="fileuploader-action-popup"></span>\
                    </td>\
                    <td class="column-title">\
                        <input type="text" class="form-control" name="attachment[alt][]" value="${title}"> </td>\
                    <td class="column-actions">\
                        <a href="javascript:;" class="btn btn-sm red fileuploader-action fileuploader-action-remove">\
                            <i class="fa fa-times"></i> Remove </a>\
                        <div class="progress-bar2">${progressBar}<span></span></div>\
                    </td>\
                </tr>',
                item2: '<tr class="fileuploader-item columns">\
                    <td align="center" class="column-order">\
                        <input type="hidden" name="media[id][]" value="${data.id}">\
                        <input type="text" class="form-control input-mini input-priority" name="media[priority][]" value="${data.priority}"> </td>\
                    <td align="center" class="column-thumbnail">\
                        ${image} <span class="fileuploader-action-popup"></span>\
                    </td>\
                    <td class="column-title">\
                        <input type="text" class="form-control" name="media[alt][]" value="${data.alt}"> </td>\
                    <td class="column-actions">\
                        <a href="${data.url}" class="btn btn-sm fileuploader-action fileuploader-action-download" title="${captions.download}"> <i class="fa fa-download"></i> Download </a>\
                        <a href="javascript:;" class="btn btn-delete-file btn-sm red fileuploader-action fileuploader-action-remove" data-ajax="act=delete_record|table=media_libraries|id=${data.id}|config=${data.config}|type=${data.type}">\
                            <i class="fa fa-times"></i> Remove </a>\
                        <div class="progress-bar2">${progressBar}<span></span></div>\
                    </td>\
                </tr>',
                canvasImage: false,
                removeConfirmation: false,
                onItemRemove: function(itemEl, listEl, parentEl, newInputEl, inputEl) {
                    itemEl.children().animate({'opacity': 0}, 200, function() {
                        setTimeout(function() {
                            itemEl.slideUp(200, function() {
                                itemEl.remove();
                            });
                        }, 100);
                    });
                },
                
            }
        });
    }

    var handleSelect2 = function() {
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = $(".select2-data-ajax").data('label');
        var dataUrl = $(".select2-data-ajax").data('url');

        if( $('select[name="data[store_code]"]').length > 0 ){
            dataUrl += '&store='+$('select[name="data[store_code]"]').val();
        }

        // @see https://select2.github.io/examples.html#data-ajax
        function formatRepo(repo) {
            if (repo.loading) return repo.text;
            var markup = "<div>" + repo.title + "</div>";

            return markup;
        }

        function formatRepoSelection(repo) {
            return repo.title || repo.text;
        }

        $(".select2-data-ajax").select2({
            width: "off",
            closeOnSelect: false,
            allowClear: true,
            placeholder: placeholder,
            ajax: {
                url: dataUrl,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, page) {
                    // parse the results into the format expected by Select2.
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data
                    return {
                        results: data.items
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 2,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });
        
    }

    var handleTargetTab = function(){
        var targetTab = localStorage.getItem("targetTab");
        if(targetTab && $('.nav-tabs a[href="'+targetTab+'"]').length > 0){
            $('.nav-tabs a[href="'+targetTab+'"]').tab('show');
        }else{
            $('.nav-tabs a:first').tab('show');
        }
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            localStorage.setItem("targetTab",$(this).attr('href'));
        });
        localStorage.setItem("targetTab","");
    }

    var handleLoadPlace = function(){
        $('.simple-province').each(function(){
            var group = $(this).attr('data-group');
            var simpleProvince = $(this).attr('data-value');
            var simpleDistrict = $('.simple-district[data-group="'+group+'"]').attr('data-value');
            if(simpleProvince !='' && simpleProvince > 0){
                $(this).html(province[simpleProvince].name);
                $('.simple-district[data-group="'+group+'"]').length > 0 ? $('.simple-district[data-group="'+group+'"]').html(district[simpleProvince][simpleDistrict].name) : '';
            }
        });

        $('select.province').each(function(){
            var listProvince = [];
            var group = $(this).attr('data-group');
            var eleProvince = $(this);

            if( typeof eleProvince.attr('data-multiple') !== 'undefined' && eleProvince.attr('data-multiple') !='' ){
                var curProvince = eleProvince.attr('data-multiple');
            }else{
                var curProvince = eleProvince.val();
            }
            
            if( curProvince != null && curProvince.length > 0 ){
                curProvince = curProvince.split(',');
            }else{
                var curProvince = [];
            }

            var eleDistrict = $('select.district[data-group="'+group+'"]');
            var curDistrict = eleDistrict.val();
            $.each( province, function( key, val ) {
                listProvince.push('<option value="'+key+'" '+ ( $.inArray(key,curProvince) >=0 ? 'selected' : '' ) +'>'+val.name+'</option>');
            });
            eleProvince.html(listProvince.join(""));
            if(eleDistrict.length > 0){
                eleProvince.on('change', function(){
                    var province_id = $(this).val();
                    var listDistrict = [];
                    $.each( district[province_id], function( key, val ) {
                        listDistrict.push('<option value="'+key+'" '+ ( key == curDistrict ? 'selected' : '' ) +'>'+val.name+'</option>');
                    });
                    eleDistrict.html(listDistrict.join("")).selectpicker('refresh');
                }).change();
            }
        });
    }

    var handleOther = function(){
        $('.priceFormat').priceFormat({
            limit: 13,
            prefix: '',
            centsSeparator: ',',
            thousandsSeparator: '.',
            centsLimit: 0
        });
        
        $('.colorpicker-component').colorpicker({ color:"#2b3643" });

        $('.form-validation').validationEngine({
            autoHidePrompt: false,
            autoHideDelay: 3000,
        });

        $('.startDate').datepicker({
            startDate: '0'
        });

        $('.btn-import').on('click', function(e){
            e.preventDefault();
            var frm = $(this).parents('form');
            var input = frm.find('input[type="file"]');
            input.trigger('click').change(function(){
                frm.submit();
            });
        });

        $('#generate-code').on('click', function(e){
            e.preventDefault();
            var text = App.generateCode(6);
            $('.generate-result').val(text);
        });

        $('#change-conditions-type').on('click', function(e){
            e.preventDefault();
            var btn = $(this);
            var type = btn.val();
            var input = $('input[name="data[change_conditions_type]"]')
            if(type == 'percentage_discount_from_total_cart'){
                var donvitinh = 'VNĐ';
                type='discount_from_total_cart';
            }else{
                var donvitinh = '%';
                type='percentage_discount_from_total_cart';
            }
            btn.html(donvitinh);
            btn.val(type);
            input.val(type);
            $('input[name="coupon_amount"]').val(0);
        });

        $('input[name="coupon_amount"]').on('blur', function(){
            if( $('#change-conditions-type').val() == 'percentage_discount_from_total_cart' ){
                var coupon = $(this).val().replace(/\./g,'');
                if( parseInt(coupon) > 100){
                    $(this).val(100);
                }
            }
        });
    }

	return{
		init: function(){
            handleCkeditors();
            handleSideBarActive();
			handleGroupCheckable();
			handleStrToSlug();
            handleComment();
            handleAjax();
            handleFileuploader();
            handleSelect2();
            handleTargetTab();
            handleLoadPlace();
            handleOther();
		},
	};

}();
$(document).ready(function() {
	Admin.init();
});