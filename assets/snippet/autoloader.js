(function(){
  // For ksuwiki plugin 

  // Get absolute path to this script
  var path = window.location.pathname; 
  path = path.replace(/site\/.*/, '').replace(/[^/]+$/, ''); 
  var prefix = path +"assets/snippet/";         
  // For local test 
  //var prefix = "";

  var load_js = function (file) {
    var script = prefix + "js/" + file;
	  document.write('<script type="text/javascript" src="'+ script +'"></script>');
  }
  var load_css = function (file) {
    var css =  prefix + "css/" + file;
    document.write('<link rel="stylesheet" href="' + css + '">');
  }

  load_css("snippet.css");
  load_js( "jquery/jquery.min.js"); 
  load_js( "jquery/jquery-ui.min.js"); 
  load_js( "ace/ace.js"); 
  load_js( "acorn/acorn.js");     // javascript preprocessor
  load_js( "acorn/util/walk.js"); // dom walk through
  load_js( "sandbox.js");
  load_js( "snippet.js");
})();
