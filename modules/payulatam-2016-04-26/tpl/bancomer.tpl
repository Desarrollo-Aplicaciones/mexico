{if isset($error)}
 <p style="color:red">{l s='An error occured, please try again later.' mod='payulatam'}</p>
{else} 
 	     
    
<form  method="POST" action="./modules/payulatam/bancomer.php" id="formBancomer" name="formBancomer" autocomplete="off" >
  
    <div class="contend-form" >        
        <input type="hidden" value="formBancomer" id="pagar" name="pagar" />
                            
    </div>       

</form>

{/if}
