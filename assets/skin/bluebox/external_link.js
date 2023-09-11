function external_link(){
   var host_Name = location.host;
   var host_Check;
   var link_Href;

   for(var i=0; i < document.links.length; ++i)
   {
       link_Href = document.links[i].host;
       host_Check = link_Href.indexOf(host_Name,0);

       if(host_Check == -1){
           document.links[i].innerHTML = document.links[i].innerHTML + "<img src=\"external_link.gif\" height=\"11px\" width=\"11px\" alt=\"[紊・・・・・・潟・・\" class=\"external_link\">";
       }

   }
}
window.onload = external_link;
