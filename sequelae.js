// [OLD]  Manually collapse and expand the left nav column with a floating button that remembers its state:

/*
var lil, big;

function navvy(evt)
{
    evt = evt || window.event;
    var hide = big.style.display == "block";
    lil.style.display = big.style.display;
    big.style.display = hide ? "none" : "block";
    own.className = hide ? "indexx collapsate" : "indexx";
    document.cookie = "cjhidenav=" + (hide ? "1" : "0");
    if (evt)
    	if (evt.stopPropagation)
            evt.stopPropagation();
        else
            e.preventDefault = true;
}

function navvyOneWay(evt)
{
    if (lil.style.display == "block")
        navvy(evt);
}

function makeCookieGetter(name)
{
    var r = new RegExp("(^|\\W)" + name + "=((\\w*)|\"([^\"]*)\")($|;)");
    r.get = function ()
            {
                var matches = this.exec(document.cookie);
                return !matches || !matches.length ? null :
                       decodeURIComponent((matches[4] || matches[3] || "").replace(/\+/g, ' '));
            };
    return r;
}

var navHiderCookieGetter = makeCookieGetter("cjhidenav");

function initNavvy()
{
    lil = document.getElementById('collapsed');
    big = document.getElementById('expanded');
    own = document.getElementById('collapsar');
    if (navHiderCookieGetter.get() == "1")
        navvy();
}
*/

// COMMON MENU INFRASTRUCTURE:

var menus = [];

function linkMenuToPlopup(openerId, plopupId)
{
    // if you ever use Ajax with pages that use differing IDs, you might want to add some cleanup for deadwood in menus
    var opener = document.getElementById(openerId);
    var plopup = document.getElementById(plopupId);
    if (opener && plopup)
    {
        menus[opener.id] = plopup;
        // traditional single event handlers are universal and sufficient for our needs:
        opener.onclick = menuHandler;
    }
}

function menuHandler(e)
{
    e = e || window.event;
    for (var m in menus)
    {
        if (this.id == m && menus[m].style.display == "none")
            menus[m].style.display = "block";
            // TODO: add a selected class to the trigger element?  (and strip it from inactive ones)
        else
            menus[m].style.display = "none";
            // TODO: consider checking for nested menus and avoid hiding the parent?
        if (this.id == m)
            if (e.stopPropagation)
                e.stopPropagation();
            else
                e.preventDefault = true;
    }
    return true;
}



// THIS SECTION IS FOR THE BACK-AND-FORTH NAVIGATION PER INDIVIDUAL MOVIE -- optional left and right menus:
// (not used in index/supplement pages)

function navigatize(e)
{
    // XXX POSSIBLE BUG?  Isn't "this" the A element itself in some old browsers?
    var ourLink = this.getElementsByTagName("a")[0].href;
    // ...I was going to use SPARE here, but the benefit is so minimal it's not worth the test burden.
    window.location.href = ourLink;
}

function initSequelBar()        // if you do any ajaxing that includes these menus, call initSequelBar again afterwards
{
    linkMenuToPlopup("leftmenu", "leftplop");
    linkMenuToPlopup("rightmenu", "rightplop");

    // We're putting links on images with transparent parts; try to make the clickable area match
    // as well as is practical.  This doesn't work on old browsers such as IE 7.  On those, you
    // have to click the movie title text itself; the rest of the arrow won't work.
    if (document.querySelectorAll)
    {
        // On IE 8 - 10, the handler has to be applied to the whole image area, including
        // the transparent part; for modern browsers it's applied to a smaller area.
        // The only way to fix that may be by using an old-fashioned image map.
        var ieCompromise = navigator.appVersion.indexOf("MSIE") >= 0;   // no longer present in IE 11
        var navElements = document.querySelectorAll("div.movienameL, div.movienameR");
        for (var i = 0; i < navElements.length; i++)
        {
            var element = ieCompromise ? navElements[i].parentNode : navElements[i];
            element.onclick = navigatize;   // I guess IE 10 can't put a click event on something absolutely positioned
        }
        // IE 10 also can't style those absolute-positioned divs with cursor pointers, so apply it to the image.
        if (ieCompromise)
            document.styleSheets[0].insertRule(".movienameL + IMG, .movienameR + IMG { cursor: pointer; }", document.styleSheets[0].rules.length);
    }
}


// STARTUP

function initAll()
{
    //initNavvy();
    initSequelBar();
    linkMenuToPlopup("openindex", "indexmenu");
    linkMenuToPlopup("openintro", "intromenu");
    document.body.onclick = menuHandler;           // hides all menus when clicking background
}

if (window.addEventListener)
    window.addEventListener("DOMContentLoaded", initAll, false);    // one case where a modern event handler is definitely preferable
else
    window.onload = initAll;                                        // old browsers, including IE 8

