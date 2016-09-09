<!-- CS Slider module -->
{if isset($sliders) && $sliders|@count >0}
<script type="text/javascript">
				var api;
			jQuery(window).ready(function() {
			api =  jQuery(".{if $option->fullwidth == 'on'}fullwidthbanner{else}banner{/if}").revolution(
				{
					delay:{$option->delay},
					startheight:{$option->startheight},
					startwidth:{$option->startwidth},

					hideThumbs:{$option->timehidethumbnail},

					thumbWidth:{$option->thumbnailwidth},		// Thumb With and Height and Amount (only if navigation Tyope set to thumb !)
					thumbHeight:{$option->thumbnailheight},
					thumbAmount:{$option->thumbamount},

					navigationType:"{$option->navigationtype}",		// bullet, thumb, none - trong
					navigationArrows:"{$option->navigationarrow}",	// nexttobullets, solo (old name verticalcentered), none - mui ten

					navigationStyle:"{$option->navigationstyle}",				//round,square,navbar,round-old,square-old,navbar-old, or any from the list in the docu (choose between 50+ different item), custom - hinh cua icon tron
					navigationHAlign:"{$option->navigationhalign}",				// Vertical Align top,center,bottom
					navigationVAlign:"{$option->navigationvalign}",					// Horizontal Align left,center,right
					navigationHOffset:{$option->navigationhoffset},
					navigationVOffset:{$option->navigationvoffset},

					soloArrowLeftHalign:"{$option->soloarrowlefthalign}",
					soloArrowLeftValign:"{$option->soloarrowleftvalign}",
					soloArrowLeftHOffset:{$option->soloarrowlefthoffset},
					soloArrowLeftVOffset:{$option->soloarrowleftvoffset},

					soloArrowRightHalign:"{$option->soloarrowrighthalign}",
					soloArrowRightValign:"{$option->soloarrowrightvalign}",
					soloArrowRightHOffset:{$option->soloarrowrighthoffset},
					soloArrowRightVOffset:{$option->soloarrowrightvoffset},

					touchenabled:"{$option->touchenabled}",	// Enable Swipe Function : on/off
					onHoverStop:"{$option->onhoverstop}",	// Stop Banner Timet at Hover on Slide on/off

					stopAtSlide:{$option->stopatslide},
					stopAfterLoops:{$option->stopafterloops},
					
					hideCaptionAtLimit:{$option->hidecapptionatlimit},					// It Defines if a caption should be shown under a Screen Resolution ( Basod on The Width of Browser)
					hideAllCaptionAtLilmit:{$option->hideallcapptionatlimit},				// Hide all The Captions if Width of Browser is less then this value
					hideSliderAtLimit:{$option->hideslideratlimit},					// Hide the whole slider, and stop also functions if Width of Browser is less than this value

					
					shadow:{$option->shadow},								//0 = no Shadow, 1,2,3 = 3 Different Art of Shadows  (No Shadow in Fullwidth Version !)
					fullWidth:"off"							// Turns On or Off the Fullwidth Image Centering in FullWidth Modus
						});
					});
			</script>
<div class="cs_revolution"> 
	<div class="{if $option->fullwidth == 'on'}fullwidthbanner-container{else}bannercontainer{/if} responsive">
		<div class="{if $option->fullwidth == 'on'}fullwidthbanner{else}banner{/if}">
		
			<ul>{assign var="numimg" value="0"}
				{foreach from=$sliders item=slider}
					{assign var="numimg" value=$numimg+1}
					{if $slider.image->type == "image"}
						{assign var="src" value="{$path}images/{$slider.image->value}"}
					{else}
						{assign var="src" value="{$path}revolution/images/sliderbg/transparent.png"}
					{/if}
					{if $slider.image->type == "color"}
					<style type="text/css">
						.thumb img {
							background-color:{$slider.image->value};
						}
					</style>
					{/if}
					<li data-transition="{$slider.animation->transitions}" data-masterspeed="{$slider.animation->masterspeed}" data-slotamount="{$slider.animation->slotamount}" data-delay="{$slider.animation->delay}" {if $slider.image->type == "image"} data-thumb="{$path}images/thumbs/{$slider.image->value}"{/if} >
					{if $slider.animation->enablelink == 1} <a id="csslider_{$numimg}" class="cs_slidelink" href="{$slider.url}" target="{$slider.animation->target}"></a>{/if} 
					<img src="{$src}" {if $slider.image->type == "color"}style="background-color:{$slider.image->value}"{/if}/>
					{if isset($slider.caption)}
						{foreach from=$slider.caption item=caption name=caption}
							<div class="caption {$caption->class} {$caption->incomanimation} {$caption->outgoanimation}{if $slider.animation->enablefullvideo == 1 && $caption->type_caption == 'videocaption'} fullscreenvideo{/if}"
							data-x="{$caption->datax}" data-y="{$caption->datay}" {if isset($caption->hideunderwidth)} data-captionhidden="on"{/if} data-easing="{$caption->easing}" data-endeasing="{$caption->endeasing}" data-speed="{$caption->dataspeed}" data-start="{$caption->datastart}">
							
								{if $caption->type_caption == "textcaption"}
									{$caption->text->{(int)$cookie->id_lang}}
								{else if $caption->type_caption == "imagecaption"}
									<img src="{$path}images/captions/{$caption->image_{(int)$cookie->id_lang}}" alt=""/>
								{else}
									{if $caption->type_video->{(int)$cookie->id_lang} == 1}
										<iframe src="http://www.youtube.com/embed/{$caption->type_id->{(int)$cookie->id_lang}}?hd=1&amp;wmode=opaque&amp;controls=1&amp;showinfo=0"></iframe>
									{else}
										<iframe src="http://player.vimeo.com/video/{$caption->type_id->{(int)$cookie->id_lang}}?title=0&amp;byline=0&amp;portrait=0"></iframe>
									{/if}
								{/if}
								
							</div>
						{/foreach}
					{/if}
					</li>
				{/foreach}
			</ul>
			{if $option->timerline == "true"}
			<div class="tp-bannertimer{if $option->timerlineposition == bottom} tp-bottom{/if}"></div> 
			{/if}
		</div>
	</div>
</div>
{/if}
<!-- /CS Slider module -->