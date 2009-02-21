/*
 +-----------------------------------------------------------------------+
 | iui.js                                                                |
 |                                                                       |
 | This file is part of the iMobMail, the webbased eMail application     |
 | for iPod touch(R) and iPhone(R)                                       |
 | Original JS-work done by Joe Hewitt (http://www.joehewitt.com/iui/).  |
 | See http://code.google.com/p/iui/ for information about IUI.          |
 | Licensed under the GNU GPL                                            |
 | See http://www.imobmail.org/ for more details or visit our bugtracker |
 | at http://trac.imobmail.org/                                          |
 |                                                                       |    
 | Use of iMobMail at your own risk!                                     |
 |                                                                       |
 +-----------------------------------------------------------------------+

*/

var iframeid = null;
var showdelicons = false;

(function() {

	var slideSpeed = 15;
	var slideInterval = 0;

	var currentPage = null;
	var currentDialog = null;
	var currentWidth = 0;
	var currentHash = location.hash;
	var hashPrefix = "#_";
	var pageHistory = [];
	var newPageCount = 0;
	var animating = false;
	var checkTimer;


	// *************************************************************************************************

	function readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}



	function findChild (element, nodeName)
	{
		var child;

		for (child = element.firstChild; child != null; child = child.nextSibling)
		{

			if (child.nodeName == nodeName)
			return child;
		}

		return null;
	}

	window.iui =
	{
		showPage: function(page, backwards)
		{
			if (page)
			{
				if (currentDialog)
				{
					currentDialog.removeAttribute("selected");
					currentDialog = null;
				}

				if (hasClass(page, "dialog"))
				showDialog(page);
				else
				{


					var fromPage = currentPage;
					currentPage = page;

					if (!page.id)
					page.id = "__" + (++newPageCount) + "__";

					location.href = currentHash = hashPrefix + page.id;
					pageHistory.push(page.id);

					if (fromPage)
					setTimeout(slidePages, 0, fromPage, page, backwards);
					else
					updatePage(page, fromPage);

				}

			}
		},

		showPageById: function(pageId)
		{
			var page = $(pageId);
			if (page)
			{
				var index = pageHistory.indexOf(pageId);
				var backwards = index != -1;
				if (backwards)
				pageHistory.splice(index, pageHistory.length);

				iui.showPage(page, backwards);
			}
		},

		showPageByHref: function(href, args, method, replace, cb, type)
		{
			var req = new XMLHttpRequest();
			req.onerror = function()
			{
				alert("HTTP-Error!");
				if (cb)
				cb(false);
			};

			req.onreadystatechange = function()
			{
				if (req.readyState == 4)
				{
					if (replace) {
						if (type == "isid") {
							replace.innerHTML = req.responseText;
						} else
						replaceElementWithSource(replace, req.responseText);
					} else
					{
						var frag = document.createElement("div");
						frag.innerHTML = req.responseText;
						iui.insertPages(frag.childNodes);
					}
					if (cb)
					setTimeout(cb, 1000, true);
					if (type != "listupdate") setTimeout(hideURLbar, 0);
				}
			};

			if (args)
			{
				req.open(method || "GET", href, true);
				req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				req.setRequestHeader("Content-Length", args.length);
				req.send(args.join("&"));
			}
			else
			{
				req.open(method || "GET", href, true);
				req.send(null);
			}
		},

		insertPages: function(nodes)
		{
			var targetPage;
			for (var i = 0; i < nodes.length; ++i)
			{
				var child = nodes[i];
				if (child.nodeType == 1)
				{
					if (!child.id)
					child.id = "__" + (++newPageCount) + "__";

					var clone = $(child.id);
					if (clone)
					clone.parentNode.replaceChild(child, clone);
					else
					document.body.appendChild(child);

					if (child.getAttribute("selected") == "true" || !targetPage)
					targetPage = child;

					--i;
				}
			}

			if (targetPage)
			iui.showPage(targetPage);
		},

		getSelectedPage: function()
		{
			for (var child = document.body.firstChild; child; child = child.nextSibling)
			{
				if (child.nodeType == 1 && child.getAttribute("selected") == "true")
				return child;
			}
		}
	};

	// *************************************************************************************************

	addEventListener("load", function(event)
	{
		var page = iui.getSelectedPage();
		if (page)
		iui.showPage(page);
		setTimeout(hideURLbar, 0);
		setTimeout(preloadImages, 10);
		setTimeout(checkOrientAndLocation, 10);
		setInterval(checkOrientAndLocation, 300);
	}, false);

	addEventListener("click", function(event)
	{


		var link = findParent(event.target, "a");
		if (link.target == "_none") {
			event.preventDefault();
			return;
		}
		if (link)
		{
			try { if (link != $("delmark") && link.getAttribute('type') != "delmsg" ) $("delmark").style.display = "none"; } catch (e) { }

			/* try { document.cookie= "ypos"+currentPage.id+"="+window.pageYOffset+";"; } catch (e) {}*/
			function unselect() { link.removeAttribute("selected"); }

			if (link.href && link.hash && link.hash != "#")
			{
				link.setAttribute("selected", "true");
				iui.showPage($(link.hash.substr(1)));
				setTimeout(unselect, 500);
			}
			else if (link == $("delmark")) {
				showdelicons = !showdelicons;
				showDelIcons(showdelicons);
				event.preventDefault();
				return;
			}
			else if (link == $("backButton")) {
				scrollTo(1,1);
				history.back();
			}
            else if (link == $("delmsg"))
            {
                var box=window.confirm("Diese Nachricht wirklich entfernen?")

                if (box == false) { event.preventDefault();   return false; }

                iof = $('delmsg').href.indexOf('msgid=');
                msgnr = $('delmsg').href.substr(iof+6,$('delmsg').href.length-1);



                var req = new XMLHttpRequest();
                req.open("GET", $('delmsg').href, true);
                req.send(null);



                $('msg'+msgnr).style.display = "none";

                history.back();

            }
            else if (link == $("starmsg"))
            {
                iof = $('delmsg').href.indexOf('msgid=');
                msgnr = $('delmsg').href.substr(iof+6,$('delmsg').href.length-1);

                var req = new XMLHttpRequest();
                req.open("POST", $('starmsg').href, true);
                req.send(null);

            }

			else if (link.getAttribute("type") == "delmsg")
			{
				var box=window.confirm("Diese Nachricht wirklich entfernen?")

				if (box == false) { event.preventDefault();   return false; }



				iof = link.href.indexOf('msgid=');
				msgnr = link.href.substr(iof+6,link.href.length-1);



				var req = new XMLHttpRequest();
				req.open("GET", link.href, true);
				req.send(null);



				$('msg'+msgnr).style.display = "none";


			}

			else if (link.getAttribute("type") == "submit")
			{

				submitForm(findParent(link, "form"));
				if (iframeid != null) { document.getElementById(iframeid).style.display = "inline"; iframegid = null; }
				$("smto").value = "";
				$("smcc").value = "";
				$("smsubj").value = "";
				$("smtext").value = "";
				$("newmailtitle").innerHTML = "Neue eMail";	           }
				else if (link.getAttribute("type") == "cancel")
				{
					if (iframeid != null) { document.getElementById(iframeid).style.display = "inline"; iframegid = null; }
					$("smto").value = "";
					$("smcc").value = "";
					$("smsubj").value = "";
					$("smtext").value = "";
					$("newmailtitle").innerHTML = "Neue eMail";
					cancelDialog(findParent(link, "form"));
				}
				else if ((link.target == "_replace") && (link.getAttribute("type") == "listupdate"))
				{
					link.setAttribute("selected", "progress");

					iui.showPageByHref(link.href, null, null, link, unselect,"listupdate");
				}
				else if (link.target == "_replace")
				{
					link.setAttribute("selected", "progress");

					iui.showPageByHref(link.href, null, null, link, unselect);
				}
				else if (link.target == "_replaceother")
				{

					link.setAttribute("selected", "progress");

					iui.showPageByHref(link.href, null, null, $('msg'), unselect, "isid");
				}
				else if (!link.target)
				{
					link.setAttribute("selected", "progress");
					iui.showPageByHref(link.href, null, null, null, unselect);
				}
				else
				return;

				event.preventDefault();
		}
	}, true);

	addEventListener("click", function(event)
	{
		var div = findParent(event.target, "div");
		if (div && hasClass(div, "toggle"))
		{
			div.setAttribute("toggled", div.getAttribute("toggled") != "true");
			event.preventDefault();
		}
	}, true);

	function checkOrientAndLocation()
	{
		if (animating)
		return;

		if (window.innerWidth != currentWidth)
		{
			currentWidth = window.innerWidth;
			var orient = currentWidth == 320 ? "profile" : "landscape";
			document.body.setAttribute("orient", orient);
			setTimeout(scrollTo, 100, 0, 1);
		}

		if (location.hash != currentHash)
		{
			var pageId = location.hash.substr(hashPrefix.length)
			iui.showPageById(pageId);
		}
	}

	function showDialog(page)
	{
		currentDialog = page;
		page.setAttribute("selected", "true");

		if (hasClass(page, "dialog") && !page.target)
		showForm(page);
		scrollTo(1,1);
	}

	function showForm(form)
	{
		form.onsubmit = function(event)
		{
			event.preventDefault();
			submitForm(form);
		};

		form.onclick = function(event)
		{
			if (event.target == form && hasClass(form, "dialog"))
			cancelDialog(form);
		};
	}

	function cancelDialog(form)
	{
		form.removeAttribute("selected");
	}

	function updatePage(page, fromPage)
	{
		var pageTitle = $("pageTitle");
		if (page.title)
		pageTitle.innerHTML = "<a onclick=\"window.location.reload();return false;\">"+page.title+"</a>";

		if (page.localName.toLowerCase() == "form" && !page.target)
		showForm(page);

		var backButton = $("backButton");
		if (backButton)
		{
			var prevPage = $(pageHistory[pageHistory.length-2]);
			if (prevPage && !page.getAttribute("hideBackButton"))
			{
				backButton.style.display = "inline";
				backButton.innerHTML = prevPage.title ? prevPage.title : "Back";
			}
			else
			backButton.style.display = "none";
		}
		if (page.id != "msg") {
			$("rb").innerHTML = "Logout";
			$("rb").href = "logout.php";
			$("rb").style.display = "inline";
		}
		if (page.id == "folderlist") {
			try { $("delmark").style.display = "inline"; } catch (e) { }
			$("rb").style.display = "none";
		}
		animating = false;
	}


	function slidePages(fromPage, toPage, backwards)
	{
		var axis = (backwards ? fromPage : toPage).getAttribute("axis");
		if (axis == "y")
		(backwards ? fromPage : toPage).style.top = "100%";
		else
		toPage.style.left = "100%";

		toPage.setAttribute("selected", "true");
		scrollTo(0, 1);
		clearInterval(checkTimer);

		var percent = 100;
		slide();
		var timer = setInterval(slide, slideInterval);

		function slide()
		{
			percent -= slideSpeed;
			if (percent <= 0)
			{
				percent = 0;
				if (!hasClass(toPage, "dialog"))
				fromPage.removeAttribute("selected");
				clearInterval(timer);
				checkTimer = setInterval(checkOrientAndLocation, 300);
				setTimeout(updatePage, 0, toPage, fromPage);
			}

			if (axis == "y")
			{
				backwards
				? fromPage.style.top = (100-percent) + "%"
				: toPage.style.top = percent + "%";
			}
			else
			{
				fromPage.style.left = (backwards ? (100-percent) : (percent-100)) + "%";
				toPage.style.left = (backwards ? -percent : percent) + "%";
			}
		}
	}

	/*

	function slidePages(fromPage, toPage, backwards)
	{
	animating = true;

	var titlebar = findParent($("pageTitle"), "div");
	var titlebar2 = titlebar.cloneNode(true);
	titlebar2.style.position = "absolute";
	titlebar2.style.top = "0px";
	titlebar2.style.width = titlebar.offsetWidth + "px";
	titlebar2.style.height = "25px";
	titlebar2.style.left = "100%";
	titlebar2.style.padding = "10px";
	var ch = findChild(titlebar2,"H1");
	ch.innerHTML = "&nbsp;";
	$("pageTitle").innerHTML = "&nbsp;";
	document.body.appendChild(titlebar2);



	setTimeout(function()
	{
	if (backwards)
	{
	var from2 = fromPage.cloneNode(true);
	from2.style.left = "100%";
	document.body.appendChild(from2);
	scrollTo(320, 1);
	}
	if (backwards)
	{
	fromPage.style.left = "100%";
	document.body.removeChild(from2);
	}

	toPage.style.left = backwards ? "0" : "100%";

	toPage.setAttribute("selected", "true");
	scrollTo(0, 1);
	animating = true;

	var percent = 100;
	var timer = setInterval(slide, slideInterval);

	function slide()
	{
	percent -= slideSpeed;
	if (percent <= 0)
	{
	percent = 0;
	clearInterval(timer);

	if (!hasClass(toPage, "dialog"))
	fromPage.removeAttribute("selected");
	}

	var x = backwards ? (320 + (((percent-100)/100) * 320)) : ((100-percent)/100) * 320;
	scrollTo(x, 1);

	if (percent <= 0)
	{

	if (!backwards)
	{
	var page2 = toPage.cloneNode(true);
	page2.style.left = "0";
	document.body.appendChild(page2);

	setTimeout(function() {
	scrollTo(0, 1);
	toPage.style.left = "0";
	document.body.removeChild(page2);
	}, 0);
	}

	setTimeout(function() {
	document.body.removeChild(titlebar2);
	ypos = 0;
	// cookiepos = readCookie("ypos"+currentPage.id);
	//     if (cookiepos != null) { scrollTo(window.pageXOffset,cookiepos);}
	setTimeout(updatePage, 10, toPage, fromPage);
	}, 0);
	// $("msgbox").style.display = "block";


	// alert(cookiepos);

	}
	}
	}, 500);

	}
	*/
	function preloadImages()
	{
		var preloader = document.createElement("div");
		preloader.id = "preloader";
		document.body.appendChild(preloader);
	}

	function submitForm(form)
	{

		iui.showPageByHref(form.action || "POST", encodeForm(form), form.method);

	}

	function encodeForm(form)
	{
		function encode(inputs)
		{
			for (var i = 0; i < inputs.length; ++i)
			{
				if (inputs[i].name)
				args.push(inputs[i].name + "=" + escape(inputs[i].value));
			}
		}

		var args = [];
		encode(form.getElementsByTagName("input"));
		encode(form.getElementsByTagName("select"));
		encode(form.getElementsByTagName("textarea"));
		return args;
	}

	function findParent(node, localName)
	{
		while (node && (node.nodeType != 1 || node.localName.toLowerCase() != localName))
		node = node.parentNode;
		return node;
	}

	function hasClass(self, name)
	{
		var re = new RegExp("(^|\\s)"+name+"($|\\s)");
		return re.exec(self.getAttribute("class")) != null;
	}



	function replaceElementWithSource(replace, source)
	{
		var page = replace.parentNode;
		var parent = replace;

		while (page.parentNode != document.body)
		{
			page = page.parentNode;
			parent = parent.parentNode;

		}

		var frag = document.createElement(parent.localName);

		frag.innerHTML = source;



		while (frag.firstChild)
		page.insertBefore(frag.firstChild, parent);

		page.removeChild(parent);
	}

	function $(id) { return document.getElementById(id); }
	function ddd() { console.log.apply(console, arguments); }

	function hideURLbar() {

		window.scrollTo(0, 1);
	}

})();

function removeElement(id) {
	retval = false;
	try {  var Node = document.getElementById(id);
	Node.parentNode.removeChild(Node); retval = true; } catch (e) { retval = false;}
	return retval;
}

function removeOldListentries(maxele,start,folder,offset) {

	removeElement("prevLoader");
	showDelIcons(false);
	showdelicons = false;
	counter = 0;


	for (var i = parseInt(maxele); i >= parseInt(start)+25;i--) {
		if (removeElement('msg'+i)) counter=counter+1;
	}

	if (counter>0) {

		var neuLi = document.createElement("li");
		neuLi.setAttribute("id", "prevLoader");

		neuLi.innerHTML = "<a onclick=\"removeOldnextentries('"+folder+"','"+(parseInt(start))+"');\"  href=\"folderlist.php?prev&folder="+folder+"&offset="+(parseInt(offset)+25)+"\" target=\"_replace\" type=\"listupdate\">25 vorige Nachrichten laden...</a>";
		document.getElementById("folderlist").insertBefore(neuLi,document.getElementById("folderlist").firstChild);


	}

}



function showDelIcons(show) {
	if (show) {
		numb = document.getElementsByName('msglistentry').length;
		for (i=0;i<numb;i++) document.getElementsByName('msglistentry')[i].style.display = "block";
		numb = document.getElementsByName('msgli').length;
		for (i=0;i<numb;i++) document.getElementsByName('msgli')[i].style.paddingLeft = "25px";
		document.getElementById("delmark").innerHTML = "Fertig";
	} else {
		numb = document.getElementsByName('msglistentry').length;
		for (i=0;i<numb;i++) document.getElementsByName('msglistentry')[i].style.display = "none";
		numb = document.getElementsByName('msgli').length;
		for (i=0;i<numb;i++) document.getElementsByName('msgli')[i].style.paddingLeft = "10px";
		document.getElementById("delmark").innerHTML = "Edit";
	}
}


function removeOldnextentries(folder,offset) {
	showDelIcons(false);
	showdelicons = false;
	removeElement("nextLoader");

	counter = 0;

	for (var i = parseInt(offset); i >= (parseInt(offset)-50);i--) {
		if (removeElement('msg'+i)) counter=counter+1;
	}

	if (counter>0) {

		var neuLi = document.createElement("li");
		neuLi.setAttribute("id", "nextLoader");

		neuLi.innerHTML = "<a href=\"folderlist.php?folder="+folder+"&offset="+(parseInt(offset)+26)+"\" target=\"_replace\" type=\"listupdate\">25 weitere Nachrichten laden...</a>";
		document.getElementById("folderlist").appendChild(neuLi);


	}

}


function sendFwd(iframe,subject,from) {
	if (iframe != "") { iframeid = iframe;
	document.getElementById(iframeid).style.display = "none";
	}

	document.getElementById('newmailtitle').innerHTML = "Forward";
	document.getElementById('smsubj').value = 'Fwd: ['+subject+']';


	msg = document.getElementById('msgbox').innerHTML.replace(/<\/?[^>]+>/gi, '');
	msg = msg.replace(/&gt;/g,"> ");
	msg = msg.replace(/\n/g,"\n> ");
document.getElementById('smtext').value = "\n\n"+from+" wrote:"+msg;

}

function sendReply(iframe,subject,to,cc) {

if (iframe != "") { iframeid = iframe;
document.getElementById(iframeid).style.display = "none"; 
}
if (!cc) document.getElementById('newmailtitle').innerHTML = "Reply"; else document.getElementById('newmailtitle').innerHTML = "Reply all"; 
	
document.getElementById('smsubj').value = 'Re: '+subject;
document.getElementById('smto').value = to;
if (cc) document.getElementById('smcc').value = cc;

msg = document.getElementById('msgbox').innerHTML.replace(/<\/?[^>]+>/gi, '');
msg = msg.replace(/&gt;/g,"> ");
msg = msg.replace(/\n/g,"\n> ");
document.getElementById('smtext').value = "\n\n"+to+" wrote:"+msg;

}

function SetCookie (name, value) {
  // Enter number of days the cookie should persist
  var expDays = 365;
  var exp = new Date(); 
  exp.setTime(exp.getTime() + (expDays*24*60*60*1000));
  expirationDate = exp.toGMTString();
  // Set cookie with name and value provided
  // in function call and date from above
  document.cookie = name + "=" + escape(value) + "; expires=" + expirationDate;
 
}