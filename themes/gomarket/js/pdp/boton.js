var slidesObjects_xxx = {width:230, height:40, fromBorder:true, mouseoverPause:"true", autoplay:"true", preloader:"false",progressBar:"false",bullets:"false",nextPrevious:"false",playPause: "false",wmXDistance: 8, wmYDistance: 8, wmLocation: "br", wmURL: "", wmImg: "", wmAlt: "", wmark: "true",totalTime:7, loop:true, goToUrl:'', target:'_blank', 
slides:[{items:[
{id:"image0", url:"", target:"_blank",
     rotation:0,w:230,h:40,src:"fondo.png", rotationRect:{left:0, top:0}, rect:{left:0, top:0, right:200, bottom:35}, 
      appear:{effects:["effect_item_none","effect_item_opacity"], custom_x:0, custom_y:0, x:0, y:0, delay:0, easing:Quad.easeOut, duration:1}
      ,disappear:{effects:["effect_item_none","effect_item_opacity"],custom_x:0, custom_y:0, x:0, y:0, delay:1, easing:Quad.easeOut, duration:1}}, 
{id:"image1", url:"", target:"_blank",
     rotation:0,w:230,h:40,src:"here.png", rotationRect:{left:0, top:0}, rect:{left:0, top:0, right:170, bottom:28}, 
      appear:{effects:["effect_item_scale"], custom_x:0, custom_y:0, x:0, y:0, delay:0, easing:Quad.easeOut, duration:1}
      ,disappear:{effects:["effect_item_none","effect_item_opacity"],custom_x:0, custom_y:0, x:14, y:4, delay:1, easing:Quad.easeOut, duration:1}}],delay:0, bullet:"bullet0", slideDuration:3, slideStopPoint:1.5},{items:[
{id:"image2", url:"", target:"_blank",
     rotation:0,w:230,h:40,src:"fondo.png", rotationRect:{left:0, top:0}, rect:{left:0, top:0, right:200, bottom:35}, 
      appear:{effects:["effect_item_none","effect_item_opacity"], custom_x:0, custom_y:0, x:0, y:0, delay:0, easing:Quad.easeOut, duration:1}
      ,disappear:{effects:["effect_item_none","effect_item_opacity"],custom_x:0, custom_y:0, x:0, y:0, delay:2, easing:Quad.easeOut, duration:1}}, 
{id:"image3", url:"", target:"_blank",
     rotation:0,w:230,h:40,src:"discount.png", rotationRect:{left:0, top:0}, rect:{left:0, top:0, right:127, bottom:28}, 
      appear:{effects:["effect_item_scale"], custom_x:0, custom_y:0, x:0, y:0, delay:0, easing:Quad.easeOut, duration:1}
      ,disappear:{effects:["effect_item_none","effect_item_opacity"],custom_x:0, custom_y:0, x:36, y:3, delay:2, easing:Quad.easeOut, duration:1}}],delay:0, bullet:"bullet1", slideDuration:4, slideStopPoint:2}]},id="html5", ratio = 1;

var $status = document.getElementById("statusbar"); // progress bar
var $statusHolder = document.getElementById("statusbar_wrapper"); // progress bar
var $main = document.getElementById("globalDiv");
var $next = null, $prev = null;
var $play = null, $pause = null;
var $wm = null;
var $bulletsObjects = [];

var currentSlide; // slide being played
var bufferedSlide;
var timeLines = []; // container for all slides
var bulletsMap = {};
var bulletsArray = [];
var paused = [];
var spinner; // preloader progress
var gotoAndStop;
var gotoAndStopFlag;
var playStatus; // "paused" or "playing"
var isAnimation;
var borderShift = 0;
var controlsInitialized = false;

addPreloader();
addListener(window, "load", onWindowResize);
addListener(window, "resize", onWindowResize);

function addPreloader() {
    if (!slidesObjects_xxx.preloader || slidesObjects_xxx.preloader == "false") {
        return;
    }

    var opts = {
        lines: 9,
        length: 4,
        width: 7,
        radius: 13,
        corners: 1,
        rotate: 0,
        direction: 1,
        color: "#000",
        speed: 1.6,
        trail: 51,
        shadow: false,
        hwaccel: false,
        className: "spinner",
        zIndex: 2e9,
        top: "auto",
        left: "auto"
    };
    var target = document.getElementById("globalDiv");
    spinner = new Spinner(opts).spin(target);
}

function hidePreloader() {
    if (!slidesObjects_xxx.preloader || slidesObjects_xxx.preloader == "false") {
        return;
    }
    spinner.stop();
}

function onReady() {
    var gURL = slidesObjects_xxx.goToUrl;
    bufferedSlide = -1;
    timeLines = [];
    bulletsMap = {};
    bulletsArray = [];
    gotoAndStop = 0;
    gotoAndStopFlag = 0;
    isAnimation = false;

    for (var slideIndex in slidesObjects_xxx.slides) {
        var slide = slidesObjects_xxx.slides[ slideIndex];
        var progressMultiplier = (slide.delay > 0) ? slide.slideDuration / (slide.slideDuration - slide.delay) : 1;
        var slideTimeline;

        if (slidesObjects_xxx.progressBar && slidesObjects_xxx.progressBar == "true") {
            slideTimeline = new TimelineLite({onUpdate: onUpdateTimeline, onUpdateParams: ["{self}", $status, progressMultiplier], paused: true});
        } else {
            slideTimeline = new TimelineLite({paused: true});
        }

        for (var itemIndex in slide.items) {
            var item = slide.items[ itemIndex];
            var $obj = document.getElementById(item.id);
            var totalW = slidesObjects_xxx.width, totalH = slidesObjects_xxx.height;
            var t = new TimelineLite(); // local timeline (contains item"s effects)
            var rotationObj = {rotation: item.rotation, rotationRect: item.rotationRect, rect: item.rect};

            resizeObject($obj, item.w, item.h);

            cleanUpOpacity($obj);
            setUpImageOpacity($obj, item);
            t.add(createInit($obj, item.appear, rotationObj, totalW, totalH, item.w, item.h));
            t.add(createAppear($obj, item.appear, rotationObj));
            t.add(createDisappear($obj, item.disappear, rotationObj, totalW, totalH, item.w, item.h))
                    .to($obj, 0, {"visibility": "hidden"});
            slideTimeline.add(t, "0");

            if (item.url != "") {
                initGoToUrl($obj, item.url, item.target);
            } else if (item.gotoAndPlaySlide && item.gotoAndPlaySlide != "") {
                initGotoAndPlayUrl($obj, item.gotoAndPlaySlide, item.immediate);
            } else if (item.gotoAndStopSlide && item.gotoAndStopSlide != "") {
                initGotoAndStopUrl($obj, item.gotoAndStopSlide, item.immediate);
            }
        }

        if (slide.slideDuration > slideTimeline.duration()) {
            var d = "+=" + (slide.slideDuration - slideTimeline.duration());
            slideTimeline.call(endSlideStub, null, this, d);
        }
        slideTimeline.add("end_slide");

        if (slide.delay > 0) {
            // Slide"s overlap (crosssliding)
            var pos = "-=" + slide.delay;
            slideTimeline.call(overlapSlides, null, this, pos);
        } else {
            slideTimeline.eventCallback("onComplete", onSlideComplete);
        }
        if (slide.slideStopPoint > 0) {
            var _stopPoint = slide.slideStopPoint;
            slideTimeline.call(checkForGoToAndStop, null, slideTimeline, _stopPoint);
        }
        if (slidesObjects_xxx.bullets && slidesObjects_xxx.bullets == "true") {
            // generate a map of bullets
            // Matches bullet"s id to the slide number/index
            bulletsMap[slide.bullet] = slideIndex;
            bulletsArray[ slideIndex] = slide.bullet;
        }
        if (slidesObjects_xxx.wmark && slidesObjects_xxx.wmark == "true") {
            addWatermark();
        }
        timeLines[ slideIndex] = slideTimeline;
    }

    disableContext();
    currentSlide = 0;

    if (slidesObjects_xxx.bullets && slidesObjects_xxx.bullets == "true") {
        addBullets();
        updateActiveBullet();

    }
    if (slidesObjects_xxx.mouseoverPause && slidesObjects_xxx.mouseoverPause == "true") {
        addMouseoverPause();
    }
    if (slidesObjects_xxx.nextPrevious && slidesObjects_xxx.nextPrevious == "true") {
        addNextPrevious();
    }
    if (slidesObjects_xxx.playPause && slidesObjects_xxx.playPause == "true") {
        addPlayPause();
        updatePlayPause("playing");
    }

    addGlobalURL();
    if (slidesObjects_xxx.progressBar == "true") {
    	readjustProgress();
    }
    hidePreloader();
    onHideNextPrev();
    isAnimation = true;
    controlsInitialized = true;
    onRestart();
}

function endSlideStub() {
}

/**
 * Set up image opacity
 */
function setUpImageOpacity($obj, item) {
    if (item.opacity) {
        var opacityStyle = getTransparentImageClass(item.opacity);
        var $img = $obj.getElementsByTagName("img")[ 0];
        var style = $img.getAttribute("style");

        style += " " + opacityStyle;
        $img.setAttribute("style", style);
    }
}

/**
 * Cleans up opacity and Scale from previous animations (when resized)
 */
function cleanUpOpacity($obj) {
    TweenLite.to($obj, 0, {alpha: 1, scaleX: 1, scaleY: 1});
}

function readjustProgress() {
    $status.style.backgroundColor = slidesObjects_xxx.progressBarColor;
    $status.style.opacity = slidesObjects_xxx.progressBarOp;
    $status.style.position = "absolute";
    $status.style.height = slidesObjects_xxx.progressBarSize * ratio + "px";
    $status.style.top = "0px";
    $status.style.width = "0px";
    $status.style.zIndex = "1001";

    $statusHolder.style.backgroundColor = slidesObjects_xxx.progressBarBg;
    $statusHolder.style.opacity = slidesObjects_xxx.progressBarOp;
    $statusHolder.style.position = "absolute";
    $statusHolder.style.height = slidesObjects_xxx.progressBarSize * ratio + "px";
    $statusHolder.style.top = slidesObjects_xxx.progressBarY * ratio + "px";
    $statusHolder.style.width = "200px";
    $statusHolder.style.zIndex = "1000";
}

function addGlobalURL() {
    var gURL = slidesObjects_xxx.goToUrl;
    var gTarget = slidesObjects_xxx.target;
    if (gURL != "") {
        initGoToUrl(document.getElementById("mainDiv"), gURL, gTarget);
    }
}

// Adding bullets/pagination support
function addBullets() {
    var $bulletContainer = document.getElementById("bullets");

    relocateObject($bulletContainer, slidesObjects_xxx.bulletsLeft, slidesObjects_xxx.bulletsTop);
    for (var bulletsIndex in bulletsArray) {
        var bulletId = bulletsArray[ bulletsIndex];
        var alt = "Slide #" + bulletsIndex;
        var $theBullet = null;

        if (controlsInitialized == false) {
            $theBullet = insertBullet("bullets", bulletId, slidesObjects_xxx.bulletImg, "Play", "bullet_1234");
        } else {
            $theBullet = document.getElementById(bulletId);
        }
        $bulletsObjects[ bulletsIndex] = $theBullet;
        $theBullet.onclick = function () {
            onBullet(this);
        }
        $theBullet.onmouseover = function () {
            onFocusBullet(this);
        }
        $theBullet.onmouseout = function () {
            onUnfocusBullet(this);
        }
        resizeBullet($theBullet);
    }
}

function onFocusBullet($theBullet) {
    var $currentBullet = $bulletsObjects[ getCurrentSlide()];

    if ($currentBullet.id == $theBullet.id) {
        getInnerImg($theBullet).src = slidesObjects_xxx.focusBulletActiveImg;
    } else {
        getInnerImg($theBullet).src = slidesObjects_xxx.focusBulletImg;
    }
}

function onUnfocusBullet($theBullet) {
    updateActiveBullet();
}

function updateActiveBullet() {

    for (var bulletsIndex in $bulletsObjects) {
        var $theBullet = $bulletsObjects[ bulletsIndex];

        if (bulletsIndex == getCurrentSlide()) {
            getInnerImg($theBullet).src = slidesObjects_xxx.bulletActiveImg;
            $theBullet.style.opacity = 1;
        } else {
            getInnerImg($theBullet).src = slidesObjects_xxx.bulletImg;
            $theBullet.style.opacity = slidesObjects_xxx.bulletsOp;
        }
    }
}

/**
 * Adding mouse over animation pause functionality
 */
function addMouseoverPause() {
    addListener($main, "mouseover", onPause);
    addListener($main, "mouseout", onResume);
}

/**
 * Adding Play/Pause functionality
 */
function addPlayPause() {
    if ($play == null) {
        $play = insertButton("mainDiv", "play", slidesObjects_xxx.playImg, "Play");
    }
    $play.onmouseover = function () {
        setButtonImage(this, slidesObjects_xxx.playImgActive);
        $play.style.opacity = 1;
    }
    $play.onmouseout = function () {
        setButtonImage(this, slidesObjects_xxx.playImg);
        $play.style.opacity = slidesObjects_xxx.playPauseOp;
    }
    addListener($play, "click", onPlayClicked);
    resizeObject($play, slidesObjects_xxx.playPauseWidth, slidesObjects_xxx.playPauseHeight);

    if ($pause == null) {
        $pause = insertButton("mainDiv", "pause", slidesObjects_xxx.pauseImg, "Pause");
    }
    $pause.onmouseover = function () {
        setButtonImage(this, slidesObjects_xxx.pauseImgActive);
        $pause.style.opacity = 1;
    }
    $pause.onmouseout = function () {
        setButtonImage(this, slidesObjects_xxx.pauseImg);
        $pause.style.opacity = slidesObjects_xxx.playPauseOp;
    }
    addListener($pause, "click", onPauseClicked);
    resizeObject($pause, slidesObjects_xxx.playPauseWidth, slidesObjects_xxx.playPauseHeight);

    relocatePlayPause();
}

function relocatePlayPause() {
    $play.style.position = "absolute";
    $pause.style.position = "absolute";
    if (slidesObjects_xxx.playPauseLocation == "br") {
        $play.style.bottom = slidesObjects_xxx.playPauseYDistance * ratio + "px";
        $play.style.right = (slidesObjects_xxx.playPauseXDistance - borderShift) * ratio + "px";
        $pause.style.bottom = slidesObjects_xxx.playPauseYDistance * ratio + "px";
        $pause.style.right = (slidesObjects_xxx.playPauseXDistance - borderShift) * ratio + "px";
    }
    if (slidesObjects_xxx.playPauseLocation == "bl") {
        $play.style.bottom = slidesObjects_xxx.playPauseYDistance * ratio + "px";
        $play.style.left = slidesObjects_xxx.playPauseXDistance * ratio + "px";
        $pause.style.bottom = slidesObjects_xxx.playPauseYDistance * ratio + "px";
        $pause.style.left = slidesObjects_xxx.playPauseXDistance * ratio + "px";
    }
    if (slidesObjects_xxx.playPauseLocation == "tr") {
        $play.style.top = slidesObjects_xxx.playPauseYDistance * ratio + "px";
        $play.style.right = slidesObjects_xxx.playPauseXDistance * ratio + "px";
        $pause.style.top = slidesObjects_xxx.playPauseYDistance * ratio + "px";
        $pause.style.right = slidesObjects_xxx.playPauseXDistance * ratio + "px";
    }
    if (slidesObjects_xxx.playPauseLocation == "tl") {
        $play.style.top = slidesObjects_xxx.playPauseYDistance * ratio + "px";
        $play.style.left = slidesObjects_xxx.playPauseXDistance * ratio + "px";
        $pause.style.top = slidesObjects_xxx.playPauseYDistance * ratio + "px";
        $pause.style.left = slidesObjects_xxx.playPauseXDistance * ratio + "px";
    }
}

function onPlayClicked() {
    onResume();
}

function onPauseClicked() {
    onPause();
}

/**
 * Function that"s responsible for current playing control (play/pause)
 */
function updatePlayPause(status) {
    if (slidesObjects_xxx.playPause == "false") return;

    playStatus = status;
    if (playStatus == "playing") {
        $play.style.visibility = "hidden";
        $play.style.zIndex = -10000;
        $pause.style.visibility = "";
        $pause.style.opacity = slidesObjects_xxx.playPauseOp;
        $pause.style.zIndex = 10000;
    } else if (playStatus == "paused") {
        $pause.style.visibility = "hidden";
        $pause.style.zIndex = -10000;
        $play.style.visibility = "";
        $play.style.opacity = slidesObjects_xxx.playPauseOp;
        $play.style.zIndex = 10000;
    }
}

function addWatermark() {
    if ($wm == null) {
        $wm = insertButton("mainDiv", "", slidesObjects_xxx.wmImg, slidesObjects_xxx.wmAlt);
    }
    addListener($wm, "click", onWmClicked);
    resizeObject($wm, slidesObjects_xxx.wmWidth, slidesObjects_xxx.wmHeight);
    relocateWM();
}

function onWmClicked() {
    window.open(slidesObjects_xxx.wmURL, "_blank");
}

function relocateWM() {
    $wm.style.position = "absolute";
    if (slidesObjects_xxx.wmLocation == "br") {
        $wm.style.bottom = slidesObjects_xxx.wmYDistance * ratio + "px";
        $wm.style.right = (slidesObjects_xxx.wmXDistance - borderShift) * ratio + "px";
    }
    if (slidesObjects_xxx.wmLocation == "bl") {
        $wm.style.bottom = slidesObjects_xxx.wmYDistance * ratio + "px";
        $wm.style.left = slidesObjects_xxx.wmXDistance * ratio + "px";
    }
    if (slidesObjects_xxx.wmLocation == "tr") {
        $wm.style.top = slidesObjects_xxx.wmYDistance * ratio + "px";
        $wm.style.right = slidesObjects_xxx.wmXDistance * ratio + "px";
    }
    if (slidesObjects_xxx.wmLocation == "tl") {
        $wm.style.top = slidesObjects_xxx.wmYDistance * ratio + "px";
        $wm.style.left = slidesObjects_xxx.wmXDistance * ratio + "px";
    }
}

/**
 * Adding Next/Previous slide functionality
 */
function addNextPrevious() {
    if ($next == null) {
        $next = insertButton("mainDiv", "btnNext", slidesObjects_xxx.nextImg, "Next > ");
    }
    $next.onmouseover = function () {
        setButtonImage(this, slidesObjects_xxx.nextImgActive);
        onFocusNextPrev();
        onShowNextPrev();
    }
    $next.onmouseout = function () {
        setButtonImage(this, slidesObjects_xxx.nextImg);
        onUnfocusNextPrev();
    }
    addListener($next, "click", onNextClicked);
    resizeObject($next, slidesObjects_xxx.nextPrevWidth, slidesObjects_xxx.nextPrevHeight);

    if ($prev == null) {
        $prev = insertButton("mainDiv", "btnPrev", slidesObjects_xxx.prevImg, "< Previous");
    }
    $prev.onmouseover = function () {
        setButtonImage(this, slidesObjects_xxx.prevImgActive);
        onFocusNextPrev();
    }
    $prev.onmouseout = function () {
        setButtonImage(this, slidesObjects_xxx.prevImg);
        onUnfocusNextPrev();
        onShowNextPrev();
    }
    addListener($prev, "click", onPreviousClicked);
    resizeObject($prev, slidesObjects_xxx.nextPrevWidth, slidesObjects_xxx.nextPrevHeight);

    relocateNextPrev();

    addListener($main, "mouseover", onShowNextPrev);
    addListener($main, "mouseout", onHideNextPrev);
}

function relocateNextPrev() {
    $next.style.position = "absolute";
    $prev.style.position = "absolute";
    $prev.style.top = slidesObjects_xxx.prevYDistance * ratio + "px";
    $prev.style.left = (slidesObjects_xxx.prevXDistance - borderShift) * ratio + "px";
    $next.style.top = slidesObjects_xxx.nextYDistance * ratio + "px";
    $next.style.right = (slidesObjects_xxx.nextXDistance - borderShift) * ratio + "px";
}

var nextPrevFocusFlag = 0;

/**
 * Event that occurs when a mouse hovers over the scene and it triggers Next/Previous controls to fade in
 */
function onShowNextPrev() {
    if (nextPrevFocusFlag == 1) {
        TweenLite.to([$next, $prev], 1, {alpha: 1});
    } else {
        TweenLite.to([$next, $prev], 1, {alpha: slidesObjects_xxx.nextPrevOpacity});
    }
}

/**
 * Event that occurs when a mouse leaves the scene and it triggers Next/Previous controls to hide
 */
function onHideNextPrev() {
    TweenLite.to([$next, $prev], 1, {alpha: 0});
}

function onFocusNextPrev() {
    nextPrevFocusFlag = 1;
}

function onUnfocusNextPrev() {
    nextPrevFocusFlag = 0;
}

/**
 * Creates DOM button-like object
 */
function insertButton(pId, bId, imgSrc, bAlt) {
    var $div = document.createElement("div");
    var $img = document.createElement("img");

    $img.setAttribute("id", bId);
    $img.setAttribute("src", imgSrc);
    $img.setAttribute("alt", bAlt);
    $div.appendChild($img);
    $div.style.cursor = "pointer";
    document.getElementById(pId).appendChild($div);

    return $div;
}

function setButtonImage($button, imgSrc) {
    $button.children[ 0].src = imgSrc;
}

function insertBullet(parent, bId, src, bAlt, cssclass) {
    var $div = document.createElement("div");
    var $img = document.createElement("img");

    $div.setAttribute("id", bId);
    $img.setAttribute("src", src);
    $img.setAttribute("alt", bAlt);
    if (cssclass != "") {
        $div.setAttribute("class", cssclass);
    }
    $div.appendChild($img);
    $div.style.cursor = "pointer";
    $div.style.opacity = slidesObjects_xxx.bulletsOp;
    document.getElementById(parent).appendChild($div);

    return $div;
}

function getInnerImg($div) {
    return $div.children[ 0];
}

/**
 * Retrieves 1-based index of the next slide.
 */
function getNextSlide() {
    if (currentSlide == slidesObjects_xxx.slides.length - 1) {
        return 0;
    } else {
        return currentSlide + 1;
    }
}

/**
 * Retrieves 1-based index of the previous slide.
 */
function getPrevSlide() {
    if (currentSlide == 0) {
        return slidesObjects_xxx.slides.length - 1;
    } else {
        return currentSlide - 1;
    }
}

/**
 * Increment the count of the current slide.
 */
function doIncSlide() {
    if (currentSlide == slidesObjects_xxx.slides.length - 1) {
        currentSlide = 0;
    } else {
        ++currentSlide;
    }
}

/**
 * Reduces the count of the current slide.
 */
function doDecSlide() {
    if (currentSlide == 0) {
        currentSlide = slidesObjects_xxx.slides.length - 1;
    } else {
        --currentSlide;
    }
}

function getCurrentSlide() {
    return currentSlide;
}

function setNextSlide(nextSlide) {
    currentSlide = nextSlide;
}

// Init animation
function createInit(obj, event, rotationObj, totalW, totalH, w, h) {
    var effects = {}, o = 0, m = 0, _x, _y;
    var rotation = parseFloat(rotationObj.rotation);

    for (var effectIndex in event.effects) {
        var effect = event.effects[ effectIndex];

        if (effect == "effect_item_opacity" || effect == "effect_item_scale") {
            o = 1;
        }
        if (effect == "effect_item_scale") {
            effects["scaleX"] = 0;
            effects["scaleY"] = 0;
        }
        if (effect == "effect_item_none") {
            _x = event.x;
            _y = event.y;
            m = 1;
        }
        if (effect == "effect_item_top") {
            _x = event.x;
            if (rotation !== 0) {
                _y = -rotationObj.rect.bottom - 1;
            } else {
                _y = -h;
            }
            m = 1;
        }
        if (effect == "effect_item_bottom") {
            _x = event.x;
            if (rotation !== 0) {
                _y = totalH - rotationObj.rect.top + 1;
            } else {
                _y = totalH;
            }
            m = 1;
        }
        if (effect == "effect_item_right") {
            if (rotation !== 0) {
                _x = -rotationObj.rect.left + totalW + 1 + borderShift;
            } else {
                _x = totalW + borderShift + 1;
            }
            _y = event.y;
            m = 1;
        }
        if (effect == "effect_item_left") {
            if (rotation !== 0) {
                _x = -rotationObj.rect.right - 1 - borderShift;
            } else {
                _x = -w - borderShift;
            }
            _y = event.y;
            m = 1;
        }
        if (effect == "effect_item_custom") {
            _x = event.custom_x;
            _y = event.custom_y;
            m = 1;
        }
    }
    if (o == 1 && m == 0) {
        // there is opacity/scale effect without move effect
        _x = event.x;
        _y = event.y;
    }
    if (rotation !== 0) {
        effects["rotation"] = rotation;
        if (m == 1) {
            _x = _x - rotationObj.rotationRect.left + rotationObj.rect.left;
            _y = _y - rotationObj.rotationRect.top + rotationObj.rect.top;
        }
    }
    effects["x"] = _x * ratio;
    effects["y"] = _y * ratio;
    
    return TweenLite.to(obj, 0, effects);
}

function createAppear(obj, event, rotationObj) {
    var effects = {};

    for (var effectIndex in event.effects) {
        var effect = event.effects[ effectIndex];

        if (effect == "effect_item_opacity") {
            // Opacity Effect
            effects["autoAlpha"] = 1;
        }
        if (effect == "effect_item_scale") {
            // scale effect
            effects["visibility"] = "visible";
            effects["scaleX"] = 1;
            effects["scaleY"] = 1;
        }
        if (effect == "effect_item_top" || effect == "effect_item_right" || effect == "effect_item_bottom"
                || effect == "effect_item_left" || effect == "effect_item_custom"
                || effect == "effect_item_none") {
            var _x = event.x, _y = event.y;

            if (parseFloat(rotationObj.rotation) !== 0) {
                _x = _x - rotationObj.rotationRect.left + rotationObj.rect.left;
                _y = _y - rotationObj.rotationRect.top + rotationObj.rect.top;
            }
            effects["visibility"] = "visible";
            effects["x"] = _x * ratio;
            effects["y"] = _y * ratio;
        }
    }
    effects["delay"] = event.delay;
    effects["ease"] = event.easing;


    
    return TweenLite.to(obj, event.duration, effects);
}

function createDisappear(obj, event, rotationObj, totalW, totalH, w, h) {
    var effects = {}, _x, _y, m = 0;
    var rotation = parseFloat(rotationObj.rotation);

    for (var effectIndex in event.effects) {
        var effect = event.effects[ effectIndex];

        if (effect == "effect_item_opacity") {
            // Opacity Effect
            effects["autoAlpha"] = 0;
        }
        if (effect == "effect_item_scale") {
            // scale effect
            effects["scaleX"] = 0;
            effects["scaleY"] = 0;
        }
        if (effect == "effect_item_top") {
            _x = event.x;
            if (rotation !== 0) {
                _y = -rotationObj.rect.bottom - 1;
            } else {
                _y = -h;
            }
            m = 1;
        }
        if (effect == "effect_item_bottom") {
            _x = event.x;
            if (rotation !== 0) {
                _y = totalH - rotationObj.rect.top + 1;
            } else {
                _y = totalH;
            }
            m = 1;
        }
        if (effect == "effect_item_right") {
            if (rotation !== 0) {
                _x = -rotationObj.rect.left + totalW + 1 + borderShift;
            } else {
                _x = totalW + borderShift;
            }
            _y = event.y;
            m = 1;
        }
        if (effect == "effect_item_left") {
            if (rotation !== 0) {
                _x = -rotationObj.rect.right - 1 - borderShift;
            } else {
                _x = -w - borderShift;
            }
            _y = event.y;
            m = 1;
        }
        if (effect == "effect_item_custom") {
            _x = event.custom_x;
            _y = event.custom_y;
            m = 1;
        }
    }
    if (rotation !== 0 && m == 1) {
        _x = _x - rotationObj.rotationRect.left + rotationObj.rect.left;
        _y = _y - rotationObj.rotationRect.top + rotationObj.rect.top;
    }
    if (m == 1) {
        effects["visibility"] = "visible";
        effects["x"] = _x * ratio;
        effects["y"] = _y * ratio;
    }
    effects["delay"] = event.delay;
    effects["ease"] = event.easing;

    return TweenLite.to(obj, event.duration, effects);
}

/**
 * event is called when a single slide is complete.
 */
function onSlideComplete() {
    validateGotoAndStop();
    updatePlayPause("paused");
    if (getNextBufferedSlide() != -1) {
        currentSlide = getNextBufferedSlide();
        setNextBufferedSlide(-1);
        onPlay();
    } else {
        if (!slidesObjects_xxx.loop || slidesObjects_xxx.loop == false) {
            if (getNextSlide() == 0) {
                return;
            }
        }
        onNext();
    }
    updateActiveBullet();
}

/**
 * Event is called when a main timeline is updated.
 * Used by progress bars.
 */
function onUpdateTimeline(timeline, statusbar, multiplier) {
    var wd = getGlobalWidth();
    var w = wd * timeline.progress();

    w *= multiplier;
    if (w <= wd) {
        statusbar.style.width = w + "px";
    }
}

function addListener(element, eventName, handler) {
    if (element.addEventListener) {
        element.addEventListener(eventName, handler, false);
    } else if (element.attachEvent) {
        element.attachEvent("on" + eventName, handler);
    } else {
        element["on" + eventName] = handler;
    }
}

function removeEventHandler(elem, eventType, handler) {
    if (elem.removeEventListener) {
        elem.removeEventListener(eventType, handler, false);
    } else if (elem.detachEvent) {
        elem.detachEvent("on" + eventType, handler);
    }
}

function onPlay() {
    var thisSlide = timeLines[ getCurrentSlide()];

    if (thisSlide.progress() != 1) {
        thisSlide.play();
    } else {
        thisSlide.restart();
    }
    updatePlayPause("playing");
}

function onPause() {
    for (var idx = 0; idx < timeLines.length; idx++) {
        if (timeLines[ idx].isActive()) {
            timeLines[ idx].pause();
            paused[ idx] = true;
        }
    }
    updatePlayPause("paused");
}

function onResume() {
    for (var idx = 0; idx < paused.length; idx++) {
        if (paused[ idx] == true) {
            timeLines[ idx].resume();
        }
    }
    updatePlayPause("playing");
}

function onRestart() {
    crearProgressBar();
    for (var idx = 0; idx < timeLines.length; idx++) {
        if (paused[ idx] == true) {
            timeLines[ idx].seek("end_slide");
        }
        if (timeLines[ idx].isActive()) {
            timeLines[ idx].seek("end_slide");
        }
    }
    timeLines[ 0].restart();
    updatePlayPause("playing");
}

function onNext() {
    jumpToSlide(getNextSlide());
}

function onPrevious() {
    jumpToSlide(getPrevSlide());
}

function jumpToSlide(slideIndex) {
    var thisSlide = timeLines[ getCurrentSlide()];
    crearProgressBar();
    stopAllSlides();
    currentSlide = slideIndex; // update current slide
    var newSlide = timeLines[ slideIndex];
    onPlay();
    updateActiveBullet();
}

function stopAllSlides() {
    for (var idx = 0; idx < timeLines.length; idx++) {
        if (paused[ idx] == true) {
            timeLines[ idx].seek("end_slide");
        }
        if (timeLines[ idx].isActive()) {
            timeLines[ idx].seek("end_slide");
        }
    }
}

function crearProgressBar() {
    $status.style.width = "0px";
}

function getNextBufferedSlide() {
    return bufferedSlide;
}

function setNextBufferedSlide(b) {
    bufferedSlide = b;
}

function validateGotoAndStop() {
    if (gotoAndStopFlag == 1) {
        gotoAndStopFlag = 0;
        gotoAndStop = 1;
    }
}

function overlapSlides() {
    validateGotoAndStop();
    var nextSlide = getNextBufferedSlide();
    if (nextSlide == -1 || nextSlide == currentSlide) {
        nextSlide = getNextSlide();
        if (!slidesObjects_xxx.loop || slidesObjects_xxx.loop == false) {
            if (nextSlide == 0) {
                return;
            }

        }
    }
    if (nextSlide != -1) {
        setNextBufferedSlide(-1);
        currentSlide = nextSlide;
        var newSlide = timeLines[ nextSlide];
        if (newSlide.isActive()) {
            newSlide.seek("end_slide");
        }
        onPlay();
        updateActiveBullet();
    }
}

// external URL
function initGoToUrl(item, url, target) {
    if (url != "") {
        item.onclick = function () {
            window.open(url, target);
        };
        item.style.cursor = "pointer";
    }
}

// Slide URL (gotoAndStop)
function initGotoAndStopUrl(item, slide, immediate) {
    var s = parseInt(slide);

    item.onclick = function () {
        if (immediate == "true") {
            onGotoAndStopImmediate(s);
        } else {
            onGotoAndStopDelayed(s);
        }
    };
    item.style.cursor = "pointer";
}

// Slide URL (gotoAndPlay)
function initGotoAndPlayUrl(item, slide, immediate) {
    var s = parseInt(slide);

    item.onclick = function () {
        if (immediate == "true") {
            onGotoAndPlayImmediate(s);
        } else {
            onGotoAndPlayDelayed(s);
        }
    };
    item.style.cursor = "pointer";
}

// GoToAndStop immediate Jumps
function onGotoAndStopImmediate(slide) {
    gotoAndStop = 1;
    jumpToSlide(slide);
}

// GoToAndStop delayed Jumps
function onGotoAndStopDelayed(slide) {
    gotoAndStopFlag = 1;
    setNextBufferedSlide(slide);
    onResume();
}

function onGotoAndPlayImmediate(slide) {
    jumpToSlide(slide);
}

function onGotoAndPlayDelayed(slide) {
    setNextBufferedSlide(slide);
}

function checkForGoToAndStop() {
    if (slidesObjects_xxx.autoplay == "false" || gotoAndStop == 1) {
        gotoAndStop = 0;
        gotoAndStopFlag = 0;
        onPause();
    }
    if (slidesObjects_xxx.loop == false && slidesObjects_xxx.autoplay == "true" && getNextSlide() == 0) {
        onPause();
    }
}

// Regular slide Jumps
function onBullet(_this) {
    var bulletId = _this.id;
    var slideIndex = parseInt(bulletsMap[ bulletId]);

    gotoAndStop = 0;
    gotoAndStopFlag = 0;
    jumpToSlide(slideIndex);
}

function getTransparentImageClass(opacity) {
    var o = parseFloat(opacity);
    var oIE = o * 100;
    var s = "";

    s = "-ms-filter:'progid:DXImageTransform.Microsoft.Alpha(Opacity=" + oIE + ")'; ";
    s += "filter: alpha(opacity=" + oIE + "); ";
    s += "-moz-opacity: " + o + "; ";
    s += "-khtml-opacity:" + o + "; ";
    s += "opacity: " + o + "; "

    return s;
}

function getGlobalWidth() {
    return $main.innerWidth || $main.clientWidth;
}

function disableContext() {
    document.getElementById("mainDiv").oncontextmenu = function (event) {
        return false;
    }
}

function onWindowResize() {
    var w = window,
            banner = document.getElementById("mainDiv"),
            globalBanner = document.getElementById("globalDiv"),
            x = w.innerWidth || w.clientWidth,
            y = w.innerHeight || w.clientHeight;
    borderShift = 0;
    if (x < slidesObjects_xxx.width) {
        ratio = x / slidesObjects_xxx.width;
        banner.style.width = Math.round(slidesObjects_xxx.width * ratio) + "px";
        banner.style.height = Math.round(slidesObjects_xxx.height * ratio) + "px";
        globalBanner.style.height = Math.round(slidesObjects_xxx.height * ratio) + "px";
    } else {
        ratio = 1;
        banner.style.width = slidesObjects_xxx.width + "px";
        banner.style.height = slidesObjects_xxx.height + "px";
        globalBanner.style.height = slidesObjects_xxx.height + "px";
        if (slidesObjects_xxx.fromBorder) {
            borderShift = Math.round((x - slidesObjects_xxx.width) / 2);
        }
    }
    if (window.parent) {
        window.parent.postMessage("changeSize" + banner.style.height + "html5maker" + id, "*");
    }
    killAnimation();
    onReady();
}

function killAnimation() {
    if (!isAnimation) {
        return;
    }
    var $parent = document.getElementById("mainDiv");

    if (slidesObjects_xxx.bullets && slidesObjects_xxx.bullets == "true") {
        var parent = document.getElementById("bullets");

        for (var bulletsIndex in $bulletsObjects) {
            var $b = $bulletsObjects[ bulletsIndex];
            //parent.removeChild($b);
        }
    }
    if (slidesObjects_xxx.mouseoverPause && slidesObjects_xxx.mouseoverPause == "true") {
        removeEventHandler($main, "mouseover", onPause);
        removeEventHandler($main, "mouseout", onResume);
    }
    if (slidesObjects_xxx.playPause && slidesObjects_xxx.playPause == "true") {
        removeEventHandler($play, "click", onPlayClicked);
        removeEventHandler($pause, "click", onPauseClicked);
    }
    if (slidesObjects_xxx.nextPrevious && slidesObjects_xxx.nextPrevious == "true") {
        removeEventHandler($main, "mouseover", onShowNextPrev);
        removeEventHandler($main, "mouseout", onHideNextPrev);
        removeEventHandler($next, "click", onNextClicked);
        removeEventHandler($prev, "click", onPreviousClicked);
    }
    if (slidesObjects_xxx.wmark && slidesObjects_xxx.wmark == "true") {
        removeEventHandler($wm, "click", onWmClicked);
    }


    for (var ind in timeLines) {
        var tl = timeLines[ ind];
        tl.seek("end_slide");
        tl.kill();
    }
    timeLines = [];
}

function onNextClicked() {
    gotoAndStop = 0;
    gotoAndStopFlag = 0;
    onNext();
}

function onPreviousClicked() {
    gotoAndStop = 0;
    gotoAndStopFlag = 0;
    onPrevious();
}

function resizeBullet(item) {
    resizeObject(item, slidesObjects_xxx.bulletWidth, slidesObjects_xxx.bulletHeight);
    item.style.marginRight = (slidesObjects_xxx.bulletsSpacing * ratio) + "px";
}

function resizeObject(item, originalW, originalH) {
    var image = item.getElementsByTagName("img")[ 0];

    item.style.width = (originalW * ratio) + "px";
    item.style.height = (originalH * ratio) + "px";
    if (image && image != null) {
        image.style.width = (originalW * ratio) + "px";
        image.style.height = (originalH * ratio) + "px";
    }
}

function relocateObject(item, originalLeft, originalTop) {
    item.style.left = (originalLeft * ratio) + "px";
    item.style.top = (originalTop * ratio) + "px";
}

function LOG(msg) {
    if (window.console) {
        console.log(msg);
    }
}