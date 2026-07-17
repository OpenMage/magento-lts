/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var varienGrid = function(containerId, url, pageVar, sortVar, dirVar, filterVar) {
    this.initialize(containerId, url, pageVar, sortVar, dirVar, filterVar);
};

varienGrid.prototype = {
    initialize : function(containerId, url, pageVar, sortVar, dirVar, filterVar){
        this.containerId = containerId;
        this.url = url;
        this.pageVar = pageVar || false;
        this.sortVar = sortVar || false;
        this.dirVar  = dirVar || false;
        this.filterVar  = filterVar || false;
        this.tableSufix = '_table';
        this.useAjax = false;
        this.rowClickCallback = false;
        this.checkboxCheckCallback = false;
        this.preInitCallback = false;
        this.initCallback = false;
        this.initRowCallback = false;
        this.doFilterCallback = false;

        this.reloadParams = false;

        this.trOnMouseOver  = this.rowMouseOver.bind(this);
        this.trOnMouseOut   = this.rowMouseOut.bind(this);
        this.trOnClick      = this.rowMouseClick.bind(this);
        this.trOnDblClick   = this.rowMouseDblClick.bind(this);
        this.trOnKeyPress   = this.keyPress.bind(this);

        this.thLinkOnClick      = this.doSort.bind(this);
        this.initGrid();
    },
    initGrid : function(){
        if(this.preInitCallback){
            this.preInitCallback(this);
        }
        if(document.getElementById(this.containerId+this.tableSufix)){
            this.rows = Array.from(document.querySelectorAll('#'+this.containerId+this.tableSufix+' tbody tr'));
            for (var row=0; row<this.rows.length; row++) {
                if(row%2==0){
                    this.rows[row].classList.add('even');
                }else{
                    this.rows[row].classList.add('odd');
                }

                this.rows[row].addEventListener('mouseover',this.trOnMouseOver);
                this.rows[row].addEventListener('mouseout',this.trOnMouseOut);
                this.rows[row].addEventListener('mousedown',this.trOnClick);
                this.rows[row].addEventListener('click',this.trOnClick);
                this.rows[row].addEventListener('dblclick',this.trOnDblClick);
            }
        }
        if(this.sortVar && this.dirVar){
            var columns = Array.from(document.querySelectorAll('#'+this.containerId+this.tableSufix+' thead a'));

            for(var col=0; col<columns.length; col++){
                columns[col].addEventListener('click',this.thLinkOnClick);
            }
        }
        this.bindFilterFields();
        this.bindFieldsChange();
        if(this.initCallback){
            try {
                this.initCallback(this);
            }
            catch (e) {
                if(console) {
                    console.log(e);
                }
            }
        }
    },
    initGridAjax: function () {
        this.initGrid();
        this.initGridRows();
    },
    initGridRows: function() {
        if(this.initRowCallback){
            for (var row=0; row<this.rows.length; row++) {
                try {
                    this.initRowCallback(this, this.rows[row]);
                } catch (e) {
                    if(console) {
                        console.log(e);
                    }
                }
            }
        }
    },
    getContainerId : function(){
        return this.containerId;
    },
    rowMouseOver : function(event){
        var element = event.target.closest('tr');

        if (!element || !element.title) return;

        element.classList.add('on-mouse');

        if (!element.classList.contains('pointer')
            && (this.rowClickCallback !== openGridRow || element.title)) {
            if (element.title) {
                element.classList.add('pointer');
            }
        }
    },
    rowMouseOut : function(event){
        var element = event.target.closest('tr');
        if (element) {
            element.classList.remove('on-mouse');
        }
    },
    rowMouseClick : function(event){
        if (event.button != 1 && event.type == "mousedown") {
            return; // Ignore mousedown for any button except middle
        }
        if (event.button == 2) {
            return; // Ignore right click
        }
        if(this.rowClickCallback){
            try{
                this.rowClickCallback(this, event);
            }
            catch(e){}
        }
        varienGlobalEvents.fireEvent('gridRowClick', event);
    },
    rowMouseDblClick : function(event){
        varienGlobalEvents.fireEvent('gridRowDblClick', event);
    },
    keyPress : function(event){

    },
    doSort : function(event){
        var element = event.target.closest('a');

        if(element && element.name && element.title){
            this.addVarToUrl(this.sortVar, element.name);
            this.addVarToUrl(this.dirVar, element.title);
            this.reload(this.url);
        }
        event.preventDefault();
        event.stopPropagation();
        return false;
    },
    loadByElement : function(element){
        if(element && element.name){
            this.reload(this.addVarToUrl(element.name, element.value));
        }
    },
    reload : function(url){
        if (!this.reloadParams) {
            this.reloadParams = {form_key: FORM_KEY};
        }
        else {
            this.reloadParams.form_key = FORM_KEY;
        }
        url = url || this.url;
        if(this.useAjax){
            var ajaxUrl = url + (url.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true');
            var params = this.reloadParams || {};
            var containerId = this.containerId;
            var self = this;

            if (typeof varienLoaderHandler !== 'undefined' && typeof varienLoaderHandler.handler !== 'undefined') {
                varienLoaderHandler.handler.onCreate({options: {loaderArea: containerId}});
            }

            var body = new URLSearchParams(params).toString();
            fetch(ajaxUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body: body
            }).then(function(response) {
                return response.text();
            }).then(function(responseText) {
                try {
                    var isJSON = false;
                    try {
                        var response = JSON.parse(responseText);
                        isJSON = true;
                    } catch(jsonErr) {}

                    if (isJSON) {
                        if (response.error) {
                            alert(response.message);
                        }
                        if(response.ajaxExpired && response.ajaxRedirect) {
                            setLocation(response.ajaxRedirect);
                        }
                    } else {
                        var cleanText = responseText.replace(/>\s+</g, '><');
                        document.getElementById(containerId).innerHTML = cleanText;
                    }
                } catch (e) {
                    document.getElementById(containerId).innerHTML = responseText.replace(/>\s+</g, '><');
                }

                // evalScripts: extract and execute script tags
                var container = document.getElementById(containerId);
                if (container) {
                    var scripts = container.querySelectorAll('script');
                    for (var s = 0; s < scripts.length; s++) {
                        var scriptEl = document.createElement('script');
                        if (scripts[s].src) {
                            scriptEl.src = scripts[s].src;
                        } else {
                            scriptEl.text = scripts[s].text;
                        }
                        document.head.appendChild(scriptEl).parentNode.removeChild(scriptEl);
                    }
                }

                self.initGridAjax();
            }).catch(function() {
                self._processFailure();
            }).finally(function() {
                if (typeof varienLoaderHandler !== 'undefined' && typeof varienLoaderHandler.handler !== 'undefined') {
                    varienLoaderHandler.handler.onComplete();
                }
            });
            return;
        }
        else{
            if(this.reloadParams){
                var keys = Object.keys(this.reloadParams);
                for (var i = 0; i < keys.length; i++) {
                    url = this.addVarToUrl(keys[i], this.reloadParams[keys[i]]);
                }
            }
            setLocation(url);
        }
    },
    /*_processComplete : function(transport){
        console.log(transport);
        if (transport && transport.responseText){
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
        }
        if (response.ajaxExpired && response.ajaxRedirect) {
            location.href = response.ajaxRedirect;
            return false;
        }
        this.initGrid();
    },*/
    _processFailure : function(){
        location.href = BASE_URL;
    },
    _addVarToUrl : function(url, varName, varValue){
        var re = new RegExp('\/('+varName+'\/.*?\/)');
        var parts = url.split(new RegExp('\\?'));
        url = parts[0].replace(re, '/');
        if (varValue !== null && varValue !== undefined && varValue !== '') {
            url+= varName+'/'+varValue+'/';
        }
        if(parts.length>1) {
            url+= '?' + parts[1];
        }
        return url;
    },
    addVarToUrl : function(varName, varValue){
        this.url = this._addVarToUrl(this.url, varName, varValue);
        return this.url;
    },
    doExport : function(){
        var exportEl = document.getElementById(this.containerId+'_export');
        if(exportEl){
            var exportUrl = exportEl.value;
            if(this.massaction && this.massaction.checkedString) {
                exportUrl = this._addVarToUrl(exportUrl, this.massaction.formFieldNameInternal, this.massaction.checkedString);
            }
            setLocation(exportUrl);
        }
    },
    bindFilterFields : function(){
        var filters = Array.from(document.querySelectorAll('#'+this.containerId+' .filter input, #'+this.containerId+' .filter select'));
        for (var i=0; i<filters.length; i++) {
            filters[i].addEventListener('keypress',this.filterKeyPress.bind(this));
        }
    },
    bindFieldsChange : function(){
        if (!document.getElementById(this.containerId)) {
            return;
        }
        var tableEl = document.getElementById(this.containerId+this.tableSufix);
        var tbody = tableEl ? tableEl.querySelector('tbody') : null;
        if (!tbody) return;
        var dataElements = Array.from(tbody.querySelectorAll('input, select'));
        for(var i=0; i<dataElements.length;i++){
            dataElements[i].addEventListener('change', dataElements[i].setHasChanges.bind(dataElements[i]));
        }
    },
    filterKeyPress : function(event){
        if(event.keyCode==13){
            this.doFilter();
        }
    },
    doFilter : function(){
        var filters = Array.from(document.querySelectorAll('#'+this.containerId+' .filter input, #'+this.containerId+' .filter select'));
        var elements = [];
        for(var i=0; i<filters.length; i++){
            if(filters[i].value && filters[i].value.length) elements.push(filters[i]);
        }
        if (!this.doFilterCallback || (this.doFilterCallback && this.doFilterCallback())) {
            this.addVarToUrl(this.pageVar, 1);
            var pairs = [];
            for (var j = 0; j < elements.length; j++) {
                if (elements[j].name) {
                    pairs.push(encodeURIComponent(elements[j].name) + '=' + encodeURIComponent(elements[j].value));
                }
            }
            this.reload(this.addVarToUrl(this.filterVar, encode_base64(pairs.join('&'))));
        }
    },
    resetFilter : function(){
        this.addVarToUrl(this.pageVar, 1);
        this.reload(this.addVarToUrl(this.filterVar, ''));
    },
    checkCheckboxes : function(element){
        var container = document.getElementById(this.containerId);
        var elements = Array.from(container.querySelectorAll('input[name="'+element.name+'"]'));
        for(var i=0; i<elements.length;i++){
            this.setCheckboxChecked(elements[i], element.checked);
        }
    },
    setCheckboxChecked : function(element, checked){
        element.checked = checked;
        element.setHasChanges({});
        if(this.checkboxCheckCallback){
            this.checkboxCheckCallback(this,element,checked);
        }
    },
    inputPage : function(event, maxNum){
        var element = event.target;
        var keyCode = event.keyCode || event.which;
        if(keyCode==13){
            this.setPage(element.value);
        }
        /*if(keyCode>47 && keyCode<58){

        }
        else{
             event.preventDefault();
             event.stopPropagation();
        }*/
    },
    setPage : function(pageNumber){
        this.reload(this.addVarToUrl(this.pageVar, pageNumber));
    }
};

function shouldOpenGridRowNewTab(evt){
    return evt.ctrlKey // Windows ctrl + click
        || evt.metaKey // macOS command + click
        || evt.button == 1 // Middle mouse click
}

function openGridRow(grid, evt){
    var trElement = evt.target.closest('tr');
    if(['a', 'input', 'select', 'option'].indexOf(evt.target.tagName.toLowerCase())!=-1) {
        return;
    }
    if(trElement && trElement.title){
        if (shouldOpenGridRowNewTab(evt)) {
            window.open(trElement.title, '_blank');
        } else {
            setLocation(trElement.title);
        }
    }
}

var varienGridMassaction = function(containerId, grid, checkedValues, formFieldNameInternal, formFieldName) {
    this.initialize(containerId, grid, checkedValues, formFieldNameInternal, formFieldName);
};
varienGridMassaction.prototype = {
    /* Predefined vars */
    checkedValues: {},
    checkedString: '',
    oldCallbacks: {},
    errorText:'',
    items: {},
    gridIds: [],
    useSelectAll: false,
    currentItem: false,
    lastChecked: { left: false, top: false, checkbox: false },
    fieldTemplate: '<input type="hidden" name="#{name}" value="#{value}" />',
    initialize: function (containerId, grid, checkedValues, formFieldNameInternal, formFieldName) {
        this.setOldCallback('row_click', grid.rowClickCallback);
        this.setOldCallback('init',      grid.initCallback);
        this.setOldCallback('init_row',  grid.initRowCallback);
        this.setOldCallback('pre_init',  grid.preInitCallback);

        this.useAjax        = false;
        this.grid           = grid;
        this.grid.massaction = this;
        this.containerId    = containerId;
        this.initMassactionElements();

        this.checkedString          = checkedValues;
        this.formFieldName          = formFieldName;
        this.formFieldNameInternal  = formFieldNameInternal;

        this.grid.initCallback      = this.onGridInit.bind(this);
        this.grid.preInitCallback   = this.onGridPreInit.bind(this);
        this.grid.initRowCallback   = this.onGridRowInit.bind(this);
        this.grid.rowClickCallback  = this.onGridRowClick.bind(this);
        this.initCheckboxes();
        this.checkCheckboxes();
    },
    _evaluateTemplate: function(template, data) {
        return template.replace(/#\{(\w+)\}/g, function(match, key) {
            return data[key] !== undefined ? data[key] : '';
        });
    },
    setUseAjax: function(flag) {
        this.useAjax = flag;
    },
    setUseSelectAll: function(flag) {
        this.useSelectAll = flag;
    },
    initMassactionElements: function() {
        this.container      = document.getElementById(this.containerId);
        this.count          = document.getElementById(this.containerId + '-count');
        this.formHiddens    = document.getElementById(this.containerId + '-form-hiddens');
        this.formAdditional = document.getElementById(this.containerId + '-form-additional');
        this.select         = document.getElementById(this.containerId + '-select');
        this.form           = this.prepareForm();
        this.validator      = new Validation(this.form);
        this.select.addEventListener('change', this.onSelectChange.bind(this));
        this.lastChecked    = { left: false, top: false, checkbox: false };
        this.initMassSelect();
    },
    prepareForm: function() {
        var form = document.getElementById(this.containerId + '-form'), formPlace = null,
            formElement = this.formHiddens || this.formAdditional;

        if (!formElement) {
            formElement = this.container.getElementsByTagName('button')[0];
            formElement && formElement.parentNode;
        }
        if (!form && formElement) {
            /* fix problem with rendering form in FF through innerHTML property */
            form = document.createElement('form');
            form.setAttribute('method', 'post');
            form.setAttribute('action', '');
            form.id = this.containerId + '-form';
            formPlace = formElement.parentNode.parentNode;
            formPlace.parentNode.appendChild(form);
            form.appendChild(formPlace);
        }

        return form;
    },
    setGridIds: function(gridIds) {
        this.gridIds = gridIds;
        this.updateCount();
    },
    getGridIds: function() {
        return this.gridIds;
    },
    setItems: function(items) {
        this.items = items;
        this.updateCount();
    },
    getItems: function() {
        return this.items;
    },
    getItem: function(itemId) {
        if(this.items[itemId]) {
            return this.items[itemId];
        }
        return false;
    },
    getOldCallback: function (callbackName) {
        return this.oldCallbacks[callbackName] ? this.oldCallbacks[callbackName] : function(){};
    },
    setOldCallback: function (callbackName, callback) {
        this.oldCallbacks[callbackName] = callback;
    },
    onGridPreInit: function(grid) {
        this.initMassactionElements();
        this.getOldCallback('pre_init')(grid);
    },
    onGridInit: function(grid) {
        this.initCheckboxes();
        this.checkCheckboxes();
        this.updateCount();
        this.getOldCallback('init')(grid);
    },
    onGridRowInit: function(grid, row) {
        this.getOldCallback('init_row')(grid, row);
    },
    onGridRowClick: function(grid, evt) {

        var tdElement = evt.target.closest('td');
        var trElement = evt.target.closest('tr');

        if(!tdElement.querySelector('input')) {
            if(tdElement.querySelector('a') || tdElement.querySelector('select')) {
                return;
            }
            if (trElement.title) {
                if (shouldOpenGridRowNewTab(evt)) {
                    window.open(trElement.title, '_blank');
                } else {
                    setLocation(trElement.title);
                }
            }
            else{
                var checkbox = Array.from(trElement.querySelectorAll('input'));
                var isInput  = evt.target.tagName == 'input';
                var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;

                if(checked) {
                    this.checkedString = varienStringArray.add(checkbox[0].value, this.checkedString);
                } else {
                    this.checkedString = varienStringArray.remove(checkbox[0].value, this.checkedString);
                }
                this.grid.setCheckboxChecked(checkbox[0], checked);
                this.updateCount();
            }
            return;
        }

        if(evt.target.isMassactionCheckbox) {
           this.setCheckbox(evt.target);
        } else {
           var checkbox = this.findCheckbox(evt);
           if (checkbox) {
               checkbox.checked = !checkbox.checked;
               this.setCheckbox(checkbox);
           }
        }
    },
    onSelectChange: function(evt) {
        var item = this.getSelectedItem();
        if(item) {
            var block = document.getElementById(this.containerId + '-item-' + item.id + '-block');
            this.formAdditional.innerHTML = block ? block.innerHTML : '';
        } else {
            this.formAdditional.innerHTML = '';
        }

        this.validator.reset();
    },
    findCheckbox: function(evt) {
        if(['a', 'input', 'select'].indexOf(evt.target.tagName.toLowerCase())!==-1) {
            return false;
        }
        var checkbox = false;
        var tr = evt.target.closest('tr');
        var elements = Array.from(tr.querySelectorAll('.massaction-checkbox'));
        elements.forEach(function(element){
            if(element.isMassactionCheckbox) {
                checkbox = element;
            }
        });
        return checkbox;
    },
    initCheckboxes: function() {
        this.getCheckboxes().forEach(function(checkbox) {
           checkbox.isMassactionCheckbox = true;
        });
    },
    checkCheckboxes: function() {
        var self = this;
        this.getCheckboxes().forEach(function(checkbox) {
            checkbox.checked = varienStringArray.has(checkbox.value, self.checkedString);
        });
    },
    selectAll: function() {
        this.setCheckedValues((this.useSelectAll ? this.getGridIds() : this.getCheckboxesValuesAsString()));
        this.checkCheckboxes();
        this.updateCount();
        this.clearLastChecked();
        return false;
    },
    unselectAll: function() {
        this.setCheckedValues('');
        this.checkCheckboxes();
        this.updateCount();
        this.clearLastChecked();
        return false;
    },
    selectVisible: function() {
        this.setCheckedValues(this.getCheckboxesValuesAsString());
        this.checkCheckboxes();
        this.updateCount();
        this.clearLastChecked();
        return false;
    },
    unselectVisible: function() {
        var self = this;
        this.getCheckboxesValues().forEach(function(key){
            self.checkedString = varienStringArray.remove(key, self.checkedString);
        });
        this.checkCheckboxes();
        this.updateCount();
        this.clearLastChecked();
        return false;
    },
    setCheckedValues: function(values) {
        this.checkedString = values;
    },
    getCheckedValues: function() {
        return this.checkedString;
    },
    getCheckboxes: function() {
        var result = [];
        if (this.grid.rows) {
            this.grid.rows.forEach(function(row){
                var checkboxes = Array.from(row.querySelectorAll('.massaction-checkbox'));
                checkboxes.forEach(function(checkbox){
                    result.push(checkbox);
                });
            });
        }
        return result;
    },
    getCheckboxesValues: function() {
        var result = [];
        this.getCheckboxes().forEach(function(checkbox) {
            result.push(checkbox.value);
        });
        return result;
    },
    getCheckboxesValuesAsString: function()
    {
        return this.getCheckboxesValues().join(',');
    },
    setCheckbox: function(checkbox) {
        if(checkbox.checked) {
            this.checkedString = varienStringArray.add(checkbox.value, this.checkedString);
        } else {
            this.checkedString = varienStringArray.remove(checkbox.value, this.checkedString);
        }
        this.updateCount();
    },
    updateCount: function() {
        this.count.innerHTML = varienStringArray.count(this.checkedString);
        if(!this.grid.reloadParams) {
            this.grid.reloadParams = {};
        }
        this.grid.reloadParams[this.formFieldNameInternal] = this.checkedString;
    },
    getSelectedItem: function() {
        if(this.getItem(this.select.value)) {
            return this.getItem(this.select.value);
        } else {
            return false;
        }
    },
    apply: function() {
        if(varienStringArray.count(this.checkedString) == 0) {
                alert(this.errorText);
                return;
            }

        var item = this.getSelectedItem();
        if(!item) {
            this.validator.validate();
            return;
        }
        this.currentItem = item;
        var fieldName = (item.field ? item.field : this.formFieldName);

        if(this.currentItem.confirm && !window.confirm(this.currentItem.confirm)) {
            return;
        }

        this.formHiddens.innerHTML = '';
        this.formHiddens.insertAdjacentHTML('beforeend', this._evaluateTemplate(this.fieldTemplate, {name: fieldName, value: this.checkedString}));
        this.formHiddens.insertAdjacentHTML('beforeend', this._evaluateTemplate(this.fieldTemplate, {name: 'massaction_prepare_key', value: fieldName}));

        if(!this.validator.validate()) {
            return;
        }

        if(this.useAjax && item.url) {
            var self = this;
            var formData = new URLSearchParams(new FormData(this.form)).toString();
            fetch(item.url, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body: formData
            }).then(function(response) {
                return response.text();
            }).then(function(responseText) {
                self.onMassactionComplete(responseText);
            });
        } else if(item.url) {
            this.form.action = item.url;
            this.form.submit();
        }
    },
    onMassactionComplete: function(responseText) {
        if(this.currentItem.complete) {
            try {
                var listener = this.getListener(this.currentItem.complete) || function(){};
                listener(this.grid, this, responseText);
            } catch (e) {}
       }
    },
    getListener: function(strValue) {
        return eval(strValue);
    },
    initMassSelect: function() {
        var self = this;
        Array.from(document.querySelectorAll('input[class~="massaction-checkbox"]')).forEach(
            function(element) {
                element.addEventListener('click', self.massSelect.bind(self));
            }
        );
    },
    clearLastChecked: function() {
        this.lastChecked = {
            left: false,
            top: false,
            checkbox: false
        };
    },
    massSelect: function(evt) {
        if(this.lastChecked.left !== false
            && this.lastChecked.top !== false
            && evt.button === 0
            && evt.shiftKey === true
        ) {
            var currentCheckbox = evt.target;
            var lastCheckbox = this.lastChecked.checkbox;
            if (lastCheckbox != currentCheckbox) {
                var start = this.getCheckboxOrder(lastCheckbox);
                var finish = this.getCheckboxOrder(currentCheckbox);
                if (start !== false && finish !== false) {
                    this.selectCheckboxRange(
                        Math.min(start, finish),
                        Math.max(start, finish),
                        currentCheckbox.checked
                    );
                }
            }
        }

        var rect = evt.target.getBoundingClientRect();
        this.lastChecked = {
            left: rect.left,
            top: rect.top,
            checkbox: evt.target // "boundary" checkbox
        };
    },
    getCheckboxOrder: function(curCheckbox) {
        var order = false;
        this.getCheckboxes().forEach(function(checkbox, key){
            if (curCheckbox == checkbox) {
                order = key;
            }
        });
        return order;
    },
    selectCheckboxRange: function(start, finish, isChecked){
        var self = this;
        this.getCheckboxes().forEach(function(checkbox, key){
            if (key >= start && key <= finish) {
                checkbox.checked = isChecked;
                self.setCheckbox(checkbox);
            }
        });
    }
};

var varienGridAction = {
    execute: function(select) {
        var isJSON = false;
        if (select.value) {
            try {
                JSON.parse(select.value);
                isJSON = true;
            } catch(e) {}
        }
        if(!select.value || !isJSON) {
            return;
        }

        var config = JSON.parse(select.value);
        if(config.confirm && !window.confirm(config.confirm)) {
            select.options[0].selected = true;
            return;
        }

        if(config.popup) {
            var win = window.open(config.href, 'action_window', 'width=500,height=600,resizable=1,scrollbars=1');
            win.focus();
            select.options[0].selected = true;
        } else {
            setLocation(config.href);
        }
    }
};

var varienStringArray = {
    remove: function(str, haystack)
    {
        haystack = ',' + haystack + ',';
        haystack = haystack.replace(new RegExp(',' + str + ',', 'g'), ',');
        return this.trimComma(haystack);
    },
    add: function(str, haystack)
    {
        haystack = ',' + haystack + ',';
        if (haystack.search(new RegExp(',' + str + ',', 'g'), haystack) === -1) {
            haystack += str + ',';
        }
        return this.trimComma(haystack);
    },
    has: function(str, haystack)
    {
        haystack = ',' + haystack + ',';
        if (haystack.search(new RegExp(',' + str + ',', 'g'), haystack) === -1) {
            return false;
        }
        return true;
    },
    count: function(haystack)
    {
        if (typeof haystack != 'string') {
            return 0;
        }
        var match;
        if (match = haystack.match(new RegExp(',', 'g'))) {
            return match.length + 1;
        } else if (haystack.length != 0) {
            return 1;
        }
        return 0;
    },
    each: function(haystack, fnc)
    {
        var arr = haystack.split(',');
        for (var i=0; i<arr.length; i++) {
            fnc(arr[i]);
        }
    },
    trimComma: function(string)
    {
        string = string.replace(new RegExp('^(,+)','i'), '');
        string = string.replace(new RegExp('(,+)$','i'), '');
        return string;
    }
};

var serializerController = function(hiddenDataHolder, predefinedData, inputsToManage, grid, reloadParamName) {
    this.initialize(hiddenDataHolder, predefinedData, inputsToManage, grid, reloadParamName);
};
serializerController.prototype = {
    oldCallbacks: {},
    initialize: function(hiddenDataHolder, predefinedData, inputsToManage, grid, reloadParamName){
        //Grid inputs
        this.tabIndex = 1000;
        this.inputsToManage       = inputsToManage;
        this.multidimensionalMode = inputsToManage.length > 0;

        //Plain object as grid data store
        this.gridData             = this.getGridDataObject(predefinedData);

        //Hidden input data holder
        this.hiddenDataHolder     = document.getElementById(hiddenDataHolder);
        this.hiddenDataHolder.value = this.serializeObject();

        this.grid = grid;

        // Set old callbacks
        this.setOldCallback('row_click', this.grid.rowClickCallback);
        this.setOldCallback('init_row', this.grid.initRowCallback);
        this.setOldCallback('checkbox_check', this.grid.checkboxCheckCallback);

        //Grid
        this.reloadParamName = reloadParamName;
        this.grid.reloadParams = {};
        this.grid.reloadParams[this.reloadParamName+'[]'] = this.getDataForReloadParam();
        this.grid.rowClickCallback = this.rowClick.bind(this);
        this.grid.initRowCallback = this.rowInit.bind(this);
        this.grid.checkboxCheckCallback = this.registerData.bind(this);
        this.grid.rows.forEach(this.eachRow.bind(this));
    },
    setOldCallback: function (callbackName, callback) {
        this.oldCallbacks[callbackName] = callback;
    },
    getOldCallback: function (callbackName) {
        return this.oldCallbacks[callbackName] ? this.oldCallbacks[callbackName] : function(){};
    },
    registerData : function(grid, element, checked) {
        if(this.multidimensionalMode){
            if(checked){
                 if(element.inputElements) {
                     this.gridData[element.value] = {};
                     for(var i = 0; i < element.inputElements.length; i++) {
                         element.inputElements[i].disabled = false;
                         this.gridData[element.value][element.inputElements[i].name] = element.inputElements[i].value;
                     }
                 }
            }
            else{
                if(element.inputElements){
                    for(var i = 0; i < element.inputElements.length; i++) {
                        element.inputElements[i].disabled = true;
                    }
                }
                delete this.gridData[element.value];
            }
        }
        else{
            if(checked){
                this.gridData[element.value] = element.value;
            }
            else{
                delete this.gridData[element.value];
            }
        }

        this.hiddenDataHolder.value = this.serializeObject();
        this.grid.reloadParams = {};
        this.grid.reloadParams[this.reloadParamName+'[]'] = this.getDataForReloadParam();
        this.getOldCallback('checkbox_check')(grid, element, checked);
    },
    eachRow : function(row) {
        this.rowInit(this.grid, row);
    },
    rowClick : function(grid, event) {
        var tdElement = event.target.closest('td');
        var isInput   = event.target.tagName == 'INPUT';
        if(tdElement){
            var checkbox = Array.from(tdElement.querySelectorAll('input'));
            if(checkbox[0] && !checkbox[0].disabled){
                var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                this.grid.setCheckboxChecked(checkbox[0], checked);
            }
        }
        this.getOldCallback('row_click')(grid, event);
    },
    inputChange : function(event) {
        var element = event.target;
        if(element && element.checkboxElement && element.checkboxElement.checked){
            if (this.gridData[element.checkboxElement.value]) {
                this.gridData[element.checkboxElement.value][element.name] = element.value;
            }
            this.hiddenDataHolder.value = this.serializeObject();
        }
    },
    rowInit : function(grid, row) {
        if(this.multidimensionalMode){
            var checkbox = row.querySelector('.checkbox');
            var selectors = [];
            for (var n = 0; n < this.inputsToManage.length; n++) {
                selectors.push('input[name="' + this.inputsToManage[n] + '"]');
                selectors.push('select[name="' + this.inputsToManage[n] + '"]');
            }
            var inputs = Array.from(row.querySelectorAll(selectors.join(', ')));
            if(checkbox && inputs.length > 0) {
                checkbox.inputElements = inputs;
                for(var i = 0; i < inputs.length; i++) {
                    inputs[i].checkboxElement = checkbox;
                    if(this.gridData[checkbox.value] && this.gridData[checkbox.value][inputs[i].name]) {
                        inputs[i].value = this.gridData[checkbox.value][inputs[i].name];
                    }
                    inputs[i].disabled = !checkbox.checked;
                    inputs[i].tabIndex = this.tabIndex++;
                    inputs[i].addEventListener('keyup', this.inputChange.bind(this));
                    inputs[i].addEventListener('change', this.inputChange.bind(this));
                }
            }
        }
        this.getOldCallback('init_row')(grid, row);
    },

    //Stuff methods
    getGridDataObject: function (_object){
        if (this.multidimensionalMode) {
            // Clone the object
            var result = {};
            var keys = Object.keys(_object);
            for (var i = 0; i < keys.length; i++) {
                result[keys[i]] = _object[keys[i]];
            }
            return result;
        } else {
            return this.convertArrayToObject(_object);
        }
    },
    getDataForReloadParam: function(){
        return this.multidimensionalMode ? Object.keys(this.gridData) : this._objectValues(this.gridData);
    },
    _objectValues: function(obj) {
        var values = [];
        var keys = Object.keys(obj);
        for (var i = 0; i < keys.length; i++) {
            values.push(obj[keys[i]]);
        }
        return values;
    },
    serializeObject: function(){
        if(this.multidimensionalMode){
            var clone = {};
            var keys = Object.keys(this.gridData);
            for (var i = 0; i < keys.length; i++) {
                clone[keys[i]] = encode_base64(new URLSearchParams(this.gridData[keys[i]]).toString());
            }
            return new URLSearchParams(clone).toString();
        }
        else{
            return this._objectValues(this.gridData).join('&');
        }
    },
    convertArrayToObject: function (_array){
        var _object = {};
        for(var i = 0, l = _array.length; i < l; i++){
            _object[_array[i]] = _array[i];
        }
        return _object;
    }
};
