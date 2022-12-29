$.extend(true, $.fn.dataTable.defaults, {
    sDom: "<'row'<'col-md-6 text-left'l><'col-md-6 text-right'f>r>t<'row'<'col-md-6 text-left'i><'col-md-6 text-right'p>>",
    sPaginationType: "bootstrap",
    "fnDrawCallback": function() {
        $(".tip").tooltip({ html: true });
        $(".popnote").popover();
        $('.checkbox').iCheck({ checkboxClass: 'icheckbox_square-blue', radioClass: 'iradio_square-blue', increaseArea: '20%' });
        $("input").addClass('input-xs');
        $("select").addClass('select input-xs');
    }
});
$.extend($.fn.dataTableExt.oStdClasses, { sWrapper: "dataTables_wrapper form-inline" });
$.fn.dataTableExt.oApi.fnPagingInfo = function(a) { return { iStart: a._iDisplayStart, iEnd: a.fnDisplayEnd(), iLength: a._iDisplayLength, iTotal: a.fnRecordsTotal(), iFilteredTotal: a.fnRecordsDisplay(), iPage: a._iDisplayLength === -1 ? 0 : Math.ceil(a._iDisplayStart / a._iDisplayLength), iTotalPages: a._iDisplayLength === -1 ? 0 : Math.ceil(a.fnRecordsDisplay() / a._iDisplayLength) } };
$.extend($.fn.dataTableExt.oPagination, {
    bootstrap: {
        fnInit: function(e, b, d) {
            var a = e.oLanguage.oPaginate;
            var f = function(g) { g.preventDefault(); if (e.oApi._fnPageChange(e, g.data.action)) { d(e) } };
            $(b).append('<ul class="pagination pagination-sm"><li class="prev disabled"><a href="#"> ' + a.sPrevious + '</a></li><li class="next disabled"><a href="#">' + a.sNext + " </a></li></ul>");
            var c = $("a", b);
            $(c[0]).bind("click.DT", { action: "previous" }, f);
            $(c[1]).bind("click.DT", { action: "next" }, f)
        },
        fnUpdate: function(c, k) {
            var l = 5;
            var e = c.oInstance.fnPagingInfo();
            var h = c.aanFeatures.p;
            var g, m, f, d, a, n, b = Math.floor(l / 2);
            if (e.iTotalPages < l) {
                a = 1;
                n = e.iTotalPages
            } else {
                if (e.iPage <= b) {
                    a = 1;
                    n = l
                } else {
                    if (e.iPage >= (e.iTotalPages - b)) {
                        a = e.iTotalPages - l + 1;
                        n = e.iTotalPages
                    } else {
                        a = e.iPage - b + 1;
                        n = a + l - 1
                    }
                }
            }
            for (g = 0, m = h.length; g < m; g++) {
                $("li:gt(0)", h[g]).filter(":not(:last)").remove();
                for (f = a; f <= n; f++) {
                    d = (f == e.iPage + 1) ? 'class="active"' : "";
                    $("<li " + d + '><a href="#">' + f + "</a></li>").insertBefore($("li:last", h[g])[0]).bind("click", function(i) {
                        i.preventDefault();
                        c._iDisplayStart = (parseInt($("a", this).text(), 10) - 1) * e.iLength;
                        k(c)
                    })
                }
                if (e.iPage === 0) { $("li:first", h[g]).addClass("disabled") } else { $("li:first", h[g]).removeClass("disabled") }
                if (e.iPage === e.iTotalPages - 1 || e.iTotalPages === 0) { $("li:last", h[g]).addClass("disabled") } else { $("li:last", h[g]).removeClass("disabled") }
            }
        }
    }
});
if ($.fn.DataTable.TableTools) {
    $.extend(true, $.fn.DataTable.TableTools.classes, { container: "btn-group", buttons: { normal: "btn btn-sm btn-primary", disabled: "disabled" }, collection: { container: "DTTT_dropdown dropdown-menu", buttons: { normal: "", disabled: "disabled" } }, print: { info: "DTTT_print_info modal" }, select: { row: "active" } });
    $.extend(true, $.fn.DataTable.TableTools.DEFAULTS.oTags, { collection: { container: "ul", button: "li", liner: "a" } })
};

jQuery.fn.dataTableExt.oApi.fnSetFilteringDelay = function(oSettings, iDelay) {
    var _that = this;
    if (iDelay === undefined) {
        iDelay = 500;
    }
    this.each(function(i) {
        $.fn.dataTableExt.iApiIndex = i;
        var
            $this = this,
            oTimerId = null,
            sPreviousSearch = null,
            anControl = $('input', _that.fnSettings().aanFeatures.f);

        anControl.unbind('keyup search input').bind('keyup search input', function() {
            var $$this = $this;

            if (sPreviousSearch === null || sPreviousSearch != anControl.val()) {
                window.clearTimeout(oTimerId);
                sPreviousSearch = anControl.val();
                oTimerId = window.setTimeout(function() {
                    $.fn.dataTableExt.iApiIndex = i;
                    _that.fnFilter(anControl.val());
                }, iDelay);
            }
        });
        return this;
    });
    return this;
};

//sDom:"<'row'<'col-md-6 text-left'l><'col-md-6 text-right'<'pull-right dtBtn'T>f>r>t<'row'<'col-md-6 text-left'i><'col-md-6 text-right'p>>",

! function(e) {
    var i = '{preview}\n<div class="input-group {class}">\n   {caption}\n   <div class="input-group-btn">\n       {remove}\n       {upload}\n       {browse}\n   </div>\n</div>',
        t = "{preview}\n{remove}\n{upload}\n{browse}\n",
        n = '<div class="file-preview {class}">\n   <div class="close fileinput-remove text-right"><i class="fa fa-2x">&times;</i></div>\n   <div class="file-preview-thumbnails"></div>\n   <div class="clearfix"></div>   <div class="file-preview-status text-center text-success"></div>\n</div>',
        a = '<div tabindex="-1" class="form-control file-caption {class}">\n   <div class="file-caption-name"></div>\n</div>',
        r = '<div id="{id}" class="modal fade">\n  <div class="modal-dialog modal-lg">\n    <div class="modal-content">\n      <div class="modal-header">\n        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>\n        <h3 class="modal-title">Detailed Preview <small>{title}</small></h3>\n      </div>\n      <div class="modal-body">\n        <textarea class="form-control" style="font-family:Monaco,Consolas,monospace; height: {height}px;" readonly>{body}</textarea>\n      </div>\n    </div>\n  </div>\n</div>\n',
        l = '<div class="file-preview-frame" id="{previewId}">\n   {content}\n</div>\n',
        o = '<div class="file-preview-frame" id="{previewId}">\n   <div class="file-preview-text" title="{caption}">\n       {strText}\n   </div>\n</div>\n',
        s = '<div class="file-preview-frame" id="{previewId}">\n   <div class="file-preview-other">\n       {caption}\n   </div>\n</div>',
        p = function(i, t) { return null === i || void 0 === i || i == [] || "" === i || t && "" === e.trim(i) },
        c = Array.isArray || function(e) { return "[object Array]" === Object.prototype.toString.call(e) },
        d = function(i, t, n) { return p(i) || p(i[t]) ? n : e(i[t]) },
        m = function(e, i) { return "undefined" != typeof e ? e.match("image.*") : i.match(/\.(gif|png|jpe?g)$/i) },
        v = function(e, i) { return "undefined" != typeof e ? e.match("text.*") : i.match(/\.(txt|md|csv|htm|html|php|ini)$/i) },
        u = function() { return Math.round((new Date).getTime() + 100 * Math.random()) },
        f = function() { return window.File && window.FileReader && window.FileList && window.Blob },
        w = window.URL || window.webkitURL,
        g = function(i, t) { this.$element = e(i), f() ? (this.init(t), this.listen()) : this.$element.removeClass("file-loading") };
    g.prototype = {
        constructor: g,
        init: function(e) {
            var n = this;
            n.reader = null, n.showCaption = e.showCaption, n.showPreview = e.showPreview, n.maxFileSize = e.maxFileSize, n.maxFileCount = e.maxFileCount, n.msgSizeTooLarge = e.msgSizeTooLarge, n.msgFilesTooMany = e.msgFilesTooMany, n.msgFileNotFound = e.msgFileNotFound, n.msgFileNotReadable = e.msgFileNotReadable, n.msgFilePreviewAborted = e.msgFilePreviewAborted, n.msgFilePreviewError = e.msgFilePreviewError, n.msgValidationError = e.msgValidationError, n.msgErrorClass = e.msgErrorClass, n.initialDelimiter = e.initialDelimiter, n.initialPreview = e.initialPreview, n.initialCaption = e.initialCaption, n.initialPreviewCount = e.initialPreviewCount, n.initialPreviewContent = e.initialPreviewContent, n.overwriteInitial = e.overwriteInitial, n.showRemove = e.showRemove, n.showUpload = e.showUpload, n.captionClass = e.captionClass, n.previewClass = e.previewClass, n.mainClass = e.mainClass, n.mainTemplate = p(e.mainTemplate) ? n.showCaption ? i : t : e.mainTemplate, n.previewTemplate = n.showPreview ? e.previewTemplate : "", n.previewGenericTemplate = e.previewGenericTemplate, n.previewImageTemplate = e.previewImageTemplate, n.previewTextTemplate = e.previewTextTemplate, n.previewOtherTemplate = e.previewOtherTemplate, n.captionTemplate = e.captionTemplate, n.browseLabel = e.browseLabel, n.browseIcon = e.browseIcon, n.browseClass = e.browseClass, n.removeLabel = e.removeLabel, n.removeIcon = e.removeIcon, n.removeClass = e.removeClass, n.uploadLabel = e.uploadLabel, n.uploadIcon = e.uploadIcon, n.uploadClass = e.uploadClass, n.uploadUrl = e.uploadUrl, n.msgLoading = e.msgLoading, n.msgProgress = e.msgProgress, n.msgSelected = e.msgSelected, n.previewFileType = e.previewFileType, n.wrapTextLength = e.wrapTextLength, n.wrapIndicator = e.wrapIndicator, n.isError = !1, n.isDisabled = n.$element.attr("disabled") || n.$element.attr("readonly"), p(n.$element.attr("id")) && n.$element.attr("id", u()), "undefined" == typeof n.$container ? n.$container = n.createContainer() : n.refreshContainer(), n.$captionContainer = d(e, "elCaptionContainer", n.$container.find(".file-caption")), n.$caption = d(e, "elCaptionText", n.$container.find(".file-caption-name")), n.$previewContainer = d(e, "elPreviewContainer", n.$container.find(".file-preview")), n.$preview = d(e, "elPreviewImage", n.$container.find(".file-preview-thumbnails")), n.$previewStatus = d(e, "elPreviewStatus", n.$container.find(".file-preview-status"));
            var a = n.initialPreview;
            n.initialPreviewCount = c(a) ? a.length : a.length > 0 ? a.split(n.initialDelimiter).length : 0, n.initPreview(), n.original = { preview: n.$preview.html(), caption: n.$caption.html() }, n.options = e, n.$element.removeClass("file-loading")
        },
        listen: function() {
            var i = this,
                t = i.$element,
                n = i.$captionContainer,
                a = i.$btnFile;
            t.on("change", e.proxy(i.change, i)), a.on("click", function() { i.clear(!1), n.focus() }), e(t[0].form).on("reset", e.proxy(i.reset, i)), i.$container.on("click", ".fileinput-remove:not([disabled])", e.proxy(i.clear, i))
        },
        refresh: function(i) {
            var t = this,
                n = arguments.length ? e.extend(t.options, i) : t.options;
            t.init(n)
        },
        initPreview: function() {
            var e = this,
                i = "",
                t = e.initialPreview,
                n = e.initialPreviewCount,
                a = e.initialCaption.length,
                r = "preview-" + u(),
                l = a > 0 ? e.initialCaption : e.msgSelected.replace("{n}", n);
            if (c(t) && n > 0) {
                for (var o = 0; n > o; o++) r += "-" + o, i += e.previewGenericTemplate.replace("{previewId}", r).replace("{content}", t[o]);
                n > 1 && 0 == a && (l = e.msgSelected.replace("{n}", n))
            } else {
                if (!(n > 0)) return a > 0 ? (e.$caption.html(l), void e.$captionContainer.attr("title", l)) : void 0;
                for (var s = t.split(e.initialDelimiter), o = 0; n > o; o++) r += "-" + o, i += e.previewGenericTemplate.replace("{previewId}", r).replace("{content}", s[o]);
                n > 1 && 0 == a && (l = e.msgSelected.replace("{n}", n))
            }
            e.initialPreviewContent = i, e.$preview.html(i), e.$caption.html(l), e.$captionContainer.attr("title", l), e.$container.removeClass("file-input-new")
        },
        clear: function(e) {
            var i = this;
            if (e && e.preventDefault(), i.reader instanceof FileReader && i.reader.abort(), i.$element.val(""), i.resetErrors(!0), e !== !1 && (i.$element.trigger("change"), i.$element.trigger("fileclear")), i.overwriteInitial && (i.initialPreviewCount = 0), i.overwriteInitial || p(i.initialPreviewContent)) {
                i.$preview.html("");
                var t = !i.overwriteInitial && i.initialCaption.length > 0 ? i.original.caption : "";
                i.$caption.html(t), i.$captionContainer.attr("title", ""), i.$container.removeClass("file-input-new").addClass("file-input-new")
            } else i.showFileIcon(), i.$preview.html(i.original.preview), i.$caption.html(i.original.caption), i.$container.removeClass("file-input-new");
            i.hideFileIcon(), i.$element.trigger("filecleared"), i.$captionContainer.focus()
        },
        reset: function() {
            var e = this;
            e.clear(!1), e.$preview.html(e.original.preview), e.$caption.html(e.original.caption), e.$container.find(".fileinput-filename").text(""), e.$element.trigger("filereset"), e.initialPreview.length > 0 && e.$container.removeClass("file-input-new")
        },
        disable: function() {
            var e = this;
            e.isDisabled = !0, e.$element.attr("disabled", "disabled"), e.$container.find(".kv-fileinput-caption").addClass("file-caption-disabled"), e.$container.find(".btn-file, .fileinput-remove, .kv-fileinput-upload").attr("disabled", !0)
        },
        enable: function() {
            var e = this;
            e.isDisabled = !1, e.$element.removeAttr("disabled"), e.$container.find(".kv-fileinput-caption").removeClass("file-caption-disabled"), e.$container.find(".btn-file, .fileinput-remove, .kv-fileinput-upload").removeAttr("disabled")
        },
        hideFileIcon: function() { this.overwriteInitial && this.$captionContainer.find(".kv-caption-icon").hide() },
        showFileIcon: function() { this.$captionContainer.find(".kv-caption-icon").show() },
        resetErrors: function(e) {
            var i = this,
                t = i.$previewContainer.find(".kv-fileinput-error");
            i.isError = !1, e ? t.fadeOut("slow") : t.remove()
        },
        showError: function(e, i, t, n) {
            var a = this,
                r = a.$previewContainer.find(".kv-fileinput-error");
            return p(r.attr("class")) ? a.$previewContainer.append('<div class="kv-fileinput-error ' + a.msgErrorClass + '">' + e + "</div>") : r.html(e), r.hide(), r.fadeIn(800), a.$element.trigger("fileerror", [i, t, n]), a.$element.val(""), !0
        },
        errorHandler: function(e, i) {
            var t = this;
            switch (e.target.error.code) {
                case e.target.error.NOT_FOUND_ERR:
                    t.addError(t.msgFileNotFound.replace("{name}", i));
                    break;
                case e.target.error.NOT_READABLE_ERR:
                    t.addError(t.msgFileNotReadable.replace("{name}", i));
                    break;
                case e.target.error.ABORT_ERR:
                    t.addError(t.msgFilePreviewAborted.replace("{name}", i));
                    break;
                default:
                    t.addError(t.msgFilePreviewError.replace("{name}", i))
            }
        },
        loadImage: function(i, t) {
            var n = this,
                a = e(document.createElement("img"));
            a.attr({ src: w.createObjectURL(i), "class": "file-preview-image", title: t, alt: t, onload: function() { w.revokeObjectURL(a.src) } }), a.width() >= n.$preview.width() && a.attr({ width: "100%", height: "auto" });
            var r = e(document.createElement("div")).append(a);
            return r.html()
        },
        readFiles: function(e) {
            function i(b) {
                if (b >= h) return o.removeClass("loading"), void s.html("");
                var C = g + "-" + b,
                    $ = e[b],
                    F = $.name,
                    T = m($.type, $.name),
                    y = v($.type, $.name),
                    x = ($.size ? $.size : 0) / 1e3;
                if (x = x.toFixed(2), t.maxFileSize > 0 && x > t.maxFileSize) { var I = t.msgSizeTooLarge.replace("{name}", F).replace("{size}", x).replace("{maxSize}", t.maxFileSize); return void(t.isError = t.showError(I, $, C, b)) }
                a.length > 0 && ("any" == d ? T || y : "text" == d ? y : T) && "undefined" != typeof FileReader ? (s.html(p.replace("{index}", b + 1).replace("{files}", h)), o.addClass("loading"), l.onerror = function(e) { t.errorHandler(e, F) }, l.onload = function(e) {
                    var i = "",
                        n = "";
                    if (y) {
                        var l = e.target.result;
                        if (l.length > f) {
                            var o = u(),
                                s = .75 * window.innerHeight,
                                n = r.replace("{id}", o).replace("{title}", F).replace("{body}", l).replace("{height}", s);
                            w = w.replace("{title}", F).replace("{dialog}", "$('#" + o + "').modal('show')"), l = l.substring(0, f - 1) + w
                        }
                        i = t.previewTextTemplate.replace("{previewId}", C).replace("{caption}", F).replace("{strText}", l) + n
                    } else i = t.previewImageTemplate.replace("{previewId}", C).replace("{content}", t.loadImage($, F));
                    a.append("\n" + i)
                }, l.onloadend = function() {
                    var e = c.replace("{index}", b + 1).replace("{files}", h).replace("{percent}", 100).replace("{name}", $.name);
                    setTimeout(function() { s.html(e) }, 1e3), setTimeout(function() { i(b + 1) }, 1500), n.trigger("fileloaded", [$, C, b])
                }, l.onprogress = function(e) {
                    if (e.lengthComputable) {
                        var i = parseInt(e.loaded / e.total * 100, 10),
                            t = c.replace("{index}", b + 1).replace("{files}", h).replace("{percent}", i).replace("{name}", $.name);
                        setTimeout(function() { s.html(t) }, 1e3)
                    }
                }, y ? l.readAsText($) : l.readAsArrayBuffer($)) : (a.append("\n" + t.previewOtherTemplate.replace("{previewId}", C).replace("{caption}", F)), n.trigger("fileloaded", [$, C, b]), setTimeout(i(b + 1), 1e3))
            }
            this.reader = new FileReader;
            var t = this,
                n = t.$element,
                a = t.$preview,
                l = t.reader,
                o = t.$previewContainer,
                s = t.$previewStatus,
                p = t.msgLoading,
                c = t.msgProgress,
                d = (t.msgSelected, t.previewFileType),
                f = parseInt(t.wrapTextLength),
                w = t.wrapIndicator,
                g = "preview-" + u(),
                h = e.length;
            i(0)
        },
        change: function(e) {
            var i, t = this,
                n = t.$element,
                a = n.val().replace(/\\/g, "/").replace(/.*\//, ""),
                r = 0,
                l = t.$preview,
                o = n.get(0).files,
                s = t.msgSelected,
                c = p(o) ? 1 : o.length + t.initialPreviewCount;
            if (t.hideFileIcon(), i = void 0 === e.target.files ? e.target && e.target.value ? [{ name: e.target.value.replace(/^.+\\/, "") }] : [] : e.target.files, 0 !== i.length) {
                t.resetErrors(), l.html(""), t.overwriteInitial || l.html(t.initialPreviewContent);
                var r = i.length;
                if (t.maxFileCount > 0 && r > t.maxFileCount) { var d = t.msgFilesTooMany.replace("{m}", t.maxFileCount).replace("{n}", r); return t.isError = t.showError(d, null, null, null), t.$captionContainer.find(".kv-caption-icon").hide(), t.$caption.html(t.msgValidationError), void t.$container.removeClass("file-input-new") }
                t.readFiles(o), t.reader = null;
                var m = c > 1 ? s.replace("{n}", c) : a;
                t.isError ? (t.$captionContainer.find(".kv-caption-icon").hide(), m = t.msgValidationError) : t.showFileIcon(), t.$caption.html(m), t.$captionContainer.attr("title", m), t.$container.removeClass("file-input-new"), n.trigger("fileselect", [c, a])
            }
        },
        initBrowse: function(e) {
            var i = this;
            i.$btnFile = e.find(".btn-file"), i.$btnFile.append(i.$element)
        },
        createContainer: function() {
            var i = this,
                t = e(document.createElement("span")).attr({ "class": "file-input file-input-new" }).html(i.renderMain());
            return i.$element.before(t), i.initBrowse(t), t
        },
        refreshContainer: function() {
            var e = this,
                i = e.$container;
            i.before(e.$element), i.html(e.renderMain()), e.initBrowse(i)
        },
        renderMain: function() {
            var e = this,
                i = e.previewTemplate.replace("{class}", e.previewClass),
                t = e.isDisabled ? e.captionClass + " file-caption-disabled" : e.captionClass,
                n = e.captionTemplate.replace("{class}", t + " kv-fileinput-caption");
            return e.mainTemplate.replace("{class}", e.mainClass).replace("{preview}", i).replace("{caption}", n).replace("{upload}", e.renderUpload()).replace("{remove}", e.renderRemove()).replace("{browse}", e.renderBrowse())
        },
        renderBrowse: function() {
            var e = this,
                i = e.browseClass + " btn-file",
                t = "";
            return e.isDisabled && (t = " disabled "), '<div class="' + i + '"' + t + "> " + e.browseIcon + e.browseLabel + " </div>"
        },
        renderRemove: function() {
            var e = this,
                i = e.removeClass + " btn-danger fileinput-remove fileinput-remove-button",
                t = "";
            return e.showRemove ? (e.isDisabled && (t = " disabled "), '<button type="button" class="' + i + '"' + t + ">" + e.removeIcon + e.removeLabel + "</button>") : ""
        },
        renderUpload: function() {
            var e = this,
                i = e.uploadClass + " kv-fileinput-upload",
                t = "",
                n = "";
            return e.showUpload ? (e.isDisabled && (n = " disabled "), t = p(e.uploadUrl) ? '<button type="submit" class="' + i + '"' + n + ">" + e.uploadIcon + e.uploadLabel + "</button>" : '<a href="' + e.uploadUrl + '" class="' + e.uploadClass + '"' + n + ">" + e.uploadIcon + e.uploadLabel + "</a>") : ""
        }
    }, e.fn.fileinput = function(i) {
        return this.each(function() {
            var t = e(this),
                n = t.data("fileinput");
            n || t.data("fileinput", n = new g(this, i)), "string" == typeof i && n[i]()
        })
    }, e.fn.fileinput = function(i) {
        var t = Array.apply(null, arguments);
        return t.shift(), this.each(function() {
            var n = e(this),
                a = n.data("fileinput"),
                r = "object" == typeof i && i;
            a || n.data("fileinput", a = new g(this, e.extend({}, e.fn.fileinput.defaults, r, e(this).data()))), "string" == typeof i && a[i].apply(a, t)
        })
    }, e.fn.fileinput.defaults = { showCaption: !0, showPreview: !0, showRemove: !0, showUpload: !0, captionClass: "", previewClass: "", mainClass: "", mainTemplate: null, initialDelimiter: "*$$*", initialPreview: "", initialCaption: "", initialPreviewCount: 0, initialPreviewContent: "", overwriteInitial: !0, previewTemplate: n, previewGenericTemplate: l, previewImageTemplate: l, previewTextTemplate: o, previewOtherTemplate: s, captionTemplate: a, browseLabel: "Browse &hellip;", browseIcon: '<i class="fa fa-folder-open"></i> &nbsp;', browseClass: "btn btn-primary", removeLabel: "Remove", removeIcon: '<i class="fa fa-ban-circle"></i> ', removeClass: "btn btn-default", uploadLabel: "Upload", uploadIcon: '<i class="fa fa-upload"></i> ', uploadClass: "btn btn-default", uploadUrl: null, maxFileSize: 0, maxFileCount: 0, msgSizeTooLarge: 'File "{name}" (<b>{size} KB</b>) exceeds maximum allowed upload size of <b>{maxSize} KB</b>. Please retry your upload!', msgFilesTooMany: "Number of files selected for upload <b>({n})</b> exceeds maximum allowed limit of <b>{m}</b>. Please retry your upload!", msgFileNotFound: 'File "{name}" not found!', msgFileNotReadable: 'File "{name}" is not readable.', msgFilePreviewAborted: 'File preview aborted for "{name}".', msgFilePreviewError: 'An error occurred while reading the file "{name}".', msgValidationError: '<span class="text-danger"><i class="fa fa-exclamation-sign"></i> File Upload Error</span>', msgErrorClass: "file-error-message", msgLoading: "Loading  file {index} of {files} &hellip;", msgProgress: "Loading file {index} of {files} - {name} - {percent}% completed.", msgSelected: "{n} files selected", previewFileType: "image", wrapTextLength: 250, wrapIndicator: ' <span class="wrap-indicator" title="{title}" onclick="{dialog}">[&hellip;]</span>', elCaptionContainer: null, elCaptionText: null, elPreviewContainer: null, elPreviewImage: null, elPreviewStatus: null }, e(document).ready(function() {
        var i = e("input.file[type=file]"),
            t = null != i.attr("type") ? i.length : 0;
        t > 0 && i.fileinput()
    })
}(window.jQuery);

! function(c) {
    function f() { return new Date(Date.UTC.apply(Date, arguments)) }

    function a() { var g = new Date(); return f(g.getUTCFullYear(), g.getUTCMonth(), g.getUTCDate(), g.getUTCHours(), g.getUTCMinutes(), g.getUTCSeconds(), 0) }
    var e = function(h, g) {
        var i = this;
        this.element = c(h);
        this.language = g.language || this.element.data("date-language") || "en";
        this.language = this.language in d ? this.language : "en";
        this.isRTL = d[this.language].rtl || false;
        this.formatType = g.formatType || this.element.data("format-type") || "standard";
        this.format = b.parseFormat(g.format || this.element.data("date-format") || d[this.language].format || b.getDefaultFormat(this.formatType, "input"), this.formatType);
        this.isInline = false;
        this.isVisible = false;
        this.isInput = this.element.is("input");
        this.bootcssVer = this.isInput ? (this.element.is(".form-control") ? 3 : 2) : (this.bootcssVer = this.element.is(".input-group") ? 3 : 2);
        this.component = this.element.is(".date") ? (this.bootcssVer == 3 ? this.element.find(".input-group-addon .fa-th, .input-group-addon .fa-time, .input-group-addon .fa-calendar").parent() : this.element.find(".add-on .icon-th, .add-on .icon-time, .add-on .icon-calendar").parent()) : false;
        this.componentReset = this.element.is(".date") ? (this.bootcssVer == 3 ? this.element.find(".input-group-addon .fa-remove").parent() : this.element.find(".add-on .icon-remove").parent()) : false;
        this.hasInput = this.component && this.element.find("input").length;
        if (this.component && this.component.length === 0) { this.component = false }
        this.linkField = g.linkField || this.element.data("link-field") || false;
        this.linkFormat = b.parseFormat(g.linkFormat || this.element.data("link-format") || b.getDefaultFormat(this.formatType, "link"), this.formatType);
        this.minuteStep = g.minuteStep || this.element.data("minute-step") || 5;
        this.pickerPosition = g.pickerPosition || this.element.data("picker-position") || "bottom-right";
        this.showMeridian = g.showMeridian || this.element.data("show-meridian") || false;
        this.initialDate = g.initialDate || new Date();
        this._attachEvents();
        this.formatViewType = "datetime";
        if ("formatViewType" in g) { this.formatViewType = g.formatViewType } else { if ("formatViewType" in this.element.data()) { this.formatViewType = this.element.data("formatViewType") } }
        this.minView = 0;
        if ("minView" in g) { this.minView = g.minView } else { if ("minView" in this.element.data()) { this.minView = this.element.data("min-view") } }
        this.minView = b.convertViewMode(this.minView);
        this.maxView = b.modes.length - 1;
        if ("maxView" in g) { this.maxView = g.maxView } else { if ("maxView" in this.element.data()) { this.maxView = this.element.data("max-view") } }
        this.maxView = b.convertViewMode(this.maxView);
        this.wheelViewModeNavigation = false;
        if ("wheelViewModeNavigation" in g) { this.wheelViewModeNavigation = g.wheelViewModeNavigation } else { if ("wheelViewModeNavigation" in this.element.data()) { this.wheelViewModeNavigation = this.element.data("view-mode-wheel-navigation") } }
        this.wheelViewModeNavigationInverseDirection = false;
        if ("wheelViewModeNavigationInverseDirection" in g) { this.wheelViewModeNavigationInverseDirection = g.wheelViewModeNavigationInverseDirection } else { if ("wheelViewModeNavigationInverseDirection" in this.element.data()) { this.wheelViewModeNavigationInverseDirection = this.element.data("view-mode-wheel-navigation-inverse-dir") } }
        this.wheelViewModeNavigationDelay = 100;
        if ("wheelViewModeNavigationDelay" in g) { this.wheelViewModeNavigationDelay = g.wheelViewModeNavigationDelay } else { if ("wheelViewModeNavigationDelay" in this.element.data()) { this.wheelViewModeNavigationDelay = this.element.data("view-mode-wheel-navigation-delay") } }
        this.startViewMode = 2;
        if ("startView" in g) { this.startViewMode = g.startView } else { if ("startView" in this.element.data()) { this.startViewMode = this.element.data("start-view") } }
        this.startViewMode = b.convertViewMode(this.startViewMode);
        this.viewMode = this.startViewMode;
        this.viewSelect = this.minView;
        if ("viewSelect" in g) { this.viewSelect = g.viewSelect } else { if ("viewSelect" in this.element.data()) { this.viewSelect = this.element.data("view-select") } }
        this.viewSelect = b.convertViewMode(this.viewSelect);
        this.forceParse = true;
        if ("forceParse" in g) { this.forceParse = g.forceParse } else { if ("dateForceParse" in this.element.data()) { this.forceParse = this.element.data("date-force-parse") } }
        this.picker = c((this.bootcssVer == 3) ? b.templateV3 : b.template).appendTo(this.isInline ? this.element : "body").on({ click: c.proxy(this.click, this), mousedown: c.proxy(this.mousedown, this) });
        if (this.wheelViewModeNavigation) { if (c.fn.mousewheel) { this.picker.on({ mousewheel: c.proxy(this.mousewheel, this) }) } else { console.log("Mouse Wheel event is not supported. Please include the jQuery Mouse Wheel plugin before enabling this option") } }
        if (this.isInline) { this.picker.addClass("datetimepicker-inline") } else { this.picker.addClass("datetimepicker-dropdown-" + this.pickerPosition + " dropdown-menu") }
        if (this.isRTL) { this.picker.addClass("datetimepicker-rtl"); if (this.bootcssVer == 3) { this.picker.find(".prev span, .next span").toggleClass("fa-arrow-left fa-arrow-right") } else { this.picker.find(".prev i, .next i").toggleClass("icon-arrow-left icon-arrow-right") } }
        c(document).on("mousedown", function(j) { if (c(j.target).closest(".datetimepicker").length === 0) { i.hide() } });
        this.autoclose = false;
        if ("autoclose" in g) { this.autoclose = g.autoclose } else { if ("dateAutoclose" in this.element.data()) { this.autoclose = this.element.data("date-autoclose") } }
        this.keyboardNavigation = true;
        if ("keyboardNavigation" in g) { this.keyboardNavigation = g.keyboardNavigation } else { if ("dateKeyboardNavigation" in this.element.data()) { this.keyboardNavigation = this.element.data("date-keyboard-navigation") } }
        this.todayBtn = (g.todayBtn || this.element.data("date-today-btn") || false);
        this.todayHighlight = (g.todayHighlight || this.element.data("date-today-highlight") || false);
        this.weekStart = ((g.weekStart || this.element.data("date-weekstart") || d[this.language].weekStart || 0) % 7);
        this.weekEnd = ((this.weekStart + 6) % 7);
        this.startDate = -Infinity;
        this.endDate = Infinity;
        this.daysOfWeekDisabled = [];
        this.setStartDate(g.startDate || this.element.data("date-startdate"));
        this.setEndDate(g.endDate || this.element.data("date-enddate"));
        this.setDaysOfWeekDisabled(g.daysOfWeekDisabled || this.element.data("date-days-of-week-disabled"));
        this.fillDow();
        this.fillMonths();
        this.update();
        this.showMode();
        if (this.isInline) { this.show() }
    };
    e.prototype = {
        constructor: e,
        _events: [],
        _attachEvents: function() {
            this._detachEvents();
            if (this.isInput) {
                this._events = [
                    [this.element, { focus: c.proxy(this.show, this), keyup: c.proxy(this.update, this), keydown: c.proxy(this.keydown, this) }]
                ]
            } else {
                if (this.component && this.hasInput) {
                    this._events = [
                        [this.element.find("input"), { focus: c.proxy(this.show, this), keyup: c.proxy(this.update, this), keydown: c.proxy(this.keydown, this) }],
                        [this.component, { click: c.proxy(this.show, this) }]
                    ];
                    if (this.componentReset) { this._events.push([this.componentReset, { click: c.proxy(this.reset, this) }]) }
                } else {
                    if (this.element.is("div")) { this.isInline = true } else {
                        this._events = [
                            [this.element, { click: c.proxy(this.show, this) }]
                        ]
                    }
                }
            }
            for (var g = 0, h, j; g < this._events.length; g++) {
                h = this._events[g][0];
                j = this._events[g][1];
                h.on(j)
            }
        },
        _detachEvents: function() {
            for (var g = 0, h, j; g < this._events.length; g++) {
                h = this._events[g][0];
                j = this._events[g][1];
                h.off(j)
            }
            this._events = []
        },
        show: function(g) {
            this.picker.show();
            this.height = this.component ? this.component.outerHeight() : this.element.outerHeight();
            if (this.forceParse) { this.update() }
            this.place();
            c(window).on("resize", c.proxy(this.place, this));
            if (g) {
                g.stopPropagation();
                g.preventDefault()
            }
            this.isVisible = true;
            this.element.trigger({ type: "show", date: this.date })
        },
        hide: function(g) {
            if (!this.isVisible) { return }
            if (this.isInline) { return }
            this.picker.hide();
            c(window).off("resize", this.place);
            this.viewMode = this.startViewMode;
            this.showMode();
            if (!this.isInput) { c(document).off("mousedown", this.hide) }
            if (this.forceParse && (this.isInput && this.element.val() || this.hasInput && this.element.find("input").val())) { this.setValue() }
            this.isVisible = false;
            this.element.trigger({ type: "hide", date: this.date })
        },
        remove: function() {
            this._detachEvents();
            this.picker.remove();
            delete this.picker;
            delete this.element.data().datetimepicker
        },
        getDate: function() { var g = this.getUTCDate(); return new Date(g.getTime() + (g.getTimezoneOffset() * 60000)) },
        getUTCDate: function() { return this.date },
        setDate: function(g) { this.setUTCDate(new Date(g.getTime() - (g.getTimezoneOffset() * 60000))) },
        setUTCDate: function(g) {
            if (g >= this.startDate && g <= this.endDate) {
                this.date = g;
                this.setValue();
                this.viewDate = this.date;
                this.fill()
            } else { this.element.trigger({ type: "outOfRange", date: g, startDate: this.startDate, endDate: this.endDate }) }
        },
        setFormat: function(h) { this.format = b.parseFormat(h, this.formatType); var g; if (this.isInput) { g = this.element } else { if (this.component) { g = this.element.find("input") } } if (g && g.val()) { this.setValue() } },
        setValue: function() {
            var g = this.getFormattedDate();
            if (!this.isInput) {
                if (this.component) { this.element.find("input").val(g) }
                this.element.data("date", g)
            } else { this.element.val(g) }
            if (this.linkField) { c("#" + this.linkField).val(this.getFormattedDate(this.linkFormat)) }
        },
        getFormattedDate: function(g) { if (g == undefined) { g = this.format } return b.formatDate(this.date, g, this.language, this.formatType) },
        setStartDate: function(g) {
            this.startDate = g || -Infinity;
            if (this.startDate !== -Infinity) { this.startDate = b.parseDate(this.startDate, this.format, this.language, this.formatType) }
            this.update();
            this.updateNavArrows()
        },
        setEndDate: function(g) {
            this.endDate = g || Infinity;
            if (this.endDate !== Infinity) { this.endDate = b.parseDate(this.endDate, this.format, this.language, this.formatType) }
            this.update();
            this.updateNavArrows()
        },
        setDaysOfWeekDisabled: function(g) {
            this.daysOfWeekDisabled = g || [];
            if (!c.isArray(this.daysOfWeekDisabled)) { this.daysOfWeekDisabled = this.daysOfWeekDisabled.split(/,\s*/) }
            this.daysOfWeekDisabled = c.map(this.daysOfWeekDisabled, function(h) { return parseInt(h, 10) });
            this.update();
            this.updateNavArrows()
        },
        place: function() {
            if (this.isInline) { return }
            var g = 0;
            c("div").each(function() { var l = parseInt(c(this).css("zIndex"), 10); if (l > g) { g = l } });
            var k = g + 10;
            var j, i, h;
            if (this.component) {
                j = this.component.offset();
                h = j.left;
                if (this.pickerPosition == "bottom-left" || this.pickerPosition == "top-left") { h += this.component.outerWidth() - this.picker.outerWidth() }
            } else {
                j = this.element.offset();
                h = j.left
            }
            if (this.pickerPosition == "top-left" || this.pickerPosition == "top-right") { i = j.top - this.picker.outerHeight() } else { i = j.top + this.height }
            this.picker.css({ top: i, left: h, zIndex: k })
        },
        update: function() {
            var g, h = false;
            if (arguments && arguments.length && (typeof arguments[0] === "string" || arguments[0] instanceof Date)) {
                g = arguments[0];
                h = true
            } else { g = this.element.data("date") || (this.isInput ? this.element.val() : this.element.find("input").val()) || this.initialDate; if (typeof g == "string" || g instanceof String) { g = g.replace(/^\s+|\s+$/g, "") } }
            if (!g) {
                g = new Date();
                h = false
            }
            this.date = b.parseDate(g, this.format, this.language, this.formatType);
            if (h) { this.setValue() }
            if (this.date < this.startDate) { this.viewDate = new Date(this.startDate) } else { if (this.date > this.endDate) { this.viewDate = new Date(this.endDate) } else { this.viewDate = new Date(this.date) } }
            this.fill()
        },
        fillDow: function() {
            var g = this.weekStart,
                h = "<tr>";
            while (g < this.weekStart + 7) { h += '<th class="dow">' + d[this.language].daysMin[(g++) % 7] + "</th>" }
            h += "</tr>";
            this.picker.find(".datetimepicker-days thead").append(h)
        },
        fillMonths: function() {
            var h = "",
                g = 0;
            while (g < 12) { h += '<span class="month">' + d[this.language].monthsShort[g++] + "</span>" }
            this.picker.find(".datetimepicker-months td").html(h)
        },
        fill: function() {
            if (this.date == null || this.viewDate == null) { return }
            var E = new Date(this.viewDate),
                q = E.getUTCFullYear(),
                F = E.getUTCMonth(),
                j = E.getUTCDate(),
                z = E.getUTCHours(),
                u = E.getUTCMinutes(),
                v = this.startDate !== -Infinity ? this.startDate.getUTCFullYear() : -Infinity,
                A = this.startDate !== -Infinity ? this.startDate.getUTCMonth() : -Infinity,
                l = this.endDate !== Infinity ? this.endDate.getUTCFullYear() : Infinity,
                w = this.endDate !== Infinity ? this.endDate.getUTCMonth() : Infinity,
                n = (new f(this.date.getUTCFullYear(), this.date.getUTCMonth(), this.date.getUTCDate())).valueOf(),
                D = new Date();
            this.picker.find(".datetimepicker-days thead th:eq(1)").text(d[this.language].months[F] + " " + q);
            if (this.formatViewType == "time") {
                var B = z % 12 ? z % 12 : 12;
                var h = (B < 10 ? "0" : "") + B;
                var m = (u < 10 ? "0" : "") + u;
                var H = d[this.language].meridiem[z < 12 ? 0 : 1];
                this.picker.find(".datetimepicker-hours thead th:eq(1)").text(h + ":" + m + " " + H.toUpperCase());
                this.picker.find(".datetimepicker-minutes thead th:eq(1)").text(h + ":" + m + " " + H.toUpperCase())
            } else {
                this.picker.find(".datetimepicker-hours thead th:eq(1)").text(j + " " + d[this.language].months[F] + " " + q);
                this.picker.find(".datetimepicker-minutes thead th:eq(1)").text(j + " " + d[this.language].months[F] + " " + q)
            }
            this.picker.find("tfoot th.today").text(d[this.language].today).toggle(this.todayBtn !== false);
            this.updateNavArrows();
            this.fillMonths();
            var I = f(q, F - 1, 28, 0, 0, 0, 0),
                y = b.getDaysInMonth(I.getUTCFullYear(), I.getUTCMonth());
            I.setUTCDate(y);
            I.setUTCDate(y - (I.getUTCDay() - this.weekStart + 7) % 7);
            var g = new Date(I);
            g.setUTCDate(g.getUTCDate() + 42);
            g = g.valueOf();
            var o = [];
            var r;
            while (I.valueOf() < g) {
                if (I.getUTCDay() == this.weekStart) { o.push("<tr>") }
                r = "";
                if (I.getUTCFullYear() < q || (I.getUTCFullYear() == q && I.getUTCMonth() < F)) { r += " old" } else { if (I.getUTCFullYear() > q || (I.getUTCFullYear() == q && I.getUTCMonth() > F)) { r += " new" } }
                if (this.todayHighlight && I.getUTCFullYear() == D.getFullYear() && I.getUTCMonth() == D.getMonth() && I.getUTCDate() == D.getDate()) { r += " today" }
                if (I.valueOf() == n) { r += " active" }
                if ((I.valueOf() + 86400000) <= this.startDate || I.valueOf() > this.endDate || c.inArray(I.getUTCDay(), this.daysOfWeekDisabled) !== -1) { r += " disabled" }
                o.push('<td class="day' + r + '">' + I.getUTCDate() + "</td>");
                if (I.getUTCDay() == this.weekEnd) { o.push("</tr>") }
                I.setUTCDate(I.getUTCDate() + 1)
            }
            this.picker.find(".datetimepicker-days tbody").empty().append(o.join(""));
            o = [];
            var s = "",
                C = "",
                p = "";
            for (var x = 0; x < 24; x++) {
                var t = f(q, F, j, x);
                r = "";
                if ((t.valueOf() + 3600000) <= this.startDate || t.valueOf() > this.endDate) { r += " disabled" } else { if (z == x) { r += " active" } }
                if (this.showMeridian && d[this.language].meridiem.length == 2) {
                    C = (x < 12 ? d[this.language].meridiem[0] : d[this.language].meridiem[1]);
                    if (C != p) {
                        if (p != "") { o.push("</fieldset>") }
                        o.push('<fieldset class="hour"><legend>' + C.toUpperCase() + "</legend>")
                    }
                    p = C;
                    s = (x % 12 ? x % 12 : 12);
                    o.push('<span class="hour' + r + " hour_" + (x < 12 ? "am" : "pm") + '">' + s + "</span>");
                    if (x == 23) { o.push("</fieldset>") }
                } else {
                    s = x + ":00";
                    o.push('<span class="hour' + r + '">' + s + "</span>")
                }
            }
            this.picker.find(".datetimepicker-hours td").html(o.join(""));
            o = [];
            s = "", C = "", p = "";
            for (var x = 0; x < 60; x += this.minuteStep) {
                var t = f(q, F, j, z, x, 0);
                r = "";
                if (t.valueOf() < this.startDate || t.valueOf() > this.endDate) { r += " disabled" } else { if (Math.floor(u / this.minuteStep) == Math.floor(x / this.minuteStep)) { r += " active" } }
                if (this.showMeridian && d[this.language].meridiem.length == 2) {
                    C = (z < 12 ? d[this.language].meridiem[0] : d[this.language].meridiem[1]);
                    if (C != p) {
                        if (p != "") { o.push("</fieldset>") }
                        o.push('<fieldset class="minute"><legend>' + C.toUpperCase() + "</legend>")
                    }
                    p = C;
                    s = (z % 12 ? z % 12 : 12);
                    o.push('<span class="minute' + r + '">' + s + ":" + (x < 10 ? "0" + x : x) + "</span>");
                    if (x == 59) { o.push("</fieldset>") }
                } else {
                    s = x + ":00";
                    o.push('<span class="minute' + r + '">' + z + ":" + (x < 10 ? "0" + x : x) + "</span>")
                }
            }
            this.picker.find(".datetimepicker-minutes td").html(o.join(""));
            var J = this.date.getUTCFullYear();
            var k = this.picker.find(".datetimepicker-months").find("th:eq(1)").text(q).end().find("span").removeClass("active");
            if (J == q) { k.eq(this.date.getUTCMonth()).addClass("active") }
            if (q < v || q > l) { k.addClass("disabled") }
            if (q == v) { k.slice(0, A).addClass("disabled") }
            if (q == l) { k.slice(w + 1).addClass("disabled") }
            o = "";
            q = parseInt(q / 10, 10) * 10;
            var G = this.picker.find(".datetimepicker-years").find("th:eq(1)").text(q + "-" + (q + 9)).end().find("td");
            q -= 1;
            for (var x = -1; x < 11; x++) {
                o += '<span class="year' + (x == -1 || x == 10 ? " old" : "") + (J == q ? " active" : "") + (q < v || q > l ? " disabled" : "") + '">' + q + "</span>";
                q += 1
            }
            G.html(o);
            this.place()
        },
        updateNavArrows: function() {
            var k = new Date(this.viewDate),
                i = k.getUTCFullYear(),
                j = k.getUTCMonth(),
                h = k.getUTCDate(),
                g = k.getUTCHours();
            switch (this.viewMode) {
                case 0:
                    if (this.startDate !== -Infinity && i <= this.startDate.getUTCFullYear() && j <= this.startDate.getUTCMonth() && h <= this.startDate.getUTCDate() && g <= this.startDate.getUTCHours()) { this.picker.find(".prev").css({ visibility: "hidden" }) } else { this.picker.find(".prev").css({ visibility: "visible" }) }
                    if (this.endDate !== Infinity && i >= this.endDate.getUTCFullYear() && j >= this.endDate.getUTCMonth() && h >= this.endDate.getUTCDate() && g >= this.endDate.getUTCHours()) { this.picker.find(".next").css({ visibility: "hidden" }) } else { this.picker.find(".next").css({ visibility: "visible" }) }
                    break;
                case 1:
                    if (this.startDate !== -Infinity && i <= this.startDate.getUTCFullYear() && j <= this.startDate.getUTCMonth() && h <= this.startDate.getUTCDate()) { this.picker.find(".prev").css({ visibility: "hidden" }) } else { this.picker.find(".prev").css({ visibility: "visible" }) }
                    if (this.endDate !== Infinity && i >= this.endDate.getUTCFullYear() && j >= this.endDate.getUTCMonth() && h >= this.endDate.getUTCDate()) { this.picker.find(".next").css({ visibility: "hidden" }) } else { this.picker.find(".next").css({ visibility: "visible" }) }
                    break;
                case 2:
                    if (this.startDate !== -Infinity && i <= this.startDate.getUTCFullYear() && j <= this.startDate.getUTCMonth()) { this.picker.find(".prev").css({ visibility: "hidden" }) } else { this.picker.find(".prev").css({ visibility: "visible" }) }
                    if (this.endDate !== Infinity && i >= this.endDate.getUTCFullYear() && j >= this.endDate.getUTCMonth()) { this.picker.find(".next").css({ visibility: "hidden" }) } else { this.picker.find(".next").css({ visibility: "visible" }) }
                    break;
                case 3:
                case 4:
                    if (this.startDate !== -Infinity && i <= this.startDate.getUTCFullYear()) { this.picker.find(".prev").css({ visibility: "hidden" }) } else { this.picker.find(".prev").css({ visibility: "visible" }) }
                    if (this.endDate !== Infinity && i >= this.endDate.getUTCFullYear()) { this.picker.find(".next").css({ visibility: "hidden" }) } else { this.picker.find(".next").css({ visibility: "visible" }) }
                    break
            }
        },
        mousewheel: function(h) {
            h.preventDefault();
            h.stopPropagation();
            if (this.wheelPause) { return }
            this.wheelPause = true;
            var g = h.originalEvent;
            var j = g.wheelDelta;
            var i = j > 0 ? 1 : (j === 0) ? 0 : -1;
            if (this.wheelViewModeNavigationInverseDirection) { i = -i }
            this.showMode(i);
            setTimeout(c.proxy(function() { this.wheelPause = false }, this), this.wheelViewModeNavigationDelay)
        },
        click: function(k) {
            k.stopPropagation();
            k.preventDefault();
            var l = c(k.target).closest("span, td, th, legend");
            if (l.length == 1) {
                if (l.is(".disabled")) { this.element.trigger({ type: "outOfRange", date: this.viewDate, startDate: this.startDate, endDate: this.endDate }); return }
                switch (l[0].nodeName.toLowerCase()) {
                    case "th":
                        switch (l[0].className) {
                            case "switch":
                                this.showMode(1);
                                break;
                            case "prev":
                            case "next":
                                var g = b.modes[this.viewMode].navStep * (l[0].className == "prev" ? -1 : 1);
                                switch (this.viewMode) {
                                    case 0:
                                        this.viewDate = this.moveHour(this.viewDate, g);
                                        break;
                                    case 1:
                                        this.viewDate = this.moveDate(this.viewDate, g);
                                        break;
                                    case 2:
                                        this.viewDate = this.moveMonth(this.viewDate, g);
                                        break;
                                    case 3:
                                    case 4:
                                        this.viewDate = this.moveYear(this.viewDate, g);
                                        break
                                }
                                this.fill();
                                break;
                            case "today":
                                var h = new Date();
                                h = f(h.getFullYear(), h.getMonth(), h.getDate(), h.getHours(), h.getMinutes(), h.getSeconds(), 0);
                                if (h < this.startDate) { h = this.startDate } else { if (h > this.endDate) { h = this.endDate } }
                                this.viewMode = this.startViewMode;
                                this.showMode(0);
                                this._setDate(h);
                                this.fill();
                                if (this.autoclose) { this.hide() }
                                break
                        }
                        break;
                    case "span":
                        if (!l.is(".disabled")) {
                            var n = this.viewDate.getUTCFullYear(),
                                m = this.viewDate.getUTCMonth(),
                                o = this.viewDate.getUTCDate(),
                                p = this.viewDate.getUTCHours(),
                                i = this.viewDate.getUTCMinutes(),
                                q = this.viewDate.getUTCSeconds();
                            if (l.is(".month")) {
                                this.viewDate.setUTCDate(1);
                                m = l.parent().find("span").index(l);
                                o = this.viewDate.getUTCDate();
                                this.viewDate.setUTCMonth(m);
                                this.element.trigger({ type: "changeMonth", date: this.viewDate });
                                if (this.viewSelect >= 3) { this._setDate(f(n, m, o, p, i, q, 0)) }
                            } else {
                                if (l.is(".year")) {
                                    this.viewDate.setUTCDate(1);
                                    n = parseInt(l.text(), 10) || 0;
                                    this.viewDate.setUTCFullYear(n);
                                    this.element.trigger({ type: "changeYear", date: this.viewDate });
                                    if (this.viewSelect >= 4) { this._setDate(f(n, m, o, p, i, q, 0)) }
                                } else {
                                    if (l.is(".hour")) {
                                        p = parseInt(l.text(), 10) || 0;
                                        if (l.hasClass("hour_am") || l.hasClass("hour_pm")) { if (p == 12 && l.hasClass("hour_am")) { p = 0 } else { if (p != 12 && l.hasClass("hour_pm")) { p += 12 } } }
                                        this.viewDate.setUTCHours(p);
                                        this.element.trigger({ type: "changeHour", date: this.viewDate });
                                        if (this.viewSelect >= 1) { this._setDate(f(n, m, o, p, i, q, 0)) }
                                    } else {
                                        if (l.is(".minute")) {
                                            i = parseInt(l.text().substr(l.text().indexOf(":") + 1), 10) || 0;
                                            this.viewDate.setUTCMinutes(i);
                                            this.element.trigger({ type: "changeMinute", date: this.viewDate });
                                            if (this.viewSelect >= 0) { this._setDate(f(n, m, o, p, i, q, 0)) }
                                        }
                                    }
                                }
                            }
                            if (this.viewMode != 0) {
                                var j = this.viewMode;
                                this.showMode(-1);
                                this.fill();
                                if (j == this.viewMode && this.autoclose) { this.hide() }
                            } else { this.fill(); if (this.autoclose) { this.hide() } }
                        }
                        break;
                    case "td":
                        if (l.is(".day") && !l.is(".disabled")) {
                            var o = parseInt(l.text(), 10) || 1;
                            var n = this.viewDate.getUTCFullYear(),
                                m = this.viewDate.getUTCMonth(),
                                p = this.viewDate.getUTCHours(),
                                i = this.viewDate.getUTCMinutes(),
                                q = this.viewDate.getUTCSeconds();
                            if (l.is(".old")) {
                                if (m === 0) {
                                    m = 11;
                                    n -= 1
                                } else { m -= 1 }
                            } else {
                                if (l.is(".new")) {
                                    if (m == 11) {
                                        m = 0;
                                        n += 1
                                    } else { m += 1 }
                                }
                            }
                            this.viewDate.setUTCFullYear(n);
                            this.viewDate.setUTCMonth(m, o);
                            this.element.trigger({ type: "changeDay", date: this.viewDate });
                            if (this.viewSelect >= 2) { this._setDate(f(n, m, o, p, i, q, 0)) }
                        }
                        var j = this.viewMode;
                        this.showMode(-1);
                        this.fill();
                        if (j == this.viewMode && this.autoclose) { this.hide() }
                        break
                }
            }
        },
        _setDate: function(g, i) {
            if (!i || i == "date") { this.date = g }
            if (!i || i == "view") { this.viewDate = g }
            this.fill();
            this.setValue();
            var h;
            if (this.isInput) { h = this.element } else { if (this.component) { h = this.element.find("input") } }
            if (h) { h.change(); if (this.autoclose && (!i || i == "date")) {} }
            this.element.trigger({ type: "changeDate", date: this.date })
        },
        moveMinute: function(h, g) {
            if (!g) { return h }
            var i = new Date(h.valueOf());
            i.setUTCMinutes(i.getUTCMinutes() + (g * this.minuteStep));
            return i
        },
        moveHour: function(h, g) {
            if (!g) { return h }
            var i = new Date(h.valueOf());
            i.setUTCHours(i.getUTCHours() + g);
            return i
        },
        moveDate: function(h, g) {
            if (!g) { return h }
            var i = new Date(h.valueOf());
            i.setUTCDate(i.getUTCDate() + g);
            return i
        },
        moveMonth: function(g, h) {
            if (!h) { return g }
            var l = new Date(g.valueOf()),
                p = l.getUTCDate(),
                m = l.getUTCMonth(),
                k = Math.abs(h),
                o, n;
            h = h > 0 ? 1 : -1;
            if (k == 1) {
                n = h == -1 ? function() { return l.getUTCMonth() == m } : function() { return l.getUTCMonth() != o };
                o = m + h;
                l.setUTCMonth(o);
                if (o < 0 || o > 11) { o = (o + 12) % 12 }
            } else {
                for (var j = 0; j < k; j++) { l = this.moveMonth(l, h) }
                o = l.getUTCMonth();
                l.setUTCDate(p);
                n = function() { return o != l.getUTCMonth() }
            }
            while (n()) {
                l.setUTCDate(--p);
                l.setUTCMonth(o)
            }
            return l
        },
        moveYear: function(h, g) { return this.moveMonth(h, g * 12) },
        dateWithinRange: function(g) { return g >= this.startDate && g <= this.endDate },
        keydown: function(k) {
            if (this.picker.is(":not(:visible)")) { if (k.keyCode == 27) { this.show() } return }
            var m = false,
                h, n, l, o, g;
            switch (k.keyCode) {
                case 27:
                    this.hide();
                    k.preventDefault();
                    break;
                case 37:
                case 39:
                    if (!this.keyboardNavigation) { break }
                    h = k.keyCode == 37 ? -1 : 1;
                    viewMode = this.viewMode;
                    if (k.ctrlKey) { viewMode += 2 } else { if (k.shiftKey) { viewMode += 1 } }
                    if (viewMode == 4) {
                        o = this.moveYear(this.date, h);
                        g = this.moveYear(this.viewDate, h)
                    } else {
                        if (viewMode == 3) {
                            o = this.moveMonth(this.date, h);
                            g = this.moveMonth(this.viewDate, h)
                        } else {
                            if (viewMode == 2) {
                                o = this.moveDate(this.date, h);
                                g = this.moveDate(this.viewDate, h)
                            } else {
                                if (viewMode == 1) {
                                    o = this.moveHour(this.date, h);
                                    g = this.moveHour(this.viewDate, h)
                                } else {
                                    if (viewMode == 0) {
                                        o = this.moveMinute(this.date, h);
                                        g = this.moveMinute(this.viewDate, h)
                                    }
                                }
                            }
                        }
                    }
                    if (this.dateWithinRange(o)) {
                        this.date = o;
                        this.viewDate = g;
                        this.setValue();
                        this.update();
                        k.preventDefault();
                        m = true
                    }
                    break;
                case 38:
                case 40:
                    if (!this.keyboardNavigation) { break }
                    h = k.keyCode == 38 ? -1 : 1;
                    viewMode = this.viewMode;
                    if (k.ctrlKey) { viewMode += 2 } else { if (k.shiftKey) { viewMode += 1 } }
                    if (viewMode == 4) {
                        o = this.moveYear(this.date, h);
                        g = this.moveYear(this.viewDate, h)
                    } else {
                        if (viewMode == 3) {
                            o = this.moveMonth(this.date, h);
                            g = this.moveMonth(this.viewDate, h)
                        } else {
                            if (viewMode == 2) {
                                o = this.moveDate(this.date, h * 7);
                                g = this.moveDate(this.viewDate, h * 7)
                            } else {
                                if (viewMode == 1) {
                                    if (this.showMeridian) {
                                        o = this.moveHour(this.date, h * 6);
                                        g = this.moveHour(this.viewDate, h * 6)
                                    } else {
                                        o = this.moveHour(this.date, h * 4);
                                        g = this.moveHour(this.viewDate, h * 4)
                                    }
                                } else {
                                    if (viewMode == 0) {
                                        o = this.moveMinute(this.date, h * 4);
                                        g = this.moveMinute(this.viewDate, h * 4)
                                    }
                                }
                            }
                        }
                    }
                    if (this.dateWithinRange(o)) {
                        this.date = o;
                        this.viewDate = g;
                        this.setValue();
                        this.update();
                        k.preventDefault();
                        m = true
                    }
                    break;
                case 13:
                    if (this.viewMode != 0) {
                        var j = this.viewMode;
                        this.showMode(-1);
                        this.fill();
                        if (j == this.viewMode && this.autoclose) { this.hide() }
                    } else { this.fill(); if (this.autoclose) { this.hide() } }
                    k.preventDefault();
                    break;
                case 9:
                    this.hide();
                    break
            }
            if (m) {
                var i;
                if (this.isInput) { i = this.element } else { if (this.component) { i = this.element.find("input") } }
                if (i) { i.change() }
                this.element.trigger({ type: "changeDate", date: this.date })
            }
        },
        showMode: function(g) {
            if (g) {
                var h = Math.max(0, Math.min(b.modes.length - 1, this.viewMode + g));
                if (h >= this.minView && h <= this.maxView) {
                    this.element.trigger({ type: "changeMode", date: this.viewDate, oldViewMode: this.viewMode, newViewMode: h });
                    this.viewMode = h
                }
            }
            this.picker.find(">div").hide().filter(".datetimepicker-" + b.modes[this.viewMode].clsName).css("display", "block");
            this.updateNavArrows()
        },
        reset: function(g) { this._setDate(null, "date") }
    };
    c.fn.datetimepicker = function(h) {
        var g = Array.apply(null, arguments);
        g.shift();
        return this.each(function() {
            var k = c(this),
                j = k.data("datetimepicker"),
                i = typeof h == "object" && h;
            if (!j) { k.data("datetimepicker", (j = new e(this, c.extend({}, c.fn.datetimepicker.defaults, i)))) }
            if (typeof h == "string" && typeof j[h] == "function") { j[h].apply(j, g) }
        })
    };
    c.fn.datetimepicker.defaults = {};
    c.fn.datetimepicker.Constructor = e;
    var d = c.fn.datetimepicker.dates = { en: { days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"], daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"], daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"], months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"], monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], meridiem: ["am", "pm"], suffix: ["st", "nd", "rd", "th"], today: "Today" } };
    var b = {
        modes: [{ clsName: "minutes", navFnc: "Hours", navStep: 1 }, { clsName: "hours", navFnc: "Date", navStep: 1 }, { clsName: "days", navFnc: "Month", navStep: 1 }, { clsName: "months", navFnc: "FullYear", navStep: 1 }, { clsName: "years", navFnc: "FullYear", navStep: 10 }],
        isLeapYear: function(g) { return (((g % 4 === 0) && (g % 100 !== 0)) || (g % 400 === 0)) },
        getDaysInMonth: function(g, h) { return [31, (b.isLeapYear(g) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][h] },
        getDefaultFormat: function(g, h) { if (g == "standard") { if (h == "input") { return "yyyy-mm-dd hh:ii" } else { return "yyyy-mm-dd hh:ii:ss" } } else { if (g == "php") { if (h == "input") { return "Y-m-d H:i" } else { return "Y-m-d H:i:s" } } else { throw new Error("Invalid format type.") } } },
        validParts: function(g) { if (g == "standard") { return /hh?|HH?|p|P|ii?|ss?|dd?|DD?|mm?|MM?|yy(?:yy)?/g } else { if (g == "php") { return /[dDjlNwzFmMnStyYaABgGhHis]/g } else { throw new Error("Invalid format type.") } } },
        nonpunctuation: /[^ -\/:-@\[-`{-~\t\n\rTZ]+/g,
        parseFormat: function(j, h) {
            var g = j.replace(this.validParts(h), "\0").split("\0"),
                i = j.match(this.validParts(h));
            if (!g || !g.length || !i || i.length == 0) { throw new Error("Invalid date format.") }
            return { separators: g, parts: i }
        },
        parseDate: function(l, u, o, r) {
            if (l instanceof Date) {
                var w = new Date(l.valueOf() - l.getTimezoneOffset() * 60000);
                w.setMilliseconds(0);
                return w
            }
            if (/^\d{4}\-\d{1,2}\-\d{1,2}$/.test(l)) { u = this.parseFormat("yyyy-mm-dd", r) }
            if (/^\d{4}\-\d{1,2}\-\d{1,2}[T ]\d{1,2}\:\d{1,2}$/.test(l)) { u = this.parseFormat("yyyy-mm-dd hh:ii", r) }
            if (/^\d{4}\-\d{1,2}\-\d{1,2}[T ]\d{1,2}\:\d{1,2}\:\d{1,2}[Z]{0,1}$/.test(l)) { u = this.parseFormat("yyyy-mm-dd hh:ii:ss", r) }
            if (/^[-+]\d+[dmwy]([\s,]+[-+]\d+[dmwy])*$/.test(l)) {
                var x = /([-+]\d+)([dmwy])/,
                    m = l.match(/([-+]\d+)([dmwy])/g),
                    g, k;
                l = new Date();
                for (var n = 0; n < m.length; n++) {
                    g = x.exec(m[n]);
                    k = parseInt(g[1]);
                    switch (g[2]) {
                        case "d":
                            l.setUTCDate(l.getUTCDate() + k);
                            break;
                        case "m":
                            l = e.prototype.moveMonth.call(e.prototype, l, k);
                            break;
                        case "w":
                            l.setUTCDate(l.getUTCDate() + k * 7);
                            break;
                        case "y":
                            l = e.prototype.moveYear.call(e.prototype, l, k);
                            break
                    }
                }
                return f(l.getUTCFullYear(), l.getUTCMonth(), l.getUTCDate(), l.getUTCHours(), l.getUTCMinutes(), l.getUTCSeconds(), 0)
            }
            var m = l && l.match(this.nonpunctuation) || [],
                l = new Date(0, 0, 0, 0, 0, 0, 0),
                q = {},
                t = ["hh", "h", "ii", "i", "ss", "s", "yyyy", "yy", "M", "MM", "m", "mm", "D", "DD", "d", "dd", "H", "HH", "p", "P"],
                v = {
                    hh: function(s, i) { return s.setUTCHours(i) },
                    h: function(s, i) { return s.setUTCHours(i) },
                    HH: function(s, i) { return s.setUTCHours(i == 12 ? 0 : i) },
                    H: function(s, i) { return s.setUTCHours(i == 12 ? 0 : i) },
                    ii: function(s, i) { return s.setUTCMinutes(i) },
                    i: function(s, i) { return s.setUTCMinutes(i) },
                    ss: function(s, i) { return s.setUTCSeconds(i) },
                    s: function(s, i) { return s.setUTCSeconds(i) },
                    yyyy: function(s, i) { return s.setUTCFullYear(i) },
                    yy: function(s, i) { return s.setUTCFullYear(2000 + i) },
                    m: function(s, i) {
                        i -= 1;
                        while (i < 0) { i += 12 }
                        i %= 12;
                        s.setUTCMonth(i);
                        while (s.getUTCMonth() != i) { s.setUTCDate(s.getUTCDate() - 1) }
                        return s
                    },
                    d: function(s, i) { return s.setUTCDate(i) },
                    p: function(s, i) { return s.setUTCHours(i == 1 ? s.getUTCHours() + 12 : s.getUTCHours()) }
                },
                j, p, g;
            v.M = v.MM = v.mm = v.m;
            v.dd = v.d;
            v.P = v.p;
            l = f(l.getFullYear(), l.getMonth(), l.getDate(), l.getHours(), l.getMinutes(), l.getSeconds());
            if (m.length == u.parts.length) {
                for (var n = 0, h = u.parts.length; n < h; n++) {
                    j = parseInt(m[n], 10);
                    g = u.parts[n];
                    if (isNaN(j)) {
                        switch (g) {
                            case "MM":
                                p = c(d[o].months).filter(function() {
                                    var i = this.slice(0, m[n].length),
                                        s = m[n].slice(0, i.length);
                                    return i == s
                                });
                                j = c.inArray(p[0], d[o].months) + 1;
                                break;
                            case "M":
                                p = c(d[o].monthsShort).filter(function() {
                                    var i = this.slice(0, m[n].length),
                                        s = m[n].slice(0, i.length);
                                    return i == s
                                });
                                j = c.inArray(p[0], d[o].monthsShort) + 1;
                                break;
                            case "p":
                            case "P":
                                j = c.inArray(m[n].toLowerCase(), d[o].meridiem);
                                break
                        }
                    }
                    q[g] = j
                }
                for (var n = 0, y; n < t.length; n++) { y = t[n]; if (y in q && !isNaN(q[y])) { v[y](l, q[y]) } }
            }
            return l
        },
        formatDate: function(g, m, o, k) {
            if (g == null) { return "" }
            var n;
            if (k == "standard") {
                n = { yy: g.getUTCFullYear().toString().substring(2), yyyy: g.getUTCFullYear(), m: g.getUTCMonth() + 1, M: d[o].monthsShort[g.getUTCMonth()], MM: d[o].months[g.getUTCMonth()], d: g.getUTCDate(), D: d[o].daysShort[g.getUTCDay()], DD: d[o].days[g.getUTCDay()], p: (d[o].meridiem.length == 2 ? d[o].meridiem[g.getUTCHours() < 12 ? 0 : 1] : ""), h: g.getUTCHours(), i: g.getUTCMinutes(), s: g.getUTCSeconds() };
                if (d[o].meridiem.length == 2) { n.H = (n.h % 12 == 0 ? 12 : n.h % 12) } else { n.H = n.h }
                n.HH = (n.H < 10 ? "0" : "") + n.H;
                n.P = n.p.toUpperCase();
                n.hh = (n.h < 10 ? "0" : "") + n.h;
                n.ii = (n.i < 10 ? "0" : "") + n.i;
                n.ss = (n.s < 10 ? "0" : "") + n.s;
                n.dd = (n.d < 10 ? "0" : "") + n.d;
                n.mm = (n.m < 10 ? "0" : "") + n.m
            } else {
                if (k == "php") {
                    n = { y: g.getUTCFullYear().toString().substring(2), Y: g.getUTCFullYear(), F: d[o].months[g.getUTCMonth()], M: d[o].monthsShort[g.getUTCMonth()], n: g.getUTCMonth() + 1, t: b.getDaysInMonth(g.getUTCFullYear(), g.getUTCMonth()), j: g.getUTCDate(), l: d[o].days[g.getUTCDay()], D: d[o].daysShort[g.getUTCDay()], w: g.getUTCDay(), N: (g.getUTCDay() == 0 ? 7 : g.getUTCDay()), S: (g.getUTCDate() % 10 <= d[o].suffix.length ? d[o].suffix[g.getUTCDate() % 10 - 1] : ""), a: (d[o].meridiem.length == 2 ? d[o].meridiem[g.getUTCHours() < 12 ? 0 : 1] : ""), g: (g.getUTCHours() % 12 == 0 ? 12 : g.getUTCHours() % 12), G: g.getUTCHours(), i: g.getUTCMinutes(), s: g.getUTCSeconds() };
                    n.m = (n.n < 10 ? "0" : "") + n.n;
                    n.d = (n.j < 10 ? "0" : "") + n.j;
                    n.A = n.a.toString().toUpperCase();
                    n.h = (n.g < 10 ? "0" : "") + n.g;
                    n.H = (n.G < 10 ? "0" : "") + n.G;
                    n.i = (n.i < 10 ? "0" : "") + n.i;
                    n.s = (n.s < 10 ? "0" : "") + n.s
                } else { throw new Error("Invalid format type.") }
            }
            var g = [],
                l = c.extend([], m.separators);
            for (var j = 0, h = m.parts.length; j < h; j++) {
                if (l.length) { g.push(l.shift()) }
                g.push(n[m.parts[j]])
            }
            if (l.length) { g.push(l.shift()) }
            return g.join("")
        },
        convertViewMode: function(g) {
            switch (g) {
                case 4:
                case "decade":
                    g = 4;
                    break;
                case 3:
                case "year":
                    g = 3;
                    break;
                case 2:
                case "month":
                    g = 2;
                    break;
                case 1:
                case "day":
                    g = 1;
                    break;
                case 0:
                case "hour":
                    g = 0;
                    break
            }
            return g
        },
        headTemplate: '<thead><tr><th class="prev"><i class="icon-arrow-left"/></th><th colspan="5" class="switch"></th><th class="next"><i class="icon-arrow-right"/></th></tr></thead>',
        headTemplateV3: '<thead><tr><th class="prev"><i class="fa fa-arrow-left"></i> </th><th colspan="5" class="switch"></th><th class="next"><i class="fa fa-arrow-right"></i> </th></tr></thead>',
        contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>',
        footTemplate: '<tfoot><tr><th colspan="7" class="today"></th></tr></tfoot>'
    };
    b.template = '<div class="datetimepicker"><div class="datetimepicker-minutes"><table class=" table-condensed">' + b.headTemplate + b.contTemplate + b.footTemplate + '</table></div><div class="datetimepicker-hours"><table class=" table-condensed">' + b.headTemplate + b.contTemplate + b.footTemplate + '</table></div><div class="datetimepicker-days"><table class=" table-condensed">' + b.headTemplate + "<tbody></tbody>" + b.footTemplate + '</table></div><div class="datetimepicker-months"><table class="table-condensed">' + b.headTemplate + b.contTemplate + b.footTemplate + '</table></div><div class="datetimepicker-years"><table class="table-condensed">' + b.headTemplate + b.contTemplate + b.footTemplate + "</table></div></div>";
    b.templateV3 = '<div class="datetimepicker"><div class="datetimepicker-minutes"><table class=" table-condensed">' + b.headTemplateV3 + b.contTemplate + b.footTemplate + '</table></div><div class="datetimepicker-hours"><table class=" table-condensed">' + b.headTemplateV3 + b.contTemplate + b.footTemplate + '</table></div><div class="datetimepicker-days"><table class=" table-condensed">' + b.headTemplateV3 + "<tbody></tbody>" + b.footTemplate + '</table></div><div class="datetimepicker-months"><table class="table-condensed">' + b.headTemplateV3 + b.contTemplate + b.footTemplate + '</table></div><div class="datetimepicker-years"><table class="table-condensed">' + b.headTemplateV3 + b.contTemplate + b.footTemplate + "</table></div></div>";
    c.fn.datetimepicker.DPGlobal = b;
    c.fn.datetimepicker.noConflict = function() { c.fn.datetimepicker = old; return this };
    c(document).on("focus.datetimepicker.data-api click.datetimepicker.data-api", '[data-provide="datetimepicker"]', function(h) {
        var g = c(this);
        if (g.data("datetimepicker")) { return }
        h.preventDefault();
        g.datetimepicker("show")
    });
    c(function() { c('[data-provide="datetimepicker-inline"]').datetimepicker() })
}(window.jQuery);

! function(e) {
    var t = function(t, n, r) {
        var i = typeof n == "object",
            s;
        this.startDate = moment().startOf("day");
        this.endDate = moment().startOf("day");
        this.minDate = !1;
        this.maxDate = !1;
        this.dateLimit = !1;
        this.showDropdowns = !1;
        this.showWeekNumbers = !1;
        this.timePicker = !1;
        this.timePickerIncrement = 30;
        this.timePicker12Hour = !0;
        this.ranges = {};
        this.opens = "right";
        this.buttonClasses = ["btn", "btn-small"];
        this.applyClass = "btn-success";
        this.cancelClass = "btn-default";
        this.format = "MM/DD/YYYY";
        this.separator = " - ";
        this.locale = { applyLabel: "Apply", cancelLabel: "Cancel", fromLabel: "From", toLabel: "To", weekLabel: "W", customRangeLabel: "Custom Range", daysOfWeek: moment()._lang._weekdaysMin.slice(), monthNames: moment()._lang._monthsShort.slice(), firstDay: 0 };
        this.cb = function() {};
        this.element = e(t);
        this.element.hasClass("pull-right") && (this.opens = "left");
        this.element.is("input") ? this.element.on({ click: e.proxy(this.show, this), focus: e.proxy(this.show, this) }) : this.element.on("click", e.proxy(this.show, this));
        s = this.locale;
        if (i) {
            typeof n.locale == "object" && e.each(s, function(e, t) { s[e] = n.locale[e] || t });
            n.applyClass && (this.applyClass = n.applyClass);
            n.cancelClass && (this.cancelClass = n.cancelClass)
        }
        var o = '<div class="daterangepicker dropdown-menu"><div class="calendar left"></div><div class="calendar right"></div><div class="ranges"><div class="range_inputs"><div class="daterangepicker_start_input" style="float: left"><label for="daterangepicker_start">' + this.locale.fromLabel + "</label>" + '<input class="input-mini" type="text" name="daterangepicker_start" value="" disabled="disabled" />' + "</div>" + '<div class="daterangepicker_end_input" style="float: left; padding-left: 11px">' + '<label for="daterangepicker_end">' + this.locale.toLabel + "</label>" + '<input class="input-mini" type="text" name="daterangepicker_end" value="" disabled="disabled" />' + "</div>" + '<button class="' + this.applyClass + ' applyBtn" disabled="disabled">' + this.locale.applyLabel + "</button>&nbsp;" + '<button class="' + this.cancelClass + ' cancelBtn">' + this.locale.cancelLabel + "</button>" + "</div>" + "</div>" + "</div>";
        this.container = e(o).appendTo("body");
        if (i) {
            typeof n.format == "string" && (this.format = n.format);
            typeof n.separator == "string" && (this.separator = n.separator);
            typeof n.startDate == "string" && (this.startDate = moment(n.startDate, this.format));
            typeof n.endDate == "string" && (this.endDate = moment(n.endDate, this.format));
            typeof n.minDate == "string" && (this.minDate = moment(n.minDate, this.format));
            typeof n.maxDate == "string" && (this.maxDate = moment(n.maxDate, this.format));
            typeof n.startDate == "object" && (this.startDate = moment(n.startDate));
            typeof n.endDate == "object" && (this.endDate = moment(n.endDate));
            typeof n.minDate == "object" && (this.minDate = moment(n.minDate));
            typeof n.maxDate == "object" && (this.maxDate = moment(n.maxDate));
            if (typeof n.ranges == "object") {
                for (var u in n.ranges) {
                    var a = moment(n.ranges[u][0]),
                        f = moment(n.ranges[u][1]);
                    this.minDate && a.isBefore(this.minDate) && (a = moment(this.minDate));
                    this.maxDate && f.isAfter(this.maxDate) && (f = moment(this.maxDate));
                    if (this.minDate && f.isBefore(this.minDate) || this.maxDate && a.isAfter(this.maxDate)) continue;
                    this.ranges[u] = [a, f]
                }
                var l = "<ul>";
                for (var u in this.ranges) l += "<li>" + u + "</li>";
                l += "<li>" + this.locale.customRangeLabel + "</li>";
                l += "</ul>";
                this.container.find(".ranges").prepend(l)
            }
            typeof n.dateLimit == "object" && (this.dateLimit = n.dateLimit);
            if (typeof n.locale == "object" && typeof n.locale.firstDay == "number") {
                this.locale.firstDay = n.locale.firstDay;
                var c = n.locale.firstDay;
                while (c > 0) {
                    this.locale.daysOfWeek.push(this.locale.daysOfWeek.shift());
                    c--
                }
            }
            typeof n.opens == "string" && (this.opens = n.opens);
            typeof n.showWeekNumbers == "boolean" && (this.showWeekNumbers = n.showWeekNumbers);
            typeof n.buttonClasses == "string" && (this.buttonClasses = [n.buttonClasses]);
            typeof n.buttonClasses == "object" && (this.buttonClasses = n.buttonClasses);
            typeof n.showDropdowns == "boolean" && (this.showDropdowns = n.showDropdowns);
            typeof n.timePicker == "boolean" && (this.timePicker = n.timePicker);
            typeof n.timePickerIncrement == "number" && (this.timePickerIncrement = n.timePickerIncrement);
            typeof n.timePicker12Hour == "boolean" && (this.timePicker12Hour = n.timePicker12Hour)
        }
        if (!this.timePicker) {
            this.startDate = this.startDate.startOf("day");
            this.endDate = this.endDate.startOf("day")
        }
        var h = this.container;
        e.each(this.buttonClasses, function(e, t) { h.find("button").addClass(t) });
        if (this.opens == "right") {
            var p = this.container.find(".calendar.left"),
                d = this.container.find(".calendar.right");
            p.removeClass("left").addClass("right");
            d.removeClass("right").addClass("left")
        }
        if (typeof n == "undefined" || typeof n.ranges == "undefined") {
            this.container.find(".calendar").show();
            this.move()
        }
        typeof r == "function" && (this.cb = r);
        this.container.addClass("opens" + this.opens);
        if (!i || typeof n.startDate == "undefined" && typeof n.endDate == "undefined")
            if (e(this.element).is("input[type=text]")) {
                var v = e(this.element).val(),
                    m = v.split(this.separator),
                    a, f;
                if (m.length == 2) {
                    a = moment(m[0], this.format);
                    f = moment(m[1], this.format)
                }
                if (a != null && f != null) {
                    this.startDate = a;
                    this.endDate = f
                }
            }
        this.oldStartDate = this.startDate.clone();
        this.oldEndDate = this.endDate.clone();
        this.leftCalendar = { month: moment([this.startDate.year(), this.startDate.month(), 1, this.startDate.hour(), this.startDate.minute()]), calendar: [] };
        this.rightCalendar = { month: moment([this.endDate.year(), this.endDate.month(), 1, this.endDate.hour(), this.endDate.minute()]), calendar: [] };
        this.container.on("mousedown", e.proxy(this.mousedown, this));
        this.container.find(".calendar").on("click", ".prev", e.proxy(this.clickPrev, this));
        this.container.find(".calendar").on("click", ".next", e.proxy(this.clickNext, this));
        this.container.find(".ranges").on("click", "button.applyBtn", e.proxy(this.clickApply, this));
        this.container.find(".ranges").on("click", "button.cancelBtn", e.proxy(this.clickCancel, this));
        this.container.find(".ranges").on("click", ".daterangepicker_start_input", e.proxy(this.showCalendars, this));
        this.container.find(".ranges").on("click", ".daterangepicker_end_input", e.proxy(this.showCalendars, this));
        this.container.find(".calendar").on("click", "td.available", e.proxy(this.clickDate, this));
        this.container.find(".calendar").on("mouseenter", "td.available", e.proxy(this.enterDate, this));
        this.container.find(".calendar").on("mouseleave", "td.available", e.proxy(this.updateView, this));
        this.container.find(".ranges").on("click", "li", e.proxy(this.clickRange, this));
        this.container.find(".ranges").on("mouseenter", "li", e.proxy(this.enterRange, this));
        this.container.find(".ranges").on("mouseleave", "li", e.proxy(this.updateView, this));
        this.container.find(".calendar").on("change", "select.yearselect", e.proxy(this.updateMonthYear, this));
        this.container.find(".calendar").on("change", "select.monthselect", e.proxy(this.updateMonthYear, this));
        this.container.find(".calendar").on("change", "select.hourselect", e.proxy(this.updateTime, this));
        this.container.find(".calendar").on("change", "select.minuteselect", e.proxy(this.updateTime, this));
        this.container.find(".calendar").on("change", "select.ampmselect", e.proxy(this.updateTime, this));
        this.element.on("keyup", e.proxy(this.updateFromControl, this));
        this.updateView();
        this.updateCalendars()
    };
    t.prototype = {
        constructor: t,
        mousedown: function(e) { e.stopPropagation() },
        updateView: function() {
            this.leftCalendar.month.month(this.startDate.month()).year(this.startDate.year());
            this.rightCalendar.month.month(this.endDate.month()).year(this.endDate.year());
            this.container.find("input[name=daterangepicker_start]").val(this.startDate.format(this.format));
            this.container.find("input[name=daterangepicker_end]").val(this.endDate.format(this.format));
            this.startDate.isSame(this.endDate) || this.startDate.isBefore(this.endDate) ? this.container.find("button.applyBtn").removeAttr("disabled") : this.container.find("button.applyBtn").attr("disabled", "disabled")
        },
        updateFromControl: function() {
            if (!this.element.is("input")) return;
            if (!this.element.val().length) return;
            var e = this.element.val().split(this.separator),
                t = moment(e[0], this.format),
                n = moment(e[1], this.format);
            if (t == null || n == null) return;
            if (n.isBefore(t)) return;
            this.startDate = t;
            this.endDate = n;
            this.notify();
            this.updateCalendars()
        },
        notify: function() {
            this.updateView();
            this.cb(this.startDate, this.endDate)
        },
        move: function() {
            var t = e(this.container).find(".ranges").outerWidth();
            if (e(this.container).find(".calendar").is(":visible")) {
                var n = 24;
                t += e(this.container).find(".calendar").outerWidth() * 2 + n
            }
            if (this.opens == "left") {
                this.container.css({ top: this.element.offset().top + this.element.outerHeight(), right: e(window).width() - this.element.offset().left - this.element.outerWidth(), left: "auto", "min-width": t });
                this.container.offset().left < 0 && this.container.css({ right: "auto", left: 9 })
            } else {
                this.container.css({ top: this.element.offset().top + this.element.outerHeight(), left: this.element.offset().left, right: "auto", "min-width": t });
                this.container.offset().left + this.container.outerWidth() > e(window).width() && this.container.css({ left: "auto", right: 0 })
            }
        },
        show: function(t) {
            this.container.show();
            this.move();
            if (t) {
                t.stopPropagation();
                t.preventDefault()
            }
            e(document).on("mousedown", e.proxy(this.hide, this));
            this.element.trigger("shown", { target: t.target, picker: this })
        },
        hide: function(t) {
            this.container.hide();
            (!this.startDate.isSame(this.oldStartDate) || !this.endDate.isSame(this.oldEndDate)) && this.notify();
            this.oldStartDate = this.startDate.clone();
            this.oldEndDate = this.endDate.clone();
            e(document).off("mousedown", this.hide);
            this.element.trigger("hidden", { picker: this })
        },
        enterRange: function(e) {
            var t = e.target.innerHTML;
            if (t == this.locale.customRangeLabel) this.updateView();
            else {
                var n = this.ranges[t];
                this.container.find("input[name=daterangepicker_start]").val(n[0].format(this.format));
                this.container.find("input[name=daterangepicker_end]").val(n[1].format(this.format))
            }
        },
        showCalendars: function() {
            this.container.find(".calendar").show();
            this.move()
        },
        updateInputText: function() { this.element.is("input") && this.element.val(this.startDate.format(this.format) + this.separator + this.endDate.format(this.format)) },
        clickRange: function(e) {
            var t = e.target.innerHTML;
            if (t == this.locale.customRangeLabel) this.showCalendars();
            else {
                var n = this.ranges[t];
                this.startDate = n[0];
                this.endDate = n[1];
                if (!this.timePicker) {
                    this.startDate.startOf("day");
                    this.endDate.startOf("day")
                }
                this.leftCalendar.month.month(this.startDate.month()).year(this.startDate.year()).hour(this.startDate.hour()).minute(this.startDate.minute());
                this.rightCalendar.month.month(this.endDate.month()).year(this.endDate.year()).hour(this.endDate.hour()).minute(this.endDate.minute());
                this.updateCalendars();
                this.updateInputText();
                this.container.find(".calendar").hide();
                this.hide()
            }
        },
        clickPrev: function(t) {
            var n = e(t.target).parents(".calendar");
            n.hasClass("left") ? this.leftCalendar.month.subtract("month", 1) : this.rightCalendar.month.subtract("month", 1);
            this.updateCalendars()
        },
        clickNext: function(t) {
            var n = e(t.target).parents(".calendar");
            n.hasClass("left") ? this.leftCalendar.month.add("month", 1) : this.rightCalendar.month.add("month", 1);
            this.updateCalendars()
        },
        enterDate: function(t) {
            var n = e(t.target).attr("data-title"),
                r = n.substr(1, 1),
                i = n.substr(3, 1),
                s = e(t.target).parents(".calendar");
            s.hasClass("left") ? this.container.find("input[name=daterangepicker_start]").val(this.leftCalendar.calendar[r][i].format(this.format)) : this.container.find("input[name=daterangepicker_end]").val(this.rightCalendar.calendar[r][i].format(this.format))
        },
        clickDate: function(t) {
            var n = e(t.target).attr("data-title"),
                r = n.substr(1, 1),
                i = n.substr(3, 1),
                s = e(t.target).parents(".calendar");
            if (s.hasClass("left")) {
                var o = this.leftCalendar.calendar[r][i],
                    u = this.endDate;
                if (typeof this.dateLimit == "object") {
                    var a = moment(o).add(this.dateLimit).startOf("day");
                    u.isAfter(a) && (u = a)
                }
            } else {
                var o = this.startDate,
                    u = this.rightCalendar.calendar[r][i];
                if (typeof this.dateLimit == "object") {
                    var f = moment(u).subtract(this.dateLimit).startOf("day");
                    o.isBefore(f) && (o = f)
                }
            }
            s.find("td").removeClass("active");
            if (o.isSame(u) || o.isBefore(u)) {
                e(t.target).addClass("active");
                this.startDate = o;
                this.endDate = u
            } else if (o.isAfter(u)) {
                e(t.target).addClass("active");
                this.startDate = o;
                this.endDate = moment(o).add("day", 1).startOf("day")
            }
            this.leftCalendar.month.month(this.startDate.month()).year(this.startDate.year());
            this.rightCalendar.month.month(this.endDate.month()).year(this.endDate.year());
            this.updateCalendars()
        },
        clickApply: function(e) {
            this.updateInputText();
            this.hide()
        },
        clickCancel: function(e) {
            this.startDate = this.oldStartDate;
            this.endDate = this.oldEndDate;
            this.updateView();
            this.updateCalendars();
            this.hide()
        },
        updateMonthYear: function(t) {
            var n = e(t.target).closest(".calendar").hasClass("left"),
                r = this.container.find(".calendar.left");
            n || (r = this.container.find(".calendar.right"));
            var i = r.find(".monthselect").val(),
                s = r.find(".yearselect").val();
            n ? this.leftCalendar.month.month(i).year(s) : this.rightCalendar.month.month(i).year(s);
            this.updateCalendars()
        },
        updateTime: function(t) {
            var n = e(t.target).closest(".calendar").hasClass("left"),
                r = this.container.find(".calendar.left");
            n || (r = this.container.find(".calendar.right"));
            var i = parseInt(r.find(".hourselect").val()),
                s = parseInt(r.find(".minuteselect").val());
            if (this.timePicker12Hour) {
                var o = r.find(".ampmselect").val();
                o == "PM" && i < 12 && (i += 12);
                o == "AM" && i == 12 && (i = 0)
            }
            if (n) {
                var u = this.startDate;
                u.hour(i);
                u.minute(s);
                this.startDate = u;
                this.leftCalendar.month.hour(i).minute(s)
            } else {
                var a = this.endDate;
                a.hour(i);
                a.minute(s);
                this.endDate = a;
                this.rightCalendar.month.hour(i).minute(s)
            }
            this.updateCalendars()
        },
        updateCalendars: function() {
            this.leftCalendar.calendar = this.buildCalendar(this.leftCalendar.month.month(), this.leftCalendar.month.year(), this.leftCalendar.month.hour(), this.leftCalendar.month.minute(), "left");
            this.rightCalendar.calendar = this.buildCalendar(this.rightCalendar.month.month(), this.rightCalendar.month.year(), this.rightCalendar.month.hour(), this.rightCalendar.month.minute(), "right");
            this.container.find(".calendar.left").html(this.renderCalendar(this.leftCalendar.calendar, this.startDate, this.minDate, this.maxDate));
            this.container.find(".calendar.right").html(this.renderCalendar(this.rightCalendar.calendar, this.endDate, this.startDate, this.maxDate));
            this.container.find(".ranges li").removeClass("active");
            var e = !0,
                t = 0;
            for (var n in this.ranges) {
                if (this.timePicker) {
                    if (this.startDate.isSame(this.ranges[n][0]) && this.endDate.isSame(this.ranges[n][1])) {
                        e = !1;
                        this.container.find(".ranges li:eq(" + t + ")").addClass("active")
                    }
                } else if (this.startDate.format("YYYY-MM-DD") == this.ranges[n][0].format("YYYY-MM-DD") && this.endDate.format("YYYY-MM-DD") == this.ranges[n][1].format("YYYY-MM-DD")) {
                    e = !1;
                    this.container.find(".ranges li:eq(" + t + ")").addClass("active")
                }
                t++
            }
            e && this.container.find(".ranges li:last").addClass("active")
        },
        buildCalendar: function(e, t, n, r, i) {
            var s = moment([t, e, 1]),
                o = moment(s).subtract("month", 1).month(),
                u = moment(s).subtract("month", 1).year(),
                a = moment([u, o]).daysInMonth(),
                f = s.day(),
                l = [];
            for (var c = 0; c < 6; c++) l[c] = [];
            var h = a - f + this.locale.firstDay + 1;
            h > a && (h -= 7);
            f == this.locale.firstDay && (h = a - 6);
            var p = moment([u, o, h, n, r]);
            for (var c = 0, d = 0, v = 0; c < 42; c++, d++, p = moment(p).add("day", 1)) {
                if (c > 0 && d % 7 == 0) {
                    d = 0;
                    v++
                }
                l[v][d] = p
            }
            return l
        },
        renderDropdowns: function(e, t, n) {
            var r = e.month(),
                i = '<select class="monthselect">',
                s = !1,
                o = !1;
            for (var u = 0; u < 12; u++)(!s || u >= t.month()) && (!o || u <= n.month()) && (i += "<option value='" + u + "'" + (u === r ? " selected='selected'" : "") + ">" + this.locale.monthNames[u] + "</option>");
            i += "</select>";
            var a = e.year(),
                f = n && n.year() || a + 5,
                l = t && t.year() || a - 50,
                c = '<select class="yearselect">';
            for (var h = l; h <= f; h++) c += '<option value="' + h + '"' + (h === a ? ' selected="selected"' : "") + ">" + h + "</option>";
            c += "</select>";
            return i + c
        },
        renderCalendar: function(t, n, r, i) {
            var s = '<div class="calendar-date">';
            s += '<table class="table-condensed">';
            s += "<thead>";
            s += "<tr>";
            this.showWeekNumbers && (s += "<th></th>");
            !r || r.isBefore(t[1][1]) ? s += '<th class="prev available"><i class="icon-arrow-left fa fa-arrow-left"></i></th>' : s += "<th></th>";
            var o = this.locale.monthNames[t[1][1].month()] + t[1][1].format(" YYYY");
            this.showDropdowns && (o = this.renderDropdowns(t[1][1], r, i));
            s += '<th colspan="5" style="width: auto">' + o + "</th>";
            !i || i.isAfter(t[1][1]) ? s += '<th class="next available"><i class="icon-arrow-right fa fa-arrow-right"></i></th>' : s += "<th></th>";
            s += "</tr>";
            s += "<tr>";
            this.showWeekNumbers && (s += '<th class="week">' + this.locale.weekLabel + "</th>");
            e.each(this.locale.daysOfWeek, function(e, t) { s += "<th>" + t + "</th>" });
            s += "</tr>";
            s += "</thead>";
            s += "<tbody>";
            for (var u = 0; u < 6; u++) {
                s += "<tr>";
                this.showWeekNumbers && (s += '<td class="week">' + t[u][0].week() + "</td>");
                for (var a = 0; a < 7; a++) {
                    var f = "available ";
                    f += t[u][a].month() == t[1][1].month() ? "" : "off";
                    if (r && t[u][a].isBefore(r) || i && t[u][a].isAfter(i)) f = " off disabled ";
                    else if (t[u][a].format("YYYY-MM-DD") == n.format("YYYY-MM-DD")) {
                        f += " active ";
                        t[u][a].format("YYYY-MM-DD") == this.startDate.format("YYYY-MM-DD") && (f += " start-date ");
                        t[u][a].format("YYYY-MM-DD") == this.endDate.format("YYYY-MM-DD") && (f += " end-date ")
                    } else if (t[u][a] >= this.startDate && t[u][a] <= this.endDate) {
                        f += " in-range ";
                        t[u][a].isSame(this.startDate) && (f += " start-date ");
                        t[u][a].isSame(this.endDate) && (f += " end-date ")
                    }
                    var l = "r" + u + "c" + a;
                    s += '<td class="' + f.replace(/\s+/g, " ").replace(/^\s?(.*?)\s?$/, "$1") + '" data-title="' + l + '">' + t[u][a].date() + "</td>"
                }
                s += "</tr>"
            }
            s += "</tbody>";
            s += "</table>";
            s += "</div>";
            if (this.timePicker) {
                s += '<div class="calendar-time">';
                s += '<select class="hourselect">';
                var c = 0,
                    h = 23,
                    p = n.hour();
                if (this.timePicker12Hour) {
                    c = 1;
                    h = 12;
                    p >= 12 && (p -= 12);
                    p == 0 && (p = 12)
                }
                for (var d = c; d <= h; d++) d == p ? s += '<option value="' + d + '" selected="selected">' + d + "</option>" : s += '<option value="' + d + '">' + d + "</option>";
                s += "</select> : ";
                s += '<select class="minuteselect">';
                for (var d = 0; d < 60; d += this.timePickerIncrement) {
                    var v = d;
                    v < 10 && (v = "0" + v);
                    d == n.minute() ? s += '<option value="' + d + '" selected="selected">' + v + "</option>" : s += '<option value="' + d + '">' + v + "</option>"
                }
                s += "</select> ";
                if (this.timePicker12Hour) {
                    s += '<select class="ampmselect">';
                    n.hour() >= 12 ? s += '<option value="AM">AM</option><option value="PM" selected="selected">PM</option>' : s += '<option value="AM" selected="selected">AM</option><option value="PM">PM</option>';
                    s += "</select>"
                }
                s += "</div>"
            }
            return s
        }
    };
    e.fn.daterangepicker = function(n, r) {
        this.each(function() {
            var i = e(this);
            i.data("daterangepicker") || i.data("daterangepicker", new t(i, n, r))
        });
        return this
    }
}(window.jQuery);

(function(factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as anonymous module.
        define(['jquery'], factory);
    } else {
        // Browser globals.
        factory(jQuery);
    }
}(function($) {

    var pluses = /\+/g;

    function encode(s) {
        return config.raw ? s : encodeURIComponent(s);
    }

    function decode(s) {
        return config.raw ? s : decodeURIComponent(s);
    }

    function stringifyCookieValue(value) {
        return encode(config.json ? JSON.stringify(value) : String(value));
    }

    function parseCookieValue(s) {
        if (s.indexOf('"') === 0) {
            // This is a quoted cookie as according to RFC2068, unescape...
            s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
        }

        try {
            // Replace server-side written pluses with spaces.
            // If we can't decode the cookie, ignore it, it's unusable.
            // If we can't parse the cookie, ignore it, it's unusable.
            s = decodeURIComponent(s.replace(pluses, ' '));
            return config.json ? JSON.parse(s) : s;
        } catch (e) {}
    }

    function read(s, converter) {
        var value = config.raw ? s : parseCookieValue(s);
        return $.isFunction(converter) ? converter(value) : value;
    }

    var config = $.cookie = function(key, value, options) {

        // Write

        if (value !== undefined && !$.isFunction(value)) {
            options = $.extend({}, config.defaults, options);

            if (typeof options.expires === 'number') {
                var days = options.expires,
                    t = options.expires = new Date();
                t.setTime(+t + days * 864e+5);
            }

            return (document.cookie = [
                encode(key), '=', stringifyCookieValue(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path ? '; path=' + options.path : '',
                options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''
            ].join(''));
        }

        // Read

        var result = key ? undefined : {};

        // To prevent the for loop in the first place assign an empty array
        // in case there are no cookies at all. Also prevents odd result when
        // calling $.cookie().
        var cookies = document.cookie ? document.cookie.split('; ') : [];

        for (var i = 0, l = cookies.length; i < l; i++) {
            var parts = cookies[i].split('=');
            var name = decode(parts.shift());
            var cookie = parts.join('=');

            if (key && key === name) {
                // If second argument (value) is a function it's a converter...
                result = read(cookie, value);
                break;
            }

            // Prevent storing a cookie that we couldn't decode.
            if (!key && (cookie = read(cookie)) !== undefined) {
                result[name] = cookie;
            }
        }

        return result;
    };

    config.defaults = {};

    $.removeCookie = function(key, options) {
        if ($.cookie(key) === undefined) {
            return false;
        }

        // Must not alter options, thus extending a fresh object...
        $.cookie(key, '', $.extend({}, options, { expires: -1 }));
        return !$.cookie(key);
    };

}));

(function() {
    "use strict";
    var EkkoLightbox;

    EkkoLightbox = function(element, options) {
        var content, footer, header, video_id,
            _this = this;
        this.options = $.extend({
            gallery_parent_selector: '*:not(.row)',
            title: null,
            footer: null,
            remote: null,
            left_arrow_class: '.fa .fa-chevron-left',
            right_arrow_class: '.fa .fa-chevron-right',
            directional_arrows: true,
            type: null,
            onShow: function() {},
            onShown: function() {},
            onHide: function() {},
            onHidden: function() {},
            id: false
        }, options || {});
        this.$element = $(element);
        content = '';
        this.modal_id = this.options.modal_id ? this.options.modal_id : 'ekkoLightbox-' + Math.floor((Math.random() * 1000) + 1);
        header = '<div class="modal-header"' + (this.options.title ? '' : ' style="display:none"') + '><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">' + this.options.title + '</h4></div>';
        footer = '<div class="modal-footer"' + (this.options.footer ? '' : ' style="display:none"') + '>' + this.options.footer + '</div>';
        $(document.body).append('<div id="' + this.modal_id + '" class="ekko-lightbox modal fade" tabindex="-1"><div class="modal-dialog"><div class="modal-content">' + header + '<div class="modal-body"><div class="ekko-lightbox-container"><div></div></div></div>' + footer + '</div></div></div>');
        this.modal = $('#' + this.modal_id);
        this.modal_body = this.modal.find('.modal-body').first();
        this.lightbox_container = this.modal_body.find('.ekko-lightbox-container').first();
        this.lightbox_body = this.lightbox_container.find('> div:first-child').first();
        this.modal_arrows = null;
        this.padding = {
            left: parseFloat(this.modal_body.css('padding-left'), 10),
            right: parseFloat(this.modal_body.css('padding-right'), 10),
            bottom: parseFloat(this.modal_body.css('padding-bottom'), 10),
            top: parseFloat(this.modal_body.css('padding-top'), 10)
        };
        if (!this.options.remote) {
            this.error('No remote target given');
        } else {
            this.gallery = this.$element.data('gallery');
            if (this.gallery) {
                if (this.options.gallery_parent_selector === 'document.body' || this.options.gallery_parent_selector === '') {
                    this.gallery_items = $(document.body).find('*[data-toggle="lightbox"][data-gallery="' + this.gallery + '"]');
                } else {
                    this.gallery_items = this.$element.parents(this.options.gallery_parent_selector).first().find('*[data-toggle="lightbox"][data-gallery="' + this.gallery + '"]');
                }
                this.gallery_index = this.gallery_items.index(this.$element);
                $(document).on('keydown.ekkoLightbox', this.navigate.bind(this));
                if (this.options.directional_arrows && this.gallery_items.length > 1) {
                    this.lightbox_container.prepend('<div class="ekko-lightbox-nav-overlay"><a href="#" class="' + this.strip_stops(this.options.left_arrow_class) + '"></a><a href="#" class="' + this.strip_stops(this.options.right_arrow_class) + '"></a></div>');
                    this.modal_arrows = this.lightbox_container.find('div.ekko-lightbox-nav-overlay').first();
                    this.lightbox_container.find('a' + this.strip_spaces(this.options.left_arrow_class)).on('click', function(event) {
                        event.preventDefault();
                        return _this.navigate_left();
                    });
                    this.lightbox_container.find('a' + this.strip_spaces(this.options.right_arrow_class)).on('click', function(event) {
                        event.preventDefault();
                        return _this.navigate_right();
                    });
                }
            }
            if (this.options.type) {
                if (this.options.type === 'image') {
                    this.preloadImage(this.options.remote, true);
                } else if (this.options.type === 'youtube' && (video_id = this.getYoutubeId(this.options.remote))) {
                    this.showYoutubeVideo(video_id);
                } else if (this.options.type === 'vimeo') {
                    this.showVimeoVideo(this.options.remote);
                } else {
                    this.error("Could not detect remote target type. Force the type using data-type=\"image|youtube|vimeo\"");
                }
            } else {
                this.detectRemoteType(this.options.remote);
            }
        }
        this.modal.on('show.bs.modal', this.options.onShow.bind(this)).on('shown.bs.modal', function() {
            if (_this.modal_arrows) {
                _this.resize(_this.lightbox_body.width());
            }
            return _this.options.onShown.call(_this);
        }).on('hide.bs.modal', this.options.onHide.bind(this)).on('hidden.bs.modal', function() {
            if (_this.gallery) {
                $(document).off('keydown.ekkoLightbox');
            }
            _this.modal.remove();
            return _this.options.onHidden.call(_this);
        }).modal('show', options);
        return this.modal;
    };

    EkkoLightbox.prototype = {
        strip_stops: function(str) {
            return str.replace(/\./g, '');
        },
        strip_spaces: function(str) {
            return str.replace(/\s/g, '');
        },
        isImage: function(str) {
            return str.match(/(^data:image\/.*,)|(\.(jp(e|g|eg)|gif|png|bmp|webp|svg)((\?|#).*)?$)/i);
        },
        isSwf: function(str) {
            return str.match(/\.(swf)((\?|#).*)?$/i);
        },
        getYoutubeId: function(str) {
            var match;
            match = str.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/);
            if (match && match[2].length === 11) {
                return match[2];
            } else {
                return false;
            }
        },
        getVimeoId: function(str) {
            if (str.indexOf('vimeo') > 0) {
                return str;
            } else {
                return false;
            }
        },
        navigate: function(event) {
            event = event || window.event;
            if (event.keyCode === 39 || event.keyCode === 37) {
                if (event.keyCode === 39) {
                    return this.navigate_right();
                } else if (event.keyCode === 37) {
                    return this.navigate_left();
                }
            }
        },
        navigate_left: function() {
            var src;
            if (this.gallery_items.length === 1) {
                return;
            }
            if (this.gallery_index === 0) {
                this.gallery_index = this.gallery_items.length - 1;
            } else {
                this.gallery_index--;
            }
            this.$element = $(this.gallery_items.get(this.gallery_index));
            this.updateTitleAndFooter();
            src = this.$element.attr('data-remote') || this.$element.attr('href');
            return this.detectRemoteType(src, this.$element.attr('data-type'));
        },
        navigate_right: function() {
            var next, src;
            if (this.gallery_items.length === 1) {
                return;
            }
            if (this.gallery_index === this.gallery_items.length - 1) {
                this.gallery_index = 0;
            } else {
                this.gallery_index++;
            }
            this.$element = $(this.gallery_items.get(this.gallery_index));
            src = this.$element.attr('data-remote') || this.$element.attr('href');
            this.updateTitleAndFooter();
            this.detectRemoteType(src, this.$element.attr('data-type'));
            if (this.gallery_index + 1 < this.gallery_items.length) {
                next = $(this.gallery_items.get(this.gallery_index + 1), false);
                src = next.attr('data-remote') || next.attr('href');
                if (next.attr('data-type') === 'image' || this.isImage(src)) {
                    return this.preloadImage(src, false);
                }
            }
        },
        detectRemoteType: function(src, type) {
            var video_id;
            if (type === 'image' || this.isImage(src)) {
                return this.preloadImage(src, true);
            } else if (type === 'youtube' || (video_id = this.getYoutubeId(src))) {
                return this.showYoutubeVideo(video_id);
            } else if (type === 'vimeo' || (video_id = this.getVimeoId(src))) {
                return this.showVimeoVideo(video_id);
            } else {
                return this.error("Could not detect remote target type. Force the type using data-type=\"image|youtube|vimeo\"");
            }
        },
        updateTitleAndFooter: function() {
            var caption, footer, header, title;
            header = this.modal.find('.modal-dialog .modal-content .modal-header');
            footer = this.modal.find('.modal-dialog .modal-content .modal-footer');
            title = this.$element.data('title') || "";
            caption = this.$element.data('footer') || "";
            if (title) {
                header.css('display', '').find('.modal-title').html(title);
            } else {
                header.css('display', 'none');
            }
            if (caption) {
                footer.css('display', '').html(caption);
            } else {
                footer.css('display', 'none');
            }
            return this;
        },
        showLoading: function() {
            this.lightbox_body.html('<div class="modal-loading">Loading..</div>');
            return this;
        },
        showYoutubeVideo: function(id) {
            var height, width;
            width = this.$element.data('width') || 560;
            height = this.$element.data('height') || 315;
            this.resize(width);
            this.lightbox_body.html('<iframe width="' + width + '" height="' + height + '" src="//www.youtube.com/embed/' + id + '?badge=0&autoplay=1&html5=1" frameborder="0" allowfullscreen></iframe>');
            if (this.modal_arrows) {
                return this.modal_arrows.css('display', 'none');
            }
        },
        showVimeoVideo: function(id) {
            this.resize(500);
            this.lightbox_body.html('<iframe width="500" height="281" src="' + id + '?autoplay=1" frameborder="0" allowfullscreen></iframe>');
            if (this.modal_arrows) {
                return this.modal_arrows.css('display', 'none');
            }
        },
        error: function(message) {
            this.lightbox_body.html(message);
            return this;
        },
        preloadImage: function(src, onLoadShowImage) {
            var img,
                _this = this;
            img = new Image();
            if ((onLoadShowImage == null) || onLoadShowImage === true) {
                img.onload = function() {
                    var image, width;
                    width = _this.checkImageDimensions(img.width);
                    image = $('<img />');
                    image.attr('src', img.src);
                    image.css('max-width', '100%');
                    _this.lightbox_body.html(image);
                    if (_this.modal_arrows) {
                        _this.modal_arrows.css('display', 'block');
                    }
                    return _this.resize(width);
                };
                img.onerror = function() {
                    return _this.error('Failed to load image: ' + src);
                };
            }
            img.src = src;
            return img;
        },
        resize: function(width) {
            var width_inc_padding;
            width_inc_padding = width + this.padding.left + this.padding.right;
            this.modal.find('.modal-content').css('width', width_inc_padding);
            this.modal.find('.modal-dialog').css('width', width_inc_padding + 20);
            this.lightbox_container.find('a').css('padding-top', function() {
                return $(this).parent().height() / 2;
            });
            return this;
        },
        checkImageDimensions: function(max_width) {
            var w, width;
            w = $(window);
            width = max_width;
            if ((max_width + (this.padding.left + this.padding.right + 20)) > w.width()) {
                width = w.width() - (this.padding.left + this.padding.right + 20);
            }
            return width;
        },
        close: function() {
            return this.modal.modal('hide');
        }
    };

    $.fn.ekkoLightbox = function(options) {
        return this.each(function() {
            var $this;
            $this = $(this);
            options = $.extend({
                remote: $this.attr('data-remote') || $this.attr('href'),
                gallery_parent_selector: $this.attr('data-parent'),
                type: $this.attr('data-type')
            }, options, $this.data());
            new EkkoLightbox(this, options);
            return this;
        });
    };

}).call(this);


/*!
 * iCheck v1.0.1, http://git.io/arlzeA
 * ===================================
 * Powerful jQuery and Zepto plugin for checkboxes and radio buttons customization
 *
 * (c) 2013 Damir Sultanov, http://fronteed.com
 * MIT Licensed
 */

(function($) {

    // Cached vars
    var _iCheck = 'iCheck',
        _iCheckHelper = _iCheck + '-helper',
        _checkbox = 'checkbox',
        _radio = 'radio',
        _checked = 'checked',
        _unchecked = 'un' + _checked,
        _disabled = 'disabled',
        _determinate = 'determinate',
        _indeterminate = 'in' + _determinate,
        _update = 'update',
        _type = 'type',
        _click = 'click',
        _touch = 'touchbegin.i touchend.i',
        _add = 'addClass',
        _remove = 'removeClass',
        _callback = 'trigger',
        _label = 'label',
        _cursor = 'cursor',
        _mobile = /ipad|iphone|ipod|android|blackberry|windows phone|opera mini|silk/i.test(navigator.userAgent);

    // Plugin init
    $.fn[_iCheck] = function(options, fire) {

        // Walker
        var handle = 'input[type="' + _checkbox + '"], input[type="' + _radio + '"]',
            stack = $(),
            walker = function(object) {
                object.each(function() {
                    var self = $(this);

                    if (self.is(handle)) {
                        stack = stack.add(self);
                    } else {
                        stack = stack.add(self.find(handle));
                    };
                });
            };

        // Check if we should operate with some method
        if (/^(check|uncheck|toggle|indeterminate|determinate|disable|enable|update|destroy)$/i.test(options)) {

            // Normalize method's name
            options = options.toLowerCase();

            // Find checkboxes and radio buttons
            walker(this);

            return stack.each(function() {
                var self = $(this);

                if (options == 'destroy') {
                    tidy(self, 'ifDestroyed');
                } else {
                    operate(self, true, options);
                };

                // Fire method's callback
                if ($.isFunction(fire)) {
                    fire();
                };
            });

            // Customization
        } else if (typeof options == 'object' || !options) {

            // Check if any options were passed
            var settings = $.extend({
                    checkedClass: _checked,
                    disabledClass: _disabled,
                    indeterminateClass: _indeterminate,
                    labelHover: true,
                    aria: false
                }, options),

                selector = settings.handle,
                hoverClass = settings.hoverClass || 'hover',
                focusClass = settings.focusClass || 'focus',
                activeClass = settings.activeClass || 'active',
                labelHover = !!settings.labelHover,
                labelHoverClass = settings.labelHoverClass || 'hover',

                // Setup clickable area
                area = ('' + settings.increaseArea).replace('%', '') | 0;

            // Selector limit
            if (selector == _checkbox || selector == _radio) {
                handle = 'input[type="' + selector + '"]';
            };

            // Clickable area limit
            if (area < -50) {
                area = -50;
            };

            // Walk around the selector
            walker(this);

            return stack.each(function() {
                var self = $(this);

                // If already customized
                tidy(self);

                var node = this,
                    id = node.id,

                    // Layer styles
                    offset = -area + '%',
                    size = 100 + (area * 2) + '%',
                    layer = {
                        position: 'absolute',
                        top: offset,
                        left: offset,
                        display: 'block',
                        width: size,
                        height: size,
                        margin: 0,
                        padding: 0,
                        background: '#fff',
                        border: 0,
                        opacity: 0
                    },

                    // Choose how to hide input
                    hide = _mobile ? {
                        position: 'absolute',
                        visibility: 'hidden'
                    } : area ? layer : {
                        position: 'absolute',
                        opacity: 0
                    },

                    // Get proper class
                    className = node[_type] == _checkbox ? settings.checkboxClass || 'i' + _checkbox : settings.radioClass || 'i' + _radio,

                    // Find assigned labels
                    label = $(_label + '[for="' + id + '"]').add(self.closest(_label)),

                    // Check ARIA option
                    aria = !!settings.aria,

                    // Set ARIA placeholder
                    ariaID = _iCheck + '-' + Math.random().toString(36).substr(2, 6),

                    // Parent & helper
                    parent = '<div class="' + className + '" ' + (aria ? 'role="' + node[_type] + '" ' : ''),
                    helper;

                // Set ARIA "labelledby"
                if (aria) {
                    label.each(function() {
                        parent += 'aria-labelledby="';

                        if (this.id) {
                            parent += this.id;
                        } else {
                            this.id = ariaID;
                            parent += ariaID;
                        }

                        parent += '"';
                    });
                };

                // Wrap input
                parent = self.wrap(parent + '/>')[_callback]('ifCreated').parent().append(settings.insert);

                // Layer addition
                helper = $('<ins class="' + _iCheckHelper + '"/>').css(layer).appendTo(parent);

                // Finalize customization
                self.data(_iCheck, { o: settings, s: self.attr('style') }).css(hide);
                !!settings.inheritClass && parent[_add](node.className || '');
                !!settings.inheritID && id && parent.attr('id', _iCheck + '-' + id);
                parent.css('position') == 'static' && parent.css('position', 'relative');
                operate(self, true, _update);

                // Label events
                if (label.length) {
                    label.on(_click + '.i mouseover.i mouseout.i ' + _touch, function(event) {
                        var type = event[_type],
                            item = $(this);

                        // Do nothing if input is disabled
                        if (!node[_disabled]) {

                            // Click
                            if (type == _click) {
                                if ($(event.target).is('a')) {
                                    return;
                                }
                                operate(self, false, true);

                                // Hover state
                            } else if (labelHover) {

                                // mouseout|touchend
                                if (/ut|nd/.test(type)) {
                                    parent[_remove](hoverClass);
                                    item[_remove](labelHoverClass);
                                } else {
                                    parent[_add](hoverClass);
                                    item[_add](labelHoverClass);
                                };
                            };

                            if (_mobile) {
                                event.stopPropagation();
                            } else {
                                return false;
                            };
                        };
                    });
                };

                // Input events
                self.on(_click + '.i focus.i blur.i keyup.i keydown.i keypress.i', function(event) {
                    var type = event[_type],
                        key = event.keyCode;

                    // Click
                    if (type == _click) {
                        return false;

                        // Keydown
                    } else if (type == 'keydown' && key == 32) {
                        if (!(node[_type] == _radio && node[_checked])) {
                            if (node[_checked]) {
                                off(self, _checked);
                            } else {
                                on(self, _checked);
                            };
                        };

                        return false;

                        // Keyup
                    } else if (type == 'keyup' && node[_type] == _radio) {
                        !node[_checked] && on(self, _checked);

                        // Focus/blur
                    } else if (/us|ur/.test(type)) {
                        parent[type == 'blur' ? _remove : _add](focusClass);
                    };
                });

                // Helper events
                helper.on(_click + ' mousedown mouseup mouseover mouseout ' + _touch, function(event) {
                    var type = event[_type],

                        // mousedown|mouseup
                        toggle = /wn|up/.test(type) ? activeClass : hoverClass;

                    // Do nothing if input is disabled
                    if (!node[_disabled]) {

                        // Click
                        if (type == _click) {
                            operate(self, false, true);

                            // Active and hover states
                        } else {

                            // State is on
                            if (/wn|er|in/.test(type)) {

                                // mousedown|mouseover|touchbegin
                                parent[_add](toggle);

                                // State is off
                            } else {
                                parent[_remove](toggle + ' ' + activeClass);
                            };

                            // Label hover
                            if (label.length && labelHover && toggle == hoverClass) {

                                // mouseout|touchend
                                label[/ut|nd/.test(type) ? _remove : _add](labelHoverClass);
                            };
                        };

                        if (_mobile) {
                            event.stopPropagation();
                        } else {
                            return false;
                        };
                    };
                });
            });
        } else {
            return this;
        };
    };

    // Do something with inputs
    function operate(input, direct, method) {
        var node = input[0],
            state = /er/.test(method) ? _indeterminate : /bl/.test(method) ? _disabled : _checked,
            active = method == _update ? {
                checked: node[_checked],
                disabled: node[_disabled],
                indeterminate: input.attr(_indeterminate) == 'true' || input.attr(_determinate) == 'false'
            } : node[state];

        // Check, disable or indeterminate
        if (/^(ch|di|in)/.test(method) && !active) {
            on(input, state);

            // Uncheck, enable or determinate
        } else if (/^(un|en|de)/.test(method) && active) {
            off(input, state);

            // Update
        } else if (method == _update) {

            // Handle states
            for (var state in active) {
                if (active[state]) {
                    on(input, state, true);
                } else {
                    off(input, state, true);
                };
            };

        } else if (!direct || method == 'toggle') {

            // Helper or label was clicked
            if (!direct) {
                input[_callback]('ifClicked');
            };

            // Toggle checked state
            if (active) {
                if (node[_type] !== _radio) {
                    off(input, state);
                };
            } else {
                on(input, state);
            };
        };
    };

    // Add checked, disabled or indeterminate state
    function on(input, state, keep) {
        var node = input[0],
            parent = input.parent(),
            checked = state == _checked,
            indeterminate = state == _indeterminate,
            disabled = state == _disabled,
            callback = indeterminate ? _determinate : checked ? _unchecked : 'enabled',
            regular = option(input, callback + capitalize(node[_type])),
            specific = option(input, state + capitalize(node[_type]));

        // Prevent unnecessary actions
        if (node[state] !== true) {

            // Toggle assigned radio buttons
            if (!keep && state == _checked && node[_type] == _radio && node.name) {
                var form = input.closest('form'),
                    inputs = 'input[name="' + node.name + '"]';

                inputs = form.length ? form.find(inputs) : $(inputs);

                inputs.each(function() {
                    if (this !== node && $(this).data(_iCheck)) {
                        off($(this), state);
                    };
                });
            };

            // Indeterminate state
            if (indeterminate) {

                // Add indeterminate state
                node[state] = true;

                // Remove checked state
                if (node[_checked]) {
                    off(input, _checked, 'force');
                };

                // Checked or disabled state
            } else {

                // Add checked or disabled state
                if (!keep) {
                    node[state] = true;
                };

                // Remove indeterminate state
                if (checked && node[_indeterminate]) {
                    off(input, _indeterminate, false);
                };
            };

            // Trigger callbacks
            callbacks(input, checked, state, keep);
        };

        // Add proper cursor
        if (node[_disabled] && !!option(input, _cursor, true)) {
            parent.find('.' + _iCheckHelper).css(_cursor, 'default');
        };

        // Add state class
        parent[_add](specific || option(input, state) || '');

        // Set ARIA attribute
        disabled ? parent.attr('aria-disabled', 'true') : parent.attr('aria-checked', indeterminate ? 'mixed' : 'true');

        // Remove regular state class
        parent[_remove](regular || option(input, callback) || '');
    };

    // Remove checked, disabled or indeterminate state
    function off(input, state, keep) {
        var node = input[0],
            parent = input.parent(),
            checked = state == _checked,
            indeterminate = state == _indeterminate,
            disabled = state == _disabled,
            callback = indeterminate ? _determinate : checked ? _unchecked : 'enabled',
            regular = option(input, callback + capitalize(node[_type])),
            specific = option(input, state + capitalize(node[_type]));

        // Prevent unnecessary actions
        if (node[state] !== false) {

            // Toggle state
            if (indeterminate || !keep || keep == 'force') {
                node[state] = false;
            };

            // Trigger callbacks
            callbacks(input, checked, callback, keep);
        };

        // Add proper cursor
        if (!node[_disabled] && !!option(input, _cursor, true)) {
            parent.find('.' + _iCheckHelper).css(_cursor, 'pointer');
        };

        // Remove state class
        parent[_remove](specific || option(input, state) || '');

        // Set ARIA attribute
        disabled ? parent.attr('aria-disabled', 'false') : parent.attr('aria-checked', 'false');

        // Add regular state class
        parent[_add](regular || option(input, callback) || '');
    };

    // Remove all traces
    function tidy(input, callback) {
        if (input.data(_iCheck)) {

            // Remove everything except input
            input.parent().html(input.attr('style', input.data(_iCheck).s || ''));

            // Callback
            if (callback) {
                input[_callback](callback);
            };

            // Unbind events
            input.off('.i').unwrap();
            $(_label + '[for="' + input[0].id + '"]').add(input.closest(_label)).off('.i');
        };
    };

    // Get some option
    function option(input, state, regular) {
        if (input.data(_iCheck)) {
            return input.data(_iCheck).o[state + (regular ? '' : 'Class')];
        };
    };

    // Capitalize some string
    function capitalize(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    };

    // Executable handlers
    function callbacks(input, checked, callback, keep) {
        if (!keep) {
            if (checked) {
                input[_callback]('ifToggled');
            };

            input[_callback]('ifChanged')[_callback]('if' + capitalize(callback));
        };
    };
})(window.jQuery || window.Zepto);

! function(t) {
    function e(t, e) { return function(n) { return u(t.call(this, n), e) } }

    function n(t, e) { return function(n) { return this.lang().ordinal(t.call(this, n), e) } }

    function s() {}

    function i(t) { a(this, t) }

    function r(t) {
        var e = t.years || t.year || t.y || 0,
            n = t.months || t.month || t.M || 0,
            s = t.weeks || t.week || t.w || 0,
            i = t.days || t.day || t.d || 0,
            r = t.hours || t.hour || t.h || 0,
            a = t.minutes || t.minute || t.m || 0,
            o = t.seconds || t.second || t.s || 0,
            u = t.milliseconds || t.millisecond || t.ms || 0;
        this._input = t, this._milliseconds = u + 1e3 * o + 6e4 * a + 36e5 * r, this._days = i + 7 * s, this._months = n + 12 * e, this._data = {}, this._bubble()
    }

    function a(t, e) { for (var n in e) e.hasOwnProperty(n) && (t[n] = e[n]); return t }

    function o(t) { return 0 > t ? Math.ceil(t) : Math.floor(t) }

    function u(t, e) { for (var n = t + ""; n.length < e;) n = "0" + n; return n }

    function h(t, e, n, s) {
        var i, r, a = e._milliseconds,
            o = e._days,
            u = e._months;
        a && t._d.setTime(+t._d + a * n), (o || u) && (i = t.minute(), r = t.hour()), o && t.date(t.date() + o * n), u && t.month(t.month() + u * n), a && !s && H.updateOffset(t), (o || u) && (t.minute(i), t.hour(r))
    }

    function d(t) { return "[object Array]" === Object.prototype.toString.call(t) }

    function c(t, e) {
        var n, s = Math.min(t.length, e.length),
            i = Math.abs(t.length - e.length),
            r = 0;
        for (n = 0; s > n; n++) ~~t[n] !== ~~e[n] && r++;
        return r + i
    }

    function f(t) { return t ? ie[t] || t.toLowerCase().replace(/(.)s$/, "$1") : t }

    function l(t, e) { return e.abbr = t, x[t] || (x[t] = new s), x[t].set(e), x[t] }

    function _(t) {
        if (!t) return H.fn._lang;
        if (!x[t] && A) try { require("./lang/" + t) } catch (e) { return H.fn._lang }
        return x[t]
    }

    function m(t) { return t.match(/\[.*\]/) ? t.replace(/^\[|\]$/g, "") : t.replace(/\\/g, "") }

    function y(t) { var e, n, s = t.match(E); for (e = 0, n = s.length; n > e; e++) s[e] = ue[s[e]] ? ue[s[e]] : m(s[e]); return function(i) { var r = ""; for (e = 0; n > e; e++) r += s[e] instanceof Function ? s[e].call(i, t) : s[e]; return r } }

    function M(t, e) {
        function n(e) { return t.lang().longDateFormat(e) || e }
        for (var s = 5; s-- && N.test(e);) e = e.replace(N, n);
        return re[e] || (re[e] = y(e)), re[e](t)
    }

    function g(t, e) {
        switch (t) {
            case "DDDD":
                return V;
            case "YYYY":
                return X;
            case "YYYYY":
                return $;
            case "S":
            case "SS":
            case "SSS":
            case "DDD":
                return I;
            case "MMM":
            case "MMMM":
            case "dd":
            case "ddd":
            case "dddd":
                return R;
            case "a":
            case "A":
                return _(e._l)._meridiemParse;
            case "X":
                return B;
            case "Z":
            case "ZZ":
                return j;
            case "T":
                return q;
            case "MM":
            case "DD":
            case "YY":
            case "HH":
            case "hh":
            case "mm":
            case "ss":
            case "M":
            case "D":
            case "d":
            case "H":
            case "h":
            case "m":
            case "s":
                return J;
            default:
                return new RegExp(t.replace("\\", ""))
        }
    }

    function p(t) {
        var e = (j.exec(t) || [])[0],
            n = (e + "").match(ee) || ["-", 0, 0],
            s = +(60 * n[1]) + ~~n[2];
        return "+" === n[0] ? -s : s
    }

    function D(t, e, n) {
        var s, i = n._a;
        switch (t) {
            case "M":
            case "MM":
                i[1] = null == e ? 0 : ~~e - 1;
                break;
            case "MMM":
            case "MMMM":
                s = _(n._l).monthsParse(e), null != s ? i[1] = s : n._isValid = !1;
                break;
            case "D":
            case "DD":
            case "DDD":
            case "DDDD":
                null != e && (i[2] = ~~e);
                break;
            case "YY":
                i[0] = ~~e + (~~e > 68 ? 1900 : 2e3);
                break;
            case "YYYY":
            case "YYYYY":
                i[0] = ~~e;
                break;
            case "a":
            case "A":
                n._isPm = _(n._l).isPM(e);
                break;
            case "H":
            case "HH":
            case "h":
            case "hh":
                i[3] = ~~e;
                break;
            case "m":
            case "mm":
                i[4] = ~~e;
                break;
            case "s":
            case "ss":
                i[5] = ~~e;
                break;
            case "S":
            case "SS":
            case "SSS":
                i[6] = ~~(1e3 * ("0." + e));
                break;
            case "X":
                n._d = new Date(1e3 * parseFloat(e));
                break;
            case "Z":
            case "ZZ":
                n._useUTC = !0, n._tzm = p(e)
        }
        null == e && (n._isValid = !1)
    }

    function Y(t) {
        var e, n, s = [];
        if (!t._d) {
            for (e = 0; 7 > e; e++) t._a[e] = s[e] = null == t._a[e] ? 2 === e ? 1 : 0 : t._a[e];
            s[3] += ~~((t._tzm || 0) / 60), s[4] += ~~((t._tzm || 0) % 60), n = new Date(0), t._useUTC ? (n.setUTCFullYear(s[0], s[1], s[2]), n.setUTCHours(s[3], s[4], s[5], s[6])) : (n.setFullYear(s[0], s[1], s[2]), n.setHours(s[3], s[4], s[5], s[6])), t._d = n
        }
    }

    function w(t) {
        var e, n, s = t._f.match(E),
            i = t._i;
        for (t._a = [], e = 0; e < s.length; e++) n = (g(s[e], t).exec(i) || [])[0], n && (i = i.slice(i.indexOf(n) + n.length)), ue[s[e]] && D(s[e], n, t);
        i && (t._il = i), t._isPm && t._a[3] < 12 && (t._a[3] += 12), t._isPm === !1 && 12 === t._a[3] && (t._a[3] = 0), Y(t)
    }

    function k(t) {
        var e, n, s, r, o, u = 99;
        for (r = 0; r < t._f.length; r++) e = a({}, t), e._f = t._f[r], w(e), n = new i(e), o = c(e._a, n.toArray()), n._il && (o += n._il.length), u > o && (u = o, s = n);
        a(t, s)
    }

    function v(t) {
        var e, n = t._i,
            s = K.exec(n);
        if (s) {
            for (t._f = "YYYY-MM-DD" + (s[2] || " "), e = 0; 4 > e; e++)
                if (te[e][1].exec(n)) { t._f += te[e][0]; break }
            j.exec(n) && (t._f += " Z"), w(t)
        } else t._d = new Date(n)
    }

    function T(e) {
        var n = e._i,
            s = G.exec(n);
        n === t ? e._d = new Date : s ? e._d = new Date(+s[1]) : "string" == typeof n ? v(e) : d(n) ? (e._a = n.slice(0), Y(e)) : e._d = n instanceof Date ? new Date(+n) : new Date(n)
    }

    function b(t, e, n, s, i) { return i.relativeTime(e || 1, !!n, t, s) }

    function S(t, e, n) {
        var s = W(Math.abs(t) / 1e3),
            i = W(s / 60),
            r = W(i / 60),
            a = W(r / 24),
            o = W(a / 365),
            u = 45 > s && ["s", s] || 1 === i && ["m"] || 45 > i && ["mm", i] || 1 === r && ["h"] || 22 > r && ["hh", r] || 1 === a && ["d"] || 25 >= a && ["dd", a] || 45 >= a && ["M"] || 345 > a && ["MM", W(a / 30)] || 1 === o && ["y"] || ["yy", o];
        return u[2] = e, u[3] = t > 0, u[4] = n, b.apply({}, u)
    }

    function F(t, e, n) {
        var s, i = n - e,
            r = n - t.day();
        return r > i && (r -= 7), i - 7 > r && (r += 7), s = H(t).add("d", r), { week: Math.ceil(s.dayOfYear() / 7), year: s.year() }
    }

    function O(t) {
        var e = t._i,
            n = t._f;
        return null === e || "" === e ? null : ("string" == typeof e && (t._i = e = _().preparse(e)), H.isMoment(e) ? (t = a({}, e), t._d = new Date(+e._d)) : n ? d(n) ? k(t) : w(t) : T(t), new i(t))
    }

    function z(t, e) { H.fn[t] = H.fn[t + "s"] = function(t) { var n = this._isUTC ? "UTC" : ""; return null != t ? (this._d["set" + n + e](t), H.updateOffset(this), this) : this._d["get" + n + e]() } }

    function C(t) { H.duration.fn[t] = function() { return this._data[t] } }

    function L(t, e) { H.duration.fn["as" + t] = function() { return +this / e } }
    for (var H, P, U = "2.1.0", W = Math.round, x = {}, A = "undefined" != typeof module && module.exports, G = /^\/?Date\((\-?\d+)/i, Z = /(\-)?(\d*)?\.?(\d+)\:(\d+)\:(\d+)\.?(\d{3})?/, E = /(\[[^\[]*\])|(\\)?(Mo|MM?M?M?|Do|DDDo|DD?D?D?|ddd?d?|do?|w[o|w]?|W[o|W]?|YYYYY|YYYY|YY|gg(ggg?)?|GG(GGG?)?|e|E|a|A|hh?|HH?|mm?|ss?|SS?S?|X|zz?|ZZ?|.)/g, N = /(\[[^\[]*\])|(\\)?(LT|LL?L?L?|l{1,4})/g, J = /\d\d?/, I = /\d{1,3}/, V = /\d{3}/, X = /\d{1,4}/, $ = /[+\-]?\d{1,6}/, R = /[0-9]*['a-z\u00A0-\u05FF\u0700-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+|[\u0600-\u06FF\/]+(\s*?[\u0600-\u06FF]+){1,2}/i, j = /Z|[\+\-]\d\d:?\d\d/i, q = /T/i, B = /[\+\-]?\d+(\.\d{1,3})?/, K = /^\s*\d{4}-\d\d-\d\d((T| )(\d\d(:\d\d(:\d\d(\.\d\d?\d?)?)?)?)?([\+\-]\d\d:?\d\d)?)?/, Q = "YYYY-MM-DDTHH:mm:ssZ", te = [
            ["HH:mm:ss.S", /(T| )\d\d:\d\d:\d\d\.\d{1,3}/],
            ["HH:mm:ss", /(T| )\d\d:\d\d:\d\d/],
            ["HH:mm", /(T| )\d\d:\d\d/],
            ["HH", /(T| )\d\d/]
        ], ee = /([\+\-]|\d\d)/gi, ne = "Date|Hours|Minutes|Seconds|Milliseconds".split("|"), se = { Milliseconds: 1, Seconds: 1e3, Minutes: 6e4, Hours: 36e5, Days: 864e5, Months: 2592e6, Years: 31536e6 }, ie = { ms: "millisecond", s: "second", m: "minute", h: "hour", d: "day", w: "week", M: "month", y: "year" }, re = {}, ae = "DDD w W M D d".split(" "), oe = "M D H h m s w W".split(" "), ue = {
            M: function() { return this.month() + 1 },
            MMM: function(t) { return this.lang().monthsShort(this, t) },
            MMMM: function(t) { return this.lang().months(this, t) },
            D: function() { return this.date() },
            DDD: function() { return this.dayOfYear() },
            d: function() { return this.day() },
            dd: function(t) { return this.lang().weekdaysMin(this, t) },
            ddd: function(t) { return this.lang().weekdaysShort(this, t) },
            dddd: function(t) { return this.lang().weekdays(this, t) },
            w: function() { return this.week() },
            W: function() { return this.isoWeek() },
            YY: function() { return u(this.year() % 100, 2) },
            YYYY: function() { return u(this.year(), 4) },
            YYYYY: function() { return u(this.year(), 5) },
            gg: function() { return u(this.weekYear() % 100, 2) },
            gggg: function() { return this.weekYear() },
            ggggg: function() { return u(this.weekYear(), 5) },
            GG: function() { return u(this.isoWeekYear() % 100, 2) },
            GGGG: function() { return this.isoWeekYear() },
            GGGGG: function() { return u(this.isoWeekYear(), 5) },
            e: function() { return this.weekday() },
            E: function() { return this.isoWeekday() },
            a: function() { return this.lang().meridiem(this.hours(), this.minutes(), !0) },
            A: function() { return this.lang().meridiem(this.hours(), this.minutes(), !1) },
            H: function() { return this.hours() },
            h: function() { return this.hours() % 12 || 12 },
            m: function() { return this.minutes() },
            s: function() { return this.seconds() },
            S: function() { return ~~(this.milliseconds() / 100) },
            SS: function() { return u(~~(this.milliseconds() / 10), 2) },
            SSS: function() { return u(this.milliseconds(), 3) },
            Z: function() {
                var t = -this.zone(),
                    e = "+";
                return 0 > t && (t = -t, e = "-"), e + u(~~(t / 60), 2) + ":" + u(~~t % 60, 2)
            },
            ZZ: function() {
                var t = -this.zone(),
                    e = "+";
                return 0 > t && (t = -t, e = "-"), e + u(~~(10 * t / 6), 4)
            },
            z: function() { return this.zoneAbbr() },
            zz: function() { return this.zoneName() },
            X: function() { return this.unix() }
        }; ae.length;) P = ae.pop(), ue[P + "o"] = n(ue[P], P);
    for (; oe.length;) P = oe.pop(), ue[P + P] = e(ue[P], 2);
    for (ue.DDDD = e(ue.DDD, 3), s.prototype = {
            set: function(t) { var e, n; for (n in t) e = t[n], "function" == typeof e ? this[n] = e : this["_" + n] = e },
            _months: "January_February_March_April_May_June_July_August_September_October_November_December".split("_"),
            months: function(t) { return this._months[t.month()] },
            _monthsShort: "Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec".split("_"),
            monthsShort: function(t) { return this._monthsShort[t.month()] },
            monthsParse: function(t) {
                var e, n, s;
                for (this._monthsParse || (this._monthsParse = []), e = 0; 12 > e; e++)
                    if (this._monthsParse[e] || (n = H([2e3, e]), s = "^" + this.months(n, "") + "|^" + this.monthsShort(n, ""), this._monthsParse[e] = new RegExp(s.replace(".", ""), "i")), this._monthsParse[e].test(t)) return e
            },
            _weekdays: "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),
            weekdays: function(t) { return this._weekdays[t.day()] },
            _weekdaysShort: "Sun_Mon_Tue_Wed_Thu_Fri_Sat".split("_"),
            weekdaysShort: function(t) { return this._weekdaysShort[t.day()] },
            _weekdaysMin: "Su_Mo_Tu_We_Th_Fr_Sa".split("_"),
            weekdaysMin: function(t) { return this._weekdaysMin[t.day()] },
            weekdaysParse: function(t) {
                var e, n, s;
                for (this._weekdaysParse || (this._weekdaysParse = []), e = 0; 7 > e; e++)
                    if (this._weekdaysParse[e] || (n = H([2e3, 1]).day(e), s = "^" + this.weekdays(n, "") + "|^" + this.weekdaysShort(n, "") + "|^" + this.weekdaysMin(n, ""), this._weekdaysParse[e] = new RegExp(s.replace(".", ""), "i")), this._weekdaysParse[e].test(t)) return e
            },
            _longDateFormat: { LT: "h:mm A", L: "MM/DD/YYYY", LL: "MMMM D YYYY", LLL: "MMMM D YYYY LT", LLLL: "dddd, MMMM D YYYY LT" },
            longDateFormat: function(t) { var e = this._longDateFormat[t]; return !e && this._longDateFormat[t.toUpperCase()] && (e = this._longDateFormat[t.toUpperCase()].replace(/MMMM|MM|DD|dddd/g, function(t) { return t.slice(1) }), this._longDateFormat[t] = e), e },
            isPM: function(t) { return "p" === (t + "").toLowerCase()[0] },
            _meridiemParse: /[ap]\.?m?\.?/i,
            meridiem: function(t, e, n) { return t > 11 ? n ? "pm" : "PM" : n ? "am" : "AM" },
            _calendar: { sameDay: "[Today at] LT", nextDay: "[Tomorrow at] LT", nextWeek: "dddd [at] LT", lastDay: "[Yesterday at] LT", lastWeek: "[Last] dddd [at] LT", sameElse: "L" },
            calendar: function(t, e) { var n = this._calendar[t]; return "function" == typeof n ? n.apply(e) : n },
            _relativeTime: { future: "in %s", past: "%s ago", s: "a few seconds", m: "a minute", mm: "%d minutes", h: "an hour", hh: "%d hours", d: "a day", dd: "%d days", M: "a month", MM: "%d months", y: "a year", yy: "%d years" },
            relativeTime: function(t, e, n, s) { var i = this._relativeTime[n]; return "function" == typeof i ? i(t, e, n, s) : i.replace(/%d/i, t) },
            pastFuture: function(t, e) { var n = this._relativeTime[t > 0 ? "future" : "past"]; return "function" == typeof n ? n(e) : n.replace(/%s/i, e) },
            ordinal: function(t) { return this._ordinal.replace("%d", t) },
            _ordinal: "%d",
            preparse: function(t) { return t },
            postformat: function(t) { return t },
            week: function(t) { return F(t, this._week.dow, this._week.doy).week },
            _week: { dow: 0, doy: 6 }
        }, H = function(t, e, n) { return O({ _i: t, _f: e, _l: n, _isUTC: !1 }) }, H.utc = function(t, e, n) { return O({ _useUTC: !0, _isUTC: !0, _l: n, _i: t, _f: e }) }, H.unix = function(t) { return H(1e3 * t) }, H.duration = function(t, e) {
            var n, s, i = H.isDuration(t),
                a = "number" == typeof t,
                o = i ? t._input : a ? {} : t,
                u = Z.exec(t);
            return a ? e ? o[e] = t : o.milliseconds = t : u && (n = "-" === u[1] ? -1 : 1, o = { y: 0, d: ~~u[2] * n, h: ~~u[3] * n, m: ~~u[4] * n, s: ~~u[5] * n, ms: ~~u[6] * n }), s = new r(o), i && t.hasOwnProperty("_lang") && (s._lang = t._lang), s
        }, H.version = U, H.defaultFormat = Q, H.updateOffset = function() {}, H.lang = function(t, e) { return t ? (e ? l(t, e) : x[t] || _(t), H.duration.fn._lang = H.fn._lang = _(t), void 0) : H.fn._lang._abbr }, H.langData = function(t) { return t && t._lang && t._lang._abbr && (t = t._lang._abbr), _(t) }, H.isMoment = function(t) { return t instanceof i }, H.isDuration = function(t) { return t instanceof r }, H.fn = i.prototype = {
            clone: function() { return H(this) },
            valueOf: function() { return +this._d + 6e4 * (this._offset || 0) },
            unix: function() { return Math.floor(+this / 1e3) },
            toString: function() { return this.format("ddd MMM DD YYYY HH:mm:ss [GMT]ZZ") },
            toDate: function() { return this._offset ? new Date(+this) : this._d },
            toISOString: function() { return M(H(this).utc(), "YYYY-MM-DD[T]HH:mm:ss.SSS[Z]") },
            toArray: function() { var t = this; return [t.year(), t.month(), t.date(), t.hours(), t.minutes(), t.seconds(), t.milliseconds()] },
            isValid: function() { return null == this._isValid && (this._isValid = this._a ? !c(this._a, (this._isUTC ? H.utc(this._a) : H(this._a)).toArray()) : !isNaN(this._d.getTime())), !!this._isValid },
            utc: function() { return this.zone(0) },
            local: function() { return this.zone(0), this._isUTC = !1, this },
            format: function(t) { var e = M(this, t || H.defaultFormat); return this.lang().postformat(e) },
            add: function(t, e) { var n; return n = "string" == typeof t ? H.duration(+e, t) : H.duration(t, e), h(this, n, 1), this },
            subtract: function(t, e) { var n; return n = "string" == typeof t ? H.duration(+e, t) : H.duration(t, e), h(this, n, -1), this },
            diff: function(t, e, n) {
                var s, i, r = this._isUTC ? H(t).zone(this._offset || 0) : H(t).local(),
                    a = 6e4 * (this.zone() - r.zone());
                return e = f(e), "year" === e || "month" === e ? (s = 432e5 * (this.daysInMonth() + r.daysInMonth()), i = 12 * (this.year() - r.year()) + (this.month() - r.month()), i += (this - H(this).startOf("month") - (r - H(r).startOf("month"))) / s, i -= 6e4 * (this.zone() - H(this).startOf("month").zone() - (r.zone() - H(r).startOf("month").zone())) / s, "year" === e && (i /= 12)) : (s = this - r, i = "second" === e ? s / 1e3 : "minute" === e ? s / 6e4 : "hour" === e ? s / 36e5 : "day" === e ? (s - a) / 864e5 : "week" === e ? (s - a) / 6048e5 : s), n ? i : o(i)
            },
            from: function(t, e) { return H.duration(this.diff(t)).lang(this.lang()._abbr).humanize(!e) },
            fromNow: function(t) { return this.from(H(), t) },
            calendar: function() {
                var t = this.diff(H().startOf("day"), "days", !0),
                    e = -6 > t ? "sameElse" : -1 > t ? "lastWeek" : 0 > t ? "lastDay" : 1 > t ? "sameDay" : 2 > t ? "nextDay" : 7 > t ? "nextWeek" : "sameElse";
                return this.format(this.lang().calendar(e, this))
            },
            isLeapYear: function() { var t = this.year(); return 0 === t % 4 && 0 !== t % 100 || 0 === t % 400 },
            isDST: function() { return this.zone() < this.clone().month(0).zone() || this.zone() < this.clone().month(5).zone() },
            day: function(t) { var e = this._isUTC ? this._d.getUTCDay() : this._d.getDay(); return null != t ? "string" == typeof t && (t = this.lang().weekdaysParse(t), "number" != typeof t) ? this : this.add({ d: t - e }) : e },
            month: function(t) { var e, n = this._isUTC ? "UTC" : ""; return null != t ? "string" == typeof t && (t = this.lang().monthsParse(t), "number" != typeof t) ? this : (e = this.date(), this.date(1), this._d["set" + n + "Month"](t), this.date(Math.min(e, this.daysInMonth())), H.updateOffset(this), this) : this._d["get" + n + "Month"]() },
            startOf: function(t) {
                switch (t = f(t)) {
                    case "year":
                        this.month(0);
                    case "month":
                        this.date(1);
                    case "week":
                    case "day":
                        this.hours(0);
                    case "hour":
                        this.minutes(0);
                    case "minute":
                        this.seconds(0);
                    case "second":
                        this.milliseconds(0)
                }
                return "week" === t && this.weekday(0), this
            },
            endOf: function(t) { return this.startOf(t).add(t, 1).subtract("ms", 1) },
            isAfter: function(t, e) { return e = "undefined" != typeof e ? e : "millisecond", +this.clone().startOf(e) > +H(t).startOf(e) },
            isBefore: function(t, e) { return e = "undefined" != typeof e ? e : "millisecond", +this.clone().startOf(e) < +H(t).startOf(e) },
            isSame: function(t, e) { return e = "undefined" != typeof e ? e : "millisecond", +this.clone().startOf(e) === +H(t).startOf(e) },
            min: function(t) { return t = H.apply(null, arguments), this > t ? this : t },
            max: function(t) { return t = H.apply(null, arguments), t > this ? this : t },
            zone: function(t) { var e = this._offset || 0; return null == t ? this._isUTC ? e : this._d.getTimezoneOffset() : ("string" == typeof t && (t = p(t)), Math.abs(t) < 16 && (t = 60 * t), this._offset = t, this._isUTC = !0, e !== t && h(this, H.duration(e - t, "m"), 1, !0), this) },
            zoneAbbr: function() { return this._isUTC ? "UTC" : "" },
            zoneName: function() { return this._isUTC ? "Coordinated Universal Time" : "" },
            daysInMonth: function() { return H.utc([this.year(), this.month() + 1, 0]).date() },
            dayOfYear: function(t) { var e = W((H(this).startOf("day") - H(this).startOf("year")) / 864e5) + 1; return null == t ? e : this.add("d", t - e) },
            weekYear: function(t) { var e = F(this, this.lang()._week.dow, this.lang()._week.doy).year; return null == t ? e : this.add("y", t - e) },
            isoWeekYear: function(t) { var e = F(this, 1, 4).year; return null == t ? e : this.add("y", t - e) },
            week: function(t) { var e = this.lang().week(this); return null == t ? e : this.add("d", 7 * (t - e)) },
            isoWeek: function(t) { var e = F(this, 1, 4).week; return null == t ? e : this.add("d", 7 * (t - e)) },
            weekday: function(t) { var e = (this._d.getDay() + 7 - this.lang()._week.dow) % 7; return null == t ? e : this.add("d", t - e) },
            isoWeekday: function(t) { return null == t ? this.day() || 7 : this.day(this.day() % 7 ? t : t - 7) },
            lang: function(e) { return e === t ? this._lang : (this._lang = _(e), this) }
        }, P = 0; P < ne.length; P++) z(ne[P].toLowerCase().replace(/s$/, ""), ne[P]);
    z("year", "FullYear"), H.fn.days = H.fn.day, H.fn.months = H.fn.month, H.fn.weeks = H.fn.week, H.fn.isoWeeks = H.fn.isoWeek, H.fn.toJSON = H.fn.toISOString, H.duration.fn = r.prototype = {
        _bubble: function() {
            var t, e, n, s, i = this._milliseconds,
                r = this._days,
                a = this._months,
                u = this._data;
            u.milliseconds = i % 1e3, t = o(i / 1e3), u.seconds = t % 60, e = o(t / 60), u.minutes = e % 60, n = o(e / 60), u.hours = n % 24, r += o(n / 24), u.days = r % 30, a += o(r / 30), u.months = a % 12, s = o(a / 12), u.years = s
        },
        weeks: function() { return o(this.days() / 7) },
        valueOf: function() { return this._milliseconds + 864e5 * this._days + 2592e6 * (this._months % 12) + 31536e6 * ~~(this._months / 12) },
        humanize: function(t) {
            var e = +this,
                n = S(e, !t, this.lang());
            return t && (n = this.lang().pastFuture(e, n)), this.lang().postformat(n)
        },
        add: function(t, e) { var n = H.duration(t, e); return this._milliseconds += n._milliseconds, this._days += n._days, this._months += n._months, this._bubble(), this },
        subtract: function(t, e) { var n = H.duration(t, e); return this._milliseconds -= n._milliseconds, this._days -= n._days, this._months -= n._months, this._bubble(), this },
        get: function(t) { return t = f(t), this[t.toLowerCase() + "s"]() },
        as: function(t) { return t = f(t), this["as" + t.charAt(0).toUpperCase() + t.slice(1) + "s"]() },
        lang: H.fn.lang
    };
    for (P in se) se.hasOwnProperty(P) && (L(P, se[P]), C(P.toLowerCase()));
    L("Weeks", 6048e5), H.duration.fn.asMonths = function() { return (+this - 31536e6 * this.years()) / 2592e6 + 12 * this.years() }, H.lang("en", {
        ordinal: function(t) {
            var e = t % 10,
                n = 1 === ~~(t % 100 / 10) ? "th" : 1 === e ? "st" : 2 === e ? "nd" : 3 === e ? "rd" : "th";
            return t + n
        }
    }), A && (module.exports = H), "undefined" == typeof ender && (this.moment = H), "function" == typeof define && define.amd && define("moment", [], function() { return H })
}.call(this);

! function(a, b) { "use strict"; "function" == typeof define && define.amd ? define(["jquery"], b) : "object" == typeof exports ? module.exports = b(require("jquery")) : a.bootbox = b(a.jQuery) }(this, function a(b, c) {
    "use strict";

    function d(a) { var b = q[o.locale]; return b ? b[a] : q.en[a] }

    function e(a, c, d) {
        a.stopPropagation(), a.preventDefault();
        var e = b.isFunction(d) && d(a) === !1;
        e || c.modal("hide")
    }

    function f(a) { var b, c = 0; for (b in a) c++; return c }

    function g(a, c) {
        var d = 0;
        b.each(a, function(a, b) { c(a, b, d++) })
    }

    function h(a) {
        var c, d;
        if ("object" != typeof a) throw new Error("Please supply an object of options");
        if (!a.message) throw new Error("Please specify a message");
        return a = b.extend({}, o, a), a.buttons || (a.buttons = {}), a.backdrop = a.backdrop ? "static" : !1, c = a.buttons, d = f(c), g(c, function(a, e, f) {
            if (b.isFunction(e) && (e = c[a] = { callback: e }), "object" !== b.type(e)) throw new Error("button with key " + a + " must be an object");
            e.label || (e.label = a), e.className || (e.className = 2 >= d && f === d - 1 ? "btn-primary" : "btn-default")
        }), a
    }

    function i(a, b) {
        var c = a.length,
            d = {};
        if (1 > c || c > 2) throw new Error("Invalid argument length");
        return 2 === c || "string" == typeof a[0] ? (d[b[0]] = a[0], d[b[1]] = a[1]) : d = a[0], d
    }

    function j(a, c, d) { return b.extend(!0, {}, a, i(c, d)) }

    function k(a, b, c, d) { var e = { className: "bootbox-" + a, buttons: l.apply(null, b) }; return m(j(e, d, c), b) }

    function l() {
        for (var a = {}, b = 0, c = arguments.length; c > b; b++) {
            var e = arguments[b],
                f = e.toLowerCase(),
                g = e.toUpperCase();
            a[f] = { label: d(g) }
        }
        return a
    }

    function m(a, b) { var d = {}; return g(b, function(a, b) { d[b] = !0 }), g(a.buttons, function(a) { if (d[a] === c) throw new Error("button key " + a + " is not allowed (options are " + b.join("\n") + ")") }), a }
    var n = { dialog: "<div class='bootbox modal' tabindex='-1' role='dialog'><div class='modal-dialog modal-sm'><div class='modal-content'><div class='modal-body'><div class='bootbox-body'></div></div></div></div></div>", header: "<div class='modal-header'><h4 class='modal-title'></h4></div>", footer: "<div class='modal-footer'></div>", closeButton: "<button type='button' class='bootbox-close-button close' data-dismiss='modal' aria-hidden='true'><i class='fa fa-2x'>&times;</i></button>", form: "<form class='bootbox-form'></form>", inputs: { text: "<input class='bootbox-input bootbox-input-text form-control' autocomplete=off type=text />", textarea: "<textarea class='bootbox-input bootbox-input-textarea form-control'></textarea>", email: "<input class='bootbox-input bootbox-input-email form-control' autocomplete='off' type='email' />", select: "<select class='bootbox-input bootbox-input-select form-control'></select>", checkbox: "<div class='checkbox'><label><input class='bootbox-input bootbox-input-checkbox' type='checkbox' /></label></div>", date: "<input class='bootbox-input bootbox-input-date form-control' autocomplete=off type='date' />", time: "<input class='bootbox-input bootbox-input-time form-control' autocomplete=off type='time' />", number: "<input class='bootbox-input bootbox-input-number form-control' autocomplete=off type='number' />", password: "<input class='bootbox-input bootbox-input-password form-control' autocomplete='off' type='password' />" } },
        o = { locale: "en", backdrop: !0, animate: !0, className: null, closeButton: !0, show: !0, container: "body" },
        p = {};
    p.alert = function() { var a; if (a = k("alert", ["ok"], ["message", "callback"], arguments), a.callback && !b.isFunction(a.callback)) throw new Error("alert requires callback property to be a function when provided"); return a.buttons.ok.callback = a.onEscape = function() { return b.isFunction(a.callback) ? a.callback() : !0 }, p.dialog(a) }, p.confirm = function() { var a; if (a = k("confirm", ["cancel", "confirm"], ["message", "callback"], arguments), a.buttons.cancel.callback = a.onEscape = function() { return a.callback(!1) }, a.buttons.confirm.callback = function() { return a.callback(!0) }, !b.isFunction(a.callback)) throw new Error("confirm requires a callback"); return p.dialog(a) }, p.prompt = function() {
        var a, d, e, f, h, i, k;
        f = b(n.form), d = { className: "bootbox-prompt", buttons: l("cancel", "confirm"), value: "", inputType: "text" }, a = m(j(d, arguments, ["title", "callback"]), ["cancel", "confirm"]), i = a.show === c ? !0 : a.show;
        var o = ["date", "time", "number"],
            q = document.createElement("input");
        if (q.setAttribute("type", a.inputType), o[a.inputType] && (a.inputType = q.type), a.message = f, a.buttons.cancel.callback = a.onEscape = function() { return a.callback(null) }, a.buttons.confirm.callback = function() {
                var c;
                switch (a.inputType) {
                    case "text":
                    case "textarea":
                    case "email":
                    case "select":
                    case "date":
                    case "time":
                    case "number":
                    case "password":
                        c = h.val();
                        break;
                    case "checkbox":
                        var d = h.find("input:checked");
                        c = [], g(d, function(a, d) { c.push(b(d).val()) })
                }
                return a.callback(c)
            }, a.show = !1, !a.title) throw new Error("prompt requires a title");
        if (!b.isFunction(a.callback)) throw new Error("prompt requires a callback");
        if (!n.inputs[a.inputType]) throw new Error("invalid prompt type");
        switch (h = b(n.inputs[a.inputType]), a.inputType) {
            case "text":
            case "textarea":
            case "email":
            case "date":
            case "time":
            case "number":
            case "password":
                h.val(a.value);
                break;
            case "select":
                var r = {};
                if (k = a.inputOptions || [], !k.length) throw new Error("prompt with select requires options");
                g(k, function(a, d) {
                    var e = h;
                    if (d.value === c || d.text === c) throw new Error("given options in wrong format");
                    d.group && (r[d.group] || (r[d.group] = b("<optgroup/>").attr("label", d.group)), e = r[d.group]), e.append("<option value='" + d.value + "'>" + d.text + "</option>")
                }), g(r, function(a, b) { h.append(b) }), h.val(a.value);
                break;
            case "checkbox":
                var s = b.isArray(a.value) ? a.value : [a.value];
                if (k = a.inputOptions || [], !k.length) throw new Error("prompt with checkbox requires options");
                if (!k[0].value || !k[0].text) throw new Error("given options in wrong format");
                h = b("<div/>"), g(k, function(c, d) {
                    var e = b(n.inputs[a.inputType]);
                    e.find("input").attr("value", d.value), e.find("label").append(d.text), g(s, function(a, b) { b === d.value && e.find("input").prop("checked", !0) }), h.append(e)
                })
        }
        return a.placeholder && h.attr("placeholder", a.placeholder), a.pattern && h.attr("pattern", a.pattern), f.append(h), f.on("submit", function(a) { a.preventDefault(), e.find(".btn-primary").click() }), e = p.dialog(a), e.off("shown.bs.modal"), e.on("shown.bs.modal", function() { h.focus() }), i === !0 && e.modal("show"), e
    }, p.dialog = function(a) {
        a = h(a);
        var c = b(n.dialog),
            d = c.find(".modal-body"),
            f = a.buttons,
            i = "",
            j = { onEscape: a.onEscape };
        if (g(f, function(a, b) { i += "<button data-bb-handler='" + a + "' type='button' class='btn " + b.className + "'>" + b.label + "</button>", j[a] = b.callback }), d.find(".bootbox-body").html(a.message), a.animate === !0 && c.addClass("fade"), a.className && c.addClass(a.className), a.title && d.before(n.header), a.closeButton) {
            var k = b(n.closeButton);
            a.title ? c.find(".modal-header").prepend(k) : k.css("margin-top", "-10px").prependTo(d)
        }
        return a.title && c.find(".modal-title").html(a.title), i.length && (d.after(n.footer), c.find(".modal-footer").html(i)), c.on("hidden.bs.modal", function(a) { a.target === this && c.remove() }), c.on("shown.bs.modal", function() { c.find(".btn-primary:first").focus() }), c.on("escape.close.bb", function(a) { j.onEscape && e(a, c, j.onEscape) }), c.on("click", ".modal-footer button", function(a) {
            var d = b(this).data("bb-handler");
            e(a, c, j[d])
        }), c.on("click", ".bootbox-close-button", function(a) { e(a, c, j.onEscape) }), c.on("keyup", function(a) { 27 === a.which && c.trigger("escape.close.bb") }), b(a.container).append(c), c.modal({ backdrop: a.backdrop, keyboard: !1, show: !1 }), a.show && c.modal("show"), c
    }, p.setDefaults = function() {
        var a = {};
        2 === arguments.length ? a[arguments[0]] = arguments[1] : a = arguments[0], b.extend(o, a)
    }, p.hideAll = function() { b(".bootbox").modal("hide") };
    var q = { br: { OK: "OK", CANCEL: "Cancelar", CONFIRM: "Sim" }, da: { OK: "OK", CANCEL: "Annuller", CONFIRM: "Accepter" }, de: { OK: "OK", CANCEL: "Abbrechen", CONFIRM: "Akzeptieren" }, en: { OK: "OK", CANCEL: "Cancel", CONFIRM: "OK" }, es: { OK: "OK", CANCEL: "Cancelar", CONFIRM: "Aceptar" }, fi: { OK: "OK", CANCEL: "Peruuta", CONFIRM: "OK" }, fr: { OK: "OK", CANCEL: "Annuler", CONFIRM: "D'accord" }, he: { OK: "×�×™×©×•×¨", CANCEL: "×‘×™×˜×•×œ", CONFIRM: "×�×™×©×•×¨" }, it: { OK: "OK", CANCEL: "Annulla", CONFIRM: "Conferma" }, lt: { OK: "Gerai", CANCEL: "AtÅ¡aukti", CONFIRM: "Patvirtinti" }, lv: { OK: "Labi", CANCEL: "Atcelt", CONFIRM: "ApstiprinÄ�t" }, nl: { OK: "OK", CANCEL: "Annuleren", CONFIRM: "Accepteren" }, no: { OK: "OK", CANCEL: "Avbryt", CONFIRM: "OK" }, pl: { OK: "OK", CANCEL: "Anuluj", CONFIRM: "PotwierdÅº" }, ru: { OK: "OK", CANCEL: "ÐžÑ‚Ð¼ÐµÐ½Ð°", CONFIRM: "ÐŸÑ€Ð¸Ð¼ÐµÐ½Ð¸Ñ‚ÑŒ" }, sv: { OK: "OK", CANCEL: "Avbryt", CONFIRM: "OK" }, tr: { OK: "Tamam", CANCEL: "Ä°ptal", CONFIRM: "Onayla" }, zh_CN: { OK: "OK", CANCEL: "å�–æ¶ˆ", CONFIRM: "ç¡®è®¤" }, zh_TW: { OK: "OK", CANCEL: "å�–æ¶ˆ", CONFIRM: "ç¢ºèª�" } };
    return p.init = function(c) { return a(c || b) }, p
});
(function(c) {
    var a = 0;
    "use strict";
    var d = function(e) {
        this[0] = e.startOffset;
        this[1] = e.endOffset;
        this.range = e;
        return this;
    };
    d.prototype.equals = function() { return this[0] === this[1]; };
    c.fn.redactor = function(f) { var g = []; var e = Array.prototype.slice.call(arguments, 1); if (typeof f === "string") { this.each(function() { var j = c.data(this, "redactor"); if (typeof j !== "undefined" && c.isFunction(j[f])) { var h = j[f].apply(j, e); if (h !== undefined && h !== j) { g.push(h); } } else { return c.error('No such method "' + f + '" for Redactor'); } }); } else { this.each(function() { if (!c.data(this, "redactor")) { c.data(this, "redactor", b(this, f)); } }); } if (g.length === 0) { return this; } else { if (g.length === 1) { return g[0]; } else { return g; } } };

    //เปลียนจาก redactor ไปเป็น redactor
    function b(f, e) { return new b.prototype.init(f, e); }
    c.Redactor = b;
    c.Redactor.VERSION = "9.1.9";
    c.Redactor.opts = { rangy: false, iframe: false, fullpage: false, css: false, lang: "en", direction: "ltr", placeholder: false, wym: false, mobile: true, cleanup: true, tidyHtml: true, pastePlainText: false, removeEmptyTags: true, templateVars: false, xhtml: false, visual: true, focus: false, tabindex: false, autoresize: true, minHeight: false, maxHeight: false, shortcuts: true, autosave: false, autosaveInterval: 60, plugins: false, linkAnchor: true, linkEmail: true, linkProtocol: "http://", linkNofollow: false, linkSize: 50, imageFloatMargin: "10px", imageGetJson: false, imageUpload: false, imageUploadParam: "file", fileUpload: false, fileUploadParam: "file", clipboardUpload: true, clipboardUploadUrl: false, dragUpload: true, dnbImageTypes: ["image/png", "image/jpeg", "image/gif"], s3: false, uploadFields: false, observeImages: true, observeLinks: true, modalOverlay: true, tabSpaces: false, tabFocus: true, air: false, airButtons: ["formatting", "|", "bold", "italic", "deleted", "|", "unorderedlist", "orderedlist", "outdent", "indent"], toolbar: true, toolbarFixed: false, toolbarFixedTarget: document, toolbarFixedTopOffset: 0, toolbarFixedBox: false, toolbarExternal: false, buttonSource: true, buttonSeparator: '<li class="redactor_separator"></li>', buttonsCustom: {}, buttonsAdd: [], buttons: ["html", "|", "formatting", "|", "bold", "italic", "deleted", "|", "unorderedlist", "orderedlist", "outdent", "indent", "|", "image", "video", "file", "table", "link", "|", "alignment", "|", "horizontalrule"], activeButtons: ["deleted", "italic", "bold", "underline", "unorderedlist", "orderedlist", "alignleft", "aligncenter", "alignright", "justify", "table"], activeButtonsStates: { b: "bold", strong: "bold", i: "italic", em: "italic", del: "deleted", strike: "deleted", ul: "unorderedlist", ol: "orderedlist", u: "underline", tr: "table", td: "table", table: "table" }, activeButtonsAdd: false, formattingTags: ["p", "blockquote", "pre", "h1", "h2", "h3", "h4", "h5", "h6"], linebreaks: false, paragraphy: true, convertDivs: true, convertLinks: true, convertImageLinks: false, convertVideoLinks: false, formattingPre: false, phpTags: false, allowedTags: false, deniedTags: ["html", "head", "link", "body", "meta", "script", "style", "applet"], boldTag: "strong", italicTag: "em", indentValue: 20, buffer: [], rebuffer: [], textareamode: false, emptyHtml: "<p>&#x200b;</p>", invisibleSpace: "&#x200b;", rBlockTest: /^(P|H[1-6]|LI|ADDRESS|SECTION|HEADER|FOOTER|ASIDE|ARTICLE)$/i, alignmentTags: ["P", "H1", "H2", "H3", "H4", "H5", "H6", "DD", "DL", "DT", "DIV", "TD", "BLOCKQUOTE", "OUTPUT", "FIGCAPTION", "ADDRESS", "SECTION", "HEADER", "FOOTER", "ASIDE", "ARTICLE"], ownLine: ["area", "body", "head", "hr", "i?frame", "link", "meta", "noscript", "style", "script", "table", "tbody", "thead", "tfoot"], contOwnLine: ["li", "dt", "dt", "h[1-6]", "option", "script"], newLevel: ["blockquote", "div", "dl", "fieldset", "form", "frameset", "map", "ol", "p", "pre", "select", "td", "th", "tr", "ul"], blockLevelElements: ["P", "H1", "H2", "H3", "H4", "H5", "H6", "DD", "DL", "DT", "DIV", "LI", "BLOCKQUOTE", "OUTPUT", "FIGCAPTION", "PRE", "ADDRESS", "SECTION", "HEADER", "FOOTER", "ASIDE", "ARTICLE", "TD"], langs: { en: { html: "HTML", video: "Insert Video", image: "Insert Image", table: "Table", link: "Link", link_insert: "Insert link", link_edit: "Edit link", unlink: "Unlink", formatting: "Formatting", paragraph: "Normal text", quote: "Quote", code: "Code", header1: "Header 1", header2: "Header 2", header3: "Header 3", header4: "Header 4", header5: "Header 5", bold: "Bold", italic: "Italic", fontcolor: "Font Color", backcolor: "Back Color", unorderedlist: "Unordered List", orderedlist: "Ordered List", outdent: "Outdent", indent: "Indent", cancel: "Cancel", insert: "Insert", save: "Save", _delete: "Delete", insert_table: "Insert Table", insert_row_above: "Add Row Above", insert_row_below: "Add Row Below", insert_column_left: "Add Column Left", insert_column_right: "Add Column Right", delete_column: "Delete Column", delete_row: "Delete Row", delete_table: "Delete Table", rows: "Rows", columns: "Columns", add_head: "Add Head", delete_head: "Delete Head", title: "Title", image_position: "Position", none: "None", left: "Left", right: "Right", image_web_link: "Image Web Link", text: "Text", mailto: "Email", web: "URL", video_html_code: "Video Embed Code", file: "Insert File", upload: "Upload", download: "Download", choose: "Choose", or_choose: "Or choose", drop_file_here: "Drop file here", align_left: "Align text to the left", align_center: "Center text", align_right: "Align text to the right", align_justify: "Justify text", horizontalrule: "Insert Horizontal Rule", deleted: "Deleted", anchor: "Anchor", link_new_tab: "Open link in new tab", underline: "Underline", alignment: "Alignment", filename: "Name (optional)", edit: "Edit" } } };
    b.fn = c.Redactor.prototype = {
        keyCode: { BACKSPACE: 8, DELETE: 46, DOWN: 40, ENTER: 13, ESC: 27, TAB: 9, CTRL: 17, META: 91, LEFT: 37, LEFT_WIN: 91 },
        init: function(f, e) {
            this.rtePaste = false;
            this.$element = this.$source = c(f);
            this.uuid = a++;
            var g = c.extend(true, {}, c.Redactor.opts);
            this.opts = c.extend({}, g, this.$element.data(), e);
            this.start = true;
            this.dropdowns = [];
            this.sourceHeight = this.$source.css("height");
            this.sourceWidth = this.$source.css("width");
            if (this.opts.fullpage) { this.opts.iframe = true; }
            if (this.opts.linebreaks) { this.opts.paragraphy = false; }
            if (this.opts.paragraphy) { this.opts.linebreaks = false; }
            if (this.opts.toolbarFixedBox) { this.opts.toolbarFixed = true; }
            this.document = document;
            this.window = window;
            this.savedSel = false;
            this.cleanlineBefore = new RegExp("^<(/?" + this.opts.ownLine.join("|/?") + "|" + this.opts.contOwnLine.join("|") + ")[ >]");
            this.cleanlineAfter = new RegExp("^<(br|/?" + this.opts.ownLine.join("|/?") + "|/" + this.opts.contOwnLine.join("|/") + ")[ >]");
            this.cleannewLevel = new RegExp("^</?(" + this.opts.newLevel.join("|") + ")[ >]");
            this.rTestBlock = new RegExp("^(" + this.opts.blockLevelElements.join("|") + ")$", "i");
            if (this.opts.linebreaks === false) { if (this.opts.allowedTags !== false) { var h = ["strong", "em", "del"]; var j = ["b", "i", "strike"]; if (c.inArray("p", this.opts.allowedTags) === "-1") { this.opts.allowedTags.push("p"); } for (i in h) { if (c.inArray(h[i], this.opts.allowedTags) != "-1") { this.opts.allowedTags.push(j[i]); } } } if (this.opts.deniedTags !== false) { var l = c.inArray("p", this.opts.deniedTags); if (l !== "-1") { this.opts.deniedTags.splice(l, l); } } }
            if (this.browser("msie") || this.browser("opera")) { this.opts.buttons = this.removeFromArrayByValue(this.opts.buttons, "horizontalrule"); }
            this.opts.curLang = this.opts.langs[this.opts.lang];
            this.buildStart();
        },
        toolbarInit: function(e) { return { html: { title: e.html, func: "toggle" }, formatting: { title: e.formatting, func: "show", dropdown: { p: { title: e.paragraph, func: "formatBlocks" }, blockquote: { title: e.quote, func: "formatQuote", className: "redactor_format_blockquote" }, pre: { title: e.code, func: "formatBlocks", className: "redactor_format_pre" }, h1: { title: e.header1, func: "formatBlocks", className: "redactor_format_h1" }, h2: { title: e.header2, func: "formatBlocks", className: "redactor_format_h2" }, h3: { title: e.header3, func: "formatBlocks", className: "redactor_format_h3" }, h4: { title: e.header4, func: "formatBlocks", className: "redactor_format_h4" }, h5: { title: e.header5, func: "formatBlocks", className: "redactor_format_h5" } } }, bold: { title: e.bold, exec: "bold" }, italic: { title: e.italic, exec: "italic" }, deleted: { title: e.deleted, exec: "strikethrough" }, underline: { title: e.underline, exec: "underline" }, unorderedlist: { title: "&bull; " + e.unorderedlist, exec: "insertunorderedlist" }, orderedlist: { title: "1. " + e.orderedlist, exec: "insertorderedlist" }, outdent: { title: "< " + e.outdent, func: "indentingOutdent" }, indent: { title: "> " + e.indent, func: "indentingIndent" }, image: { title: e.image, func: "imageShow" }, video: { title: e.video, func: "videoShow" }, file: { title: e.file, func: "fileShow" }, table: { title: e.table, func: "show", dropdown: { insert_table: { title: e.insert_table, func: "tableShow" }, separator_drop1: { name: "separator" }, insert_row_above: { title: e.insert_row_above, func: "tableAddRowAbove" }, insert_row_below: { title: e.insert_row_below, func: "tableAddRowBelow" }, insert_column_left: { title: e.insert_column_left, func: "tableAddColumnLeft" }, insert_column_right: { title: e.insert_column_right, func: "tableAddColumnRight" }, separator_drop2: { name: "separator" }, add_head: { title: e.add_head, func: "tableAddHead" }, delete_head: { title: e.delete_head, func: "tableDeleteHead" }, separator_drop3: { name: "separator" }, delete_column: { title: e.delete_column, func: "tableDeleteColumn" }, delete_row: { title: e.delete_row, func: "tableDeleteRow" }, delete_table: { title: e.delete_table, func: "tableDeleteTable" } } }, link: { title: e.link, func: "show", dropdown: { link: { title: e.link_insert, func: "linkShow" }, unlink: { title: e.unlink, exec: "unlink" } } }, fontcolor: { title: e.fontcolor, func: "show" }, backcolor: { title: e.backcolor, func: "show" }, alignment: { title: e.alignment, func: "show", dropdown: { alignleft: { title: e.align_left, func: "alignmentLeft" }, aligncenter: { title: e.align_center, func: "alignmentCenter" }, alignright: { title: e.align_right, func: "alignmentRight" }, justify: { title: e.align_justify, func: "alignmentJustify" } } }, alignleft: { title: e.align_left, func: "alignmentLeft" }, aligncenter: { title: e.align_center, func: "alignmentCenter" }, alignright: { title: e.align_right, func: "alignmentRight" }, justify: { title: e.align_justify, func: "alignmentJustify" }, horizontalrule: { exec: "inserthorizontalrule", title: e.horizontalrule } }; },
        callback: function(e, f, g) { var h = this.opts[e + "Callback"]; if (c.isFunction(h)) { if (f === false) { return h.call(this, g); } else { return h.call(this, f, g); } } else { return g; } },
        destroy: function() {
            clearInterval(this.autosaveInterval);
            c(window).off(".redactor");
            this.$source.off("redactor-textarea");
            this.$element.off(".redactor").removeData("redactor");
            var f = this.get();
            if (this.opts.textareamode) {
                this.$box.after(this.$source);
                this.$box.remove();
                this.$source.val(f).show();
            } else {
                var e = this.$editor;
                if (this.opts.iframe) { e = this.$element; }
                this.$box.after(e);
                this.$box.remove();
                e.removeClass("redactor_editor").removeClass("redactor_editor_wym").removeAttr("contenteditable").html(f).show();
            }
            if (this.opts.air) { c("#redactor_air_" + this.uuid).remove(); }
        },
        getObject: function() { return c.extend({}, this); },
        getEditor: function() { return this.$editor; },
        getBox: function() { return this.$box; },
        getIframe: function() { return (this.opts.iframe) ? this.$frame : false; },
        getToolbar: function() { return (this.$toolbar) ? this.$toolbar : false; },
        get: function() { return this.$source.val(); },
        getCodeIframe: function() {
            this.$editor.removeAttr("contenteditable").removeAttr("dir");
            var e = this.outerHtml(this.$frame.contents().children());
            this.$editor.attr({ contenteditable: true, dir: this.opts.direction });
            return e;
        },
        set: function(e, f, g) {
            e = e.toString();
            e = e.replace(/\$/g, "&#36;");
            if (this.opts.fullpage) { this.setCodeIframe(e); } else { this.setEditor(e, f); }
            if (e == "") { g = false; }
            if (g !== false) { this.placeholderRemove(); }
        },
        setEditor: function(e, f) {
            if (f !== false) {
                e = this.cleanSavePreCode(e);
                e = this.cleanStripTags(e);
                e = this.cleanConvertProtected(e);
                e = this.cleanConvertInlineTags(e, true);
                if (this.opts.linebreaks === false) { e = this.cleanConverters(e); } else { e = e.replace(/<p(.*?)>([\w\W]*?)<\/p>/gi, "$2<br>"); }
            }
            e = e.replace(/&amp;#36;/g, "$");
            e = this.cleanEmpty(e);
            this.$editor.html(e);
            this.setNonEditable();
            this.setSpansVerified();
            this.sync();
        },
        setCodeIframe: function(e) {
            var f = this.iframePage();
            this.$frame[0].src = "about:blank";
            e = this.cleanConvertProtected(e);
            e = this.cleanConvertInlineTags(e);
            e = this.cleanRemoveSpaces(e);
            f.open();
            f.write(e);
            f.close();
            if (this.opts.fullpage) { this.$editor = this.$frame.contents().find("body").attr({ contenteditable: true, dir: this.opts.direction }); }
            this.setNonEditable();
            this.setSpansVerified();
            this.sync();
        },
        setFullpageOnInit: function(e) {
            e = this.cleanSavePreCode(e, true);
            e = this.cleanConverters(e);
            e = this.cleanEmpty(e);
            this.$editor.html(e);
            this.setNonEditable();
            this.setSpansVerified();
            this.sync();
        },
        setSpansVerified: function() {
            var f = this.$editor.find("span");
            var e = "inline";
            c.each(f, function() {
                var g = this.outerHTML;
                var j = new RegExp("<" + this.tagName, "gi");
                var h = g.replace(j, "<" + e);
                j = new RegExp("</" + this.tagName, "gi");
                h = h.replace(j, "</" + e);
                c(this).replaceWith(h);
            });
        },
        setSpansVerifiedHtml: function(e) { e = e.replace(/<span(.*?)>/, "<inline$1>"); return e.replace(/<\/span>/, "</inline>"); },
        setNonEditable: function() { this.$editor.find(".noneditable").attr("contenteditable", false); },
        sync: function() {
            var e = "";
            this.cleanUnverified();
            if (this.opts.fullpage) { e = this.getCodeIframe(); } else { e = this.$editor.html(); }
            e = this.syncClean(e);
            e = this.cleanRemoveEmptyTags(e);
            e = e.replace(/<\/li><(ul|ol)>([\w\W]*?)<\/(ul|ol)>/gi, "<$1>$2</$1></li>");
            if (c.trim(e) === "<br>") { e = ""; }
            if (this.opts.xhtml) {
                var f = ["br", "hr", "img", "link", "input", "meta"];
                c.each(f, function(g, h) { e = e.replace(new RegExp("<" + h + "(.*?[^/$]?)>", "gi"), "<" + h + "$1 />"); });
            }
            e = this.callback("syncBefore", false, e);
            this.$source.val(e);
            this.callback("syncAfter", false, e);
            if (this.start === false) { this.callback("change", false, e); }
        },
        syncClean: function(e) {
            if (!this.opts.fullpage) { e = this.cleanStripTags(e); }
            e = c.trim(e);
            e = this.placeholderRemoveFromCode(e);
            e = e.replace(/&#x200b;/gi, "");
            e = e.replace(/&#8203;/gi, "");
            e = e.replace(/<\/a>&nbsp;/gi, "</a> ");
            if (this.opts.linkNofollow) {
                e = e.replace(/<a(.*?)rel="nofollow"(.*?)>/gi, "<a$1$2>");
                e = e.replace(/<a(.*?)>/gi, '<a$1 rel="nofollow">');
            }
            e = e.replace("<!--?php", "<?php");
            e = e.replace("?-->", "?>");
            e = e.replace(/<(.*?)class="noeditable"(.*?) contenteditable="false"(.*?)>/gi, '<$1class="noeditable"$2$3>');
            e = e.replace(/ data-tagblock=""/gi, "");
            e = e.replace(/<br\s?\/?>\n?<\/(P|H[1-6]|LI|ADDRESS|SECTION|HEADER|FOOTER|ASIDE|ARTICLE)>/gi, "</$1>");
            e = e.replace(/<span(.*?)id="redactor-image-box"(.*?)>([\w\W]*?)<img(.*?)><\/span>/i, "$3<img$4>");
            e = e.replace(/<span(.*?)id="redactor-image-resizer"(.*?)>(.*?)<\/span>/i, "");
            e = e.replace(/<span(.*?)id="redactor-image-editter"(.*?)>(.*?)<\/span>/i, "");
            e = e.replace(/<font(.*?)>([\w\W]*?)<\/font>/gi, "$2");
            e = e.replace(/<span(.*?)>([\w\W]*?)<\/span>/gi, "$2");
            e = e.replace(/<inline>/gi, "<span>");
            e = e.replace(/<inline /gi, "<span ");
            e = e.replace(/<\/inline>/gi, "</span>");
            e = e.replace(/<span(.*?)class="redactor_placeholder"(.*?)>([\w\W]*?)<\/span>/gi, "");
            e = e.replace(/<span>([\w\W]*?)<\/span>/gi, "$1");
            e = e.replace(/&amp;/gi, "&");
            e = e.replace(/™/gi, "&trade;");
            e = e.replace(/©/gi, "&copy;");
            e = this.cleanReConvertProtected(e);
            return e;
        },
        buildStart: function() {
            this.content = "";
            this.$box = c('<div class="redactor_box" />');
            if (this.$source[0].tagName === "TEXTAREA") { this.opts.textareamode = true; }
            if (this.opts.mobile === false && this.isMobile()) { this.buildMobile(); } else {
                this.buildContent();
                if (this.opts.iframe) {
                    this.opts.autoresize = false;
                    this.iframeStart();
                } else { if (this.opts.textareamode) { this.buildFromTextarea(); } else { this.buildFromElement(); } }
                if (!this.opts.iframe) {
                    this.buildOptions();
                    this.buildAfter();
                }
            }
        },
        buildMobile: function() {
            if (!this.opts.textareamode) {
                this.$editor = this.$source;
                this.$editor.hide();
                this.$source = this.buildCodearea(this.$editor);
                this.$source.val(this.content);
            }
            this.$box.insertAfter(this.$source).append(this.$source);
        },
        buildContent: function() { if (this.opts.textareamode) { this.content = c.trim(this.$source.val()); } else { this.content = c.trim(this.$source.html()); } },
        buildFromTextarea: function() {
            this.$editor = c("<div />");
            this.$box.insertAfter(this.$source).append(this.$editor).append(this.$source);
            this.buildAddClasses(this.$editor);
            this.buildEnable();
        },
        buildFromElement: function() {
            this.$editor = this.$source;
            this.$source = this.buildCodearea(this.$editor);
            this.$box.insertAfter(this.$editor).append(this.$editor).append(this.$source);
            this.buildEnable();
        },
        buildCodearea: function(e) { return c("<textarea />").attr("name", e.attr("id")).css("height", this.sourceHeight); },
        buildAddClasses: function(e) { c.each(this.$source.get(0).className.split(/\s+/), function(f, g) { e.addClass("redactor_" + g); }); },
        buildEnable: function() {
            this.$editor.addClass("redactor_editor").attr({ contenteditable: true, dir: this.opts.direction });
            this.$source.attr("dir", this.opts.direction).hide();
            this.set(this.content, true, false);
        },
        buildOptions: function() {
            var e = this.$editor;
            if (this.opts.iframe) { e = this.$frame; }
            if (this.opts.tabindex) { e.attr("tabindex", this.opts.tabindex); }
            if (this.opts.minHeight) { e.css("min-height", this.opts.minHeight + "px"); }
            if (this.opts.maxHeight) {
                this.opts.autoresize = false;
                this.sourceHeight = this.opts.maxHeight;
            }
            if (this.opts.wym) { this.$editor.addClass("redactor_editor_wym"); }
            if (!this.opts.autoresize) { e.css("height", this.sourceHeight); }
        },
        buildAfter: function() {
            this.start = false;
            if (this.opts.toolbar) {
                this.opts.toolbar = this.toolbarInit(this.opts.curLang);
                this.toolbarBuild();
            }
            this.modalTemplatesInit();
            this.buildPlugins();
            this.buildBindKeyboard();
            if (this.opts.autosave) { this.autosave(); }
            setTimeout(c.proxy(this.observeStart, this), 4);
            if (this.browser("mozilla")) {
                try {
                    this.document.execCommand("enableObjectResizing", false, false);
                    this.document.execCommand("enableInlineTableEditing", false, false);
                } catch (f) {}
            }
            if (this.opts.focus) { setTimeout(c.proxy(this.focus, this), 100); }
            if (!this.opts.visual) {
                setTimeout(c.proxy(function() {
                    this.opts.visual = true;
                    this.toggle(false);
                }, this), 200);
            }
            this.callback("init");
        },
        buildBindKeyboard: function() {
            this.dblEnter = 0;
            if (this.opts.dragUpload && this.opts.imageUpload !== false) { this.$editor.on("drop.redactor", c.proxy(this.buildEventDrop, this)); }
            this.$editor.on("paste.redactor", c.proxy(this.buildEventPaste, this));
            this.$editor.on("keydown.redactor", c.proxy(this.buildEventKeydown, this));
            this.$editor.on("keyup.redactor", c.proxy(this.buildEventKeyup, this));
            if (c.isFunction(this.opts.textareaKeydownCallback)) { this.$source.on("keydown.redactor-textarea", c.proxy(this.opts.textareaKeydownCallback, this)); }
            if (c.isFunction(this.opts.focusCallback)) { this.$editor.on("focus.redactor", c.proxy(this.opts.focusCallback, this)); }
            var e;
            c(document).mousedown(function(f) { e = c(f.target); });
            this.$editor.on("blur.redactor", c.proxy(function(f) { if (!c(e).hasClass("redactor_toolbar") && c(e).parents(".redactor_toolbar").size() == 0) { this.selectall = false; if (c.isFunction(this.opts.blurCallback)) { this.callback("blur", f); } } }, this));
        },
        buildEventDrop: function(j) {
            j = j.originalEvent || j;
            if (window.FormData === undefined || !j.dataTransfer) { return true; }
            var h = j.dataTransfer.files.length;
            if (h == 0) { return true; }
            j.preventDefault();
            var g = j.dataTransfer.files[0];
            if (this.opts.dnbImageTypes !== false && this.opts.dnbImageTypes.indexOf(g.type) == -1) { return true; }
            this.bufferSet();
            var f = c('<div id="redactor-progress-drag" class="redactor-progress redactor-progress-striped"><div id="redactor-progress-bar" class="redactor-progress-bar" style="width: 100%;"></div></div>');
            c(document.body).append(f);
            if (this.opts.s3 === false) { this.dragUploadAjax(this.opts.imageUpload, g, true, f, j, this.opts.imageUploadParam); } else { this.s3uploadFile(g); }
        },
        buildEventPaste: function(g) {
            var h = false;
            if (this.browser("webkit") && navigator.userAgent.indexOf("Chrome") === -1) { var f = this.browser("version").split("."); if (f[0] < 536) { h = true; } }
            if (h) { return true; }
            if (this.browser("opera")) { return true; }
            if (this.opts.clipboardUpload && this.buildEventClipboardUpload(g)) { return true; }
            if (this.opts.cleanup) {
                this.rtePaste = true;
                this.selectionSave();
                if (!this.selectall) {
                    if (this.opts.autoresize === true && this.fullscreen !== true) {
                        this.$editor.height(this.$editor.height());
                        this.saveScroll = this.document.body.scrollTop;
                    } else { this.saveScroll = this.$editor.scrollTop(); }
                }
                var j = this.extractContent();
                setTimeout(c.proxy(function() {
                    var e = this.extractContent();
                    this.$editor.append(j);
                    this.selectionRestore();
                    var l = this.getFragmentHtml(e);
                    this.pasteClean(l);
                    if (this.opts.autoresize === true && this.fullscreen !== true) { this.$editor.css("height", "auto"); }
                }, this), 1);
            }
        },
        buildEventClipboardUpload: function(j) {
            var h = j.originalEvent || j;
            this.clipboardFilePaste = false;
            if (typeof(h.clipboardData) === "undefined") { return false; }
            if (h.clipboardData.items) {
                var g = h.clipboardData.items[0].getAsFile();
                if (g !== null) {
                    this.bufferSet();
                    this.clipboardFilePaste = true;
                    var f = new FileReader();
                    f.onload = c.proxy(this.pasteClipboardUpload, this);
                    f.readAsDataURL(g);
                    return true;
                }
            }
            return false;
        },
        buildEventKeydown: function(n) {
            if (this.rtePaste) { return false; }
            var r = n.which;
            var f = n.ctrlKey || n.metaKey;
            var p = this.getParent();
            var o = this.getCurrent();
            var j = this.getBlock();
            var h = false;
            this.callback("keydown", n);
            this.imageResizeHide(false);
            if ((p && c(p).get(0).tagName === "PRE") || (o && c(o).get(0).tagName === "PRE")) { h = true; if (r === this.keyCode.DOWN) { this.insertAfterLastElement(j); } }
            if (r === this.keyCode.DOWN) { if (p && c(p)[0].tagName === "BLOCKQUOTE") { this.insertAfterLastElement(p); } if (o && c(o)[0].tagName === "BLOCKQUOTE") { this.insertAfterLastElement(o); } if (p && c(p)[0].tagName === "P" && c(p).parent()[0].tagName == "BLOCKQUOTE") { this.insertAfterLastElement(p, c(p).parent()[0]); } if (o && c(o)[0].tagName === "P" && p && c(p)[0].tagName == "BLOCKQUOTE") { this.insertAfterLastElement(o, p); } }
            if (f && !n.shiftKey) { this.shortcuts(n, r); }
            if (f && r === 90 && !n.shiftKey && !n.altKey) { n.preventDefault(); if (this.opts.buffer.length) { this.bufferUndo(); } else { this.document.execCommand("undo", false, false); } return; } else { if (f && r === 90 && n.shiftKey && !n.altKey) { n.preventDefault(); if (this.opts.rebuffer.length != 0) { this.bufferRedo(); } else { this.document.execCommand("redo", false, false); } return; } }
            if (f && r === 65) { this.selectall = true; } else { if (r != this.keyCode.LEFT_WIN && !f) { this.selectall = false; } }
            if (r == this.keyCode.ENTER && !n.shiftKey && !n.ctrlKey && !n.metaKey) {
                if (this.browser("msie") && (p.nodeType == 1 && (p.tagName == "TD" || p.tagName == "TH"))) {
                    n.preventDefault();
                    this.bufferSet();
                    this.insertNode(document.createElement("br"));
                    this.callback("enter", n);
                    return false;
                }
                if (j && (j.tagName == "BLOCKQUOTE" || c(j).parent()[0].tagName == "BLOCKQUOTE")) {
                    if (this.isEndOfElement()) {
                        if (this.dblEnter == 1) {
                            var m;
                            var q;
                            if (j.tagName == "BLOCKQUOTE") {
                                q = "br";
                                m = j;
                            } else {
                                q = "p";
                                m = c(j).parent()[0];
                            }
                            n.preventDefault();
                            this.insertingAfterLastElement(m);
                            this.dblEnter = 0;
                            if (q == "p") { c(j).parent().find("p").last().remove(); } else {
                                var l = c.trim(c(j).html());
                                c(j).html(l.replace(/<br\s?\/?>$/i, ""));
                            }
                            return;
                        } else { this.dblEnter++; }
                    } else { this.dblEnter++; }
                }
                if (h === true) { return this.buildEventKeydownPre(n, o); } else {
                    if (!this.opts.linebreaks) {
                        if (j && this.opts.rBlockTest.test(j.tagName)) {
                            this.bufferSet();
                            setTimeout(c.proxy(function() {
                                var s = this.getBlock();
                                if (s.tagName === "DIV" && !c(s).hasClass("redactor_editor")) {
                                    var e = c("<p>" + this.opts.invisibleSpace + "</p>");
                                    c(s).replaceWith(e);
                                    this.selectionStart(e);
                                }
                            }, this), 1);
                        } else {
                            if (j === false) {
                                this.bufferSet();
                                var g = c("<p>" + this.opts.invisibleSpace + "</p>");
                                this.insertNode(g[0]);
                                this.selectionStart(g);
                                this.callback("enter", n);
                                return false;
                            }
                        }
                    }
                    if (this.opts.linebreaks) {
                        if (j && this.opts.rBlockTest.test(j.tagName)) {
                            this.bufferSet();
                            setTimeout(c.proxy(function() { var e = this.getBlock(); if ((e.tagName === "DIV" || e.tagName === "P") && !c(e).hasClass("redactor_editor")) { this.replaceLineBreak(e); } }, this), 1);
                        } else { return this.buildEventKeydownInsertLineBreak(n); }
                    }
                    if (j.tagName == "BLOCKQUOTE" || j.tagName == "FIGCAPTION") { return this.buildEventKeydownInsertLineBreak(n); }
                }
                this.callback("enter", n);
            } else {
                if (r === this.keyCode.ENTER && (n.ctrlKey || n.shiftKey)) {
                    this.bufferSet();
                    n.preventDefault();
                    this.insertLineBreak();
                }
            }
            if (r === this.keyCode.TAB && this.opts.shortcuts) { return this.buildEventKeydownTab(n, h); }
            if (r === this.keyCode.BACKSPACE) { this.buildEventKeydownBackspace(o); }
        },
        buildEventKeydownPre: function(h, g) {
            h.preventDefault();
            this.bufferSet();
            var f = c(g).parent().text();
            this.insertNode(document.createTextNode("\n"));
            if (f.search(/\s$/) == -1) { this.insertNode(document.createTextNode("\n")); }
            this.sync();
            this.callback("enter", h);
            return false;
        },
        buildEventKeydownTab: function(g, f) {
            if (!this.opts.tabFocus) { return true; }
            if (this.isEmpty(this.get()) && this.opts.tabSpaces === false) { return true; }
            g.preventDefault();
            if (f === true && !g.shiftKey) {
                this.bufferSet();
                this.insertNode(document.createTextNode("\t"));
                this.sync();
                return false;
            } else {
                if (this.opts.tabSpaces !== false) {
                    this.bufferSet();
                    this.insertNode(document.createTextNode(Array(this.opts.tabSpaces + 1).join("\u00a0")));
                    this.sync();
                    return false;
                } else { if (!g.shiftKey) { this.indentingIndent(); } else { this.indentingOutdent(); } }
            }
            return false;
        },
        buildEventKeydownBackspace: function(f) {
            if (typeof f.tagName !== "undefined" && /^(H[1-6])$/i.test(f.tagName)) {
                var e;
                if (this.opts.linebreaks === false) { e = c("<p>" + this.opts.invisibleSpace + "</p>"); } else { e = c("<br>" + this.opts.invisibleSpace); }
                c(f).replaceWith(e);
                this.selectionStart(e);
            }
            if (typeof f.nodeValue !== "undefined" && f.nodeValue !== null) { if (f.remove && f.nodeType === 3 && f.nodeValue.match(/[^/\u200B]/g) == null) { f.remove(); } }
        },
        buildEventKeydownInsertLineBreak: function(f) {
            this.bufferSet();
            f.preventDefault();
            this.insertLineBreak();
            this.callback("enter", f);
            return;
        },
        buildEventKeyup: function(m) {
            if (this.rtePaste) { return false; }
            var f = m.which;
            var h = this.getParent();
            var l = this.getCurrent();
            if (!this.opts.linebreaks && l.nodeType == 3 && (h == false || h.tagName == "BODY")) {
                var j = c("<p>").append(c(l).clone());
                c(l).replaceWith(j);
                var g = c(j).next();
                if (typeof(g[0]) !== "undefined" && g[0].tagName == "BR") { g.remove(); }
                this.selectionEnd(j);
            }
            if ((this.opts.convertLinks || this.opts.convertImageLinks || this.opts.convertVideoLinks) && f === this.keyCode.ENTER) { this.buildEventKeyupConverters(); }
            if (f === this.keyCode.DELETE || f === this.keyCode.BACKSPACE) { return this.formatEmpty(m); }
            this.callback("keyup", m);
            this.sync();
        },
        buildEventKeyupConverters: function() {
            this.formatLinkify(this.opts.linkProtocol, this.opts.convertLinks, this.opts.convertImageLinks, this.opts.convertVideoLinks, this.opts.linkSize);
            setTimeout(c.proxy(function() { if (this.opts.convertImageLinks) { this.observeImages(); } if (this.opts.observeLinks) { this.observeLinks(); } }, this), 5);
        },
        buildPlugins: function() {
            if (!this.opts.plugins) { return; }
            c.each(this.opts.plugins, c.proxy(function(e, f) { if (RedactorPlugins[f]) { c.extend(this, RedactorPlugins[f]); if (c.isFunction(RedactorPlugins[f].init)) { this.init(); } } }, this));
        },
        iframeStart: function() {
            this.iframeCreate();
            if (this.opts.textareamode) { this.iframeAppend(this.$source); } else {
                this.$sourceOld = this.$source.hide();
                this.$source = this.buildCodearea(this.$sourceOld);
                this.iframeAppend(this.$sourceOld);
            }
        },
        iframeAppend: function(e) {
            this.$source.attr("dir", this.opts.direction).hide();
            this.$box.insertAfter(e).append(this.$frame).append(this.$source);
        },
        iframeCreate: function() {
            this.$frame = c('<iframe style="width: 100%;" frameborder="0" />').one("load", c.proxy(function() {
                if (this.opts.fullpage) {
                    this.iframePage();
                    if (this.content === "") { this.content = this.opts.invisibleSpace; }
                    this.$frame.contents()[0].write(this.content);
                    this.$frame.contents()[0].close();
                    var e = setInterval(c.proxy(function() {
                        if (this.$frame.contents().find("body").html()) {
                            clearInterval(e);
                            this.iframeLoad();
                        }
                    }, this), 0);
                } else { this.iframeLoad(); }
            }, this));
        },
        iframeDoc: function() { return this.$frame[0].contentWindow.document; },
        iframePage: function() { var e = this.iframeDoc(); if (e.documentElement) { e.removeChild(e.documentElement); } return e; },
        iframeAddCss: function(e) { e = e || this.opts.css; if (this.isString(e)) { this.$frame.contents().find("head").append('<link rel="stylesheet" href="' + e + '" />'); } if (c.isArray(e)) { c.each(e, c.proxy(function(g, f) { this.iframeAddCss(f); }, this)); } },
        iframeLoad: function() {
            this.$editor = this.$frame.contents().find("body").attr({ contenteditable: true, dir: this.opts.direction });
            if (this.$editor[0]) {
                this.document = this.$editor[0].ownerDocument;
                this.window = this.document.defaultView || window;
            }
            this.iframeAddCss();
            if (this.opts.fullpage) { this.setFullpageOnInit(this.$editor.html()); } else { this.set(this.content, true, false); }
            this.buildOptions();
            this.buildAfter();
        },
        placeholderStart: function(e) {
            if (this.isEmpty(e)) {
                if (this.$element.attr("placeholder")) { this.opts.placeholder = this.$element.attr("placeholder"); }
                if (this.opts.placeholder === "") { this.opts.placeholder = false; }
                if (this.opts.placeholder !== false) {
                    this.opts.focus = false;
                    this.$editor.one("focus.redactor_placeholder", c.proxy(this.placeholderFocus, this));
                    return c('<span class="redactor_placeholder" data-redactor="verified">').attr("contenteditable", false).text(this.opts.placeholder);
                }
            }
            return false;
        },
        placeholderFocus: function() {
            this.$editor.find("span.redactor_placeholder").remove();
            var e = "";
            if (this.opts.linebreaks === false) { e = this.opts.emptyHtml; }
            this.$editor.off("focus.redactor_placeholder");
            this.$editor.html(e);
            if (this.opts.linebreaks === false) { this.selectionStart(this.$editor.children()[0]); } else { this.focus(); }
            this.sync();
        },
        placeholderRemove: function() {
            this.opts.placeholder = false;
            this.$editor.find("span.redactor_placeholder").remove();
            this.$editor.off("focus.redactor_placeholder");
        },
        placeholderRemoveFromCode: function(e) { return e.replace(/<span class="redactor_placeholder"(.*?)>(.*?)<\/span>/i, ""); },
        shortcuts: function(g, f) { if (!this.opts.shortcuts) { return; } if (!g.altKey) { if (f === 77) { this.shortcutsLoad(g, "removeFormat"); } else { if (f === 66) { this.shortcutsLoad(g, "bold"); } else { if (f === 73) { this.shortcutsLoad(g, "italic"); } else { if (f === 74) { this.shortcutsLoad(g, "insertunorderedlist"); } else { if (f === 75) { this.shortcutsLoad(g, "insertorderedlist"); } else { if (f === 72) { this.shortcutsLoad(g, "superscript"); } else { if (f === 76) { this.shortcutsLoad(g, "subscript"); } } } } } } } } else { if (f === 48) { this.shortcutsLoadFormat(g, "p"); } else { if (f === 49) { this.shortcutsLoadFormat(g, "h1"); } else { if (f === 50) { this.shortcutsLoadFormat(g, "h2"); } else { if (f === 51) { this.shortcutsLoadFormat(g, "h3"); } else { if (f === 52) { this.shortcutsLoadFormat(g, "h4"); } else { if (f === 53) { this.shortcutsLoadFormat(g, "h5"); } else { if (f === 54) { this.shortcutsLoadFormat(g, "h6"); } } } } } } } } },
        shortcutsLoad: function(g, f) {
            g.preventDefault();
            this.execCommand(f, false);
        },
        shortcutsLoadFormat: function(g, f) {
            g.preventDefault();
            this.formatBlocks(f);
        },
        focus: function() { if (!this.browser("opera")) { this.window.setTimeout(c.proxy(this.focusSet, this, true), 1); } else { this.$editor.focus(); } },
        focusEnd: function() { this.focusSet(); },
        focusSet: function(g) {
            this.$editor.focus();
            var e = this.getRange();
            e.selectNodeContents(this.$editor[0]);
            e.collapse(g || false);
            var f = this.getSelection();
            f.removeAllRanges();
            f.addRange(e);
        },
        toggle: function(e) { if (this.opts.visual) { this.toggleCode(e); } else { this.toggleVisual(); } },
        toggleVisual: function() {
            var e = this.$source.hide().val();
            if (typeof this.modified !== "undefined") { this.modified = this.cleanRemoveSpaces(this.modified, false) !== this.cleanRemoveSpaces(e, false); }
            if (this.modified) { if (this.opts.fullpage && e === "") { this.setFullpageOnInit(e); } else { this.set(e); if (this.opts.fullpage) { this.buildBindKeyboard(); } } }
            if (this.opts.iframe) { this.$frame.show(); } else { this.$editor.show(); }
            if (this.opts.fullpage) { this.$editor.attr("contenteditable", true); }
            this.$source.off("keydown.redactor-textarea-indenting");
            this.$editor.focus();
            this.selectionRestore();
            this.observeStart();
            this.buttonActiveVisual();
            this.buttonInactive("html");
            this.opts.visual = true;
        },
        toggleCode: function(g) {
            if (g !== false) { this.selectionSave(); }
            var e = null;
            if (this.opts.iframe) {
                e = this.$frame.height();
                if (this.opts.fullpage) { this.$editor.removeAttr("contenteditable"); }
                this.$frame.hide();
            } else {
                e = this.$editor.innerHeight();
                this.$editor.hide();
            }
            var f = this.$source.val();
            if (f !== "" && this.opts.tidyHtml) { this.$source.val(this.cleanHtml(f)); }
            this.modified = f;
            this.$source.height(e).show().focus();
            this.$source.on("keydown.redactor-textarea-indenting", this.textareaIndenting);
            this.buttonInactiveVisual();
            this.buttonActive("html");
            this.opts.visual = false;
        },
        textareaIndenting: function(g) {
            if (g.keyCode === 9) {
                var f = c(this);
                var h = f.get(0).selectionStart;
                f.val(f.val().substring(0, h) + "\t" + f.val().substring(f.get(0).selectionEnd));
                f.get(0).selectionStart = f.get(0).selectionEnd = h + 1;
                return false;
            }
        },
        autosave: function() {
            var e = false;
            this.autosaveInterval = setInterval(c.proxy(function() {
                var f = this.get();
                if (e !== f) {
                    c.ajax({
                        url: this.opts.autosave,
                        type: "post",
                        data: this.$source.attr("name") + "=" + escape(encodeURIComponent(f)),
                        success: c.proxy(function(g) {
                            this.callback("autosave", false, g);
                            e = f;
                        }, this)
                    });
                }
            }, this), this.opts.autosaveInterval * 1000);
        },
        toolbarBuild: function() {
            if (this.opts.air) { this.opts.buttons = this.opts.airButtons; } else {
                if (!this.opts.buttonSource) {
                    var f = this.opts.buttons.indexOf("html"),
                        g = this.opts.buttons[f + 1];
                    this.opts.buttons.splice(f, 1);
                    if (g === "|") { this.opts.buttons.splice(f, 1); }
                }
            }
            c.extend(this.opts.toolbar, this.opts.buttonsCustom);
            c.each(this.opts.buttonsAdd, c.proxy(function(h, j) { this.opts.buttons.push(j); }, this));
            if (this.opts.toolbar) { c.each(this.opts.toolbar.formatting.dropdown, c.proxy(function(h, j) { if (c.inArray(h, this.opts.formattingTags) == "-1") { delete this.opts.toolbar.formatting.dropdown[h]; } }, this)); }
            if (this.opts.buttons.length === 0) { return false; }
            this.airEnable();
            this.$toolbar = c("<ul>").addClass("redactor_toolbar").attr("id", "redactor_toolbar_" + this.uuid);
            if (this.opts.air) {
                this.$air = c('<div class="redactor_air">').attr("id", "redactor_air_" + this.uuid).hide();
                this.$air.append(this.$toolbar);
                c("body").append(this.$air);
            } else { if (this.opts.toolbarExternal) { c(this.opts.toolbarExternal).html(this.$toolbar); } else { this.$box.prepend(this.$toolbar); } }
            c.each(this.opts.buttons, c.proxy(function(j, l) {
                if (l === "|") { this.$toolbar.append(c(this.opts.buttonSeparator)); } else {
                    if (this.opts.toolbar[l]) {
                        var h = this.opts.toolbar[l];
                        if (this.opts.fileUpload === false && l === "file") { return true; }
                        this.$toolbar.append(c("<li>").append(this.buttonBuild(l, h)));
                    }
                }
            }, this));
            this.$toolbar.find("a").attr("tabindex", "-1");
            if (this.opts.toolbarFixed) {
                this.toolbarObserveScroll();
                c(this.opts.toolbarFixedTarget).on("scroll.redactor", c.proxy(this.toolbarObserveScroll, this));
            }
            if (this.opts.activeButtons) {
                var e = c.proxy(this.buttonActiveObserver, this);
                this.$editor.on("mouseup.redactor keyup.redactor", e);
            }
        },
        toolbarObserveScroll: function() {
            var j = c(this.opts.toolbarFixedTarget).scrollTop();
            var g = this.$box.offset().top;
            var h = 0;
            var e = g + this.$box.height() + 40;
            if (j > g) {
                var f = "100%";
                if (this.opts.toolbarFixedBox) {
                    h = this.$box.offset().left;
                    f = this.$box.innerWidth();
                    this.$toolbar.addClass("toolbar_fixed_box");
                }
                this.toolbarFixed = true;
                this.$toolbar.css({ position: "fixed", width: f, zIndex: 1005, top: this.opts.toolbarFixedTopOffset + "px", left: h });
                if (j < e) { this.$toolbar.css("visibility", "visible"); } else { this.$toolbar.css("visibility", "hidden"); }
            } else {
                this.toolbarFixed = false;
                this.$toolbar.css({ position: "relative", width: "auto", top: 0, left: h });
                if (this.opts.toolbarFixedBox) { this.$toolbar.removeClass("toolbar_fixed_box"); }
            }
        },
        airEnable: function() {
            if (!this.opts.air) { return; }
            this.$editor.on("mouseup.redactor keyup.redactor", this, c.proxy(function(g) {
                var j = this.getSelectionText();
                if (g.type === "mouseup" && j != "") { this.airShow(g); }
                if (g.type === "keyup" && g.shiftKey && j != "") {
                    var f = c(this.getElement(this.getSelection().focusNode)),
                        h = f.offset();
                    h.height = f.height();
                    this.airShow(h, true);
                }
            }, this));
        },
        airShow: function(l, f) {
            if (!this.opts.air) { return; }
            var j, h;
            c(".redactor_air").hide();
            if (f) {
                j = l.left;
                h = l.top + l.height + 14;
                if (this.opts.iframe) {
                    h += this.$box.position().top - c(this.document).scrollTop();
                    j += this.$box.position().left;
                }
            } else {
                var g = this.$air.innerWidth();
                j = l.clientX;
                if (c(this.document).width() < (j + g)) { j -= g; }
                h = l.clientY + 14;
                if (this.opts.iframe) {
                    h += this.$box.position().top;
                    j += this.$box.position().left;
                } else { h += c(this.document).scrollTop(); }
            }
            this.$air.css({ left: j + "px", top: h + "px" }).show();
            this.airBindHide();
        },
        airBindHide: function() {
            if (!this.opts.air) { return; }
            var e = c.proxy(function(f) {
                c(f).on("mousedown.redactor", c.proxy(function(g) {
                    if (c(g.target).closest(this.$toolbar).length === 0) {
                        this.$air.fadeOut(100);
                        this.selectionRemove();
                        c(f).off(g);
                    }
                }, this)).on("keydown.redactor", c.proxy(function(g) {
                    if (g.which === this.keyCode.ESC) { this.getSelection().collapseToStart(); }
                    this.$air.fadeOut(100);
                    c(f).off(g);
                }, this));
            }, this);
            e(document);
            if (this.opts.iframe) { e(this.document); }
        },
        airBindMousemoveHide: function() {
            if (!this.opts.air) { return; }
            var e = c.proxy(function(f) {
                c(f).on("mousemove.redactor", c.proxy(function(g) {
                    if (c(g.target).closest(this.$toolbar).length === 0) {
                        this.$air.fadeOut(100);
                        c(f).off(g);
                    }
                }, this));
            }, this);
            e(document);
            if (this.opts.iframe) { e(this.document); }
        },
        dropdownBuild: function(f, e) {
            c.each(e, c.proxy(function(j, h) {
                if (!h.className) { h.className = ""; }
                var g;
                if (h.name === "separator") { g = c('<a class="redactor_separator_drop">'); } else {
                    g = c('<a href="#" class="' + h.className + " redactor_dropdown_" + j + '">' + h.title + "</a>");
                    g.on("click", c.proxy(function(l) {
                        if (l.preventDefault) { l.preventDefault(); }
                        if (this.browser("msie")) { l.returnValue = false; }
                        if (h.callback) { h.callback.call(this, j, g, h, l); }
                        if (h.exec) { this.execCommand(h.exec, j); }
                        if (h.func) { this[h.func](j); }
                        this.buttonActiveObserver();
                        if (this.opts.air) { this.$air.fadeOut(100); }
                    }, this));
                }
                f.append(g);
            }, this));
        },
        dropdownShow: function(m, q) {
            if (!this.opts.visual) { m.preventDefault(); return false; }
            var n = this.$toolbar.find(".redactor_dropdown_box_" + q);
            var f = this.buttonGet(q);
            if (f.hasClass("dropact")) { this.dropdownHideAll(); } else {
                this.dropdownHideAll();
                this.buttonActive(q);
                f.addClass("dropact");
                var r = f.position();
                if (this.toolbarFixed) { r = f.offset(); }
                var o = n.width();
                if ((r.left + o) > c(document).width()) { r.left -= o; }
                var h = r.left + "px";
                var j = 29;
                var l = "absolute";
                var p = j + "px";
                if (this.opts.toolbarFixed && this.toolbarFixed) { l = "fixed"; } else { if (!this.opts.air) { p = r.top + j + "px"; } }
                n.css({ position: l, left: h, top: p }).show();
            }
            var g = c.proxy(function(s) { this.dropdownHide(s, n); }, this);
            c(document).one("click", g);
            this.$editor.one("click", g);
            m.stopPropagation();
            this.$editor.focus();
        },
        dropdownHideAll: function() {
            this.$toolbar.find("a.dropact").removeClass("redactor_act").removeClass("dropact");
            c(".redactor_dropdown").hide();
        },
        dropdownHide: function(g, f) {
            if (!c(g.target).hasClass("dropact")) {
                f.removeClass("dropact");
                this.dropdownHideAll();
            }
        },
        buttonBuild: function(h, e) {
            var f = c('<a href="javascript:;" title="' + e.title + '" tabindex="-1" class="redactor_btn redactor_btn_' + h + '"></a>');
            f.on("click", c.proxy(function(j) {
                if (j.preventDefault) { j.preventDefault(); }
                if (this.browser("msie")) { j.returnValue = false; }
                if (f.hasClass("redactor_button_disabled")) { return false; }
                if (this.isFocused() === false && !e.exec) { this.$editor.focus(); }
                if (e.exec) {
                    this.$editor.focus();
                    this.execCommand(e.exec, h);
                    this.airBindMousemoveHide();
                } else {
                    if (e.func && e.func !== "show") {
                        this[e.func](h);
                        this.airBindMousemoveHide();
                    } else {
                        if (e.callback) {
                            e.callback.call(this, h, f, e, j);
                            this.airBindMousemoveHide();
                        } else { if (e.dropdown) { this.dropdownShow(j, h); } }
                    }
                }
                this.buttonActiveObserver(false, h);
            }, this));
            if (e.dropdown) {
                var g = c('<div class="redactor_dropdown redactor_dropdown_box_' + h + '" style="display: none;">');
                g.appendTo(this.$toolbar);
                this.dropdownBuild(g, e.dropdown);
            }
            return f;
        },
        buttonGet: function(e) { if (!this.opts.toolbar) { return false; } return c(this.$toolbar.find("a.redactor_btn_" + e)); },
        buttonActiveToggle: function(f) { var e = this.buttonGet(f); if (e.hasClass("redactor_act")) { e.removeClass("redactor_act"); } else { e.addClass("redactor_act"); } },
        buttonActive: function(e) { this.buttonGet(e).addClass("redactor_act"); },
        buttonInactive: function(e) { this.buttonGet(e).removeClass("redactor_act"); },
        buttonInactiveAll: function(e) { c.each(this.opts.toolbar, c.proxy(function(f) { if (f != e) { this.buttonInactive(f); } }, this)); },
        buttonActiveVisual: function() { this.$toolbar.find("a.redactor_btn").not("a.redactor_btn_html").removeClass("redactor_button_disabled"); },
        buttonInactiveVisual: function() { this.$toolbar.find("a.redactor_btn").not("a.redactor_btn_html").addClass("redactor_button_disabled"); },
        buttonChangeIcon: function(e, f) { this.buttonGet(e).addClass("redactor_btn_" + f); },
        buttonRemoveIcon: function(e, f) { this.buttonGet(e).removeClass("redactor_btn_" + f); },
        buttonAddSeparator: function() { this.$toolbar.append(c(this.opts.buttonSeparator)); },
        buttonAddSeparatorAfter: function(e) { this.buttonGet(e).parent().after(c(this.opts.buttonSeparator)); },
        buttonAddSeparatorBefore: function(e) { this.buttonGet(e).parent().before(c(this.opts.buttonSeparator)); },
        buttonRemoveSeparatorAfter: function(e) { this.buttonGet(e).parent().next().remove(); },
        buttonRemoveSeparatorBefore: function(e) { this.buttonGet(e).parent().prev().remove(); },
        buttonSetRight: function(e) {
            if (!this.opts.toolbar) { return; }
            this.buttonGet(e).parent().addClass("redactor_btn_right");
        },
        buttonSetLeft: function(e) {
            if (!this.opts.toolbar) { return; }
            this.buttonGet(e).parent().removeClass("redactor_btn_right");
        },
        buttonAdd: function(f, g, j, h) {
            if (!this.opts.toolbar) { return; }
            var e = this.buttonBuild(f, { title: g, callback: j, dropdown: h });
            this.$toolbar.append(c("<li>").append(e));
        },
        buttonAddFirst: function(f, g, j, h) {
            if (!this.opts.toolbar) { return; }
            var e = this.buttonBuild(f, { title: g, callback: j, dropdown: h });
            this.$toolbar.prepend(c("<li>").append(e));
        },
        buttonAddAfter: function(m, f, h, l, j) { if (!this.opts.toolbar) { return; } var e = this.buttonBuild(f, { title: h, callback: l, dropdown: j }); var g = this.buttonGet(m); if (g.size() !== 0) { g.parent().after(c("<li>").append(e)); } else { this.$toolbar.append(c("<li>").append(e)); } },
        buttonAddBefore: function(j, f, h, m, l) { if (!this.opts.toolbar) { return; } var e = this.buttonBuild(f, { title: h, callback: m, dropdown: l }); var g = this.buttonGet(j); if (g.size() !== 0) { g.parent().before(c("<li>").append(e)); } else { this.$toolbar.append(c("<li>").append(e)); } },
        buttonRemove: function(e, g) {
            var f = this.buttonGet(e);
            if (g) { f.parent().next().remove(); }
            f.parent().removeClass("redactor_btn_right");
            f.remove();
        },
        buttonActiveObserver: function(h, l) {
            var f = this.getParent();
            this.buttonInactiveAll(l);
            if (h === false && l !== "html") { if (c.inArray(l, this.opts.activeButtons) != -1) { this.buttonActiveToggle(l); } return; }
            if (f && f.tagName === "A") { this.$toolbar.find("a.redactor_dropdown_link").text(this.opts.curLang.link_edit); } else { this.$toolbar.find("a.redactor_dropdown_link").text(this.opts.curLang.link_insert); }
            if (this.opts.activeButtonsAdd) {
                c.each(this.opts.activeButtonsAdd, c.proxy(function(e, m) { this.opts.activeButtons.push(m); }, this));
                c.extend(this.opts.activeButtonsStates, this.opts.activeButtonsAdd);
            }
            c.each(this.opts.activeButtonsStates, c.proxy(function(e, m) { if (c(f).closest(e, this.$editor.get()[0]).length != 0) { this.buttonActive(m); } }, this));
            var g = c(f).closest(this.opts.alignmentTags.toString().toLowerCase(), this.$editor[0]);
            if (g.length) {
                var j = g.css("text-align");
                switch (j) {
                    case "right":
                        this.buttonActive("alignright");
                        break;
                    case "center":
                        this.buttonActive("aligncenter");
                        break;
                    case "justify":
                        this.buttonActive("justify");
                        break;
                    default:
                        this.buttonActive("alignleft");
                        break;
                }
            }
        },
        execPasteFrag: function(e) {
            var j = this.getSelection();
            if (j.getRangeAt && j.rangeCount) {
                range = j.getRangeAt(0);
                range.deleteContents();
                var f = document.createElement("div");
                f.innerHTML = e;
                var m = document.createDocumentFragment(),
                    h, g;
                while ((h = f.firstChild)) { g = m.appendChild(h); }
                var l = m.firstChild;
                range.insertNode(m);
                if (g) {
                    range = range.cloneRange();
                    range.setStartAfter(g);
                    range.collapse(true);
                    j.removeAllRanges();
                    j.addRange(range);
                }
            }
        },
        exec: function(f, g, e) {
            if (f === "formatblock" && this.browser("msie")) { g = "<" + g + ">"; }
            if (f === "inserthtml" && this.browser("msie")) {
                if (!this.isIe11()) {
                    this.$editor.focus();
                    this.document.selection.createRange().pasteHTML(g);
                } else { this.execPasteFrag(g); }
            } else { this.document.execCommand(f, false, g); }
            if (e !== false) { this.sync(); }
            this.callback("execCommand", f, g);
        },
        execCommand: function(f, g, e) {
            if (!this.opts.visual) { this.$source.focus(); return false; }
            if (f === "inserthtml") {
                this.insertHtml(g, e);
                this.callback("execCommand", f, g);
                return;
            }
            if (this.currentOrParentIs("PRE") && !this.opts.formattingPre) { return false; }
            if (f === "insertunorderedlist" || f === "insertorderedlist") { return this.execLists(f, g); }
            if (f === "unlink") { return this.execUnlink(f, g); }
            this.exec(f, g, e);
            if (f === "inserthorizontalrule") { this.$editor.find("hr").removeAttr("id"); }
        },
        execUnlink: function(f, g) {
            this.bufferSet();
            var e = this.currentOrParentIs("A");
            if (e) {
                c(e).replaceWith(c(e).text());
                this.sync();
                this.callback("execCommand", f, g);
                return;
            }
        },
        execLists: function(j, h) {
            this.bufferSet();
            var r = this.getParent();
            var o = c(r).closest("ol, ul");
            var n = false;
            if (o.length) { n = true; var q = o[0].tagName; if ((j === "insertunorderedlist" && q === "OL") || (j === "insertorderedlist" && q === "UL")) { n = false; } }
            this.selectionSave();
            if (n) {
                var f = this.getNodes();
                var g = this.getBlocks(f);
                if (typeof f[0] != "undefined" && f.length > 1 && f[0].nodeType == 3) { g.unshift(this.getBlock()); }
                var m = "",
                    t = "";
                c.each(g, c.proxy(function(w, x) {
                    if (x.tagName == "LI") {
                        var v = c(x);
                        var u = v.clone();
                        u.find("ul", "ol").remove();
                        if (this.opts.linebreaks === false) { m += this.outerHtml(c("<p>").append(u.contents())); } else { m += u.html() + "<br>"; }
                        if (w == 0) {
                            v.addClass("redactor-replaced").empty();
                            t = this.outerHtml(v);
                        } else { v.remove(); }
                    }
                }, this));
                html = this.$editor.html().replace(t, "</" + q + ">" + m + "<" + q + ">");
                this.$editor.html(html);
                this.$editor.find(q + ":empty").remove();
            } else {
                var e = this.getParent();
                this.document.execCommand(j);
                var r = this.getParent();
                var o = c(r).closest("ol, ul");
                if (e && e.tagName == "TD") { o.wrapAll("<td>"); }
                if (o.length) {
                    var s = o.children().first();
                    if (c.trim(c(s).text()) == "") {
                        var l = c('<span id="selection-marker-1"></span>');
                        c(s).prepend(l);
                    }
                    if ((this.browser("msie") || this.browser("mozilla")) && r.tagName !== "LI") { c(r).replaceWith(c(r).html()); }
                    var p = o.parent();
                    if (this.isParentRedactor(p) && this.nodeTestBlocks(p[0])) { p.replaceWith(p.contents()); }
                }
                if (this.browser("mozilla")) { this.$editor.focus(); }
            }
            this.selectionRestore();
            c(l).remove();
            this.sync();
            this.callback("execCommand", j, h);
            return;
        },
        indentingIndent: function() { this.indentingStart("indent"); },
        indentingOutdent: function() { this.indentingStart("outdent"); },
        indentingStart: function(h) {
            this.bufferSet();
            if (h === "indent") {
                var j = this.getBlock();
                this.selectionSave();
                if (j && j.tagName == "LI") {
                    var o = this.getParent();
                    var l = c(o).closest("ol, ul");
                    var n = l[0].tagName;
                    var f = this.getBlocks();
                    c.each(f, function(t, u) { if (u.tagName == "LI") { var r = c(u).prev(); if (r.size() != 0 && r[0].tagName == "LI") { var q = r.children("ul, ol"); if (q.size() == 0) { r.append(c("<" + n + ">").append(u)); } else { q.append(u); } } } });
                } else {
                    if (j === false && this.opts.linebreaks === true) {
                        this.exec("formatBlock", "blockquote");
                        var p = this.getBlock();
                        var j = c('<div data-tagblock="">').html(c(p).html());
                        c(p).replaceWith(j);
                        var g = this.normalize(c(j).css("margin-left")) + this.opts.indentValue;
                        c(j).css("margin-left", g + "px");
                    } else {
                        var e = this.getBlocks();
                        c.each(e, c.proxy(function(r, s) {
                            var q = false;
                            if (s.tagName === "TD") { return; }
                            if (c.inArray(s.tagName, this.opts.alignmentTags) !== -1) { q = c(s); } else { q = c(s).closest(this.opts.alignmentTags.toString().toLowerCase(), this.$editor[0]); }
                            var t = this.normalize(q.css("margin-left")) + this.opts.indentValue;
                            q.css("margin-left", t + "px");
                        }, this));
                    }
                }
                this.selectionRestore();
            } else {
                this.selectionSave();
                var j = this.getBlock();
                if (j && j.tagName == "LI") {
                    var f = this.getBlocks();
                    var m = 0;
                    this.insideOutdent(j, m, f);
                } else {
                    var e = this.getBlocks();
                    c.each(e, c.proxy(function(r, s) {
                        var q = false;
                        if (c.inArray(s.tagName, this.opts.alignmentTags) !== -1) { q = c(s); } else { q = c(s).closest(this.opts.alignmentTags.toString().toLowerCase(), this.$editor[0]); }
                        var t = this.normalize(q.css("margin-left")) - this.opts.indentValue;
                        if (t <= 0) {
                            if (this.opts.linebreaks === true && typeof(q.data("tagblock")) !== "undefined") { q.replaceWith(q.html()); } else {
                                q.css("margin-left", "");
                                this.removeEmptyAttr(q, "style");
                            }
                        } else { q.css("margin-left", t + "px"); }
                    }, this));
                }
                this.selectionRestore();
            }
            this.sync();
        },
        insideOutdent: function(e, g, f) {
            if (e && e.tagName == "LI") {
                var h = c(e).parent().parent();
                if (h.size() != 0 && h[0].tagName == "LI") { h.after(e); } else {
                    if (typeof f[g] != "undefined") {
                        e = f[g];
                        g++;
                        this.insideOutdent(e, g, f);
                    } else { this.execCommand("insertunorderedlist"); }
                }
            }
        },
        alignmentLeft: function() { this.alignmentSet("", "JustifyLeft"); },
        alignmentRight: function() { this.alignmentSet("right", "JustifyRight"); },
        alignmentCenter: function() { this.alignmentSet("center", "JustifyCenter"); },
        alignmentJustify: function() { this.alignmentSet("justify", "JustifyFull"); },
        alignmentSet: function(f, h) {
            this.bufferSet();
            if (this.oldIE()) { this.document.execCommand(h, false, false); return true; }
            this.selectionSave();
            var j = this.getBlock();
            if (!j && this.opts.linebreaks) {
                this.exec("formatBlock", "blockquote");
                var e = this.getBlock();
                var j = c('<div data-tagblock="">').html(c(e).html());
                c(e).replaceWith(j);
                c(j).css("text-align", f);
                this.removeEmptyAttr(j, "style");
                if (f == "" && typeof(c(j).data("tagblock")) !== "undefined") { c(j).replaceWith(c(j).html()); }
            } else {
                var g = this.getBlocks();
                c.each(g, c.proxy(function(m, n) {
                    var l = false;
                    if (c.inArray(n.tagName, this.opts.alignmentTags) !== -1) { l = c(n); } else { l = c(n).closest(this.opts.alignmentTags.toString().toLowerCase(), this.$editor[0]); }
                    if (l) {
                        l.css("text-align", f);
                        this.removeEmptyAttr(l, "style");
                    }
                }, this));
            }
            this.selectionRestore();
            this.sync();
        },
        cleanEmpty: function(e) { var f = this.placeholderStart(e); if (f !== false) { return f; } if (this.opts.linebreaks === false) { if (e === "") { e = this.opts.emptyHtml; } else { if (e.search(/^<hr\s?\/?>$/gi) !== -1) { e = "<hr>" + this.opts.emptyHtml; } } } return e; },
        cleanConverters: function(e) { if (this.opts.convertDivs) { e = e.replace(/<div(.*?)>([\w\W]*?)<\/div>/gi, "<p$1>$2</p>"); } if (this.opts.paragraphy) { e = this.cleanParagraphy(e); } return e; },
        cleanConvertProtected: function(e) {
            if (this.opts.templateVars) {
                e = e.replace(/\{\{(.*?)\}\}/gi, "<!-- template double $1 -->");
                e = e.replace(/\{(.*?)\}/gi, "<!-- template $1 -->");
            }
            e = e.replace(/<script(.*?)>([\w\W]*?)<\/script>/gi, '<title type="text/javascript" style="display: none;" class="redactor-script-tag"$1>$2</title>');
            e = e.replace(/<style(.*?)>([\w\W]*?)<\/style>/gi, '<section$1 style="display: none;" rel="redactor-style-tag">$2</section>');
            e = e.replace(/<form(.*?)>([\w\W]*?)<\/form>/gi, '<section$1 rel="redactor-form-tag">$2</section>');
            if (this.opts.phpTags) { e = e.replace(/<\?php([\w\W]*?)\?>/gi, '<section style="display: none;" rel="redactor-php-tag">$1</section>'); } else { e = e.replace(/<\?php([\w\W]*?)\?>/gi, ""); }
            return e;
        },
        cleanReConvertProtected: function(e) {
            if (this.opts.templateVars) {
                e = e.replace(/<!-- template double (.*?) -->/gi, "{{$1}}");
                e = e.replace(/<!-- template (.*?) -->/gi, "{$1}");
            }
            e = e.replace(/<title type="text\/javascript" style="display: none;" class="redactor-script-tag"(.*?)>([\w\W]*?)<\/title>/gi, '<script$1 type="text/javascript">$2<\/script>');
            e = e.replace(/<section(.*?) style="display: none;" rel="redactor-style-tag">([\w\W]*?)<\/section>/gi, "<style$1>$2</style>");
            e = e.replace(/<section(.*?)rel="redactor-form-tag"(.*?)>([\w\W]*?)<\/section>/gi, "<form$1$2>$3</form>");
            if (this.opts.phpTags) { e = e.replace(/<section style="display: none;" rel="redactor-php-tag">([\w\W]*?)<\/section>/gi, "<?php\r\n$1\r\n?>"); }
            return e;
        },
        cleanRemoveSpaces: function(f, e) {
            if (e !== false) {
                var e = [];
                var h = f.match(/<(pre|style|script|title)(.*?)>([\w\W]*?)<\/(pre|style|script|title)>/gi);
                if (h === null) { h = []; }
                if (this.opts.phpTags) { var g = f.match(/<\?php([\w\W]*?)\?>/gi); if (g) { h = c.merge(h, g); } }
                if (h) {
                    c.each(h, function(j, l) {
                        f = f.replace(l, "buffer_" + j);
                        e.push(l);
                    });
                }
            }
            f = f.replace(/\n/g, " ");
            f = f.replace(/[\t]*/g, "");
            f = f.replace(/\n\s*\n/g, "\n");
            f = f.replace(/^[\s\n]*/g, " ");
            f = f.replace(/[\s\n]*$/g, " ");
            f = f.replace(/>\s{2,}</g, "> <");
            f = this.cleanReplacer(f, e);
            f = f.replace(/\n\n/g, "\n");
            return f;
        },
        cleanReplacer: function(f, e) {
            if (e === false) { return f; }
            c.each(e, function(g, h) { f = f.replace("buffer_" + g, h); });
            return f;
        },
        cleanRemoveEmptyTags: function(h) {
            h = h.replace(/<span>([\w\W]*?)<\/span>/gi, "$1");
            h = h.replace(/[\u200B-\u200D\uFEFF]/g, "");
            var j = ["<b>\\s*</b>", "<b>&nbsp;</b>", "<em>\\s*</em>"];
            var g = ["<pre></pre>", "<blockquote>\\s*</blockquote>", "<dd></dd>", "<dt></dt>", "<ul></ul>", "<ol></ol>", "<li></li>", "<table></table>", "<tr></tr>", "<span>\\s*<span>", "<span>&nbsp;<span>", "<p>\\s*</p>", "<p></p>", "<p>&nbsp;</p>", "<p>\\s*<br>\\s*</p>", "<div>\\s*</div>", "<div>\\s*<br>\\s*</div>"];
            if (this.opts.removeEmptyTags) { g = g.concat(j); } else { g = j; }
            var e = g.length;
            for (var f = 0; f < e; ++f) { h = h.replace(new RegExp(g[f], "gi"), ""); }
            return h;
        },
        cleanParagraphy: function(l) {
            l = c.trim(l);
            if (this.opts.linebreaks === true) { return l; }
            if (l === "" || l === "<p></p>") { return this.opts.emptyHtml; }
            l = l + "\n";
            var n = [];
            var j = l.match(/<(table|div|pre|object)(.*?)>([\w\W]*?)<\/(table|div|pre|object)>/gi);
            if (!j) { j = []; }
            var m = l.match(/<!--([\w\W]*?)-->/gi);
            if (m) { j = c.merge(j, m); }
            if (this.opts.phpTags) { var f = l.match(/<section(.*?)rel="redactor-php-tag">([\w\W]*?)<\/section>/gi); if (f) { j = c.merge(j, f); } }
            if (j) {
                c.each(j, function(p, q) {
                    n[p] = q;
                    l = l.replace(q, "{replace" + p + "}\n");
                });
            }
            l = l.replace(/<br \/>\s*<br \/>/gi, "\n\n");

            function h(s, p, q) { return l.replace(new RegExp(s, p), q); }
            var e = "(comment|html|body|head|title|meta|style|script|link|iframe|table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|option|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)";
            l = h("(<" + e + "[^>]*>)", "gi", "\n$1");
            l = h("(</" + e + ">)", "gi", "$1\n\n");
            l = h("\r\n", "g", "\n");
            l = h("\r", "g", "\n");
            l = h("/\n\n+/", "g", "\n\n");
            var o = l.split(new RegExp("\ns*\n", "g"), -1);
            l = "";
            for (var g in o) {
                if (o.hasOwnProperty(g)) {
                    if (o[g].search("{replace") == -1) {
                        o[g] = o[g].replace(/<p>\n\t<\/p>/gi, "");
                        o[g] = o[g].replace(/<p><\/p>/gi, "");
                        if (o[g] != "") { l += "<p>" + o[g].replace(/^\n+|\n+$/g, "") + "</p>"; }
                    } else { l += o[g]; }
                }
            }
            l = h("<p>s*</p>", "gi", "");
            l = h("<p>([^<]+)</(div|address|form)>", "gi", "<p>$1</p></$2>");
            l = h("<p>s*(</?" + e + "[^>]*>)s*</p>", "gi", "$1");
            l = h("<p>(<li.+?)</p>", "gi", "$1");
            l = h("<p>s*(</?" + e + "[^>]*>)", "gi", "$1");
            l = h("(</?" + e + "[^>]*>)s*</p>", "gi", "$1");
            l = h("(</?" + e + "[^>]*>)s*<br />", "gi", "$1");
            l = h("<br />(s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)", "gi", "$1");
            l = h("\n</p>", "gi", "</p>");
            l = h("<li><p>", "gi", "<li>");
            l = h("</p></li>", "gi", "</li>");
            l = h("</li><p>", "gi", "</li>");
            l = h("<p>\t?\n?<p>", "gi", "<p>");
            l = h("</dt><p>", "gi", "</dt>");
            l = h("</dd><p>", "gi", "</dd>");
            l = h("<br></p></blockquote>", "gi", "</blockquote>");
            l = h("<p>\t*</p>", "gi", "");
            c.each(n, function(p, q) { l = l.replace("{replace" + p + "}", q); });
            return c.trim(l);
        },
        cleanConvertInlineTags: function(e, h) {
            var f = "strong";
            if (this.opts.boldTag === "b") { f = "b"; }
            var g = "em";
            if (this.opts.italicTag === "i") { g = "i"; }
            e = e.replace(/<span style="font-style: italic;">([\w\W]*?)<\/span>/gi, "<" + g + ">$1</" + g + ">");
            e = e.replace(/<span style="font-weight: bold;">([\w\W]*?)<\/span>/gi, "<" + f + ">$1</" + f + ">");
            if (this.opts.boldTag === "strong") { e = e.replace(/<b>([\w\W]*?)<\/b>/gi, "<strong>$1</strong>"); } else { e = e.replace(/<strong>([\w\W]*?)<\/strong>/gi, "<b>$1</b>"); }
            if (this.opts.italicTag === "em") { e = e.replace(/<i>([\w\W]*?)<\/i>/gi, "<em>$1</em>"); } else { e = e.replace(/<em>([\w\W]*?)<\/em>/gi, "<i>$1</i>"); }
            if (h !== true) { e = e.replace(/<strike>([\w\W]*?)<\/strike>/gi, "<del>$1</del>"); } else { e = e.replace(/<del>([\w\W]*?)<\/del>/gi, "<strike>$1</strike>"); }
            return e;
        },
        cleanStripTags: function(g) {
            if (g == "" || typeof g == "undefined") { return g; }
            var h = false;
            if (this.opts.allowedTags !== false) { h = true; }
            var e = h === true ? this.opts.allowedTags : this.opts.deniedTags;
            var f = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
            g = g.replace(f, function(l, j) { if (h === true) { return c.inArray(j.toLowerCase(), e) > "-1" ? l : ""; } else { return c.inArray(j.toLowerCase(), e) > "-1" ? "" : l; } });
            g = this.cleanConvertInlineTags(g);
            return g;
        },
        cleanSavePreCode: function(e, f) {
            var g = e.match(/<(pre|code)(.*?)>([\w\W]*?)<\/(pre|code)>/gi);
            if (g !== null) {
                c.each(g, c.proxy(function(j, l) {
                    var h = l.match(/<(pre|code)(.*?)>([\w\W]*?)<\/(pre|code)>/i);
                    h[3] = h[3].replace(/&nbsp;/g, " ");
                    if (f !== false) { h[3] = this.cleanEncodeEntities(h[3]); }
                    h[3] = h[3].replace(/\$/g, "&#36;");
                    e = e.replace(l, "<" + h[1] + h[2] + ">" + h[3] + "</" + h[1] + ">");
                }, this));
            }
            return e;
        },
        cleanEncodeEntities: function(e) { e = String(e).replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, '"'); return e.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;"); },
        cleanUnverified: function() {
            var e = this.$editor.find("li, img, a, b, strong, sub, sup, i, em, u, small, strike, del, span, cite");
            e.filter('[style*="background-color: transparent;"][style*="line-height"]').css("background-color", "").css("line-height", "");
            e.filter('[style*="background-color: transparent;"]').css("background-color", "");
            e.css("line-height", "");
            c.each(e, c.proxy(function(f, g) { this.removeEmptyAttr(g, "style"); }, this));
            this.$editor.find('div[style="text-align: -webkit-auto;"]').contents().unwrap();
        },
        cleanHtml: function(f) {
            var j = 0,
                m = f.length,
                l = 0,
                e = null,
                g = null,
                p = "",
                h = "",
                o = "";
            this.cleanlevel = 0;
            for (; j < m; j++) {
                l = j;
                if (-1 == f.substr(j).indexOf("<")) { h += f.substr(j); return this.cleanFinish(h); }
                while (l < m && f.charAt(l) != "<") { l++; }
                if (j != l) {
                    o = f.substr(j, l - j);
                    if (!o.match(/^\s{2,}$/g)) {
                        if ("\n" == h.charAt(h.length - 1)) { h += this.cleanGetTabs(); } else {
                            if ("\n" == o.charAt(0)) {
                                h += "\n" + this.cleanGetTabs();
                                o = o.replace(/^\s+/, "");
                            }
                        }
                        h += o;
                    }
                    if (o.match(/\n/)) { h += "\n" + this.cleanGetTabs(); }
                }
                e = l;
                while (l < m && ">" != f.charAt(l)) { l++; }
                p = f.substr(e, l - e);
                j = l;
                var n;
                if ("!--" == p.substr(1, 3)) {
                    if (!p.match(/--$/)) {
                        while ("-->" != f.substr(l, 3)) { l++; }
                        l += 2;
                        p = f.substr(e, l - e);
                        j = l;
                    }
                    if ("\n" != h.charAt(h.length - 1)) { h += "\n"; }
                    h += this.cleanGetTabs();
                    h += p + ">\n";
                } else {
                    if ("!" == p[1]) { h = this.placeTag(p + ">", h); } else {
                        if ("?" == p[1]) { h += p + ">\n"; } else {
                            if (n = p.match(/^<(script|style|pre)/i)) {
                                n[1] = n[1].toLowerCase();
                                p = this.cleanTag(p);
                                h = this.placeTag(p, h);
                                g = String(f.substr(j + 1)).toLowerCase().indexOf("</" + n[1]);
                                if (g) {
                                    o = f.substr(j + 1, g);
                                    j += g;
                                    h += o;
                                }
                            } else {
                                p = this.cleanTag(p);
                                h = this.placeTag(p, h);
                            }
                        }
                    }
                }
            }
            return this.cleanFinish(h);
        },
        cleanGetTabs: function() { var f = ""; for (var e = 0; e < this.cleanlevel; e++) { f += "\t"; } return f; },
        cleanFinish: function(e) {
            e = e.replace(/\n\s*\n/g, "\n");
            e = e.replace(/^[\s\n]*/, "");
            e = e.replace(/[\s\n]*$/, "");
            e = e.replace(/<script(.*?)>\n<\/script>/gi, "<script$1><\/script>");
            this.cleanlevel = 0;
            return e;
        },
        cleanTag: function(f) {
            var h = "";
            f = f.replace(/\n/g, " ");
            f = f.replace(/\s{2,}/g, " ");
            f = f.replace(/^\s+|\s+$/g, " ");
            var g = "";
            if (f.match(/\/$/)) {
                g = "/";
                f = f.replace(/\/+$/, "");
            }
            var e;
            while (e = /\s*([^= ]+)(?:=((['"']).*?\3|[^ ]+))?/.exec(f)) {
                if (e[2]) { h += e[1].toLowerCase() + "=" + e[2]; } else { if (e[1]) { h += e[1].toLowerCase(); } }
                h += " ";
                f = f.substr(e[0].length);
            }
            return h.replace(/\s*$/, "") + g + ">";
        },
        placeTag: function(e, g) {
            var f = e.match(this.cleannewLevel);
            if (e.match(this.cleanlineBefore) || f) {
                g = g.replace(/\s*$/, "");
                g += "\n";
            }
            if (f && "/" == e.charAt(1)) { this.cleanlevel--; }
            if ("\n" == g.charAt(g.length - 1)) { g += this.cleanGetTabs(); }
            if (f && "/" != e.charAt(1)) { this.cleanlevel++; }
            g += e;
            if (e.match(this.cleanlineAfter) || e.match(this.cleannewLevel)) {
                g = g.replace(/ *$/, "");
                g += "\n";
            }
            return g;
        },
        formatEmpty: function(j) {
            var f = c.trim(this.$editor.html());
            if (this.opts.linebreaks) {
                if (f == "") {
                    j.preventDefault();
                    this.$editor.html("");
                    this.focus();
                }
            } else {
                f = f.replace(/<br\s?\/?>/i, "");
                var h = f.replace(/<p>\s?<\/p>/gi, "");
                if (f === "" || h === "") {
                    j.preventDefault();
                    var g = c(this.opts.emptyHtml).get(0);
                    this.$editor.html(g);
                    this.focus();
                }
            }
            this.sync();
        },
        formatBlocks: function(e) {
            this.bufferSet();
            var f = this.getBlocks();
            this.selectionSave();
            c.each(f, c.proxy(function(g, j) { if (j.tagName !== "LI") { var h = c(j).parent(); if (e === "p") { if ((j.tagName === "P" && h.size() != 0 && h[0].tagName === "BLOCKQUOTE") || j.tagName === "BLOCKQUOTE") { this.formatQuote(); return; } else { if (this.opts.linebreaks) { return; } else { this.formatBlock(e, j); } } } else { this.formatBlock(e, j); } } }, this));
            this.selectionRestore();
            this.sync();
        },
        formatBlock: function(e, j) {
            if (j === false) { j = this.getBlock(); }
            if (j === false) { if (this.opts.linebreaks === true) { this.execCommand("formatblock", e); } return true; }
            var h = "";
            if (e !== "pre") { h = c(j).contents(); } else { h = c(j).html(); if (c.trim(h) === "") { h = '<span id="selection-marker-1"></span>'; } }
            if (j.tagName === "PRE") { e = "p"; }
            if (this.opts.linebreaks === true && e === "p") { c(j).replaceWith(c("<div>").append(h).html() + "<br>"); } else {
                var f = this.getParent();
                var g = c("<" + e + ">").append(h);
                c(j).replaceWith(g);
                if (f && f.tagName == "TD") { c(g).wrapAll("<td>"); }
            }
        },
        formatChangeTag: function(g, e, f) {
            if (f !== false) { this.selectionSave(); }
            var h = c("<" + e + "/>");
            c(g).replaceWith(function() { return h.append(c(this).contents()); });
            if (f !== false) { this.selectionRestore(); }
            return h;
        },
        formatQuote: function() {
            this.bufferSet();
            if (this.opts.linebreaks === false) {
                this.selectionSave();
                var e = this.getBlocks();
                var p = false;
                var u = e.length;
                if (e) {
                    var m = "";
                    var v = "";
                    var h = false;
                    var r = true;
                    c.each(e, function(w, x) { if (x.tagName !== "P") { r = false; } });
                    c.each(e, c.proxy(function(w, x) {
                        if (x.tagName === "BLOCKQUOTE") { this.formatBlock("p", x, false); } else {
                            if (x.tagName === "P") {
                                p = c(x).parent();
                                if (p[0].tagName == "BLOCKQUOTE") {
                                    var y = c(p).children("p").size();
                                    if (y == 1) { c(p).replaceWith(x); } else {
                                        if (y == u) {
                                            h = "blockquote";
                                            m += this.outerHtml(x);
                                        } else {
                                            h = "html";
                                            m += this.outerHtml(x);
                                            if (w == 0) {
                                                c(x).addClass("redactor-replaced").empty();
                                                v = this.outerHtml(x);
                                            } else { c(x).remove(); }
                                        }
                                    }
                                } else {
                                    if (r === false || e.length == 1) { this.formatBlock("blockquote", x, false); } else {
                                        h = "paragraphs";
                                        m += this.outerHtml(x);
                                    }
                                }
                            } else { if (x.tagName !== "LI") { this.formatBlock("blockquote", x, false); } }
                        }
                    }, this));
                    if (h) {
                        if (h == "paragraphs") {
                            c(e[0]).replaceWith("<blockquote>" + m + "</blockquote>");
                            c(e).remove();
                        } else {
                            if (h == "blockquote") { c(p).replaceWith(m); } else {
                                if (h == "html") {
                                    var o = this.$editor.html().replace(v, "</blockquote>" + m + "<blockquote>");
                                    this.$editor.html(o);
                                    this.$editor.find("blockquote").each(function() { if (c.trim(c(this).html()) == "") { c(this).remove(); } });
                                }
                            }
                        }
                    }
                }
                this.selectionRestore();
            } else {
                var j = this.getBlock();
                if (j.tagName === "BLOCKQUOTE") {
                    this.selectionSave();
                    var o = c.trim(c(j).html());
                    var s = c.trim(this.getSelectionHtml());
                    o = o.replace(/<span(.*?)id="selection-marker(.*?)<\/span>/gi, "");
                    if (o == s) { c(j).replaceWith(c(j).html() + "<br>"); } else {
                        this.inlineFormat("tmp");
                        var l = this.$editor.find("tmp");
                        l.empty();
                        var q = this.$editor.html().replace("<tmp></tmp>", '</blockquote><span id="selection-marker-1">' + this.opts.invisibleSpace + "</span>" + s + "<blockquote>");
                        this.$editor.html(q);
                        l.remove();
                        this.$editor.find("blockquote").each(function() { if (c.trim(c(this).html()) == "") { c(this).remove(); } });
                    }
                    this.selectionRestore();
                    this.$editor.find("span#selection-marker-1").attr("id", false);
                } else {
                    var g = this.selectionWrap("blockquote");
                    var o = c(g).html();
                    var t = ["ul", "ol", "table", "tr", "tbody", "thead", "tfoot", "dl"];
                    c.each(t, function(w, x) {
                        o = o.replace(new RegExp("<" + x + "(.*?)>", "gi"), "");
                        o = o.replace(new RegExp("</" + x + ">", "gi"), "");
                    });
                    var f = this.opts.blockLevelElements;
                    f.push("td");
                    c.each(f, function(w, x) {
                        o = o.replace(new RegExp("<" + x + "(.*?)>", "gi"), "");
                        o = o.replace(new RegExp("</" + x + ">", "gi"), "<br>");
                    });
                    c(g).html(o);
                    this.selectionElement(g);
                    var n = c(g).next();
                    if (n.size() != 0 && n[0].tagName === "BR") { n.remove(); }
                }
            }
            this.sync();
        },
        blockRemoveAttr: function(e, g) {
            var f = this.getBlocks();
            c(f).removeAttr(e);
            this.sync();
        },
        blockSetAttr: function(e, g) {
            var f = this.getBlocks();
            c(f).attr(e, g);
            this.sync();
        },
        blockRemoveStyle: function(f) {
            var e = this.getBlocks();
            c(e).css(f, "");
            this.removeEmptyAttr(e, "style");
            this.sync();
        },
        blockSetStyle: function(g, f) {
            var e = this.getBlocks();
            c(e).css(g, f);
            this.sync();
        },
        blockRemoveClass: function(f) {
            var e = this.getBlocks();
            c(e).removeClass(f);
            this.removeEmptyAttr(e, "class");
            this.sync();
        },
        blockSetClass: function(f) {
            var e = this.getBlocks();
            c(e).addClass(f);
            this.sync();
        },
        inlineRemoveClass: function(e) {
            this.selectionSave();
            this.inlineEachNodes(function(f) {
                c(f).removeClass(e);
                this.removeEmptyAttr(f, "class");
            });
            this.selectionRestore();
            this.sync();
        },
        inlineSetClass: function(e) { var f = this.getCurrent(); if (!c(f).hasClass(e)) { this.inlineMethods("addClass", e); } },
        inlineRemoveStyle: function(e) {
            this.selectionSave();
            this.inlineEachNodes(function(f) {
                c(f).css(e, "");
                this.removeEmptyAttr(f, "style");
            });
            this.selectionRestore();
            this.sync();
        },
        inlineSetStyle: function(f, e) { this.inlineMethods("css", f, e); },
        inlineRemoveAttr: function(e) {
            this.selectionSave();
            var g = this.getRange(),
                h = this.getElement(),
                f = this.getNodes();
            if (g.collapsed || g.startContainer === g.endContainer && h) { f = c(h); }
            c(f).removeAttr(e);
            this.inlineUnwrapSpan();
            this.selectionRestore();
            this.sync();
        },
        inlineSetAttr: function(e, f) { this.inlineMethods("attr", e, f); },
        inlineMethods: function(h, e, j) {
            this.bufferSet();
            this.selectionSave();
            var f = this.getRange();
            var g = this.getElement();
            if ((f.collapsed || f.startContainer === f.endContainer) && g && !this.nodeTestBlocks(g)) { c(g)[h](e, j); } else {
                this.document.execCommand("fontSize", false, 4);
                var l = this.$editor.find("font");
                c.each(l, c.proxy(function(m, n) { this.inlineSetMethods(h, n, e, j); }, this));
            }
            this.selectionRestore();
            this.sync();
        },
        inlineSetMethods: function(j, o, g, l) {
            var m = c(o).parent(),
                e;
            var n = this.getSelectionText();
            var h = c(m).text();
            var f = n == h;
            if (f && m && m[0].tagName === "INLINE" && m[0].attributes.length != 0) {
                e = m;
                c(o).replaceWith(c(o).html());
            } else {
                e = c("<inline>").append(c(o).contents());
                c(o).replaceWith(e);
            }
            c(e)[j](g, l);
            return e;
        },
        inlineEachNodes: function(j) {
            var f = this.getRange(),
                g = this.getElement(),
                e = this.getNodes(),
                h;
            if (f.collapsed || f.startContainer === f.endContainer && g) {
                e = c(g);
                h = true;
            }
            c.each(e, c.proxy(function(m, o) {
                if (!h && o.tagName !== "INLINE") { var l = this.getSelectionText(); var p = c(o).parent().text(); var n = l == p; if (n && o.parentNode.tagName === "INLINE" && !c(o.parentNode).hasClass("redactor_editor")) { o = o.parentNode; } else { return; } }
                j.call(this, o);
            }, this));
        },
        inlineUnwrapSpan: function() {
            var e = this.$editor.find("inline");
            c.each(e, c.proxy(function(g, h) { var f = c(h); if (f.attr("class") === undefined && f.attr("style") === undefined) { f.contents().unwrap(); } }, this));
        },
        inlineFormat: function(e) {
            this.selectionSave();
            this.document.execCommand("fontSize", false, 4);
            var g = this.$editor.find("font");
            var f;
            c.each(g, function(h, l) {
                var j = c("<" + e + "/>").append(c(l).contents());
                c(l).replaceWith(j);
                f = j;
            });
            this.selectionRestore();
            this.sync();
        },
        inlineRemoveFormat: function(e) {
            this.selectionSave();
            var f = e.toUpperCase();
            var g = this.getNodes();
            var h = c(this.getParent()).parent();
            c.each(g, function(j, l) { if (l.tagName === f) { this.inlineRemoveFormatReplace(l); } });
            if (h && h[0].tagName === f) { this.inlineRemoveFormatReplace(h); }
            this.selectionRestore();
            this.sync();
        },
        inlineRemoveFormatReplace: function(e) { c(e).replaceWith(c(e).contents()); },
        insertHtml: function(g, j) {
            var m = this.getCurrent();
            var h = m.parentNode;
            this.$editor.focus();
            this.bufferSet();
            var e = c("<div>").append(c.parseHTML(g));
            g = e.html();
            g = this.cleanRemoveEmptyTags(g);
            e = c("<div>").append(c.parseHTML(g));
            var f = this.getBlock();
            if (e.contents().length == 1) {
                var l = e.contents()[0].tagName;
                if (l != "P" && l == f.tagName || l == "PRE") {
                    g = e.text();
                    e = c("<div>").append(g);
                }
            }
            if (!this.opts.linebreaks && e.contents().length == 1 && e.contents()[0].nodeType == 3 && (this.getRangeSelectedNodes().length > 2 || (!m || m.tagName == "BODY" && !h || h.tagName == "HTML"))) { g = "<p>" + g + "</p>"; }
            g = this.setSpansVerifiedHtml(g);
            if (e.contents().length > 1 && f || e.contents().is("p, :header, ul, ol, li, div, table, td, blockquote, pre, address, section, header, footer, aside, article")) { if (this.browser("msie")) { if (!this.isIe11()) { this.document.selection.createRange().pasteHTML(g); } else { this.execPasteFrag(g); } } else { this.document.execCommand("inserthtml", false, g); } } else { this.insertHtmlAdvanced(g, false); }
            if (this.selectall) { this.window.setTimeout(c.proxy(function() { if (!this.opts.linebreaks) { this.selectionEnd(this.$editor.contents().last()); } else { this.focusEnd(); } }, this), 1); }
            this.observeStart();
            this.setNonEditable();
            if (j !== false) { this.sync(); }
        },
        insertHtmlAdvanced: function(f, l) {
            f = this.setSpansVerifiedHtml(f);
            var m = this.getSelection();
            if (m.getRangeAt && m.rangeCount) {
                var e = m.getRangeAt(0);
                e.deleteContents();
                var g = this.document.createElement("div");
                g.innerHTML = f;
                var n = this.document.createDocumentFragment(),
                    j, h;
                while ((j = g.firstChild)) { h = n.appendChild(j); }
                e.insertNode(n);
                if (h) {
                    e = e.cloneRange();
                    e.setStartAfter(h);
                    e.collapse(true);
                    m.removeAllRanges();
                    m.addRange(e);
                }
            }
            if (l !== false) { this.sync(); }
        },
        insertBeforeCursor: function(f) {
            f = this.setSpansVerifiedHtml(f);
            var g = c(f);
            var j = document.createElement("span");
            j.innerHTML = "\u200B";
            var e = this.getRange();
            e.insertNode(j);
            e.insertNode(g[0]);
            e.collapse(false);
            var h = this.getSelection();
            h.removeAllRanges();
            h.addRange(e);
            this.sync();
        },
        insertText: function(f) {
            var e = c(c.parseHTML(f));
            if (e.length) { f = e.text(); }
            this.$editor.focus();
            if (this.browser("msie") && !this.isIe11()) { this.document.selection.createRange().pasteHTML(f); } else { this.document.execCommand("inserthtml", false, f); }
            this.sync();
        },
        insertNode: function(j) {
            j = j[0] || j;
            if (j.tagName == "SPAN") {
                var e = "inline";
                var f = j.outerHTML;
                var h = new RegExp("<" + j.tagName, "i");
                var g = f.replace(h, "<" + e);
                h = new RegExp("</" + j.tagName, "i");
                g = g.replace(h, "</" + e);
                j = c(g)[0];
            }
            var l = this.getSelection();
            if (l.getRangeAt && l.rangeCount) {
                range = l.getRangeAt(0);
                range.deleteContents();
                range.insertNode(j);
                range.setEndAfter(j);
                range.setStartAfter(j);
                l.removeAllRanges();
                l.addRange(range);
            }
        },
        insertNodeToCaretPositionFromPoint: function(l, j) {
            var g;
            var f = l.clientX,
                n = l.clientY;
            if (this.document.caretPositionFromPoint) {
                var m = this.document.caretPositionFromPoint(f, n);
                g = this.getRange();
                g.setStart(m.offsetNode, m.offset);
                g.collapse(true);
                g.insertNode(j);
            } else {
                if (this.document.caretRangeFromPoint) {
                    g = this.document.caretRangeFromPoint(f, n);
                    g.insertNode(j);
                } else {
                    if (typeof document.body.createTextRange != "undefined") {
                        g = this.document.body.createTextRange();
                        g.moveToPoint(f, n);
                        var h = g.duplicate();
                        h.moveToPoint(f, n);
                        g.setEndPoint("EndToEnd", h);
                        g.select();
                    }
                }
            }
        },
        insertAfterLastElement: function(e, f) {
            if (typeof(f) != "undefined") { e = f; }
            if (this.isEndOfElement()) {
                if (this.opts.linebreaks) { var g = c("<div>").append(c.trim(this.$editor.html())).contents(); if (this.outerHtml(g.last()[0]) != this.outerHtml(e)) { return false; } } else { if (this.$editor.contents().last()[0] !== e) { return false; } }
                this.insertingAfterLastElement(e);
            }
        },
        insertingAfterLastElement: function(e) {
            this.bufferSet();
            if (this.opts.linebreaks === false) {
                var f = c(this.opts.emptyHtml);
                c(e).after(f);
                this.selectionStart(f);
            } else {
                var f = c('<span id="selection-marker-1">' + this.opts.invisibleSpace + "</span>", this.document)[0];
                c(e).after(f);
                c(f).after(this.opts.invisibleSpace);
                this.selectionRestore();
                this.$editor.find("span#selection-marker-1").removeAttr("id");
            }
        },
        insertLineBreak: function() {
            this.selectionSave();
            this.$editor.find("#selection-marker-1").before("<br>" + (this.browser("webkit") ? this.opts.invisibleSpace : ""));
            this.selectionRestore();
        },
        insertDoubleLineBreak: function() {
            this.selectionSave();
            this.$editor.find("#selection-marker-1").before("<br><br>" + (this.browser("webkit") ? this.opts.invisibleSpace : ""));
            this.selectionRestore();
        },
        replaceLineBreak: function(e) {
            var f = c("<br>" + this.opts.invisibleSpace);
            c(e).replaceWith(f);
            this.selectionStart(f);
        },
        pasteClean: function(g) {
            g = this.callback("pasteBefore", false, g);
            if (this.browser("msie")) { var f = c.trim(g); if (f.search(/^<a(.*?)>(.*?)<\/a>$/i) == 0) { g = g.replace(/^<a(.*?)>(.*?)<\/a>$/i, "$2"); } }
            if (this.opts.pastePlainText) {
                var f = this.document.createElement("div");
                g = g.replace(/<br>|<\/H[1-6]>|<\/p>|<\/div>/gi, "\n");
                f.innerHTML = g;
                g = f.textContent || f.innerText;
                g = c.trim(g);
                g = g.replace("\n", "<br>");
                g = this.cleanParagraphy(g);
                this.pasteInsert(g);
                return false;
            }
            if (this.currentOrParentIs("PRE")) {
                g = this.pastePre(g);
                this.pasteInsert(g);
                return true;
            }
            g = g.replace(/<p(.*?)class="MsoListParagraphCxSpFirst"([\w\W]*?)<\/p>/gi, "<ul><li$2</li>");
            g = g.replace(/<p(.*?)class="MsoListParagraphCxSpMiddle"([\w\W]*?)<\/p>/gi, "<li$2</li>");
            g = g.replace(/<p(.*?)class="MsoListParagraphCxSpLast"([\w\W]*?)<\/p>/gi, "<li$2</li></ul>");
            g = g.replace(/<p(.*?)class="MsoListParagraph"([\w\W]*?)<\/p>/gi, "<ul><li$2</li></ul>");
            g = g.replace(/·/g, "");
            g = g.replace(/<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi, "");
            g = g.replace(/(&nbsp;){2,}/gi, "&nbsp;");
            g = g.replace(/&nbsp;/gi, " ");
            g = g.replace(/<b\sid="internal-source-marker(.*?)">([\w\W]*?)<\/b>/gi, "$2");
            g = g.replace(/<b(.*?)id="docs-internal-guid(.*?)">([\w\W]*?)<\/b>/gi, "$3");
            g = this.cleanStripTags(g);
            g = g.replace(/<td><\/td>/gi, "[td]");
            g = g.replace(/<td>&nbsp;<\/td>/gi, "[td]");
            g = g.replace(/<td><br><\/td>/gi, "[td]");
            g = g.replace(/<td(.*?)colspan="(.*?)"(.*?)>([\w\W]*?)<\/td>/gi, '[td colspan="$2"]$4[/td]');
            g = g.replace(/<td(.*?)rowspan="(.*?)"(.*?)>([\w\W]*?)<\/td>/gi, '[td rowspan="$2"]$4[/td]');
            g = g.replace(/<a(.*?)href="(.*?)"(.*?)>([\w\W]*?)<\/a>/gi, '[a href="$2"]$4[/a]');
            g = g.replace(/<iframe(.*?)>([\w\W]*?)<\/iframe>/gi, "[iframe$1]$2[/iframe]");
            g = g.replace(/<video(.*?)>([\w\W]*?)<\/video>/gi, "[video$1]$2[/video]");
            g = g.replace(/<audio(.*?)>([\w\W]*?)<\/audio>/gi, "[audio$1]$2[/audio]");
            g = g.replace(/<embed(.*?)>([\w\W]*?)<\/embed>/gi, "[embed$1]$2[/embed]");
            g = g.replace(/<object(.*?)>([\w\W]*?)<\/object>/gi, "[object$1]$2[/object]");
            g = g.replace(/<param(.*?)>/gi, "[param$1]");
            g = g.replace(/<img(.*?)>/gi, "[img$1]");
            g = g.replace(/ class="(.*?)"/gi, "");
            g = g.replace(/<(\w+)([\w\W]*?)>/gi, "<$1>");
            g = g.replace(/<[^\/>][^>]*>(\s*|\t*|\n*|&nbsp;|<br>)<\/[^>]+>/gi, "");
            g = g.replace(/<div>\s*?\t*?\n*?(<ul>|<ol>|<p>)/gi, "$1");
            g = g.replace(/\[td colspan="(.*?)"\]([\w\W]*?)\[\/td\]/gi, '<td colspan="$1">$2</td>');
            g = g.replace(/\[td rowspan="(.*?)"\]([\w\W]*?)\[\/td\]/gi, '<td rowspan="$1">$2</td>');
            g = g.replace(/\[td\]/gi, "<td>&nbsp;</td>");
            g = g.replace(/\[a href="(.*?)"\]([\w\W]*?)\[\/a\]/gi, '<a href="$1">$2</a>');
            g = g.replace(/\[iframe(.*?)\]([\w\W]*?)\[\/iframe\]/gi, "<iframe$1>$2</iframe>");
            g = g.replace(/\[video(.*?)\]([\w\W]*?)\[\/video\]/gi, "<video$1>$2</video>");
            g = g.replace(/\[audio(.*?)\]([\w\W]*?)\[\/audio\]/gi, "<audio$1>$2</audio>");
            g = g.replace(/\[embed(.*?)\]([\w\W]*?)\[\/embed\]/gi, "<embed$1>$2</embed>");
            g = g.replace(/\[object(.*?)\]([\w\W]*?)\[\/object\]/gi, "<object$1>$2</object>");
            g = g.replace(/\[param(.*?)\]/gi, "<param$1>");
            g = g.replace(/\[img(.*?)\]/gi, "<img$1>");
            if (this.opts.convertDivs) {
                g = g.replace(/<div(.*?)>([\w\W]*?)<\/div>/gi, "<p>$2</p>");
                g = g.replace(/<\/div><p>/gi, "<p>");
                g = g.replace(/<\/p><\/div>/gi, "</p>");
            }
            if (this.currentOrParentIs("LI")) { g = g.replace(/<p>([\w\W]*?)<\/p>/gi, "$1<br>"); } else { g = this.cleanParagraphy(g); }
            g = g.replace(/<span(.*?)>([\w\W]*?)<\/span>/gi, "$2");
            g = g.replace(/<img>/gi, "");
            g = g.replace(/<[^\/>][^>][^img|param|source]*>(\s*|\t*|\n*|&nbsp;|<br>)<\/[^>]+>/gi, "");
            g = g.replace(/\n{3,}/gi, "\n");
            g = g.replace(/<p><p>/gi, "<p>");
            g = g.replace(/<\/p><\/p>/gi, "</p>");
            g = g.replace(/<li>(\s*|\t*|\n*)<p>/gi, "<li>");
            g = g.replace(/<\/p>(\s*|\t*|\n*)<\/li>/gi, "</li>");
            if (this.opts.linebreaks === true) { g = g.replace(/<p(.*?)>([\w\W]*?)<\/p>/gi, "$2<br>"); }
            g = g.replace(/<[^\/>][^>][^img|param|source]*>(\s*|\t*|\n*|&nbsp;|<br>)<\/[^>]+>/gi, "");
            g = g.replace(/<img src="webkit-fake-url\:\/\/(.*?)"(.*?)>/gi, "");
            g = g.replace(/<td(.*?)>(\s*|\t*|\n*)<p>([\w\W]*?)<\/p>(\s*|\t*|\n*)<\/td>/gi, "<td$1>$3</td>");
            g = g.replace(/<div(.*?)>([\w\W]*?)<\/div>/gi, "$2");
            g = g.replace(/<div(.*?)>([\w\W]*?)<\/div>/gi, "$2");
            this.pasteClipboardMozilla = false;
            if (this.browser("mozilla")) {
                if (this.opts.clipboardUpload) {
                    var h = g.match(/<img src="data:image(.*?)"(.*?)>/gi);
                    if (h !== null) {
                        this.pasteClipboardMozilla = h;
                        for (k in h) {
                            var e = h[k].replace("<img", '<img data-mozilla-paste-image="' + k + '" ');
                            g = g.replace(h[k], e);
                        }
                    }
                }
                while (/<br>$/gi.test(g)) { g = g.replace(/<br>$/gi, ""); }
            }
            g = g.replace(/<p>•([\w\W]*?)<\/p>/gi, "<li>$1</li>");
            while (/<font>([\w\W]*?)<\/font>/gi.test(g)) { g = g.replace(/<font>([\w\W]*?)<\/font>/gi, "$1"); }
            g = g.replace(/<p>\n?<li>/gi, "<li>");
            if (this.browser("msie") && !this.isIe11()) { g = g.replace(/\n/g, ""); }
            this.pasteInsert(g);
        },
        pastePre: function(f) {
            f = f.replace(/<br>|<\/H[1-6]>|<\/p>|<\/div>/gi, "\n");
            var e = this.document.createElement("div");
            e.innerHTML = f;
            return this.cleanEncodeEntities(e.textContent || e.innerText);
        },
        pasteInsert: function(e) {
            if (this.selectall) {
                if (!this.opts.linebreaks) { this.$editor.html(this.opts.emptyHtml); } else { this.$editor.html(""); }
                this.$editor.focus();
            }
            e = this.callback("pasteAfter", false, e);
            this.insertHtml(e);
            this.selectall = false;
            setTimeout(c.proxy(function() { this.rtePaste = false; if (this.browser("mozilla")) { this.$editor.find("p:empty").remove(); } if (this.pasteClipboardMozilla !== false) { this.pasteClipboardUploadMozilla(); } }, this), 100);
            if (this.opts.autoresize && this.fullscreen !== true) { c(this.document.body).scrollTop(this.saveScroll); } else { this.$editor.scrollTop(this.saveScroll); }
        },
        pasteClipboardUploadMozilla: function() {
            var e = this.$editor.find("img[data-mozilla-paste-image]");
            c.each(e, c.proxy(function(h, j) {
                var g = c(j);
                var f = j.src.split(",");
                var l = f[1];
                var m = f[0].split(";")[0].split(":")[1];
                c.post(this.opts.clipboardUploadUrl, { contentType: m, data: l }, c.proxy(function(o) {
                    var n = (typeof o === "string" ? c.parseJSON(o) : o);
                    g.attr("src", n.filelink);
                    g.removeAttr("data-mozilla-paste-image");
                    this.sync();
                    this.callback("imageUpload", g, n);
                }, this));
            }, this));
        },
        pasteClipboardUpload: function(j) {
            var g = j.target.result;
            var f = g.split(",");
            var h = f[1];
            var l = f[0].split(";")[0].split(":")[1];
            if (this.opts.clipboardUpload) {
                c.post(this.opts.clipboardUploadUrl, { contentType: l, data: h }, c.proxy(function(n) {
                    var m = (typeof n === "string" ? c.parseJSON(n) : n);
                    var e = '<img src="' + m.filelink + '" id="clipboard-image-marker" />';
                    this.execCommand("inserthtml", e, false);
                    var o = c(this.$editor.find("img#clipboard-image-marker"));
                    if (o.length) { o.removeAttr("id"); } else { o = false; }
                    this.sync();
                    if (o) { this.callback("imageUpload", o, m); }
                }, this));
            } else { this.insertHtml('<img src="' + g + '" />'); }
        },
        bufferSet: function(e) {
            if (e !== undefined) { this.opts.buffer.push(e); } else {
                this.selectionSave();
                this.opts.buffer.push(this.$editor.html());
                this.selectionRemoveMarkers("buffer");
            }
        },
        bufferUndo: function() {
            if (this.opts.buffer.length === 0) { this.$editor.focus(); return; }
            this.selectionSave();
            this.opts.rebuffer.push(this.$editor.html());
            this.selectionRestore(false, true);
            this.$editor.html(this.opts.buffer.pop());
            this.selectionRestore();
            setTimeout(c.proxy(this.observeStart, this), 100);
        },
        bufferRedo: function() {
            if (this.opts.rebuffer.length === 0) { this.$editor.focus(); return false; }
            this.selectionSave();
            this.opts.buffer.push(this.$editor.html());
            this.selectionRestore(false, true);
            this.$editor.html(this.opts.rebuffer.pop());
            this.selectionRestore(true);
            setTimeout(c.proxy(this.observeStart, this), 4);
        },
        observeStart: function() { this.observeImages(); if (this.opts.observeLinks) { this.observeLinks(); } },
        observeLinks: function() {
            this.$editor.find("a").on("click", c.proxy(this.linkObserver, this));
            this.$editor.on("click.redactor", c.proxy(function(f) { this.linkObserverTooltipClose(f); }, this));
            c(document).on("click.redactor", c.proxy(function(f) { this.linkObserverTooltipClose(f); }, this));
        },
        observeImages: function() {
            if (this.opts.observeImages === false) { return false; }
            this.$editor.find("img").each(c.proxy(function(e, f) {
                if (this.browser("msie")) { c(f).attr("unselectable", "on"); }
                this.imageResize(f);
            }, this));
        },
        linkObserver: function(h) {
            var j = c(h.target);
            var m = j.offset();
            if (this.opts.iframe) {
                var g = this.$frame.offset();
                m.top = g.top + (m.top - c(this.document).scrollTop());
                m.left += g.left;
            }
            var p = c('<span class="redactor-link-tooltip"></span>');
            var f = j.attr("href");
            if (f.length > 24) { f = f.substring(0, 24) + "..."; }
            var l = c('<a href="' + j.attr("href") + '" target="_blank">' + f + "</a>").on("click", c.proxy(function(q) { this.linkObserverTooltipClose(false); }, this));
            var n = c('<a href="#">' + this.opts.curLang.edit + "</a>").on("click", c.proxy(function(q) {
                q.preventDefault();
                this.linkShow();
                this.linkObserverTooltipClose(false);
            }, this));
            var o = c('<a href="#">' + this.opts.curLang.unlink + "</a>").on("click", c.proxy(function(q) {
                q.preventDefault();
                this.execCommand("unlink");
                this.linkObserverTooltipClose(false);
            }, this));
            p.append(l);
            p.append(" | ");
            p.append(n);
            p.append(" | ");
            p.append(o);
            p.css({ top: (m.top + 20) + "px", left: m.left + "px" });
            c(".redactor-link-tooltip").remove();
            c("body").append(p);
        },
        linkObserverTooltipClose: function(f) {
            if (f !== false && f.target.tagName == "A") { return false; }
            c(".redactor-link-tooltip").remove();
        },
        getSelection: function() { if (!this.opts.rangy) { return this.document.getSelection(); } else { if (!this.opts.iframe) { return rangy.getSelection(); } else { return rangy.getSelection(this.$frame[0]); } } },
        getRange: function() { if (!this.opts.rangy) { if (this.document.getSelection) { var e = this.getSelection(); if (e.getRangeAt && e.rangeCount) { return e.getRangeAt(0); } } return this.document.createRange(); } else { if (!this.opts.iframe) { return rangy.createRange(); } else { return rangy.createRange(this.iframeDoc()); } } },
        selectionElement: function(e) { this.setCaret(e); },
        selectionStart: function(e) { this.selectionSet(e[0] || e, 0, null, 0); },
        selectionEnd: function(e) { this.selectionSet(e[0] || e, 1, null, 1); },
        selectionSet: function(n, m, l, h) {
            if (l == null) { l = n; }
            if (h == null) { h = m; }
            var g = this.getSelection();
            if (!g) { return; }
            var f = this.getRange();
            f.setStart(n, m);
            f.setEnd(l, h);
            try { g.removeAllRanges(); } catch (j) {}
            g.addRange(f);
        },
        selectionWrap: function(e) {
            e = e.toLowerCase();
            var h = this.getBlock();
            if (h) {
                var j = this.formatChangeTag(h, e);
                this.sync();
                return j;
            }
            var g = this.getSelection();
            var f = g.getRangeAt(0);
            var j = document.createElement(e);
            j.appendChild(f.extractContents());
            f.insertNode(j);
            this.selectionElement(j);
            return j;
        },
        selectionAll: function() {
            var e = this.getRange();
            e.selectNodeContents(this.$editor[0]);
            var f = this.getSelection();
            f.removeAllRanges();
            f.addRange(e);
        },
        selectionRemove: function() { this.getSelection().removeAllRanges(); },
        getCaretOffset: function(h) {
            var e = 0;
            var g = this.getRange();
            var f = g.cloneRange();
            f.selectNodeContents(h);
            f.setEnd(g.endContainer, g.endOffset);
            e = c.trim(f.toString()).length;
            return e;
        },
        getCaretOffsetRange: function() { return new d(this.getSelection().getRangeAt(0)); },
        setCaret: function(h, f, m) {
            if (typeof m === "undefined") { m = f; }
            h = h[0] || h;
            var o = this.getRange();
            o.selectNodeContents(h);
            var p = this.getTextNodesIn(h);
            var l = false;
            var e = 0,
                q;
            if (p.length == 1 && f) {
                o.setStart(p[0], f);
                o.setEnd(p[0], m);
            } else {
                for (var n = 0, j; j = p[n++];) {
                    q = e + j.length;
                    if (!l && f >= e && (f < q || (f == q && n < p.length))) {
                        o.setStart(j, f - e);
                        l = true;
                    }
                    if (l && m <= q) { o.setEnd(j, m - e); break; }
                    e = q;
                }
            }
            var g = this.getSelection();
            g.removeAllRanges();
            g.addRange(o);
        },
        getTextNodesIn: function(j) { var h = []; if (j.nodeType == 3) { h.push(j); } else { var g = j.childNodes; for (var f = 0, e = g.length; f < e; ++f) { h.push.apply(h, this.getTextNodesIn(g[f])); } } return h; },
        getCurrent: function() { var e = false; var f = this.getSelection(); if (f && f.rangeCount > 0) { e = f.getRangeAt(0).startContainer; } return this.isParentRedactor(e); },
        getParent: function(e) { e = e || this.getCurrent(); if (e) { return this.isParentRedactor(c(e).parent()[0]); } else { return false; } },
        getBlock: function(e) {
            if (typeof e === "undefined") { e = this.getCurrent(); }
            while (e) {
                if (this.nodeTestBlocks(e)) { if (c(e).hasClass("redactor_editor")) { return false; } return e; }
                e = e.parentNode;
            }
            return false;
        },
        getBlocks: function(f) {
            var g = [];
            if (typeof f == "undefined") { var e = this.getRange(); if (e && e.collapsed === true) { return [this.getBlock()]; } var f = this.getNodes(e); }
            c.each(f, c.proxy(function(h, j) { if (this.opts.iframe === false && c(j).parents("div.redactor_editor").size() == 0) { return false; } if (this.nodeTestBlocks(j)) { g.push(j); } }, this));
            if (g.length === 0) { g = [this.getBlock()]; }
            return g;
        },
        nodeTestBlocks: function(e) { return e.nodeType == 1 && this.rTestBlock.test(e.nodeName); },
        tagTestBlock: function(e) { return this.rTestBlock.test(e); },
        getNodes: function(g, e) {
            if (typeof g == "undefined" || g == false) { var g = this.getRange(); }
            if (g && g.collapsed === true) { if (typeof e === "undefined" && this.tagTestBlock(e)) { var m = this.getBlock(); if (m.tagName == e) { return [m]; } else { return []; } } else { return [this.getCurrent()]; } }
            var f = [],
                l = [];
            var j = this.document.getSelection();
            if (!j.isCollapsed) { f = this.getRangeSelectedNodes(j.getRangeAt(0)); }
            c.each(f, c.proxy(function(n, o) { if (this.opts.iframe === false && c(o).parents("div.redactor_editor").size() == 0) { return false; } if (typeof e === "undefined") { if (c.trim(o.textContent) != "") { l.push(o); } } else { if (o.tagName == e) { l.push(o); } } }, this));
            if (l.length == 0) { if (typeof e === "undefined" && this.tagTestBlock(e)) { var m = this.getBlock(); if (m.tagName == e) { return l.push(m); } else { return []; } } else { l.push(this.getCurrent()); } }
            var h = l[l.length - 1];
            if (this.nodeTestBlocks(h)) { l = l.slice(0, -1); }
            return l;
        },
        getElement: function(e) {
            if (!e) { e = this.getCurrent(); }
            while (e) {
                if (e.nodeType == 1) { if (c(e).hasClass("redactor_editor")) { return false; } return e; }
                e = e.parentNode;
            }
            return false;
        },
        getRangeSelectedNodes: function(f) {
            f = f || this.getRange();
            var g = f.startContainer;
            var e = f.endContainer;
            if (g == e) { return [g]; }
            var h = [];
            while (g && g != e) { h.push(g = this.nextNode(g)); }
            g = f.startContainer;
            while (g && g != f.commonAncestorContainer) {
                h.unshift(g);
                g = g.parentNode;
            }
            return h;
        },
        nextNode: function(e) { if (e.hasChildNodes()) { return e.firstChild; } else { while (e && !e.nextSibling) { e = e.parentNode; } if (!e) { return null; } return e.nextSibling; } },
        getSelectionText: function() { return this.getSelection().toString(); },
        getSelectionHtml: function() {
            var h = "";
            var j = this.getSelection();
            if (j.rangeCount) {
                var f = this.document.createElement("div");
                var e = j.rangeCount;
                for (var g = 0; g < e; ++g) { f.appendChild(j.getRangeAt(g).cloneContents()); }
                h = f.innerHTML;
            }
            return this.syncClean(h);
        },
        selectionSave: function() { if (!this.isFocused()) { this.$editor.focus(); } if (!this.opts.rangy) { this.selectionCreateMarker(this.getRange()); } else { this.savedSel = rangy.saveSelection(); } },
        selectionCreateMarker: function(h, e) {
            if (!h) { return; }
            var g = c('<span id="selection-marker-1" class="redactor-selection-marker">' + this.opts.invisibleSpace + "</span>", this.document)[0];
            var f = c('<span id="selection-marker-2" class="redactor-selection-marker">' + this.opts.invisibleSpace + "</span>", this.document)[0];
            if (h.collapsed === true) { this.selectionSetMarker(h, g, true); } else {
                this.selectionSetMarker(h, g, true);
                this.selectionSetMarker(h, f, false);
            }
            this.savedSel = this.$editor.html();
            this.selectionRestore(false, false);
        },
        selectionSetMarker: function(e, g, f) {
            var h = e.cloneRange();
            h.collapse(f);
            h.insertNode(g);
            h.detach();
        },
        selectionRestore: function(h, e) {
            if (!this.opts.rangy) {
                if (h === true && this.savedSel) { this.$editor.html(this.savedSel); }
                var g = this.$editor.find("span#selection-marker-1");
                var f = this.$editor.find("span#selection-marker-2");
                if (this.browser("mozilla")) { this.$editor.focus(); } else { if (!this.isFocused()) { this.$editor.focus(); } }
                if (g.length != 0 && f.length != 0) { this.selectionSet(g[0], 0, f[0], 0); } else { if (g.length != 0) { this.selectionSet(g[0], 0, null, 0); } }
                if (e !== false) {
                    this.selectionRemoveMarkers();
                    this.savedSel = false;
                }
            } else { rangy.restoreSelection(this.savedSel); }
        },
        selectionRemoveMarkers: function(e) { if (!this.opts.rangy) { c.each(this.$editor.find("span.redactor-selection-marker"), function() { var f = c.trim(c(this).html().replace(/[^\u0000-\u1C7F]/g, "")); if (f == "") { c(this).remove(); } else { c(this).removeAttr("class").removeAttr("id"); } }); } else { rangy.removeMarkers(this.savedSel); } },
        tableShow: function() {
            this.selectionSave();
            this.modalInit(this.opts.curLang.table, this.opts.modal_table, 300, c.proxy(function() {
                c("#redactor_insert_table_btn").click(c.proxy(this.tableInsert, this));
                setTimeout(function() { c("#redactor_table_rows").focus(); }, 200);
            }, this));
        },
        tableInsert: function() {
            var r = c("#redactor_table_rows").val(),
                f = c("#redactor_table_columns").val(),
                n = c("<div></div>"),
                e = Math.floor(Math.random() * 99999),
                p = c('<table id="table' + e + '"><tbody></tbody></table>'),
                g, l, m, o;
            for (g = 0; g < r; g++) {
                l = c("<tr></tr>");
                for (m = 0; m < f; m++) {
                    o = c("<td>" + this.opts.invisibleSpace + "</td>");
                    if (g === 0 && m === 0) { o.append('<span id="selection-marker-1">' + this.opts.invisibleSpace + "</span>"); }
                    c(l).append(o);
                }
                p.append(l);
            }
            n.append(p);
            var h = n.html();
            this.modalClose();
            this.selectionRestore();
            var j = this.getBlock() || this.getCurrent();
            if (j && j.tagName != "BODY") { c(j).after(h); } else { this.insertHtmlAdvanced(h, false); }
            this.selectionRestore();
            var q = this.$editor.find("#table" + e);
            this.buttonActiveObserver();
            q.find("span#selection-marker-1").remove();
            q.removeAttr("id");
            this.sync();
        },
        tableDeleteTable: function() {
            var e = c(this.getParent()).closest("table");
            if (!this.isParentRedactor(e)) { return false; }
            if (e.size() == 0) { return false; }
            this.bufferSet();
            e.remove();
            this.sync();
        },
        tableDeleteRow: function() {
            var e = c(this.getParent()).closest("table");
            if (!this.isParentRedactor(e)) { return false; }
            if (e.size() == 0) { return false; }
            this.bufferSet();
            var h = c(this.getParent()).closest("tr");
            var f = h.prev().length ? h.prev() : h.next();
            if (f.length) { var g = f.children("td").first(); if (g.length) { g.prepend('<span id="selection-marker-1">' + this.opts.invisibleSpace + "</span>"); } }
            h.remove();
            this.selectionRestore();
            this.sync();
        },
        tableDeleteColumn: function() {
            var g = c(this.getParent()).closest("table");
            if (!this.isParentRedactor(g)) { return false; }
            if (g.size() == 0) { return false; }
            this.bufferSet();
            var e = c(this.getParent()).closest("td");
            var f = e.get(0).cellIndex;
            g.find("tr").each(c.proxy(function(h, j) {
                var l = f - 1 < 0 ? f + 1 : f - 1;
                if (h === 0) { c(j).find("td").eq(l).prepend('<span id="selection-marker-1">' + this.opts.invisibleSpace + "</span>"); }
                c(j).find("td").eq(f).remove();
            }, this));
            this.selectionRestore();
            this.sync();
        },
        tableAddHead: function() {
            var e = c(this.getParent()).closest("table");
            if (!this.isParentRedactor(e)) { return false; }
            if (e.size() == 0) { return false; }
            this.bufferSet();
            if (e.find("thead").size() !== 0) { this.tableDeleteHead(); } else {
                var f = e.find("tr").first().clone();
                f.find("td").html(this.opts.invisibleSpace);
                $thead = c("<thead></thead>");
                $thead.append(f);
                e.prepend($thead);
                this.sync();
            }
        },
        tableDeleteHead: function() {
            var e = c(this.getParent()).closest("table");
            if (!this.isParentRedactor(e)) { return false; }
            var f = e.find("thead");
            if (f.size() == 0) { return false; }
            this.bufferSet();
            f.remove();
            this.sync();
        },
        tableAddRowAbove: function() { this.tableAddRow("before"); },
        tableAddRowBelow: function() { this.tableAddRow("after"); },
        tableAddColumnLeft: function() { this.tableAddColumn("before"); },
        tableAddColumnRight: function() { this.tableAddColumn("after"); },
        tableAddRow: function(f) {
            var e = c(this.getParent()).closest("table");
            if (!this.isParentRedactor(e)) { return false; }
            if (e.size() == 0) { return false; }
            this.bufferSet();
            var g = c(this.getParent()).closest("tr");
            var h = g.clone();
            h.find("td").html(this.opts.invisibleSpace);
            if (f === "after") { g.after(h); } else { g.before(h); }
            this.sync();
        },
        tableAddColumn: function(h) {
            var g = c(this.getParent()).closest("table");
            if (!this.isParentRedactor(g)) { return false; }
            if (g.size() == 0) { return false; }
            this.bufferSet();
            var f = 0;
            var j = c(this.getParent()).closest("tr");
            var e = c(this.getParent()).closest("td");
            j.find("td").each(c.proxy(function(l, m) { if (c(m)[0] === e[0]) { f = l; } }, this));
            g.find("tr").each(c.proxy(function(l, n) {
                var m = c(n).find("td").eq(f);
                var o = m.clone();
                o.html(this.opts.invisibleSpace);
                h === "after" ? m.after(o) : m.before(o);
            }, this));
            this.sync();
        },
        videoShow: function() {
            this.selectionSave();
            this.modalInit(this.opts.curLang.video, this.opts.modal_video, 600, c.proxy(function() {
                c("#redactor_insert_video_btn").click(c.proxy(this.videoInsert, this));
                setTimeout(function() { c("#redactor_insert_video_area").focus(); }, 200);
            }, this));
        },
        videoInsert: function() {
            var e = c("#redactor_insert_video_area").val();
            e = this.cleanStripTags(e);
            this.selectionRestore();
            var f = this.getBlock() || this.getCurrent();
            if (f) { c(f).after(e); } else { this.insertHtmlAdvanced(e, false); }
            this.sync();
            this.modalClose();
        },
        linkShow: function() {
            this.selectionSave();
            var e = c.proxy(function() {
                this.insert_link_node = false;
                var g = this.getSelection();
                var f = "",
                    o = "",
                    j = "";
                var h = this.getParent();
                var l = c(h).parent().get(0);
                if (l && l.tagName === "A") { h = l; }
                if (h && h.tagName === "A") {
                    f = h.href;
                    o = c(h).text();
                    j = h.target;
                    this.insert_link_node = h;
                } else { o = g.toString(); }
                c(".redactor_link_text").val(o);
                var q = self.location.href.replace(/\/$/i, "");
                var n = f.replace(q, "");
                if (this.opts.linkProtocol === false) {
                    var p = new RegExp("^(http|ftp|https)://" + self.location.host, "i");
                    n = n.replace(p, "");
                }
                var m = c("#redactor_tabs").find("a");
                if (this.opts.linkEmail === false) { m.eq(1).remove(); }
                if (this.opts.linkAnchor === false) { m.eq(2).remove(); }
                if (this.opts.linkEmail === false && this.opts.linkAnchor === false) {
                    c("#redactor_tabs").remove();
                    c("#redactor_link_url").val(n);
                } else {
                    if (f.search("mailto:") === 0) {
                        this.modalSetTab.call(this, 2);
                        c("#redactor_tab_selected").val(2);
                        c("#redactor_link_mailto").val(f.replace("mailto:", ""));
                    } else {
                        if (n.search(/^#/gi) === 0) {
                            this.modalSetTab.call(this, 3);
                            c("#redactor_tab_selected").val(3);
                            c("#redactor_link_anchor").val(n.replace(/^#/gi, ""));
                        } else { c("#redactor_link_url").val(n); }
                    }
                }
                if (j === "_blank") { c("#redactor_link_blank").prop("checked", true); }
                c("#redactor_insert_link_btn").click(c.proxy(this.linkProcess, this));
                setTimeout(function() { c("#redactor_link_url").focus(); }, 200);
            }, this);
            this.modalInit(this.opts.curLang.link, this.opts.modal_link, 460, e);
        },
        linkProcess: function() {
            var j = c("#redactor_tab_selected").val();
            var g = "",
                n = "",
                l = "",
                m = "";
            if (j === "1") {
                g = c("#redactor_link_url").val();
                n = c("#redactor_link_url_text").val();
                if (c("#redactor_link_blank").prop("checked")) {
                    l = ' target="_blank"';
                    m = "_blank";
                }
                var h = "((xn--)?[a-z0-9]+(-[a-z0-9]+)*.)+[a-z]{2,}";
                var f = new RegExp("^(http|ftp|https)://" + h, "i");
                var e = new RegExp("^" + h, "i");
                if (g.search(f) == -1 && g.search(e) == 0 && this.opts.linkProtocol) { g = this.opts.linkProtocol + g; }
            } else {
                if (j === "2") {
                    g = "mailto:" + c("#redactor_link_mailto").val();
                    n = c("#redactor_link_mailto_text").val();
                } else {
                    if (j === "3") {
                        g = "#" + c("#redactor_link_anchor").val();
                        n = c("#redactor_link_anchor_text").val();
                    }
                }
            }
            n = n.replace(/<|>/g, "");
            this.linkInsert('<a href="' + g + '"' + l + ">" + n + "</a>", c.trim(n), g, m);
        },
        linkInsert: function(e, j, f, h) {
            this.selectionRestore();
            if (j !== "") {
                if (this.insert_link_node) {
                    this.bufferSet();
                    c(this.insert_link_node).text(j).attr("href", f);
                    if (h !== "") { c(this.insert_link_node).attr("target", h); } else { c(this.insert_link_node).removeAttr("target"); }
                    this.sync();
                } else {
                    var g = c(e).addClass("redactor-added-link");
                    this.exec("inserthtml", this.outerHtml(g), false);
                    this.$editor.find("a.redactor-added-link").removeAttr("style").removeClass("redactor-added-link").each(function() { if (this.className == "") { c(this).removeAttr("class"); } });
                    this.sync();
                }
            }
            setTimeout(c.proxy(function() { if (this.opts.observeLinks) { this.observeLinks(); } }, this), 5);
            this.modalClose();
        },
        fileShow: function() {
            this.selectionSave();
            var e = c.proxy(function() {
                var f = this.getSelection();
                var g = "";
                if (this.oldIE()) { g = f.text; } else { g = f.toString(); }
                c("#redactor_filename").val(g);
                if (!this.isMobile()) { this.draguploadInit("#redactor_file", { url: this.opts.fileUpload, uploadFields: this.opts.uploadFields, success: c.proxy(this.fileCallback, this), error: c.proxy(function(j, h) { this.callback("fileUploadError", h); }, this), uploadParam: this.opts.fileUploadParam }); }
                this.uploadInit("redactor_file", { auto: true, url: this.opts.fileUpload, success: c.proxy(this.fileCallback, this), error: c.proxy(function(j, h) { this.callback("fileUploadError", h); }, this) });
            }, this);
            this.modalInit(this.opts.curLang.file, this.opts.modal_file, 500, e);
        },
        fileCallback: function(f) {
            this.selectionRestore();
            if (f !== false) {
                var h = c("#redactor_filename").val();
                if (h === "") { h = f.filename; }
                var g = '<a href="' + f.filelink + '" id="filelink-marker">' + h + "</a>";
                if (this.browser("webkit") && !!this.window.chrome) { g = g + "&nbsp;"; }
                this.execCommand("inserthtml", g, false);
                var e = c(this.$editor.find("a#filelink-marker"));
                if (e.size() != 0) { e.removeAttr("id"); } else { e = false; }
                this.sync();
                this.callback("fileUpload", e, f);
            }
            this.modalClose();
        },
        imageShow: function() {
            this.selectionSave();
            var e = c.proxy(function() {
                if (this.opts.imageGetJson) {
                    c.getJSON(this.opts.imageGetJson, c.proxy(function(m) {
                        var h = {},
                            l = 0;
                        c.each(m, c.proxy(function(o, p) {
                            if (typeof p.folder !== "undefined") {
                                l++;
                                h[p.folder] = l;
                            }
                        }, this));
                        var j = false;
                        c.each(m, c.proxy(function(r, s) {
                            var q = "";
                            if (typeof s.title !== "undefined") { q = s.title; }
                            var o = 0;
                            if (!c.isEmptyObject(h) && typeof s.folder !== "undefined") { o = h[s.folder]; if (j === false) { j = ".redactorfolder" + o; } }
                            var p = c('<img src="' + s.thumb + '" class="redactorfolder redactorfolder' + o + '" rel="' + s.image + '" title="' + q + '" />');
                            c("#redactor_image_box").append(p);
                            c(p).click(c.proxy(this.imageThumbClick, this));
                        }, this));
                        if (!c.isEmptyObject(h)) {
                            c(".redactorfolder").hide();
                            c(j).show();
                            var n = function(o) {
                                c(".redactorfolder").hide();
                                c(".redactorfolder" + c(o.target).val()).show();
                            };
                            var g = c('<select id="redactor_image_box_select">');
                            c.each(h, function(p, o) { g.append(c('<option value="' + o + '">' + p + "</option>")); });
                            c("#redactor_image_box").before(g);
                            g.change(n);
                        }
                    }, this));
                } else { c("#redactor_tabs").find("a").eq(1).remove(); }
                if (this.opts.imageUpload || this.opts.s3) { if (!this.isMobile() && this.opts.s3 === false) { if (c("#redactor_file").length) { this.draguploadInit("#redactor_file", { url: this.opts.imageUpload, uploadFields: this.opts.uploadFields, success: c.proxy(this.imageCallback, this), error: c.proxy(function(h, g) { this.callback("imageUploadError", g); }, this), uploadParam: this.opts.imageUploadParam }); } } if (this.opts.s3 === false) { this.uploadInit("redactor_file", { auto: true, url: this.opts.imageUpload, success: c.proxy(this.imageCallback, this), error: c.proxy(function(h, g) { this.callback("imageUploadError", g); }, this) }); } else { c("#redactor_file").on("change.redactor", c.proxy(this.s3handleFileSelect, this)); } } else {
                    c(".redactor_tab").hide();
                    if (!this.opts.imageGetJson) {
                        c("#redactor_tabs").remove();
                        c("#redactor_tab3").show();
                    } else {
                        var f = c("#redactor_tabs").find("a");
                        f.eq(0).remove();
                        f.eq(1).addClass("redactor_tabs_act");
                        c("#redactor_tab2").show();
                    }
                }
                c("#redactor_upload_btn").click(c.proxy(this.imageCallbackLink, this));
                if (!this.opts.imageUpload && !this.opts.imageGetJson) { setTimeout(function() { c("#redactor_file_link").focus(); }, 200); }
            }, this);
            this.modalInit(this.opts.curLang.image, this.opts.modal_image, 610, e);
        },
        imageEdit: function(g) {
            var e = g;
            var f = e.parent().parent();
            var h = c.proxy(function() {
                c("#redactor_file_alt").val(e.attr("alt"));
                c("#redactor_image_edit_src").attr("href", e.attr("src"));
                c("#redactor_form_image_align").val(e.css("float"));
                if (c(f).get(0).tagName === "A") { c("#redactor_file_link").val(c(f).attr("href")); if (c(f).attr("target") == "_blank") { c("#redactor_link_blank").prop("checked", true); } }
                c("#redactor_image_delete_btn").click(c.proxy(function() { this.imageRemove(e); }, this));
                c("#redactorSaveBtn").click(c.proxy(function() { this.imageSave(e); }, this));
            }, this);
            this.modalInit(this.opts.curLang.edit, this.opts.modal_image_edit, 380, h);
        },
        imageRemove: function(f) {
            var e = c(f).parent();
            c(f).remove();
            if (e.length && e[0].tagName === "P") {
                this.$editor.focus();
                this.selectionStart(e);
            }
            this.callback("imageDelete", f);
            this.modalClose();
            this.sync();
        },
        imageSave: function(h) {
            var f = c(h);
            var g = f.parent();
            f.attr("alt", c("#redactor_file_alt").val());
            var n = c("#redactor_form_image_align").val();
            if (n === "left") {
                this.imageMargin = "0 " + this.opts.imageFloatMargin + " " + this.opts.imageFloatMargin + " 0";
                f.css({ "float": "left", margin: this.imageMargin });
            } else {
                if (n === "right") {
                    this.imageMargin = "0 0 " + this.opts.imageFloatMargin + " " + this.opts.imageFloatMargin + "";
                    f.css({ "float": "right", margin: this.imageMargin });
                } else {
                    this.imageMargin = "0px";
                    var l = f.closest("#redactor-image-box");
                    if (l.size() != 0) { l.css({ "float": "", margin: "" }); }
                    f.css({ "float": "", margin: "" });
                }
            }
            var j = c.trim(c("#redactor_file_link").val());
            if (j !== "") {
                var m = false;
                if (c("#redactor_link_blank").prop("checked")) { m = true; }
                if (g.get(0).tagName !== "A") {
                    var e = c('<a href="' + j + '">' + this.outerHtml(h) + "</a>");
                    if (m) { e.attr("target", "_blank"); }
                    f.replaceWith(e);
                } else { g.attr("href", j); if (m) { g.attr("target", "_blank"); } else { g.removeAttr("target"); } }
            } else { if (g.get(0).tagName === "A") { g.replaceWith(this.outerHtml(h)); } }
            this.modalClose();
            this.observeImages();
            this.sync();
        },
        imageResizeHide: function(g) {
            if (g !== false && c(g.target).parent().size() != 0 && c(g.target).parent()[0].id === "redactor-image-box") { return false; }
            var f = this.$editor.find("#redactor-image-box");
            if (f.size() == 0) { return false; }
            this.$editor.find("#redactor-image-editter, #redactor-image-resizer").remove();
            if (this.imageMargin != "0px") {
                f.find("img").css("margin", this.imageMargin);
                f.css("margin", "");
                this.imageMargin = "0px";
            }
            f.find("img").css("opacity", "");
            f.replaceWith(function() { return c(this).contents(); });
            c(document).off("click.redactor-image-resize-hide");
            this.$editor.off("click.redactor-image-resize-hide");
            this.$editor.off("keydown.redactor-image-delete");
            this.sync();
        },
        imageResize: function(f) {
            var e = c(f);
            e.on("mousedown", c.proxy(function() { this.imageResizeHide(false); }, this));
            e.on("dragstart", c.proxy(function() {
                this.$editor.on("drop.redactor-image-inside-drop", c.proxy(function() {
                    setTimeout(c.proxy(function() {
                        this.observeImages();
                        this.$editor.off("drop.redactor-image-inside-drop");
                        this.sync();
                    }, this), 1);
                }, this));
            }, this));
            e.on("click", c.proxy(function(l) {
                if (this.$editor.find("#redactor-image-box").size() != 0) { return false; }
                var n = false,
                    q, p, m = e.width() / e.height(),
                    o = 20,
                    j = 10;
                var g = this.imageResizeControls(e);
                var h = false;
                g.on("mousedown", function(r) {
                    h = true;
                    r.preventDefault();
                    m = e.width() / e.height();
                    q = Math.round(r.pageX - e.eq(0).offset().left);
                    p = Math.round(r.pageY - e.eq(0).offset().top);
                });
                c(this.document.body).on("mousemove", c.proxy(function(v) {
                    if (h) {
                        var s = Math.round(v.pageX - e.eq(0).offset().left) - q;
                        var r = Math.round(v.pageY - e.eq(0).offset().top) - p;
                        var u = e.height();
                        var w = parseInt(u, 10) + r;
                        var t = Math.round(w * m);
                        if (t > o) { e.width(t); if (t < 100) { this.imageEditter.css({ marginTop: "-7px", marginLeft: "-13px", fontSize: "9px", padding: "3px 5px" }); } else { this.imageEditter.css({ marginTop: "-11px", marginLeft: "-18px", fontSize: "11px", padding: "7px 10px" }); } }
                        q = Math.round(v.pageX - e.eq(0).offset().left);
                        p = Math.round(v.pageY - e.eq(0).offset().top);
                        this.sync();
                    }
                }, this)).on("mouseup", function() { h = false; });
                this.$editor.on("keydown.redactor-image-delete", c.proxy(function(s) {
                    var r = s.which;
                    if (this.keyCode.BACKSPACE == r || this.keyCode.DELETE == r) {
                        this.bufferSet();
                        this.imageResizeHide(false);
                        this.imageRemove(e);
                    }
                }, this));
                c(document).on("click.redactor-image-resize-hide", c.proxy(this.imageResizeHide, this));
                this.$editor.on("click.redactor-image-resize-hide", c.proxy(this.imageResizeHide, this));
            }, this));
        },
        imageResizeControls: function(f) {
            var g = c('<span id="redactor-image-box" data-redactor="verified">');
            g.css({ position: "relative", display: "inline-block", lineHeight: 0, outline: "1px dashed rgba(0, 0, 0, .6)", "float": f.css("float") });
            g.attr("contenteditable", false);
            this.imageMargin = f[0].style.margin;
            if (this.imageMargin != "0px") {
                g.css("margin", this.imageMargin);
                f.css("margin", "");
            }
            f.css("opacity", 0.5).after(g);
            this.imageEditter = c('<span id="redactor-image-editter" data-redactor="verified">' + this.opts.curLang.edit + "</span>");
            this.imageEditter.css({ position: "absolute", zIndex: 2, top: "50%", left: "50%", marginTop: "-11px", marginLeft: "-18px", lineHeight: 1, backgroundColor: "#000", color: "#fff", fontSize: "11px", padding: "7px 10px", cursor: "pointer" });
            this.imageEditter.attr("contenteditable", false);
            this.imageEditter.on("click", c.proxy(function() { this.imageEdit(f); }, this));
            g.append(this.imageEditter);
            var e = c('<span id="redactor-image-resizer" data-redactor="verified"></span>');
            e.css({ position: "absolute", zIndex: 2, lineHeight: 1, cursor: "nw-resize", bottom: "-4px", right: "-5px", border: "1px solid #fff", backgroundColor: "#000", width: "8px", height: "8px" });
            e.attr("contenteditable", false);
            g.append(e);
            g.append(f);
            return e;
        },
        imageThumbClick: function(h) {
            var f = '<img id="image-marker" src="' + c(h.target).attr("rel") + '" alt="' + c(h.target).attr("title") + '" />';
            var g = this.getParent();
            if (this.opts.paragraphy && c(g).closest("li").size() == 0) { f = "<p>" + f + "</p>"; }
            this.imageInsert(f, true);
        },
        imageCallbackLink: function() {
            var f = c("#redactor_file_link").val();
            if (f !== "") {
                var e = '<img id="image-marker" src="' + f + '" />';
                if (this.opts.linebreaks === false) { e = "<p>" + e + "</p>"; }
                this.imageInsert(e, true);
            } else { this.modalClose(); }
        },
        imageCallback: function(e) { this.imageInsert(e); },
        imageInsert: function(f, h) {
            this.selectionRestore();
            if (f !== false) {
                var e = "";
                if (h !== true) { e = '<img id="image-marker" src="' + f.filelink + '" />'; var g = this.getParent(); if (this.opts.paragraphy && c(g).closest("li").size() == 0) { e = "<p>" + e + "</p>"; } } else { e = f; }
                this.execCommand("inserthtml", e, false);
                var j = c(this.$editor.find("img#image-marker"));
                if (j.length) { j.removeAttr("id"); } else { j = false; }
                this.sync();
                h !== true && this.callback("imageUpload", j, f);
            }
            this.modalClose();
            this.observeImages();
        },
        modalTemplatesInit: function() { c.extend(this.opts, { modal_file: String() + '<section><div id="redactor-progress" class="redactor-progress redactor-progress-striped" style="display: none;"><div id="redactor-progress-bar" class="redactor-progress-bar" style="width: 100%;"></div></div><form id="redactorUploadFileForm" method="post" action="" enctype="multipart/form-data"><label>' + this.opts.curLang.filename + '</label><input type="text" id="redactor_filename" class="redactor_input" /><div style="margin-top: 7px;"><input type="file" id="redactor_file" name="' + this.opts.fileUploadParam + '" /></div></form></section>', modal_image_edit: String() + "<section><label>" + this.opts.curLang.title + '</label><input id="redactor_file_alt" class="redactor_input" /><label>' + this.opts.curLang.link + '</label><input id="redactor_file_link" class="redactor_input" /><label><input type="checkbox" id="redactor_link_blank"> ' + this.opts.curLang.link_new_tab + "</label><label>" + this.opts.curLang.image_position + '</label><select id="redactor_form_image_align"><option value="none">' + this.opts.curLang.none + '</option><option value="left">' + this.opts.curLang.left + '</option><option value="right">' + this.opts.curLang.right + '</option></select></section><footer><button id="redactor_image_delete_btn" class="redactor_modal_btn redactor_modal_delete_btn">' + this.opts.curLang._delete + '</button>&nbsp;&nbsp;&nbsp;<button class="redactor_modal_btn redactor_btn_modal_close">' + this.opts.curLang.cancel + '</button><input type="button" name="save" class="redactor_modal_btn redactor_modal_action_btn" id="redactorSaveBtn" value="' + this.opts.curLang.save + '" /></footer>', modal_image: String() + '<section><div id="redactor_tabs"><a href="#" class="redactor_tabs_act">' + this.opts.curLang.upload + '</a><a href="#">' + this.opts.curLang.choose + '</a><a href="#">' + this.opts.curLang.link + '</a></div><div id="redactor-progress" class="redactor-progress redactor-progress-striped" style="display: none;"><div id="redactor-progress-bar" class="redactor-progress-bar" style="width: 100%;"></div></div><form id="redactorInsertImageForm" method="post" action="" enctype="multipart/form-data"><div id="redactor_tab1" class="redactor_tab"><input type="file" id="redactor_file" name="' + this.opts.imageUploadParam + '" /></div><div id="redactor_tab2" class="redactor_tab" style="display: none;"><div id="redactor_image_box"></div></div></form><div id="redactor_tab3" class="redactor_tab" style="display: none;"><label>' + this.opts.curLang.image_web_link + '</label><input type="text" name="redactor_file_link" id="redactor_file_link" class="redactor_input"  /></div></section><footer><button class="redactor_modal_btn redactor_btn_modal_close">' + this.opts.curLang.cancel + '</button><input type="button" name="upload" class="redactor_modal_btn redactor_modal_action_btn" id="redactor_upload_btn" value="' + this.opts.curLang.insert + '" /></footer>', modal_link: String() + '<section><form id="redactorInsertLinkForm" method="post" action=""><div id="redactor_tabs"><a href="#" class="redactor_tabs_act">URL</a><a href="#">Email</a><a href="#">' + this.opts.curLang.anchor + '</a></div><input type="hidden" id="redactor_tab_selected" value="1" /><div class="redactor_tab" id="redactor_tab1"><label>URL</label><input type="text" id="redactor_link_url" class="redactor_input"  /><label>' + this.opts.curLang.text + '</label><input type="text" class="redactor_input redactor_link_text" id="redactor_link_url_text" /><label><input type="checkbox" id="redactor_link_blank"> ' + this.opts.curLang.link_new_tab + '</label></div><div class="redactor_tab" id="redactor_tab2" style="display: none;"><label>Email</label><input type="text" id="redactor_link_mailto" class="redactor_input" /><label>' + this.opts.curLang.text + '</label><input type="text" class="redactor_input redactor_link_text" id="redactor_link_mailto_text" /></div><div class="redactor_tab" id="redactor_tab3" style="display: none;"><label>' + this.opts.curLang.anchor + '</label><input type="text" class="redactor_input" id="redactor_link_anchor"  /><label>' + this.opts.curLang.text + '</label><input type="text" class="redactor_input redactor_link_text" id="redactor_link_anchor_text" /></div></form></section><footer><button class="redactor_modal_btn redactor_btn_modal_close">' + this.opts.curLang.cancel + '</button><input type="button" class="redactor_modal_btn redactor_modal_action_btn" id="redactor_insert_link_btn" value="' + this.opts.curLang.insert + '" /></footer>', modal_table: String() + "<section><label>" + this.opts.curLang.rows + '</label><input type="text" size="5" value="2" id="redactor_table_rows" /><label>' + this.opts.curLang.columns + '</label><input type="text" size="5" value="3" id="redactor_table_columns" /></section><footer><button class="redactor_modal_btn redactor_btn_modal_close">' + this.opts.curLang.cancel + '</button><input type="button" name="upload" class="redactor_modal_btn redactor_modal_action_btn" id="redactor_insert_table_btn" value="' + this.opts.curLang.insert + '" /></footer>', modal_video: String() + '<section><form id="redactorInsertVideoForm"><label>' + this.opts.curLang.video_html_code + '</label><textarea id="redactor_insert_video_area" style="width: 99%; height: 160px;"></textarea></form></section><footer><button class="redactor_modal_btn redactor_btn_modal_close">' + this.opts.curLang.cancel + '</button><input type="button" class="redactor_modal_btn redactor_modal_action_btn" id="redactor_insert_video_btn" value="' + this.opts.curLang.insert + '" /></footer>' }); },
        modalInit: function(m, h, f, n) {
            var e = c("#redactor_modal_overlay");
            if (!e.length) {
                this.$overlay = e = c('<div id="redactor_modal_overlay" style="display: none;"></div>');
                c("body").prepend(this.$overlay);
            }
            if (this.opts.modalOverlay) { e.show().on("click", c.proxy(this.modalClose, this)); }
            var j = c("#redactor_modal");
            if (!j.length) {
                this.$modal = j = c('<div id="redactor_modal" style="display: none;"><div id="redactor_modal_close">&times;</div><header id="redactor_modal_header"></header><div id="redactor_modal_inner"></div></div>');
                c("body").append(this.$modal);
            }
            c("#redactor_modal_close").on("click", c.proxy(this.modalClose, this));
            this.hdlModalClose = c.proxy(function(o) { if (o.keyCode === this.keyCode.ESC) { this.modalClose(); return false; } }, this);
            c(document).keyup(this.hdlModalClose);
            this.$editor.keyup(this.hdlModalClose);
            this.modalcontent = false;
            if (h.indexOf("#") == 0) {
                this.modalcontent = c(h);
                c("#redactor_modal_inner").empty().append(this.modalcontent.html());
                this.modalcontent.html("");
            } else { c("#redactor_modal_inner").empty().append(h); }
            j.find("#redactor_modal_header").html(m);
            if (typeof c.fn.draggable !== "undefined") {
                j.draggable({ handle: "#redactor_modal_header" });
                j.find("#redactor_modal_header").css("cursor", "move");
            }
            var l = c("#redactor_tabs");
            if (l.length) {
                var g = this;
                l.find("a").each(function(o, p) {
                    o++;
                    c(p).on("click", function(r) {
                        r.preventDefault();
                        l.find("a").removeClass("redactor_tabs_act");
                        c(this).addClass("redactor_tabs_act");
                        c(".redactor_tab").hide();
                        c("#redactor_tab" + o).show();
                        c("#redactor_tab_selected").val(o);
                        if (g.isMobile() === false) {
                            var q = j.outerHeight();
                            j.css("margin-top", "-" + (q + 10) / 2 + "px");
                        }
                    });
                });
            }
            j.find(".redactor_btn_modal_close").on("click", c.proxy(this.modalClose, this));
            if (this.opts.autoresize === true) { this.saveModalScroll = this.document.body.scrollTop; } else { this.saveModalScroll = this.$editor.scrollTop(); }
            if (this.isMobile() === false) {
                j.css({ position: "fixed", top: "-2000px", left: "50%", width: f + "px", marginLeft: "-" + (f + 60) / 2 + "px" }).show();
                this.modalSaveBodyOveflow = c(document.body).css("overflow");
                c(document.body).css("overflow", "hidden");
            } else { j.css({ position: "fixed", width: "100%", height: "100%", top: "0", left: "0", margin: "0", minHeight: "300px" }).show(); }
            if (typeof n === "function") { n(); }
            if (this.isMobile() === false) {
                setTimeout(function() {
                    var o = j.outerHeight();
                    j.css({ top: "50%", height: "auto", minHeight: "auto", marginTop: "-" + (o + 10) / 2 + "px" });
                }, 10);
            }
        },
        modalClose: function() {
            c("#redactor_modal_close").off("click", this.modalClose);
            c("#redactor_modal").fadeOut("fast", c.proxy(function() {
                var e = c("#redactor_modal_inner");
                if (this.modalcontent !== false) {
                    this.modalcontent.html(e.html());
                    this.modalcontent = false;
                }
                e.html("");
                if (this.opts.modalOverlay) { c("#redactor_modal_overlay").hide().off("click", this.modalClose); }
                c(document).unbind("keyup", this.hdlModalClose);
                this.$editor.unbind("keyup", this.hdlModalClose);
                this.selectionRestore();
                if (this.opts.autoresize && this.saveModalScroll) { c(this.document.body).scrollTop(this.saveModalScroll); } else { if (this.opts.autoresize === false && this.saveModalScroll) { this.$editor.scrollTop(this.saveModalScroll); } }
            }, this));
            if (this.isMobile() === false) { c(document.body).css("overflow", this.modalSaveBodyOveflow ? this.modalSaveBodyOveflow : "visible"); }
            return false;
        },
        modalSetTab: function(e) {
            c(".redactor_tab").hide();
            c("#redactor_tabs").find("a").removeClass("redactor_tabs_act").eq(e - 1).addClass("redactor_tabs_act");
            c("#redactor_tab" + e).show();
        },
        s3handleFileSelect: function(l) { var h = l.target.files; for (var g = 0, j; j = h[g]; g++) { this.s3uploadFile(j); } },
        s3uploadFile: function(e) { this.s3executeOnSignedUrl(e, c.proxy(function(f) { this.s3uploadToS3(e, f); }, this)); },
        s3executeOnSignedUrl: function(e, h) {
            var f = new XMLHttpRequest();
            var g = "?";
            if (this.opts.s3.search(/\?/) != "-1") { g = "&"; }
            f.open("GET", this.opts.s3 + g + "name=" + e.name + "&type=" + e.type, true);
            if (f.overrideMimeType) { f.overrideMimeType("text/plain; charset=x-user-defined"); }
            f.onreadystatechange = function(j) {
                if (this.readyState == 4 && this.status == 200) {
                    c("#redactor-progress").fadeIn();
                    h(decodeURIComponent(this.responseText));
                } else { if (this.readyState == 4 && this.status != 200) {} }
            };
            f.send();
        },
        s3createCORSRequest: function(g, e) {
            var f = new XMLHttpRequest();
            if ("withCredentials" in f) { f.open(g, e, true); } else {
                if (typeof XDomainRequest != "undefined") {
                    f = new XDomainRequest();
                    f.open(g, e);
                } else { f = null; }
            }
            return f;
        },
        s3uploadToS3: function(f, e) {
            var g = this.s3createCORSRequest("PUT", e);
            if (!g) {} else {
                g.onload = c.proxy(function() {
                    if (g.status == 200) {
                        c("#redactor-progress, #redactor-progress-drag").hide();
                        var l = e.split("?");
                        if (!l[0]) { return false; }
                        this.selectionRestore();
                        var h = "";
                        h = '<img id="image-marker" src="' + l[0] + '" />';
                        if (this.opts.paragraphy) { h = "<p>" + h + "</p>"; }
                        this.execCommand("inserthtml", h, false);
                        var j = c(this.$editor.find("img#image-marker"));
                        if (j.length) { j.removeAttr("id"); } else { j = false; }
                        this.sync();
                        this.callback("imageUpload", j, false);
                        this.modalClose();
                        this.observeImages();
                    } else {}
                }, this);
                g.onerror = function() {};
                g.upload.onprogress = function(h) {};
                g.setRequestHeader("Content-Type", f.type);
                g.setRequestHeader("x-amz-acl", "public-read");
                g.send(f);
            }
        },
        uploadInit: function(g, e) {
            this.uploadOptions = { url: false, success: false, error: false, start: false, trigger: false, auto: false, input: false };
            c.extend(this.uploadOptions, e);
            var f = c("#" + g);
            if (f.length && f[0].tagName === "INPUT") {
                this.uploadOptions.input = f;
                this.el = c(f[0].form);
            } else { this.el = f; }
            this.element_action = this.el.attr("action");
            if (this.uploadOptions.auto) {
                c(this.uploadOptions.input).change(c.proxy(function(h) {
                    this.el.submit(function(j) { return false; });
                    this.uploadSubmit(h);
                }, this));
            } else { if (this.uploadOptions.trigger) { c("#" + this.uploadOptions.trigger).click(c.proxy(this.uploadSubmit, this)); } }
        },
        uploadSubmit: function(f) {
            c("#redactor-progress").fadeIn();
            this.uploadForm(this.element, this.uploadFrame());
        },
        uploadFrame: function() {
            this.id = "f" + Math.floor(Math.random() * 99999);
            var f = this.document.createElement("div");
            var e = '<iframe style="display:none" id="' + this.id + '" name="' + this.id + '"></iframe>';
            f.innerHTML = e;
            c(f).appendTo("body");
            if (this.uploadOptions.start) { this.uploadOptions.start(); }
            c("#" + this.id).load(c.proxy(this.uploadLoaded, this));
            return this.id;
        },
        uploadForm: function(j, h) {
            if (this.uploadOptions.input) {
                var l = "redactorUploadForm" + this.id,
                    e = "redactorUploadFile" + this.id;
                this.form = c('<form  action="' + this.uploadOptions.url + '" method="POST" target="' + h + '" name="' + l + '" id="' + l + '" enctype="multipart/form-data" />');
                if (this.opts.uploadFields !== false && typeof this.opts.uploadFields === "object") {
                    c.each(this.opts.uploadFields, c.proxy(function(n, f) {
                        if (f != null && f.toString().indexOf("#") === 0) { f = c(f).val(); }
                        var o = c("<input/>", { type: "hidden", name: n, value: f });
                        c(this.form).append(o);
                    }, this));
                }
                var g = this.uploadOptions.input;
                var m = c(g).clone();
                c(g).attr("id", e).before(m).appendTo(this.form);
                c(this.form).css("position", "absolute").css("top", "-2000px").css("left", "-2000px").appendTo("body");
                this.form.submit();
            } else {
                j.attr("target", h).attr("method", "POST").attr("enctype", "multipart/form-data").attr("action", this.uploadOptions.url);
                this.element.submit();
            }
        },
        uploadLoaded: function() {
            var h = c("#" + this.id)[0],
                j;
            if (h.contentDocument) { j = h.contentDocument; } else { if (h.contentWindow) { j = h.contentWindow.document; } else { j = window.frames[this.id].document; } }
            if (this.uploadOptions.success) {
                c("#redactor-progress").hide();
                if (typeof j !== "undefined") {
                    var g = j.body.innerHTML;
                    var f = g.match(/\{(.|\n)*\}/)[0];
                    f = f.replace(/^\[/, "");
                    f = f.replace(/\]$/, "");
                    var e = c.parseJSON(f);
                    if (typeof e.error == "undefined") { this.uploadOptions.success(e); } else {
                        this.uploadOptions.error(this, e);
                        this.modalClose();
                    }
                } else {
                    this.modalClose();
                    alert("Upload failed!");
                }
            }
            this.el.attr("action", this.element_action);
            this.el.attr("target", "");
        },
        draguploadInit: function(f, e) {
            this.draguploadOptions = c.extend({ url: false, success: false, error: false, preview: false, uploadFields: false, text: this.opts.curLang.drop_file_here, atext: this.opts.curLang.or_choose, uploadParam: false }, e);
            if (window.FormData === undefined) { return false; }
            this.droparea = c('<div class="redactor_droparea"></div>');
            this.dropareabox = c('<div class="redactor_dropareabox">' + this.draguploadOptions.text + "</div>");
            this.dropalternative = c('<div class="redactor_dropalternative">' + this.draguploadOptions.atext + "</div>");
            this.droparea.append(this.dropareabox);
            c(f).before(this.droparea);
            c(f).before(this.dropalternative);
            this.dropareabox.on("dragover", c.proxy(function() { return this.draguploadOndrag(); }, this));
            this.dropareabox.on("dragleave", c.proxy(function() { return this.draguploadOndragleave(); }, this));
            this.dropareabox.get(0).ondrop = c.proxy(function(g) {
                g.preventDefault();
                this.dropareabox.removeClass("hover").addClass("drop");
                this.dragUploadAjax(this.draguploadOptions.url, g.dataTransfer.files[0], false, false, false, this.draguploadOptions.uploadParam);
            }, this);
        },
        dragUploadAjax: function(h, l, f, g, n, m) {
            if (!f) {
                var o = c.ajaxSettings.xhr();
                if (o.upload) { o.upload.addEventListener("progress", c.proxy(this.uploadProgress, this), false); }
                c.ajaxSetup({ xhr: function() { return o; } });
            }
            var j = new FormData();
            if (m !== false) { j.append(m, l); } else { j.append("file", l); }
            if (this.opts.uploadFields !== false && typeof this.opts.uploadFields === "object") {
                c.each(this.opts.uploadFields, c.proxy(function(p, e) {
                    if (e != null && e.toString().indexOf("#") === 0) { e = c(e).val(); }
                    j.append(p, e);
                }, this));
            }
            c.ajax({
                url: h,
                dataType: "html",
                data: j,
                cache: false,
                contentType: false,
                processData: false,
                type: "POST",
                success: c.proxy(function(q) {
                    q = q.replace(/^\[/, "");
                    q = q.replace(/\]$/, "");
                    var p = (typeof q === "string" ? c.parseJSON(q) : q);
                    if (f) {
                        g.fadeOut("slow", function() { c(this).remove(); });
                        var e = c("<img>");
                        e.attr("src", p.filelink).attr("id", "drag-image-marker");
                        this.insertNodeToCaretPositionFromPoint(n, e[0]);
                        var r = c(this.$editor.find("img#drag-image-marker"));
                        if (r.length) { r.removeAttr("id"); } else { r = false; }
                        this.sync();
                        this.observeImages();
                        if (r) { this.callback("imageUpload", r, p); }
                        if (typeof p.error !== "undefined") { this.callback("imageUploadError", p); }
                    } else {
                        if (typeof p.error == "undefined") { this.draguploadOptions.success(p); } else {
                            this.draguploadOptions.error(this, p);
                            this.draguploadOptions.success(false);
                        }
                    }
                }, this)
            });
        },
        draguploadOndrag: function() { this.dropareabox.addClass("hover"); return false; },
        draguploadOndragleave: function() { this.dropareabox.removeClass("hover"); return false; },
        uploadProgress: function(g, h) {
            var f = g.loaded ? parseInt(g.loaded / g.total * 100, 10) : g;
            this.dropareabox.text("Loading " + f + "% " + (h || ""));
        },
        isMobile: function() { return /(iPhone|iPod|BlackBerry|Android)/.test(navigator.userAgent); },
        normalize: function(e) { if (typeof(e) === "undefined") { return 0; } return parseInt(e.replace("px", ""), 10); },
        outerHtml: function(e) { return c("<div>").append(c(e).eq(0).clone()).html(); },
        isString: function(e) { return Object.prototype.toString.call(e) == "[object String]"; },
        isEmpty: function(e) {
            e = e.replace(/&#x200b;|<br>|<br\/>|&nbsp;/gi, "");
            e = e.replace(/\s/g, "");
            e = e.replace(/^<p>[^\W\w\D\d]*?<\/p>$/i, "");
            return e == "";
        },
        isIe11: function() { return !!navigator.userAgent.match(/Trident\/7\./); },
        browser: function(f) { var g = navigator.userAgent.toLowerCase(); var e = /(opr)[\/]([\w.]+)/.exec(g) || /(chrome)[ \/]([\w.]+)/.exec(g) || /(webkit)[ \/]([\w.]+).*(safari)[ \/]([\w.]+)/.exec(g) || /(webkit)[ \/]([\w.]+)/.exec(g) || /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(g) || /(msie) ([\w.]+)/.exec(g) || g.indexOf("trident") >= 0 && /(rv)(?::| )([\w.]+)/.exec(g) || g.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(g) || []; if (f == "version") { return e[2]; } if (f == "webkit") { return (e[1] == "chrome" || e[1] == "webkit"); } if (e[1] == "rv") { return f == "msie"; } if (e[1] == "opr") { return f == "webkit"; } return f == e[1]; },
        oldIE: function() { if (this.browser("msie") && parseInt(this.browser("version"), 10) < 9) { return true; } return false; },
        getFragmentHtml: function(f) {
            var e = f.cloneNode(true);
            var g = this.document.createElement("div");
            g.appendChild(e);
            return g.innerHTML;
        },
        extractContent: function() { var e = this.$editor[0]; var g = this.document.createDocumentFragment(); var f; while ((f = e.firstChild)) { g.appendChild(f); } return g; },
        isParentRedactor: function(e) { if (!e) { return false; } if (this.opts.iframe) { return e; } if (c(e).parents("div.redactor_editor").length == 0 || c(e).hasClass("redactor_editor")) { return false; } else { return e; } },
        currentOrParentIs: function(e) {
            var f = this.getParent(),
                g = this.getCurrent();
            return f && f.tagName === e ? f : g && g.tagName === e ? g : false;
        },
        isEndOfElement: function() { var f = this.getBlock(); var h = this.getCaretOffset(f); var g = c.trim(c(f).text()).replace(/\n\r\n/g, ""); var e = g.length; if (h == e) { return true; } else { return false; } },
        isFocused: function() { var e, f = this.getSelection(); if (f && f.rangeCount && f.rangeCount > 0) { e = f.getRangeAt(0).startContainer; } if (!e) { return false; } if (this.opts.iframe) { if (this.getCaretOffsetRange().equals()) { return !this.$editor.is(e); } else { return true; } } return c(e).closest("div.redactor_editor").length != 0; },
        removeEmptyAttr: function(f, e) { if (c(f).attr(e) == "") { c(f).removeAttr(e); } },
        removeFromArrayByValue: function(g, f) { var e = null; while ((e = g.indexOf(f)) !== -1) { g.splice(e, 1); } return g; }
    };
    b.prototype.init.prototype = b.prototype;
    c.Redactor.fn.formatLinkify = function(x, u, m, r, j) {
        var s = /(^|&lt;|\s)(www\..+?\..+?)(\s|&gt;|$)/g,
            q = /(^|&lt;|\s)(((https?|ftp):\/\/|mailto:).+?)(\s|&gt;|$)/g,
            e = /(https?:\/\/.*\.(?:png|jpg|jpeg|gif))/gi,
            w = /https?:\/\/(?:[0-9A-Z-]+\.)?(?:youtu\.be\/|youtube\.com\S*[^\w\-\s])([\w\-]{11})(?=[^\w\-]|$)(?![?=&+%\w.-]*(?:['"][^<>]*>|<\/a>))[?=&+%\w.-]*/ig,
            t = /https?:\/\/(www\.)?vimeo.com\/(\d+)($|\/)/;
        var v = (this.$editor ? this.$editor.get(0) : this).childNodes,
            l = v.length;
        while (l--) {
            var h = v[l];
            if (h.nodeType === 3) {
                var p = h.nodeValue;
                if (r && p) {
                    var o = '<iframe width="500" height="281" src="',
                        g = '" frameborder="0" allowfullscreen></iframe>';
                    if (p.match(w)) {
                        p = p.replace(w, o + "//www.youtube.com/embed/$1" + g);
                        c(h).after(p).remove();
                    } else {
                        if (p.match(t)) {
                            p = p.replace(t, o + "//player.vimeo.com/video/$2" + g);
                            c(h).after(p).remove();
                        }
                    }
                }
                if (m && p && p.match(e)) {
                    p = p.replace(e, '<img src="$1">');
                    c(h).after(p).remove();
                }
                if (u && p && (p.match(s) || p.match(q))) {
                    var f = (p.match(s) || p.match(q));
                    f = f[0];
                    if (f.length > j) { f = f.substring(0, j) + "..."; }
                    p = p.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(s, '$1<a href="' + x + '$2">' + c.trim(f) + "</a>$3").replace(q, '$1<a href="$2">' + c.trim(f) + "</a>$5");
                    c(h).after(p).remove();
                }
            } else { if (h.nodeType === 1 && !/^(a|button|textarea)$/i.test(h.tagName)) { c.Redactor.fn.formatLinkify.call(h, x, u, m, r, j); } }
        }
    };
})(jQuery);

/*!
 * accounting.js v0.4.1, copyright 2014 Open Exchange Rates, MIT license, http://openexchangerates.github.io/accounting.js
 */
(function(p, z) {
    function q(a) { return !!("" === a || a && a.charCodeAt && a.substr) }

    function m(a) { return u ? u(a) : "[object Array]" === v.call(a) }

    function r(a) { return "[object Object]" === v.call(a) }

    function s(a, b) {
        var d, a = a || {},
            b = b || {};
        for (d in b) b.hasOwnProperty(d) && null == a[d] && (a[d] = b[d]);
        return a
    }

    function j(a, b, d) {
        var c = [],
            e, h;
        if (!a) return c;
        if (w && a.map === w) return a.map(b, d);
        for (e = 0, h = a.length; e < h; e++) c[e] = b.call(d, a[e], e, a);
        return c
    }

    function n(a, b) { a = Math.round(Math.abs(a)); return isNaN(a) ? b : a }

    function x(a) { var b = c.settings.currency.format; "function" === typeof a && (a = a()); return q(a) && a.match("%v") ? { pos: a, neg: a.replace("-", "").replace("%v", "-%v"), zero: a } : !a || !a.pos || !a.pos.match("%v") ? !q(b) ? b : c.settings.currency.format = { pos: b, neg: b.replace("%v", "-%v"), zero: b } : a }
    var c = { version: "0.4.1", settings: { currency: { symbol: "$", format: "%s%v", decimal: ".", thousand: ",", precision: 2, grouping: 3 }, number: { precision: 0, grouping: 3, thousand: ",", decimal: "." } } },
        w = Array.prototype.map,
        u = Array.isArray,
        v = Object.prototype.toString,
        o = c.unformat = c.parse = function(a, b) {
            if (m(a)) return j(a, function(a) { return o(a, b) });
            a = a || 0;
            if ("number" === typeof a) return a;
            var b = b || ".",
                c = RegExp("[^0-9-" + b + "]", ["g"]),
                c = parseFloat(("" + a).replace(/\((.*)\)/, "-$1").replace(c, "").replace(b, "."));
            return !isNaN(c) ? c : 0
        },
        y = c.toFixed = function(a, b) {
            var b = n(b, c.settings.number.precision),
                d = Math.pow(10, b);
            return (Math.round(c.unformat(a) * d) / d).toFixed(b)
        },
        t = c.formatNumber = c.format = function(a, b, d, i) {
            if (m(a)) return j(a, function(a) { return t(a, b, d, i) });
            var a = o(a),
                e = s(r(b) ? b : { precision: b, thousand: d, decimal: i }, c.settings.number),
                h = n(e.precision),
                f = 0 > a ? "-" : "",
                g = parseInt(y(Math.abs(a || 0), h), 10) + "",
                l = 3 < g.length ? g.length % 3 : 0;
            return f + (l ? g.substr(0, l) + e.thousand : "") + g.substr(l).replace(/(\d{3})(?=\d)/g, "$1" + e.thousand) + (h ? e.decimal + y(Math.abs(a), h).split(".")[1] : "")
        },
        A = c.formatMoney = function(a, b, d, i, e, h) {
            if (m(a)) return j(a, function(a) { return A(a, b, d, i, e, h) });
            var a = o(a),
                f = s(r(b) ? b : { symbol: b, precision: d, thousand: i, decimal: e, format: h }, c.settings.currency),
                g = x(f.format);
            return (0 < a ? g.pos : 0 > a ? g.neg : g.zero).replace("%s", f.symbol).replace("%v", t(Math.abs(a), n(f.precision), f.thousand, f.decimal))
        };
    c.formatColumn = function(a, b, d, i, e, h) {
        if (!a) return [];
        var f = s(r(b) ? b : { symbol: b, precision: d, thousand: i, decimal: e, format: h }, c.settings.currency),
            g = x(f.format),
            l = g.pos.indexOf("%s") < g.pos.indexOf("%v") ? !0 : !1,
            k = 0,
            a = j(a, function(a) {
                if (m(a)) return c.formatColumn(a, f);
                a = o(a);
                a = (0 < a ? g.pos : 0 > a ? g.neg : g.zero).replace("%s", f.symbol).replace("%v", t(Math.abs(a), n(f.precision), f.thousand, f.decimal));
                if (a.length > k) k = a.length;
                return a
            });
        return j(a, function(a) { return q(a) && a.length < k ? l ? a.replace(f.symbol, f.symbol + Array(k - a.length + 1).join(" ")) : Array(k - a.length + 1).join(" ") + a : a })
    };
    if ("undefined" !== typeof exports) {
        if ("undefined" !== typeof module && module.exports) exports = module.exports = c;
        exports.accounting = c
    } else "function" === typeof define && define.amd ? define([], function() { return c }) : (c.noConflict = function(a) {
        return function() {
            p.accounting = a;
            c.noConflict = z;
            return c
        }
    }(p.accounting), p.accounting = c)
})(this);

/** Abstract base class for collection plugins v1.0.1.
	Written by Keith Wood (kbwood{at}iinet.com.au) December 2013.
	Licensed under the MIT (https://github.com/jquery/jquery/blob/master/MIT-LICENSE.txt) license. */
(function() {
    var j = false;
    window.JQClass = function() {};
    JQClass.classes = {};
    JQClass.extend = function extender(f) {
        var g = this.prototype;
        j = true;
        var h = new this();
        j = false;
        for (var i in f) {
            h[i] = typeof f[i] == 'function' && typeof g[i] == 'function' ? (function(d, e) {
                return function() {
                    var b = this._super;
                    this._super = function(a) { return g[d].apply(this, a || []) };
                    var c = e.apply(this, arguments);
                    this._super = b;
                    return c
                }
            })(i, f[i]) : f[i]
        }

        function JQClass() { if (!j && this._init) { this._init.apply(this, arguments) } }
        JQClass.prototype = h;
        JQClass.prototype.constructor = JQClass;
        JQClass.extend = extender;
        return JQClass
    }
})();
(function($) {
    JQClass.classes.JQPlugin = JQClass.extend({
        name: 'plugin',
        defaultOptions: {},
        regionalOptions: {},
        _getters: [],
        _getMarker: function() { return 'is-' + this.name },
        _init: function() {
            $.extend(this.defaultOptions, (this.regionalOptions && this.regionalOptions['']) || {});
            var c = camelCase(this.name);
            $[c] = this;
            $.fn[c] = function(a) {
                var b = Array.prototype.slice.call(arguments, 1);
                if ($[c]._isNotChained(a, b)) { return $[c][a].apply($[c], [this[0]].concat(b)) }
                return this.each(function() {
                    if (typeof a === 'string') {
                        if (a[0] === '_' || !$[c][a]) { throw 'Unknown method: ' + a; }
                        $[c][a].apply($[c], [this].concat(b))
                    } else { $[c]._attach(this, a) }
                })
            }
        },
        setDefaults: function(a) { $.extend(this.defaultOptions, a || {}) },
        _isNotChained: function(a, b) { if (a === 'option' && (b.length === 0 || (b.length === 1 && typeof b[0] === 'string'))) { return true } return $.inArray(a, this._getters) > -1 },
        _attach: function(a, b) {
            a = $(a);
            if (a.hasClass(this._getMarker())) { return }
            a.addClass(this._getMarker());
            b = $.extend({}, this.defaultOptions, this._getMetadata(a), b || {});
            var c = $.extend({ name: this.name, elem: a, options: b }, this._instSettings(a, b));
            a.data(this.name, c);
            this._postAttach(a, c);
            this.option(a, b)
        },
        _instSettings: function(a, b) { return {} },
        _postAttach: function(a, b) {},
        _getMetadata: function(d) {
            try {
                var f = d.data(this.name.toLowerCase()) || '';
                f = f.replace(/'/g, '"');
                f = f.replace(/([a-zA-Z0-9]+):/g, function(a, b, i) { var c = f.substring(0, i).match(/"/g); return (!c || c.length % 2 === 0 ? '"' + b + '":' : b + ':') });
                f = $.parseJSON('{' + f + '}');
                for (var g in f) { var h = f[g]; if (typeof h === 'string' && h.match(/^new Date\((.*)\)$/)) { f[g] = eval(h) } }
                return f
            } catch (e) { return {} }
        },
        _getInst: function(a) { return $(a).data(this.name) || {} },
        option: function(a, b, c) {
            a = $(a);
            var d = a.data(this.name);
            if (!b || (typeof b === 'string' && c == null)) { var e = (d || {}).options; return (e && b ? e[b] : e) }
            if (!a.hasClass(this._getMarker())) { return }
            var e = b || {};
            if (typeof b === 'string') {
                e = {};
                e[b] = c
            }
            this._optionsChanged(a, d, e);
            $.extend(d.options, e)
        },
        _optionsChanged: function(a, b, c) {},
        destroy: function(a) {
            a = $(a);
            if (!a.hasClass(this._getMarker())) { return }
            this._preDestroy(a, this._getInst(a));
            a.removeData(this.name).removeClass(this._getMarker())
        },
        _preDestroy: function(a, b) {}
    });

    function camelCase(c) { return c.replace(/-([a-z])/g, function(a, b) { return b.toUpperCase() }) }
    $.JQPlugin = {
        createPlugin: function(a, b) {
            if (typeof a === 'object') {
                b = a;
                a = 'JQPlugin'
            }
            a = camelCase(a);
            var c = camelCase(b.name);
            JQClass.classes[c] = JQClass.classes[a].extend(b);
            new JQClass.classes[c]()
        }
    }
})(jQuery);

//     Underscore.js 1.8.3
//     http://underscorejs.org
//     (c) 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
//     Underscore may be freely distributed under the MIT license.
(function() {
    function n(n) {
        function t(t, r, e, u, i, o) {
            for (; i >= 0 && o > i; i += n) {
                var a = u ? u[i] : i;
                e = r(e, t[a], a, t)
            }
            return e
        }
        return function(r, e, u, i) {
            e = b(e, i, 4);
            var o = !k(r) && m.keys(r),
                a = (o || r).length,
                c = n > 0 ? 0 : a - 1;
            return arguments.length < 3 && (u = r[o ? o[c] : c], c += n), t(r, e, u, o, c, a)
        }
    }

    function t(n) {
        return function(t, r, e) {
            r = x(r, e);
            for (var u = O(t), i = n > 0 ? 0 : u - 1; i >= 0 && u > i; i += n)
                if (r(t[i], i, t)) return i;
            return -1
        }
    }

    function r(n, t, r) {
        return function(e, u, i) {
            var o = 0,
                a = O(e);
            if ("number" == typeof i) n > 0 ? o = i >= 0 ? i : Math.max(i + a, o) : a = i >= 0 ? Math.min(i + 1, a) : i + a + 1;
            else if (r && i && a) return i = r(e, u), e[i] === u ? i : -1;
            if (u !== u) return i = t(l.call(e, o, a), m.isNaN), i >= 0 ? i + o : -1;
            for (i = n > 0 ? o : a - 1; i >= 0 && a > i; i += n)
                if (e[i] === u) return i;
            return -1
        }
    }

    function e(n, t) {
        var r = I.length,
            e = n.constructor,
            u = m.isFunction(e) && e.prototype || a,
            i = "constructor";
        for (m.has(n, i) && !m.contains(t, i) && t.push(i); r--;) i = I[r], i in n && n[i] !== u[i] && !m.contains(t, i) && t.push(i)
    }
    var u = this,
        i = u._,
        o = Array.prototype,
        a = Object.prototype,
        c = Function.prototype,
        f = o.push,
        l = o.slice,
        s = a.toString,
        p = a.hasOwnProperty,
        h = Array.isArray,
        v = Object.keys,
        g = c.bind,
        y = Object.create,
        d = function() {},
        m = function(n) { return n instanceof m ? n : this instanceof m ? void(this._wrapped = n) : new m(n) };
    "undefined" != typeof exports ? ("undefined" != typeof module && module.exports && (exports = module.exports = m), exports._ = m) : u._ = m, m.VERSION = "1.8.3";
    var b = function(n, t, r) {
            if (t === void 0) return n;
            switch (null == r ? 3 : r) {
                case 1:
                    return function(r) { return n.call(t, r) };
                case 2:
                    return function(r, e) { return n.call(t, r, e) };
                case 3:
                    return function(r, e, u) { return n.call(t, r, e, u) };
                case 4:
                    return function(r, e, u, i) { return n.call(t, r, e, u, i) }
            }
            return function() { return n.apply(t, arguments) }
        },
        x = function(n, t, r) { return null == n ? m.identity : m.isFunction(n) ? b(n, t, r) : m.isObject(n) ? m.matcher(n) : m.property(n) };
    m.iteratee = function(n, t) { return x(n, t, 1 / 0) };
    var _ = function(n, t) {
            return function(r) {
                var e = arguments.length;
                if (2 > e || null == r) return r;
                for (var u = 1; e > u; u++)
                    for (var i = arguments[u], o = n(i), a = o.length, c = 0; a > c; c++) {
                        var f = o[c];
                        t && r[f] !== void 0 || (r[f] = i[f])
                    }
                return r
            }
        },
        j = function(n) {
            if (!m.isObject(n)) return {};
            if (y) return y(n);
            d.prototype = n;
            var t = new d;
            return d.prototype = null, t
        },
        w = function(n) { return function(t) { return null == t ? void 0 : t[n] } },
        A = Math.pow(2, 53) - 1,
        O = w("length"),
        k = function(n) { var t = O(n); return "number" == typeof t && t >= 0 && A >= t };
    m.each = m.forEach = function(n, t, r) {
        t = b(t, r);
        var e, u;
        if (k(n))
            for (e = 0, u = n.length; u > e; e++) t(n[e], e, n);
        else { var i = m.keys(n); for (e = 0, u = i.length; u > e; e++) t(n[i[e]], i[e], n) }
        return n
    }, m.map = m.collect = function(n, t, r) {
        t = x(t, r);
        for (var e = !k(n) && m.keys(n), u = (e || n).length, i = Array(u), o = 0; u > o; o++) {
            var a = e ? e[o] : o;
            i[o] = t(n[a], a, n)
        }
        return i
    }, m.reduce = m.foldl = m.inject = n(1), m.reduceRight = m.foldr = n(-1), m.find = m.detect = function(n, t, r) { var e; return e = k(n) ? m.findIndex(n, t, r) : m.findKey(n, t, r), e !== void 0 && e !== -1 ? n[e] : void 0 }, m.filter = m.select = function(n, t, r) { var e = []; return t = x(t, r), m.each(n, function(n, r, u) { t(n, r, u) && e.push(n) }), e }, m.reject = function(n, t, r) { return m.filter(n, m.negate(x(t)), r) }, m.every = m.all = function(n, t, r) { t = x(t, r); for (var e = !k(n) && m.keys(n), u = (e || n).length, i = 0; u > i; i++) { var o = e ? e[i] : i; if (!t(n[o], o, n)) return !1 } return !0 }, m.some = m.any = function(n, t, r) { t = x(t, r); for (var e = !k(n) && m.keys(n), u = (e || n).length, i = 0; u > i; i++) { var o = e ? e[i] : i; if (t(n[o], o, n)) return !0 } return !1 }, m.contains = m.includes = m.include = function(n, t, r, e) { return k(n) || (n = m.values(n)), ("number" != typeof r || e) && (r = 0), m.indexOf(n, t, r) >= 0 }, m.invoke = function(n, t) {
        var r = l.call(arguments, 2),
            e = m.isFunction(t);
        return m.map(n, function(n) { var u = e ? t : n[t]; return null == u ? u : u.apply(n, r) })
    }, m.pluck = function(n, t) { return m.map(n, m.property(t)) }, m.where = function(n, t) { return m.filter(n, m.matcher(t)) }, m.findWhere = function(n, t) { return m.find(n, m.matcher(t)) }, m.max = function(n, t, r) {
        var e, u, i = -1 / 0,
            o = -1 / 0;
        if (null == t && null != n) { n = k(n) ? n : m.values(n); for (var a = 0, c = n.length; c > a; a++) e = n[a], e > i && (i = e) } else t = x(t, r), m.each(n, function(n, r, e) { u = t(n, r, e), (u > o || u === -1 / 0 && i === -1 / 0) && (i = n, o = u) });
        return i
    }, m.min = function(n, t, r) {
        var e, u, i = 1 / 0,
            o = 1 / 0;
        if (null == t && null != n) { n = k(n) ? n : m.values(n); for (var a = 0, c = n.length; c > a; a++) e = n[a], i > e && (i = e) } else t = x(t, r), m.each(n, function(n, r, e) { u = t(n, r, e), (o > u || 1 / 0 === u && 1 / 0 === i) && (i = n, o = u) });
        return i
    }, m.shuffle = function(n) { for (var t, r = k(n) ? n : m.values(n), e = r.length, u = Array(e), i = 0; e > i; i++) t = m.random(0, i), t !== i && (u[i] = u[t]), u[t] = r[i]; return u }, m.sample = function(n, t, r) { return null == t || r ? (k(n) || (n = m.values(n)), n[m.random(n.length - 1)]) : m.shuffle(n).slice(0, Math.max(0, t)) }, m.sortBy = function(n, t, r) {
        return t = x(t, r), m.pluck(m.map(n, function(n, r, e) { return { value: n, index: r, criteria: t(n, r, e) } }).sort(function(n, t) {
            var r = n.criteria,
                e = t.criteria;
            if (r !== e) { if (r > e || r === void 0) return 1; if (e > r || e === void 0) return -1 }
            return n.index - t.index
        }), "value")
    };
    var F = function(n) {
        return function(t, r, e) {
            var u = {};
            return r = x(r, e), m.each(t, function(e, i) {
                var o = r(e, i, t);
                n(u, e, o)
            }), u
        }
    };
    m.groupBy = F(function(n, t, r) { m.has(n, r) ? n[r].push(t) : n[r] = [t] }), m.indexBy = F(function(n, t, r) { n[r] = t }), m.countBy = F(function(n, t, r) { m.has(n, r) ? n[r]++ : n[r] = 1 }), m.toArray = function(n) { return n ? m.isArray(n) ? l.call(n) : k(n) ? m.map(n, m.identity) : m.values(n) : [] }, m.size = function(n) { return null == n ? 0 : k(n) ? n.length : m.keys(n).length }, m.partition = function(n, t, r) {
        t = x(t, r);
        var e = [],
            u = [];
        return m.each(n, function(n, r, i) {
            (t(n, r, i) ? e : u).push(n)
        }), [e, u]
    }, m.first = m.head = m.take = function(n, t, r) { return null == n ? void 0 : null == t || r ? n[0] : m.initial(n, n.length - t) }, m.initial = function(n, t, r) { return l.call(n, 0, Math.max(0, n.length - (null == t || r ? 1 : t))) }, m.last = function(n, t, r) { return null == n ? void 0 : null == t || r ? n[n.length - 1] : m.rest(n, Math.max(0, n.length - t)) }, m.rest = m.tail = m.drop = function(n, t, r) { return l.call(n, null == t || r ? 1 : t) }, m.compact = function(n) { return m.filter(n, m.identity) };
    var S = function(n, t, r, e) {
        for (var u = [], i = 0, o = e || 0, a = O(n); a > o; o++) {
            var c = n[o];
            if (k(c) && (m.isArray(c) || m.isArguments(c))) {
                t || (c = S(c, t, r));
                var f = 0,
                    l = c.length;
                for (u.length += l; l > f;) u[i++] = c[f++]
            } else r || (u[i++] = c)
        }
        return u
    };
    m.flatten = function(n, t) { return S(n, t, !1) }, m.without = function(n) { return m.difference(n, l.call(arguments, 1)) }, m.uniq = m.unique = function(n, t, r, e) {
        m.isBoolean(t) || (e = r, r = t, t = !1), null != r && (r = x(r, e));
        for (var u = [], i = [], o = 0, a = O(n); a > o; o++) {
            var c = n[o],
                f = r ? r(c, o, n) : c;
            t ? (o && i === f || u.push(c), i = f) : r ? m.contains(i, f) || (i.push(f), u.push(c)) : m.contains(u, c) || u.push(c)
        }
        return u
    }, m.union = function() { return m.uniq(S(arguments, !0, !0)) }, m.intersection = function(n) {
        for (var t = [], r = arguments.length, e = 0, u = O(n); u > e; e++) {
            var i = n[e];
            if (!m.contains(t, i)) {
                for (var o = 1; r > o && m.contains(arguments[o], i); o++);
                o === r && t.push(i)
            }
        }
        return t
    }, m.difference = function(n) { var t = S(arguments, !0, !0, 1); return m.filter(n, function(n) { return !m.contains(t, n) }) }, m.zip = function() { return m.unzip(arguments) }, m.unzip = function(n) { for (var t = n && m.max(n, O).length || 0, r = Array(t), e = 0; t > e; e++) r[e] = m.pluck(n, e); return r }, m.object = function(n, t) { for (var r = {}, e = 0, u = O(n); u > e; e++) t ? r[n[e]] = t[e] : r[n[e][0]] = n[e][1]; return r }, m.findIndex = t(1), m.findLastIndex = t(-1), m.sortedIndex = function(n, t, r, e) {
        r = x(r, e, 1);
        for (var u = r(t), i = 0, o = O(n); o > i;) {
            var a = Math.floor((i + o) / 2);
            r(n[a]) < u ? i = a + 1 : o = a
        }
        return i
    }, m.indexOf = r(1, m.findIndex, m.sortedIndex), m.lastIndexOf = r(-1, m.findLastIndex), m.range = function(n, t, r) { null == t && (t = n || 0, n = 0), r = r || 1; for (var e = Math.max(Math.ceil((t - n) / r), 0), u = Array(e), i = 0; e > i; i++, n += r) u[i] = n; return u };
    var E = function(n, t, r, e, u) {
        if (!(e instanceof t)) return n.apply(r, u);
        var i = j(n.prototype),
            o = n.apply(i, u);
        return m.isObject(o) ? o : i
    };
    m.bind = function(n, t) {
        if (g && n.bind === g) return g.apply(n, l.call(arguments, 1));
        if (!m.isFunction(n)) throw new TypeError("Bind must be called on a function");
        var r = l.call(arguments, 2),
            e = function() { return E(n, e, t, this, r.concat(l.call(arguments))) };
        return e
    }, m.partial = function(n) {
        var t = l.call(arguments, 1),
            r = function() { for (var e = 0, u = t.length, i = Array(u), o = 0; u > o; o++) i[o] = t[o] === m ? arguments[e++] : t[o]; for (; e < arguments.length;) i.push(arguments[e++]); return E(n, r, this, this, i) };
        return r
    }, m.bindAll = function(n) { var t, r, e = arguments.length; if (1 >= e) throw new Error("bindAll must be passed function names"); for (t = 1; e > t; t++) r = arguments[t], n[r] = m.bind(n[r], n); return n }, m.memoize = function(n, t) {
        var r = function(e) {
            var u = r.cache,
                i = "" + (t ? t.apply(this, arguments) : e);
            return m.has(u, i) || (u[i] = n.apply(this, arguments)), u[i]
        };
        return r.cache = {}, r
    }, m.delay = function(n, t) { var r = l.call(arguments, 2); return setTimeout(function() { return n.apply(null, r) }, t) }, m.defer = m.partial(m.delay, m, 1), m.throttle = function(n, t, r) {
        var e, u, i, o = null,
            a = 0;
        r || (r = {});
        var c = function() { a = r.leading === !1 ? 0 : m.now(), o = null, i = n.apply(e, u), o || (e = u = null) };
        return function() {
            var f = m.now();
            a || r.leading !== !1 || (a = f);
            var l = t - (f - a);
            return e = this, u = arguments, 0 >= l || l > t ? (o && (clearTimeout(o), o = null), a = f, i = n.apply(e, u), o || (e = u = null)) : o || r.trailing === !1 || (o = setTimeout(c, l)), i
        }
    }, m.debounce = function(n, t, r) {
        var e, u, i, o, a, c = function() {
            var f = m.now() - o;
            t > f && f >= 0 ? e = setTimeout(c, t - f) : (e = null, r || (a = n.apply(i, u), e || (i = u = null)))
        };
        return function() { i = this, u = arguments, o = m.now(); var f = r && !e; return e || (e = setTimeout(c, t)), f && (a = n.apply(i, u), i = u = null), a }
    }, m.wrap = function(n, t) { return m.partial(t, n) }, m.negate = function(n) { return function() { return !n.apply(this, arguments) } }, m.compose = function() {
        var n = arguments,
            t = n.length - 1;
        return function() { for (var r = t, e = n[t].apply(this, arguments); r--;) e = n[r].call(this, e); return e }
    }, m.after = function(n, t) { return function() { return --n < 1 ? t.apply(this, arguments) : void 0 } }, m.before = function(n, t) { var r; return function() { return --n > 0 && (r = t.apply(this, arguments)), 1 >= n && (t = null), r } }, m.once = m.partial(m.before, 2);
    var M = !{ toString: null }.propertyIsEnumerable("toString"),
        I = ["valueOf", "isPrototypeOf", "toString", "propertyIsEnumerable", "hasOwnProperty", "toLocaleString"];
    m.keys = function(n) { if (!m.isObject(n)) return []; if (v) return v(n); var t = []; for (var r in n) m.has(n, r) && t.push(r); return M && e(n, t), t }, m.allKeys = function(n) { if (!m.isObject(n)) return []; var t = []; for (var r in n) t.push(r); return M && e(n, t), t }, m.values = function(n) { for (var t = m.keys(n), r = t.length, e = Array(r), u = 0; r > u; u++) e[u] = n[t[u]]; return e }, m.mapObject = function(n, t, r) { t = x(t, r); for (var e, u = m.keys(n), i = u.length, o = {}, a = 0; i > a; a++) e = u[a], o[e] = t(n[e], e, n); return o }, m.pairs = function(n) { for (var t = m.keys(n), r = t.length, e = Array(r), u = 0; r > u; u++) e[u] = [t[u], n[t[u]]]; return e }, m.invert = function(n) { for (var t = {}, r = m.keys(n), e = 0, u = r.length; u > e; e++) t[n[r[e]]] = r[e]; return t }, m.functions = m.methods = function(n) { var t = []; for (var r in n) m.isFunction(n[r]) && t.push(r); return t.sort() }, m.extend = _(m.allKeys), m.extendOwn = m.assign = _(m.keys), m.findKey = function(n, t, r) {
        t = x(t, r);
        for (var e, u = m.keys(n), i = 0, o = u.length; o > i; i++)
            if (e = u[i], t(n[e], e, n)) return e
    }, m.pick = function(n, t, r) {
        var e, u, i = {},
            o = n;
        if (null == o) return i;
        m.isFunction(t) ? (u = m.allKeys(o), e = b(t, r)) : (u = S(arguments, !1, !1, 1), e = function(n, t, r) { return t in r }, o = Object(o));
        for (var a = 0, c = u.length; c > a; a++) {
            var f = u[a],
                l = o[f];
            e(l, f, o) && (i[f] = l)
        }
        return i
    }, m.omit = function(n, t, r) {
        if (m.isFunction(t)) t = m.negate(t);
        else {
            var e = m.map(S(arguments, !1, !1, 1), String);
            t = function(n, t) { return !m.contains(e, t) }
        }
        return m.pick(n, t, r)
    }, m.defaults = _(m.allKeys, !0), m.create = function(n, t) { var r = j(n); return t && m.extendOwn(r, t), r }, m.clone = function(n) { return m.isObject(n) ? m.isArray(n) ? n.slice() : m.extend({}, n) : n }, m.tap = function(n, t) { return t(n), n }, m.isMatch = function(n, t) {
        var r = m.keys(t),
            e = r.length;
        if (null == n) return !e;
        for (var u = Object(n), i = 0; e > i; i++) { var o = r[i]; if (t[o] !== u[o] || !(o in u)) return !1 }
        return !0
    };
    var N = function(n, t, r, e) {
        if (n === t) return 0 !== n || 1 / n === 1 / t;
        if (null == n || null == t) return n === t;
        n instanceof m && (n = n._wrapped), t instanceof m && (t = t._wrapped);
        var u = s.call(n);
        if (u !== s.call(t)) return !1;
        switch (u) {
            case "[object RegExp]":
            case "[object String]":
                return "" + n == "" + t;
            case "[object Number]":
                return +n !== +n ? +t !== +t : 0 === +n ? 1 / +n === 1 / t : +n === +t;
            case "[object Date]":
            case "[object Boolean]":
                return +n === +t
        }
        var i = "[object Array]" === u;
        if (!i) {
            if ("object" != typeof n || "object" != typeof t) return !1;
            var o = n.constructor,
                a = t.constructor;
            if (o !== a && !(m.isFunction(o) && o instanceof o && m.isFunction(a) && a instanceof a) && "constructor" in n && "constructor" in t) return !1
        }
        r = r || [], e = e || [];
        for (var c = r.length; c--;)
            if (r[c] === n) return e[c] === t;
        if (r.push(n), e.push(t), i) {
            if (c = n.length, c !== t.length) return !1;
            for (; c--;)
                if (!N(n[c], t[c], r, e)) return !1
        } else {
            var f, l = m.keys(n);
            if (c = l.length, m.keys(t).length !== c) return !1;
            for (; c--;)
                if (f = l[c], !m.has(t, f) || !N(n[f], t[f], r, e)) return !1
        }
        return r.pop(), e.pop(), !0
    };
    m.isEqual = function(n, t) { return N(n, t) }, m.isEmpty = function(n) { return null == n ? !0 : k(n) && (m.isArray(n) || m.isString(n) || m.isArguments(n)) ? 0 === n.length : 0 === m.keys(n).length }, m.isElement = function(n) { return !(!n || 1 !== n.nodeType) }, m.isArray = h || function(n) { return "[object Array]" === s.call(n) }, m.isObject = function(n) { var t = typeof n; return "function" === t || "object" === t && !!n }, m.each(["Arguments", "Function", "String", "Number", "Date", "RegExp", "Error"], function(n) { m["is" + n] = function(t) { return s.call(t) === "[object " + n + "]" } }), m.isArguments(arguments) || (m.isArguments = function(n) { return m.has(n, "callee") }), "function" != typeof /./ && "object" != typeof Int8Array && (m.isFunction = function(n) { return "function" == typeof n || !1 }), m.isFinite = function(n) { return isFinite(n) && !isNaN(parseFloat(n)) }, m.isNaN = function(n) { return m.isNumber(n) && n !== +n }, m.isBoolean = function(n) { return n === !0 || n === !1 || "[object Boolean]" === s.call(n) }, m.isNull = function(n) { return null === n }, m.isUndefined = function(n) { return n === void 0 }, m.has = function(n, t) { return null != n && p.call(n, t) }, m.noConflict = function() { return u._ = i, this }, m.identity = function(n) { return n }, m.constant = function(n) { return function() { return n } }, m.noop = function() {}, m.property = w, m.propertyOf = function(n) { return null == n ? function() {} : function(t) { return n[t] } }, m.matcher = m.matches = function(n) {
        return n = m.extendOwn({}, n),
            function(t) { return m.isMatch(t, n) }
    }, m.times = function(n, t, r) {
        var e = Array(Math.max(0, n));
        t = b(t, r, 1);
        for (var u = 0; n > u; u++) e[u] = t(u);
        return e
    }, m.random = function(n, t) { return null == t && (t = n, n = 0), n + Math.floor(Math.random() * (t - n + 1)) }, m.now = Date.now || function() { return (new Date).getTime() };
    var B = { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#x27;", "`": "&#x60;" },
        T = m.invert(B),
        R = function(n) {
            var t = function(t) { return n[t] },
                r = "(?:" + m.keys(n).join("|") + ")",
                e = RegExp(r),
                u = RegExp(r, "g");
            return function(n) { return n = null == n ? "" : "" + n, e.test(n) ? n.replace(u, t) : n }
        };
    m.escape = R(B), m.unescape = R(T), m.result = function(n, t, r) { var e = null == n ? void 0 : n[t]; return e === void 0 && (e = r), m.isFunction(e) ? e.call(n) : e };
    var q = 0;
    m.uniqueId = function(n) { var t = ++q + ""; return n ? n + t : t }, m.templateSettings = { evaluate: /<%([\s\S]+?)%>/g, interpolate: /<%=([\s\S]+?)%>/g, escape: /<%-([\s\S]+?)%>/g };
    var K = /(.)^/,
        z = { "'": "'", "\\": "\\", "\r": "r", "\n": "n", "\u2028": "u2028", "\u2029": "u2029" },
        D = /\\|'|\r|\n|\u2028|\u2029/g,
        L = function(n) { return "\\" + z[n] };
    m.template = function(n, t, r) {
        !t && r && (t = r), t = m.defaults({}, t, m.templateSettings);
        var e = RegExp([(t.escape || K).source, (t.interpolate || K).source, (t.evaluate || K).source].join("|") + "|$", "g"),
            u = 0,
            i = "__p+='";
        n.replace(e, function(t, r, e, o, a) { return i += n.slice(u, a).replace(D, L), u = a + t.length, r ? i += "'+\n((__t=(" + r + "))==null?'':_.escape(__t))+\n'" : e ? i += "'+\n((__t=(" + e + "))==null?'':__t)+\n'" : o && (i += "';\n" + o + "\n__p+='"), t }), i += "';\n", t.variable || (i = "with(obj||{}){\n" + i + "}\n"), i = "var __t,__p='',__j=Array.prototype.join," + "print=function(){__p+=__j.call(arguments,'');};\n" + i + "return __p;\n";
        try { var o = new Function(t.variable || "obj", "_", i) } catch (a) { throw a.source = i, a }
        var c = function(n) { return o.call(this, n, m) },
            f = t.variable || "obj";
        return c.source = "function(" + f + "){\n" + i + "}", c
    }, m.chain = function(n) { var t = m(n); return t._chain = !0, t };
    var P = function(n, t) { return n._chain ? m(t).chain() : t };
    m.mixin = function(n) {
        m.each(m.functions(n), function(t) {
            var r = m[t] = n[t];
            m.prototype[t] = function() { var n = [this._wrapped]; return f.apply(n, arguments), P(this, r.apply(m, n)) }
        })
    }, m.mixin(m), m.each(["pop", "push", "reverse", "shift", "sort", "splice", "unshift"], function(n) {
        var t = o[n];
        m.prototype[n] = function() { var r = this._wrapped; return t.apply(r, arguments), "shift" !== n && "splice" !== n || 0 !== r.length || delete r[0], P(this, r) }
    }), m.each(["concat", "join", "slice"], function(n) {
        var t = o[n];
        m.prototype[n] = function() { return P(this, t.apply(this._wrapped, arguments)) }
    }), m.prototype.value = function() { return this._wrapped }, m.prototype.valueOf = m.prototype.toJSON = m.prototype.value, m.prototype.toString = function() { return "" + this._wrapped }, "function" == typeof define && define.amd && define("underscore", [], function() { return m })
}).call(this);