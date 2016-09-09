{if $MENU != ''} 
    <link rel="stylesheet" type="text/css" href="{$css_dir}modules/blocktopmenu/css/opt-in.css" />
    
    <div style="display: inline-block;vertical-align: top;display: inline;float: right;margin-top: 11px;margin-top: 31px;float: left;width:101%" >       
        <div class="bloque1"><span class="texto-news1" style="color: #FFF;">SUSCRÍBETE A NUESTRO BOLETÍN</span></div>
        <div class="bloque1"><span class="texto-news1" style="color: #dee54b;">DE BIENESTAR Y SALUD</span></div>
        <div class="bloque1"><input type="text" placeholder="Ingresa tu E-mail aquí" name="mail" id="mail" /></div>
        <div class="bloque1">
            <div class="celda1"><input type="button" name="hombre" id="hombre" value="HOMBRE" /></div>
            <div class="celda1"><input type="button" name="mujer" id="mujer" value="MUJER" /></div>           
        </div>
    </div>
    
     <script type="text/javascript">
         {literal}
// validación sexo  
 $(function(){ 
 var sex=0;
 $( "#hombre" ).click(function() {
  sex='M'; 
 });
  $( "#mujer" ).click(function() {
  sex='F';  
 });
 
 // ajax
$( "#hombre,#mujer" ).click(function() {
   $.ajax(ruta, {
   "type": "post", // usualmente post o get
   "success": function(result) {
      
      if(result==='error5')
      {
         alert('Ingresa tu correo, para inscribirte a nuestro boletín.');
      }
      if(result==='error2')
      {
       alert('Ingresa un correo válido para inscribirte a nuestro boletín.');   
      }
      if(result==='ok')
      {
          alert('¡Felicitaciones!  Te has suscrito con éxito a nuestro boletín.');
          $('#mail').val('');
    
      }

   },
   "error": function(result) {
    console.log("Error AjaxLigthBox -> "+result);
   },
   "data": {mail: $("#mail" ).val(), sex: sex },
   "async": true
}); 

});

});       
{/literal}        
   </script>
    
	<!-- Menu -->
	<div class="sf-contener clearfix">
		<ul class="sf-menu clearfix" id="menu_parent">
			{$MENU}
			{if $MENU_SEARCH}
				<li class="sf-search noBack" style="float:right">
					<form id="searchbox" action="{$link->getPageLink('search')}" method="get">
						<p>
							<input type="hidden" name="controller" value="search" />
							<input type="hidden" value="position" name="orderby"/>
							<input type="hidden" value="desc" name="orderway"/>
							<input type="text" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|escape:'htmlall':'UTF-8'}{/if}" />
						</p>
					</form>
				</li>
			{/if}
		</ul>
	</div>
	<div class="sf-right">&nbsp;</div>

	<!--/ Menu -->
	<script>$('ul#menu_parent > li').last().addClass("last");$('ul#menu_parent > li').first().addClass("first");</script>       
{/if}