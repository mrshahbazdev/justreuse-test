var windowWidth,lazyImages,lazyBackground,lazyIframe,lazyScripts,navigator_platform,src,style,datasrc,urls,analytics,s,x,i,j,flag;function ks_init(){flag&&(flag=0,lazyLoadImg(),lazyLoadBackground(),lazyLoadIframe(),load_all_js())}function isElementInViewport(t){var a=t.getBoundingClientRect();return a.top>=0&&a.left>=0&&a.bottom<=(window.innerHeight||document.documentElement.clientHeight)&&a.right<=(window.innerWidth||document.documentElement.clientWidth)}function lazyLoadImg(){lazyImages.forEach(function(t){null!=(src=windowWidth<600?null==t.dataset.mobsrc?t.dataset.src:t.dataset.mobsrc:t.dataset.src)&&(t.src=src),t.classList.remove("lazy2")})}function lazyLoadImg2(){lazyImages.forEach(function(t){isElementInViewport(t)&&(null!=(src=windowWidth<600?null==t.dataset.mobsrc?t.dataset.src:t.dataset.mobsrc:t.dataset.src)&&(t.src=src),t.classList.remove("lazy"))})}function lazyLoadBackground(){lazyBackground.forEach(function(t){lazybg=windowWidth<600?null==t.dataset.mobstyle?t.dataset.style:t.dataset.mobstyle:t.dataset.style,null!=lazybg&&(t.style=lazybg),t.classList.remove("lazybg")})}function lazyLoadIframe(){lazyIframe.forEach(function(t){null!=t.dataset.src&&(t.src=t.dataset.src)})}function lazyLoadScripts(){j!=lazyScripts.length&&("lazyload2"==lazyScripts[j].getAttribute("type")?(lazyScripts[j].setAttribute("type","lazyloaded"),void 0!==lazyScripts[j].dataset.src?((s=document.createElement("script")).src=lazyScripts[j].dataset.src,document.body.appendChild(s),s.onload=function(){j++,lazyLoadScripts()}):((s=document.createElement("script")).innerHTML=lazyScripts[j].innerHTML,document.body.appendChild(s),j++,lazyLoadScripts())):(j++,lazyLoadScripts()))}function lazyLoadCss(t){(s=document.createElement("link")).rel="stylesheet",s.href=t,document.getElementsByTagName("head")[0].appendChild(s)}function lazyLoadJS(t){(s=document.createElement("script")).src=t,document.body.appendChild(s)}document.addEventListener("DOMContentLoaded",function(){windowWidth=screen.width,lazyImages=document.querySelectorAll("img.lazy2"),nolazyImages=document.querySelectorAll("img.lazy"),lazyBackground=document.querySelectorAll(".lazybg"),lazyIframe=document.querySelectorAll("iframe"),lazyScripts=document.getElementsByTagName("script"),navigator_platform=navigator.platform,i=0,j=0,flag=1,window.addEventListener("scroll",function(){ks_init()}),window.addEventListener("mousemove",function(){ks_init()}),window.addEventListener("touchstart",function(){ks_init()}),"Linux x86_64"!=navigator_platform&&ks_init()});

function load_all_js() {
	
	document.body.click();
	
	lazyLoadCss("https://www.pushkahome.com/pub/media/pushka/web/css/appendcss.css?ss37896");
	
	j=0;
	lazyScripts = document.getElementsByTagName("script");
	lazyLoadScripts();

	setInterval(function() {
		lazyImages = document.querySelectorAll('img.lazy2');
		lazyBackground = document.querySelectorAll('.lazybg');
		lazyLoadImg();
		lazyLoadBackground();
	}, 2000);
}