var SandBox;
(function() {
	"use strict";
	function timeout(win, f, ms) {
		win.__setTimeout(f, ms);
	};
	var exceutables = [];
	SandBox = function(options, callback) {
		this.callbacks = {};
		var sandbox = this ;
		var frame = this.frame = document.createElement("iframe");
		frame.src = "about:blank";
		frame.style.display = "none";
		if (options.place) {
			options.place(frame);
		} else {
			document.body.appendChild(frame);
		}
		var win = this.win = frame.contentWindow;
		var self = win.__sandbox = this;

		win.onerror = function(e, _file, line) {
			if (!self.output)
				return;
			self.output.out("error", [ e
					+ (line != null ? " (line " + line + ")": "") ]);
			return true;
		};
		win.console = {
			log: function() {
				self.out("log", arguments);
			},
			error: function() {
				self.out("error", arguments);
			},
			warn: function() {
				self.out("warn", arguments);
			},
			info: function() {
				self.out("log", arguments);
			}
		};
		win.__setTimeout = win.setTimeout;
		win.__setInterval = win.setInterval;
		win.setTimeout = function(code, time) {
			return win.__setTimeout(function() {
				self.run(code);
			}, time);
		};
		win.setInterval = function(code, time) {
			return win.__setInterval(function() {
				self.run(code);
			}, time);
		};
		this.startedAt = 0;
		this.extraSecs = 1;
		this.output = null;
		callback(sandbox);
	};

	SandBox.prototype = {
		run: function(code, lang, output) {
			if (output)
				this.output = output;
			this.startedAt = Date.now();
			this.extraSecs = 1;
			this.win.__c = 0;
			timeout(this.win, preprocess(code, lang, this), 0);
		},
		setHTML: function(code, output, callback) {
			var sandbox = this, doc = this.win.document;
			this.frame.style.display = "block";
			doc.documentElement.innerHTML = code;
			this.frame.style.height = "50px";
			this.resizeFrame();
		},
		resizeFrame: function() {
			this.frame.style.height = Math.max(80, Math.min(
				this.frame.contentWindow.document.body.scrollHeight, 600))
				+ "px";
		},
		tick: function() {
			var now = Date.now();
			if (now < this.startedAt + this.extraSecs * 1000)
				return;
			var bail = confirm("This code has been running for "
				+ this.extraSecs + " seconds. Abort it?");
			this.startedAt += Date.now() - now;
			this.extraSecs += Math.max(this.extraSecs, 8);
			if (bail)
				throw new Error("Aborted");
		},
		out: function(type, args) {
			if (this.output)
				this.output.out(type, args);
			else
				console[type].apply(console, args);
		},
		error: function(exception) {
			if (!this.output)
				throw exception;
			var stack = parseStack(exception.stack);
			this.output.out("error",
					[ String(exception)
						+ (stack.length ? " (" + frameString(stack[0])
						+ ")": "") 
					]);
			if (stack.length > 1) {
				this.output.div.lastChild.appendChild(document
					.createTextNode(" "));
				var mark = this.output.div.lastChild.appendChild(document
					.createElement("span"));
				mark.innerHTML = "…";
				mark.className = "sandbox-output-etc";
				mark.addEventListener("click", function(e) {
					mark.className = "";
					mark.innerHTML = "\n called from "
						+ stack.slice(1).map(frameString).join(
						"\n called from ");
				});
			}
		}
	};

	function parseStack(stack) { // parse error information in error stack
		var found = [], m;
		var re = /([\w$]*)@.*?:(\d+)|\bat (?:([^\s(]+) \()?.*?:(\d+)/g;
		while (m = re.exec(stack)) {
			found.push({
				fn: m[1] || m[3] || null,
				line: m[2] || m[4]
			});
		}
		return found;
	}
	function frameString(frame) { // create a frame name
		return "line " + frame.line
				+ (frame.fn ? " in function " + frame.fn: "");
	}

	function preprocess(code, lang, sandbox) {
		if (typeof code != "string") {
			if (code.apply)
				return function() {
					try {
						return code.apply(this, arguments);
					} catch (e) {
						sandbox.error(e);
					}
				};
			return code;
		}
		function loop(node) {
			if (node.body.type == "BlockStatement") {
				patches.push(
					{from: node.body.end - 1, text: backJump}
				);
			} else {
				patches.push(
					{from: node.body.start, text: "{" }, 
					{from: node.body.end, text: backJump + "}" }
				);
			}
		}// end loop()

		switch (lang) {
		case "javascript":
			try {
				var ast = acorn.parse(code);
			} catch (e) {
				return code;
			}
			var patches = [];
			var backJump = "if (++__c % 1000 === 0) __sandbox.tick();";

			acorn.walk.simple(ast, {
				ForStatement: loop,
				ForInStatement: loop,
				WhileStatement: loop,
				DoWhileStatement: loop
			});
			patches.sort(function(a, b) {
				return a.from - b.from;
			});
			var out = "";
			for ( var i = 0, pos = 0; i < patches.length; ++i) {
				var patch = patches[i];
				out += code.slice(pos, patch.from) + patch.text;
				pos = patch.to || patch.from;
			}
			out += code.slice(pos, code.length);
			return "try{" + out + "\n}catch(e){__sandbox.error(e);}";
		case "php":
		case "java":
		case "perl":
		case "python":
		case "ruby":
		/*	
			$.ajax({
				type: "post",
				url: "http://xxx.kyusan-u.ac.jp/node/run/", // call backend
				data: {	"lang": lang,	"src": code },
				success: function(data) {
					sandbox.setHTML(data); 
				},
				error: function(xrl, txtStatus, err) {
					var msg = xrl.statusText;
					sandbox.output.out("error", msg);
				}
			});
			break;
		*/
		default:
			sandbox.output.out("Error", " " + lang + " not supported");
		}//end switch (lang)
	}// end of function preprocess()

	var Output = SandBox.Output = function(div) {
		this.div = div;
	};

	Output.prototype = {
		clear: function() {
			this.div.innerHTML = "";
		},
		out: function(type, args) {
			var wrap = document.createElement("pre");
			wrap.className = "sandbox-output-" + type;
			for ( var i = 0; i < args.length; ++i) {
				var arg = args[i];
				if (i)
					wrap.appendChild(document.createTextNode(" "));
				if (typeof arg == "string")
					wrap.appendChild(document.createTextNode(arg));
				else
					wrap.appendChild(represent(arg, 58));
			}
			this.div.appendChild(wrap);
		}
	};

	// formatted output

	function span(type, text) {
		var sp = document.createElement("span");
		sp.className = "sandbox-output-" + type;
		sp.appendChild(document.createTextNode(text));
		return sp;
	}

	function eltSize(elt) {
		return elt.textContent.length;
	}

	function represent(val, space) {
		if (typeof val == "boolean")
			return span("bool", String(val));
		if (typeof val == "number")
			return span("number", String(val));
		if (typeof val == "string")
			return span("string", JSON.stringify(val));
		if (val == null)
			return span("null", String(val));
		if (Array.isArray(val))
			return representArray(val, space);
		else
			return representObj(val, space);
	}

	function representArray(val, space) {
		space -= 2;
		var wrap = document.createElement("span");
		wrap.appendChild(document.createTextNode("["));
		for ( var i = 0; i < val.length; ++i) {
			if (i) {
				wrap.appendChild(document.createTextNode(", "));
				space -= 2;
			}
			var next = space > 0 && represent(val[i], space);
			var nextSize = next ? eltSize(next): 0;
			if (space - nextSize <= 0) {
				wrap.appendChild(span("etc", "…")).addEventListener("click",
						function(e) {
							expandObj(wrap, "array", val);
						});
				break;
			}
			space -= nextSize;
			wrap.appendChild(next);
		}
		wrap.appendChild(document.createTextNode("]"));
		return wrap;
	}

	function representObj(val, space) {
		var string = typeof val.toString == "function" && val.toString(), m, elt;
		if (!string || /^\[object .*\]$/.test(string))
			return representSimpleObj(val, space);
		if (val.call && (m = string.match(/^\s*(function[^(]*\([^)]*\))/)))
			return span("fun", m[1] + "{…}");
		var elt = span("etc", string);
		elt.addEventListener("click", function(e) {
			expandObj(elt, "obj", val);
		});
		return elt;
	}

	function constructorName(obj) {
		if (!obj.constructor)
			return null;
		var m = String(obj.constructor).match(/^function\s*([^\s(]+)/);
		if (m && m[1] != "Object")
			return m[1];
	}

	function hop(obj, prop) {
		return Object.prototype.hasOwnProperty.call(obj, prop);
	}

	function representSimpleObj(val, space) {
		space -= 2;
		var wrap = document.createElement("span");
		var name = constructorName(val);
		if (name) {
			space -= name.length;
			wrap.appendChild(document.createTextNode(name));
		}
		wrap.appendChild(document.createTextNode("{"));
		try {
			var first = true;
			for ( var prop in val)
				if (hop(val, prop)) {
					if (first) {
						first = false;
					} else {
						space -= 2;
						wrap.appendChild(document.createTextNode(", "));
					}
					var next = space > 0 && represent(val[prop], space);
					var nextSize = next ? prop.length + 2 + eltSize(next): 0;
					if (space - nextSize <= 0) {
						wrap.appendChild(span("etc", "…")).addEventListener(
								"click", function(e) {
									expandObj(wrap, "obj", val);
								});
						break;
					}
					space -= nextSize;
					wrap.appendChild(span("prop", prop + ": "));
					wrap.appendChild(next);
				}
		} catch (e) {
			wrap.appendChild(document.createTextNode("…"));
		}
		wrap.appendChild(document.createTextNode("}"));
		return wrap;
	}

	function expandObj(node, type, val) {
		var wrap = document.createElement("span");
		var opening = type == "array" ? "[": "{", cname;
		if (opening == "{" && (cname = constructorName(val)))
			opening = cname + " {";
		wrap.appendChild(document.createTextNode(opening));
		var block = wrap.appendChild(document.createElement("div"));
		block.className = "sandbox-output-etc-block";
		var table = block.appendChild(document.createElement("table"));
		function addProp(name) {
			var row = table.appendChild(document.createElement("tr"));
			row.appendChild(document.createElement("td")).appendChild(
					span("prop", name + ":"));
			row.appendChild(document.createElement("td")).appendChild(
					represent(val[name], 40));
		}
		if (type == "array") {
			for ( var i = 0; i < val.length; ++i)
				addProp(i);
		} else {
			for ( var prop in val)
				if (hop(val, prop))
					addProp(prop);
		}
		wrap.appendChild(document.createTextNode(type == "array" ? "]": "}"));
		node.parentNode.replaceChild(wrap, node);
	}
})();
