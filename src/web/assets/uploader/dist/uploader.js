/*! For license information please see Uploader.js.LICENSE.txt */
!function(){function t(e){return t="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},t(e)}function e(){"use strict";e=function(){return r};var r={},n=Object.prototype,o=n.hasOwnProperty,i=Object.defineProperty||function(t,e,r){t[e]=r.value},a="function"==typeof Symbol?Symbol:{},s=a.iterator||"@@iterator",l=a.asyncIterator||"@@asyncIterator",u=a.toStringTag||"@@toStringTag";function c(t,e,r){return Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}),t[e]}try{c({},"")}catch(t){c=function(t,e,r){return t[e]=r}}function f(t,e,r,n){var o=e&&e.prototype instanceof p?e:p,a=Object.create(o.prototype),s=new S(n||[]);return i(a,"_invoke",{value:_(t,r,s)}),a}function h(t,e,r){try{return{type:"normal",arg:t.call(e,r)}}catch(t){return{type:"throw",arg:t}}}r.wrap=f;var d={};function p(){}function v(){}function y(){}var g={};c(g,s,(function(){return this}));var m=Object.getPrototypeOf,w=m&&m(m(F([])));w&&w!==n&&o.call(w,s)&&(g=w);var b=y.prototype=p.prototype=Object.create(g);function E(t){["next","throw","return"].forEach((function(e){c(t,e,(function(t){return this._invoke(e,t)}))}))}function x(e,r){function n(i,a,s,l){var u=h(e[i],e,a);if("throw"!==u.type){var c=u.arg,f=c.value;return f&&"object"==t(f)&&o.call(f,"__await")?r.resolve(f.__await).then((function(t){n("next",t,s,l)}),(function(t){n("throw",t,s,l)})):r.resolve(f).then((function(t){c.value=t,s(c)}),(function(t){return n("throw",t,s,l)}))}l(u.arg)}var a;i(this,"_invoke",{value:function(t,e){function o(){return new r((function(r,o){n(t,e,r,o)}))}return a=a?a.then(o,o):o()}})}function _(t,e,r){var n="suspendedStart";return function(o,i){if("executing"===n)throw new Error("Generator is already running");if("completed"===n){if("throw"===o)throw i;return{value:void 0,done:!0}}for(r.method=o,r.arg=i;;){var a=r.delegate;if(a){var s=L(a,r);if(s){if(s===d)continue;return s}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if("suspendedStart"===n)throw n="completed",r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);n="executing";var l=h(t,e,r);if("normal"===l.type){if(n=r.done?"completed":"suspendedYield",l.arg===d)continue;return{value:l.arg,done:r.done}}"throw"===l.type&&(n="completed",r.method="throw",r.arg=l.arg)}}}function L(t,e){var r=e.method,n=t.iterator[r];if(void 0===n)return e.delegate=null,"throw"===r&&t.iterator.return&&(e.method="return",e.arg=void 0,L(t,e),"throw"===e.method)||"return"!==r&&(e.method="throw",e.arg=new TypeError("The iterator does not provide a '"+r+"' method")),d;var o=h(n,t.iterator,e.arg);if("throw"===o.type)return e.method="throw",e.arg=o.arg,e.delegate=null,d;var i=o.arg;return i?i.done?(e[t.resultName]=i.value,e.next=t.nextLoc,"return"!==e.method&&(e.method="next",e.arg=void 0),e.delegate=null,d):i:(e.method="throw",e.arg=new TypeError("iterator result is not an object"),e.delegate=null,d)}function C(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function j(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function S(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(C,this),this.reset(!0)}function F(t){if(t){var e=t[s];if(e)return e.call(t);if("function"==typeof t.next)return t;if(!isNaN(t.length)){var r=-1,n=function e(){for(;++r<t.length;)if(o.call(t,r))return e.value=t[r],e.done=!1,e;return e.value=void 0,e.done=!0,e};return n.next=n}}return{next:O}}function O(){return{value:void 0,done:!0}}return v.prototype=y,i(b,"constructor",{value:y,configurable:!0}),i(y,"constructor",{value:v,configurable:!0}),v.displayName=c(y,u,"GeneratorFunction"),r.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===v||"GeneratorFunction"===(e.displayName||e.name))},r.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,y):(t.__proto__=y,c(t,u,"GeneratorFunction")),t.prototype=Object.create(b),t},r.awrap=function(t){return{__await:t}},E(x.prototype),c(x.prototype,l,(function(){return this})),r.AsyncIterator=x,r.async=function(t,e,n,o,i){void 0===i&&(i=Promise);var a=new x(f(t,e,n,o),i);return r.isGeneratorFunction(e)?a:a.next().then((function(t){return t.done?t.value:a.next()}))},E(b),c(b,u,"Generator"),c(b,s,(function(){return this})),c(b,"toString",(function(){return"[object Generator]"})),r.keys=function(t){var e=Object(t),r=[];for(var n in e)r.push(n);return r.reverse(),function t(){for(;r.length;){var n=r.pop();if(n in e)return t.value=n,t.done=!1,t}return t.done=!0,t}},r.values=F,S.prototype={constructor:S,reset:function(t){if(this.prev=0,this.next=0,this.sent=this._sent=void 0,this.done=!1,this.delegate=null,this.method="next",this.arg=void 0,this.tryEntries.forEach(j),!t)for(var e in this)"t"===e.charAt(0)&&o.call(this,e)&&!isNaN(+e.slice(1))&&(this[e]=void 0)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(t){if(this.done)throw t;var e=this;function r(r,n){return a.type="throw",a.arg=t,e.next=r,n&&(e.method="next",e.arg=void 0),!!n}for(var n=this.tryEntries.length-1;n>=0;--n){var i=this.tryEntries[n],a=i.completion;if("root"===i.tryLoc)return r("end");if(i.tryLoc<=this.prev){var s=o.call(i,"catchLoc"),l=o.call(i,"finallyLoc");if(s&&l){if(this.prev<i.catchLoc)return r(i.catchLoc,!0);if(this.prev<i.finallyLoc)return r(i.finallyLoc)}else if(s){if(this.prev<i.catchLoc)return r(i.catchLoc,!0)}else{if(!l)throw new Error("try statement without catch or finally");if(this.prev<i.finallyLoc)return r(i.finallyLoc)}}}},abrupt:function(t,e){for(var r=this.tryEntries.length-1;r>=0;--r){var n=this.tryEntries[r];if(n.tryLoc<=this.prev&&o.call(n,"finallyLoc")&&this.prev<n.finallyLoc){var i=n;break}}i&&("break"===t||"continue"===t)&&i.tryLoc<=e&&e<=i.finallyLoc&&(i=null);var a=i?i.completion:{};return a.type=t,a.arg=e,i?(this.method="next",this.next=i.finallyLoc,d):this.complete(a)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),d},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.finallyLoc===t)return this.complete(r.completion,r.afterLoc),j(r),d}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.tryLoc===t){var n=r.completion;if("throw"===n.type){var o=n.arg;j(r)}return o}}throw new Error("illegal catch attempt")},delegateYield:function(t,e,r){return this.delegate={iterator:F(t),resultName:e,nextLoc:r},"next"===this.method&&(this.arg=void 0),d}},r}function r(t,e){var r="undefined"!=typeof Symbol&&t[Symbol.iterator]||t["@@iterator"];if(!r){if(Array.isArray(t)||(r=a(t))||e&&t&&"number"==typeof t.length){r&&(t=r);var n=0,o=function(){};return{s:o,n:function(){return n>=t.length?{done:!0}:{done:!1,value:t[n++]}},e:function(t){throw t},f:o}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var i,s=!0,l=!1;return{s:function(){r=r.call(t)},n:function(){var t=r.next();return s=t.done,t},e:function(t){l=!0,i=t},f:function(){try{s||null==r.return||r.return()}finally{if(l)throw i}}}}function n(t,e,r,n,o,i,a){try{var s=t[i](a),l=s.value}catch(t){return void r(t)}s.done?e(l):Promise.resolve(l).then(n,o)}function o(t){return function(){var e=this,r=arguments;return new Promise((function(o,i){var a=t.apply(e,r);function s(t){n(a,o,i,s,l,"next",t)}function l(t){n(a,o,i,s,l,"throw",t)}s(void 0)}))}}function i(t,e){return function(t){if(Array.isArray(t))return t}(t)||function(t,e){var r=null==t?null:"undefined"!=typeof Symbol&&t[Symbol.iterator]||t["@@iterator"];if(null!=r){var n,o,i,a,s=[],l=!0,u=!1;try{if(i=(r=r.call(t)).next,0===e){if(Object(r)!==r)return;l=!1}else for(;!(l=(n=i.call(r)).done)&&(s.push(n.value),s.length!==e);l=!0);}catch(t){u=!0,o=t}finally{try{if(!l&&null!=r.return&&(a=r.return(),Object(a)!==a))return}finally{if(u)throw o}}return s}}(t,e)||a(t,e)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function a(t,e){if(t){if("string"==typeof t)return s(t,e);var r=Object.prototype.toString.call(t).slice(8,-1);return"Object"===r&&t.constructor&&(r=t.constructor.name),"Map"===r||"Set"===r?Array.from(t):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?s(t,e):void 0}}function s(t,e){(null==e||e>t.length)&&(e=t.length);for(var r=0,n=new Array(e);r<e;r++)n[r]=t[r];return n}var l,u;Craft.CloudUploader=Craft.BaseUploader.extend({element:null,$fileInput:null,_totalBytes:0,_uploadedBytes:0,_lastUploadedBytes:0,_validFileCounter:0,_handleChange:null,init:function(t,e){var r=this;e=$.extend({},Craft.CloudUploader.defaults,e),this.base(t,e),this.element=t[0],this.$dropZone=e.dropZone,this._handleChange=this.handleChange.bind(this),this.$fileInput.on("change",this._handleChange),Object.entries(this.settings.events).forEach((function(t){var e=i(t,2),n=e[0],o=e[1];r.element.addEventListener(n,o)})),this.allowedKinds&&!this._extensionList&&this._createExtensionList(),this.$dropZone&&this.$dropZone.on({dragover:function(t){r.handleDragEvent(t)&&(t.originalEvent.dataTransfer.dropEffect="copy")},drop:function(t){r.handleDragEvent(t)&&r.uploadFiles(t.originalEvent.dataTransfer.files)},dragenter:this.handleDragEvent,dragleave:this.handleDragEvent})},handleDragEvent:function(t){var e,r;return!(null==t||null===(e=t.originalEvent)||void 0===e||null===(r=e.dataTransfer)||void 0===r||!r.files||(t.preventDefault(),t.stopPropagation(),0))},uploadFiles:(u=o(e().mark((function t(n){var o,i,l,u,c,f=this;return e().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:o=function(t){if(Array.isArray(t))return s(t)}(e=n)||function(t){if("undefined"!=typeof Symbol&&null!=t[Symbol.iterator]||null!=t["@@iterator"])return Array.from(t)}(e)||a(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}(),i=o.filter((function(t){var e,r=!0;if(null!==(e=f._extensionList)&&void 0!==e&&e.length){var n=t.name.match(/\.([a-z0-4_]+)$/i)[1];f._extensionList.includes(n.toLowerCase())&&(f._rejectedFiles.type.push("“"+t.name+"”"),r=!1)}return t.size>f.settings.maxFileSize&&(f._rejectedFiles.size.push("“"+t.name+"”"),r=!1),r&&"function"==typeof f.settings.canAddMoreFiles&&!f.settings.canAddMoreFiles(f._validFileCounter)&&(f._rejectedFiles.limit.push("“"+t.name+"”"),r=!1),r&&(f._totalBytes+=t.size,f._validFileCounter++,f._inProgressCounter++),r})),this.processErrorMessages(),this.element.dispatchEvent(new Event("fileuploadstart")),l=r(i),t.prev=5,l.s();case 7:if((u=l.n()).done){t.next=14;break}return c=u.value,t.next=11,this.uploadFile(c);case 11:this._inProgressCounter--;case 12:t.next=7;break;case 14:t.next=19;break;case 16:t.prev=16,t.t0=t.catch(5),l.e(t.t0);case 19:return t.prev=19,l.f(),t.finish(19);case 22:this._totalBytes=0,this._uploadedBytes=0,this._lastUploadedBytes=0,this._inProgressCounter=0;case 26:case"end":return t.stop()}var e}),t,this,[[5,16,19,22]])}))),function(t){return u.apply(this,arguments)}),uploadFile:(l=o(e().mark((function t(r){var n,o,i,a,s,l=this;return e().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return n=Object.assign({},this.formData,{filename:r.name,lastModified:r.lastModified}),t.prev=1,t.next=4,Craft.sendActionRequest("POST","cloud/get-upload-url",{data:n});case 4:return o=t.sent,Object.assign(n,o.data,{size:r.size}),t.prev=6,t.next=9,this.getImage(r);case 9:i=t.sent,a=i.width,s=i.height,Object.assign(n,{width:a,height:s}),t.next=17;break;case 15:t.prev=15,t.t0=t.catch(6);case 17:return t.next=19,axios.put(o.data.url,r,{headers:{"Content-Type":r.type},onUploadProgress:function(t){l._uploadedBytes=l._uploadedBytes+t.loaded-l._lastUploadedBytes,l._lastUploadedBytes=t.loaded,l.element.dispatchEvent(new CustomEvent("fileuploadprogressall",{detail:{loaded:l._uploadedBytes,total:l._totalBytes}}))}});case 19:return t.next=21,axios.post(this.settings.url,n);case 21:o=t.sent,this.element.dispatchEvent(new CustomEvent("fileuploaddone",{detail:o.data})),t.next=28;break;case 25:t.prev=25,t.t1=t.catch(1),this.element.dispatchEvent(new CustomEvent("fileuploadfail",{detail:{message:t.t1.message,filename:r.name}}));case 28:return t.prev=28,this._lastUploadedBytes=0,this.element.dispatchEvent(new Event("fileuploadalways")),t.finish(28);case 32:case"end":return t.stop()}}),t,this,[[1,25,28,32],[6,15]])}))),function(t){return l.apply(this,arguments)}),handleChange:function(t){this.uploadFiles(t.target.files),this.$fileInput.val("")},getImage:function(t){return new Promise((function(e,r){t.type.startsWith("image/")||r(new Error("File is not an image."));var n=new FileReader;n.addEventListener("load",(function(t){var o=new Image;o.src=n.result,o.addEventListener("load",(function(t){e(t.target)})),o.addEventListener("error",(function(t){r(new Error("Error loading image."))}))}),!1),n.readAsDataURL(t)}))},destroy:function(){var t=this;this.$fileInput.off("change",this._handleChange),this.$dropZone.off("dragover drop dragenter dragleave"),Object.entries(this.settings.events).forEach((function(e){var r=i(e,2),n=r[0],o=r[1];t.element.removeEventListener(n,o)}))}},{defaults:{maxFileSize:Craft.maxUploadFileSize,createAction:"cloud/create-asset",replaceAction:"cloud/replace-file"}}),Craft.registerAssetUploaderClass("craft\\cloud\\fs\\AssetFs",Craft.CloudUploader)}();
//# sourceMappingURL=Uploader.js.map