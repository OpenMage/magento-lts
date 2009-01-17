/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

Flex = {};
Flex.currentID = 0;
Flex.uniqId = function() {
    return 'flexMovieUID'+( ++Flex.currentID );
}

Flex.Object = Class.create();

Object.extend( Flex.Object.prototype, {
			initialize: function ( config ) {
                this.isIE  = (navigator.appVersion.indexOf("MSIE") != -1) ? true : false;
            	this.isWin = (navigator.appVersion.toLowerCase().indexOf("win") != -1) ? true : false;
            	this.isOpera = (navigator.userAgent.indexOf("Opera") != -1) ? true : false;
            	this.attributes = {
            		 quality:"high",
            		 pluginspage: "http://www.adobe.com/go/getflashplayer",
            		 type: "application/x-shockwave-flash",
            		 allowScriptAccess: "always",
                     classid: "clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
            	};
            	this.bridgeName = '';
            	this.bridge = false;
            	this.setAttributes( config );
            	this.applied = false;

            	var myTemplatesPattern = /(^|.|\r|\n)(\{(.*?)\})/;
            	if(this.detectFlashVersion(9, 0, 28)) {
            		if(this.isIE && !this.isOpera) {
            			this.template = new Template( '<object {objectAttributes}><param name="allowFullScreen" value="true"/>{objectParameters}</object>', myTemplatesPattern )
            		} else {
            			this.template = new Template( '<embed {embedAttributes} allowfullscreen="true" />', myTemplatesPattern );
            		}
            	} else {
            		this.template = new Template(  'This content requires the Adobe Flash Player. '
            										   +' <a href=http://www.adobe.com/go/getflash/>Get Flash</a>', myTemplatesPattern );
            	}

            	this.parametersTemplate = new Template( '<param name="{name}" value="{value}" />', myTemplatesPattern );
            	this.attributesTemplate = new Template( ' {name}="{value}" ', myTemplatesPattern );
            },
			setAttribute : function( name, value ) {
				if(!this.applied) {
					this.attributes[name] = value;
                }
			},
			getAttribute : function( name ) {
				return this.attributes[name];
			},
			setAttributes : function( attributesList ) {
				$H(attributesList).each(function(pair){
					this.setAttribute(pair.key, pair.value);
				}.bind(this));
			},
			getAttributes : function( ) {
				return this.attributes;
			},
			apply : function( container ) {
				if (!this.applied)	{
					this.setAttribute( "id", Flex.uniqId());
					this.preInitBridge();
					var readyHTML = this.template.evaluate( this.generateTemplateValues() );
                    $(container).update( readyHTML );
				}
				this.applied = true;
			},
            applyWrite : function( ) {
				if (!this.applied)	{
					this.setAttribute( "id", Flex.uniqId());
					this.preInitBridge();
					var readyHTML = this.template.evaluate( this.generateTemplateValues() );
                    document.write( readyHTML );
				}
				this.applied = true;
			},
			preInitBridge: function () {
			    this.bridgeName = this.getAttribute('id') + 'bridge';
			    this.setAttribute('flashVars', 'bridgeName=' + this.bridgeName);
			    var scopeObj = this;
			    FABridge.addInitializationCallback(
			         this.bridgeName,
			         function () {
			             scopeObj.bridge = this.root();
			             scopeObj.initBridge();
			         }
			    );
			},
			initBridge: function() {
			    if(this.onBridgeInit) {
			        this.onBridgeInit(this.getBridge());
			    }
			},
            getBridge : function() {
				return this.bridge;
			},
			generateTemplateValues : function( )
			{
				var embedAttributes = {};
				var objectAttributes = {};
				var parameters = {};
				$H(this.attributes).each(function(pair) {
					var attributeName = pair.key.toLowerCase();
                    this.attributes[pair.key] = this.escapeAttributes( pair.value );
					switch (attributeName) {
						case "pluginspage":
							embedAttributes[pair.key] = this.attributes[pair.key];
							break;
						case "src":
						case "movie":
							embedAttributes['src'] = parameters['movie'] = this.attributes[pair.key];
							break;
						case "type":
							embedAttributes[pair.key]  = this.attributes[pair.key];
						case "classid":
						case "codebase":
							objectAttributes[pair.key] = this.attributes[pair.key];
							break;
						case "id":
							embedAttributes['name'] = this.attributes[pair.key];
						case "width":
						case "height":
						case "align":
						case "vspace":
						case "hspace":
						case "class":
						case "title":
						case "accesskey":
						case "name":
						case "tabindex":
							embedAttributes[pair.key] = objectAttributes[pair.key] = this.attributes[pair.key];
							break;
						default:
							embedAttributes[pair.key] = parameters[pair.key] = this.attributes[pair.key];
							break;
					}
				}.bind(this));

				var result = {
				    objectAttributes: '',
				    objectParameters: '',
				    embedAttributes : ''
				};


				$H(objectAttributes).each(function(pair){
			         result.objectAttributes += this.attributesTemplate.evaluate({
                         name:pair.key,
                         value:pair.value
			         });
				}.bind(this));

				$H(embedAttributes).each(function(pair){
			         result.embedAttributes += this.attributesTemplate.evaluate({
                         name:pair.key,
                         value:pair.value
			         });
				}.bind(this));

				$H(parameters).each(function(pair){
			         result.objectParameters += this.parametersTemplate.evaluate({
                         name:pair.key,
                         value:pair.value
			         });
				}.bind(this));

				return result;
			},
            escapeAttributes: function (value) {
                if(typeof value == 'string') {
                    return value.replace(new RegExp("&","g"), "&amp;");
                } else {
                    return value;
                }
            },
			detectFlashVersion : function( reqMajorVer, reqMinorVer, reqRevision ) {
				var versionStr = this.getSwfVer();
			    if (versionStr == -1 ) {
			        return false;
			    } else if (versionStr != 0) {
			        if(this.isIE && this.isWin && !this.isOpera) {
			            // Given "WIN 2,0,0,11"
			            tempArray         = versionStr.split(" ");  // ["WIN", "2,0,0,11"]
			            tempString        = tempArray[1];           // "2,0,0,11"
			            versionArray      = tempString.split(",");  // ['2', '0', '0', '11']
			        } else {
			            versionArray      = versionStr.split(".");
			        }
			        var versionMajor      = versionArray[0];
			        var versionMinor      = versionArray[1];
			        var versionRevision   = versionArray[2];

			            // is the major.revision >= requested major.revision AND the minor version >= requested minor
			        if (versionMajor > parseFloat(reqMajorVer)) {
			            return true;
			        } else if (versionMajor == parseFloat(reqMajorVer)) {
			            if (versionMinor > parseFloat(reqMinorVer))
			                return true;
			            else if (versionMinor == parseFloat(reqMinorVer)) {
			                if (versionRevision >= parseFloat(reqRevision))
			                    return true;
			            }
			        }
			        return false;
			    }
			},
			controlVersion : function () {
			    var version;
			    var axo;
			    var e;
			    try {
			        // version will be set for 7.X or greater players
			        axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
			        version = axo.GetVariable("$version");
			    } catch (e) {
			    }

			    if (!version) {
			        try {
			            axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
			            version = "WIN 6,0,21,0";
			            axo.AllowScriptAccess = "always";
			            version = axo.GetVariable("$version");

			        } catch (e) {
			        }
			    }

			    if (!version) {
			        try {
			            axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
			            version = axo.GetVariable("$version");
			        } catch (e) {
			        }
			    }

			    if (!version) {
			        try {
			            axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
			            version = "WIN 3,0,18,0";
			        } catch (e) {
			        }
			    }

			    if (!version) {
			        try {
			            axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
			            version = "WIN 2,0,0,11";
			        } catch (e) {
			            version = -1;
			        }
			    }
			    return version;
			},
			getSwfVer : function (){
			    var flashVer = -1;
			    if (navigator.plugins != null && navigator.plugins.length > 0) {
			        if (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]) {
			            var swVer2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
			            var flashDescription = navigator.plugins["Shockwave Flash" + swVer2].description;
			            var descArray = flashDescription.split(" ");
			            var tempArrayMajor = descArray[2].split(".");
			            var versionMajor = tempArrayMajor[0];
			            var versionMinor = tempArrayMajor[1];
			            if ( descArray[3] != "" ) {
			                tempArrayMinor = descArray[3].split("r");
			            } else {
			                tempArrayMinor = descArray[4].split("r");
			            }
			            var versionRevision = tempArrayMinor[1] > 0 ? tempArrayMinor[1] : 0;
			            var flashVer = versionMajor + "." + versionMinor + "." + versionRevision;
			        }
			    }
			    else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.6") != -1) flashVer = 4;
			    else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.5") != -1) flashVer = 3;
			    else if (navigator.userAgent.toLowerCase().indexOf("webtv") != -1) flashVer = 2;
			    else if ( this.isIE && this.isWin && !this.isOpera ) {
			        flashVer = this.controlVersion();
			    }
			    return flashVer;
			}
} );

/** FABridge extenstion **/

/*
Copyright 2006 Adobe Systems Incorporated

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

 The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.


THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/


/*
 * The Bridge class, responsible for navigating AS instances
 */
function FABridge(target,bridgeName)
{
    this.target = target;
    this.remoteTypeCache = {};
    this.remoteInstanceCache = {};
    this.remoteFunctionCache = {};
    this.localFunctionCache = {};
    this.bridgeID = FABridge.nextBridgeID++;
    this.name = bridgeName;
    this.nextLocalFuncID = 0;
    FABridge.instances[this.name] = this;
    FABridge.idMap[this.bridgeID] = this;

    return this;
}

// type codes for packed values
FABridge.TYPE_ASINSTANCE =  1;
FABridge.TYPE_ASFUNCTION =  2;

FABridge.TYPE_JSFUNCTION =  3;
FABridge.TYPE_ANONYMOUS =   4;

FABridge.initCallbacks = {};
FABridge.userTypes = {};

FABridge.addToUserTypes = function()
{
	for (var i = 0; i < arguments.length; i++)
	{
		FABridge.userTypes[arguments[i]] = {
			'typeName': arguments[i],
			'enriched': false
		};
	}
}

FABridge.argsToArray = function(args)
{
    var result = [];
    for (var i = 0; i < args.length; i++)
    {
        result[i] = args[i];
    }
    return result;
}

function instanceFactory(objID)
{
    this.fb_instance_id = objID;
    return this;
}

function FABridge__invokeJSFunction(args)
{
    var funcID = args[0];
    var throughArgs = args.concat();//FABridge.argsToArray(arguments);
    throughArgs.shift();

    var bridge = FABridge.extractBridgeFromID(funcID);
    return bridge.invokeLocalFunction(funcID, throughArgs);
}

FABridge.addInitializationCallback = function(bridgeName, callback)
{
    var inst = FABridge.instances[bridgeName];
    if (inst != undefined)
    {
        callback.call(inst);
        return;
    }

    var callbackList = FABridge.initCallbacks[bridgeName];
    if(callbackList == null)
    {
        FABridge.initCallbacks[bridgeName] = callbackList = [];
    }

    callbackList.push(callback);
}

/**
 * attach the bridge to an instance of a flash movie on the page
 * by looking at the flashvars to see if the bridgeName is present
 * called from AS when the bridge is initialized to attach the JS
 */
function FABridge__bridgeInitialized(bridgeName)
{
    var searchStr = "bridgeName="+ bridgeName;

    if (Prototype.Browser.IE)
    {
        var flashInstances = $$('object');
        if (flashInstances.size() == 1)
        {
            FABridge.attachBridge(flashInstances[0], bridgeName);
        }
        else
        {
            flashInstances.each(function(inst){
                var params = inst.select('param');
                var flash_found = false;

                params.each(function(param) {
                    if (param["name"].toLowerCase() == "flashvars" && param["value"].indexOf(searchStr) >= 0) {
                        FABridge.attachBridge(inst, bridgeName);
                        flash_found = true;
                        throw $break;
                    }
                });

                if (flash_found) {
                    throw $break;
                }
            });
        }
    }
    else
    {
        var flashInstances = $$('embed');
        if (flashInstances.size() == 1)
        {
            FABridge.attachBridge(flashInstances[0], bridgeName);
        }
        else
        {
            flashInstances.each(function(inst){
                var flashVars = inst.readAttribute('flashVars') || inst.readAttribute('flashvars');
                if (flashVars.indexOf(searchStr) >= 0) {
                    FABridge.attachBridge(inst, bridgeName);
                }
            });
        }
    }
    return true;
}

// used to track multiple bridge instances, since callbacks from AS are global across the page.

FABridge.nextBridgeID = 0;
FABridge.instances = {};
FABridge.idMap = {};
FABridge.refCount = 0;

FABridge.extractBridgeFromID = function(id)
{
    var bridgeID = (id >> 16);
    return FABridge.idMap[bridgeID];
}

FABridge.attachBridge = function(instance, bridgeName)
{
    var newBridgeInstance = new FABridge(instance, bridgeName);

    FABridge[bridgeName] = newBridgeInstance;

/*  FABridge[bridgeName] = function() {
        return newBridgeInstance.root();
    }
*/
    var callbacks = FABridge.initCallbacks[bridgeName];
    if (callbacks == null)
    {
        return;
    }
    for (var i = 0; i < callbacks.length; i++)
    {
        callbacks[i].call(newBridgeInstance);
    }
    delete FABridge.initCallbacks[bridgeName]
}

// some methods can't be proxied.  You can use the explicit get,set, and call methods if necessary.

FABridge.blockedMethods =
{
    toString: true,
    get: true,
    set: true,
    call: true
};

FABridge.prototype =
{


// bootstrapping

    root: function()
    {
        return this.deserialize(this.target.getRoot());
    },
//clears all of the AS objects in the cache maps
    releaseASObjects: function()
    {
        return this.target.releaseASObjects();
    },
//clears a specific object in AS from the type maps
    releaseNamedASObject: function(value)
    {
        if(typeof(value) != "object")
        {
            return false;
        }
        else
        {
            var ret =  this.target.releaseNamedASObject(value.fb_instance_id);
            return ret;
        }
    },
//create a new AS Object
    create: function(className)
    {
        return this.deserialize(this.target.create(className));
    },


    // utilities

    makeID: function(token)
    {
        return (this.bridgeID << 16) + token;
    },


    // low level access to the flash object

//get a named property from an AS object
    getPropertyFromAS: function(objRef, propName)
    {
        if (FABridge.refCount > 0)
        {
            throw new Error("You are trying to call recursively into the Flash Player which is not allowed. In most cases the JavaScript setTimeout function, can be used as a workaround.");
        }
        else
        {
            FABridge.refCount++;
            retVal = this.target.getPropFromAS(objRef, propName);
            retVal = this.handleError(retVal);
            FABridge.refCount--;
            return retVal;
        }
    },
//set a named property on an AS object
    setPropertyInAS: function(objRef,propName, value)
    {
        if (FABridge.refCount > 0)
        {
            throw new Error("You are trying to call recursively into the Flash Player which is not allowed. In most cases the JavaScript setTimeout function, can be used as a workaround.");
        }
        else
        {
            FABridge.refCount++;
            retVal = this.target.setPropInAS(objRef,propName, this.serialize(value));
            retVal = this.handleError(retVal);
            FABridge.refCount--;
            return retVal;
        }
    },

//call an AS function
    callASFunction: function(funcID, args)
    {
        if (FABridge.refCount > 0)
        {
            throw new Error("You are trying to call recursively into the Flash Player which is not allowed. In most cases the JavaScript setTimeout function, can be used as a workaround.");
        }
        else
        {
            FABridge.refCount++;
            retVal = this.target.invokeASFunction(funcID, this.serialize(args));
            retVal = this.handleError(retVal);
            FABridge.refCount--;
            return retVal;
        }
    },
//call a method on an AS object
    callASMethod: function(objID, funcName, args)
    {
        if (FABridge.refCount > 0)
        {
            throw new Error("You are trying to call recursively into the Flash Player which is not allowed. In most cases the JavaScript setTimeout function, can be used as a workaround.");
        }
        else
        {
            FABridge.refCount++;
            args = this.serialize(args);
            retVal = this.target.invokeASMethod(objID, funcName, args);
            retVal = this.handleError(retVal);
            FABridge.refCount--;
            return retVal;
        }
    },

    // responders to remote calls from flash

    //callback from flash that executes a local JS function
    //used mostly when setting js functions as callbacks on events
    invokeLocalFunction: function(funcID, args)
    {
        var result;
        var func = this.localFunctionCache[funcID];

        if(func != undefined)
        {
            result = this.serialize(func.apply(null, this.deserialize(args)));
        }

        return result;
    },

    // Object Types and Proxies
	getUserTypeDescriptor: function(objTypeName)
	{
		var simpleType = objTypeName.replace(/^([^:]*)\:\:([^:]*)$/, "$2");
    	var isUserProto = ((typeof window[simpleType] == "function") && (typeof FABridge.userTypes[simpleType] != "undefined"));

    	var protoEnriched = false;

    	if (isUserProto) {
	    	protoEnriched = FABridge.userTypes[simpleType].enriched;
    	}
    	var toret = {
    		'simpleType': simpleType,
    		'isUserProto': isUserProto,
    		'protoEnriched': protoEnriched
    	};
    	return toret;
	},

    // accepts an object reference, returns a type object matching the obj reference.
    getTypeFromName: function(objTypeName)
    {
    	var ut = this.getUserTypeDescriptor(objTypeName);
    	var toret = this.remoteTypeCache[objTypeName];
    	if (ut.isUserProto)
		{
    		//enrich both of the prototypes: the FABridge one, as well as the class in the page.
	    	if (!ut.protoEnriched)
			{

		    	for (i in window[ut.simpleType].prototype)
				{
		    		toret[i] = window[ut.simpleType].prototype[i];
		    	}

				window[ut.simpleType].prototype = toret;
				this.remoteTypeCache[objTypeName] = toret;
				FABridge.userTypes[ut.simpleType].enriched = true;
	    	}
    	}
        return toret;
    },
    //create an AS proxy for the given object ID and type
    createProxy: function(objID, typeName)
    {
    	//get user created type, if it exists
    	var ut = this.getUserTypeDescriptor(typeName);

        var objType = this.getTypeFromName(typeName);

		if (ut.isUserProto)
		{
			var instFactory = window[ut.simpleType];
			var instance = new instFactory(this.name, objID);
			instance.fb_instance_id = objID;
		}
		else
		{
	        instanceFactory.prototype = objType;
	        var instance = new instanceFactory(objID);
		}

        this.remoteInstanceCache[objID] = instance;
        return instance;
    },
    //return the proxy associated with the given object ID
    getProxy: function(objID)
    {
        return this.remoteInstanceCache[objID];
    },

    // accepts a type structure, returns a constructed type
    addTypeDataToCache: function(typeData)
    {
        newType = new ASProxy(this, typeData.name);
        var accessors = typeData.accessors;
        for (var i = 0; i < accessors.length; i++)
        {
            this.addPropertyToType(newType, accessors[i]);
        }

        var methods = typeData.methods;
        for (var i = 0; i < methods.length; i++)
        {
            if (FABridge.blockedMethods[methods[i]] == undefined)
            {
                this.addMethodToType(newType, methods[i]);
            }
        }


        this.remoteTypeCache[newType.typeName] = newType;
        return newType;
    },

    //add a property to a typename; used to define the properties that can be called on an AS proxied object
    addPropertyToType: function(ty, propName)
    {
        var c = propName.charAt(0);
        var setterName;
        var getterName;
        if(c >= "a" && c <= "z")
        {
            getterName = "get" + c.toUpperCase() + propName.substr(1);
            setterName = "set" + c.toUpperCase() + propName.substr(1);
        }
        else
        {
            getterName = "get" + propName;
            setterName = "set" + propName;
        }
        ty[setterName] = function(val)
        {
            this.bridge.setPropertyInAS(this.fb_instance_id, propName, val);
        }
        ty[getterName] = function()
        {
            return this.bridge.deserialize(this.bridge.getPropertyFromAS(this.fb_instance_id, propName));
        }
    },

    //add a method to a typename; used to define the methods that can be callefd on an AS proxied object
    addMethodToType: function(ty, methodName)
    {
        ty[methodName] = function()
        {
            return this.bridge.deserialize(this.bridge.callASMethod(this.fb_instance_id, methodName, FABridge.argsToArray(arguments)));
        }
    },

    // Function Proxies

    //returns the AS proxy for the specified function ID
    getFunctionProxy: function(funcID)
    {
        var bridge = this;
        if (this.remoteFunctionCache[funcID] == null)
        {
            this.remoteFunctionCache[funcID] = function()
            {
                bridge.callASFunction(funcID, FABridge.argsToArray(arguments));
            }
        }
        return this.remoteFunctionCache[funcID];
    },

    //reutrns the ID of the given function; if it doesnt exist it is created and added to the local cache
    getFunctionID: function(func)
    {
        if (func.__bridge_id__ == undefined)
        {
            func.__bridge_id__ = this.makeID(this.nextLocalFuncID++);
            this.localFunctionCache[func.__bridge_id__] = func;
        }
        return func.__bridge_id__;
    },

    // serialization / deserialization

    serialize: function(value)
    {
        var result = {};

        var t = typeof(value);
        //primitives are kept as such
        if (t == "number" || t == "string" || t == "boolean" || t == null || t == undefined)
        {
            result = value;
        }
        else if (value instanceof Array)
        {
            //arrays are serializesd recursively
            result = [];
            for (var i = 0; i < value.length; i++)
            {
                result[i] = this.serialize(value[i]);
            }
        }
        else if (t == "function")
        {
            //js functions are assigned an ID and stored in the local cache
            result.type = FABridge.TYPE_JSFUNCTION;
            result.value = this.getFunctionID(value);
        }
        else if (value instanceof ASProxy)
        {
            result.type = FABridge.TYPE_ASINSTANCE;
            result.value = value.fb_instance_id;
        }
        else
        {
            result.type = FABridge.TYPE_ANONYMOUS;
            result.value = value;
        }

        return result;
    },

    //on deserialization we always check the return for the specific error code that is used to marshall NPE's into JS errors
    // the unpacking is done by returning the value on each pachet for objects/arrays
    deserialize: function(packedValue)
    {

        var result;

        var t = typeof(packedValue);
        if (t == "number" || t == "string" || t == "boolean" || packedValue == null || packedValue == undefined)
        {
            result = this.handleError(packedValue);
        }
        else if (packedValue instanceof Array)
        {
            result = [];
            for (var i = 0; i < packedValue.length; i++)
            {
                result[i] = this.deserialize(packedValue[i]);
            }
        }
        else if (t == "object")
        {
            for(var i = 0; i < packedValue.newTypes.length; i++)
            {
                this.addTypeDataToCache(packedValue.newTypes[i]);
            }
            for (var aRefID in packedValue.newRefs)
            {
                this.createProxy(aRefID, packedValue.newRefs[aRefID]);
            }
            if (packedValue.type == FABridge.TYPE_PRIMITIVE)
            {
                result = packedValue.value;
            }
            else if (packedValue.type == FABridge.TYPE_ASFUNCTION)
            {
                result = this.getFunctionProxy(packedValue.value);
            }
            else if (packedValue.type == FABridge.TYPE_ASINSTANCE)
            {
                result = this.getProxy(packedValue.value);
            }
            else if (packedValue.type == FABridge.TYPE_ANONYMOUS)
            {
                result = packedValue.value;
            }
        }
        return result;
    },
    //increases the reference count for the given object
    addRef: function(obj)
    {
        this.target.incRef(obj.fb_instance_id);
    },
    //decrease the reference count for the given object and release it if needed
    release:function(obj)
    {
        this.target.releaseRef(obj.fb_instance_id);
    },

    // check the given value for the components of the hard-coded error code : __FLASHERROR
    // used to marshall NPE's into flash

    handleError: function(value)
    {
        if (typeof(value)=="string" && value.indexOf("__FLASHERROR")==0)
        {
            var myErrorMessage = value.split("||");
            if(FABridge.refCount > 0 )
            {
                FABridge.refCount--;
            }
            throw new Error(myErrorMessage[1]);
            return value;
        }
        else
        {
            return value;
        }
    }
};

// The root ASProxy class that facades a flash object

ASProxy = function(bridge, typeName)
{
    this.bridge = bridge;
    this.typeName = typeName;
    return this;
};
//methods available on each ASProxy object
ASProxy.prototype =
{
    get: function(propName)
    {
        return this.bridge.deserialize(this.bridge.getPropertyFromAS(this.fb_instance_id, propName));
    },

    set: function(propName, value)
    {
        this.bridge.setPropertyInAS(this.fb_instance_id, propName, value);
    },

    call: function(funcName, args)
    {
        this.bridge.callASMethod(this.fb_instance_id, funcName, args);
    },

    addRef: function() {
        this.bridge.addRef(this);
    },

    release: function() {
        this.bridge.release(this);
    }
};
