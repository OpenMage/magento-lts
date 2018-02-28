/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var telephoneElem = Class.create();

telephoneElem.prototype = {
    initialize : function(fval,f1,f2,f3,f4){
        this.valField = fval;
        this.f1 = f1;
        this.f2 = f2;
        this.f3 = f3;
        this.f4 = f4;
        this.last = f3;

        this.eventKeyPress = this.keyPress.bindAsEventListener(this);
        this.eventKeyUp = this.keyUp.bindAsEventListener(this);

        Event.observe(this.f1, "keyup", this.eventKeyUp);
        Event.observe(this.f2, "keyup", this.eventKeyUp);
        Event.observe(this.f3, "keyup", this.eventKeyUp);

        Event.observe(this.f1, "keypress", this.eventKeyPress);
        Event.observe(this.f2, "keypress", this.eventKeyPress);
        Event.observe(this.f3, "keypress", this.eventKeyPress);

        if (this.f4)
        {
            Event.observe(this.f4, "keyup", this.eventKeyUp);
            Event.observe(this.f4, "keypress", this.eventKeyPress);
            this.last = f4;
        }
        this.loadValues();
    },

    keyPress: function(event){
        var code = event.keyCode;
/*		if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != Event.KEY_BACKSPACE && event.keyCode != Event.KEY_DELETE && event.keyCode != Event.KEY_LEFT && event.keyCode != Event.KEY_RIGHT)
        {
            Event.stop(event);
        }*/
    },

    keyUp: function(event){
        var element = Event.element(event);
        var code = event.keyCode;
        if (element.id != this.last && code != Event.KEY_TAB && code != 16 && code != Event.KEY_BACKSPACE && code != Event.KEY_DELETE && code != Event.KEY_LEFT && code != Event.KEY_RIGHT)
        {
            var size = element.size;
            if (element.value.length == size)
            {
                if (nextElem = this.getNextElement(element.id))
                {
                    Field.activate(nextElem);
                }
            }
        }
        this.setValField();
    },

    getNextElement: function(curent_id){
        if (curent_id == this.last)
        {
            return false;
        }

        if (curent_id == this.f1)
        {
            return this.f2;
        }
        if (curent_id == this.f2)
        {
            return this.f3;
        }
        if (curent_id == this.f3)
        {
            return this.f4;
        }

        return false;
    },

    setValField: function(){
        cur_value = '';
        if($F(this.f1)) cur_value += '(' + $F(this.f1) + ') ';
        if($F(this.f2)) cur_value += $F(this.f2);
        if($F(this.f3)) cur_value += '-' + $F(this.f3);
        if (this.f4) cur_value += $F(this.f4) ? '-' + $F(this.f4) : '';

        $(this.valField).value = cur_value;
    },

    loadValues: function(){
        var val = $F(this.valField);
        if (val && val.length)
        {
            re = /^[\(]?(\d{3})[\)]?[-|\s]?(\d{3})[-|\s](\d{4})[-|\s]?(\d{0,4})?$/;
            if (re.test(val))
            {
                arrVal = re.exec(val);
                $(this.f1).value = arrVal[1];
                $(this.f2).value = arrVal[2];
                $(this.f3).value = arrVal[3];
                if (this.f4 && arrVal[4])
                {
                    $(this.f4).value = arrVal[4];
                }
            }
        }
    }
};
