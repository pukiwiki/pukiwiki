window.addEventListener("load", function() {
	// If there's no ecmascript 5 support, don't try to initialize
	if (!Object.create || !window.JSON)
		return;
	var theme = "xcode";
	var modes = ["javascript","html","php","ruby","python","perl","java","css","xml","css","sql","c_cpp"];
 	var snippets = document.body.getElementsByClassName("snippet");
 	for (var i=0; i < snippets.length; i++){
 		var node = snippets[i];
 		var lang = node.getAttribute("code-language");	
		var isEditor =  node.getAttribute("sandbox")  ? true: false;
		var isReadonly = !isEditor;		
		//if (modes.indexOf(lang)>=0){
		var code = node.textContent, id	= uniqueID() ;
		var wrap = node.parentNode.insertBefore(elt("div", {
			"class": "editor-wrap",
			"id": id,
		}), node);
		wrap.appendChild(elt("div",{
			"class"	:  "editor-title",
		},lang));
		var main = wrap.appendChild(elt("div"));
		var out = wrap.appendChild(elt("div", {
			"class": "sandbox-output"
		}));
		var menu = wrap.appendChild(elt("div", {
			"class": "sandbox-menu",
			style: "display:none",
			title: "Sandbox menu..."
		}));
		node.style.display = "none"; //hide original source code
		var editor = ace.edit(main);
   		editor.getSession().setValue(code);
   		editor.container.style.opacity = "";
		editor.setOptions({
        	maxLines:16 
        	, theme: "ace/theme/" + theme
        	, mode: "ace/mode/" + lang
        	, autoScrollEditorIntoView: true
        	, readOnly: isReadonly
    	});

		if (isEditor){
			var data = {
				editor: editor,
				wrap: wrap,
				orig: node,
				lang: lang,
			};
			data.output = new SandBox.Output(out);

			openMenu(data, menu);
		
			getSandbox(data, function(box){
				data.sandbox = box;	
			});

			wrap.addEventListener("mouseover", function(e){
				this.style.border = "4px solid #74DF00";	
				//this.style.margin = "1rem -5em";
				this.lastChild.style.display = "";
			});	
			wrap.addEventListener("mouseout", function(e){
				this.style.border = "";
				this.style.margin = "";
				this.lastChild.style.display = "none";
			});
		}
		//} //if (modes.indexOf(lang)>=0)
	}
	function uniqueID(){
 	   var S4 = function() {
    	   return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
    	};
    	return S4()+S4();
	}	
	function elt(type, attrs) {
		var firstChild = 1;
		var node = document.createElement(type);
		if (attrs && typeof attrs == "object" && attrs.nodeType == null) {
			for ( var attr in attrs)
				if (attrs.hasOwnProperty(attr)) {
					if (attr == "css")
						node.style.cssText = attrs[attr];
					else
						node.setAttribute(attr, attrs[attr]);
				}
			firstChild = 2;
		}
		for ( var i = firstChild; i < arguments.length; ++i) {
			var child = arguments[i];
			if (typeof child == "string")
				child = document.createTextNode(child);
			node.appendChild(child);
		}
		return node;
	}
	function openMenu(data, node) {
		var menu = elt("div", {
			"class": "sandbox-open-menu",
			"style"	:  "display:none",
		});
		var items = [ 
			[ "Run", function() { // (ctrl-enter)
				runCode(data);
			} ], [ "Reset", function() { // (ctrl-z)
				revertCode(data);
			} ], [ "Close", function() {  // (ctrl-q)
				closeCode(data);
			} ]	
		];

		items.forEach(function(choice) {
			menu.appendChild(elt("span", choice[0]) );
		});
		
		function click(e) {
			var target = e.target;
			if (e.target.parentNode == menu) {
				for ( var i = 0; i < menu.childNodes.length; ++i)
					if (target == menu.childNodes[i])
						items[i][1]();
			}
		}
		setTimeout(function() {
			window.addEventListener("click", click);
		}, 20);
		
		node.parentNode.appendChild(menu);
	}

	function runCode(data) {
		data.output.clear();
		var val = data.editor.getValue();
		var delay;
		//console.log("runCode",data.lang);
		var box = data.sandbox;
		if (data.lang=="html"){
			box.setHTML(val, data.output, Math.min);
			refreshPreview();
			data.editor.on("change", function() {
	  			clearTimeout(delay);
	  			delay = setTimeout(refreshPreview, 100);
    		})
		}else{
			box.run(val, data.lang, data.output);
		}
		function refreshPreview(){
			var frame = data.wrap.children[2];	//iframe for output
        	var preview =  frame.contentDocument ||  frame.contentWindow.document;
        	preview.open();
        	preview.write(data.editor.getValue());
        	preview.close();
		}
	}
	
	function closeCode(data) {
		var frame = data.wrap.children[2];	//iframe for output
		if(frame.tagName=="IFRAME"){
			frame.style.display = "none";
		}
		revertCode(data);
		data.output.clear();
	}

	function revertCode(data) {
		data.editor.setValue(data.orig.textContent);
		data.editor.clearSelection();
	}

	function getSandbox(data, callback) {
		var options = {
			lang: data.lang,
			frame:  data.frame,
			loadFiles: window.sandboxLoadFiles
		},html;
		
		if (data.lang == "html" ) {
			html = data.editor.getValue();
		}
		options.place = function(node) {
			placeFrame(node, data.wrap);
		};
		
		new SandBox(options, function(box) {
			if (html != null)
				box.win.document.documentElement.innerHTML = html;
			callback(box);
		});
	}

	function placeFrame(frame, wrap) {
		wrap.insertBefore(frame, wrap.childNodes[2]);
	}
});
