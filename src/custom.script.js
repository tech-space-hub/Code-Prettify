(function () { function htmlEscape(s) { return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');}
  var quineHtml = htmlEscape(document.getElementById("quine").innerHTML);  
  quineHtml = quineHtml.replace(/&lt;script src[\s\S]*?&gt;&lt;\/script&gt;|&lt;!--\?[\s\S]*?--&gt;|&lt;pre\b[\s\S]*?&lt;\/pre&gt;/g,'<span class="operative">$&<\/span>');
  document.getElementById("quine").innerHTML = quineHtml;
})();